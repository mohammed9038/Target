<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Region;
use App\Models\Channel;

class MultipleRegionsChannelsSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample regions if they don't exist
        $regions = [
            ['region_code' => 'R001', 'name' => 'North Region', 'is_active' => true],
            ['region_code' => 'R002', 'name' => 'South Region', 'is_active' => true],
            ['region_code' => 'R003', 'name' => 'East Region', 'is_active' => true],
            ['region_code' => 'R004', 'name' => 'West Region', 'is_active' => true],
        ];

        foreach ($regions as $regionData) {
            Region::firstOrCreate(
                ['region_code' => $regionData['region_code']],
                $regionData
            );
        }

        // Create sample channels if they don't exist
        $channels = [
            ['channel_code' => 'C001', 'name' => 'Retail', 'is_active' => true],
            ['channel_code' => 'C002', 'name' => 'Wholesale', 'is_active' => true],
            ['channel_code' => 'C003', 'name' => 'Online', 'is_active' => true],
        ];

        foreach ($channels as $channelData) {
            Channel::firstOrCreate(
                ['channel_code' => $channelData['channel_code']],
                $channelData
            );
        }

        // Create sample users if they don't exist
        $users = [
            [
                'username' => 'admin',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'classification' => 'both',
                'regions' => [], // Admin has access to all
                'channels' => [], // Admin has access to all
            ],
            [
                'username' => 'manager_food_north',
                'password' => bcrypt('password'),
                'role' => 'manager',
                'classification' => 'food',
                'regions' => ['North Region'],
                'channels' => ['Retail', 'Wholesale'],
            ],
            [
                'username' => 'manager_nonfood_multi',
                'password' => bcrypt('password'),
                'role' => 'manager',
                'classification' => 'non_food',
                'regions' => ['South Region', 'East Region'],
                'channels' => ['Retail', 'Online'],
            ],
            [
                'username' => 'manager_both_all',
                'password' => bcrypt('password'),
                'role' => 'manager',
                'classification' => 'both',
                'regions' => ['North Region', 'South Region', 'East Region', 'West Region'],
                'channels' => ['Retail', 'Wholesale', 'Online'],
            ],
        ];

        foreach ($users as $userData) {
            $regionNames = $userData['regions'];
            $channelNames = $userData['channels'];
            unset($userData['regions'], $userData['channels']);

            $user = User::firstOrCreate(
                ['username' => $userData['username']],
                $userData
            );

            // Attach regions
            if (!empty($regionNames)) {
                $regionIds = Region::whereIn('name', $regionNames)->pluck('id');
                $user->regions()->sync($regionIds);
            }

            // Attach channels
            if (!empty($channelNames)) {
                $channelIds = Channel::whereIn('name', $channelNames)->pluck('id');
                $user->channels()->sync($channelIds);
            }
        }

        $this->command->info('Sample users with multiple regions/channels created successfully!');
    }
}