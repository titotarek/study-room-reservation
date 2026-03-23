<?php
/**
 * @var \App\ViewModels\RoomViewModel[] $rooms
 */

$title = 'Admin – Manage Rooms';
?>

<?php require __DIR__ . '/partials/header.php'; ?>
<section>
    <div class="flex justify-between items-center mb-10">
        <div>
            <h2 class="text-3xl font-bold">Room Inventory</h2>
            <p class="text-slate-500 mt-1">Configure study spaces and availability.</p>
        </div>

        <button type="button"
                id="openAddRoomBtn"
                onclick="openAddRoomModal()"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold shadow-md transition">
            + Add New Room
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <?php foreach ($rooms as $room): ?>
            <div class="bg-white rounded-2xl shadow-md p-6 border hover:shadow-lg transition">

                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-xl font-bold"><?= htmlspecialchars($room->roomNumber) ?></h3>

                    <div class="flex gap-4 text-sm items-start">
                        <button type="button"
                                onclick="openSlotModal(<?= $room->id ?>)"
                                class="text-blue-600 hover:underline font-semibold">
                            Slots
                        </button>

                        <form id="delete-room-form-<?= (int) $room->id ?>" action="/admin/rooms/delete" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                            <input type="hidden" name="id" value="<?= (int) $room->id ?>">
                            <button type="button"
                                    onclick="openDeleteRoomModal(<?= (int) $room->id ?>, '<?= htmlspecialchars($room->roomNumber, ENT_QUOTES) ?>')"
                                    class="text-red-500 hover:underline font-semibold">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>

                <div class="space-y-2 text-sm">
                    <p><span class="font-semibold">Location:</span> <?= htmlspecialchars($room->location) ?></p>
                    <p><span class="font-semibold">Capacity:</span> <?= $room->capacity ?></p>
                    <p><span class="font-semibold">Equipment:</span> <?= htmlspecialchars($room->equipment ?: 'Standard Setup') ?></p>
                </div>

            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ================= ROOM DELETE CONFIRMATION MODAL ================= -->
<div id="confirmDeleteRoomModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50" role="dialog" aria-modal="true" aria-labelledby="confirmDeleteRoomTitle" aria-describedby="confirmDeleteRoomText" aria-hidden="true">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 relative">
        <button type="button"
                onclick="closeDeleteRoomModal()"
                aria-label="Close delete room dialog"
                class="absolute top-3 right-3 text-slate-400 hover:text-slate-600">
            ✕
        </button>

        <h3 id="confirmDeleteRoomTitle" class="text-xl font-bold mb-4">Delete Room</h3>
        <p id="confirmDeleteRoomText" class="text-sm text-slate-700"></p>

        <div class="mt-6 flex justify-end gap-3">
            <button type="button"
                    onclick="closeDeleteRoomModal()"
                    class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100">
                Cancel
            </button>
            <button type="button"
                    onclick="confirmDeleteRoom()"
                    class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">
                Delete Room
            </button>
        </div>
    </div>
</div>

<!-- ================= ADD ROOM MODAL ================= -->
<div id="addRoomModal"
     class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50"
     role="dialog"
     aria-modal="true"
     aria-labelledby="addRoomTitle"
     aria-hidden="true">

    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-8 relative animate-fadeIn">

        <button type="button"
                id="closeAddRoomBtn"
                onclick="closeAddRoomModal()"
                aria-label="Close add room dialog"
                class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
            ✕
        </button>

        <h3 id="addRoomTitle" class="text-xl font-bold mb-6">Create New Room</h3>

        <form action="/admin/rooms/store" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">

            <label for="roomNumber" class="block text-sm font-semibold text-slate-600">
                Room Number
            </label>
            <input type="text"
                   id="roomNumber"
                   name="room_number"
                   placeholder="Room Number"
                   required
                   class="w-full border border-slate-200 focus:ring-2 focus:ring-blue-500 p-3 rounded-xl outline-none">

            <label for="roomLocation" class="block text-sm font-semibold text-slate-600">
                Location
            </label>
            <input type="text"
                   id="roomLocation"
                   name="location"
                   placeholder="Location"
                   required
                   class="w-full border border-slate-200 focus:ring-2 focus:ring-blue-500 p-3 rounded-xl outline-none">

            <label for="roomCapacity" class="block text-sm font-semibold text-slate-600">
                Capacity
            </label>
            <input type="number"
                   id="roomCapacity"
                   name="capacity"
                   placeholder="Capacity"
                   required
                   class="w-full border border-slate-200 focus:ring-2 focus:ring-blue-500 p-3 rounded-xl outline-none">

            <label for="roomEquipment" class="block text-sm font-semibold text-slate-600">
                Equipment
            </label>
            <input type="text"
                   id="roomEquipment"
                   name="equipment"
                   placeholder="Equipment"
                   class="w-full border border-slate-200 focus:ring-2 focus:ring-blue-500 p-3 rounded-xl outline-none">

            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-semibold transition">
                Create Room
            </button>

        </form>
    </div>
