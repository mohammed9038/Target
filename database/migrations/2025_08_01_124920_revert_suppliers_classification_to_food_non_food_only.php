<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Remove 'both' classification from suppliers - should only be food or non_food
        Schema::table('suppliers', function (Blueprint $table) {
            $table->enum('classification_new', ['food', 'non_food'])->after('classification');
        });
        
        // Convert 'both' to 'food' as fallback, keep others as-is
        DB::statement("UPDATE suppliers SET classification_new = CASE WHEN classification = 'both' THEN 'food' ELSE classification END");
        
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('classification');
        });
        
        Schema::table('suppliers', function (Blueprint $table) {
            $table->renameColumn('classification_new', 'classification');
        });
    }

    public function down(): void
    {
        // Add back 'both' option if needed
        Schema::table('suppliers', function (Blueprint $table) {
            $table->enum('classification_new', ['food', 'non_food', 'both'])->after('classification');
        });
        
        DB::statement("UPDATE suppliers SET classification_new = classification");
        
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('classification');
        });
        
        Schema::table('suppliers', function (Blueprint $table) {
            $table->renameColumn('classification_new', 'classification');
        });
    }
};