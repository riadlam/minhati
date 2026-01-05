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
            // Drop nom_pere and prenom_pere columns
            if (Schema::hasColumn('eleves', 'nom_pere')) {
                $table->dropColumn('nom_pere');
            }
            if (Schema::hasColumn('eleves', 'prenom_pere')) {
                $table->dropColumn('prenom_pere');
            }
            
            // Drop nin_pere, nss_pere, and orphelin columns
            if (Schema::hasColumn('eleves', 'nin_pere')) {
                $table->dropColumn('nin_pere');
            }
            if (Schema::hasColumn('eleves', 'nss_pere')) {
                $table->dropColumn('nss_pere');
            }
            if (Schema::hasColumn('eleves', 'orphelin')) {
                $table->dropColumn('orphelin');
            }
            
            // Add father_id column if it doesn't exist
            if (!Schema::hasColumn('eleves', 'father_id')) {
                $table->unsignedBigInteger('father_id')->nullable()->after('mother_id');
                $table->foreign('father_id')->references('id')->on('fathers')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eleves', function (Blueprint $table) {
            // Drop foreign key and column
            if (Schema::hasColumn('eleves', 'father_id')) {
                $table->dropForeign(['father_id']);
                $table->dropColumn('father_id');
            }
            
            // Re-add nom_pere and prenom_pere columns
            if (!Schema::hasColumn('eleves', 'nom_pere')) {
                $table->string('nom_pere', 50)->nullable()->after('prenom');
            }
            if (!Schema::hasColumn('eleves', 'prenom_pere')) {
                $table->string('prenom_pere', 50)->nullable()->after('nom_pere');
            }
            
            // Re-add nin_pere, nss_pere, and orphelin columns
            if (!Schema::hasColumn('eleves', 'nin_pere')) {
                $table->string('nin_pere', 18)->nullable()->after('code_commune');
            }
            if (!Schema::hasColumn('eleves', 'nss_pere')) {
                $table->string('nss_pere', 12)->nullable()->after('nin_pere');
            }
            if (!Schema::hasColumn('eleves', 'orphelin')) {
                $table->string('orphelin', 1)->nullable()->default('0')->after('handicap');
            }
        });
    }
};
