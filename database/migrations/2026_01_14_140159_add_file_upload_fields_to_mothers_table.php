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
            $table->string('biometric_id', 255)->nullable()->after('date_insertion');
            $table->string('biometric_id_back', 255)->nullable()->after('biometric_id');
            $table->string('Certificate_of_none_income', 255)->nullable()->after('biometric_id_back');
            $table->string('salary_certificate', 255)->nullable()->after('Certificate_of_none_income');
            $table->string('Certificate_of_non_affiliation_to_social_security', 255)->nullable()->after('salary_certificate');
            $table->string('crossed_ccp', 255)->nullable()->after('Certificate_of_non_affiliation_to_social_security');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mothers', function (Blueprint $table) {
            $table->dropColumn([
                'biometric_id',
                'biometric_id_back',
                'Certificate_of_none_income',
                'salary_certificate',
                'Certificate_of_non_affiliation_to_social_security',
                'crossed_ccp'
            ]);
        });
    }
};
