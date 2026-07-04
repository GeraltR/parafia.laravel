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
        Schema::create('hero_buttons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hero_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->string('href');
            $table->string('icon');
            $table->boolean('external')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hero_buttons');
    }
};
