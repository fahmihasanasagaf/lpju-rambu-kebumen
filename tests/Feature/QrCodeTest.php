<?php

namespace Tests\Feature;

use App\Models\Desa;
use App\Models\Kecamatan;
use App\Models\Lpju;
use App\Models\Rambu;
use App\Models\SumberDana;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;

class QrCodeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        if (! Schema::hasTable('kecamatan')) Schema::create('kecamatan', function (Blueprint $table) { $table->id(); $table->string('kode'); $table->string('kecamatan'); $table->timestamps(); });
        if (! Schema::hasTable('desa')) Schema::create('desa', function (Blueprint $table) { $table->id(); $table->string('kd_kec'); $table->string('kd_desa'); $table->string('desa'); $table->timestamps(); });
        $kecamatan = Kecamatan::first() ?: Kecamatan::create(['kode' => '01', 'kecamatan' => 'Kebumen']);
        Desa::first() ?: Desa::create(['kd_kec' => $kecamatan->kode, 'kd_desa' => '001', 'desa' => 'Kebumen']);
        SumberDana::first() ?: SumberDana::create(['nama_sumber' => 'APBD']);
    }

    public function test_lpju_qr_endpoint_returns_svg_without_authentication(): void
    {
        $asset = $this->lpju();

        $response = $this->get(route('assets.lpju.qr', $asset));

        $response->assertOk()
            ->assertHeader('Content-Type', 'image/svg+xml')
            ->assertHeader('Content-Disposition', 'inline')
            ->assertSee('<svg', false)
            ->assertDontSee('<!DOCTYPE html>', false)
            ->assertDontSee('Bearer', false)
            ->assertDontSee('Unauthenticated', false);
    }

    public function test_rambu_qr_endpoint_returns_svg_without_authentication(): void
    {
        $asset = Rambu::create([
            'desa_id' => Desa::first()->id, 'sumber_dana_id' => SumberDana::first()->id,
            'jenis_rambu' => 'Peringatan', 'alamat' => 'Jalan Kebumen',
            'latitude' => '-7.678600', 'longitude' => '109.656500', 'status' => 'baik',
            'petugas_id' => User::factory()->create(['role' => 'operator'])->id,
        ]);

        $response = $this->get(route('assets.rambu.qr', $asset));

        $response->assertOk()
            ->assertHeader('Content-Type', 'image/svg+xml')
            ->assertSee('<svg', false)
            ->assertDontSee('<!DOCTYPE html>', false);
    }

    public function test_unknown_asset_qr_returns_not_found(): void
    {
        $this->get('/assets/lpju/999999/qr')->assertNotFound();
        $this->get('/assets/rambu/999999/qr')->assertNotFound();
    }

    private function lpju(): Lpju
    {
        return Lpju::create([
            'desa_id' => Desa::first()->id, 'sumber_dana_id' => SumberDana::first()->id,
            'alamat' => 'Jalan Kebumen', 'latitude' => '-7.678600', 'longitude' => '109.656500',
            'status' => 'baik', 'petugas_id' => User::factory()->create(['role' => 'operator'])->id,
        ]);
    }
}
