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
        Schema::table('footer_configs', function (Blueprint $table) {
            $table->string('office_title')->default('Godziny otwarcia kancelarii');
            $table->string('bg_color')->nullable();
            $table->string('title_font')->nullable();
            $table->string('title_size')->nullable();
            $table->string('title_color')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('footer_configs', function (Blueprint $table) {
            $table->dropColumn(['office_title', 'bg_color', 'title_font', 'title_size', 'title_color']);
        });
    }
};
