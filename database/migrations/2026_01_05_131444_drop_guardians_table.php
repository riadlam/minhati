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
        Schema::dropIfExists('guardians');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate guardians table if needed (using the original migration structure)
        if (!Schema::hasTable('guardians')) {
            Schema::create('guardians', function (Blueprint $table) {
                $table->id();
                $table->string('nin', 18)->unique()->comment('الرقم الوطني للوصي');
                $table->string('nss', 12)->nullable()->comment('رقم الضمان الاجتماعي للوصي');
                $table->string('nom_ar', 50)->comment('لقب الوصي بالعربية');
                $table->string('prenom_ar', 50)->comment('اسم الوصي بالعربية');
                $table->string('nom_fr', 50)->nullable()->comment('لقب الوصي بالفرنسية');
                $table->string('prenom_fr', 50)->nullable()->comment('اسم الوصي بالفرنسية');
                $table->string('categorie_sociale', 80)->nullable()->comment('الفئة الاجتماعية');
                $table->decimal('montant_s', 10, 2)->nullable()->comment('مبلغ الدخل الشهري');
                $table->string('tuteur_nin', 18)->comment('الرقم الوطني للولي الذي عين الوصي');
                $table->date('date_insertion')->nullable();
                $table->timestamps();
                $table->index('tuteur_nin');
            });
        }
    }
};
