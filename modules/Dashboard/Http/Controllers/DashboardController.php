<?php

namespace Modules\Dashboard\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\User\Enums\UserRoles;
use Modules\User\Models\User;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductCategory;
use Modules\Delivery\Models\DeliveryLocation;
use Modules\Delivery\Models\DeliveryArea;
use Modules\ContactMessage\Models\ContactMessage;
use Modules\Order\Enums\OrderStatusEnum;
use Modules\Order\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->role === UserRoles::SUPER_ADMIN) {
            return inertia('app/dashboards/SuperAdmin', [
                'user' => $user,
                'stats' => [
                    'total_users' => User::where('role', '!=', UserRoles::SUPER_ADMIN)->count(),
                    'total_admins' => User::where('role', '=', UserRoles::ADMIN)->count(),
                    'total_cashiers' => User::where('role', '=', UserRoles::CASHIER)->count(),

                    'total_products' => Product::count(),
                    'total_product_categories' => ProductCategory::count(),

                    'total_orders' => Order::count(),
                    'orders_need_attention' => Order::where('order_status', OrderStatusEnum::PENDING->value)->whereColumn('amount_paid', '>=', 'total_selling_price')->count(),

                    'total_delivery_locations' => DeliveryLocation::count(),
                    'total_delivery_areas' => DeliveryArea::count(),

                    'total_callbacks' => ContactMessage::count(),
                    'total_unread_callbacks' => ContactMessage::where('is_read', false)->count(),
                ]
            ]);
        }

        if ($user->role === UserRoles::ADMIN) {
            return inertia('app/dashboards/Admin', [
                'user' => $user,
                'stats' => [
                    'total_users' => User::where('role', '!=', UserRoles::SUPER_ADMIN)->count(),
                    'total_admins' => User::where('role', '=', UserRoles::ADMIN)->count(),
                    'total_cashiers' => User::where('role', '=', UserRoles::CASHIER)->count(),

                    'total_products' => Product::count(),
                    'total_product_categories' => ProductCategory::count(),

                    'total_orders' => Order::count(),
                    'orders_need_attention' => Order::needsAttention()->count(),

                    'total_delivery_locations' => DeliveryLocation::count(),
                    'total_delivery_areas' => DeliveryArea::count(),

                    'total_callbacks' => ContactMessage::count(),
                    'total_unread_callbacks' => ContactMessage::where('is_read', false)->count(),
                ]
            ]);
        }

        if ($user->role === UserRoles::CASHIER) {
            return inertia('app/dashboards/Cashier', [
                'user' => $user,
                'stats' => [
                    'total_products' => Product::where('is_active', true)->count(),
                    'total_product_categories' => ProductCategory::where('is_active', true)->count(),
                ]
            ]);
        }

        if ($user->role === UserRoles::CUSTOMER) {
            $ordersQuery = $user->orders();

            $stats = [
                'total_orders' => $ordersQuery->count(),
                'pending_orders' => (clone $ordersQuery)->pending()->count(),
                'processing_orders' => (clone $ordersQuery)->processing()->count(),
                'shipped_orders' => (clone $ordersQuery)->shipped()->count(),
                'delivered_orders' => (clone $ordersQuery)->delivered()->count(),
                'cancelled_orders' => (clone $ordersQuery)->cancelled()->count(),
                'active_orders' => (clone $ordersQuery)->active()->count(), // Using the new scope
                'total_spent' => (clone $ordersQuery)->paid()->sum('total_amount'),
                // 'recent_orders' => OrderResource::collection($ordersQuery->latest()->paginate(20)),
            ];

            return inertia('app/dashboards/Customer', [
                'user' => $user,
                'stats' => $stats
            ]);
        }
        return inertia('app/dashboards/Dashboard');
    }
}
