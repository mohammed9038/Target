<?php

namespace App\Http\Controllers;

use App\Models\SalesTarget;
use App\Models\Region;
use App\Models\Channel;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\Salesman;
use App\Models\ActiveMonthYear;
use Illuminate\Http\Request;

class TargetController extends Controller
{
    public function index()
    {
        $targets = SalesTarget::with(['salesman.region', 'salesman.channel', 'supplier', 'category'])->paginate(15);
        $regions = Region::all();
        $channels = Channel::all();
        $suppliers = Supplier::all();
        $categories = Category::all();
        $salesmen = Salesman::all();
        $activePeriods = ActiveMonthYear::where('is_open', true)->get();
        
        return view('targets.index', compact('targets', 'regions', 'channels', 'suppliers', 'categories', 'salesmen', 'activePeriods'));
    }

    public function create()
    {
        $regions = Region::all();
        $channels = Channel::all();
        $suppliers = Supplier::all();
        $categories = Category::all();
        $salesmen = Salesman::all();
        $activePeriods = ActiveMonthYear::where('is_open', true)->get();
        
        return view('targets.create', compact('regions', 'channels', 'suppliers', 'categories', 'salesmen', 'activePeriods'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'year' => 'required|integer|min:2020|max:2030',
            'month' => 'required|integer|min:1|max:12',
            'salesman_id' => 'required|exists:salesmen,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'category_id' => 'required|exists:categories,id',
            'target_amount' => 'required|numeric|min:0',
        ]);

        // Check for existing target
        $existingTarget = SalesTarget::where([
            'year' => $validated['year'],
            'month' => $validated['month'],
            'salesman_id' => $validated['salesman_id'],
            'supplier_id' => $validated['supplier_id'],
            'category_id' => $validated['category_id'],
        ])->first();

        if ($existingTarget) {
            return back()->withErrors(['target' => 'A target already exists for this combination.'])->withInput();
        }

        SalesTarget::create($validated);
        return redirect()->route('targets.index')->with('success', 'Sales target created successfully.');
    }

    public function edit(SalesTarget $target)
    {
        $regions = Region::all();
        $channels = Channel::all();
        $suppliers = Supplier::all();
        $categories = Category::all();
        $salesmen = Salesman::all();
        $activePeriods = ActiveMonthYear::where('is_open', true)->get();
        
        return view('targets.edit', compact('target', 'regions', 'channels', 'suppliers', 'categories', 'salesmen', 'activePeriods'));
    }

    public function update(Request $request, SalesTarget $target)
    {
        $validated = $request->validate([
            'year' => 'required|integer|min:2020|max:2030',
            'month' => 'required|integer|min:1|max:12',
            'salesman_id' => 'required|exists:salesmen,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'category_id' => 'required|exists:categories,id',
            'target_amount' => 'required|numeric|min:0',
        ]);

        // Check for existing target (excluding current one)
        $existingTarget = SalesTarget::where([
            'year' => $validated['year'],
            'month' => $validated['month'],
            'salesman_id' => $validated['salesman_id'],
            'supplier_id' => $validated['supplier_id'],
            'category_id' => $validated['category_id'],
        ])->where('id', '!=', $target->id)->first();

        if ($existingTarget) {
            return back()->withErrors(['target' => 'A target already exists for this combination.'])->withInput();
        }

        $target->update($validated);
        return redirect()->route('targets.index')->with('success', 'Sales target updated successfully.');
    }

    public function destroy(SalesTarget $target)
    {
        $target->delete();
        return redirect()->route('targets.index')->with('success', 'Sales target deleted successfully.');
    }
} 