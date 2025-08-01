<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUGGING ISSUES ===\n";

echo "1. Checking sales_targets table structure:\n";
$columns = \DB::select('PRAGMA table_info(sales_targets)');
foreach($columns as $col) {
    echo "  - {$col->name} ({$col->type})\n";
}

echo "\n2. Checking current targets count:\n";
$count = \App\Models\SalesTarget::count();
echo "Total targets: {$count}\n";

if ($count > 0) {
    echo "\n3. Sample target:\n";
    $target = \App\Models\SalesTarget::with(['salesman', 'supplier', 'category', 'region', 'channel'])->first();
    echo "ID: {$target->id}\n";
    echo "Year: {$target->year}, Month: {$target->month}\n";
    echo "Salesman: " . ($target->salesman->name ?? 'NULL') . "\n";
    echo "Supplier: " . ($target->supplier->name ?? 'NULL') . "\n";
    echo "Category: " . ($target->category->name ?? 'NULL') . "\n";
    echo "Amount: {$target->target_amount}\n";
}

echo "\n4. Testing matrix query:\n";
$request = new \Illuminate\Http\Request();
$request->merge(['year' => 2025, 'month' => 8]);

// Set auth user
$user = \App\Models\User::where('email', 'admin@example.com')->first();
\Auth::setUser($user);

$controller = new \App\Http\Controllers\Api\V1\TargetController();
$response = $controller->getMatrix($request);
$data = json_decode($response->getContent(), true);

echo "Matrix response keys: " . implode(', ', array_keys($data['data'] ?? [])) . "\n";
echo "Salesmen count: " . count($data['data']['salesmen'] ?? []) . "\n";
echo "Suppliers count: " . count($data['data']['suppliers'] ?? []) . "\n";
echo "Targets count: " . count($data['data']['targets'] ?? []) . "\n";

echo "\n=== DEBUG COMPLETE ===\n";