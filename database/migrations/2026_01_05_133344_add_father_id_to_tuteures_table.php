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
            if (!Schema::hasColumn('tuteures', 'father_id')) {
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
        Schema::table('tuteures', function (Blueprint $table) {
            if (Schema::hasColumn('tuteures', 'father_id')) {
                $table->dropForeign(['father_id']);
                $table->dropColumn('father_id');
            }
        });
    }
};
