<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Study Room Reservation') ?></title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/main.css">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="min-h-screen flex flex-col text-slate-800">

<nav class="sticky top-0 z-50 px-4 pt-4" aria-label="Primary navigation">
    <div class="page-shell glass-panel rounded-[2rem] px-5 sm:px-7 py-4">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <a href="/" class="flex items-center gap-3">
                <div class="h-11 w-11 rounded-2xl bg-gradient-to-br from-emerald-700 via-emerald-600 to-amber-500 text-white flex items-center justify-center shadow-lg shadow-emerald-900/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div>
                    <p class="text-lg font-black tracking-tight text-slate-900">StudySpace</p>
                    <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">Room Reservation</p>
                </div>
            </a>

            <div class="flex flex-wrap items-center gap-2 text-sm font-semibold text-slate-600">
                <?php if (!empty($_SESSION['user'])): ?>
                    <a href="/rooms" class="rounded-full px-4 py-2 hover:bg-white/90 hover:text-emerald-700">Rooms</a>
                    <a href="/my-reservations" class="rounded-full px-4 py-2 hover:bg-white/90 hover:text-emerald-700">My Bookings</a>

                    <?php if (($_SESSION['user']['role'] ?? '') === 'admin'): ?>
                        <a href="/admin/rooms" class="rounded-full px-4 py-2 hover:bg-white/90 hover:text-emerald-700">Admin</a>
                    <?php endif; ?>

                    <a href="/logout" class="rounded-full bg-white/90 px-4 py-2 text-rose-600 hover:bg-rose-50">Logout</a>
                <?php else: ?>
                    <a href="/rooms" class="rounded-full px-4 py-2 hover:bg-white/90 hover:text-emerald-700">Browse Rooms</a>
                    <a href="/login" class="rounded-full bg-gradient-to-r from-emerald-700 to-emerald-600 px-5 py-2.5 text-white shadow-lg shadow-emerald-900/20 hover:from-emerald-800 hover:to-emerald-700">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<main class="flex-grow px-4 py-6 sm:px-6 sm:py-8">
