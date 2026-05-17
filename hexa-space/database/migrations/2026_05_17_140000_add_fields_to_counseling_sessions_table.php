<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('counseling_sessions', function (Blueprint $table) {
            $table->string('final_mood')->nullable()->after('status');
            $table->boolean('is_escalated')->default(false)->after('final_mood');
            $table->text('doctor_notes')->nullable()->after('is_escalated');
        });
    }

    public function down(): void
    {
        Schema::table('counseling_sessions', function (Blueprint $table) {
            $table->dropColumn(['final_mood', 'is_escalated', 'doctor_notes']);
        });
    }
};
