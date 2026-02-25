<?php

namespace App\Repositories;

interface ITimeSlotRepository
{
    public function create(array $data): bool;

    public function getByRoom(int $roomId): array;

    public function delete(int $id): bool;

    public function find(int $id): ?array;
}
