<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\UserController;

Route::middleware('role:admin,super_admin')
    ->prefix('users')
    ->name('users.')
    ->controller(UserController::class)
    ->group(function ()
{
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{user:uuid}/edit', 'edit')->name('edit');
    Route::put('/{user:uuid}', 'update')->name('update');
    Route::delete('/{user:uuid}', 'destroy')->name('destroy');
});