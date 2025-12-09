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
        Schema::table('borrows', function (Blueprint $table) {
            $table->decimal('fine_amount', 10, 2)->default(0)->after('status');
            $table->timestamp('fine_calculated_at')->nullable()->after('fine_amount');
            $table->timestamp('fine_paid_at')->nullable()->after('fine_calculated_at');
            $table->text('fine_notes')->nullable()->after('fine_paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrows', function (Blueprint $table) {
            $table->dropColumn(['fine_amount', 'fine_calculated_at', 'fine_paid_at', 'fine_notes']);
        });
    }
};
