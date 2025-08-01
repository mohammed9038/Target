<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SalesTarget;
use App\Models\ActiveMonthYear;
use App\Models\Salesman;
use App\Models\Supplier;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get dashboard statistics
        $stats = [
            'targets_count' => SalesTarget::count(),
            'open_periods_count' => ActiveMonthYear::where('is_open', true)->count(),
            'salesmen_count' => Salesman::count(),
            'suppliers_count' => Supplier::count(),
        ];
        
        // Get recent targets (limit to 5)
        $recentTargets = SalesTarget::with(['salesman.region', 'salesman.channel', 'supplier', 'category'])
            ->latest()
            ->limit(5)
            ->get();
        
        return view('dashboard', compact('user', 'stats', 'recentTargets'));
    }
} 