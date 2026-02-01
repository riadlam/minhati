<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * الحالة العائلية: متزوج، أرمل، مطلق
     */
    public function up(): void
    {
        Schema::table('tuteures', function (Blueprint $table) {
            if (!Schema::hasColumn('tuteures', 'situation_familiale')) {
                $table->string('situation_familiale', 20)->nullable()->after('sexe')->comment('الحالة العائلية: متزوج، أرمل، مطلق');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tuteures', function (Blueprint $table) {
            if (Schema::hasColumn('tuteures', 'situation_familiale')) {
                $table->dropColumn('situation_familiale');
            }
        });
    }
};
