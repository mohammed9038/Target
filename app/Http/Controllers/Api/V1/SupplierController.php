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
        $supplier->delete();

        return response()->json([
            'message' => 'Supplier deleted successfully'
        ]);
    }
} 