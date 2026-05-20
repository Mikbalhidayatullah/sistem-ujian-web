<div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
    <div class="dashboard-card p-6 sm:p-7">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="max-w-3xl">
                <p class="dashboard-kicker">{{ $exam->subject->display_name }}</p>
                <h1 class="dashboard-section-title mt-3 text-3xl sm:text-4xl">
                    {{ $exam->title }}
                </h1>
                <p class="dashboard-copy mt-4 max-w-2xl">
                    {{ $exam->description ?: 'Belum ada deskripsi ujian.' }}
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <form method="POST" action="{{ route('teacher.exams.destroy', $exam) }}" onsubmit="return confirm('Hapus ujian ini? Semua soal, sesi, jawaban, log pelanggaran, dan media soal akan ikut dihapus.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="dashboard-button-danger">
                        Hapus ujian
                    </button>
                </form>
                <a href="{{ route('teacher.exams.edit', $exam) }}" class="dashboard-button-return">
                    Edit ujian
                </a>
                <a href="{{ route('teacher.exams.print', $exam) }}" target="_blank" rel="noopener" class="dashboard-button-soft">
                    Print
                </a>
                <form method="POST" action="{{ route('teacher.exams.access', $exam) }}">
                    @csrf
                    <input type="hidden" name="action" value="{{ $exam->isManuallyOpen() ? 'close' : 'open' }}">
                    <button type="submit" class="{{ $exam->isManuallyOpen() ? 'dashboard-button-danger' : 'dashboard-button-success' }}">
                        {{ $exam->isManuallyOpen() ? 'Tutup akses sekarang' : 'Buka akses sekarang' }}
                    </button>
                </form>
                <form method="POST" action="{{ route('teacher.exams.violations.toggle', $exam) }}">
                    @csrf
                    <input type="hidden" name="action" value="{{ $exam->violations_enabled ? 'disable' : 'enable' }}">
                    <button
                        type="submit"
                        class="{{ $exam->violations_enabled ? 'dashboard-button-return' : 'dashboard-button-success' }}"
                    >
                        {{ $exam->violations_enabled ? 'Matikan fitur pelanggaran' : 'Aktifkan fitur pelanggaran' }}
                    </button>
                </form>
                <a href="{{ route('teacher.exams.export-scores', $exam) }}" class="dashboard-button-soft">
                    Download Excel
                </a>
            </div>
        </div>

        <div class="mt-6 flex flex-wrap gap-2 text-xs">
            <x-ui.dashboard-pill :tone="$exam->isManuallyOpen() ? 'default' : 'danger'">
                {{ $exam->isManuallyOpen() ? 'Akses manual dibuka' : 'Akses manual ditutup' }}
            </x-ui.dashboard-pill>
            <x-ui.dashboard-pill :tone="$exam->isWithinSchedule() ? 'default' : 'slate'">
                {{ $exam->isWithinSchedule() ? 'Dalam jadwal ujian' : 'Di luar jadwal ujian' }}
            </x-ui.dashboard-pill>
            <x-ui.dashboard-pill :tone="$exam->isOpenNow() ? 'success' : 'warning'">
                {{ $exam->isOpenNow() ? 'Siswa bisa masuk sekarang' : 'Siswa belum bisa masuk' }}
            </x-ui.dashboard-pill>
            <x-ui.dashboard-pill :tone="$exam->violations_enabled ? 'warning' : 'info'">
                {{ $exam->violations_enabled ? 'Fitur pelanggaran aktif' : 'Fitur pelanggaran nonaktif' }}
            </x-ui.dashboard-pill>
        </div>

        <div class="mt-6 grid gap-3 text-sm sm:grid-cols-2">
            <div class="dashboard-muted-card p-4">
                <p class="text-slate-500">Token ujian</p>
                <p class="mt-1 text-2xl font-black text-slate-900">{{ $exam->access_token }}</p>
            </div>
            <div class="dashboard-muted-card p-4">
                <p class="text-slate-500">PIN ujian</p>
                <p class="mt-1 text-2xl font-black text-slate-900">{{ $exam->access_pin }}</p>
            </div>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-1">
        <div class="dashboard-card p-5">
            <p class="text-sm font-semibold text-slate-500">Ringkasan aturan</p>
            <div class="mt-4 grid gap-3 text-sm">
                <div class="dashboard-muted-card p-4">
                    <p class="text-slate-500">Durasi</p>
                    <p class="mt-1 font-bold text-slate-900">{{ $exam->duration_minutes }} menit</p>
                </div>
                <div class="dashboard-muted-card p-4">
                    <p class="text-slate-500">Batas pelanggaran</p>
                    <p class="mt-1 font-bold text-slate-900">{{ $exam->max_violations }}</p>
                </div>
                <div class="dashboard-muted-card p-4">
                    <p class="text-slate-500">Mode pelanggaran</p>
                    <p class="mt-1 font-bold text-slate-900">{{ $exam->violations_enabled ? 'ON - dicatat otomatis' : 'OFF - open book / fleksibel' }}</p>
                </div>
                <div class="dashboard-muted-card p-4">
                    <p class="text-slate-500">Mulai</p>
                    <p class="mt-1 font-bold text-slate-900">{{ $exam->start_at ? $exam->start_at->format('d M Y H:i') : 'Manual kapan saja' }}</p>
                </div>
                <div class="dashboard-muted-card p-4">
                    <p class="text-slate-500">Selesai</p>
                    <p class="mt-1 font-bold text-slate-900">{{ $exam->end_at ? $exam->end_at->format('d M Y H:i') : 'Tanpa batas akhir' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
