<?php

namespace App\Repositories;

use App\Models\Project;
use App\Repositories\Contracts\ProjectRepositoryInterface;

class ProjectRepository implements ProjectRepositoryInterface
{
    public function allForUser(int $userId)
    {
        return Project::where('user_id', $userId)->get();
    }

    public function findByIdAndUser(int $id, int $userId)
    {
        return Project::where('id', $id)->where('user_id', $userId)->firstOrFail();
    }

    public function create(array $data)
    {
        return Project::create($data);
    }

    public function update(int $id, array $data)
    {
        $project = Project::findOrFail($id);
        $project->update($data);
        return $project;
    }

    public function delete(int $id)
    {
        return Project::destroy($id);
    }
}
