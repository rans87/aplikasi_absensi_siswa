<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Guru;
use App\Models\Siswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_and_access_dashboard()
    {
        $user = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $response = $this->post('/login', [
            'role' => 'admin',
            'username' => 'admin@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($user, 'web');
    }

    public function test_guru_can_login_and_access_dashboard()
    {
        $guru = Guru::create([
            'nama' => 'Guru Test',
            'email' => 'guru@test.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'role' => 'guru',
            'email' => 'guru@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/guru/dashboard');
        $this->assertAuthenticatedAs($guru, 'guru');
        
        // Verify dashboard access
        $response = $this->actingAs($guru, 'guru')->get('/guru/dashboard');
        $response->assertStatus(200);
    }

    public function test_login_page_redirects_authenticated_guru()
    {
        $guru = Guru::create([
            'nama' => 'Guru Test',
            'email' => 'guru@test.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($guru, 'guru')->get('/login');
        $response->assertRedirect('/guru/dashboard');
    }
}
