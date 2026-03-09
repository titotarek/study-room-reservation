<?php require __DIR__ . '/partials/header.php'; ?>

<main class="p-4 sm:p-8 flex-grow bg-[#F8FAFC]">
    <div class="max-w-4xl mx-auto">

        <header class="mb-10">
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                Available Rooms
            </h1>
            <p class="text-slate-500 font-medium">
                Select a space to view the weekly schedule.
            </p>
        </header>

        <div id="room-list" class="grid gap-6">
            <?php foreach ($rooms as $room): ?>
                <?php $roomId = (int) $room->id; ?>

                <div class="bg-white p-6 sm:p-8 rounded-[2rem] shadow border border-white hover:border-blue-100 transition-all">

                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">

                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-blue-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                                </svg>
                            </div>

                            <div>
                                <h2 class="font-black text-xl text-slate-800">
                                    Room <?= htmlspecialchars($room->roomNumber) ?>
                                </h2>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">
                                    Max <?= htmlspecialchars($room->capacity) ?> People
                                </p>
                            </div>
                        </div>

                        <button 
                            type="button"
                            onclick="viewSlots(<?= $roomId ?>)"
                            class="w-full sm:w-auto text-blue-600 bg-blue-50 px-6 py-3 rounded-xl font-bold text-sm hover:bg-blue-600 hover:text-white transition-all">
                            View Available Today
                        </button>
                    </div>

                    <div class="mt-8">

                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-widest">
                                Weekly Schedule
                            </h3>
                            <span class="text-xs text-slate-400">
                                Recurring availability
                            </span>
                        </div>

                        <?php if (!empty($room->weeklySlots)): ?>

                            <?php
                            $order = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
                            $grouped = [];

                            foreach ($room->weeklySlots as $slot) {
                                $grouped[$slot['day_of_week']][] = $slot;
                            }
                            ?>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

                                <?php foreach ($order as $day): ?>
                                    <?php if (!empty($grouped[$day])): ?>

                                        <div class="bg-gradient-to-br from-white to-slate-50 
                                                    border border-slate-100 
                                                    rounded-2xl p-5 
                                                    shadow-sm hover:shadow-md 
                                                    transition-all duration-300">

                                            <div class="flex items-center justify-between mb-4">
                                                <p class="font-bold text-slate-800">
                                                    <?= $day ?>
                                                </p>
                                                <span class="text-[10px] text-slate-400 uppercase tracking-widest">
                                                    <?= count($grouped[$day]) ?> Slots
                                                </span>
                                            </div>

                                            <div class="space-y-3">

                                                <?php foreach ($grouped[$day] as $slot): ?>

                                                    <?php $isTaken = !empty($slot['is_taken']); ?>

                                                    <form action="/reservations/store" method="POST"
                                                        class="flex items-center justify-between rounded-xl px-3 py-2 border transition-all duration-200
                                                        <?= $isTaken
                                                            ? 'bg-gray-200 border-gray-200 opacity-60 pointer-events-none'
                                                            : 'bg-white border-slate-100 hover:border-blue-300 hover:bg-blue-50' ?>">

                                                        <div class="text-sm font-medium text-slate-700">
                                                            <?= substr($slot['start_time'],0,5) ?>
                                                            -
                                                            <?= substr($slot['end_time'],0,5) ?>
                                                        </div>

                                                        <?php if (!$isTaken): ?>

                                                            <input type="hidden" name="room_id" value="<?= $roomId ?>">
                                                            <input type="hidden" name="time_slot_id" value="<?= $slot['id'] ?>">
                                                            <input type="hidden" name="reservation_date" value="<?= date('Y-m-d') ?>">

                                                            <button type="submit"
                                                                class="text-xs font-semibold bg-blue-600 text-white px-3 py-1.5 rounded-lg hover:bg-blue-700 transition-all">
                                                                Book
                                                            </button>

                                                        <?php else: ?>

                                                            <button disabled
                                                                class="text-xs font-semibold bg-gray-400 text-white px-3 py-1.5 rounded-lg cursor-not-allowed">
                                                                Taken
                                                            </button>

                                                        <?php endif; ?>

                                                    </form>

                                                <?php endforeach; ?>

                                            </div>

                                        </div>

                                    <?php endif; ?>
                                <?php endforeach; ?>

                            </div>

                        <?php else: ?>

                            <div class="bg-slate-50 border border-dashed border-slate-200 
                                        rounded-2xl p-6 text-center text-slate-400">
                                No weekly schedule configured.
                            </div>

                        <?php endif; ?>

                    </div>

                </div>

            <?php endforeach; ?>
        </div>

    </div>
</main>

<script src="/assets/js/rooms.js"></script>