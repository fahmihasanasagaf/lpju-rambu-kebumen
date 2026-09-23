<?php

namespace Tests\Feature;

use App\Models\Desa;
use App\Models\Kecamatan;
use App\Models\Lpju;
use App\Models\Rambu;
use App\Models\SumberDana;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;

class ComplaintTest extends TestCase
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

    public function test_public_complaint_submission_stores_location_and_photo(): void
    {
        Storage::fake('public');
        $asset = $this->asset();

        $response = $this->post('/api/aduan', [
            'jenis_aset' => 'lpju', 'aset_id' => $asset->id, 'kategori_aduan' => 'lampu_mati',
            'nama_pelapor' => 'Warga Kebumen', 'kontak_pelapor' => '081234',
            'alamat_kejadian' => 'Jalan Kebumen', 'latitude' => '-7.678600', 'longitude' => '109.656500',
            'deskripsi' => 'Lampu tidak menyala', 'foto' => UploadedFile::fake()->image('aduan.jpg'),
        ], ['Accept' => 'application/json']);

        $response->assertCreated()->assertJsonPath('status_aduan', 'baru')->assertJsonMissing(['status_aduan' => 'selesai']);
        $this->assertDatabaseHas('aduan', ['aset_id' => $asset->id, 'alamat_kejadian' => 'Jalan Kebumen', 'latitude' => '-7.678600']);
        Storage::disk('public')->assertExists($response->json('foto'));
    }

    public function test_guests_cannot_read_or_update_internal_complaints(): void
    {
        $asset = $this->asset();
        $created = $this->postJson('/api/aduan', [
            'jenis_aset' => 'lpju', 'aset_id' => $asset->id, 'kategori_aduan' => 'lainnya',
            'nama_pelapor' => 'Warga', 'alamat_kejadian' => 'Jalan', 'latitude' => -7.6,
            'longitude' => 109.6, 'deskripsi' => 'Keluhan',
        ])->json('id');

        $this->getJson('/api/aduan')->assertUnauthorized();
        $this->getJson("/api/aduan/{$created}")->assertUnauthorized();
        $this->putJson("/api/aduan/{$created}", ['status_aduan' => 'diproses'])->assertUnauthorized();
    }

    public function test_admin_and_operator_can_manage_status_but_public_payload_cannot_set_it(): void
    {
        $asset = $this->asset();
        $id = $this->postJson('/api/aduan', [
            'jenis_aset' => 'lpju', 'aset_id' => $asset->id, 'kategori_aduan' => 'lainnya',
            'nama_pelapor' => 'Warga', 'alamat_kejadian' => 'Jalan', 'latitude' => -7.6,
            'longitude' => 109.6, 'deskripsi' => 'Keluhan', 'status_aduan' => 'selesai',
        ])->assertCreated()->json('id');
        $operator = User::factory()->create(['role' => 'operator']);
        $this->actingAs($operator, 'sanctum')->putJson("/api/aduan/{$id}", ['status_aduan' => 'diproses'])->assertOk()->assertJsonPath('ditindak_oleh', $operator->id);
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin, 'sanctum')->getJson('/api/aduan')->assertOk();
        $this->actingAs($admin, 'sanctum')->putJson("/api/aduan/{$id}", ['status_aduan' => 'selesai'])->assertOk();
    }

    public function test_public_complaint_rejects_invalid_photo_and_coordinates(): void
    {
        $asset = $this->asset();
        $this->postJson('/api/aduan', [
            'jenis_aset' => 'lpju', 'aset_id' => $asset->id, 'kategori_aduan' => 'lainnya',
            'nama_pelapor' => 'Warga', 'alamat_kejadian' => 'Jalan', 'latitude' => 91,
            'longitude' => 109, 'deskripsi' => 'Keluhan', 'foto' => UploadedFile::fake()->create('script.php', 10, 'application/x-php'),
        ])->assertUnprocessable()->assertJsonValidationErrors(['latitude', 'foto']);
    }

    public function test_public_complaint_page_is_renderable(): void
    {
        $this->get('/aduan')->assertOk()->assertSee('Kirim aduan')->assertSee('complaint-map');
    }



    public function test_complaint_summary_requires_auth_and_counts_all_statuses(): void
    {
        $asset = $this->asset();
        foreach (['baru', 'selesai'] as $status) {
            $this->postJson('/api/aduan', ['jenis_aset'=>'lpju','aset_id'=>$asset->id,'kategori_aduan'=>'lainnya','nama_pelapor'=>'Warga','alamat_kejadian'=>'Jalan','latitude'=>-7.6,'longitude'=>109.6,'deskripsi'=>'Keluhan']);
        }
        $this->getJson('/api/aduan/summary')->assertUnauthorized();
        $operator = User::factory()->create(['role'=>'operator']);
        $response = $this->actingAs($operator, 'sanctum')->getJson('/api/aduan/summary')->assertOk();
        $response->assertJsonStructure(['total', 'selesai', 'persentase_selesai'])->assertJsonPath('total', 2)->assertJsonPath('selesai', 0)->assertJsonPath('persentase_selesai', 0);
    }

    public function test_status_changes_create_deduplicated_complaint_history(): void
    {
        $asset = $this->asset();
        $id = $this->postJson('/api/aduan', ['jenis_aset'=>'lpju','aset_id'=>$asset->id,'kategori_aduan'=>'lainnya','nama_pelapor'=>'Warga','alamat_kejadian'=>'Jalan','latitude'=>-7.6,'longitude'=>109.6,'deskripsi'=>'Keluhan'])->json('id');
        $operator = User::factory()->create(['role'=>'operator']);
        $this->actingAs($operator, 'sanctum')->putJson("/api/aduan/$id", ['status_aduan'=>'diproses','keterangan'=>'Ditindak'])->assertOk();
        $this->actingAs($operator, 'sanctum')->putJson("/api/aduan/$id", ['status_aduan'=>'diproses'])->assertOk();
        $this->actingAs($operator, 'sanctum')->putJson("/api/aduan/$id", ['status_aduan'=>'selesai'])->assertOk();
        $this->assertDatabaseCount('aduan_histories', 2);
        $this->assertDatabaseHas('aduan_histories', ['aduan_id'=>$id,'status_lama'=>'baru','status_baru'=>'diproses','diubah_oleh'=>$operator->id]);
        $this->actingAs($operator, 'sanctum')->getJson('/api/aduan/'.$id)->assertJsonCount(2, 'histories');
        $this->actingAs($operator, 'sanctum')->getJson('/api/histori')->assertJsonFragment(['name' => $operator->name, 'role' => 'operator']);
        $this->actingAs($operator, 'sanctum')->getJson('/api/histori?source=aduan')->assertOk()->assertJsonCount(2);
    }

    private function asset(): Lpju
    {
        return Lpju::create([
            'desa_id' => Desa::first()->id, 'sumber_dana_id' => SumberDana::first()->id,
            'alamat' => 'Jalan Kebumen', 'latitude' => '-7.678600', 'longitude' => '109.656500',
            'status' => 'baik', 'petugas_id' => User::factory()->create(['role' => 'operator'])->id,
        ]);
    }
}
