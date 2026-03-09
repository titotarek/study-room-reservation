<?php
/**
 * @var \App\ViewModels\RoomViewModel[] $rooms
 */

$title = 'Admin – Manage Rooms';
usort($rooms, fn($a, $b) => strcmp($a->roomNumber, $b->roomNumber));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen flex flex-col text-slate-800">

<!-- ================= HEADER ================= -->
<header class="bg-white/80 backdrop-blur border-b shadow-sm sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
        <h1 class="text-xl font-bold tracking-tight">Admin Panel</h1>
        <a href="/logout" class="text-sm font-semibold text-red-500 hover:text-red-600 transition">
            Logout
        </a>
    </div>

    <div class="border-t">
        <div class="max-w-7xl mx-auto px-6 flex gap-8">
            <a href="/admin/rooms"
               class="py-4 font-semibold border-b-2 border-blue-600 text-blue-600">
                Rooms
            </a>

            <a href="/admin/reservations"
               class="py-4 font-semibold text-slate-500 hover:text-blue-600 transition">
                Reservations
            </a>
        </div>
    </div>
</header>

<!-- ================= MAIN ================= -->
<main class="flex-1 max-w-7xl mx-auto w-full px-6 py-10">

    <div class="flex justify-between items-center mb-10">
        <div>
            <h2 class="text-3xl font-bold">Room Inventory</h2>
            <p class="text-slate-500 mt-1">Configure study spaces and availability.</p>
        </div>

        <button onclick="openAddRoomModal()"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold shadow-md transition">
            + Add New Room
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <?php foreach ($rooms as $room): ?>
            <div class="bg-white rounded-2xl shadow-md p-6 border hover:shadow-lg transition">

                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-xl font-bold"><?= htmlspecialchars($room->roomNumber) ?></h3>

                    <div class="flex gap-4 text-sm">
                        <button onclick="openSlotModal(<?= $room->id ?>)"
                                class="text-blue-600 hover:underline font-semibold">
                            Slots
                        </button>

                        <a href="/admin/rooms/delete?id=<?= $room->id ?>"
                           onclick="return confirm('Delete this room?')"
                           class="text-red-500 hover:underline font-semibold">
                            Delete
                        </a>
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

</main>

<!-- ================= FOOTER ================= -->
<footer class="bg-white border-t py-4 text-center text-sm text-slate-400">
    Admin Panel © <?= date('Y') ?>
</footer>

<!-- ================= ADD ROOM MODAL ================= -->
<div id="addRoomModal"
     class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">

    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-8 relative animate-fadeIn">

        <button onclick="closeAddRoomModal()"
                class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
            ✕
        </button>

        <h3 class="text-xl font-bold mb-6">Create New Room</h3>

        <form action="/admin/rooms/store" method="POST" class="space-y-4">

            <input type="text"
                   name="room_number"
                   placeholder="Room Number"
                   required
                   class="w-full border border-slate-200 focus:ring-2 focus:ring-blue-500 p-3 rounded-xl outline-none">

            <input type="text"
                   name="location"
                   placeholder="Location"
                   required
                   class="w-full border border-slate-200 focus:ring-2 focus:ring-blue-500 p-3 rounded-xl outline-none">

            <input type="number"
                   name="capacity"
                   placeholder="Capacity"
                   required
                   class="w-full border border-slate-200 focus:ring-2 focus:ring-blue-500 p-3 rounded-xl outline-none">

            <input type="text"
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
     class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">

    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-8 relative max-h-[90vh] overflow-y-auto">

        <button onclick="closeSlotModal()"
                class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
            ✕
        </button>

        <h3 class="text-xl font-bold mb-6">Manage Time Slots</h3>

        <div id="existingSlots" class="space-y-3 mb-6"></div>

        <hr class="my-6">

        <form action="/admin/slots/store"
              method="POST"
              id="slotForm"
              class="space-y-4">

            <input type="hidden" name="room_id" id="slotRoomId">
            <input type="hidden" name="slot_id" id="slotId">

            <select name="day_of_week"
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
        <label class="block text-sm font-semibold text-slate-600 mb-1">
            Start Time
        </label>
        <input type="time"
       name="start_time"
       required
       class="w-full border border-slate-200 p-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition">

           <label class="block text-sm font-semibold text-slate-600 mb-1">
            End Time
        </label>
<input type="time"
       name="end_time"
       required
       class="w-full border border-slate-200 p-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition">

<p id="slotError"
   class="text-red-500 text-sm font-medium hidden bg-red-50 border border-red-200 rounded-lg px-3 py-2">
   End time must be after start time
</p>

<button type="submit"
        id="slotSubmitBtn"
        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-semibold transition disabled:opacity-50 disabled:cursor-not-allowed">
    Add Slot
</button>

        </form>
    </div>
</div>

<script src="/assets/js/admin-rooms-tabs.js"></script>
<script src="/assets/js/admin-rooms.js"></script>

</body>
</html>