<?php require __DIR__ . '/partials/header.php'; ?>

<div class="bg-[#F8FAFC] min-h-screen text-slate-800">

<main class="p-4 md:p-8">
<div class="max-w-5xl mx-auto">

<header class="mb-8">
<h1 class="text-3xl md:text-4xl font-extrabold tracking-tight">
My Reservations
</h1>
<p class="text-slate-500 mt-2 font-medium">
Manage your upcoming study sessions.
</p>
</header>

<?php if (empty($reservations)): ?>

<div class="text-center py-20 bg-white rounded-3xl shadow-sm border border-slate-100">
<p class="text-slate-500 text-lg font-semibold mb-4">
No active bookings
</p>

<a href="/rooms"
class="inline-block bg-blue-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-700 transition shadow-md">
Start Booking
</a>
</div>

<?php else: ?>

<!-- Mobile Cards -->
<div class="sm:hidden space-y-4">

<?php foreach ($reservations as $res): ?>

<?php
$formattedDate = date('l d M Y', strtotime($res->date));
?>

<div class="bg-white border border-slate-100 p-5 rounded-3xl shadow-sm">

<div class="font-black text-lg">
Room <?= htmlspecialchars($res->room) ?>
</div>

<div class="text-xs text-blue-600 font-bold">
<?= $formattedDate ?>
</div>

<div class="flex justify-between items-center mt-4">

<span class="text-sm font-bold">
<?= htmlspecialchars($res->numPeople) ?> People
</span>

<span class="bg-blue-600 text-white px-3 py-1 rounded-lg text-xs font-bold">
<?= htmlspecialchars($res->time) ?>
</span>

</div>

<div class="flex gap-4 mt-5 text-sm font-bold">

<a href="/reservations/edit?id=<?= $res->id ?>"
class="text-blue-600 hover:underline">
Edit
</a>

<button onclick="openCancelModal(<?= $res->id ?>)"
class="text-red-600 hover:underline">
Cancel
</button>

</div>

</div>

<?php endforeach; ?>

</div>

<!-- Desktop Table -->
<div class="hidden sm:block overflow-x-auto bg-white rounded-3xl shadow-sm border border-slate-100">

<table class="w-full text-left">

<thead>
<tr class="text-slate-400 text-xs uppercase border-b">
<th class="p-6">Room</th>
<th class="p-6">Date</th>
<th class="p-6">Group</th>
<th class="p-6">Time</th>
<th class="p-6 text-right">Actions</th>
</tr>
</thead>

<tbody>

<?php foreach ($reservations as $res): ?>

<?php
$formattedDate = date('l d M Y', strtotime($res->date));
?>

<tr class="border-b hover:bg-slate-50 transition">

<td class="p-6 font-bold">
Room <?= htmlspecialchars($res->room) ?>
</td>

<td class="p-6">
<?= $formattedDate ?>
</td>

<td class="p-6">
<?= htmlspecialchars($res->numPeople) ?>
</td>

<td class="p-6 font-semibold">
<?= htmlspecialchars($res->time) ?>
</td>

<td class="p-6 text-right text-sm font-bold">

<a href="/reservations/edit?id=<?= $res->id ?>"
class="text-blue-600 mr-4 hover:underline">
Edit
</a>

<button onclick="openCancelModal(<?= $res->id ?>)"
class="text-red-600 hover:underline">
Cancel
</button>

</td>

</tr>

<?php endforeach; ?>

</tbody>
</table>

</div>

<?php endif; ?>

</div>
</main>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>

<script src="/assets/js/my-reservations.js"></script>