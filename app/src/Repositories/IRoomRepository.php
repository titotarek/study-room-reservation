<?php

namespace App\Repositories;

interface IRoomRepository
{
    public function getAllRooms(): array;

    public function find(int $id): ?array;

    public function create(array $data): int|bool;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function getTimeSlotsByRoom(int $roomId, ?string $reservationDate = null): array;

    public function deleteTimeSlot(int $slotId): bool;

    public function getAvailableSlotsByRoomAndDate(
        int $roomId,
        string $date,
        ?int $reservationId = null
    ): array;
}
