<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\{Desa, Kecamatan, SumberDana};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;

class SecurityAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_registration_ignores_requested_admin_role(): void
    {
        $this->mockCaptcha();

        $response = $this->postJson('/api/register', [
            'name' => 'Operator Baru',
            'email' => 'operator@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
            'captcha_token' => 'test-token',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('users', [
            'email' => 'operator@example.test',
            'role' => 'operator',
        ]);
    }

    public function test_public_registration_always_creates_operator(): void
    {
        $this->withoutExceptionHandling();
        $this->mockCaptcha();

        $response = $this->postJson('/api/register', [
            'name' => 'Operator Baru',
            'email' => 'operator@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'captcha_token' => 'test-token',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('users', [
            'email' => 'operator@example.test',
            'role' => 'operator',
        ]);
    }

    public function test_operator_can_read_reference_data_but_cannot_mutate_it(): void
    {
        Schema::create('kecamatan', function (Blueprint $table) { $table->id(); $table->string('kode'); $table->string('kecamatan'); $table->timestamps(); });
        Schema::create('desa', function (Blueprint $table) { $table->id(); $table->string('kd_kec'); $table->string('kd_desa'); $table->string('desa'); $table->timestamps(); });
        Desa::create(['kd_kec' => '01', 'kd_desa' => '001', 'desa' => 'Test']);
        SumberDana::create(['nama_sumber' => 'APBD']);
        $operator = User::factory()->create(['role' => 'operator']);

        $this->actingAs($operator, 'sanctum')->getJson('/api/desa')->assertOk();
        $this->actingAs($operator, 'sanctum')->getJson('/api/kecamatan')->assertOk();
        $this->actingAs($operator, 'sanctum')->getJson('/api/sumber-dana')->assertOk();
        $this->actingAs($operator, 'sanctum')
            ->postJson('/api/desa', ['kd_kec' => '01', 'kd_desa' => '002', 'desa' => 'Baru'])
            ->assertForbidden();
    }

    public function test_history_requires_authenticated_operator_or_admin(): void
    {
        $this->getJson('/api/histori')->assertUnauthorized();

        $operator = User::factory()->create(['role' => 'operator']);
        $this->actingAs($operator, 'sanctum')->getJson('/api/histori')->assertOk();
    }

    public function test_geography_mutations_are_admin_only(): void
    {
        $operator = User::factory()->create(['role' => 'operator']);
        $this->actingAs($operator, 'sanctum')
            ->postJson('/api/kecamatan', ['kode' => '01', 'kecamatan' => 'Test'])
            ->assertForbidden();

        $this->postJson('/api/kecamatan', ['kode' => '01', 'kecamatan' => 'Test'])
            ->assertForbidden();
    }

    public function test_rate_limits_register_requests(): void
    {
        $this->mockCaptcha();
        $payload = ['name' => 'Rate Test', 'email' => 'rate@example.test', 'password' => 'password123', 'password_confirmation' => 'password123', 'captcha_token' => 'test'];
        foreach (range(1, 3) as $i) {
            $payload['email'] = "rate{$i}@example.test";
            $this->postJson('/api/register', $payload);
        }
        $payload['email'] = 'rate4@example.test';
        $this->postJson('/api/register', $payload)->assertStatus(429);
    }

    public function test_operator_cannot_delete_assets(): void
    {
        $operator = User::factory()->create(['role' => 'operator']);
        $this->actingAs($operator, 'sanctum')->deleteJson('/api/lpju/999')->assertForbidden();
    }

    public function test_last_admin_cannot_be_deleted_or_demoted(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin, 'sanctum')->deleteJson('/api/admin/users/'.$admin->id)->assertStatus(422);
        $this->actingAs($admin, 'sanctum')->putJson('/api/admin/users/'.$admin->id, ['role' => 'operator'])->assertStatus(422);
    }
    private function mockCaptcha(): void
    {
        \Illuminate\Support\Facades\Http::fake([
            'google.com/recaptcha/api/siteverify' => \Illuminate\Support\Facades\Http::response(['success' => true]),
        ]);
    }
}
