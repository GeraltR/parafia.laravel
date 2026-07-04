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
        Schema::create('footer_configs', function (Blueprint $table) {
            $table->id();
            $table->string('office_note')->default('');
            $table->string('map_embed_url')->default('');
            $table->string('map_link')->default('');
            $table->string('copyright_text')->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('footer_configs');
    }
};
