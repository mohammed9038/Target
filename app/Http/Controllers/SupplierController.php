<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::paginate(15);
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'classification' => 'required|in:food,non_food',
        ]);

        Supplier::create($validated);
        return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully.');
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'classification' => 'required|in:food,non_food',
        ]);

        $supplier->update($validated);
        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        // Check if supplier has any sales targets
        $targetsCount = \App\Models\SalesTarget::where('supplier_id', $supplier->id)->count();
        
        if ($targetsCount > 0) {
            return redirect()->route('suppliers.index')->with('error', "Cannot delete supplier '{$supplier->name}'. This supplier has {$targetsCount} sales target(s) assigned. Please reassign or delete the targets first.");
        }

        // Check if supplier has any categories
        $categoriesCount = \App\Models\Category::where('supplier_id', $supplier->id)->count();
        
        if ($categoriesCount > 0) {
            return redirect()->route('suppliers.index')->with('error', "Cannot delete supplier '{$supplier->name}'. This supplier has {$categoriesCount} categor(ies) assigned. Please delete the categories first.");
        }

        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');
    }
} 