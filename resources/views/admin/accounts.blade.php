<x-layouts.dashboard :title="'Kelola Akun Guru | Sistem Ujian'">
    @php
        $teacherCount = $teachers->count();
        $examCount = $teachers->sum('exams_count');
        $usedExamCount = $teachers->sum('used_exams_count');
        $isEditingTeacher = filled($editingTeacher);
    @endphp

    <section class="space-y-8">
        <div class="dashboard-card p-6 sm:p-7">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="max-w-3xl">
                    <p class="dashboard-kicker">Account management</p>
                    <h1 class="dashboard-section-title mt-3 text-3xl sm:text-4xl">
                        Kelola akun guru dari workspace admin yang lebih rapi.
                    </h1>
                    <p class="dashboard-copy mt-4 max-w-2xl">
                        Halaman ini difokuskan untuk menambah, mengubah, dan memilih akun guru tanpa menampilkan panel statistik dashboard lagi.
                    </p>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap gap-2 text-xs">
                <span class="dashboard-pill">{{ $teacherCount }} akun guru</span>
                <span class="dashboard-pill">{{ $examCount }} ujian terhubung</span>
                <span class="dashboard-pill">{{ $usedExamCount }} ujian sudah dipakai</span>
            </div>
        </div>

        @if ($isEditingTeacher)
            <div class="dashboard-card p-6 sm:p-7">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="dashboard-kicker">Edit Account</p>
                        <h2 class="dashboard-section-title mt-2">Perbarui akun {{ $editingTeacher->name }}</h2>
                        <p class="dashboard-copy mt-3">
                            Ubah data akun guru yang dipilih dari tabel di bawah. Kosongkan password jika tidak ingin menggantinya.
                        </p>
                    </div>
                    <a href="{{ route('admin.accounts.index') }}" class="dashboard-button-return gap-2">
                        <svg class="dashboard-inline-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        Tutup form edit
                    </a>
                </div>

                <form method="POST" action="{{ route('admin.teachers.update', $editingTeacher) }}" class="mt-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-4 lg:grid-cols-2">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-600">Nama guru</label>
                            <input type="text" name="name" class="dashboard-input" value="{{ old('name', $editingTeacher->name) }}" required>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-600">Email</label>
                            <input type="email" name="email" class="dashboard-input" value="{{ old('email', $editingTeacher->email) }}" required>
                        </div>
                    </div>

                    <div class="grid gap-4 lg:grid-cols-2">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-600">Password baru</label>
                            <div class="password-field" data-password-field>
                                <input type="password" name="password" class="dashboard-input password-input" placeholder="Kosongkan jika tidak ingin mengganti password">
                                <button type="button" class="password-toggle text-slate-500 hover:text-slate-700 hover:bg-slate-100" data-password-toggle aria-label="Lihat password">
                                    <span class="sr-only">Lihat password</span>
                                </button>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-600">Konfirmasi password baru</label>
                            <div class="password-field" data-password-field>
                                <input type="password" name="password_confirmation" class="dashboard-input password-input" placeholder="Ulangi password baru">
                                <button type="button" class="password-toggle text-slate-500 hover:text-slate-700 hover:bg-slate-100" data-password-toggle aria-label="Lihat password">
                                    <span class="sr-only">Lihat password</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 border-t border-slate-200 pt-4 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-xs leading-6 text-slate-500">
                            Simpan perubahan hanya jika data akun guru memang perlu diperbarui.
                        </p>

                        <button type="submit" class="dashboard-button-primary w-full gap-2 sm:w-auto">
                            <svg class="dashboard-inline-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M6 12.5 10 16l8-8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M5 4.5h14v15H5z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                            </svg>
                            Simpan perubahan akun
                        </button>
                    </div>
                </form>

                <div class="mt-8 border-t border-slate-200 pt-6">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="dashboard-kicker">Reset Password</p>
                            <h3 class="mt-2 text-xl font-bold text-slate-900">Reset password akun guru</h3>
                            <p class="mt-3 text-sm leading-6 text-slate-500">
                                Gunakan bagian ini jika guru lupa password. Password baru akan langsung menggantikan password lama tanpa mengubah data akun lain.
                            </p>
                        </div>
                        <span class="dashboard-pill">Admin only</span>
                    </div>

                    <form method="POST" action="{{ route('admin.teachers.reset-password', $editingTeacher) }}" class="mt-6 space-y-4" data-confirm-action="reset-teacher-password" data-confirm-keyword="RESET" data-confirm-message="Reset password untuk {{ $editingTeacher->name }}? Password lama akan langsung digantikan.">
                        @csrf

                        <div class="grid gap-4 lg:grid-cols-2">
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-slate-600">Password reset baru</label>
                                <div class="password-field" data-password-field>
                                    <input type="password" name="password" class="dashboard-input password-input" placeholder="Masukkan password baru" required>
                                    <button type="button" class="password-toggle text-slate-500 hover:text-slate-700 hover:bg-slate-100" data-password-toggle aria-label="Lihat password">
                                        <span class="sr-only">Lihat password</span>
                                    </button>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-slate-600">Konfirmasi password reset</label>
                                <div class="password-field" data-password-field>
                                    <input type="password" name="password_confirmation" class="dashboard-input password-input" placeholder="Ulangi password baru" required>
                                    <button type="button" class="password-toggle text-slate-500 hover:text-slate-700 hover:bg-slate-100" data-password-toggle aria-label="Lihat password">
                                        <span class="sr-only">Lihat password</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 border-t border-slate-200 pt-4 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-xs leading-6 text-slate-500">
                                Password baru wajib memiliki huruf besar, huruf kecil, dan angka agar akun guru tetap aman.
                            </p>

                            <button type="submit" class="dashboard-button-primary w-full gap-2 sm:w-auto">
                                <svg class="dashboard-inline-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M8 11V8.8a4 4 0 1 1 8 0V11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    <rect x="5.5" y="11" width="13" height="9" rx="2" stroke="currentColor" stroke-width="1.8"/>
                                    <circle cx="12" cy="15.5" r="1.2" fill="currentColor"/>
                                </svg>
                                Reset password guru
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <div class="dashboard-card p-6 sm:p-7">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="dashboard-kicker">Teacher accounts</p>
                    <h2 class="dashboard-section-title mt-2">Edit akun guru per pengguna</h2>
                    <p class="dashboard-copy mt-3">
                        Pilih akun dari tabel untuk membuka form edit. Tampilan ini lebih hemat ruang saat data guru sudah banyak.
                    </p>
                </div>
                <span class="dashboard-pill">{{ $teacherCount }} akun</span>
            </div>

            <div class="mt-6 overflow-x-auto rounded-[1.5rem] border border-slate-200">
                <table class="min-w-full divide-y divide-slate-200 bg-white">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">
                            <th class="px-5 py-4">Guru</th>
                            <th class="px-5 py-4">Email</th>
                            <th class="px-5 py-4">Mapel</th>
                            <th class="px-5 py-4">Ujian</th>
                            <th class="px-5 py-4">Dipakai</th>
                            <th class="px-5 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-sm text-slate-600">
                        @forelse ($teachers as $teacher)
                            <tr>
                                <td class="px-5 py-4">
                                    <p class="font-bold text-slate-900">{{ $teacher->name }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-slate-500">{{ $teacher->email }}</p>
                                </td>
                                <td class="px-5 py-4">{{ $teacher->subjects_count }}</td>
                                <td class="px-5 py-4">{{ $teacher->exams_count }}</td>
                                <td class="px-5 py-4">{{ $teacher->used_exams_count }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end">
                                        <a
                                            href="{{ route('admin.accounts.index', ['edit' => $teacher->id]) }}"
                                            class="{{ $isEditingTeacher && $editingTeacher->id === $teacher->id ? 'dashboard-button-primary' : 'dashboard-button-soft' }} gap-2"
                                        >
                                            @if ($isEditingTeacher && $editingTeacher->id === $teacher->id)
                                                <svg class="dashboard-inline-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M7 12.5l3.2 3.2L17 9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.8"/>
                                                </svg>
                                                Sedang diedit
                                            @else
                                                <svg class="dashboard-inline-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M4 20h4l10-10-4-4L4 16v4Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                                    <path d="m12.5 7.5 4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                </svg>
                                                Edit akun
                                            @endif
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-6 text-sm text-slate-500">
                                    Belum ada akun guru yang bisa dikelola.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</x-layouts.dashboard>
