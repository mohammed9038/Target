<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::orderBy('name')->get();
        
        return response()->json([
            'data' => $suppliers
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_code' => 'required|string|unique:suppliers,supplier_code',
            'name' => 'required|string|max:255',
            'classification' => 'required|in:food,non_food',
        ]);

        $supplier = Supplier::create($request->all());

        return response()->json([
            'data' => $supplier,
            'message' => 'Supplier created successfully'
        ], 201);
    }

    public function show(Supplier $supplier)
    {
        return response()->json([
            'data' => $supplier
        ]);
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'supplier_code' => ['required', 'string', Rule::unique('suppliers')->ignore($supplier->id)],
            'name' => 'required|string|max:255',
            'classification' => 'required|in:food,non_food',
        ]);

        $supplier->update($request->all());

        return response()->json([
            'data' => $supplier,
            'message' => 'Supplier updated successfully'
        ]);
    }

    public function destroy(Supplier $supplier)
    {
        // Check if supplier has any sales targets
        $targetsCount = \App\Models\SalesTarget::where('supplier_id', $supplier->id)->count();
        
        if ($targetsCount > 0) {
            return response()->json([
                'message' => "Cannot delete supplier '{$supplier->name}'. This supplier has {$targetsCount} sales target(s) assigned. Please reassign or delete the targets first."
            ], 422);
        }

        // Check if supplier has any categories
        $categoriesCount = \App\Models\Category::where('supplier_id', $supplier->id)->count();
        
        if ($categoriesCount > 0) {
            return response()->json([
                'message' => "Cannot delete supplier '{$supplier->name}'. This supplier has {$categoriesCount} categor(ies) assigned. Please delete the categories first."
            ], 422);
        }

        $supplier->delete();

        return response()->json([
            'message' => 'Supplier deleted successfully'
        ]);
    }
} 