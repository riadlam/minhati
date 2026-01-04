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
        Schema::table('mothers', function (Blueprint $table) {
            if (!Schema::hasColumn('mothers', 'nom_fr')) {
                $table->string('nom_fr', 50)->nullable()->after('prenom_ar')->comment('لقب الأم بالفرنسية');
            }
            if (!Schema::hasColumn('mothers', 'prenom_fr')) {
                $table->string('prenom_fr', 50)->nullable()->after('nom_fr')->comment('اسم الأم بالفرنسية');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mothers', function (Blueprint $table) {
            if (Schema::hasColumn('mothers', 'nom_fr')) {
                $table->dropColumn('nom_fr');
            }
            if (Schema::hasColumn('mothers', 'prenom_fr')) {
                $table->dropColumn('prenom_fr');
            }
        });
    }
};
