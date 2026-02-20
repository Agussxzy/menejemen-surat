<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('surat_keluar', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->unique();
            $table->date('tanggal_surat');
            $table->string('tujuan');
            $table->string('perihal');
            $table->enum('klasifikasi', ['rahasia', 'penting', 'umum'])->default('umum');
            $table->text('tembusan')->nullable();
            $table->string('penandatangan');
            $table->string('file_surat')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->timestamps();
            
            $table->index(['nomor_surat']);
            $table->index(['tanggal_surat']);
            $table->index(['tujuan']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('surat_keluar');
    }
};