<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SalesTarget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TargetsExport;

class ReportController extends Controller
{
    public function summary(Request $request)
    {
        $user = Auth::user();
        $query = SalesTarget::with(['region', 'channel', 'salesman', 'supplier', 'category']);

        // Apply user scope
        if ($user->isManager()) {
            $regionIds = $user->getRegionIds();
            $channelIds = $user->getChannelIds();
            
            if (!empty($regionIds)) {
                $query->whereIn('region_id', $regionIds);
            }
            if (!empty($channelIds)) {
                $query->whereIn('channel_id', $channelIds);
            }
                  
            // Apply classification filter if specified
            if ($user->classification && $user->classification !== 'both') {
                $query->whereHas('salesman', function($q) use ($user) {
                    $q->where('classification', $user->classification);
                });
                
                // Also filter by supplier classification
                $query->whereHas('supplier', function($q) use ($user) {
                    $q->where('classification', $user->classification);
                });
            }
        }

        // Apply filters
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        if ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
        }

        if ($request->filled('channel_id')) {
            $query->where('channel_id', $request->channel_id);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('classification')) {
            if ($request->classification !== 'both') {
                $query->whereHas('salesman', function($q) use ($request) {
                    $q->where('classification', $request->classification);
                });
            }
        }

        if ($request->filled('salesman_id')) {
            $query->where('salesman_id', $request->salesman_id);
        }

        $groupBy = $request->get('group_by', 'supplier');

        switch ($groupBy) {
            case 'supplier':
                $summary = $query->selectRaw('
                    suppliers.name as supplier_name,
                    suppliers.supplier_code,
                    COUNT(*) as target_count,
                    SUM(target_amount) as total_amount
                ')
                ->join('suppliers', 'sales_targets.supplier_id', '=', 'suppliers.id')
                ->groupBy('suppliers.id', 'suppliers.name', 'suppliers.supplier_code')
                ->orderBy('total_amount', 'desc')
                ->get();
                break;

            case 'region':
                $summary = $query->selectRaw('
                    regions.name as region_name,
                    regions.region_code,
                    COUNT(*) as target_count,
                    SUM(target_amount) as total_amount
                ')
                ->join('regions', 'sales_targets.region_id', '=', 'regions.id')
                ->groupBy('regions.id', 'regions.name', 'regions.region_code')
                ->orderBy('total_amount', 'desc')
                ->get();
                break;

            case 'channel':
                $summary = $query->selectRaw('
                    channels.name as channel_name,
                    channels.channel_code,
                    COUNT(*) as target_count,
                    SUM(target_amount) as total_amount
                ')
                ->join('channels', 'sales_targets.channel_id', '=', 'channels.id')
                ->groupBy('channels.id', 'channels.name', 'channels.channel_code')
                ->orderBy('total_amount', 'desc')
                ->get();
                break;

            case 'salesman':
                $summary = $query->selectRaw('
                    salesmen.name as salesman_name,
                    salesmen.salesman_code,
                    salesmen.employee_code,
                    COUNT(*) as target_count,
                    SUM(target_amount) as total_amount
                ')
                ->join('salesmen', 'sales_targets.salesman_id', '=', 'salesmen.id')
                ->groupBy('salesmen.id', 'salesmen.name', 'salesmen.salesman_code', 'salesmen.employee_code')
                ->orderBy('total_amount', 'desc')
                ->get();
                break;

            default:
                $summary = $query->selectRaw('
                    suppliers.name as supplier_name,
                    suppliers.supplier_code,
                    COUNT(*) as target_count,
                    SUM(target_amount) as total_amount
                ')
                ->join('suppliers', 'sales_targets.supplier_id', '=', 'suppliers.id')
                ->groupBy('suppliers.id', 'suppliers.name', 'suppliers.supplier_code')
                ->orderBy('total_amount', 'desc')
                ->get();
        }

        return response()->json([
            'data' => $summary,
            'group_by' => $groupBy
        ]);
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        
        // Apply user scope for managers
        $scope = null;
        if ($user->isManager()) {
            $scope = [
                'region_id' => $user->region_id,
                'channel_id' => $user->channel_id,
                'classification' => $user->classification,
            ];
        }

        $filters = $request->only([
            'year', 'month', 'region_id', 'channel_id', 
            'supplier_id', 'category_id', 'classification', 'salesman_id'
        ]);

        return Excel::download(
            new TargetsExport($filters, $scope),
            'targets_export_' . date('Y-m-d_H-i-s') . '.xlsx'
        );
    }
} 