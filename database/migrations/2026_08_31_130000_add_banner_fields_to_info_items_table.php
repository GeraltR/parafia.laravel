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
        Schema::table('info_items', function (Blueprint $table) {
            $table->text('banner_text')->nullable()->after('information');
            $table->string('banner_font')->nullable()->after('banner_text');
            $table->string('banner_text_color')->nullable()->after('banner_font');
            $table->string('banner_bg_color')->nullable()->after('banner_text_color');
            $table->unsignedSmallInteger('banner_duration_seconds')->default(0)->after('banner_bg_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('info_items', function (Blueprint $table) {
            $table->dropColumn([
                'banner_text',
                'banner_font',
                'banner_text_color',
                'banner_bg_color',
                'banner_duration_seconds',
            ]);
        });
    }
};
