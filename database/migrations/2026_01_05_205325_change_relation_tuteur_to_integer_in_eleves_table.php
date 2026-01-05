<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('eleves', function (Blueprint $table) {
            // First, convert existing data from text to integer
            // "ولي" -> 1 (for Father or Mother), "وصي" -> 3 (for Guardian)
            DB::statement("UPDATE eleves SET relation_tuteur = CASE 
                WHEN relation_tuteur = 'ولي' THEN '1'
                WHEN relation_tuteur = 'وصي' THEN '3'
                ELSE NULL
            END WHERE relation_tuteur IS NOT NULL");
            
            // Change column type from string to tinyInteger
            $table->tinyInteger('relation_tuteur')->nullable()->change()->comment('صفة طالب المنحة: 1=ولي (أب/أم), 3=وصي');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eleves', function (Blueprint $table) {
            // Convert back from integer to text
            DB::statement("UPDATE eleves SET relation_tuteur = CASE 
                WHEN relation_tuteur = '1' THEN 'ولي'
                WHEN relation_tuteur = '3' THEN 'وصي'
                ELSE NULL
            END WHERE relation_tuteur IS NOT NULL");
            
            // Change column type back to string
            $table->string('relation_tuteur', 10)->nullable()->change();
        });
    }
};
