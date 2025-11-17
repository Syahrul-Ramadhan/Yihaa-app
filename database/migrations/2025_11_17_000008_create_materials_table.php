<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id('material_id');
            $table->string('tittle', 200);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('uploaded_by');
            $table->string('file_url', 500);
            $table->string('thumbnail_url', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            // Foreign key ke users
            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
