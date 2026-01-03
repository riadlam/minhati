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
        if (!Schema::hasTable('mothers')) {
            Schema::create('mothers', function (Blueprint $table) {
                $table->id();
                $table->string('nin', 18)->unique()->comment('الرقم الوطني للأم');
                $table->string('nss', 12)->nullable()->comment('الرقم الوطني للضمان الاجتماعي للأم');
                $table->string('nom_ar', 50)->comment('لقب الأم بالعربية');
                $table->string('prenom_ar', 50)->comment('اسم الأم بالعربية');
                $table->string('categorie_sociale', 80)->nullable()->comment('الفئة الاجتماعية');
                $table->decimal('montant_s', 10, 2)->nullable()->comment('مبلغ الدخل الشهري');
                $table->string('tuteur_nin', 18)->comment('الرقم الوطني للولي');
                $table->date('date_insertion')->nullable();
                $table->timestamps();
                
                // Foreign key to tuteurs
                $table->foreign('tuteur_nin')->references('nin')->on('tuteures')->onDelete('cascade');
                $table->index('tuteur_nin');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mothers');
    }
};
