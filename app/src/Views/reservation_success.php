<?php require __DIR__ . '/partials/header.php'; ?>

<div class="bg-[#F8FAFC] flex items-center justify-center min-h-screen p-4 sm:p-6">

    <div class="fixed inset-0 overflow-hidden -z-10">
        <div class="absolute top-[-10%] right-[-10%] w-[50%] h-[50%] rounded-full bg-emerald-100/40 blur-[120px]"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[50%] h-[50%] rounded-full bg-blue-100/40 blur-[120px]"></div>
    </div>

    <div class="bg-white rounded-[2.5rem] sm:rounded-[3.5rem] shadow-[0_20px_60px_rgba(0,0,0,0.05)] max-w-lg w-full overflow-hidden border border-white relative">

        <!-- Success Header -->
        <div class="bg-emerald-500 p-8 sm:p-12 text-white text-center relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16 blur-2xl"></div>

            <div class="relative z-10">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-[2rem] mb-6 backdrop-blur-md border border-white/30 animate-success">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                </div>

                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">
                    Booking Confirmed
                </h1>
                <p class="text-emerald-100 mt-2 font-medium opacity-90">
                    Your study spot is ready for you!
                </p>
            </div>
        </div>

        <!-- Details Section -->
        <div class="p-8 sm:p-10 space-y-6">

            <div class="bg-slate-50/50 rounded-[2rem] p-6 sm:p-8 space-y-5 border border-slate-100">

                <div class="flex justify-between items-center pb-4 border-b border-slate-200/50">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                        Room Number
                    </span>
                    <span class="font-extrabold text-slate-900 text-base sm:text-lg">
                        Room <?= htmlspecialchars($reservation['room_number'] ?? 'N/A') ?>
                    </span>
                </div>

                <div class="flex justify-between items-center pb-4 border-b border-slate-200/50">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                        Scheduled Date
                    </span>
                    <span class="font-extrabold text-slate-900 text-base sm:text-lg">
                        <?php
                        $dateValue = $reservation['date'] ?? $reservation['reservation_date'] ?? 'now';
                        echo date('M d, Y', strtotime($dateValue));
                        ?>
                    </span>
                </div>

                <div class="flex justify-between items-center pb-4 border-b border-slate-200/50">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                        Time Slot
                    </span>
                    <span class="font-extrabold text-blue-600 text-base sm:text-lg">
                        <?= htmlspecialchars($reservation['display_time'] ?? 'Session Confirmed') ?>
                    </span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                        Group Size
                    </span>
                    <span class="font-extrabold text-slate-900 text-base sm:text-lg">
                        <?= htmlspecialchars($reservation['num_people'] ?? '1') ?> People
                    </span>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex flex-col gap-3 sm:pt-4">
                <a href="/my-reservations"
                   class="w-full bg-slate-900 text-white py-4 rounded-2xl font-bold text-center hover:bg-slate-800 transition">
                    Go to My Bookings
                </a>

                <a href="/rooms"
                   class="w-full bg-white text-slate-500 py-4 rounded-2xl font-bold text-center border-2 border-slate-100 hover:bg-slate-50 transition">
                    Book Another Room
                </a>
            </div>

            <p class="text-center text-xs text-slate-400 font-medium">
                A confirmation email has been sent to your student inbox.
            </p>

        </div>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>

<style>
@keyframes scaleIn {
    0% { transform: scale(0); opacity: 0; }
    60% { transform: scale(1.1); }
    100% { transform: scale(1); opacity: 1; }
}
.animate-success {
    animation: scaleIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
}
</style>
