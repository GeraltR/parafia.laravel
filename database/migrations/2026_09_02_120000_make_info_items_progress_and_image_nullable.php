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
            $table->string('image')->nullable()->change();
            $table->unsignedTinyInteger('progress_value')->nullable()->change();
            $table->string('progress_description')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('info_items', function (Blueprint $table) {
            $table->string('image')->nullable(false)->change();
            $table->unsignedTinyInteger('progress_value')->nullable(false)->default(0)->change();
            $table->string('progress_description')->nullable(false)->change();
        });
    }
};
