<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Services\LogService;
use App\Services\ProjectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProjectController extends Controller
{
    protected $service;

    public function __construct(ProjectService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $projects = $this->service->all(auth()->id());
        return response()->json($projects);
    }

    public function store(StoreProjectRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        $project = $this->service->create($data);

        LogService::log($request, 'create_project');
        
        return response()->json($project, 201);
    }

    public function show($id)
    {
        $project = $this->service->find($id, auth()->id());
        return response()->json($project);
    }

    public function update(UpdateProjectRequest $request, $id)
    {
        $project = $this->service->find($id, auth()->id());

        if (Gate::denies('update', $project)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $updated = $this->service->update($id, $request->validated());

        LogService::log($request, 'update_project');
        
        return response()->json($updated);
    }

    public function destroy($id)
    {
        $project = $this->service->find($id, auth()->id());

        if (Gate::denies('delete', $project)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $this->service->delete($id);
        
        return response()->json(null, 204);
    }
}
