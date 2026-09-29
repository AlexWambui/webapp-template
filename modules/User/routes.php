<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Modules\User\Models\User;

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

Route::middleware('role:admin,super_admin,cashier')
    ->get('/customers/lookup', function (Request $request) {
        $phone = preg_replace('/\D/', '', (string) $request->query('phone'));

                if ($phone === '' || strlen($phone) < 9) {
            return response()->json(null);
        }

        // Normalize to 2547XXXXXXXX
        if (str_starts_with($phone, '0')) {
            $phone = '254' . substr($phone, 1);
        } elseif (str_starts_with($phone, '7') || str_starts_with($phone, '1')) {
            $phone = '254' . $phone;
        }

        $user = User::query()
            ->where(function ($q) use ($phone) {
                $q->where('phone', $phone)
                  ->orWhere('phone', '+' . $phone)
                  ->orWhere('phone', '0' . substr($phone, 3));
            })
            ->first(['id', 'name', 'email', 'phone']);

        return response()->json($user);
    })->name('customers.lookup');