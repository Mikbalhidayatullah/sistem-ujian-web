<x-layouts.app :title="'Masuk | Sistem Ujian'" variant="light">
    <section class="mx-auto max-w-5xl">
        <div class="grid gap-6 lg:grid-cols-[1.05fr_0.95fr]">
            <div class="dashboard-card p-6 sm:p-7">
                <p class="dashboard-kicker">Akses Dashboard</p>
                <h1 class="dashboard-section-title mt-3 text-3xl sm:text-4xl">
                    Login admin dan guru
                </h1>
                

                <div class="mt-6 grid gap-4">
                    <div class="dashboard-muted-card p-5">
                        <p class="dashboard-kicker">Hak akses</p>
                        <h2 class="mt-2 text-xl font-bold text-slate-900">Satu halaman masuk untuk admin dan guru</h2>
                        <p class="mt-3 text-sm leading-7 text-slate-500">
                            Admin bisa mengelola akun guru dan memantau keseluruhan data ujian. Guru bisa membuat mata pelajaran, menyusun ujian, dan melihat hasil siswa dari dashboard masing-masing.
                        </p>
                    </div>
                                    
                </div>
            </div>

            <div class="dashboard-card p-6 sm:p-7">
                <p class="dashboard-kicker">Masuk Sekarang</p>
                <h2 class="dashboard-section-title mt-3 text-3xl sm:text-4xl">
                    Akses Login
                </h2>
                

                <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-5">
                    @csrf
                    <div class="space-y-2">
                        <label for="email" class="text-sm font-medium text-slate-600">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" class="dashboard-input" required>
                    </div>
                    <div class="space-y-2">
                        <label for="password" class="text-sm font-medium text-slate-600">Password</label>
                        <div class="password-field" data-password-field>
                            <input id="password" name="password" type="password" class="dashboard-input password-input" required>
                            <button type="button" class="password-toggle text-slate-500 hover:text-slate-700 hover:bg-slate-100" data-password-toggle aria-controls="password" aria-label="Lihat password">
                                <span class="sr-only">Lihat password</span>
                            </button>
                        </div>
                    </div>
                    <label class="flex items-center gap-3 text-sm text-slate-500">
                        <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 bg-white text-sky-500">
                        Ingat saya
                    </label>
                    <button type="submit" class="dashboard-button-primary w-full">
                        Masuk
                    </button>
                    <a href="{{ route('home') }}" class="dashboard-button-return w-full sm:w-full">
                        Kembali ke halaman siswa
                    </a>
                </form>
                              
            </div>
        </div>
    </section>
</x-layouts.app>
