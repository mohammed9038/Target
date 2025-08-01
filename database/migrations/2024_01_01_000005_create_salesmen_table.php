<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salesmen', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code')->unique();
            $table->string('salesman_code')->nullable();
            $table->string('name');
            $table->foreignId('region_id')->constrained();
            $table->foreignId('channel_id')->constrained();
            $table->enum('classification', ['food', 'non_food', 'both']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salesmen');
    }
}; 