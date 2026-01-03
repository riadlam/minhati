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
            if (!Schema::hasColumn('tuteures', 'mother_id')) {
                $table->unsignedBigInteger('mother_id')->nullable()->after('nin');
                $table->foreign('mother_id')->references('id')->on('mothers')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tuteures', function (Blueprint $table) {
            if (Schema::hasColumn('tuteures', 'mother_id')) {
                $table->dropForeign(['mother_id']);
                $table->dropColumn('mother_id');
            }
        });
    }
};
