<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title ?? 'Admin – Study Room Reservation') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body class="bg-[#F8FAFC] min-h-screen flex flex-col">
<header class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto w-full px-6 py-4 flex justify-between items-center">
        <h1 class="text-xl font-black text-slate-900">Admin Panel</h1>

        <a href="/logout" class="text-sm font-bold text-red-600 hover:underline">Logout</a>
    </div>

    <div class="border-t">
        <div class="max-w-7xl mx-auto w-full px-6 flex gap-8">
            <a href="/admin/rooms" class="py-4 font-semibold border-b-2 border-blue-600 text-blue-600">Rooms</a>
            <a href="/admin/reservations" class="py-4 font-semibold text-slate-500 hover:text-blue-600 transition">Reservations</a>
        </div>
    </div>
</header>
<main class="flex-1 max-w-7xl mx-auto w-full px-6 py-10">
