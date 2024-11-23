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
            $table->foreignId('atelier_id')->constrained('ateliers')->onDelete('cascade');
            $table->year('year_of_manufacture')->nullable();;
            $table->date('date_of_purchase')->nullable();;
            $table->integer('maximum_leasing_period');
            $table->json('allowed_leasing_hours')->nullable(); 
            $table->string('image')->nullable();
            $table->timestamps();
            $table->boolean('can_be_borrowed')->default(true);
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
