<?php require __DIR__ . '/partials/header.php'; ?>

<?php
$isEditing = isset($reservation);
$today = date('Y-m-d');
?>

<div class="bg-[#F8FAFC] min-h-screen flex flex-col">
<main class="flex-grow flex items-center justify-center p-4 sm:p-6">
    <div class="bg-white rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.05)] w-full max-w-md overflow-hidden border border-white">

        <div class="bg-gradient-to-br from-blue-600 to-blue-800 p-8 sm:p-10 text-white text-center">
            <h1 class="text-2xl sm:text-3xl font-extrabold">
                <?= $isEditing ? 'Update Booking' : 'Book a Room' ?>
            </h1>
        </div>

        <div class="p-6 sm:p-10">
            <div id="messageContainer" class="hidden mb-4 p-4 rounded-2xl" role="alert" aria-live="polite"></div>

            <form action="<?= $isEditing ? '/reservations/update' : '/reservations/store' ?>"
                  method="POST"
                  class="space-y-5">
                <input type="hidden"
                       name="csrf_token"
                       value="<?= htmlspecialchars($csrfToken ?? '') ?>">

                <?php if ($isEditing): ?>
                    <input type="hidden"
                           name="id"
                           value="<?= htmlspecialchars($reservation['id']) ?>">

                    <input type="hidden"
                           id="reservationId"
                           value="<?= htmlspecialchars($reservation['id']) ?>">

                    <input type="hidden"
                           id="currentSlotId"
                           value="<?= htmlspecialchars($reservation['time_slot_id']) ?>">
                <?php endif; ?>

                <!-- ROOM -->
                <div>
                        <label for="roomSelect" class="block text-xs font-bold uppercase text-slate-400 mb-2">
                            Study Space
                        </label>

                    <select name="room_id"
                            id="roomSelect"
                            required
                            class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200 font-semibold">

                        <?php
                        $currentRoomId = $reservation['room_id'] ?? null;

                        foreach ($rooms as $room):
                            $roomId = htmlspecialchars($room['id']);
                            $capacity = htmlspecialchars($room['capacity']);
                            $roomNumber = htmlspecialchars($room['room_number']);
                        ?>
                            <option value="<?= $roomId ?>"
                                    data-capacity="<?= $capacity ?>"
                                    <?= ($currentRoomId == $room['id']) ? 'selected' : '' ?>>
                                Room <?= $roomNumber ?> (Max <?= $capacity ?>)
                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>

                <!-- GROUP + DATE -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <!-- GROUP SIZE -->
                    <div>
                        <label for="numPeople" class="block text-xs font-bold uppercase text-slate-400 mb-2">
                            Group Size
                        </label>

                        <input type="number"
                               name="num_people"
                               id="numPeople"
                               min="1"
                               required
                               value="<?= $isEditing 
                                    ? htmlspecialchars($reservation['num_people']) 
                                    : 1 ?>"
                               class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200 font-semibold">
                    </div>

                    <!-- DATE (FIXED FIELD NAME) -->
                    <div>
                        <label for="dateInput" class="block text-xs font-bold uppercase text-slate-400 mb-2">
                            Date
                        </label>

                        <input type="date"
                               id="dateInput"
                               name="date"
                               min="<?= $today ?>"
                               required
                               value="<?= $isEditing
                                    ? htmlspecialchars($reservation['date'])
                                    : $today ?>"
                               class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200 font-semibold">
                    </div>

                </div>

                <!-- TIME SLOT -->
                <div>
                        <label for="timeSlotSelect" class="block text-xs font-bold uppercase text-slate-400 mb-2">
                            Available Session
                        </label>

                    <select name="time_slot_id"
                            id="timeSlotSelect"
                            required
                            class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200 font-semibold">
                        <option value="">Loading...</option>
                    </select>
                </div>

                <!-- SUBMIT -->
                <button type="submit"
                        id="submitBtn"
                        class="w-full bg-blue-600 text-white py-4 rounded-2xl font-bold hover:bg-blue-700 transition disabled:bg-slate-300">

                    <?= $isEditing ? 'Save Changes' : 'Confirm Booking' ?>

                </button>

            </form>
        </div>
    </div>
</main>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>

<?php if (!empty($reservation)) : ?>
    <script src="/assets/js/edit_reservation.js"></script>
<?php else : ?>
    <script src="/assets/js/reservation-form.js"></script>
<?php endif; ?>
