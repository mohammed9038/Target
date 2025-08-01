<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('supplier')->orderBy('name')->get();
        
        return response()->json([
            'data' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_code' => 'required|string',
            'name' => 'required|string|max:255',
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        // Check unique constraint per supplier
        $request->validate([
            'category_code' => [
                Rule::unique('categories')->where(function ($query) use ($request) {
                    return $query->where('supplier_id', $request->supplier_id);
                })
            ],
        ]);

        $category = Category::create($request->all());

        return response()->json([
            'data' => $category->load('supplier'),
            'message' => 'Category created successfully'
        ], 201);
    }

    public function show(Category $category)
    {
        return response()->json([
            'data' => $category->load('supplier')
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'category_code' => 'required|string',
            'name' => 'required|string|max:255',
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        // Check unique constraint per supplier
        $request->validate([
            'category_code' => [
                Rule::unique('categories')->where(function ($query) use ($request) {
                    return $query->where('supplier_id', $request->supplier_id);
                })->ignore($category->id)
            ],
        ]);

        $category->update($request->all());

        return response()->json([
            'data' => $category->load('supplier'),
            'message' => 'Category updated successfully'
        ]);
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully'
        ]);
    }
} 