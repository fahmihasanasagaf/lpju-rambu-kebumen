<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aduan', function (Blueprint $table) {
            $table->string('kategori_aduan')->nullable()->after('kontak_pelapor');
            $table->string('alamat_kejadian')->nullable()->after('deskripsi');
            $table->string('latitude')->nullable()->after('alamat_kejadian');
            $table->string('longitude')->nullable()->after('latitude');
        });
    }

    public function down(): void
    {
        Schema::table('aduan', function (Blueprint $table) {
            $table->dropColumn(['kategori_aduan', 'alamat_kejadian', 'latitude', 'longitude']);
        });
    }
};
