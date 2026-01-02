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
            if (!Schema::hasColumn('eleves', 'handicap_nature')) {
                $table->string('handicap_nature', 150)->nullable()->after('handicap');
            }
            if (!Schema::hasColumn('eleves', 'handicap_percentage')) {
                $table->decimal('handicap_percentage', 5, 2)->nullable()->after('handicap_nature');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eleves', function (Blueprint $table) {
            if (Schema::hasColumn('eleves', 'handicap_percentage')) {
                $table->dropColumn('handicap_percentage');
            }
            if (Schema::hasColumn('eleves', 'handicap_nature')) {
                $table->dropColumn('handicap_nature');
            }
        });
    }
};
