<?php

namespace App\Repositories;

interface IReservationRepository
{
    public function getSlotsByRoom(int $roomId, string $reservationDate): array;

    public function getAvailableSlotsByRoomAndDate(
        int $roomId,
        string $date,
        ?int $reservationId = null
    ): array;

    public function getSlotsByRoomAndDate(int $roomId, string $date, int $excludeId = 0): array;

    public function getByUserId(int $userId): array;

    public function getAllReservations(): array;

    public function isRoomAvailable(int $roomId, string $date, int $slotId, int $excludeId = 0): bool;

    public function create(array $data): bool;

    public function find(int $id, int $userId);

    public function delete(int $id, int $userId): bool;

    public function deleteReservation(int $id): bool;

    public function update(int $id, int $userId, array $data): bool;

    public function getReservationDetails(int $id): ?array;
}
