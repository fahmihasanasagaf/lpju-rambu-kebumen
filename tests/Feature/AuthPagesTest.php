<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthPagesTest extends TestCase
{
    public function test_login_page_is_renderable(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Masuk ke panel kerja')
            ->assertSee('Daftar sebagai Operator')
            ->assertSee('login-asset-map');
    }

    public function test_operator_registration_page_is_renderable(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertSee('Daftar sebagai Operator')
            ->assertSee('password_confirmation')
            ->assertSee('Semua pendaftaran publik otomatis menggunakan role Operator.')
            ->assertSee('/api/register', false);
    }
}
