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
        Schema::create('permohonans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->bigInteger('username');
            $table->bigInteger('nomor_ijazah');
            $table->integer('jumlah_lembar');
            $table->text('keperluan');
            $table->string('file');
            $table->string('file_ijazah_verified')->nullable();
            $table->tinyInteger('status')->default(1); // 1=dimulai, 2=diterima, 3=verifikasi, 4=ditandatangani, 5=siap diambil, 6 ditolak
            $table->timestamp('tanggal_diambil')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('username');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonans');
    }
};
