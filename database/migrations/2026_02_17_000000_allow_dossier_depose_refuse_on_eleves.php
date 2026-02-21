<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Ensures dossier_depose can store 'refuse' (declined by ts_commune).
     * Values: oui, non, refuse.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('eleves', 'dossier_depose')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE eleves MODIFY dossier_depose VARCHAR(20) NULL COMMENT \'oui=موافق عليه, non=قيد المراجعة, refuse=مرفوض من البلدية\'');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE eleves ALTER COLUMN dossier_depose TYPE VARCHAR(20)');
        }
        // SQLite and others: column is typically flexible; 'refuse' fits if column exists
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('eleves', 'dossier_depose')) {
            return;
        }
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE eleves MODIFY dossier_depose VARCHAR(10) NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE eleves ALTER COLUMN dossier_depose TYPE VARCHAR(10)');
        }
    }
};
