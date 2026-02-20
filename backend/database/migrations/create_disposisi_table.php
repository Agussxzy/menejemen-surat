<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('disposisi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_masuk_id')->constrained('surat_masuk')->onDelete('cascade');
            $table->foreignId('dari_user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('kepada_user_id')->constrained('users')->onDelete('restrict');
            $table->text('catatan')->nullable();
            $table->boolean('dibaca')->default(false);
            $table->timestamp('tanggal_disposisi')->useCurrent();
            $table->timestamps();
            
            $table->index(['surat_masuk_id']);
            $table->index(['kepada_user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('disposisi');
    }
};