<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_team', function (Blueprint $table) {
            $table->id('chat_id');
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('user_id');
            $table->text('text_chat')->nullable();
            $table->string('file_chat', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            // Foreign keys
            $table->foreign('team_id')->references('team_id')->on('teams')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_team');
    }
};
