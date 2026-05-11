<x-layouts.dashboard :title="'Dashboard Guru | Sistem Ujian'">
    @php
        $examTotal = max((int) $stats['total_exams'], 1);
        $attemptTotal = max((int) $stats['total_attempts'], 1);
        $activeExamWidth = round(($stats['active_exams'] / $examTotal) * 100, 2);
        $scheduledExamWidth = round(($stats['scheduled_exams'] / $examTotal) * 100, 2);
        $attemptDensity = round(($stats['total_attempts'] / $examTotal), 1);
    @endphp

    <section class="space-y-8">
        <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
            <div class="dashboard-card p-6 sm:p-7">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="max-w-3xl">
                        <p class="dashboard-kicker">Teacher dashboard</p>
                        <h1 class="dashboard-section-title mt-3 text-3xl sm:text-4xl">
                            Kelola mapel, ujian, dan monitoring siswa dari satu workspace yang lebih ringkas.
                        </h1>
                        <p class="dashboard-copy mt-4 max-w-2xl">
                            Struktur halaman ini saya ringankan supaya lebih dekat ke gaya template dashboard modern: fokus ke data inti, aksi cepat, dan daftar kerja yang langsung terlihat tanpa panel hero besar.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('teacher.exams.create') }}" class="dashboard-button-primary gap-2">
                            <svg class="dashboard-inline-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                            Buat ujian baru
                        </a>
                        <a href="{{ route('teacher.monitoring') }}" class="dashboard-button-soft gap-2">
                            <svg class="dashboard-inline-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M4 12c1.7-3.4 4.8-5.5 8-5.5s6.3 2.1 8 5.5c-1.7 3.4-4.8 5.5-8 5.5S5.7 15.4 4 12Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.8"/>
                            </svg>
                            Buka monitoring
                        </a>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap gap-2 text-xs">
                    <span class="dashboard-pill">{{ $stats['total_subjects'] }} mapel aktif</span>
                    <span class="dashboard-pill">{{ $stats['active_exams'] }} ujian sedang dibuka</span>
                    <span class="dashboard-pill">{{ $stats['total_attempts'] }} total sesi siswa</span>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-1">
                <div class="dashboard-card p-5">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-slate-500">Kepadatan sesi</p>
                            <p class="mt-2 text-3xl font-black text-slate-900">{{ number_format($attemptDensity, 1) }}</p>
                        </div>
                        <span class="dashboard-pill">siswa per ujian</span>
                    </div>
                    <p class="mt-4 text-sm leading-7 text-slate-500">Angka ini membantu melihat apakah distribusi peserta sudah mulai aktif atau masih perlu sosialisasi token ujian.</p>
                </div>

                <div class="dashboard-card p-5">
                    <p class="text-sm font-semibold text-slate-500">Bank soal Anda</p>
                    <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                        <div class="dashboard-muted-card p-4">
                            <p class="text-slate-500">Total soal</p>
                            <p class="mt-1 text-2xl font-black text-slate-900">{{ $stats['total_questions'] }}</p>
                        </div>
                        <div class="dashboard-muted-card p-4">
                            <p class="text-slate-500">Pelanggaran</p>
                            <p class="mt-1 text-2xl font-black text-slate-900">{{ $stats['total_violations'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="dashboard-card p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">Total mapel</p>
                        <p class="mt-2 text-3xl font-black text-slate-900">{{ $stats['total_subjects'] }}</p>
                        <p class="mt-2 text-sm text-slate-500">Daftar mata pelajaran yang Anda ampu.</p>
                    </div>
                    <x-ui.stat-icon tone="sky">
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
                        <p class="text-sm font-semibold text-slate-500">Ujian dibuat</p>
                        <p class="mt-2 text-3xl font-black text-slate-900">{{ $stats['total_exams'] }}</p>
                        <p class="mt-2 text-sm text-slate-500">Semua ujian yang sudah Anda susun.</p>
                    </div>
                    <x-ui.stat-icon tone="violet">
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
                        <p class="text-sm font-semibold text-slate-500">Ujian aktif</p>
                        <p class="mt-2 text-3xl font-black text-slate-900">{{ $stats['active_exams'] }}</p>
                        <p class="mt-2 text-sm text-slate-500">Saat ini bisa diakses siswa.</p>
                    </div>
                    <x-ui.stat-icon tone="emerald">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M7 12.5l3.2 3.2L17 9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.8"/>
                        </svg>
                    </x-ui.stat-icon>
                </div>
            </div>
            <div class="dashboard-card p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">Sesi siswa</p>
                        <p class="mt-2 text-3xl font-black text-slate-900">{{ $stats['total_attempts'] }}</p>
                        <p class="mt-2 text-sm text-slate-500">Riwayat peserta yang sudah masuk ujian.</p>
                    </div>
                    <x-ui.stat-icon tone="amber">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M7 7.5h10M7 12h10M7 16.5h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            <rect x="4" y="4.5" width="16" height="15" rx="2.5" stroke="currentColor" stroke-width="1.8"/>
                        </svg>
                    </x-ui.stat-icon>
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.18fr_0.82fr]">
            <div class="space-y-6">
                <div class="dashboard-card p-6 sm:p-7">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="dashboard-kicker">Exam overview</p>
                            <h2 class="dashboard-section-title mt-2">Status ujian yang Anda kelola</h2>
                        </div>
                        <a href="{{ route('teacher.exams.create') }}" class="dashboard-pill">
                            <svg class="dashboard-inline-icon-sm" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                            Tambah ujian
                        </a>
                    </div>

                    <div class="mt-6 grid gap-5 md:grid-cols-3">
                        <div class="dashboard-muted-card p-5">
                            <p class="text-sm font-semibold text-slate-500">Akses aktif</p>
                            <p class="mt-3 text-2xl font-black text-slate-900">{{ $stats['active_exams'] }}</p>
                            <div class="mt-4 h-2 rounded-full bg-slate-200">
                                <div class="h-full rounded-full bg-sky-500" style="width: {{ $activeExamWidth }}%;"></div>
                            </div>
                            <p class="mt-3 text-xs text-slate-500">Status kombinasi akses manual dan jadwal aktif.</p>
                        </div>
                        <div class="dashboard-muted-card p-5">
                            <p class="text-sm font-semibold text-slate-500">Terjadwal berikutnya</p>
                            <p class="mt-3 text-2xl font-black text-slate-900">{{ $stats['scheduled_exams'] }}</p>
                            <div class="mt-4 h-2 rounded-full bg-slate-200">
                                <div class="h-full rounded-full bg-violet-500" style="width: {{ $scheduledExamWidth }}%;"></div>
                            </div>
                            <p class="mt-3 text-xs text-slate-500">Ujian yang sudah dijadwalkan tetapi belum mulai.</p>
                        </div>
                        <div class="dashboard-muted-card p-5">
                            <p class="text-sm font-semibold text-slate-500">Rata-rata sesi</p>
                            <p class="mt-3 text-2xl font-black text-slate-900">{{ number_format($attemptDensity, 1) }}</p>
                            <p class="mt-4 text-xs text-slate-500">Peserta per ujian yang sudah dibuat.</p>
                        </div>
                    </div>
                </div>

                <div class="dashboard-card p-6 sm:p-7">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="dashboard-kicker">Exam list</p>
                            <h2 class="dashboard-section-title mt-2">Ujian yang Anda buat</h2>
                        </div>
                        <span class="dashboard-pill">{{ $stats['total_questions'] }} total soal</span>
                    </div>

                    <div class="mt-6 grid gap-4">
                        @forelse ($exams as $exam)
                            <div class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-[0_16px_35px_rgba(15,23,42,0.04)] transition hover:-translate-y-0.5 hover:shadow-[0_24px_50px_rgba(15,23,42,0.08)]">
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-sky-500">{{ $exam->subject->name }}</p>
                                        <a href="{{ route('teacher.exams.show', $exam) }}" class="mt-2 block text-xl font-black tracking-tight text-slate-900 transition hover:text-sky-600">
                                            {{ $exam->title }}
                                        </a>
                                    </div>
                                    <div class="flex flex-wrap gap-2 text-xs">
                                        @if ($exam->subject->class_name)
                                            <span class="rounded-full px-3 py-1 font-semibold bg-slate-100 text-slate-600">
                                                {{ $exam->subject->class_name }}
                                            </span>
                                        @endif
                                        <span class="rounded-full px-3 py-1 font-semibold {{ $exam->isManuallyOpen() ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                            {{ $exam->isManuallyOpen() ? 'Manual dibuka' : 'Manual ditutup' }}
                                        </span>
                                        <span class="rounded-full px-3 py-1 font-semibold {{ $exam->isWithinSchedule() ? 'bg-sky-100 text-sky-700' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $exam->isWithinSchedule() ? 'Dalam jadwal' : 'Di luar jadwal' }}
                                        </span>
                                    </div>
                                </div>

                                <p class="mt-3 text-sm leading-7 text-slate-500">{{ $exam->description ?: 'Belum ada deskripsi ujian.' }}</p>

                                <div class="mt-4 flex flex-wrap gap-2 text-xs">
                                    <span class="dashboard-pill">{{ $exam->questions_count }} soal</span>
                                    <span class="dashboard-pill">{{ $exam->attempts_count }} siswa</span>
                                    <span class="dashboard-pill">{{ $exam->duration_minutes }} menit</span>
                                    <span class="dashboard-pill">Token {{ $exam->access_token }}</span>
                                </div>

                                <div class="mt-5 action-row items-stretch lg:items-center">
                                    <div class="flex w-full flex-col gap-3 sm:flex-row">
                                        <form method="POST" action="{{ route('teacher.exams.access', $exam) }}" class="w-full sm:w-auto">
                                            @csrf
                                            <input type="hidden" name="action" value="{{ $exam->isManuallyOpen() ? 'close' : 'open' }}">
                                            <button type="submit" class="w-full {{ $exam->isManuallyOpen() ? 'dashboard-button-danger' : 'dashboard-button-success' }}">
                                                {{ $exam->isManuallyOpen() ? 'Tutup akses manual' : 'Buka akses manual' }}
                                            </button>
                                        </form>
                                        <a href="{{ route('teacher.exams.show', $exam) }}" class="dashboard-button-soft w-full sm:w-auto">
                                            Lihat detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="dashboard-muted-card p-5 text-sm text-slate-500">
                                Belum ada ujian. Gunakan tombol "Buat ujian baru" untuk mulai menyusun pelaksanaan.
                            </p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="dashboard-card p-6 sm:p-7">
                    <div>
                        <p class="dashboard-kicker">Subject management</p>
                        <h2 class="dashboard-section-title mt-2">Mata pelajaran dipisah ke halaman khusus</h2>
                        <p class="dashboard-copy mt-3">Kelola mapel dan kelas sekarang dari menu sidebar agar dashboard utama tetap fokus ke statistik dan ujian.</p>
                    </div>

                    <div class="mt-6 grid gap-3">
                        <a href="{{ route('teacher.subjects.index') }}" class="dashboard-button-primary w-full gap-2">
                            <svg class="dashboard-inline-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M6 5.5h8a3 3 0 0 1 3 3V18l-5-2-5 2V5.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                <path d="M9 9.5h5M9 12.5h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                            Buka halaman mata pelajaran
                        </a>
                        <p class="text-sm text-slate-500">
                            Tambahkan mapel, isi kelas, dan lihat daftar lengkap tanpa menumpuk form di dashboard guru.
                        </p>
                    </div>
                </div>

                <div class="dashboard-card p-6 sm:p-7">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="dashboard-kicker">Monitoring</p>
                            <h2 class="dashboard-section-title mt-2">Pelanggaran terbaru</h2>
                        </div>
                        <a href="{{ route('teacher.monitoring') }}" class="dashboard-pill">
                            <svg class="dashboard-inline-icon-sm" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M4 12c1.7-3.4 4.8-5.5 8-5.5s6.3 2.1 8 5.5c-1.7 3.4-4.8 5.5-8 5.5S5.7 15.4 4 12Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.8"/>
                            </svg>
                            Lihat semua
                        </a>
                    </div>

                    <div class="mt-6 space-y-3">
                        @forelse ($recentViolations as $violation)
                            <div class="rounded-[1.35rem] border border-amber-200 bg-amber-50 p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="text-sm font-bold text-amber-900">{{ $violation->attempt->participantName() }}</p>
                                    <p class="text-[11px] text-amber-700/80">{{ $violation->happened_at->format('d M Y H:i') }}</p>
                                </div>
                                <p class="mt-2 text-sm font-semibold text-amber-900">{{ $violation->attempt->exam->title }}</p>
                                <div class="mt-3 flex flex-wrap gap-2 text-[11px]">
                                    <span class="rounded-full bg-white px-2.5 py-1 font-semibold uppercase tracking-[0.2em] text-amber-700">
                                        {{ $violation->violation_type }}
                                    </span>
                                    <span class="rounded-full bg-white px-2.5 py-1 font-semibold text-amber-700">
                                        {{ $violation->attempt->exam->subject->display_name }}
                                    </span>
                                </div>
                                <p class="mt-3 text-xs leading-6 text-amber-800">{{ $violation->detail ?: 'Pelanggaran terdeteksi dari halaman ujian.' }}</p>
                            </div>
                        @empty
                            <p class="dashboard-muted-card p-5 text-sm text-slate-500">
                                Belum ada pelanggaran yang tercatat.
                            </p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.dashboard>
