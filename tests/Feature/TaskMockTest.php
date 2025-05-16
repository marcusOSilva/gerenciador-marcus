<?php

namespace Tests\Feature;

use App\Jobs\SendTaskNotification;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;

# php artisan test tests/Feature/TaskMockTest.php
class TaskMockTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_task_and_dispatches_job_and_logs()
    {
        // Cria um usuário autenticado
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        // Cria um projeto para o usuário
        $project = Project::factory()->create(['user_id' => $user->id]);

        // Fakes para fila
        Queue::fake();

        // Usa o spy para capturar chamada estática ao LogService
        \Mockery::mock('alias:App\\Services\\LogService')
            ->shouldReceive('log')
            ->once();

        // Payload
        $payload = [
            'name' => 'Tarefa de Teste',
            'description' => 'Descrição da tarefa',
            'status' => 'A Fazer',
            'user_id' => $user->id,
        ];

        // Envia requisição
        $response = $this->postJson("/api/projects/{$project->id}/tasks", $payload);

        // Verifica se foi criado com sucesso
        $response->assertCreated();

        // Verifica se a task foi salva
        $this->assertDatabaseHas('tasks', [
            'name' => 'Tarefa de Teste',
            'project_id' => $project->id,
        ]);

        // Verifica se o job foi enfileirado
        Queue::assertPushed(SendTaskNotification::class);
    }
}
