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
        Schema::dropIfExists('info_extras');

        Schema::create('info_items', function (Blueprint $table) {
            $table->id();
            $table->date('valid_from');
            $table->date('valid_to');
            $table->string('title');
            $table->text('short_info');
            $table->longText('description');
            $table->string('image');
            $table->unsignedTinyInteger('progress_value')->default(0);
            $table->string('progress_description');
            $table->longText('information')->nullable();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('info_items');
    }
};
