<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('supplier')->paginate(15);
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        return view('categories.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        Category::create($validated);
        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        $suppliers = Supplier::all();
        return view('categories.edit', compact('category', 'suppliers'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        $category->update($validated);
        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        // Check if category has any sales targets
        $targetsCount = \App\Models\SalesTarget::where('category_id', $category->id)->count();
        
        if ($targetsCount > 0) {
            return redirect()->route('categories.index')->with('error', "Cannot delete category '{$category->name}'. This category has {$targetsCount} sales target(s) assigned. Please reassign or delete the targets first.");
        }

        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }
} 