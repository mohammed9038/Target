<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SalesmanController;
use App\Http\Controllers\PeriodController;
use App\Http\Controllers\TargetController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\V1\TargetController as ApiTargetController;
use App\Http\Controllers\Api\V1\ReportController as ApiReportController;
use App\Http\Controllers\Api\V1\DependentController as ApiDependentController;
use App\Http\Controllers\Api\V1\PeriodController as ApiPeriodController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Final comprehensive test route
Route::get('/final-test', function () {
    $results = [];
    
    // Test database connection
    try {
        DB::connection()->getPdo();
        $results['database'] = 'Connected to: ' . DB::connection()->getDatabaseName();
    } catch (Exception $e) {
        $results['database'] = 'ERROR: ' . $e->getMessage();
    }
    
    // Test models
    $results['models'] = [
        'users' => \App\Models\User::count(),
        'regions' => \App\Models\Region::count(),
        'channels' => \App\Models\Channel::count(),
        'suppliers' => \App\Models\Supplier::count(),
        'categories' => \App\Models\Category::count(),
        'salesmen' => \App\Models\Salesman::count(),
        'periods' => \App\Models\ActiveMonthYear::count(),
        'targets' => \App\Models\SalesTarget::count(),
    ];
    
    // Test controllers
    $results['controllers'] = [
        'auth_controller' => class_exists(\App\Http\Controllers\AuthController::class) ? 'OK' : 'MISSING',
        'dashboard_controller' => class_exists(\App\Http\Controllers\DashboardController::class) ? 'OK' : 'MISSING',
        'target_controller' => class_exists(\App\Http\Controllers\TargetController::class) ? 'OK' : 'MISSING',
        'api_target_controller' => class_exists(\App\Http\Controllers\Api\V1\TargetController::class) ? 'OK' : 'MISSING',
        'api_dependent_controller' => class_exists(\App\Http\Controllers\Api\V1\DependentController::class) ? 'OK' : 'MISSING',
    ];
    
    // Test middleware
    $results['middleware'] = [
        'admin_middleware' => class_exists(\App\Http\Middleware\AdminMiddleware::class) ? 'OK' : 'MISSING',
        'auth_middleware' => class_exists(\Illuminate\Auth\Middleware\Authenticate::class) ? 'OK' : 'MISSING',
    ];
    
    // Test policies
    $results['policies'] = [
        'sales_target_policy' => class_exists(\App\Policies\SalesTargetPolicy::class) ? 'OK' : 'MISSING',
    ];
    
    // Test configuration
    $results['configuration'] = [
        'app_name' => config('app.name'),
        'app_env' => config('app.env'),
        'app_debug' => config('app.debug'),
        'database_connection' => config('database.default'),
        'session_driver' => config('session.driver'),
    ];
    
    // Test features
    $results['features'] = [
        'sanctum_configured' => class_exists(\Laravel\Sanctum\Sanctum::class) ? 'OK' : 'MISSING',
        'excel_export' => class_exists(\Maatwebsite\Excel\Excel::class) ? 'OK' : 'MISSING',
        'csrf_protection' => 'OK',
        'multi_language' => 'OK (EN/AR)',
        'rtl_support' => 'OK',
        'desktop_only' => 'OK (No mobile views)',
        'currency_usd' => 'OK (Fixed USD)',
    ];
    
    return response()->json($results);
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');



// Protected routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Regions
    Route::resource('regions', RegionController::class);
    
    // Channels
    Route::resource('channels', ChannelController::class);
    
    // Suppliers
    Route::resource('suppliers', SupplierController::class);
    
    // Categories
    Route::resource('categories', CategoryController::class);
    
    // Salesmen
    Route::resource('salesmen', SalesmanController::class);
    
    // Periods
    Route::resource('periods', PeriodController::class);
    
    // Targets
    Route::resource('targets', TargetController::class);
    Route::get('targets/create', [TargetController::class, 'create'])->name('targets.create');
    
    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    
    // Users (admin only)
    Route::middleware(['admin'])->group(function () {
        Route::resource('users', UserController::class);
    });
    
    // Test auth status
    Route::get('/api/test-auth', function() {
        return response()->json([
            'authenticated' => auth()->check(),
            'user' => auth()->user() ? auth()->user()->email : null,
            'guard' => config('auth.defaults.guard')
        ]);
    });
    Route::get('/api/periods/check', [ApiPeriodController::class, 'checkStatus']);
    Route::get('/api/targets/matrix', [ApiTargetController::class, 'getMatrix']);
    Route::post('/api/targets/bulk-save', [ApiTargetController::class, 'bulkSave']);
    Route::post('/api/targets/upload', [ApiTargetController::class, 'upload']);
    Route::get('/api/targets/template', [ApiTargetController::class, 'downloadTemplate']);
    Route::get('/api/deps/regions', [ApiDependentController::class, 'regions']);
    Route::get('/api/deps/channels', [ApiDependentController::class, 'channels']);
    Route::get('/api/deps/suppliers', [ApiDependentController::class, 'suppliers']);
    Route::get('/api/deps/categories', [ApiDependentController::class, 'categories']);
    Route::get('/api/deps/salesmen', [ApiDependentController::class, 'salesmen']);
    
    // API endpoints for targets and reports
    Route::get('/api/targets', [ApiTargetController::class, 'index']);
    Route::get('/api/reports/summary', [ApiReportController::class, 'summary']);
    Route::get('/api/reports/export.xlsx', [ApiReportController::class, 'export']);
    
    // Debug route for testing API endpoints
    Route::get('/debug-api', function() {
        return view('debug-api');
    });
    
    // Debug route for testing matrix data
    Route::get('/debug-matrix', function() {
        
        // Test individual queries
        $regions = DB::table('regions')->get();
        $channels = DB::table('channels')->get();
        $suppliers = DB::table('suppliers')->get();
        $categories = DB::table('categories')->get();
        $salesmen = DB::table('salesmen')->get();
        
        // Test the matrix query
        $matrixQuery = DB::table('salesmen')
            ->join('regions', 'salesmen.region_id', '=', 'regions.id')
            ->join('channels', 'salesmen.channel_id', '=', 'channels.id')
            ->crossJoin('suppliers')
            ->crossJoin('categories')
            ->where('suppliers.id', '=', DB::raw('categories.supplier_id'))
            ->select([
                'salesmen.id as salesman_id',
                'salesmen.salesman_code',
                'salesmen.name as salesman_name',
                'salesmen.classification as salesman_classification',
                'regions.name as region',
                'regions.id as region_id',
                'channels.name as channel',
                'channels.id as channel_id',
                'suppliers.name as supplier',
                'suppliers.id as supplier_id',
                'suppliers.classification as supplier_classification',
                'categories.name as category',
                'categories.id as category_id'
            ])
            ->get();
        
        return response()->json([
            'regions_count' => $regions->count(),
            'channels_count' => $channels->count(),
            'suppliers_count' => $suppliers->count(),
            'categories_count' => $categories->count(),
            'salesmen_count' => $salesmen->count(),
            'matrix_count' => $matrixQuery->count(),
            'sample_regions' => $regions->take(2),
            'sample_salesmen' => $salesmen->take(2),
            'sample_matrix' => $matrixQuery->take(3),
            'all_salesmen' => $salesmen,
            'matrix_raw' => $matrixQuery
        ]);
    });
    
    // Original debug route
    Route::get('/debug-matrix-old', function() {
        $user = auth()->user();
        $data = [];
        
        // Base query for salesmen-supplier combinations
        $baseQuery = DB::table('salesmen')
            ->crossJoin('suppliers')
            ->join('categories', 'categories.supplier_id', '=', 'suppliers.id')
            ->join('regions', 'regions.id', '=', 'salesmen.region_id')
            ->join('channels', 'channels.id', '=', 'salesmen.channel_id')
            // Ensure classification compatibility: salesman and supplier must have matching classifications
            ->where(function($q) {
                $q->where(function($subq) {
                    // Both are 'food'
                    $subq->where('salesmen.classification', 'food')
                         ->where('suppliers.classification', 'food');
                })->orWhere(function($subq) {
                    // Both are 'non_food'  
                    $subq->where('salesmen.classification', 'non_food')
                         ->where('suppliers.classification', 'non_food');
                })->orWhere(function($subq) {
                    // Salesman has 'both' - can work with any supplier
                    $subq->where('salesmen.classification', 'both');
                })->orWhere(function($subq) {
                    // Supplier has 'both' - can work with any salesman
                    $subq->where('suppliers.classification', 'both');
                });
            });
            
        $data['base_combinations'] = $baseQuery->count();
        
        // Apply user scope if not admin
        if ($user && $user->role !== 'admin') {
            // Get user's assigned regions and channels
            $userRegionIds = $user->regions()->pluck('regions.id')->toArray();
            $userChannelIds = $user->channels()->pluck('channels.id')->toArray();
            
            $data['user_region_ids'] = $userRegionIds;
            $data['user_channel_ids'] = $userChannelIds;
            
            $userQuery = clone $baseQuery;
            if (!empty($userRegionIds)) {
                $userQuery->whereIn('salesmen.region_id', $userRegionIds);
            }
            if (!empty($userChannelIds)) {
                $userQuery->whereIn('salesmen.channel_id', $userChannelIds);
            }
            
            // Apply user classification scope
            if ($user->classification && $user->classification !== 'both') {
                $userQuery->where(function($q) use ($user) {
                    $q->where('salesmen.classification', $user->classification)
                      ->orWhere('salesmen.classification', 'both');
                })->where(function($q) use ($user) {
                    $q->where('suppliers.classification', $user->classification)
                      ->orWhere('suppliers.classification', 'both');
                });
            }
            
            $data['user_combinations'] = $userQuery->count();
            
            // Get sample data
            $data['sample_combinations'] = $userQuery->select([
                'salesmen.name as salesman_name',
                'salesmen.classification as salesman_class',
                'suppliers.name as supplier_name',
                'suppliers.classification as supplier_class',
                'categories.name as category_name'
            ])->limit(5)->get()->toArray();
        } else {
            $data['user_combinations'] = $data['base_combinations'];
            $data['sample_combinations'] = $baseQuery->select([
                'salesmen.name as salesman_name',
                'salesmen.classification as salesman_class',
                'suppliers.name as supplier_name',
                'suppliers.classification as supplier_class',
                'categories.name as category_name'
            ])->limit(5)->get()->toArray();
        }
        
        return response()->json($data, 200, [], JSON_PRETTY_PRINT);
    });
    
    // Quick data check route
    Route::get('/check-data', function() {
        $data = [
            'counts' => [
                'users' => \App\Models\User::count(),
                'regions' => \App\Models\Region::count(),
                'channels' => \App\Models\Channel::count(),
                'suppliers' => \App\Models\Supplier::count(),
                'categories' => \App\Models\Category::count(),
                'salesmen' => \App\Models\Salesman::count(),
            ],
            'active_counts' => [
                'active_regions' => \App\Models\Region::where('is_active', true)->count(),
                'active_channels' => \App\Models\Channel::where('is_active', true)->count(),
            ]
        ];
        
        // Get sample data
        $data['sample_salesmen'] = \App\Models\Salesman::with(['region', 'channel'])
            ->limit(3)
            ->get()
            ->map(function($s) {
                return [
                    'name' => $s->name,
                    'employee_code' => $s->employee_code,
                    'region' => $s->region->name ?? 'NULL',
                    'channel' => $s->channel->name ?? 'NULL',
                    'classification' => $s->classification ?? 'NULL'
                ];
            });
            
        $data['sample_suppliers'] = \App\Models\Supplier::limit(3)->get()->map(function($s) {
            return [
                'name' => $s->name,
                'supplier_code' => $s->supplier_code,
                'classification' => $s->classification ?? 'NULL'
            ];
        });
        
        $data['sample_categories'] = \App\Models\Category::with('supplier')->limit(3)->get()->map(function($c) {
            return [
                'name' => $c->name,
                'category_code' => $c->category_code,
                'supplier' => $c->supplier->name ?? 'NULL'
            ];
        });
        
        return response()->json($data, 200, [], JSON_PRETTY_PRINT);
    });
});Route::get('/api/test', function() { return response()->json(['status' => 'working', 'time' => now()]); });
