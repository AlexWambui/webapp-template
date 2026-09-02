<?php

use Illuminate\Support\Facades\Route;

use Modules\ContactMessage\Http\Controllers\ContactMessageController;

Route::get('/contact', [ContactMessageController::class, 'create'])->name('contact-messages.create');
Route::post('/contact', [ContactMessageController::class, 'store'])->name('contact-messages.store');


Route::prefix('contact-messages')
    ->middleware('role:admin,super_admin')
    ->name('contact-messages.')
    ->controller(ContactMessageController::class)
    ->group(function()
{
    Route::get('/', 'index')->name('index');
    Route::get('/{contact_message:uuid}/edit', 'edit')->name('edit');
    Route::put('/{contact_message:uuid}', 'update')->name('update');
    Route::patch('/{contact_message}/toggle-resolved', 'toggleResolved')->name('toggle-resolved');
    Route::delete('/{contact_message:uuid}', 'destroy')->name('destroy');
});