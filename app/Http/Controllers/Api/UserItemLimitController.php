<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\UserItemLimit;
use App\Http\Resources\Resource;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class UserItemLimitController extends Controller
{
    /**
     * index
     */
    public function index()
    {
        $userItemLimits = UserItemLimit::latest()->paginate(10);

        if ($userItemLimits->isEmpty()) {
            return new Resource(false, 'No user item limits found', null);
        }

        return new Resource(true, 'User item limits retrieved successfully', $userItemLimits);
    }

    /**
     * store (API)
     */
    public function store(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'uid'   => 'required|unique:user_item_limits,uid',
            'name'  => 'required|unique:user_item_limits,name',
            'role'  => 'required',
            'limit' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return new Resource(false, 'Validation error', $validator->errors());
        }

        $userItemLimit = UserItemLimit::create($request->only(['uid', 'name', 'role', 'limit']));

        return new Resource(true, 'User item limit created successfully', $userItemLimit);
    }

    /**
     * show (by uid)
     */
    public function show($uid)
    {
        $userItemLimit = UserItemLimit::where('uid', $uid)->first();

        if (!$userItemLimit) {
            return new Resource(false, 'User item limit not found', null);
        }

        return new Resource(true, 'User item limit retrieved successfully', $userItemLimit);
    }

    /**
     * update (by uid)
     */
    public function update(Request $request, $uid)
    {
        $userItemLimit = UserItemLimit::where('uid', $uid)->first();

        if (!$userItemLimit) {
            return new Resource(false, 'User item limit not found', null);
        }

        $validator = Validator::make($request->all(), [
            'name'  => 'sometimes|required|unique:user_item_limits,name,' . $userItemLimit->id,
            'role'  => 'sometimes|required',
            'limit' => 'sometimes|required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return new Resource(false, 'Validation error', $validator->errors());
        }

        $userItemLimit->update($request->only(['name', 'role', 'limit']));

        return new Resource(true, 'User item limit updated successfully', $userItemLimit);
    }

    /**
     * destroy (by id)
     */
    public function destroy($id)
    {
        $userItemLimit = UserItemLimit::find($id);

        if (!$userItemLimit) {
            return new Resource(false, 'User item limit not found', null);
        }

        $userItemLimit->delete();

        return new Resource(true, 'User item limit deleted successfully', null);
    }
}
