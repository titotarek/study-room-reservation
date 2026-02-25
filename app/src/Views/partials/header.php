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
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F8FAFC; }
    </style>
</head>
<body class="min-h-screen flex flex-col">

<nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100 px-6">
    <div class="max-w-5xl mx-auto h-16 flex items-center justify-between">
        <a href="/" class="flex items-center gap-2 font-bold text-slate-900">
            <span class="text-lg">StudySpace</span>
        </a>

        <div class="flex items-center gap-6 text-sm font-semibold text-slate-600">
            <?php if (!empty($_SESSION['user'])): ?>
                <a href="/rooms" class="hover:text-blue-600">Rooms</a>
                <a href="/my-reservations" class="hover:text-blue-600">My Bookings</a>

                <?php if (($_SESSION['user']['role'] ?? '') === 'admin'): ?>
                    <a href="/admin/rooms" class="hover:text-blue-600">Admin</a>
                <?php endif; ?>

                <a href="/logout" class="text-red-500 hover:text-red-600">Logout</a>
            <?php else: ?>
                <a href="/login" class="hover:text-blue-600">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<main class="flex-grow p-4 sm:p-8">
