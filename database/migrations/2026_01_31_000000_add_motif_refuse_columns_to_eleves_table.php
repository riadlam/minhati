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
            if (!Schema::hasColumn('eleves', 'motif')) {
                $table->string('motif', 500)->nullable()->after('etat_das');
            }
            if (!Schema::hasColumn('eleves', 'cnas_refuse')) {
                $table->unsignedTinyInteger('cnas_refuse')->default(0)->after('motif');
            }
            if (!Schema::hasColumn('eleves', 'casnos_refuse')) {
                $table->unsignedTinyInteger('casnos_refuse')->default(0)->after('cnas_refuse');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eleves', function (Blueprint $table) {
            if (Schema::hasColumn('eleves', 'casnos_refuse')) {
                $table->dropColumn('casnos_refuse');
            }
            if (Schema::hasColumn('eleves', 'cnas_refuse')) {
                $table->dropColumn('cnas_refuse');
            }
            if (Schema::hasColumn('eleves', 'motif')) {
                $table->dropColumn('motif');
            }
        });
    }
};
