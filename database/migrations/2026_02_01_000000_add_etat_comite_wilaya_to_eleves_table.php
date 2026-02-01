<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eleves', function (Blueprint $table) {
            if (!Schema::hasColumn('eleves', 'etat_comite_wilaya')) {
                $table->string('etat_comite_wilaya', 20)->nullable()->after('etat_das');
            }
        });
    }

    public function down(): void
    {
        Schema::table('eleves', function (Blueprint $table) {
            if (Schema::hasColumn('eleves', 'etat_comite_wilaya')) {
                $table->dropColumn('etat_comite_wilaya');
            }
        });
    }
};
