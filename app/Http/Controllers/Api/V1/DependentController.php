<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Models\Channel;
use App\Models\Salesman;
use App\Models\Supplier;
use App\Models\Category;
use Illuminate\Http\Request;

class DependentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function regions()
    {
        $user = auth()->user();
        $query = Region::where('is_active', true);
        
        // Apply user scope for non-admin users
        if (!$user->isAdmin()) {
            $scope = $user->scope();
            if (!empty($scope['region_ids'])) {
                $query->whereIn('id', $scope['region_ids']);
            }
        }
        
        $regions = $query->orderBy('name')->get();
        return response()->json(['data' => $regions]);
    }

    public function channels()
    {
        $user = auth()->user();
        $query = Channel::where('is_active', true);
        
        // Apply user scope for non-admin users
        if (!$user->isAdmin()) {
            $scope = $user->scope();
            if (!empty($scope['channel_ids'])) {
                $query->whereIn('id', $scope['channel_ids']);
            }
        }
        
        $channels = $query->orderBy('name')->get();
        return response()->json(['data' => $channels]);
    }

    public function salesmen()
    {
        $user = auth()->user();
        $query = Salesman::whereHas('region', function ($query) {
                $query->where('is_active', true);
            })
            ->whereHas('channel', function ($query) {
                $query->where('is_active', true);
            });

        // Apply user scope for non-admin users
        if (!$user->isAdmin()) {
            $scope = $user->scope();
            
            if (!empty($scope['region_ids'])) {
                $query->whereIn('region_id', $scope['region_ids']);
            }
            if (!empty($scope['channel_ids'])) {
                $query->whereIn('channel_id', $scope['channel_ids']);
            }
            // Apply classification filter if specified
            if (isset($scope['classification']) && $scope['classification'] !== 'both') {
                $query->where('classification', $scope['classification']);
            }
        }

        $salesmen = $query->orderBy('name')->get();
        return response()->json(['data' => $salesmen]);
    }

    public function suppliers()
    {
        $user = auth()->user();
        $query = Supplier::orderBy('name');
        
        // For non-admin users, filter suppliers by classification that matches their permission
        if (!$user->isAdmin()) {
            $scope = $user->scope();
            if (isset($scope['classification']) && $scope['classification'] !== 'both') {
                $query->where('classification', $scope['classification']);
            }
        }
        
        $suppliers = $query->get();
        return response()->json(['data' => $suppliers]);
    }

    public function categories()
    {
        $user = auth()->user();
        $query = Category::with('supplier')->orderBy('name');
        
        // For non-admin users, only show categories that belong to suppliers they can access
        if (!$user->isAdmin()) {
            $scope = $user->scope();
            if (isset($scope['classification']) && $scope['classification'] !== 'both') {
                $query->whereHas('supplier', function ($q) use ($scope) {
                    $q->where('classification', $scope['classification']);
                });
            }
        }
        
        $categories = $query->get();
        return response()->json(['data' => $categories]);
    }
} 