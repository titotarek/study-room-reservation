<?php require __DIR__ . '/partials/header.php'; ?>

<div class="bg-[#F8FAFC] min-h-screen flex flex-col">

    <!-- Navigation -->
    <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between relative">

            <a href="/" class="flex items-center gap-2">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center shadow-md">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253"/>
                    </svg>
                </div>
                <span class="text-lg font-bold text-slate-900 tracking-tight">
                    StudySpace
                </span>
            </a>

            <div class="hidden sm:flex items-center gap-6 font-semibold text-sm text-slate-600">
                <a href="/" class="hover:text-blue-600 transition">Home</a>
                <a href="/rooms" class="hover:text-blue-600 transition">Rooms</a>
            </div>
        </div>
    </nav>

    <!-- Login Section -->
    <main class="flex-grow flex items-center justify-center p-6 relative">

        <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10">
            <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-blue-100/50 blur-[120px]"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-indigo-100/50 blur-[120px]"></div>
        </div>

        <div class="w-full max-w-md">

            <div class="flex flex-col items-center mb-8">
                <div class="bg-blue-600 p-3 rounded-2xl shadow-xl shadow-blue-200 mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/>
                    </svg>
                </div>

                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                    Welcome Back
                </h1>

                <p class="text-slate-500 mt-2 font-medium text-center">
                    Log in to manage your study sessions
                </p>
            </div>

            <div class="bg-white rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.05)] p-8 sm:p-10 border border-slate-100">

                <?php if (isset($_GET['error'])): ?>
                    <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-600 rounded-2xl flex items-center gap-3">
                        <p class="text-xs font-bold uppercase tracking-wide">
                            Invalid credentials
                        </p>
                    </div>
                <?php endif; ?>

                <form id="loginForm" method="POST" action="/login" class="space-y-6">

                    <!-- ✅ CSRF TOKEN -->
                    <input type="hidden"
                           name="csrf_token"
                           value="<?= htmlspecialchars($csrfToken ?? '') ?>">

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400 mb-2 ml-1">
                            Email Address
                        </label>

                        <input name="email"
                               type="email"
                               required
                               placeholder="name@university.edu"
                               class="w-full px-5 py-4 rounded-2xl bg-slate-50 border border-slate-200 outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-600 font-semibold text-slate-700"/>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400 mb-2 ml-1">
                            Password
                        </label>

                        <input name="password"
                               type="password"
                               required
                               placeholder="••••••••"
                               class="w-full px-5 py-4 rounded-2xl bg-slate-50 border border-slate-200 outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-600 font-semibold text-slate-700"/>
                    </div>

                    <button type="submit"
                            id="submitBtn"
                            class="w-full bg-blue-600 text-white py-4 rounded-2xl font-bold hover:bg-blue-700 transition-all transform active:scale-[0.98] shadow-xl shadow-blue-100 flex items-center justify-center gap-2">

                        <span id="btnText">Login to Account</span>

                        <div id="btnIcon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </div>
                    </button>

                </form>
            </div>
        </div>
    </main>

</div>

<?php require __DIR__ . '/partials/footer.php'; ?>

<script src="/assets/js/login.js"></script>
