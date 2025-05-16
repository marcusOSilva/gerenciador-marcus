<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Services\TaskService;
use Illuminate\Support\Facades\Gate;
use App\Jobs\SendTaskNotification;
use App\Services\LogService;

class TaskController extends Controller
{
    protected $service;

    public function __construct(TaskService $service)
    {
        $this->service = $service;
    }

    public function index($projectId)
    {
        return response()->json($this->service->allByProject($projectId));
    }

    public function store(StoreTaskRequest $request, $projectId)
    {
        $data = $request->validated();
        $data['project_id'] = $projectId;

        $task = $this->service->create($data);

        LogService::log($request, 'create_task');
        
        SendTaskNotification::dispatch($task->toArray());

        return response()->json($task, 201);
    }

    public function show($id)
    {
        $task = $this->service->find($id);
        return response()->json($task);
    }

    public function update(UpdateTaskRequest $request, $id)
    {
        $task = $this->service->find($id);

        if (Gate::denies('update', $task)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $updated = $this->service->update($id, $request->validated());

        LogService::log($request, 'update_task');
        
        SendTaskNotification::dispatch($updated->toArray());

        return response()->json($updated);
    }

    public function destroy($id)
    {
        $task = $this->service->find($id);

        if (Gate::denies('delete', $task)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $this->service->delete($id);
        return response()->json(null, 204);
    }
}
