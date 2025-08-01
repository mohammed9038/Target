<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Salesman;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SalesmanController extends Controller
{
    public function index()
    {
        $salesmen = Salesman::with(['region', 'channel'])->orderBy('name')->get();
        
        return response()->json([
            'data' => $salesmen
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_code' => 'required|string|unique:salesmen,employee_code',
            'salesman_code' => 'nullable|string',
            'name' => 'required|string|max:255',
            'region_id' => 'required|exists:regions,id',
            'channel_id' => 'required|exists:channels,id',
            'classification' => 'required|in:food,non_food,both',
        ]);

        $salesman = Salesman::create($request->all());

        return response()->json([
            'data' => $salesman->load(['region', 'channel']),
            'message' => 'Salesman created successfully'
        ], 201);
    }

    public function show(Salesman $salesman)
    {
        return response()->json([
            'data' => $salesman->load(['region', 'channel'])
        ]);
    }

    public function update(Request $request, Salesman $salesman)
    {
        $request->validate([
            'employee_code' => ['required', 'string', Rule::unique('salesmen')->ignore($salesman->id)],
            'salesman_code' => 'nullable|string',
            'name' => 'required|string|max:255',
            'region_id' => 'required|exists:regions,id',
            'channel_id' => 'required|exists:channels,id',
            'classification' => 'required|in:food,non_food,both',
        ]);

        $salesman->update($request->all());

        return response()->json([
            'data' => $salesman->load(['region', 'channel']),
            'message' => 'Salesman updated successfully'
        ]);
    }

    public function destroy(Salesman $salesman)
    {
        $salesman->delete();

        return response()->json([
            'message' => 'Salesman deleted successfully'
        ]);
    }
} 