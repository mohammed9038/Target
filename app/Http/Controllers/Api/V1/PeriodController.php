<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ActiveMonthYear;
use Illuminate\Http\Request;

class PeriodController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        $query = ActiveMonthYear::query();

        if ($request->filled('is_open')) {
            $query->where('is_open', $request->boolean('is_open'));
        }

        $periods = $query->orderBy('year', 'desc')
                        ->orderBy('month', 'desc')
                        ->get();

        return response()->json([
            'data' => $periods
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2020|max:2030',
            'month' => 'required|integer|min:1|max:12',
            'is_open' => 'boolean',
        ]);

        $period = ActiveMonthYear::create($request->all());

        return response()->json([
            'data' => $period,
            'message' => 'Period created successfully'
        ], 201);
    }

    public function update(Request $request, $year, $month)
    {
        $request->validate([
            'is_open' => 'required|boolean',
        ]);

        $period = ActiveMonthYear::where('year', $year)
                                ->where('month', $month)
                                ->first();

        if (!$period) {
            return response()->json([
                'message' => 'Period not found'
            ], 404);
        }

        $period->update(['is_open' => $request->is_open]);

        return response()->json([
            'data' => $period,
            'message' => 'Period updated successfully'
        ]);
    }

    public function checkStatus(Request $request)
    {
        $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $period = ActiveMonthYear::where('year', $request->year)
                                ->where('month', $request->month)
                                ->first();

        if (!$period) {
            return response()->json([
                'exists' => false,
                'is_open' => false,
                'message' => 'Period not found'
            ], 404);
        }

        return response()->json([
            'exists' => true,
            'is_open' => $period->is_open,
            'year' => $period->year,
            'month' => $period->month,
            'period' => $period
        ]);
    }
} 