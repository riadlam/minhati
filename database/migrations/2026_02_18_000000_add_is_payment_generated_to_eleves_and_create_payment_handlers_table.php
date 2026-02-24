<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds is_payment_generated to eleves (0 or 1).
     * Creates payment_handlers table with unique archive_code.
     */
    public function up(): void
    {
        Schema::table('eleves', function (Blueprint $table) {
            $table->unsignedTinyInteger('is_payment_generated')->default(0)->after('etat_final');
        });

        Schema::create('payment_handlers', function (Blueprint $table) {
            $table->id();
            $table->string('archive_code')->unique();
            $table->string('file_path', 500)->nullable();
            $table->date('date')->nullable();
            $table->unsignedInteger('n_of_tuteurs_handled')->default(0);
            $table->unsignedInteger('n_of_tuteurs_failed')->default(0);
            $table->unsignedInteger('n_of_students_handled')->default(0);
            $table->string('recipient_ccp', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eleves', function (Blueprint $table) {
            $table->dropColumn('is_payment_generated');
        });

        Schema::dropIfExists('payment_handlers');
    }
};
