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
        Schema::create('info_extras', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->json('images')->nullable();
            $table->unsignedTinyInteger('progress_percent')->default(0);
            $table->string('bank_account');
            $table->string('donation_url');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('info_extras');
    }
};
