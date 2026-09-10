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
        Schema::create('lpju', function (Blueprint $table) {
            $table->id();
            $table->integer('desa_id');
            $table->foreignId('sumber_dana_id')->constrained('sumber_dana')->onDelete('restrict');
            $table->string('alamat');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('foto')->nullable();
            $table->integer('tahun_anggaran')->nullable();
            $table->date('tanggal_diterima')->nullable();
            $table->date('tanggal_pasang')->nullable();
            $table->string('status')->default('baik');
            $table->foreignId('petugas_id')->constrained('users')->onDelete('restrict');
            $table->timestamps();

            $table->foreign('desa_id')->references('id')->on('desa')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lpju');
    }
};