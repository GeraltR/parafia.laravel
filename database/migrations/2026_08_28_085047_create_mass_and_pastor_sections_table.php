<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mass_and_pastor_sections', function (Blueprint $table) {
            $table->id();
            $table->string('position_font')->nullable();
            $table->string('position_size')->nullable();
            $table->string('position_color')->nullable();
            $table->string('name_font')->nullable();
            $table->string('name_size')->nullable();
            $table->string('name_color')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mass_and_pastor_sections');
    }
};
