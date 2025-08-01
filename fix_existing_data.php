<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== FIXING EXISTING DATA ===\n";

echo "1. Updating existing targets with classification...\n";
$targets = \App\Models\SalesTarget::with('supplier')->get();
$updated = 0;

foreach ($targets as $target) {
    if ($target->supplier) {
        $target->classification = $target->supplier->classification;
        $target->save();
        $updated++;
    }
}

echo "Updated {$updated} targets with classification\n";

echo "\n2. Testing matrix query with auth...\n";

// Create test request
$request = new \Illuminate\Http\Request();
$request->merge(['year' => 2025, 'month' => 8]);

// Get admin user properly
$user = \App\Models\User::where('email', 'admin@example.com')->first();
if (!$user) {
    echo "Creating admin user...\n";
    $user = \App\Models\User::create([
        'username' => 'admin',
        'name' => 'Admin',
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
        'role' => 'admin'
    ]);
}

// Mock the controller constructor auth
$controller = new class extends \App\Http\Controllers\Api\V1\TargetController {
    public function __construct() {
        // Skip auth middleware for testing
    }
};

// Set the authenticated user in Laravel's auth system
app('auth')->setUser($user);

echo "Testing matrix with user: {$user->email} (role: {$user->role})\n";

try {
    $response = $controller->getMatrix($request);
    $data = json_decode($response->getContent(), true);
    
    echo "Matrix response status: " . $response->getStatusCode() . "\n";
    if (isset($data['data'])) {
        echo "Salesmen: " . count($data['data']['salesmen'] ?? []) . "\n";
        echo "Suppliers: " . count($data['data']['suppliers'] ?? []) . "\n";
        echo "Targets: " . count($data['data']['targets'] ?? []) . "\n";
        
        if (!empty($data['data']['targets'])) {
            echo "First target: " . json_encode($data['data']['targets'][0]) . "\n";
        }
    } else {
        echo "No data key in response\n";
        echo "Response: " . substr($response->getContent(), 0, 200) . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n3. Checking frontend path routes...\n";
$routes = \Route::getRoutes();
$matrixRoute = null;
foreach ($routes as $route) {
    if (str_contains($route->uri(), 'targets/matrix')) {
        echo "Found matrix route: " . $route->uri() . " -> " . $route->getActionName() . "\n";
        $matrixRoute = $route;
    }
}

echo "\n=== FIX COMPLETE ===\n";