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
        Schema::create('aduan', function (Blueprint $table) {
            $table->id();
            $table->morphs('aset');
            $table->string('nama_pelapor');
            $table->string('kontak_pelapor')->nullable();
            $table->text('deskripsi');
            $table->string('foto')->nullable();
            $table->string('status_aduan')->default('baru');
            $table->date('tanggal_aduan');
            $table->foreignId('ditindak_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aduan');
    }
};