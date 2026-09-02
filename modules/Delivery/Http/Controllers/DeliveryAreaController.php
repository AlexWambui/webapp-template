<?php

namespace Modules\Delivery\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Exception;
use Modules\Delivery\Models\DeliveryLocation;
use Modules\Delivery\Models\DeliveryArea;
use Modules\Delivery\Http\Requests\DeliveryAreaRequest;
use Modules\Delivery\Http\Resources\DeliveryAreaResource;

class DeliveryAreaController extends Controller
{
    public function create(DeliveryLocation $delivery_location)
    {
        return inertia('app/deliveries/areas/Create', [
            'delivery_location' => $delivery_location
        ]);
    }

    public function store(DeliveryLocation $delivery_location, DeliveryAreaRequest $request)
    {
        try {
            DB::beginTransaction();

            $delivery_location->deliveryAreas()->create($request->validated());

            DB::commit();

            Inertia::flash('toast', [
                'type' => 'success',
                'message' => "Area created successfully"
            ]);

            return to_route('delivery-locations.show', $delivery_location);
        } catch (Exception $e) {
            DB::rollBack();

            Inertia::flash('toast', [
                'type' => 'error',
                'message' => "Failed to create area: {$e->getMessage()}"
            ]);

            return back()->withInput();
        }
    }

    public function edit(DeliveryLocation $delivery_location, DeliveryArea $delivery_area)
    {
        return inertia('app/deliveries/areas/Edit', [
            'delivery_area' => $delivery_area,
            'delivery_location' => $delivery_location
        ]);
    }

    public function update(DeliveryAreaRequest $request, DeliveryLocation $delivery_location, DeliveryArea $delivery_area)
    {
        try {
            DB::beginTransaction();

            $delivery_area->update($request->validated());

            DB::commit();

            Inertia::flash('toast', [
                'type' => 'success',
                'message' => "Area updated successfully"
            ]);

            return to_route('delivery-locations.show', $delivery_location->uuid);
        } catch (Exception $e) {
            DB::rollBack();

            Inertia::flash('toast', [
                'type' => 'error',
                'message' => "Failed to update area: {$e->getMessage()}"
            ]);

            return back()->withInput();
        }
    }

    public function destroy(DeliveryArea $delivery_area)
    {
        try {
            $delivery_area->delete();

            Inertia::flash('toast', [
                'type' => 'success',
                'message' => "Area deleted successfully"
            ]);

            return back();
        } catch (Exception $e) {
            Inertia::flash('toast', [
                'type' => 'error',
                'message' => "Failed to delete area: {$e->getMessage()}"
            ]);

            return back()->withInput();
        }
    }

    public function getAreasByLocation(DeliveryLocation $location)
    {
        $areas = $location->deliveryAreas()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return DeliveryAreaResource::collection($areas);
    }
}