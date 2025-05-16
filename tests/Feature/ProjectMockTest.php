<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;

class ProjectMockTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_project_and_logs()
    {
        // Cria e autentica um usuário
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        // Mocka o LogService
        Mockery::mock('alias:App\\Services\\LogService')
            ->shouldReceive('log')
            ->once();

        // Dados de entrada
        $payload = [
            'name' => 'Projeto de Teste',
            'description' => 'Descricao do projeto',
        ];

        // Requisição para criar projeto
        $response = $this->postJson('/api/projects', $payload);

        // Verifica resposta
        $response->assertCreated();

        // Verifica no banco
        $this->assertDatabaseHas('projects', [
            'name' => 'Projeto de Teste',
            'user_id' => $user->id,
        ]);
    }

    public function test_it_updates_project_and_logs()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $project = Project::factory()->create(['user_id' => $user->id]);

        Mockery::mock('alias:App\\Services\\LogService')
            ->shouldReceive('log')
            ->once();

        $payload = [
            'name' => 'Projeto Atualizado',
            'description' => 'Descricao Atualizada',
        ];

        $response = $this->putJson("/api/projects/{$project->id}", $payload);

        $response->assertOk();

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Projeto Atualizado',
        ]);
    }

    public function test_it_deletes_project_and_logs()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $project = Project::factory()->create(['user_id' => $user->id]);

        $response = $this->deleteJson("/api/projects/{$project->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('projects', [
            'id' => $project->id
        ]);
    }
}
