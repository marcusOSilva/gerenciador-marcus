<?php

namespace App\Repositories\Contracts;

interface ProjectRepositoryInterface
{
    public function allForUser(int $userId);
    public function findByIdAndUser(int $id, int $userId);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
}
