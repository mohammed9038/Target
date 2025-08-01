<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salesman_classifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salesman_id')->constrained('salesmen')->onDelete('cascade');
            $table->enum('classification', ['food', 'non_food']);
            $table->timestamps();
            
            // Ensure each salesman-classification combination is unique
            $table->unique(['salesman_id', 'classification']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salesman_classifications');
    }
};