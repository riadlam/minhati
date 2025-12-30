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
        Schema::table('eleves', function (Blueprint $table) {
            // Add approved_by column to track which user approved the student
            // It references users.code_user (18 characters)
            if (!Schema::hasColumn('eleves', 'approved_by')) {
                $table->string('approved_by', 18)->nullable()->after('dossier_depose');
                $table->foreign('approved_by')->references('code_user')->on('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eleves', function (Blueprint $table) {
            if (Schema::hasColumn('eleves', 'approved_by')) {
                $table->dropForeign(['approved_by']);
                $table->dropColumn('approved_by');
            }
        });
    }
};
