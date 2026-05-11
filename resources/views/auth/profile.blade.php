<x-layouts.dashboard :title="'Profil Akun | Sistem Ujian'">
    <section class="space-y-8">
        <div class="grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
            <div class="dashboard-card p-6 sm:p-7">
                <p class="dashboard-kicker">Profile</p>
                <h1 class="dashboard-section-title mt-3 text-3xl sm:text-4xl">
                    Profil akun dashboard
                </h1>
                <p class="dashboard-copy mt-4 max-w-2xl">
                    Halaman ini dipakai untuk melihat identitas akun yang sedang aktif dan mengganti password sendiri tanpa bantuan admin lain.
                </p>

                <div class="mt-6 space-y-3">
                    <div class="dashboard-muted-card p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Nama</p>
                        <p class="mt-2 text-base font-bold text-slate-900">{{ $user->name }}</p>
                    </div>
                    <div class="dashboard-muted-card p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Email</p>
                        <p class="mt-2 text-base font-bold text-slate-900">{{ $user->email }}</p>
                    </div>
                    <div class="dashboard-muted-card p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Peran</p>
                        <p class="mt-2 text-base font-bold text-slate-900">{{ $user->isAdmin() ? 'Admin' : 'Guru' }}</p>
                    </div>
                </div>
            </div>

            <div class="dashboard-card p-6 sm:p-7">
                <p class="dashboard-kicker">Security</p>
                <h2 class="dashboard-section-title mt-2">Perbarui password</h2>
                <p class="dashboard-copy mt-3">
                    Isi password lama untuk verifikasi, lalu masukkan password baru yang ingin dipakai.
                </p>

                <form method="POST" action="{{ route('profile.password.update') }}" class="mt-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-600">Password lama</label>
                        <div class="password-field" data-password-field>
                            <input type="password" name="current_password" class="dashboard-input password-input" required>
                            <button type="button" class="password-toggle text-slate-500 hover:text-slate-700 hover:bg-slate-100" data-password-toggle aria-label="Lihat password">
                                <span class="sr-only">Lihat password</span>
                            </button>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-600">Password baru</label>
                            <div class="password-field" data-password-field>
                                <input type="password" name="password" class="dashboard-input password-input" required>
                                <button type="button" class="password-toggle text-slate-500 hover:text-slate-700 hover:bg-slate-100" data-password-toggle aria-label="Lihat password">
                                    <span class="sr-only">Lihat password</span>
                                </button>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-600">Konfirmasi password baru</label>
                            <div class="password-field" data-password-field>
                                <input type="password" name="password_confirmation" class="dashboard-input password-input" required>
                                <button type="button" class="password-toggle text-slate-500 hover:text-slate-700 hover:bg-slate-100" data-password-toggle aria-label="Lihat password">
                                    <span class="sr-only">Lihat password</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 border-t border-slate-200 pt-4 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-xs leading-6 text-slate-500">
                            Gunakan password baru minimal 8 karakter dan jangan sama dengan password lama.
                        </p>

                        <button type="submit" class="dashboard-button-primary w-full sm:w-auto">
                            Simpan password baru
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-layouts.dashboard>
