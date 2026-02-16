<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eleves', function (Blueprint $table) {
            $table->text('appeal_text')->nullable()->after('etat_final');
            $table->string('appeal_document', 500)->nullable()->after('appeal_text');
            $table->string('appeal_status', 20)->nullable()->after('appeal_document');
            $table->string('appeal_accepted_by', 18)->nullable()->after('appeal_status');

            $table->foreign('appeal_accepted_by')
                  ->references('code_user')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('eleves', function (Blueprint $table) {
            $table->dropForeign(['appeal_accepted_by']);
            $table->dropColumn(['appeal_text', 'appeal_document', 'appeal_status', 'appeal_accepted_by']);
        });
    }
};
