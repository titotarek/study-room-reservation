<?php require __DIR__ . '/partials/header.php'; ?>

<div class="bg-[#F8FAFC] min-h-screen flex flex-col">

    <!-- Navigation -->
    <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100" aria-label="Homepage navigation">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between relative">

            <!-- Logo -->
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-200">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <span class="text-xl font-extrabold tracking-tight text-slate-900">
                    StudySpace
                </span>
            </div>

            <!-- Mobile Toggle -->
            <label for="menu-toggle"
                   aria-label="Toggle navigation menu"
                   class="sm:hidden cursor-pointer p-2 text-slate-600 hover:bg-slate-50 rounded-lg transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16m-7 6h7"/>
                </svg>
            </label>
            <input type="checkbox" id="menu-toggle" class="hidden" aria-hidden="true">

            <!-- Desktop Menu -->
            <div class="hidden sm:flex items-center gap-8 font-semibold text-sm text-slate-600">
                <a href="/rooms" class="hover:text-blue-600 transition">Browse Rooms</a>
                <a href="/my-reservations" class="hover:text-blue-600 transition">My Bookings</a>
                <a href="/login"
                   class="bg-blue-600 text-white px-6 py-2.5 rounded-xl hover:bg-blue-700 transition shadow-md shadow-blue-100">
                    Login
                </a>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu"
                 class="absolute top-20 left-0 w-full bg-white border-b border-slate-100 sm:hidden overflow-hidden max-h-0 opacity-0 transition-all duration-300 ease-in-out px-6 shadow-xl"
                 hidden>
                <div class="flex flex-col gap-4 font-bold text-slate-700 pb-4">
                    <a href="/rooms" class="py-2">Browse Rooms</a>
                    <a href="/my-reservations" class="py-2">My Bookings</a>
                    <a href="/login"
                       class="py-3 text-center bg-blue-600 text-white rounded-xl">
                        Login to Book
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <main class="flex-grow flex items-center justify-center p-6 sm:p-12">
        <div class="bg-white p-8 sm:p-12 rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.04)] max-w-lg w-full text-center border border-slate-50 relative overflow-hidden">

            <div class="absolute -top-24 -left-24 w-48 h-48 bg-blue-50 rounded-full blur-3xl opacity-50"></div>

            <div class="relative z-10">
                <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900 mb-4 tracking-tight leading-tight">
                    Focus Better, <span class="text-blue-600">Book Faster.</span>
                </h1>

                <p class="text-slate-500 mb-10 text-lg font-medium">
                    Secure your quiet study spot in the library with our easy-to-use booking system.
                </p>

                <div class="flex flex-col gap-4">
                    <a href="/rooms"
                       class="group flex items-center justify-center gap-2 w-full bg-blue-600 text-white py-4 rounded-2xl font-bold text-lg hover:bg-blue-700 transition-all shadow-xl shadow-blue-100 transform active:scale-[0.98]">
                        Find a Room
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>

                    <a href="/login"
                       class="block w-full border-2 border-slate-100 text-slate-600 py-4 rounded-2xl font-bold hover:bg-slate-50 hover:border-slate-200 transition">
                        Member Login
                    </a>
                </div>

                <p class="mt-8 text-[11px] font-bold uppercase tracking-widest text-slate-400">
                    Trusted by 2,000+ Students
                </p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <!-- <footer class="p-8 text-center text-slate-400 text-sm">
        &copy; <?= date('Y') ?> StudySpace Reservation System
    </footer> -->

</div>

<?php require __DIR__ . '/partials/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('menu-toggle');
    const menu = document.getElementById('mobile-menu');

    if (!toggle || !menu) return;

    function syncMenu() {
        const expanded = toggle.checked;
        menu.hidden = !expanded;
        menu.classList.toggle('max-h-0', !expanded);
        menu.classList.toggle('opacity-0', !expanded);
        menu.classList.toggle('max-h-96', expanded);
        menu.classList.toggle('opacity-100', expanded);
    }

    syncMenu();
    toggle.addEventListener('change', syncMenu);
});
</script>
