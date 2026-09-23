<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['lpju', 'rambu'] as $tableName) {
            if (! Schema::hasColumn($tableName, 'qr_code')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('qr_code')->nullable()->unique();
                });
            }
        }

        foreach (['lpju', 'rambu'] as $tableName) {
            DB::table($tableName)->whereNull('qr_code')->orderBy('id')->eachById(function ($asset) use ($tableName) {
                DB::table($tableName)->where('id', $asset->id)->update([
                    'qr_code' => strtoupper($tableName).'-'.str_pad((string) $asset->id, 4, '0', STR_PAD_LEFT),
                ]);
            });
        }
    }

    public function down(): void
    {
        foreach (['lpju', 'rambu'] as $tableName) {
            if (Schema::hasColumn($tableName, 'qr_code')) {
                Schema::table($tableName, fn (Blueprint $table) => $table->dropUnique($tableName.'_qr_code_unique')->dropColumn('qr_code'));
            }
        }
    }
};
