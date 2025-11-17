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
        Schema::create('teams', function (Blueprint $table) {
            $table->id('team_id');
            $table->string('team_name', 100);
            $table->text('team_desc')->nullable();
            $table->unsignedBigInteger('leader_id');
            $table->integer('member_count')->default(1);
            $table->integer('member_limit')->default(5);
            $table->text('terms')->nullable();
            $table->enum('team_status', ['open', 'closed', 'full'])->default('open');
            $table->timestamp('created_at')->useCurrent();
            
            // Foreign key ke users table
            $table->foreign('leader_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
