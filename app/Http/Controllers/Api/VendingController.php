<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\UserItemLimit;
use App\Models\Device;
use App\Models\Vending;
use App\Models\Item;
use App\Http\Resources\Resource;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Exports\VendingsExport;
use Maatwebsite\Excel\Facades\Excel;

class VendingController extends Controller
{
    /**
     * index
     * 
     * @return void
     */
    public function index()
    {
        $vending = Vending::with(['item', 'user'])
            ->latest()
            ->paginate(10);
    
        if ($vending->isEmpty()) {
            return new Resource(false, 'No vending records found', null);
        }
    
        // Format ulang data
        $data = $vending->getCollection()->map(function ($v) {
            return [
                'id' => $v->id,
                'device' => $v->device,
                'item' => [
                    'id' => $v->item->id,
                    'name' => $v->item->name,
                ],
                'user' => [
                    'uid' => $v->user->uid,
                    'name' => $v->user->name,
                    'role' => $v->user->role,
                    // 'remaining_limit' => $v->user->limit,
                ],
                'created_at' => $v->created_at,
            ];
        });
    
        // Masukkan hasil mapping ke dalam paginator
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $data,
            $vending->total(),
            $vending->perPage(),
            $vending->currentPage(),
            ['path' => request()->url(), 'query' => request()->query()]
        );
        return new Resource(true, 'Vending records retrieved successfully', $paginated);
    }

    /**
     * store
     * 
     * @param mixed $request
     * @return void
     */
    public function vending(Request $request)
    {
        $request->validate([
            'uid' => 'required|string',
            'device' => 'required|string',
            'item' => 'required|integer',
        ]);
    
        $user = UserItemLimit::where('uid', $request->uid)->first();
        $itemExists = Item::where('id', $request->item)->exists();
        $deviceItem = Device::where('device', $request->device)
            ->where('item_id', $request->item)
            ->first();
    
        if (!$user) {
            return new Resource(false, 'User not found in UserItemLimit table', null);
        }
    
        if (!$itemExists) {
            return new Resource(false, 'Item not found in Items table', null);
        }
    
        if (!$deviceItem) {
            return new Resource(false, 'Device item not found', null);
        }
    
        if ($user->limit <= 0) {
            return new Resource(false, 'User limit has reached 0', null);
        }
    
        if ($deviceItem->limit <= 0) {
            return new Resource(false, 'Device item limit has reached 0', null);
        }
    
        $user->decrement('limit');
        $deviceItem->decrement('limit');
    
        Vending::create([
            'user_uid' => $user->uid,
            'item_id' => $request->item,
            'device' => $request->device,
        ]);
    
        return new Resource(true, 'Limit decremented successfully', [
            'user' => [
                'uid' => $user->uid,
                'remaining_limit' => $user->limit,
            ],
            'device' => [
                'device' => $deviceItem->device,
                'item_id' => $deviceItem->item_id,
                'remaining_limit' => $deviceItem->limit,
            ]
        ]);
    }
    

    public function exportExcel()
    {
        return Excel::download(new VendingsExport, 'records_vending.xlsx');
    }
    
}
