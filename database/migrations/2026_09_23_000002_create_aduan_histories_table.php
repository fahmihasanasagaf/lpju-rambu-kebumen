<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aduan_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aduan_id')->constrained('aduan')->cascadeOnDelete();
            $table->string('status_lama')->nullable();
            $table->string('status_baru');
            $table->text('keterangan')->nullable();
            $table->foreignId('diubah_oleh')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->index(['aduan_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aduan_histories');
    }
};
