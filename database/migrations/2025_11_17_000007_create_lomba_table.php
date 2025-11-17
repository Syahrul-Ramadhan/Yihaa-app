<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lomba', function (Blueprint $table) {
            $table->id('lomba_id');
            $table->string('nama_lomba', 200);
            $table->date('tanggal_pelaksanaan');
            $table->date('mulai_pendaftaran');
            $table->date('akhir_pendaftaran');
            $table->string('lokasi', 255)->nullable();
            $table->string('kategori_lomba', 100)->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('penyelenggara', 200)->nullable();
            $table->string('link_pendaftaran', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lomba');
    }
};
