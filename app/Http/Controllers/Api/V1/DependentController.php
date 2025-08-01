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
        
        // Apply user scope for managers
        if ($user->isManager()) {
            $regionIds = $user->getRegionIds();
            if (!empty($regionIds)) {
                $query->whereIn('id', $regionIds);
            }
        }
        
        $regions = $query->orderBy('name')->get();
        return response()->json(['data' => $regions]);
    }

    public function channels()
    {
        $user = auth()->user();
        $query = Channel::where('is_active', true);
        
        // Apply user scope for managers
        if ($user->isManager()) {
            $channelIds = $user->getChannelIds();
            if (!empty($channelIds)) {
                $query->whereIn('id', $channelIds);
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

        // Apply user scope for managers
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
                $query->where('classification', $user->classification);
            }
        }

        $salesmen = $query->orderBy('name')->get();
        return response()->json(['data' => $salesmen]);
    }

    public function suppliers()
    {
        $user = auth()->user();
        $query = Supplier::orderBy('name');
        
        // For managers, filter suppliers by classification that matches their permission
        if ($user->isManager() && $user->classification && $user->classification !== 'both') {
            $query->where('classification', $user->classification);
        }
        
        $suppliers = $query->get();
        return response()->json(['data' => $suppliers]);
    }

    public function categories()
    {
        $user = auth()->user();
        $query = Category::with('supplier')->orderBy('name');
        
        // For managers, only show categories that belong to suppliers they can access
        if ($user->isManager() && $user->classification && $user->classification !== 'both') {
            $query->whereHas('supplier', function ($q) use ($user) {
                $q->where('classification', $user->classification);
            });
        }
        
        $categories = $query->get();
        return response()->json(['data' => $categories]);
    }
} 