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
        Schema::create('heroes', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('');
            $table->unsignedInteger('title_width');
            $table->string('title_font')->default('');
            $table->string('title_v_align');
            $table->string('subtitle')->default('');
            $table->unsignedInteger('subtitle_width');
            $table->string('subtitle_font')->default('');
            $table->string('subtitle_v_align');
            $table->text('keynote')->default('');
            $table->unsignedInteger('keynote_width');
            $table->string('keynote_font')->default('');
            $table->string('keynote_v_align');
            $table->string('background_image')->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('heroes');
    }
};
