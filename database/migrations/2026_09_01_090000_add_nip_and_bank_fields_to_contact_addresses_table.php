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
        Schema::table('contact_addresses', function (Blueprint $table) {
            $table->string('nip')->nullable()->after('phone');
            $table->string('bank_account_number')->nullable()->after('nip');
            $table->string('bank_name')->nullable()->after('bank_account_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_addresses', function (Blueprint $table) {
            $table->dropColumn(['nip', 'bank_account_number', 'bank_name']);
        });
    }
};
