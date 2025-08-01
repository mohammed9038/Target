<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Migrate existing single region/channel assignments to many-to-many tables
        
        // Migrate regions
        $users = DB::table('users')->whereNotNull('region_id')->get();
        foreach ($users as $user) {
            DB::table('user_regions')->insert([
                'user_id' => $user->id,
                'region_id' => $user->region_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // Migrate channels
        $users = DB::table('users')->whereNotNull('channel_id')->get();
        foreach ($users as $user) {
            DB::table('user_channels')->insert([
                'user_id' => $user->id,
                'channel_id' => $user->channel_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // Remove old columns
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['region_id']);
            $table->dropForeign(['channel_id']);
            $table->dropColumn(['region_id', 'channel_id']);
        });
    }

    public function down(): void
    {
        // Add back the old columns
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('region_id')->nullable()->constrained();
            $table->foreignId('channel_id')->nullable()->constrained();
        });
        
        // Migrate back (take the first region/channel for each user)
        $userRegions = DB::table('user_regions')
            ->select('user_id', DB::raw('MIN(region_id) as region_id'))
            ->groupBy('user_id')
            ->get();
            
        foreach ($userRegions as $ur) {
            DB::table('users')
                ->where('id', $ur->user_id)
                ->update(['region_id' => $ur->region_id]);
        }
        
        $userChannels = DB::table('user_channels')
            ->select('user_id', DB::raw('MIN(channel_id) as channel_id'))
            ->groupBy('user_id')
            ->get();
            
        foreach ($userChannels as $uc) {
            DB::table('users')
                ->where('id', $uc->user_id)
                ->update(['channel_id' => $uc->channel_id]);
        }
    }
};