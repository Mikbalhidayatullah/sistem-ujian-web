<x-layouts.dashboard :title="'Dashboard Admin | Sistem Ujian'">
    @php
        $examTotal = max((int) $stats['total_exams'], 1);
        $attemptTotal = max((int) $stats['total_attempts'], 1);
        $teacherTotal = max((int) $stats['total_teachers'], 1);
        $activeExamWidth = round(($stats['active_exams'] / $examTotal) * 100, 2);
        $usedExamWidth = round(($stats['used_exams'] / $examTotal) * 100, 2);
        $submittedAttemptWidth = round(($stats['submitted_attempts'] / $attemptTotal) * 100, 2);
        $teacherProductiveWidth = round(($teacherCharts->where('exams_count', '>', 0)->count() / $teacherTotal) * 100, 2);
        $teacherExamMax = max((int) ($teacherCharts->max('exams_count') ?? 1), 1);
        $subjectExamMax = max((int) ($subjectCharts->max('exams_count') ?? 1), 1);
    @endphp

    <section class="space-y-8">
        <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
            <div class="dashboard-card p-6 sm:p-7">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="max-w-3xl">
                        <p class="dashboard-kicker">Admin dashboard</p>
                        <h1 class="dashboard-section-title mt-3 text-3xl sm:text-4xl">
                            Pantau guru, ujian, dan sesi siswa dari workspace admin yang lebih ringkas.
                        </h1>
                        <p class="dashboard-copy mt-4 max-w-2xl">
                            Pola panelnya saya samakan dengan dashboard guru supaya area kerja admin terasa lebih rapih, tetap fokus ke data inti, dan tidak terlalu berat di bagian atas halaman.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('admin.accounts.index') }}" class="dashboard-button-primary gap-2">
                            <svg class="dashboard-inline-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M16 20a4 4 0 0 0-8 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                <circle cx="12" cy="8" r="3.2" stroke="currentColor" stroke-width="1.8"/>
                                <path d="M5 18.5c.6-2.1 2.4-3.5 4.5-3.5M19 18.5c-.6-2.1-2.4-3.5-4.5-3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                            Kelola akun guru
                        </a>
                        <a href="#teacher-create" class="dashboard-button-soft gap-2">
                            <svg class="dashboard-inline-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                            Tambah akun baru
                        </a>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap gap-2 text-xs">
                    <span class="dashboard-pill">{{ $stats['total_teachers'] }} guru aktif</span>
                    <span class="dashboard-pill">{{ $stats['active_exams'] }} ujian sedang terbuka</span>
                    <span class="dashboard-pill">{{ $stats['submitted_attempts'] }} sesi telah selesai</span>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-1">
                <div class="dashboard-card p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-slate-500">Produktivitas guru</p>
                            <p class="mt-2 text-3xl font-black text-slate-900">{{ number_format($teacherProductiveWidth, 0) }}%</p>
                        </div>
                        <span class="dashboard-pill">dari seluruh guru</span>
                    </div>
                    <div class="mt-5 h-2.5 rounded-full bg-slate-200">
                        <div class="h-full rounded-full bg-[linear-gradient(90deg,#f97316,#fb7185,#818cf8)]" style="width: {{ $teacherProductiveWidth }}%;"></div>
                    </div>
                    <p class="mt-3 text-sm leading-7 text-slate-500">Guru yang sudah mulai membuat ujian atau mapel terlihat langsung dari panel produktivitas ini.</p>
                </div>

                <div class="dashboard-card p-5">
                    <p class="text-sm font-semibold text-slate-500">Kesehatan sistem</p>
                    <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                        <div class="dashboard-muted-card p-4">
                            <p class="text-slate-500">Mapel</p>
                            <p class="mt-1 text-2xl font-black text-slate-900">{{ $stats['total_subjects'] }}</p>
                        </div>
                        <div class="dashboard-muted-card p-4">
                            <p class="text-slate-500">Ujian dipakai</p>
                            <p class="mt-1 text-2xl font-black text-slate-900">{{ $stats['used_exams'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="dashboard-card p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">Total guru</p>
                        <p class="mt-2 text-3xl font-black text-slate-900">{{ $stats['total_teachers'] }}</p>
                        <p class="mt-2 text-sm text-slate-500">Akun yang bisa mengelola mapel dan membuat ujian.</p>
                    </div>
                    <x-ui.stat-icon tone="sky">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M16 20a4 4 0 0 0-8 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            <circle cx="12" cy="8" r="3.2" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M5 18.5c.6-2.1 2.4-3.5 4.5-3.5M19 18.5c-.6-2.1-2.4-3.5-4.5-3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </x-ui.stat-icon>
                </div>
            </div>

            <div class="dashboard-card p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">Mata pelajaran</p>
                        <p class="mt-2 text-3xl font-black text-slate-900">{{ $stats['total_subjects'] }}</p>
                        <p class="mt-2 text-sm text-slate-500">Distribusi mapel yang aktif di seluruh guru.</p>
                    </div>
                    <x-ui.stat-icon tone="emerald">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M6 5.5h8a3 3 0 0 1 3 3V18l-5-2-5 2V5.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                            <path d="M9 9.5h5M9 12.5h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </x-ui.stat-icon>
                </div>
            </div>

            <div class="dashboard-card p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">Ujian berjalan</p>
                        <p class="mt-2 text-3xl font-black text-slate-900">{{ $stats['active_exams'] }}</p>
                        <p class="mt-2 text-sm text-slate-500">Ujian yang saat ini terbuka berdasarkan status dan jadwal.</p>
                    </div>
                    <x-ui.stat-icon tone="amber">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <rect x="4" y="5" width="16" height="14" rx="2.5" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M8 3.8v2.4M16 3.8v2.4M7.5 10h9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </x-ui.stat-icon>
                </div>
            </div>

            <div class="dashboard-card p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">Sesi siswa</p>
                        <p class="mt-2 text-3xl font-black text-slate-900">{{ $stats['total_attempts'] }}</p>
                        <p class="mt-2 text-sm text-slate-500">{{ $stats['submitted_attempts'] }} sesi sudah terkumpul.</p>
                    </div>
                    <x-ui.stat-icon tone="rose">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M7 7.5h10M7 12h10M7 16.5h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            <rect x="4" y="4.5" width="16" height="15" rx="2.5" stroke="currentColor" stroke-width="1.8"/>
                        </svg>
                    </x-ui.stat-icon>
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.28fr_0.72fr]">
            <div class="space-y-6">
                <div class="dashboard-card p-6 sm:p-7">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="dashboard-kicker">Operational overview</p>
                            <h2 class="dashboard-section-title mt-2">Kondisi pelaksanaan ujian hari ini</h2>
                        </div>
                        <span class="dashboard-pill">Real-time snapshot</span>
                    </div>

                    <div class="mt-6 grid gap-5 md:grid-cols-3">
                        <div class="dashboard-muted-card p-5">
                            <p class="text-sm font-semibold text-slate-500">Ujian aktif</p>
                            <p class="mt-3 text-2xl font-black text-slate-900">{{ $stats['active_exams'] }}</p>
                            <div class="mt-4 h-2 rounded-full bg-slate-200">
                                <div class="h-full rounded-full bg-sky-500" style="width: {{ $activeExamWidth }}%;"></div>
                            </div>
                            <p class="mt-3 text-xs text-slate-500">{{ $stats['active_exams'] }} dari {{ $stats['total_exams'] }} total ujian.</p>
                        </div>

                        <div class="dashboard-muted-card p-5">
                            <p class="text-sm font-semibold text-slate-500">Ujian sudah dipakai</p>
                            <p class="mt-3 text-2xl font-black text-slate-900">{{ $stats['used_exams'] }}</p>
                            <div class="mt-4 h-2 rounded-full bg-slate-200">
                                <div class="h-full rounded-full bg-emerald-500" style="width: {{ $usedExamWidth }}%;"></div>
                            </div>
                            <p class="mt-3 text-xs text-slate-500">{{ $stats['unused_exams'] }} ujian masih belum pernah dipakai.</p>
                        </div>

                        <div class="dashboard-muted-card p-5">
                            <p class="text-sm font-semibold text-slate-500">Sesi sudah submit</p>
                            <p class="mt-3 text-2xl font-black text-slate-900">{{ $stats['submitted_attempts'] }}</p>
                            <div class="mt-4 h-2 rounded-full bg-slate-200">
                                <div class="h-full rounded-full bg-violet-500" style="width: {{ $submittedAttemptWidth }}%;"></div>
                            </div>
                            <p class="mt-3 text-xs text-slate-500">{{ $stats['in_progress_attempts'] }} sesi masih berlangsung.</p>
                        </div>
                    </div>
                </div>

                <div class="dashboard-card p-6 sm:p-7">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="dashboard-kicker">Teacher performance</p>
                            <h2 class="dashboard-section-title mt-2">Guru paling aktif membuat ujian</h2>
                        </div>
                        <a href="{{ route('admin.accounts.index') }}" class="dashboard-pill">
                            <svg class="dashboard-inline-icon-sm" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M4 12c1.7-3.4 4.8-5.5 8-5.5s6.3 2.1 8 5.5c-1.7 3.4-4.8 5.5-8 5.5S5.7 15.4 4 12Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.8"/>
                            </svg>
                            Lihat semua akun
                        </a>
                    </div>

                    <div class="mt-6 space-y-4">
                        @forelse ($teacherCharts as $teacher)
                            @php
                                $teacherBar = round(($teacher->exams_count / $teacherExamMax) * 100, 2);
                            @endphp
                            <div class="dashboard-muted-card p-5">
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <div>
                                        <p class="text-base font-bold text-slate-900">{{ $teacher->name }}</p>
                                        <p class="mt-1 text-sm text-slate-500">{{ $teacher->email }}</p>
                                    </div>
                                    <div class="flex flex-wrap gap-2 text-xs">
                                        <span class="dashboard-pill">{{ $teacher->subjects_count }} mapel</span>
                                        <span class="dashboard-pill">{{ $teacher->used_exams_count }} ujian dipakai</span>
                                    </div>
                                </div>
                                <div class="mt-4 h-2.5 rounded-full bg-slate-200">
                                    <div class="h-full rounded-full bg-[linear-gradient(90deg,#f97316,#fb7185,#818cf8)]" style="width: {{ $teacherBar }}%;"></div>
                                </div>
                                <div class="mt-3 flex items-center justify-between text-xs text-slate-500">
                                    <span>{{ $teacher->exams_count }} ujian dibuat</span>
                                    <span>{{ number_format($teacherBar, 0) }}%</span>
                                </div>
                            </div>
                        @empty
                            <p class="dashboard-muted-card p-5 text-sm text-slate-500">
                                Belum ada data guru yang cukup untuk ditampilkan.
                            </p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="dashboard-card p-6 sm:p-7">
                    <p class="dashboard-kicker">Command panel</p>
                    <h2 class="dashboard-section-title mt-2">Aksi cepat admin</h2>
                    <div class="mt-6 space-y-3">
                        <a href="{{ route('admin.accounts.index') }}" class="flex items-center justify-between rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-4 text-sm font-semibold text-slate-700 transition hover:border-sky-200 hover:bg-white">
                            <span class="flex items-center gap-3">
                                <svg class="dashboard-inline-icon text-sky-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M16 20a4 4 0 0 0-8 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    <circle cx="12" cy="8" r="3.2" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="M5 18.5c.6-2.1 2.4-3.5 4.5-3.5M19 18.5c-.6-2.1-2.4-3.5-4.5-3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                                Kelola akun guru
                            </span>
                            <svg class="dashboard-inline-icon text-sky-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                        <a href="#teacher-create" class="flex items-center justify-between rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-4 text-sm font-semibold text-slate-700 transition hover:border-violet-200 hover:bg-white">
                            <span class="flex items-center gap-3">
                                <svg class="dashboard-inline-icon text-violet-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                                Tambah akun baru
                            </span>
                            <svg class="dashboard-inline-icon text-violet-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </div>

                    <div class="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                        <div class="rounded-[1.5rem] bg-gradient-to-br from-sky-50 to-white p-4">
                            <p class="text-sm font-semibold text-slate-500">Rasio submit</p>
                            <p class="mt-2 text-2xl font-black text-slate-900">{{ number_format($submittedAttemptWidth, 0) }}%</p>
                        </div>
                        <div class="rounded-[1.5rem] bg-gradient-to-br from-violet-50 to-white p-4">
                            <p class="text-sm font-semibold text-slate-500">Ujian belum dipakai</p>
                            <p class="mt-2 text-2xl font-black text-slate-900">{{ $stats['unused_exams'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="dashboard-card p-6 sm:p-7">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="dashboard-kicker">Subject spread</p>
                            <h2 class="dashboard-section-title mt-2">Distribusi mata pelajaran</h2>
                        </div>
                        <span class="dashboard-pill">Top {{ $subjectCharts->count() }}</span>
                    </div>

                    <div class="mt-6 space-y-4">
                        @forelse ($subjectCharts as $subject)
                            @php
                                $subjectBar = round(($subject->exams_count / $subjectExamMax) * 100, 2);
                            @endphp
                            <div class="rounded-[1.35rem] border border-slate-200 bg-white p-4 shadow-[0_14px_30px_rgba(15,23,42,0.04)]">
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <div>
                                        <p class="font-bold text-slate-900">{{ $subject->display_name }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $subject->teacher?->name ?? 'Guru tidak diketahui' }}</p>
                                    </div>
                                    <span class="dashboard-pill">{{ $subject->exams_count }} ujian</span>
                                </div>
                                <div class="mt-3 h-2 rounded-full bg-slate-200">
                                    <div class="h-full rounded-full bg-[linear-gradient(90deg,#38bdf8,#818cf8)]" style="width: {{ $subjectBar }}%;"></div>
                                </div>
                            </div>
                        @empty
                            <p class="dashboard-muted-card p-5 text-sm text-slate-500">
                                Belum ada distribusi mapel yang bisa ditampilkan.
                            </p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
            <div id="teacher-create" class="dashboard-card p-6 sm:p-7">
                <p class="dashboard-kicker">Create account</p>
                <h2 class="dashboard-section-title mt-2">Tambah akun guru baru</h2>
                <p class="dashboard-copy mt-3">Form ini tetap berada di dashboard supaya admin bisa menambah akun baru sambil memantau statistik sistem.</p>

                <form method="POST" action="{{ route('admin.teachers.store') }}" class="mt-6 space-y-4">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-600">Nama guru</label>
                        <input type="text" name="name" class="dashboard-input" value="{{ old('name') }}" required>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-600">Email</label>
                        <input type="email" name="email" class="dashboard-input" value="{{ old('email') }}" required>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-600">Password</label>
                            <div class="password-field" data-password-field>
                                <input type="password" name="password" class="dashboard-input password-input" required>
                                <button type="button" class="password-toggle text-slate-500 hover:text-slate-700 hover:bg-slate-100" data-password-toggle aria-label="Lihat password">
                                    <span class="sr-only">Lihat password</span>
                                </button>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-600">Konfirmasi password</label>
                            <div class="password-field" data-password-field>
                                <input type="password" name="password_confirmation" class="dashboard-input password-input" required>
                                <button type="button" class="password-toggle text-slate-500 hover:text-slate-700 hover:bg-slate-100" data-password-toggle aria-label="Lihat password">
                                    <span class="sr-only">Lihat password</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="dashboard-button-primary w-full sm:w-auto">
                        Buat akun guru
                    </button>
                </form>
            </div>

            <div class="dashboard-card p-6 sm:p-7">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="dashboard-kicker">Recent accounts</p>
                        <h2 class="dashboard-section-title mt-2">Ringkasan akun terdaftar</h2>
                    </div>
                    <a href="{{ route('admin.accounts.index') }}" class="dashboard-pill">
                        <svg class="dashboard-inline-icon-sm" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M4 20h4l10-10-4-4L4 16v4Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                            <path d="m12.5 7.5 4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        Edit akun
                    </a>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    @forelse ($teachers->take(6) as $teacher)
                        <div class="dashboard-muted-card p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-bold text-slate-900">{{ $teacher->name }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $teacher->email }}</p>
                                </div>
                                <span class="rounded-2xl bg-white px-3 py-2 text-xs font-semibold text-slate-600 shadow-sm">
                                    {{ $teacher->exams_count }} ujian
                                </span>
                            </div>
                            <div class="mt-4 flex flex-wrap gap-2 text-xs">
                                <span class="dashboard-pill">{{ $teacher->subjects_count }} mapel</span>
                                <span class="dashboard-pill">dibuat {{ $teacher->created_at->format('d M Y') }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="dashboard-muted-card p-5 text-sm text-slate-500 md:col-span-2">
                            Belum ada akun guru. Buat akun pertama dari panel di sebelah kiri.
                        </p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
</x-layouts.dashboard>
