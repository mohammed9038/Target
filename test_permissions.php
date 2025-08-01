<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use App\Models\Region;
use App\Models\Channel;
use App\Models\Salesman;
use App\Models\Supplier;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== USER PERMISSIONS TEST ===\n\n";

// Test different users
$users = [
    'admin' => User::where('username', 'admin')->first(),
    'manager (food)' => User::where('username', 'manager')->first(),
    'manager2 (non_food)' => User::where('username', 'manager2')->first(),
    'manager3 (both)' => User::where('username', 'manager3')->first(),
];

foreach ($users as $userType => $user) {
    echo "🔍 Testing user: {$userType}\n";
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
    
    // Test accessible regions
    $regions = Region::where('is_active', true);
    if (!$user->isAdmin()) {
        $userScope = $user->scope();
        if (!empty($userScope['region_ids'])) {
            $regions->whereIn('id', $userScope['region_ids']);
        }
    }
    echo "   Accessible Regions: " . $regions->count() . "\n";
    
    // Test accessible channels
    $channels = Channel::where('is_active', true);
    if (!$user->isAdmin()) {
        $userScope = $user->scope();
        if (!empty($userScope['channel_ids'])) {
            $channels->whereIn('id', $userScope['channel_ids']);
        }
    }
    echo "   Accessible Channels: " . $channels->count() . "\n";
    
    // Test accessible salesmen
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
            $salesmen->where('classification', $userScope['classification']);
        }
    }
    echo "   Accessible Salesmen: " . $salesmen->count() . "\n";
    
    // Test accessible suppliers
    $suppliers = Supplier::query();
    if (!$user->isAdmin()) {
        $userScope = $user->scope();
        if (isset($userScope['classification']) && $userScope['classification'] !== 'both') {
            $suppliers->where('classification', $userScope['classification']);
        }
    }
    echo "   Accessible Suppliers: " . $suppliers->count() . "\n";
    
    echo "\n";
}

echo "✅ Permission testing completed!\n\n";

echo "📋 Test Users Created:\n";
echo "   - admin/password (role: admin, access: everything)\n";
echo "   - manager/password (role: manager, classification: food, region: North, channel: Retail)\n";
echo "   - manager2/password (role: manager, classification: non_food, region: South, channel: Wholesale)\n";
echo "   - manager3/password (role: manager, classification: both, regions: North+South, channels: Retail+Wholesale)\n\n";

echo "🎯 How to test:\n";
echo "1. Login as 'manager' and visit /targets - should only see food classification data\n";
echo "2. Login as 'manager2' and visit /targets - should only see non_food classification data\n";
echo "3. Login as 'manager3' and visit /targets - should see both classifications but limited regions\n";
echo "4. Login as 'admin' and visit /targets - should see everything\n";