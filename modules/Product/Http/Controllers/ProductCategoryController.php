<?php

namespace Modules\Product\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Exception;
use Modules\Product\Models\ProductCategory;
use Modules\Product\Http\Requests\ProductCategoryRequest;

class ProductCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductCategory::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%");
        }

        $categories = $query->orderBy('name')->get();

        return inertia('app/products/categories/Index', [
            'categories' => $categories,
            'filters' => [
                'search' => $request->search
            ]
        ]);
    }

    public function create()
    {
        return inertia('app/products/categories/Create');
    }

    public function store(ProductCategoryRequest $request)
    {
        try {
            DB::beginTransaction();

            ProductCategory::create([
                'name' => $request->name,
            ]);

            DB::commit();

            Inertia::flash('toast', [
                'type' => "success",
                'message' => "Product Category created successfully"
            ]);

            return to_route('product-categories.index');
        } catch (Exception $e) {
            DB::rollback();

            Inertia::flash('toast', [
                'type' => "error",
                'message' => "Failed to save category: {$e->getMessage()}"
            ]);

            return back()->withInput();
        }
    }

    public function edit(ProductCategory $product_category)
    {
        return inertia('app/products/categories/Edit', [
            'product_category' => $product_category
        ]);
    }

    public function update(ProductCategory $product_category, ProductCategoryRequest $request)
    {
        try {
            DB::beginTransaction();

            $product_category->update([
                'name' => $request->name,
            ]);

            DB::commit();

            Inertia::flash('toast', [
                'type' => "success",
                'message' => "Category: {$request->name} updated successfully"
            ]);

            return to_route('product-categories.index');
        } catch (Exception $e) {
            DB::rollBack();

            Inertia::flash('toast', [
                'type' => "error",
                'message' => "Failed to update category: {$e->getMessage()}"
            ]);

            return back()->withInput();
        }
    }

    public function destroy(ProductCategory $product_category)
    {
        try {
            $product_category->delete();

            Inertia::flash('toast', [
                'type' => "success",
                'message' => "Category deleted successfully"
            ]);

            return to_route('product-categories.index');
        } catch (Exception $e) {
            Inertia::flash('toast', [
                'type' => "error",
                'message' => "Failed to delete category: {$e->getMessage()}"
            ]);

            return back()->withInput();
        }
    }
}