<?php

namespace App\Services;

use App\Repositories\ReservationRepository;
use App\Repositories\IReservationRepository;
use RuntimeException;

class AdminReservationService
{
    private IReservationRepository $reservationRepository;

    public function __construct(?IReservationRepository $reservationRepository = null)
    {
        $this->reservationRepository = $reservationRepository ?: new ReservationRepository();
    }

    /**
     * ✅ Returns data formatted for AdminReservationViewModel
     */
    public function getAllReservations(): array
    {
        try {
            return $this->reservationRepository->getAllReservations();

        } catch (RuntimeException $e) {
            error_log("AdminReservationService::getAllReservations error: " . $e->getMessage());
            throw new RuntimeException("Failed to fetch reservations.");
        }
    }

    /**
     * ✅ Admin can delete ANY reservation
     * This automatically removes it from student panel
     */
    public function deleteReservation(int $id): void
    {
        try {
            if (!$this->reservationRepository->deleteReservation($id)) {
                throw new RuntimeException("Unable to delete reservation.");
            }

        } catch (RuntimeException $e) {
            error_log("AdminReservationService::deleteReservation error: " . $e->getMessage());
            throw new RuntimeException("Failed to delete reservation.");
        }
    }
}
