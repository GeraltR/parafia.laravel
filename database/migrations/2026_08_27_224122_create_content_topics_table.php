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
        Schema::create('content_topics', function (Blueprint $table) {
            $table->id();
            $table->string('page');
            $table->string('icon_url')->nullable();
            $table->string('title');
            $table->longText('content')->default('');
            $table->timestamp('visible_from')->nullable();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->index(['page', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_topics');
    }
};
