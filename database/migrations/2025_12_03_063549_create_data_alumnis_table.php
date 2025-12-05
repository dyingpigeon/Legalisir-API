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
        Schema::create('dataalumnis', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('nim')->unique();
            $table->string('email', 100)->unique();
            $table->bigInteger('nik')->unique();
            $table->string('nama');
            $table->enum('jk', ['laki-laki', 'perempuan']);
            $table->string('nama_Ibu');
            $table->string('agama');
            $table->string('tempat_lahir', 100);
            $table->date('tanggal_lahir');
            $table->text('alamat');
            $table->string('hp', 20);
            $table->string('nomor_ijazah');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            
            // Indexes
            $table->index('nim');
            $table->index('nik');
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_alumnis');
    }
};
