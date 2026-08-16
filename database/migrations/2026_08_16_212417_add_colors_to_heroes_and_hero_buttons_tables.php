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
        Schema::table('heroes', function (Blueprint $table) {
            $table->string('title_color')->nullable()->after('title_v_align');
            $table->string('subtitle_color')->nullable()->after('subtitle_v_align');
        });

        Schema::table('hero_buttons', function (Blueprint $table) {
            $table->string('text_color')->nullable()->after('icon');
            $table->string('text_color_hover')->nullable()->after('text_color');
            $table->string('bg_color')->nullable()->after('text_color_hover');
            $table->string('bg_color_hover')->nullable()->after('bg_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('heroes', function (Blueprint $table) {
            $table->dropColumn(['title_color', 'subtitle_color']);
        });

        Schema::table('hero_buttons', function (Blueprint $table) {
            $table->dropColumn(['text_color', 'text_color_hover', 'bg_color', 'bg_color_hover']);
        });
    }
};
