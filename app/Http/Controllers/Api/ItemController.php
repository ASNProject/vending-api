<?php

namespace App\Http\Controllers\Api;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Http\Resources\Resource;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class ItemController extends Controller
{
    /**
     * index
     */
    public function index()
    {
        $items = Item::latest()->paginate(10);

        if ($items->isEmpty()) {
            return new Resource(false, 'No items found', null);
        }

        return new Resource(true, 'Items retrieved successfully', $items);
    }

    /**
     * store
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:items,name',
        ]);

        if ($validator->fails()) {
            return new Resource(false, 'Validation error', $validator->errors());
        }

        $item = Item::create([
            'name' => $request->name
        ]);

        return new Resource(true, 'Item created successfully', $item);
    }

    /**
     * show
     */
    public function show($id)
    {
        $items = Item::where('id', $id)->get();

        if ($items->isEmpty()) {
            return new Resource(false, 'Item not found', null);
        }

        return new Resource(true, 'Items retrieved successfully', $items);
    }

    /**
     * update
     */
    public function update(Request $request, $id)
    {
        $item = Item::find($id);

        if (!$item) {
            return new Resource(false, 'Item not found', null);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:items,name,' . $id,
        ]);

        if ($validator->fails()) {
            return new Resource(false, 'Validation error', $validator->errors());
        }

        $item->update([
            'name' => $request->name
        ]);

        return new Resource(true, 'Item updated successfully', $item);
    }

    /**
     * destroy
     */
    public function destroy($id)
    {
        $item = Item::find($id);

        if (!$item) {
            return new Resource(false, 'Item not found', null);
        }

        $item->delete();

        return new Resource(true, 'Item deleted successfully', null);
    }
}
