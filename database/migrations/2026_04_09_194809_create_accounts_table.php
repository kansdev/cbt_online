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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_registrasi');
            $table->string('nama');
            $table->string('nisn')->unique();
            $table->string('jenis_kelamin');
            $table->string('jurusan')->nullable();
            $table->string('kategori')->nullable();
            $table->string('jenis_umum')->nullable();
            $table->string('jenis_kejuruan')->nullable();
            $table->string('tanggal_lahir')->nullable();
            $table->integer('id_gelombang')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('nonaktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
