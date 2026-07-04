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
        Schema::create('office_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('footer_config_id')->constrained()->cascadeOnDelete();
            $table->string('day');
            $table->time('hours_on');
            $table->time('hours_end');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('office_hours');
    }
};
