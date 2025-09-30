<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DeviceManagementController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\UserItemLimitController;
use App\Http\Controllers\Api\VendingController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// GET all (with optional ?device=xxx)
Route::get('/devices', [DeviceManagementController::class, 'index']);
Route::get('/items', [ItemController::class, 'index']);
Route::get('/user-item-limits', [UserItemLimitController::class, 'index']);
Route::get('/vendings', [VendingController::class, 'index']);

// POST create + items (API version)
Route::post('/devices', [DeviceManagementController::class, 'store']);
Route::post('/items', [ItemController::class, 'store']);
Route::post('/user-item-limits', [UserItemLimitController::class, 'store']);
Route::post('/vendings', [VendingController::class, 'vending']);

// GET detail by name
Route::get('/devices/{device}', [DeviceManagementController::class, 'show']);
Route::get('/items/{id}', [ItemController::class, 'show']);
Route::get('/user-item-limits/{uid}', [UserItemLimitController::class, 'show']);

// PUT/PATCH update item limit
Route::put('/devices/{device}', [DeviceManagementController::class, 'update']);
Route::patch('/devices/{device}', [DeviceManagementController::class, 'update']);
Route::put('/items/{id}', [ItemController::class, 'update']);
Route::put('/user-item-limits/{uid}', [UserItemLimitController::class, 'update']);

// DELETE item by id
Route::delete('/devices/{id}', [DeviceManagementController::class, 'destroy']);
Route::delete('/items/{id}', [ItemController::class, 'destroy']);
Route::delete('/user-item-limits/{id}', [UserItemLimitController::class, 'destroy']);

// EXPORT
Route::get('/vendings/export/excel', [VendingController::class, 'exportExcel']);