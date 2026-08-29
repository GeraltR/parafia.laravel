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
        Schema::create('mass_intentions_configs', function (Blueprint $table) {
            $table->id();
            $table->string('holiday_described_color')->default('#7bdcb5');
            $table->string('holiday_plain_color')->default('#f78da7');
            $table->string('weekday_color')->default('#8ed1fc');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mass_intentions_configs');
    }
};
