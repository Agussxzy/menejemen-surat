<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('surat_masuk', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->unique();
            $table->date('tanggal_surat');
            $table->date('tanggal_diterima');
            $table->string('pengirim');
            $table->string('perihal');
            $table->enum('klasifikasi', ['rahasia', 'penting', 'umum'])->default('umum');
            $table->enum('status', ['baru', 'diproses', 'selesai'])->default('baru');
            $table->string('file_surat')->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->timestamps();
            
            $table->index(['nomor_surat']);
            $table->index(['tanggal_surat']);
            $table->index(['pengirim']);
            $table->index(['status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('surat_masuk');
    }
};