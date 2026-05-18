<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('counseling_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('counseling_sessions', 'final_mood')) {
                $table->string('final_mood')->nullable()->after('status');
            }
            if (!Schema::hasColumn('counseling_sessions', 'is_escalated')) {
                $table->boolean('is_escalated')->default(false)->after('final_mood');
            }
            if (!Schema::hasColumn('counseling_sessions', 'doctor_notes')) {
                $table->text('doctor_notes')->nullable()->after('is_escalated');
            }
        });
    }

    public function down(): void
    {
        Schema::table('counseling_sessions', function (Blueprint $table) {
            $table->dropColumn(['final_mood', 'is_escalated', 'doctor_notes']);
        });
    }
};
