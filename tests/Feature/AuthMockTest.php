<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;

class AuthMockTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_registers_user_and_logs()
    {
        Queue::fake();

        Mockery::mock('alias:App\\Services\\LogService')
            ->shouldReceive('log')
            ->once();

        $payload = [
            'name' => 'Usuário Teste',
            'email' => 'usuario@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertCreated();

        $this->assertDatabaseHas('users', [
            'email' => 'usuario@example.com'
        ]);
    }

    public function test_it_logs_user_in_and_logs()
    {
        Queue::fake();

        $user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => bcrypt('senha123'),
        ]);

        Mockery::mock('alias:App\\Services\\LogService')
            ->shouldReceive('log')
            ->once();

        $payload = [
            'email' => 'login@example.com',
            'password' => 'senha123',
        ];

        $response = $this->postJson('/api/login', $payload);

        $response->assertOk();
        $this->assertArrayHasKey('token', $response->json());
    }
}
