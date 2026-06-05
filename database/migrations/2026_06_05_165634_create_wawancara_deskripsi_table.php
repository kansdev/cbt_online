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
        Schema::create('wawancara_deskripsi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pewawancara_id')->constrained('pewawancara')->cascadeOnDelete();
            $table->string('kategori');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wawancara_deskripsi');
    }
};
