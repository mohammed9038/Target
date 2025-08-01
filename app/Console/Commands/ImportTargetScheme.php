<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Channel;
use App\Models\Region;
use App\Models\Salesman;
use App\Models\Supplier;
use App\Models\SalesTarget;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportTargetScheme extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-target-scheme';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import target scheme from CSV file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting target scheme import...');

        // Read CSV file
        $csvFile = base_path('Target scheme.csv');
        if (!file_exists($csvFile)) {
            $this->error('Target scheme.csv file not found!');
            return 1;
        }

        // Read CSV using fgetcsv
        $handle = fopen($csvFile, 'r');
        if ($handle === false) {
            $this->error('Could not open CSV file');
            return 1;
        }

        // Get headers from first row
        $headers = fgetcsv($handle);
        if ($headers === false) {
            $this->error('Could not read CSV headers');
            fclose($handle);
            return 1;
        }

        // Clean up headers and remove BOM
        $headers = array_map(function($header) {
            $header = trim($header);
            $header = str_replace(' ', '', $header);
            $header = str_replace("\xEF\xBB\xBF", '', $header); // Remove BOM
            $header = str_replace("\r", '', $header); // Remove carriage returns
            $header = str_replace("\n", '', $header); // Remove line feeds
            return $header;
        }, $headers);

        // Read all rows into array
        $csv = [];
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) === count($headers)) {
                $csv[] = array_map('trim', $row);
            }
        }
        fclose($handle);

        // Debug headers
        $this->info('Headers: ' . implode(', ', $headers));

        $this->info('Found ' . count($csv) . ' rows to process');

        DB::beginTransaction();
        try {
            $created = 0;
            $skipped = 0;
            $errors = [];

            foreach ($csv as $index => $row) {
                $this->info("Processing row " . ($index + 1));

                // Map CSV columns to variables
                $data = array_combine($headers, $row);
                
                // Get or create region
                $regionName = trim($data['Region']);
                $regionCode = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $regionName), 0, 10));
                
                $region = Region::firstOrCreate(
                    ['name' => $regionName],
                    [
                        'region_code' => $regionCode,
                        'is_active' => true
                    ]
                );

                // Get or create channel
                $channelName = trim($data['Channel']);
                $channelCode = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $channelName), 0, 10));
                
                $channel = Channel::firstOrCreate(
                    ['name' => $channelName],
                    [
                        'channel_code' => $channelCode,
                        'is_active' => true
                    ]
                );

                // Get or create supplier
                $supplier = Supplier::firstOrCreate(
                    ['name' => trim($data['Supplier'])],
                    [
                        'supplier_code' => strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', trim($data['Supplier'])), 0, 10)),
                        'is_active' => true
                    ]
                );

                // Get or create category
                $categoryName = trim($data['Category']);
                $categoryCode = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $categoryName), 0, 10));
                
                // Try to find existing category
                $category = Category::where('name', $categoryName)
                    ->where('supplier_id', $supplier->id)
                    ->first();
                
                if (!$category) {
                    // Find a unique category code
                    $baseCode = $categoryCode;
                    $counter = 1;
                    while (Category::where('category_code', $categoryCode)
                        ->where('supplier_id', $supplier->id)
                        ->exists()) {
                        $categoryCode = $baseCode . $counter;
                        $counter++;
                    }
                    
                    // Create new category
                    $category = Category::create([
                        'name' => $categoryName,
                        'supplier_id' => $supplier->id,
                        'category_code' => $categoryCode,
                        'is_active' => true
                    ]);
                }

                // Get or create salesman
                $classification = strtolower(trim($data['Classification']));
                $classification = str_replace(' ', '_', $classification);
                
                $salesman = Salesman::firstOrCreate(
                    ['employee_code' => trim($data['EmployeeCode'])],
                    [
                        'name' => trim($data['SalesmenName']),
                        'region_id' => $region->id,
                        'channel_id' => $channel->id,
                        'classification' => $classification,
                        'is_active' => trim($data['Status']) === 'Active',
                    ]
                );

                // Convert month name to number
                $month = date('n', strtotime($data['Month'] . ' 1'));

                // Create or update target
                if (!empty(trim($data['Amount']))) {
                    SalesTarget::updateOrCreate(
                        [
                            'year' => (int)trim($data['Year']),
                            'month' => $month,
                            'salesman_id' => $salesman->id,
                            'supplier_id' => $supplier->id,
                            'category_id' => $category->id,
                            'region_id' => $region->id,
                            'channel_id' => $channel->id,
                        ],
                        [
                            'target_amount' => (float)trim($data['Amount']),
                        ]
                    );
                    $created++;
                } else {
                    $skipped++;
                }
            }

            DB::commit();
            $this->info("Import completed successfully!");
            $this->info("Created/Updated: $created");
            $this->info("Skipped (no amount): $skipped");
            
            if (count($errors) > 0) {
                $this->warn("Errors encountered:");
                foreach ($errors as $error) {
                    $this->warn("- $error");
                }
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error during import: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
