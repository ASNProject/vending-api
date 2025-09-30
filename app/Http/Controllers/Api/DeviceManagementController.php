<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\Item;
use App\Http\Resources\Resource;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;

class DeviceManagementController extends Controller
{
    /**
     * index
     */
    public function index(Request $request)
    {
        $query = Device::with('item');

        // filter by device name
        if ($request->has('device')) {
            $query->where('device', $request->device);
        }

        // filter by item_id
        if ($request->has('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        $devices = $query->get();

        if ($devices->isEmpty()) {
            return new Resource(false, 'No records found', null);
        }

        // Kalau filter by device_name AND item_id (satu data saja)
        if ($request->has('device') && $request->has('item_id')) {
            return new Resource(true, 'Filtered record retrieved successfully', $devices->first());
        }

        // Default grouping by device
        $grouped = $devices->groupBy('device')->map(function ($group, $deviceName) {
            return [
                'device' => $deviceName,
                'items'  => $group->map(function ($device) {
                    return [
                        'item'  => $device->item,
                        'limit' => $device->limit,
                    ];
                })->values()
            ];
        })->values();

        return new Resource(true, 'Device item limits retrieved successfully', $grouped);
    }


    /**
     * store (API)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device'           => 'required|string',
            'items'            => 'required|array',
            'items.*.item_id'  => 'required|integer|exists:items,id',
            'items.*.limit'    => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return new Resource(false, 'Validation error', $validator->errors());
        }

        $createdItems = [];
        $duplicates   = [];

        foreach ($request->items as $data) {
            $existing = Device::where('device', $request->device)
                ->where('item_id', $data['item_id'])
                ->first();

            if ($existing) {
                $duplicates[] = $data['item_id'];
                continue;
            }

            $created = Device::create([
                'device'  => $request->device,
                'item_id' => $data['item_id'],
                'limit'   => $data['limit'],
            ]);

            $createdItems[] = [
                'item'  => Item::find($created->item_id),
                'limit' => $created->limit,
            ];
        }

        $response = [
            'device' => $request->device,
            'items'  => $createdItems,
        ];

        if (!empty($duplicates)) {
            return new Resource(false, 'Some items already exist for this device', [
                'duplicates' => $duplicates,
                'created'    => $createdItems,
            ]);
        }

        return new Resource(true, 'Device item limits created successfully', $response);
    }

    /**
     * show
     */
    public function show($deviceName)
    {
        $devices = Device::where('device', $deviceName)->with('item')->get();

        if ($devices->isEmpty()) {
            return new Resource(false, 'Device not found', null);
        }

        return new Resource(true, 'Device retrieved successfully', $devices);
    }

    /**
     * update
     */
    public function update(Request $request, $device)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|integer|exists:items,id',
            'limit'   => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return new Resource(false, 'Validation error', $validator->errors());
        }

        $deviceItem = Device::where('device', $device)
            ->where('item_id', $request->item_id)
            ->first();

        if (!$deviceItem) {
            return new Resource(false, 'Device item not found', null);
        }

        $deviceItem->update(['limit' => $request->limit]);

        return new Resource(true, 'Device item updated successfully', $deviceItem);
    }

    /**
     * destroy
     */
    public function destroy($id)
    {
        $deviceItem = Device::find($id);

        if (!$deviceItem) {
            return new Resource(false, 'Device item not found', null);
        }

        $deviceItem->delete();

        return new Resource(true, 'Device item deleted successfully', null);
    }
}
