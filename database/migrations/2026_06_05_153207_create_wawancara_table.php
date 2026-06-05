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
        Schema::create('wawancara', function (Blueprint $table) {
            $table->id();
            $table->foreignedId('pewawancara_id')->constrainer('pewawancara');
            $table->unsignedBigInteger('user_id');
            $table->string('nomor_pendaftaran')->unique();
            $table->text('catatan');
            $table->tinyInteger('kesimpulan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wawancara');
    }
};
