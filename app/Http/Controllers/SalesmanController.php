<?php

namespace App\Http\Controllers;

use App\Models\Salesman;
use App\Models\Region;
use App\Models\Channel;
use Illuminate\Http\Request;

class SalesmanController extends Controller
{
    public function index()
    {
        $salesmen = Salesman::with(['region', 'channel', 'classifications'])->paginate(15);
        return view('salesmen.index', compact('salesmen'));
    }

    public function create()
    {
        $regions = Region::all();
        $channels = Channel::all();
        return view('salesmen.create', compact('regions', 'channels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_code' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'region_id' => 'required|exists:regions,id',
            'channel_id' => 'required|exists:channels,id',
            'classifications' => 'required|array|min:1',
            'classifications.*' => 'in:food,non_food',
        ]);

        // Remove classifications from validated data for salesman creation
        $classifications = $validated['classifications'];
        unset($validated['classifications']);

        $salesman = Salesman::create($validated);
        
        // Add classifications
        foreach ($classifications as $classification) {
            \App\Models\SalesmanClassification::create([
                'salesman_id' => $salesman->id,
                'classification' => $classification
            ]);
        }
        return redirect()->route('salesmen.index')->with('success', 'Salesman created successfully.');
    }

    public function edit(Salesman $salesman)
    {
        $regions = Region::all();
        $channels = Channel::all();
        return view('salesmen.edit', compact('salesman', 'regions', 'channels'));
    }

    public function update(Request $request, Salesman $salesman)
    {
        $validated = $request->validate([
            'employee_code' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'region_id' => 'required|exists:regions,id',
            'channel_id' => 'required|exists:channels,id',
            'classifications' => 'required|array|min:1',
            'classifications.*' => 'in:food,non_food',
        ]);

        // Remove classifications from validated data for salesman update
        $classifications = $validated['classifications'];
        unset($validated['classifications']);

        $salesman->update($validated);
        
        // Update classifications
        $salesman->classifications()->delete(); // Remove existing classifications
        foreach ($classifications as $classification) {
            \App\Models\SalesmanClassification::create([
                'salesman_id' => $salesman->id,
                'classification' => $classification
            ]);
        }
        return redirect()->route('salesmen.index')->with('success', 'Salesman updated successfully.');
    }

    public function destroy(Salesman $salesman)
    {
        // Check if salesman has any sales targets
        $targetsCount = \App\Models\SalesTarget::where('salesman_id', $salesman->id)->count();
        
        if ($targetsCount > 0) {
            return redirect()->route('salesmen.index')->with('error', "Cannot delete salesman '{$salesman->name}'. This salesman has {$targetsCount} sales target(s) assigned. Please reassign or delete the targets first.");
        }

        $salesman->delete();
        return redirect()->route('salesmen.index')->with('success', 'Salesman deleted successfully.');
    }
} 