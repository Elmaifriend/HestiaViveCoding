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
        Schema::create('gate_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resident_id')->constrained('users')->cascadeOnDelete();
            $table->string('guest_name')->nullable();
            $table->dateTime('entry_date')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gate_entries');
    }
};
