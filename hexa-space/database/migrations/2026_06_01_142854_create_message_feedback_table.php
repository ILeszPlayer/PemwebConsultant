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
        Schema::create('message_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_message_id')->constrained('chat_messages')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('feedback', ['like', 'dislike'])->default('like');
            $table->timestamps();
            $table->unique(['chat_message_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_feedback');
    }
};
