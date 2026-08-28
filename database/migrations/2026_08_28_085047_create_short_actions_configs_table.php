<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('short_actions_configs', function (Blueprint $table) {
            $table->id();
            $table->string('title_font')->nullable();
            $table->string('title_size')->nullable();
            $table->string('title_color')->nullable();
            $table->string('subtitle_font')->nullable();
            $table->string('subtitle_size')->nullable();
            $table->string('subtitle_color')->nullable();
            $table->string('bg_color')->nullable();
            $table->string('bg_color_hover')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('short_actions_configs');
    }
};
