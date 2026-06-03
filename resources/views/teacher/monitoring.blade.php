<x-layouts.dashboard :title="'Monitoring Guru | Sistem Ujian'">
    @php
        $latestCount = $violations->count();
        $tabHiddenCount = $violations->getCollection()->where('violation_type', 'tab_hidden')->count();
        $fullscreenExitCount = $violations->getCollection()->where('violation_type', 'fullscreen_exit')->count();
        $lockedAttemptCount = $violations->getCollection()->filter(fn ($violation) => $violation->attempt?->isLocked())->count();
    @endphp

    <section class="space-y-8">
        <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
            <div class="dashboard-card p-6 sm:p-7">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="max-w-3xl">
                        <p class="dashboard-kicker">Violation log</p>
                        <h1 class="dashboard-section-title mt-3 text-3xl sm:text-4xl">
                            Monitoring pelanggaran siswa
                        </h1>
                        <p class="dashboard-copy mt-4 max-w-2xl">
                            Halaman ini difokuskan untuk melihat catatan pelanggaran secara cepat, tanpa panel pengantar besar.
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap gap-2 text-xs">
                    <span class="dashboard-pill">{{ $violations->total() }} total catatan</span>
                    <span class="dashboard-pill">Halaman {{ $violations->currentPage() }}</span>
                    <span class="dashboard-pill">{{ $latestCount }} data ditampilkan</span>
                    <span class="dashboard-pill">{{ $lockedAttemptCount }} sesi terkunci</span>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-1">
                <div class="dashboard-card p-5">
                    <p class="text-sm font-semibold text-slate-500">Pelanggaran di halaman ini</p>
                    <p class="mt-2 text-3xl font-black text-slate-900">{{ $latestCount }}</p>
                    <p class="mt-4 text-sm leading-7 text-slate-500">
                        Hitungan cepat untuk data yang sedang Anda lihat sekarang.
                    </p>
                </div>

                <div class="dashboard-card p-5">
                    <p class="text-sm font-semibold text-slate-500">Pola yang menonjol</p>
                    <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                        <div class="dashboard-muted-card p-4">
                            <p class="text-slate-500">Tab</p>
                            <p class="mt-1 text-2xl font-black text-slate-900">{{ $tabHiddenCount }}</p>
                        </div>
                        <div class="dashboard-muted-card p-4">
                            <p class="text-slate-500">Full-screen</p>
                            <p class="mt-1 text-2xl font-black text-slate-900">{{ $fullscreenExitCount }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="dashboard-card p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">Total catatan</p>
                        <p class="mt-2 text-3xl font-black text-slate-900">{{ $violations->total() }}</p>
                        <p class="mt-2 text-sm text-slate-500">Semua pelanggaran yang terekam untuk ujian Anda.</p>
                    </div>
                    <x-ui.stat-icon tone="amber">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 7.5v5M12 16h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            <path d="M10.3 4.8 4.9 14.1A2 2 0 0 0 6.6 17h10.8a2 2 0 0 0 1.7-2.9l-5.4-9.3a2 2 0 0 0-3.4 0Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                        </svg>
                    </x-ui.stat-icon>
                </div>
            </div>

            <div class="dashboard-card p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">Data di halaman ini</p>
                        <p class="mt-2 text-3xl font-black text-slate-900">{{ $latestCount }}</p>
                        <p class="mt-2 text-sm text-slate-500">Jumlah baris yang sedang tampil sekarang.</p>
                    </div>
                    <x-ui.stat-icon tone="sky">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M7 7.5h10M7 12h10M7 16.5h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            <rect x="4" y="4.5" width="16" height="15" rx="2.5" stroke="currentColor" stroke-width="1.8"/>
                        </svg>
                    </x-ui.stat-icon>
                </div>
            </div>

            <div class="dashboard-card p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">Keluar full-screen</p>
                        <p class="mt-2 text-3xl font-black text-slate-900">{{ $fullscreenExitCount }}</p>
                        <p class="mt-2 text-sm text-slate-500">{{ $tabHiddenCount }} perpindahan tab terdeteksi di halaman ini.</p>
                    </div>
                    <x-ui.stat-icon tone="rose">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M8 4.5H5.5V8M16 4.5h2.5V8M8 19.5H5.5V16M16 19.5h2.5V16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9 9h6v6H9z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                        </svg>
                    </x-ui.stat-icon>
                </div>
            </div>
        </div>

        <div class="dashboard-card p-6 sm:p-7">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="dashboard-kicker">Violation Log</p>
                    <h2 class="dashboard-section-title mt-2">Semua data monitoring</h2>
                    <p class="dashboard-copy mt-3">
                        Urutan ditampilkan dari data terbaru ke data yang lebih lama agar Anda bisa lebih cepat memeriksa kejadian yang baru muncul.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <span class="dashboard-pill">{{ $violations->total() }} total data</span>
                    @if ($violations->total() > 0)
                        <form method="POST" action="{{ route('teacher.monitoring.destroy-all') }}" data-confirm-action="delete-all-monitoring" data-confirm-keyword="HAPUS" data-confirm-message="Hapus semua data monitoring pelanggaran untuk semua ujian Anda? Tindakan ini tidak bisa dibatalkan.">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dashboard-button-danger px-4 py-2 text-xs">
                                Hapus semua monitoring
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="mt-6 space-y-4">
                @forelse ($violations as $violation)
                    <article class="rounded-[1.75rem] border border-amber-200 bg-amber-50 p-5 shadow-[0_18px_40px_rgba(251,191,36,0.08)]">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-lg font-bold text-slate-900">{{ $violation->attempt->participantName() }}</h3>
                                    <span class="rounded-full border border-amber-200 bg-white px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-amber-700">
                                        {{ str_replace('_', ' ', $violation->violation_type) }}
                                    </span>
                                    @if ($violation->attempt->isLocked())
                                        <span class="rounded-full border border-rose-200 bg-rose-100 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-rose-700">
                                            Sesi terkunci
                                        </span>
                                    @endif
                                </div>
                                @if ($violation->attempt->student_identifier)
                                    <p class="mt-2 text-xs font-semibold text-slate-500">{{ $violation->attempt->student_identifier }}</p>
                                @endif
                                <p class="mt-2 text-sm font-semibold text-amber-900">{{ $violation->attempt->exam->title }}</p>
                                <p class="mt-1 text-xs uppercase tracking-[0.24em] text-amber-700/80">{{ $violation->attempt->exam->subject->display_name }}</p>
                            </div>

                            <div class="rounded-[1.25rem] border border-amber-200 bg-white px-4 py-3 text-right text-xs text-amber-800">
                                <div>Terjadi pada</div>
                                <div class="mt-1 text-sm font-semibold text-slate-900">{{ $violation->happened_at->format('d M Y H:i:s') }}</div>
                            </div>
                        </div>

                        <div class="mt-4 rounded-[1.25rem] border border-amber-200/80 bg-white/80 p-4 text-sm leading-7 text-slate-700">
                            {{ $violation->detail ?: 'Pelanggaran terdeteksi dari halaman ujian.' }}
                        </div>

                        <div class="mt-4 flex flex-wrap justify-end gap-2">
                            @if ($violation->attempt->isLocked())
                                <form method="POST" action="{{ route('teacher.monitoring.attempts.unlock', $violation->attempt) }}" data-confirm-action="unlock-attempt" data-confirm-message="Buka kembali sesi ujian untuk {{ $violation->attempt->participantName() }}?">
                                    @csrf
                                    <button type="submit" class="dashboard-button-success px-4 py-2 text-xs">
                                        Buka kunci ujian
                                    </button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('teacher.monitoring.destroy', $violation) }}" data-confirm-action="delete-monitoring" data-confirm-keyword="HAPUS" data-confirm-message="Hapus catatan monitoring untuk {{ $violation->attempt->participantName() }}?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dashboard-button-danger px-4 py-2 text-xs">
                                    Hapus catatan
                                </button>
                            </form>
                        </div>
                    </article>
                @empty
                    <p class="dashboard-muted-card p-5 text-sm text-slate-500">
                        Belum ada pelanggaran yang tercatat.
                    </p>
                @endforelse
            </div>

            @if ($violations->hasPages())
                <div class="mt-6">
                    {{ $violations->links() }}
                </div>
            @endif
        </div>
    </section>
</x-layouts.dashboard>
