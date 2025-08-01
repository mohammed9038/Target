<?php

namespace App\Http\Controllers;

use App\Models\ActiveMonthYear;
use Illuminate\Http\Request;

class PeriodController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }
    public function index(Request $request)
    {
        $query = ActiveMonthYear::query();

        // Filter by status (default to open)
        $status = $request->get('status', 'open');
        if ($status === 'open') {
            $query->where('is_open', true);
        } elseif ($status === 'closed') {
            $query->where('is_open', false);
        }

        // Filter by year
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        // Filter by month
        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        // Sort by year and month descending
        $query->orderBy('year', 'desc')->orderBy('month', 'desc');

        $periods = $query->paginate(15)->withQueryString();

        // Get unique years and months for filters
        $years = ActiveMonthYear::distinct()->orderBy('year', 'desc')->pluck('year');
        $months = range(1, 12);

        return view('periods.index', compact('periods', 'years', 'months', 'status'));
    }

    public function create()
    {
        return view('periods.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'year' => 'required|integer|min:2020|max:2030',
            'month' => 'required|integer|min:1|max:12',
            'is_open' => 'boolean',
        ]);

        ActiveMonthYear::create($validated);
        return redirect()->route('periods.index')->with('success', 'Period created successfully.');
    }

    public function edit(ActiveMonthYear $period)
    {
        return view('periods.edit', compact('period'));
    }

    public function update(Request $request, ActiveMonthYear $period)
    {
        // Check if this is just a toggle request (only is_open field)
        if ($request->has('is_open') && !$request->has('year') && !$request->has('month')) {
            // Simple toggle for open/close button
            $isOpen = $request->input('is_open') === '1';
            
            $period->update(['is_open' => $isOpen]);
            
            $status = $isOpen ? 'opened' : 'closed';
            return redirect()->route('periods.index')->with('success', "Period {$period->year}-" . date('M', mktime(0, 0, 0, $period->month, 1)) . " has been {$status} successfully.");
        }
        
        // Full update validation for edit form
        $validated = $request->validate([
            'year' => 'required|integer|min:2020|max:2030',
            'month' => 'required|integer|min:1|max:12',
            'is_open' => 'boolean',
        ]);

        $period->update($validated);
        return redirect()->route('periods.index')->with('success', 'Period updated successfully.');
    }

    public function destroy(ActiveMonthYear $period)
    {
        $period->delete();
        return redirect()->route('periods.index')->with('success', 'Period deleted successfully.');
    }
} 