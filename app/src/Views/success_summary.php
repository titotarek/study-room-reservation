<?php require __DIR__ . '/partials/header.php'; ?>

<main class="bg-[#F8FAFC] flex items-center justify-center min-h-screen p-4 sm:p-6">

    <!-- Background Blobs -->
    <div class="fixed inset-0 overflow-hidden -z-10">
        <div class="absolute top-[-10%] right-[-10%] w-[60%] h-[60%] rounded-full bg-emerald-100/50 blur-[100px]"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[60%] h-[60%] rounded-full bg-blue-100/50 blur-[100px]"></div>
    </div>

    <div class="bg-white rounded-[2.5rem] sm:rounded-[3.5rem] shadow-[0_25px_50px_-12px_rgba(0,0,0,0.08)] max-w-lg w-full overflow-hidden border border-white">

        <!-- Success Header -->
        <div class="bg-emerald-500 p-8 sm:p-12 text-white text-center relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16 blur-2xl"></div>

            <div class="relative z-10">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-[2rem] mb-6 backdrop-blur-md border border-white/30 animate-success">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="3"
                              d="M5 13l4 4L19 7" />
                    </svg>
                </div>

                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">
                    Confirmed!
                </h1>

                <p class="text-emerald-100 mt-2 font-medium opacity-90">
                    Your study spot is officially locked in.
                </p>
            </div>
        </div>

        <!-- Reservation Details -->
        <div class="p-8 sm:p-12 space-y-8">

            <div class="bg-slate-50/50 rounded-[2rem] p-6 sm:p-8 border border-slate-100 space-y-5">

                <!-- Room -->
                <div class="flex justify-between items-center pb-4 border-b border-slate-200/50">
                    <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                        Room
                    </span>
                    <span class="font-extrabold text-slate-900 text-base sm:text-lg">
                        Room <?= htmlspecialchars($reservation['room_number'] ?? 'N/A') ?>
                    </span>
                </div>

                <!-- Date -->
                <div class="flex justify-between items-center pb-4 border-b border-slate-200/50">
                    <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                        Date
                    </span>
                    <span class="font-extrabold text-slate-900 text-base sm:text-lg">
                        <?php
                        $dateValue = $reservation['date']
                            ?? $reservation['reservation_date']
                            ?? null;

                        echo $dateValue
                            ? date('F j, Y', strtotime($dateValue))
                            : 'Date Pending';
                        ?>
                    </span>
                </div>

                <!-- Time -->
                <div class="flex justify-between items-center pb-4 border-b border-slate-200/50">
                    <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                        Time Slot
                    </span>
                    <span class="font-extrabold text-blue-600 text-base sm:text-lg">
                        <?php
                        if (isset($reservation['display_time'])) {
                            echo htmlspecialchars($reservation['display_time']);
                        } elseif (isset($reservation['start_time'])) {
                            echo substr($reservation['start_time'], 0, 5)
                                . ' - '
                                . substr($reservation['end_time'], 0, 5);
                        } else {
                            echo 'Session Confirmed';
                        }
                        ?>
                    </span>
                </div>

                <!-- Group -->
                <div class="flex justify-between items-center">
                    <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                        Group
                    </span>
                    <span class="font-extrabold text-slate-900 text-base sm:text-lg">
                        <?= htmlspecialchars($reservation['num_people'] ?? '1') ?> People
                    </span>
                </div>

            </div>

            <!-- Actions -->
            <div class="flex flex-col gap-3">
                <a href="/my-reservations"
                   class="w-full bg-slate-900 text-white py-4 sm:py-5 rounded-2xl font-bold text-center hover:bg-slate-800 transition-all shadow-xl shadow-slate-200 active:scale-[0.98]">
                    View My Bookings
                </a>

                <a href="/reservations/create"
                   class="w-full bg-white text-slate-500 py-4 sm:py-5 rounded-2xl font-bold text-center border-2 border-slate-100 hover:bg-slate-50 transition-all active:scale-[0.98]">
                    Book Another Room
                </a>
            </div>

            <p class="text-center text-[11px] text-slate-400 font-medium">
                You can cancel or modify this booking anytime in your dashboard.
            </p>

        </div>
    </div>

</main>

<?php require __DIR__ . '/partials/footer.php'; ?>
