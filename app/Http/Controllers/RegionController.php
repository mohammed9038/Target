<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function index()
    {
        $regions = Region::paginate(15);
        return view('regions.index', compact('regions'));
    }

    public function create()
    {
        return view('regions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        Region::create($validated);
        return redirect()->route('regions.index')->with('success', 'Region created successfully.');
    }

    public function edit(Region $region)
    {
        return view('regions.edit', compact('region'));
    }

    public function update(Request $request, Region $region)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $region->update($validated);
        return redirect()->route('regions.index')->with('success', 'Region updated successfully.');
    }

    public function destroy(Region $region)
    {
        try {
            // Check if region has any sales targets
            $targetsCount = \App\Models\SalesTarget::where('region_id', $region->id)->count();
            if ($targetsCount > 0) {
                return redirect()->route('regions.index')
                    ->with('error', "Cannot delete region '{$region->name}'. This region has {$targetsCount} sales target(s) assigned. Please reassign or delete the targets first.");
            }

            // Check if region has any salesmen
            if ($region->salesmen()->exists()) {
                return redirect()->route('regions.index')
                    ->with('error', 'Cannot delete region. Please reassign or delete associated salesmen first.');
            }

            // Check if region has any users
            if ($region->users()->exists()) {
                return redirect()->route('regions.index')
                    ->with('error', 'Cannot delete region. Please reassign or delete associated users first.');
            }

            $region->delete();
            return redirect()->route('regions.index')
                ->with('success', 'Region deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('regions.index')
                ->with('error', 'Failed to delete region. Please try again.');
        }
    }
} 