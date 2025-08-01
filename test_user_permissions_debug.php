<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use App\Models\Region;
use App\Models\Channel;
use App\Models\Salesman;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\SalesTarget;
use App\Models\ActiveMonthYear;
use Illuminate\Support\Facades\Auth;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== USER PERMISSIONS DEBUG TEST ===\n\n";

// Test all users
$users = [
    'admin' => User::where('username', 'admin')->first(),
    'manager' => User::where('username', 'manager')->first(),
    'manager2' => User::where('username', 'manager2')->first(),
    'manager3' => User::where('username', 'manager3')->first(),
];

foreach ($users as $username => $user) {
    echo "🔍 Testing user: {$username}\n";
    echo "   Role: {$user->role}\n";
    echo "   Classification: " . ($user->classification ?? 'none') . "\n";
    
    $scope = $user->scope();
    if ($scope) {
        echo "   Region IDs: " . implode(', ', $scope['region_ids'] ?? []) . "\n";
        echo "   Channel IDs: " . implode(', ', $scope['channel_ids'] ?? []) . "\n";
        echo "   Classification Filter: " . ($scope['classification'] ?? 'none') . "\n";
    } else {
        echo "   Scope: Admin (no restrictions)\n";
    }
    
    // Test API simulation - what would /api/v1/deps/regions return?
    echo "\n   📊 API SIMULATION:\n";
    
    // Regions
    $regions = Region::where('is_active', true);
    if (!$user->isAdmin()) {
        $userScope = $user->scope();
        if (!empty($userScope['region_ids'])) {
            $regions->whereIn('id', $userScope['region_ids']);
        }
    }
    $regionData = $regions->get(['id', 'name']);
    echo "   Regions API (/api/v1/deps/regions): " . $regionData->count() . " regions\n";
    foreach ($regionData as $region) {
        echo "     - {$region->id}: {$region->name}\n";
    }
    
    // Channels
    $channels = Channel::where('is_active', true);
    if (!$user->isAdmin()) {
        $userScope = $user->scope();
        if (!empty($userScope['channel_ids'])) {
            $channels->whereIn('id', $userScope['channel_ids']);
        }
    }
    $channelData = $channels->get(['id', 'name']);
    echo "   Channels API (/api/v1/deps/channels): " . $channelData->count() . " channels\n";
    foreach ($channelData as $channel) {
        echo "     - {$channel->id}: {$channel->name}\n";
    }
    
    // Suppliers
    $suppliers = Supplier::orderBy('name');
    if (!$user->isAdmin()) {
        $userScope = $user->scope();
        if (isset($userScope['classification']) && $userScope['classification'] !== 'both') {
            $suppliers->where('classification', $userScope['classification']);
        }
    }
    $supplierData = $suppliers->get(['id', 'name', 'classification']);
    echo "   Suppliers API (/api/v1/deps/suppliers): " . $supplierData->count() . " suppliers\n";
    foreach ($supplierData as $supplier) {
        echo "     - {$supplier->id}: {$supplier->name} ({$supplier->classification})\n";
    }
    
    // Salesmen
    $salesmen = Salesman::whereHas('region', function ($query) {
        $query->where('is_active', true);
    })->whereHas('channel', function ($query) {
        $query->where('is_active', true);
    });
    
    if (!$user->isAdmin()) {
        $userScope = $user->scope();
        if (!empty($userScope['region_ids'])) {
            $salesmen->whereIn('region_id', $userScope['region_ids']);
        }
        if (!empty($userScope['channel_ids'])) {
            $salesmen->whereIn('channel_id', $userScope['channel_ids']);
        }
        if (isset($userScope['classification']) && $userScope['classification'] !== 'both') {
            $salesmen->where(function($q) use ($userScope) {
                $q->where('classification', $userScope['classification'])
                  ->orWhere('classification', 'both');
            });
        }
    }
    $salesmenData = $salesmen->get(['id', 'name', 'classification', 'region_id', 'channel_id']);
    echo "   Salesmen API (/api/v1/deps/salesmen): " . $salesmenData->count() . " salesmen\n";
    foreach ($salesmenData as $salesman) {
        echo "     - {$salesman->id}: {$salesman->name} ({$salesman->classification}) R:{$salesman->region_id} C:{$salesman->channel_id}\n";
    }
    
    // Test Matrix Query
    echo "\n   🎯 MATRIX QUERY TEST (August 2025):\n";
    
    // Get period status
    $period = ActiveMonthYear::where('year', 2025)->where('month', 8)->first();
    $isPeriodOpen = $period ? $period->is_open : false;
    echo "   Period (2025-08) Status: " . ($isPeriodOpen ? 'OPEN' : 'CLOSED') . "\n";
    
    // Simulate matrix query with user scope
    $salesmenQuery = Salesman::with(['region', 'channel'])
        ->select('id as salesman_id', 'salesman_code', 'name as salesman_name', 'classification as salesman_classification', 'region_id', 'channel_id');
    
    // Apply user scope filters
    if (!$user->isAdmin()) {
        $userScope = $user->scope();
        if (!empty($userScope['region_ids'])) {
            $salesmenQuery->whereIn('region_id', $userScope['region_ids']);
        }
        if (!empty($userScope['channel_ids'])) {
            $salesmenQuery->whereIn('channel_id', $userScope['channel_ids']);
        }
        if (isset($userScope['classification']) && $userScope['classification'] !== 'both') {
            $salesmenQuery->where(function($q) use ($userScope) {
                $q->where('classification', $userScope['classification'])
                  ->orWhere('classification', 'both');
            });
        }
    }
    
    $matrixSalesmen = $salesmenQuery->get();
    echo "   Matrix Salesmen Count: " . $matrixSalesmen->count() . "\n";
    
    // Suppliers for matrix
    $suppliersQuery = \DB::table('suppliers')
        ->join('categories', 'suppliers.id', '=', 'categories.supplier_id')
        ->select(
            'suppliers.id as supplier_id',
            'suppliers.name as supplier_name',
            'suppliers.classification as supplier_classification',
            'categories.id as category_id',
            'categories.name as category_name'
        );
    
    if (!$user->isAdmin()) {
        $userScope = $user->scope();
        if (isset($userScope['classification']) && $userScope['classification'] !== 'both') {
            $suppliersQuery->where('suppliers.classification', $userScope['classification']);
        }
    }
    
    $matrixSuppliers = $suppliersQuery->get();
    echo "   Matrix Suppliers Count: " . $matrixSuppliers->count() . "\n";
    
    // Existing targets
    $targetsQuery = SalesTarget::where('year', 2025)->where('month', 8);
    if (!$user->isAdmin()) {
        $userScope = $user->scope();
        $targetsQuery->whereHas('salesman', function($q) use ($userScope) {
            if (!empty($userScope['region_ids'])) {
                $q->whereIn('region_id', $userScope['region_ids']);
            }
            if (!empty($userScope['channel_ids'])) {
                $q->whereIn('channel_id', $userScope['channel_ids']);
            }
            if (isset($userScope['classification']) && $userScope['classification'] !== 'both') {
                $q->where(function($subQ) use ($userScope) {
                    $subQ->where('classification', $userScope['classification'])
                         ->orWhere('classification', 'both');
                });
            }
        });
        
        if (isset($userScope['classification']) && $userScope['classification'] !== 'both') {
            $targetsQuery->whereHas('supplier', function($q) use ($userScope) {
                $q->where('classification', $userScope['classification']);
            });
        }
    }
    
    $matrixTargets = $targetsQuery->get(['salesman_id', 'supplier_id', 'category_id', 'target_amount']);
    echo "   Matrix Targets Count: " . $matrixTargets->count() . "\n";
    
    // Would matrix show data?
    $hasMatrixData = $matrixSalesmen->count() > 0 && $matrixSuppliers->count() > 0;
    echo "   MATRIX RESULT: " . ($hasMatrixData ? "✅ WOULD SHOW DATA" : "❌ NO DATA AVAILABLE") . "\n";
    
    if (!$hasMatrixData) {
        echo "   ⚠️  PROBLEM: User cannot see matrix because:\n";
        if ($matrixSalesmen->count() == 0) echo "     - No accessible salesmen\n";
        if ($matrixSuppliers->count() == 0) echo "     - No accessible suppliers\n";
    }
    
    echo "\n" . str_repeat("-", 80) . "\n\n";
}

