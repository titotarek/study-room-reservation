<?php

namespace App\Repositories;

interface ITimeSlotRepository
{
    public function create(array $data): bool;

    public function getByRoom(int $roomId): array;

    public function getAvailableSlotsByRoomAndDate(
        int $roomId,
        string $date,
        ?int $reservationId = null
    ): array;

    public function find(int $id): ?array;

    public function delete(int $id): bool;
}