</div>

<!-- ================= SLOT MODAL ================= -->
<div id="slotModal"
     class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50"
     role="dialog"
     aria-modal="true"
     aria-labelledby="slotModalTitle"
     aria-hidden="true">

    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-8 relative max-h-[90vh] overflow-y-auto">

        <button type="button"
                id="closeSlotBtn"
                onclick="closeSlotModal()"
                aria-label="Close time slot dialog"
                class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
            ✕
        </button>

        <h3 id="slotModalTitle" class="text-xl font-bold mb-6">Manage Time Slots</h3>

        <div id="existingSlots" class="space-y-3 mb-6"></div>

        <hr class="my-6">

        <form action="/admin/slots/store"
              method="POST"
              id="slotForm"
              class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">

            <input type="hidden" name="room_id" id="slotRoomId">
            <input type="hidden" name="slot_id" id="slotId">

            <label for="slotDayOfWeek" class="block text-sm font-semibold text-slate-600 mb-1">
                Day of Week
            </label>
            <select id="slotDayOfWeek"
                    name="day_of_week"
                    required
                    class="w-full border border-slate-200 p-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="">Select Day</option>
                <option>Monday</option>
                <option>Tuesday</option>
                <option>Wednesday</option>
                <option>Thursday</option>
                <option>Friday</option>
                <option>Saturday</option>
                <option>Sunday</option>
            </select>
        <label for="slotStartTime" class="block text-sm font-semibold text-slate-600 mb-1">
            Start Time
        </label>
        <input type="time"
       id="slotStartTime"
       name="start_time"
       required
       class="w-full border border-slate-200 p-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition">

           <label for="slotEndTime" class="block text-sm font-semibold text-slate-600 mb-1">
            End Time
        </label>
<input type="time"
       id="slotEndTime"
       name="end_time"
       required
       class="w-full border border-slate-200 p-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition">

<p id="slotError"
   class="text-red-500 text-sm font-medium hidden bg-red-50 border border-red-200 rounded-lg px-3 py-2"
   role="alert"
   aria-live="polite">
   End time must be after start time
</p>

<input type="hidden"
       id="slotDeleteCsrfToken"
       value="<?= htmlspecialchars($csrfToken ?? '') ?>">

<button type="submit"
        id="slotSubmitBtn"
        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-semibold transition disabled:opacity-50 disabled:cursor-not-allowed">
    Add Slot
</button>

        </form>
    </div>
</div>

<!-- ================= SLOT DELETE CONFIRMATION MODAL ================= -->
<div id="confirmDeleteSlotModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50" role="dialog" aria-modal="true" aria-labelledby="confirmDeleteSlotTitle" aria-describedby="confirmDeleteSlotText" aria-hidden="true">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 relative">
        <button type="button"
                onclick="closeDeleteSlotModal()"
                aria-label="Close delete slot dialog"
                class="absolute top-3 right-3 text-slate-400 hover:text-slate-600">
            ✕
        </button>

        <h3 id="confirmDeleteSlotTitle" class="text-xl font-bold mb-4">Confirm Deletion</h3>
        <p id="confirmDeleteSlotText" class="text-sm text-slate-700"></p>

        <div class="mt-6 flex justify-end gap-3">
            <button type="button"
                    onclick="closeDeleteSlotModal()"
                    class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100">
                Cancel
            </button>
            <button type="button"
                    onclick="confirmDeleteSlot()"
                    class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">
                Delete Slot
            </button>
        </div>
    </div>
</div>

<script src="/assets/js/admin-rooms-tabs.js"></script>
<script src="/assets/js/admin-rooms.js"></script>

<?php require __DIR__ . '/partials/footer.php'; ?>
