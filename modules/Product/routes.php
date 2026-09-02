<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\ProductController;
use Modules\Product\Http\Controllers\ProductCategoryController;

Route::middleware('role:admin,super_admin,cashier')->group(function ()
{
    Route::prefix('products')
        ->name('products.')
        ->controller(ProductController::class)
        ->group(function ()
    {
        Route::get('/', 'index')->name('index');
    });
    
    Route::prefix('product-categories')
        ->name('product-categories.')
        ->controller(ProductCategoryController::class)
        ->group(function ()
    {
        Route::get('/', [ProductCategoryController::class, 'index'])->name('index');
    });
});

Route::middleware('role:admin,super_admin')->group(function ()
{
    Route::prefix('products')
        ->name('products.')
        ->controller(ProductController::class)
        ->group(function ()
    {
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{product}/edit', 'edit')->name('edit');
        Route::put('/{product}', 'update')->name('update');
        Route::post('/products/{product}/toggle-attribute', 'toggleAttribute')->name('toggle-attribute');
        Route::delete('/{product}', 'destroy')->name('destroy');
    });
    
    Route::prefix('product-categories')
        ->name('product-categories.')
        ->controller(ProductCategoryController::class)
        ->group(function ()
    {
        Route::get('/create', [ProductCategoryController::class, 'create'])->name('create');
        Route::post('/', [ProductCategoryController::class, 'store'])->name('store');
        Route::get('/{product_category}/edit', [ProductCategoryController::class, 'edit'])->name('edit');
        Route::put('/{product_category}', [ProductCategoryController::class, 'update'])->name('update');
        Route::delete('/{product_category}', [ProductCategoryController::class, 'destroy'])->name('destroy');
    });
});

