<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SalesTarget;
use App\Models\ActiveMonthYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TargetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = SalesTarget::with([
            'region', 'channel', 'salesman', 'supplier', 'category'
        ]);

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

        if ($request->filled('employee_code')) {
            $query->byEmployeeCode($request->employee_code);
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

        $targets = $query->orderBy('year', 'desc')
                        ->orderBy('month', 'desc')
                        ->orderBy('region_id')
                        ->orderBy('channel_id')
                        ->paginate(50);

        return response()->json([
            'data' => $targets
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'year' => 'required|integer|min:2020|max:2030',
            'month' => 'required|integer|min:1|max:12',
            'region_id' => 'required|exists:regions,id',
            'channel_id' => 'required|exists:channels,id',
            'salesman_id' => 'required|exists:salesmen,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'category_id' => 'required|exists:categories,id',
            'target_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Check if period is open
        $period = ActiveMonthYear::where('year', $request->year)
                                ->where('month', $request->month)
                                ->first();

        if (!$period || !$period->is_open) {
            return response()->json([
                'message' => 'This period is not open for target entry'
            ], 422);
        }

        // Apply user scope for managers
        if ($user->isManager()) {
            if ($request->region_id != $user->region_id || $request->channel_id != $user->channel_id) {
                return response()->json([
                    'message' => 'You can only create targets within your assigned region and channel'
                ], 403);
            }
        }

        // Check uniqueness
        $existing = SalesTarget::where('year', $request->year)
                              ->where('month', $request->month)
                              ->where('salesman_id', $request->salesman_id)
                              ->where('supplier_id', $request->supplier_id)
                              ->where('category_id', $request->category_id)
                              ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Target already exists for this combination'
            ], 422);
        }

        $target = SalesTarget::create($request->all());

        return response()->json([
            'data' => $target->load(['region', 'channel', 'salesman', 'supplier', 'category']),
            'message' => 'Target created successfully'
        ], 201);
    }

    public function show(SalesTarget $target)
    {
        $user = Auth::user();
        
        // Check scope for managers
        if ($user->isManager()) {
            if ($target->region_id != $user->region_id || $target->channel_id != $user->channel_id) {
                return response()->json([
                    'message' => 'Access denied'
                ], 403);
            }
        }

        return response()->json([
            'data' => $target->load(['region', 'channel', 'salesman', 'supplier', 'category'])
        ]);
    }

    public function update(Request $request, SalesTarget $target)
    {
        $user = Auth::user();
        
        // Check scope for managers
        if ($user->isManager()) {
            if ($target->region_id != $user->region_id || $target->channel_id != $user->channel_id) {
                return response()->json([
                    'message' => 'Access denied'
                ], 403);
            }
        }

        $request->validate([
            'target_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $target->update($request->only(['target_amount', 'notes']));

        return response()->json([
            'data' => $target->load(['region', 'channel', 'salesman', 'supplier', 'category']),
            'message' => 'Target updated successfully'
        ]);
    }

    public function destroy(SalesTarget $target)
    {
        $user = Auth::user();
        
        // Check scope for managers
        if ($user->isManager()) {
            if ($target->region_id != $user->region_id || $target->channel_id != $user->channel_id) {
                return response()->json([
                    'message' => 'Access denied'
                ], 403);
            }
        }

        $target->delete();

        return response()->json([
            'message' => 'Target deleted successfully'
        ]);
    }

    public function bulkUpsert(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'targets' => 'required|array|min:1',
            'targets.*.year' => 'required|integer|min:2020|max:2030',
            'targets.*.month' => 'required|integer|min:1|max:12',
            'targets.*.region_id' => 'required|exists:regions,id',
            'targets.*.channel_id' => 'required|exists:channels,id',
            'targets.*.salesman_id' => 'required|exists:salesmen,id',
            'targets.*.supplier_id' => 'required|exists:suppliers,id',
            'targets.*.category_id' => 'required|exists:categories,id',
            'targets.*.target_amount' => 'required|numeric|min:0',
            'targets.*.notes' => 'nullable|string',
        ]);

        $created = 0;
        $updated = 0;
        $errors = [];

        foreach ($request->targets as $index => $targetData) {
            try {
                // Apply user scope for managers
                if ($user->isManager()) {
                    if ($targetData['region_id'] != $user->region_id || $targetData['channel_id'] != $user->channel_id) {
                        $errors[] = "Row " . ($index + 1) . ": Access denied for this region/channel";
                        continue;
                    }
                }

                // Check if period is open
                $period = ActiveMonthYear::where('year', $targetData['year'])
                                        ->where('month', $targetData['month'])
                                        ->first();

                if (!$period || !$period->is_open) {
                    $errors[] = "Row " . ($index + 1) . ": Period is not open";
                    continue;
                }

                $target = SalesTarget::updateOrCreate(
                    [
                        'year' => $targetData['year'],
                        'month' => $targetData['month'],
                        'salesman_id' => $targetData['salesman_id'],
                        'supplier_id' => $targetData['supplier_id'],
                        'category_id' => $targetData['category_id'],
                    ],
                    $targetData
                );

                if ($target->wasRecentlyCreated) {
                    $created++;
                } else {
                    $updated++;
                }
            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        return response()->json([
            'message' => "Bulk operation completed",
            'created' => $created,
            'updated' => $updated,
            'errors' => $errors
        ]);
    }

    public function getMatrix(Request $request)
    {
        $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:12',
            'region_id' => 'nullable|exists:regions,id',
            'channel_id' => 'nullable|exists:channels,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'category_id' => 'nullable|exists:categories,id',
            'salesman_id' => 'nullable|exists:salesmen,id',
            'classification' => 'nullable|in:food,non_food,both',
        ]);

        $user = Auth::user();

        // 1. Get Salesmen
        $salesmenQuery = \DB::table('salesmen')
            ->leftJoin('regions', 'salesmen.region_id', '=', 'regions.id')
            ->leftJoin('channels', 'salesmen.channel_id', '=', 'channels.id')
            ->select(
                'salesmen.id as salesman_id',
                'salesmen.salesman_code',
                'salesmen.name as salesman_name',
                'salesmen.classification as salesman_classification',
                'regions.name as region_name',
                'channels.name as channel_name'
            );

        // 2. Get Suppliers and Categories
        $suppliersQuery = \DB::table('suppliers')
            ->join('categories', 'suppliers.id', '=', 'categories.supplier_id')
            ->select(
                'suppliers.id as supplier_id',
                'suppliers.name as supplier_name',
                'suppliers.classification as supplier_classification',
                'categories.id as category_id',
                'categories.name as category_name'
            );

        // Apply filters
        if ($request->filled('region_id')) {
            $salesmenQuery->where('salesmen.region_id', $request->region_id);
        }
        if ($request->filled('supplier_id')) {
            $suppliersQuery->where('suppliers.id', $request->supplier_id);
        }

        // 3. Get existing targets
        $existingTargets = SalesTarget::where('year', $request->year)
            ->where('month', $request->month)
            ->get(['salesman_id', 'supplier_id', 'category_id', 'target_amount']);

        return response()->json([
            'data' => [
                'salesmen' => $salesmenQuery->get(),
                'suppliers' => $suppliersQuery->get(),
                'targets' => $existingTargets,
            ]
        ]);
    }

    public function bulkSave(Request $request)
    {
        $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:12',
            'targets' => 'required|array|min:1',
            'targets.*.salesman_id' => 'required|exists:salesmen,id',
            'targets.*.supplier_id' => 'required|exists:suppliers,id',
            'targets.*.category_id' => 'required|exists:categories,id',
            'targets.*.amount' => 'present|numeric|min:0',
        ]);

        $user = Auth::user();
        
        // Check if period is open
        $period = ActiveMonthYear::where('year', $request->year)
                                ->where('month', $request->month)
                                ->first();

        if (!$period || !$period->is_open) {
            return response()->json([
                'success' => false,
                'message' => 'This period is not open for target entry'
            ], 422);
        }

        $savedCount = 0;
        $errors = [];

        foreach ($request->targets as $targetData) {
            try {
                // Get salesman details for scope checking
                $salesman = \App\Models\Salesman::find($targetData['salesman_id']);
                
                // Apply user scope for managers
                if ($user->isManager()) {
                    if ($salesman->region_id != $user->region_id || $salesman->channel_id != $user->channel_id) {
                        continue; // Skip this target
                    }
                    
                    // Apply classification filter if specified
                    if ($user->classification && $user->classification !== 'both') {
                        if ($salesman->classification !== $user->classification) {
                            continue; // Skip this target
                        }
                    }
                }

                SalesTarget::updateOrCreate(
                    [
                        'year' => $request->year,
                        'month' => $request->month,
                        'salesman_id' => $targetData['salesman_id'],
                        'supplier_id' => $targetData['supplier_id'],
                        'category_id' => $targetData['category_id'],
                    ],
                    [
                        'region_id' => $salesman->region_id,
                        'channel_id' => $salesman->channel_id,
                        'target_amount' => $targetData['amount'],
                        'notes' => $targetData['notes'] ?? null,
                    ]
                );

                $savedCount++;
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        return response()->json([
            'success' => true,
            'saved_count' => $savedCount,
            'message' => "Successfully saved {$savedCount} targets",
            'errors' => $errors
        ]);
    }

    public function exportCsv(Request $request)
    {
        $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $user = Auth::user();
        $query = SalesTarget::with([
            'region', 'channel', 'salesman', 'supplier', 'category'
        ])->where('year', $request->year)
          ->where('month', $request->month);

        // Apply user scope
        if ($user->isManager()) {
            $query->where('region_id', $user->region_id)
                  ->where('channel_id', $user->channel_id);
                  
            // Apply classification filter if specified
            if ($user->classification && $user->classification !== 'both') {
                $query->whereHas('salesman', function($q) use ($user) {
                    $q->where('classification', $user->classification);
                });
            }
        }

        $targets = $query->get();

        $csvData = [];
        $csvData[] = [
            'Classification', 'Status', 'Year', 'Month', 'Region', 'Channel', 
            'Supplier', 'Category', 'RouteCode', 'Salesman Code', 'Employee Code', 'Salesmen Name', 'Amount'
        ];

        foreach ($targets as $target) {
            $csvData[] = [
                $target->salesman->classification ?? '',
                'Active',
                $target->year,
                date('M', mktime(0, 0, 0, $target->month, 1)),
                $target->region->name ?? '',
                $target->channel->name ?? '',
                $target->supplier->name ?? '',
                $target->category->name ?? '',
                '', // RouteCode - empty as in original CSV
                $target->salesman->salesman_code ?? '',
                $target->salesman->employee_code ?? '',
                $target->salesman->name ?? '',
                $target->target_amount
            ];
        }

        $filename = "targets_{$request->year}_{$request->month}.csv";
        
        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }

    public function upload(Request $request)
    {
        // Check if user has permission to upload
        if (!auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized. Only administrators can upload targets.'], 403);
        }

        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240', // 10MB max
            'year' => 'required|integer|min:2020|max:2030',
            'month' => 'required|integer|min:1|max:12',
        ]);

        try {
            $file = $request->file('file');
            $year = $request->input('year');
            $month = $request->input('month');
            
            $results = [
                'processed' => 0,
                'created' => 0,
                'updated' => 0,
                'errors' => 0,
                'error_details' => []
            ];

            // Read file content
            $content = file_get_contents($file->getPathname());
            $lines = explode("\n", $content);
            
            if (empty($lines)) {
                return response()->json(['message' => 'File is empty or could not be read.'], 400);
            }

            // Get headers from first line
            $headers = str_getcsv(array_shift($lines));
            $headers = array_map('trim', $headers);
            
            // Clean headers (remove BOM and normalize)
            $headers[0] = preg_replace('/^\x{FEFF}/', '', $headers[0]);
            $headers = array_map(function($header) {
                return preg_replace('/[^\w\s]/', '', strtolower(trim($header)));
            }, $headers);

            // Expected headers mapping
            $headerMap = [
                'salesman_code' => ['salesman code', 'salesmancode', 'sal code', 'salcode'],
                'employee_code' => ['employee code', 'employeecode', 'emp code', 'empcode'],
                'salesman_name' => ['salesman name', 'salesmanname', 'name', 'salesman'],
                'region' => ['region', 'region name', 'regionname'],
                'channel' => ['channel', 'channel name', 'channelname'],
                'supplier' => ['supplier', 'supplier name', 'suppliername'],
                'category' => ['category', 'category name', 'categoryname'],
                'amount' => ['amount', 'target amount', 'targetamount', 'target']
            ];

            // Find header positions
            $positions = [];
            foreach ($headerMap as $field => $variations) {
                foreach ($variations as $variation) {
                    $pos = array_search($variation, $headers);
                    if ($pos !== false) {
                        $positions[$field] = $pos;
                        break;
                    }
                }
            }

            // Check required headers
            $requiredFields = ['employee_code', 'amount'];
            foreach ($requiredFields as $field) {
                if (!isset($positions[$field])) {
                    return response()->json([
                        'message' => "Required column '{$field}' not found in file headers."
                    ], 400);
                }
            }

            // Process each data row
            foreach ($lines as $lineNumber => $line) {
                $line = trim($line);
                if (empty($line)) continue;

                $results['processed']++;
                $data = str_getcsv($line);
                
                try {
                    // Extract data based on positions
                    $salesmanCode = isset($positions['salesman_code']) ? trim($data[$positions['salesman_code']] ?? '') : '';
                    $employeeCode = isset($positions['employee_code']) ? trim($data[$positions['employee_code']] ?? '') : '';
                    $amount = isset($positions['amount']) ? trim($data[$positions['amount']] ?? '') : '';
                    
                    if (empty($salesmanCode) && empty($employeeCode)) {
                        throw new \Exception("Missing salesman code or employee code");
                    }
                    
                    if (empty($amount)) {
                        throw new \Exception("Missing amount");
                    }

                    // Convert amount to numeric
                    $amount = preg_replace('/[^\d.-]/', '', $amount);
                    $amount = floatval($amount);
                    
                    if ($amount <= 0) {
                        throw new \Exception("Invalid amount: {$amount}");
                    }

                    // Find salesman by salesman_code first, then by employee_code
                    $salesman = null;
                    if (!empty($salesmanCode)) {
                        $salesman = \App\Models\Salesman::where('salesman_code', $salesmanCode)->first();
                    }
                    if (!$salesman && !empty($employeeCode)) {
                        $salesman = \App\Models\Salesman::where('employee_code', $employeeCode)->first();
                    }
                    
                    if (!$salesman) {
                        $identifier = !empty($salesmanCode) ? "salesman code: {$salesmanCode}" : "employee code: {$employeeCode}";
                        throw new \Exception("Salesman not found for {$identifier}");
                    }

                    // Create or update target
                    $target = \App\Models\SalesTarget::updateOrCreate([
                        'salesman_id' => $salesman->id,
                        'year' => $year,
                        'month' => $month,
                        'region_id' => $salesman->region_id,
                        'channel_id' => $salesman->channel_id,
                    ], [
                        'target_amount' => $amount,
                    ]);

                    if ($target->wasRecentlyCreated) {
                        $results['created']++;
                    } else {
                        $results['updated']++;
                    }

                } catch (\Exception $e) {
                    $results['errors']++;
                    $results['error_details'][] = "Row " . ($lineNumber + 2) . ": " . $e->getMessage();
                }
            }

            return response()->json($results);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        // Check if user has permission to download template
        if (!auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized. Only administrators can download templates.'], 403);
        }

        try {
            // Get sample salesmen data from database
            $salesmen = \App\Models\Salesman::with(['region', 'channel'])
                ->take(10)
                ->get();
            
            if ($salesmen->isEmpty()) {
                // Fallback to static sample data if no salesmen in database
                $csvContent = [
                    'Salesman Code,Employee Code,Salesman Name,Region,Channel,Supplier,Category,Amount',
                    'SAL0001,EMP001,John Doe,North Region,Retail,AGUS,FLOUR,1000.00',
                    'SAL0002,EMP002,Jane Smith,South Region,Wholesale,KORHAN,DAIRY,1500.00',
                    'SAL0003,EMP003,Mike Johnson,East Region,Retail,FONTERRA,CHEESE,1200.00',
                    'SAL0004,EMP004,Sarah Wilson,West Region,Wholesale,HOBBY,SNACKS,800.00',
                    'SAL0005,EMP005,David Brown,Central Region,Retail,MAZRAA,BEVERAGES,2000.00'
                ];
            } else {
                // Create CSV with real data
                $csvContent = ['Salesman Code,Employee Code,Salesman Name,Region,Channel,Supplier,Category,Amount'];
                
                foreach ($salesmen as $salesman) {
                    $csvContent[] = sprintf(
                        '%s,%s,%s,%s,%s,%s,%s,%s',
                        $salesman->salesman_code ?? 'SAL' . str_pad($salesman->id, 4, '0', STR_PAD_LEFT),
                        $salesman->employee_code ?? '',
                        $salesman->name,
                        $salesman->region->name ?? 'Sample Region',
                        $salesman->channel->name ?? 'Sample Channel',
                        'SAMPLE_SUPPLIER',
                        'SAMPLE_CATEGORY',
                        '1000.00'
                    );
                }
            }
            
            $filename = 'targets_upload_template_' . date('Y-m-d') . '.csv';
            
            $callback = function() use ($csvContent) {
                $file = fopen('php://output', 'w');
                foreach ($csvContent as $line) {
                    fputcsv($file, str_getcsv($line));
                }
                fclose($file);
            };

            return response()->stream($callback, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename={$filename}",
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Template download failed: ' . $e->getMessage()
            ], 500);
        }
    }
} 