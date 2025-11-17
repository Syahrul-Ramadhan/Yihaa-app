<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beasiswa', function (Blueprint $table) {
            $table->id('beasiswa_id');
            $table->string('nama_beasiswa', 200);
            $table->string('jenjang_beasiswa', 100)->nullable();
            $table->date('mulai_pendaftaran');
            $table->date('akhir_pendaftaran');
            $table->text('syarat_beasiswa')->nullable();
            $table->text('benefit_beasiswa')->nullable();
            $table->string('pemberi_beasiswa', 200)->nullable();
            $table->string('link_pendaftaran', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beasiswa');
    }
};
