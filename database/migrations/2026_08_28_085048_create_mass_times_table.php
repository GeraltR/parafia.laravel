<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mass_times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mass_and_pastor_section_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->string('hours');
            $table->string('note')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mass_times');
    }
};
