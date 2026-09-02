<?php

namespace Modules\Order\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Modules\Order\Enums\DeliveryStatusEnum;
use Modules\Order\Enums\OrderStatusEnum;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderItem;
use Modules\Product\Models\Product;
use Modules\Payment\Models\Payment;
use Modules\Order\Http\Resources\OrderResource;
use Modules\Order\Http\Resources\ProductPOSResource;
use Modules\Order\Http\Requests\OrderRequest;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::search($request->search)->latest()->paginate(50);

        return Inertia::render('app/orders/orders/Index', [
            'orders' => OrderResource::collection($orders),
            'filters' => [
                'search' => $request->search
            ],
        ]);
    }

    public function create()
    {
        $products = Product::query()
            ->orderBy('name')
            ->where('is_active', true)
            ->get();

        return inertia('app/orders/orders/Create', [
            'products' => ProductPOSResource::collection($products)
        ]);
    }

    public function store(OrderRequest $request)
    {
        $validated = $request->validated();

        return DB::transaction(function () use ($validated) {
            // --- CALCULATE TOTALS ---
            $subtotal = collect($validated['cart_items'])->sum(fn($item) => $item['price'] * ($item['quantity'] ?? 1));

            // Total cost price
            $total_cost_price = 0;
            foreach ($validated['cart_items'] as $item) {
                $product = Product::find($item['id']);
                $total_cost_price += ($product->cost_price ?? 0) * ($item['quantity'] ?? 1);
            }

            // Total selling price (subtotal + delivery)
            $total_selling_price = $subtotal + $validated['delivery_cost'];

            // Calculate total paid from payments
            $total_paid = collect($validated['payments'])->sum('amount');

            // Determine initial order status
            $initialOrderStatus = $validated['delivery_method'] === 'delivery' 
                ? OrderStatusEnum::PENDING 
                : OrderStatusEnum::PROCESSING;

            // delivery details
            $delivery_location = $validated['delivery_method'] === 'shop' ? 'shop' : $validated['location'];
            $delivery_area = $validated['delivery_method'] === 'shop' ? 'shop' : $validated['area'];
            $delivery_address = $validated['delivery_method'] === 'shop' ? 'shop' : $validated['address'];

            // --- CREATE THE ORDER ---
            $order = Order::create([
                'order_number' => 'Ord_' . strtoupper(Str::random(6)) . '_' . now()->format('ymd'),
                'order_channel' => $validated['order_channel'],
                'order_status' => $initialOrderStatus->value,
                
                'subtotal' => $subtotal,
                'shipping_cost' => $validated['delivery_cost'],
                'total_selling_price' => $total_selling_price,
                'total_cost_price' => $total_cost_price,
                'amount_paid' => $total_paid,

                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'customer_email' => $validated['customer_email'],

                'delivery_method' => $validated['delivery_method'],
                'delivery_location' => $delivery_location,
                'delivery_area' => $delivery_area,
                'delivery_address' => $delivery_address,
                'delivery_status' => DeliveryStatusEnum::PENDING->value,

                'sold_at' => now(),
            ]);

            // Create initial order status
            $orderStatus = $order->orderStatuses()->create([
                'type' => 'order',
                'status' => $initialOrderStatus->value,
                'notes' => 'Order created via ' . $validated['order_channel'],
                'user_id' => Auth::id(), // If admin is logged in
                'is_system' => false,
                'changed_at' => now(),
            ]);

            // Create initial delivery status
            $deliveryStatus = $order->orderStatuses()->create([
                'type' => 'delivery',
                'status' => DeliveryStatusEnum::PENDING->value,
                'notes' => 'Delivery created',
                'user_id' => Auth::id(),
                'is_system' => false,
                'changed_at' => now(),
            ]);

            // Update order with current status IDs
            $order->update([
                'current_order_status_id' => $orderStatus->id,
                'current_delivery_status_id' => $deliveryStatus->id,
            ]);

            // --- CREATE ORDER ITEMS (Loop through cart) ---
            foreach ($validated['cart_items'] as $item) {
                $product = Product::find($item['id']);
                $quantity = $item['quantity'] ?? 1;
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    
                    'product_name' => $product->name,
                    'product_sku' => $product->sku ?? null,
                    'quantity' => $quantity,
                    'cost_price' => $product->cost_price ?? 0,
                    'selling_price' => $item['price'],
                    'subtotal' => $item['price'] * $quantity,
                    'total' => $item['price'] * $quantity,
                ]);

                // Decrease stock
                $product->decrement('current_stock', $quantity);
            }

            // --- CREATE PAYMENT RECORD ---
            foreach ($validated['payments'] as $paymentData) {
                if ($paymentData['amount'] > 0) {
                    Payment::create([
                        'order_id' => $order->id,
                        'payment_method' => $paymentData['method'],
                        'transaction_reference' => null, // Handled manually for walk-in
                        'amount' => $paymentData['amount'],
                        'payment_status' => 'paid',
                    ]);
                }
            }

            // If fully paid and delivery, update order status to confirmed
            if ($total_paid >= $total_selling_price && $validated['delivery_method'] === 'delivery') {
                $order->updateOrderStatus(
                    OrderStatusEnum::CONFIRMED,
                    'Order fully paid, confirmed',
                    null,
                    Auth::id()
                );
            }

            // If fully paid and shop pickup, update to ready_for_pickup
            if ($total_paid >= $total_selling_price && $validated['delivery_method'] === 'shop') {
                $order->updateOrderStatus(
                    OrderStatusEnum::READY_FOR_PICKUP,
                    'Order fully paid, ready for pickup',
                    null,
                    Auth::id()
                );
            }

            // --- OPTIONAL: ASSIGN LOYALTY POINTS ---
            // If you have a user/loyalty system, you can add points here
            // $user = User::where('phone', $validated['customer_phone'])->first();
            // if($user) $user->increment('points', floor($totalAmount / 100));

            Inertia::flash('toast', [
                'type' => 'success',
                'message' => "Order added successfully",
            ]);

            return redirect()->back();
        });
    }

    public function edit(Order $order)
    {
        $order->load('orderItems', 'payments', 'orderStatuses');

        return inertia('app/orders/orders/Edit', [
            'order' => new OrderResource($order),
            'orderStatuses' => OrderStatusEnum::labels(),
            'deliveryStatuses' => DeliveryStatusEnum::labels(),
        ]);
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'order_status' => 'required|string|in:' . implode(',', OrderStatusEnum::values()),
            'delivery_status' => 'nullable|string|in:' . implode(',', DeliveryStatusEnum::values()),
            'notes' => 'nullable|string|max:1000',
            'metadata' => 'nullable|array',
        ]);

        DB::beginTransaction();

        try {
            // Update order status if provided
            if (isset($validated['order_status']) && $validated['order_status'] !== $order->order_status->value) {
                $status = OrderStatusEnum::from($validated['order_status']);
                $order->updateOrderStatus(
                    $status,
                    $validated['notes'] ?? null,
                    $validated['metadata'] ?? null,
                    Auth::id()
                );
            }

            // Update delivery status if provided
            if (isset($validated['delivery_status']) && $validated['delivery_status'] !== $order->delivery_status->value) {
                $status = DeliveryStatusEnum::from($validated['delivery_status']);
                $order->updateDeliveryStatus(
                    $status,
                    $validated['notes'] ?? null,
                    $validated['metadata'] ?? null,
                    Auth::id()
                );
            }

            DB::commit();

            Inertia::flash('toast', [
                'type' => "success",
                'message' => "Order updated successfully"
            ]);

            return to_route('orders.index');

        } catch (\Throwable $e) {
            DB::rollBack();

            Inertia::flash('toast', [
                'type' => "error",
                'message' => "Order failed to update: {$e->getMessage()}"
            ]);

            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy()
    {
        //
    }
}