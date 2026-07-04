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
        Schema::create('contact_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('footer_config_id')->constrained()->cascadeOnDelete();
            $table->string('street');
            $table->string('city');
            $table->string('post_code');
            $table->string('phone');
            $table->boolean('social')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_addresses');
    }
};
