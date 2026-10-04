<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watch_specs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('watch_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('movement')->nullable();
            $table->string('case_material')->nullable();
            $table->string('case_diameter')->nullable();
            $table->string('case_thickness')->nullable();
            $table->string('dial_color')->nullable();
            $table->string('crystal')->nullable();
            $table->string('water_resistance')->nullable();
            $table->string('power_reserve')->nullable();
            $table->string('bracelet_material')->nullable();
            $table->string('weight')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watch_specs');
    }
};
