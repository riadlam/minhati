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
            // Remove mother-related columns since we now use the mothers table relationship
            if (Schema::hasColumn('eleves', 'nom_mere')) {
                $table->dropColumn('nom_mere');
            }
            if (Schema::hasColumn('eleves', 'prenom_mere')) {
                $table->dropColumn('prenom_mere');
            }
            if (Schema::hasColumn('eleves', 'nin_mere')) {
                $table->dropColumn('nin_mere');
            }
            if (Schema::hasColumn('eleves', 'nss_mere')) {
                $table->dropColumn('nss_mere');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eleves', function (Blueprint $table) {
            // Re-add the columns if needed for rollback
            if (!Schema::hasColumn('eleves', 'nom_mere')) {
                $table->string('nom_mere', 50)->nullable()->after('prenom_pere');
            }
            if (!Schema::hasColumn('eleves', 'prenom_mere')) {
                $table->string('prenom_mere', 50)->nullable()->after('nom_mere');
            }
            if (!Schema::hasColumn('eleves', 'nin_mere')) {
                $table->string('nin_mere', 18)->nullable()->after('nin_pere');
            }
            if (!Schema::hasColumn('eleves', 'nss_mere')) {
                $table->string('nss_mere', 12)->nullable()->after('nss_pere');
            }
        });
    }
};
