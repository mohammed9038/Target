<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Region;
use App\Models\Channel;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\Salesman;
use App\Models\ActiveMonthYear;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create regions first
        $region1 = Region::create([
            'region_code' => 'R001',
            'name' => 'North Region',
            'is_active' => true,
        ]);

        $region2 = Region::create([
            'region_code' => 'R002',
            'name' => 'South Region',
            'is_active' => true,
        ]);

        // Create channels
        $channel1 = Channel::create([
            'channel_code' => 'C001',
            'name' => 'Direct Sales',
            'is_active' => true,
        ]);

        $channel2 = Channel::create([
            'channel_code' => 'C002',
            'name' => 'Retail',
            'is_active' => true,
        ]);

        // Create suppliers
        $supplier1 = Supplier::create([
            'supplier_code' => 'S001',
            'name' => 'Food Supplier A',
            'classification' => 'food',
        ]);

        $supplier2 = Supplier::create([
            'supplier_code' => 'S002',
            'name' => 'Non-Food Supplier B',
            'classification' => 'non_food',
        ]);

        // Create categories
        Category::create([
            'category_code' => 'CAT001',
            'name' => 'Beverages',
            'supplier_id' => $supplier1->id,
        ]);

        Category::create([
            'category_code' => 'CAT002',
            'name' => 'Snacks',
            'supplier_id' => $supplier1->id,
        ]);

        Category::create([
            'category_code' => 'CAT003',
            'name' => 'Electronics',
            'supplier_id' => $supplier2->id,
        ]);

        // Create salesmen
        Salesman::create([
            'employee_code' => 'EMP001',
            'salesman_code' => 'SAL001',
            'name' => 'John Doe',
            'region_id' => $region1->id,
            'channel_id' => $channel1->id,
            'classification' => 'both',
        ]);

        Salesman::create([
            'employee_code' => 'EMP002',
            'salesman_code' => 'SAL002',
            'name' => 'Jane Smith',
            'region_id' => $region2->id,
            'channel_id' => $channel2->id,
            'classification' => 'food',
        ]);

        // Create active periods
        ActiveMonthYear::create([
            'year' => date('Y'),
            'month' => date('n'),
            'is_open' => true,
        ]);

        ActiveMonthYear::create([
            'year' => date('Y'),
            'month' => date('n') + 1,
            'is_open' => false,
        ]);

        // Create admin user
        $admin = User::create([
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create manager user
        $manager = User::create([
            'username' => 'manager',
            'password' => Hash::make('password'),
            'role' => 'manager',
        ]);

        // Associate manager with regions and channels using pivot tables
        $manager->regions()->attach($region1->id);
        $manager->channels()->attach($channel1->id);
    }
} 