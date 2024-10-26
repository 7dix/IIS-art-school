<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('equipments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('type_id')->constrained('types')->onDelete('cascade');
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->year('year_of_manufacture');
            $table->timestamp('date_of_purchase');
             // Maximální doba pronájmu ve dnech
            $table->integer('maximum_leasing_period');
            // Hodiny ve kterých je možné zařízení pronajmout
            // example: [8, 9, 10, 11, 12, 13, 14, 15, 16, 17]
            $table->json('allowed_leasing_hours'); 
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipments');
    }
};
