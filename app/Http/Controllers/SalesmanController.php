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
        $salesmen = Salesman::with(['region', 'channel'])->paginate(15);
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
            'classification' => 'required|in:food,non_food,both',
        ]);

        Salesman::create($validated);
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
            'classification' => 'required|in:food,non_food,both',
        ]);

        $salesman->update($validated);
        return redirect()->route('salesmen.index')->with('success', 'Salesman updated successfully.');
    }

    public function destroy(Salesman $salesman)
    {
        $salesman->delete();
        return redirect()->route('salesmen.index')->with('success', 'Salesman deleted successfully.');
    }
} 