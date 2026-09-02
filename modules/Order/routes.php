<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Http\Controllers\OrderController;

Route::middleware('role:admin,super_admin,cashier')->group(function ()
{
    Route::prefix('orders')
        ->name('orders.')
        ->controller(OrderController::class)
        ->group(function ()
    {
        Route::get('/', 'index')->name('index');
    });
});

Route::middleware('role:admin,super_admin')->group(function ()
{
    Route::prefix('orders')
        ->name('orders.')
        ->controller(OrderController::class)
        ->group(function ()
    {
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{order:uuid}/edit', 'edit')->name('edit');
        Route::put('/{order:uuid}', 'update')->name('update');
        Route::delete('/{order:uuid}', 'destroy')->name('destroy');
    });
});

