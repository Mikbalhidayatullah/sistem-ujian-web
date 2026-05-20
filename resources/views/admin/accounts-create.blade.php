<x-layouts.dashboard :title="'Tambahkan Akun | Sistem Ujian'">
    <section class="space-y-8">
        <div class="dashboard-card p-6 sm:p-7">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="max-w-3xl">
                    <p class="dashboard-kicker">Create Account</p>
                    <h1 class="dashboard-section-title mt-3 text-3xl sm:text-4xl">
                        Tambahkan akun baru dari halaman terpisah yang lebih fokus.
                    </h1>
                    <p class="dashboard-copy mt-4 max-w-2xl">
                        Gunakan halaman ini untuk membuat akun baru tanpa bercampur dengan tabel pengelolaan guru.
                    </p>
                </div>
                <span class="dashboard-pill">Akun baru</span>
            </div>
        </div>

        <div class="dashboard-card p-6 sm:p-7">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="dashboard-kicker">Account Form</p>
                    <h2 class="dashboard-section-title mt-2">Form tambah akun</h2>
                    <p class="dashboard-copy mt-3">
                        Pilih jenis akun yang ingin dibuat, lalu isi data dasarnya dengan lengkap.
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.accounts.store') }}" class="mt-6 space-y-4">
                @csrf

                <div class="grid gap-4 lg:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-600">Nama akun</label>
                        <input type="text" name="name" class="dashboard-input" value="{{ old('name') }}" required>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-600">Email</label>
                        <input type="email" name="email" class="dashboard-input" value="{{ old('email') }}" required>
                    </div>
                </div>

                <div class="grid gap-4 lg:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-600">Jenis akun</label>
                        <select name="role" class="dashboard-input" required>
                            <option value="{{ \App\Models\User::ROLE_TEACHER }}" @selected(old('role') === \App\Models\User::ROLE_TEACHER)>Guru</option>
                            <option value="{{ \App\Models\User::ROLE_ADMIN }}" @selected(old('role') === \App\Models\User::ROLE_ADMIN)>Admin</option>
                        </select>
                    </div>
                </div>

                <div class="grid gap-4 lg:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-600">Password</label>
                        <div class="password-field" data-password-field>
                            <input type="password" name="password" class="dashboard-input password-input" required>
                            <button type="button" class="password-toggle text-slate-500 hover:text-slate-700 hover:bg-slate-100" data-password-toggle aria-label="Lihat password">
                                <span class="sr-only">Lihat password</span>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-600">Konfirmasi password</label>
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
                        Akun admin bisa mengakses workspace operator, sedangkan akun guru dipakai untuk membuat ujian dan monitoring.
                    </p>

                    <button type="submit" class="dashboard-button-primary w-full gap-2 sm:w-auto">
                        <svg class="dashboard-inline-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        Tambahkan akun
                    </button>
                </div>
            </form>
        </div>
    </section>
</x-layouts.dashboard>