// Test target creation permissions
echo "🎯 TARGET CREATION PERMISSION TEST:\n\n";

$testUser = User::where('username', 'manager')->first();
echo "Testing target creation for user: manager\n";
echo "User classification: {$testUser->classification}\n";
echo "User regions: " . implode(', ', $testUser->getRegionIds()) . "\n";
echo "User channels: " . implode(', ', $testUser->getChannelIds()) . "\n";

// Try to find a valid combination for target creation
$validSalesman = Salesman::whereIn('region_id', $testUser->getRegionIds())
    ->whereIn('channel_id', $testUser->getChannelIds())
    ->where(function($q) use ($testUser) {
        $q->where('classification', $testUser->classification)
          ->orWhere('classification', 'both');
    })
    ->first();

$validSupplier = Supplier::where('classification', $testUser->classification)->first();
$validCategory = $validSupplier ? Category::where('supplier_id', $validSupplier->id)->first() : null;

if ($validSalesman && $validSupplier && $validCategory) {
    echo "✅ Valid target creation data found:\n";
    echo "   Salesman: {$validSalesman->name} (ID: {$validSalesman->id})\n";
    echo "   Supplier: {$validSupplier->name} (ID: {$validSupplier->id})\n";
    echo "   Category: {$validCategory->name} (ID: {$validCategory->id})\n";
    echo "   This user CAN create targets\n";
} else {
    echo "❌ Cannot find valid data for target creation:\n";
    echo "   Valid Salesman: " . ($validSalesman ? "✅" : "❌") . "\n";
    echo "   Valid Supplier: " . ($validSupplier ? "✅" : "❌") . "\n";
    echo "   Valid Category: " . ($validCategory ? "✅" : "❌") . "\n";
}

echo "\n=== DEBUG COMPLETE ===\n";