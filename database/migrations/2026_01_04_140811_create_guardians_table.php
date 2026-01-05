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
        if (!Schema::hasTable('guardians')) {
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
                $table->string('nin', 18)->unique()->comment('الرقم الوطني للوصي');
                $table->string('nss', 12)->nullable()->comment('الرقم الوطني للضمان الاجتماعي للوصي');
                $table->string('nom_ar', 50)->comment('لقب الوصي بالعربية');
                $table->string('prenom_ar', 50)->comment('اسم الوصي بالعربية');
                $table->string('nom_fr', 50)->nullable()->comment('لقب الوصي بالفرنسية');
                $table->string('prenom_fr', 50)->nullable()->comment('اسم الوصي بالفرنسية');
                $table->string('categorie_sociale', 80)->nullable()->comment('الفئة الاجتماعية');
                $table->decimal('montant_s', 10, 2)->nullable()->comment('مبلغ الدخل الشهري');
                $table->string('tuteur_nin', 18)->comment('الرقم الوطني للولي');
                $table->date('date_insertion')->nullable();
            $table->timestamps();
                
                // Create index first
                $table->index('tuteur_nin');
            });
            
            // Add foreign key constraint - check charset/collation of referenced column first
            if (Schema::hasTable('tuteures') && Schema::hasColumn('tuteures', 'nin')) {
                try {
                    // Get charset and collation from the referenced column
                    $columnInfo = DB::selectOne("
                        SELECT CHARACTER_SET_NAME, COLLATION_NAME 
                        FROM information_schema.COLUMNS 
                        WHERE TABLE_SCHEMA = DATABASE() 
                        AND TABLE_NAME = 'tuteures' 
                        AND COLUMN_NAME = 'nin'
                    ");
                    
                    if ($columnInfo) {
                        // Modify tuteur_nin column to match charset/collation
                        $charset = $columnInfo->CHARACTER_SET_NAME ?? 'utf8mb4';
                        $collation = $columnInfo->COLLATION_NAME ?? 'utf8mb4_unicode_ci';
                        
                        DB::statement("ALTER TABLE `guardians` 
                            MODIFY COLUMN `tuteur_nin` VARCHAR(18) 
                            CHARACTER SET {$charset} 
                            COLLATE {$collation} 
                            NOT NULL");
                    }
                    
                    // Now add the foreign key constraint
                    DB::statement('ALTER TABLE `guardians` 
                        ADD CONSTRAINT `guardians_tuteur_nin_foreign` 
                        FOREIGN KEY (`tuteur_nin`) 
                        REFERENCES `tuteures` (`nin`) 
                        ON DELETE CASCADE 
                        ON UPDATE CASCADE');
                } catch (\Exception $e) {
                    // If foreign key creation fails, log it but don't fail the migration
                    // The relationship can be added manually if needed
                    \Log::warning('Failed to create foreign key constraint: ' . $e->getMessage());
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guardians');
    }
};
