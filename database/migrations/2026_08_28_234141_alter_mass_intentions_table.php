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
        Schema::table('mass_intentions', function (Blueprint $table) {
            $table->dropColumn('color');
            $table->boolean('is_holiday')->default(false)->after('intention');
            $table->string('day_description')->nullable()->after('is_holiday');
            $table->foreignId('author_id')->nullable()->after('day_description')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mass_intentions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('author_id');
            $table->dropColumn(['is_holiday', 'day_description']);
            $table->string('color')->nullable();
        });
    }
};
