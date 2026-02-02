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
        Schema::table('tax_returns', function (Blueprint $table) {
            $table->foreignId('accountant_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable()->after('submitted_at');
            $table->timestamp('reviewed_at')->nullable()->after('assigned_at');
            $table->text('accountant_notes')->nullable()->after('reviewed_at');

            // Add indexes for better query performance
            $table->index('status');
            $table->index('tax_year');
            $table->index(['accountant_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tax_returns', function (Blueprint $table) {
            $table->dropForeign(['accountant_id']);
            $table->dropIndex(['accountant_id', 'status']);
            $table->dropIndex(['tax_year']);
            $table->dropIndex(['status']);
            $table->dropColumn(['accountant_id', 'assigned_at', 'reviewed_at', 'accountant_notes']);
        });
    }
};
