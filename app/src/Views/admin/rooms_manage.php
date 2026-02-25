<?php
/**
 * @var \App\ViewModels\RoomViewModel[] $rooms
 */

$title = 'Admin – Manage Rooms';

// Sort rooms by room number for a cleaner inventory view
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

<body class="bg-[#F8FAFC] min-h-screen flex flex-col">

<!-- =========================
     ADMIN HEADER (Clean)
========================= -->
<header class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
        <h1 class="text-xl font-black text-slate-900">
            Admin Panel
        </h1>

        <a href="/logout"
           class="text-sm font-bold text-red-600 hover:underline">
            Logout
        </a>
    </div>
</header>

<main class="flex-1 max-w-7xl mx-auto w-full">

<div class="p-6 md:p-8 lg:p-12">

    <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-12">
        <div>
            <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900">
                Room Inventory
            </h1>
            <p class="text-slate-500 font-medium mt-1">
                Add rooms and manage availability.
            </p>
        </div>

        <button
            type="button"
            id="openAddRoomBtn"
            class="w-full sm:w-auto bg-blue-600 text-white px-8 py-4 rounded-2xl font-bold hover:bg-blue-700 transition shadow-xl">
            + Add New Room
        </button>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 lg:gap-8">
        <?php if (empty($rooms)): ?>
            <div class="col-span-full text-center text-slate-400 italic py-12">
                No rooms available.
            </div>
        <?php else: ?>
            <?php foreach ($rooms as $room): ?>
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-white hover:border-blue-100 transition">

                    <div class="flex justify-between items-start mb-6">
                        <div class="bg-blue-50 text-blue-600 px-5 py-3 rounded-2xl font-black text-2xl">
                            <?= htmlspecialchars($room->roomNumber) ?>
                        </div>

                        <div class="flex gap-3">
                            <button
                                type="button"
                                class="openSlotBtn p-2 text-blue-500 hover:bg-blue-50 rounded-xl"
                                data-room="<?= $room->id ?>">
                                🕒
                            </button>

                            <a href="/admin/rooms/delete?id=<?= $room->id ?>"
                               onclick="return confirm('Are you sure you want to delete this room?')"
                               class="p-2 text-red-500 hover:bg-red-50 rounded-xl">
                                🗑
                            </a>
                        </div>
                    </div>

                    <p class="text-slate-600 font-semibold italic text-sm">
                        <?= htmlspecialchars($room->location) ?>
                        • Capacity: <?= htmlspecialchars($room->capacity) ?> People
                    </p>

                    <div class="pt-4 border-t mt-4">
                        <span class="text-xs font-bold text-slate-400 uppercase">
                            Equipment
                        </span>
                        <p class="text-sm text-blue-600 font-bold mt-1">
                            <?= htmlspecialchars($room->equipment ?: 'Standard Desk/Chairs') ?>
                        </p>
                    </div>

                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

</main>

<!-- =========================
     ADMIN FOOTER
========================= -->
<footer class="bg-white border-t mt-auto">
    <div class="max-w-7xl mx-auto px-6 py-4 text-sm text-slate-400 text-center">
        Admin Panel © <?= date('Y') ?>
    </div>
</footer>

<!-- =========================
     MODALS (UNCHANGED)
========================= -->

<div id="addRoomModal"
     class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">

    <div class="bg-white rounded-3xl p-8 w-full max-w-lg shadow-2xl relative">
        <button id="closeAddRoomBtn"
                type="button"
                class="absolute top-4 right-4 text-slate-400 hover:text-slate-700 text-xl">
            ✕
        </button>

        <h2 class="text-2xl font-extrabold mb-6">Add New Room</h2>

        <form action="/admin/rooms/store" method="POST" class="space-y-4">
            <input type="text" name="room_number" required placeholder="Room Number (e.g. 303)"
                   class="w-full p-4 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none">

            <input type="text" name="location" required placeholder="Location"
                   class="w-full p-4 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none">

            <input type="number" name="capacity" min="1" required placeholder="Capacity"
                   class="w-full p-4 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none">

            <input type="text" name="equipment" placeholder="Equipment (Optional)"
                   class="w-full p-4 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none">

            <button type="submit"
                    class="w-full bg-blue-600 text-white py-4 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg mt-2">
                Create Room
            </button>
        </form>
    </div>
</div>

<div id="slotModal"
     class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">

    <div class="bg-white rounded-3xl p-8 w-full max-w-lg shadow-2xl relative">
        <button id="closeSlotBtn"
                type="button"
                class="absolute top-4 right-4 text-slate-400 hover:text-slate-700 text-xl">
            ✕
        </button>

        <h2 class="text-xl font-bold">Time Slot Management</h2>
        <p class="text-slate-500 text-sm mt-2 mb-4">
            Manage availability for this specific room.
        </p>
        <div id="slotContent">
            <p class="text-slate-400 italic">No slots configured yet.</p>
        </div>
    </div>
</div>

<script src="/assets/js/admin-rooms.js"></script>

</body>
</html>
