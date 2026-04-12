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
        Schema::create('soal_acaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_siswa')->constrained('accounts')->cascadeOnDelete();
            $table->foreignId('id_soal')->constrained('soals')->cascadeOnDelete();
            $table->integer('urutan');
            $table->string('tahap');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soal_acaks');
    }
};
