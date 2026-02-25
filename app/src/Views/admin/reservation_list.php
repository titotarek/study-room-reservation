<?php
/**
 * @var \App\ViewModels\AdminReservationViewModel[] $reservations
 */

$title = 'Admin – Reservations';

/* =========================
   SAFE SORTING
========================= */
usort($reservations, function ($a, $b) {
    $dateCompare = strtotime($a->date) <=> strtotime($b->date);
    return $dateCompare !== 0
        ? $dateCompare
        : strcmp($a->time, $b->time);
});
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

<div class="p-4 md:p-8">

    <!-- =========================
         STATS DASHBOARD
    ========================== -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow border">
            <div class="text-sm text-slate-400">Total Reservations</div>
            <div class="text-3xl font-black">
                <?= count($reservations) ?>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow border">
            <div class="text-sm text-slate-400">Today</div>
            <div class="text-3xl font-black">
                <?= count(array_filter($reservations, fn($r) => $r->date === date('Y-m-d'))) ?>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow border">
            <div class="text-sm text-slate-400">Future Bookings</div>
            <div class="text-3xl font-black">
                <?= count(array_filter($reservations, fn($r) => strtotime($r->date) > strtotime(date('Y-m-d')))) ?>
            </div>
        </div>
    </div>

    <!-- =========================
         CONFIRM MODAL
    ========================== -->
    <div id="customConfirmModal"
         class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white w-full max-w-md rounded-[2.5rem] p-8 shadow-2xl text-center">
            <h3 class="text-2xl font-black text-slate-900 mb-3">Cancel Reservation?</h3>
            <p class="text-slate-500 mb-8">This action cannot be undone.</p>

            <div class="flex flex-col gap-3">
                <button id="modalConfirmBtn"
                        class="bg-red-600 text-white py-4 rounded-2xl font-bold hover:bg-red-700 transition">
                    Yes, Confirm Delete
                </button>
                <button onclick="hideDeleteModal()"
                        class="bg-slate-100 py-4 rounded-2xl font-bold hover:bg-slate-200 transition">
                    No, Keep it
                </button>
            </div>
        </div>
    </div>

    <!-- =========================
         HEADER
    ========================== -->
    <header class="mb-8">
        <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">
            System Reservations
        </h1>
        <p class="text-slate-500 mt-2">
            Manage all active study space bookings.
        </p>
    </header>

    <!-- =========================
         DESKTOP TABLE
    ========================== -->
    <div class="hidden lg:block bg-white rounded-[2rem] shadow border overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 text-xs uppercase text-slate-400">
            <tr>
                <th class="p-6">Student</th>
                <th class="p-6">Room</th>
                <th class="p-6">Date</th>
                <th class="p-6">Time</th>
                <th class="p-6">Group</th>
                <th class="p-6 text-center">Action</th>
            </tr>
            </thead>

            <tbody class="divide-y">
            <?php if (empty($reservations)): ?>
                <tr>
                    <td colspan="6" class="p-12 text-center text-slate-400">
                        No active reservations found.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($reservations as $res): ?>
                    <tr class="hover:bg-slate-50 transition">
                        <td class="p-6">
                            <div class="font-bold"><?= htmlspecialchars($res->studentName) ?></div>
                            <div class="text-xs text-slate-400"><?= htmlspecialchars($res->studentEmail) ?></div>
                        </td>

                        <td class="p-6 font-bold">
                            Room <?= htmlspecialchars($res->room) ?>
                        </td>

                        <td class="p-6">
                            <?= htmlspecialchars($res->date) ?>
                        </td>

                        <td class="p-6 font-bold">
                            <?= htmlspecialchars($res->time) ?>
                        </td>

                        <td class="p-6 font-bold text-blue-600">
                            <?= htmlspecialchars($res->numPeople ?? $res->num_people ?? 1) ?> pax
                        </td>

                        <td class="p-6 text-center">
                            <form id="delete-form-<?= $res->id ?>"
                                  action="/admin/reservations/delete"
                                  method="POST">
                                <input type="hidden" name="id" value="<?= $res->id ?>">
                                <button type="button"
                                        onclick="showDeleteModal(<?= $res->id ?>)"
                                        class="bg-red-50 text-red-600 px-4 py-2 rounded-xl text-xs font-bold hover:bg-red-600 hover:text-white transition">
                                    Cancel
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
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

<script src="/assets/js/admin-reservations.js"></script>

</body>
</html>
