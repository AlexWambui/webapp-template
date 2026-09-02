<?php

namespace Modules\Delivery\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Exception;
use Modules\Delivery\Models\DeliveryLocation;
use Modules\Delivery\Http\Resources\DeliveryLocationResource;
use Modules\Delivery\Http\Resources\DeliveryAreaResource;
use Modules\Delivery\Http\Requests\DeliveryLocationRequest;

class DeliveryLocationController extends Controller
{
    public function index(Request $request)
    {
        $query = DeliveryLocation::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $locations = $query->orderBy('name')
            ->withCount(['deliveryAreas'])
            ->paginate(20);

        return inertia('app/deliveries/locations/Index', [
            'locations' => DeliveryLocationResource::collection($locations),
            'filters' => $request->only(['search'])
        ]);
    }

    public function create()
    {
        return inertia('app/deliveries/locations/Create');
    }

    public function store(DeliveryLocationRequest $request)
    {
        try {
            DB::beginTransaction();

            DeliveryLocation::create([
                'name' => $request->name,
            ]);

            DB::commit();

            Inertia::flash('toast', [
                'type' => "success",
                'message' => "Location created successfully"
            ]);

            return to_route('delivery-locations.index');
        } catch (Exception $e) {
            DB::rollBack();

            Inertia::flash('toast', [
                'type' => "error",
                'message' => "Failed to create location: {$e->getMessage()}"
            ]);

            return back()->withInput();
        }
    }

    public function show(Request $request, DeliveryLocation $delivery_location)
    {
        $query = $delivery_location->deliveryAreas();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $delivery_areas = $query->orderBy('name')->paginate(30);

        return inertia('app/deliveries/locations/Show', [
            'delivery_location' => $delivery_location,
            'delivery_areas' => DeliveryAreaResource::collection($delivery_areas)
        ]);
    }

    public function edit(DeliveryLocation $delivery_location)
    {
        return inertia('app/deliveries/locations/Edit', [
            'delivery_location' => $delivery_location
        ]);
    }

    public function update(DeliveryLocationRequest $request, DeliveryLocation $delivery_location)
    {
        try {
            DB::beginTransaction();

            $delivery_location->update([
                'name' => $request->name,
            ]);

            DB::commit();

            Inertia::flash('toast', [
                'type' => "success",
                'message' => "Location updated successfully"
            ]);

            return to_route('delivery-locations.index');
        } catch (Exception $e) {
            DB::rollBack();

            Inertia::flash('toast', [
                'type' => "error",
                'message' => "Failed to update location: {$e->getMessage()}"
            ]);

            return back()->withInput();
        }
    }

    public function destroy(DeliveryLocation $delivery_location)
    {
        try {
            $delivery_location->delete();

            Inertia::flash('toast', [
                'type' => "success",
                'message' => "Location deleted successfully"
            ]);

            return to_route('delivery-locations.index');
        } catch (Exception $e) {
            Inertia::flash('toast', [
                'type' => "error",
                'message' => "Failed to delete location: {$e->getMessage()}"
            ]);

            return back()->withInput();
        }
    }
}