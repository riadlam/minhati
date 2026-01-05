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
        Schema::table('tuteures', function (Blueprint $table) {
            if (!Schema::hasColumn('tuteures', 'relation_tuteur')) {
                $table->tinyInteger('relation_tuteur')->nullable()->after('mother_id')->comment('دور الشخص: 1=أب, 2=أم, 3=وصي');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tuteures', function (Blueprint $table) {
            if (Schema::hasColumn('tuteures', 'relation_tuteur')) {
                $table->dropColumn('relation_tuteur');
            }
        });
    }
};
