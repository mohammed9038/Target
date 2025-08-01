<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RegionController extends Controller
{
    public function index()
    {
        $regions = Region::orderBy('name')->get();
        
        return response()->json([
            'data' => $regions
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'region_code' => 'required|string|unique:regions,region_code',
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $region = Region::create($request->all());

        return response()->json([
            'data' => $region,
            'message' => 'Region created successfully'
        ], 201);
    }

    public function show(Region $region)
    {
        return response()->json([
            'data' => $region
        ]);
    }

    public function update(Request $request, Region $region)
    {
        $request->validate([
            'region_code' => ['required', 'string', Rule::unique('regions')->ignore($region->id)],
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $region->update($request->all());

        return response()->json([
            'data' => $region,
            'message' => 'Region updated successfully'
        ]);
    }

    public function destroy(Region $region)
    {
        // Check if region has any sales targets
        $targetsCount = \App\Models\SalesTarget::where('region_id', $region->id)->count();
        
        if ($targetsCount > 0) {
            return response()->json([
                'message' => "Cannot delete region '{$region->name}'. This region has {$targetsCount} sales target(s) assigned. Please reassign or delete the targets first."
            ], 422);
        }

        // Check if region has any salesmen
        $salesmenCount = \App\Models\Salesman::where('region_id', $region->id)->count();
        
        if ($salesmenCount > 0) {
            return response()->json([
                'message' => "Cannot delete region '{$region->name}'. This region has {$salesmenCount} salesman/salesmen assigned. Please reassign the salesmen first."
            ], 422);
        }

        $region->delete();

        return response()->json([
            'message' => 'Region deleted successfully'
        ]);
    }
} 