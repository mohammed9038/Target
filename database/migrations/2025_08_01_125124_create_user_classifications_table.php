<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_classifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('classification', ['food', 'non_food']);
            $table->timestamps();
            
            // Ensure each user-classification combination is unique
            $table->unique(['user_id', 'classification']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_classifications');
    }
};