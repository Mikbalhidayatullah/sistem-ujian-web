<x-layouts.app :title="'Hasil Ujian'" :hide-auth-nav="true" variant="light" :show-theme-toggle="true">
    <x-slot:headerMeta>
        <div class="flex flex-wrap items-center gap-2 text-xs sm:text-sm">
            <span class="exam-chip exam-chip-info">
                {{ $attempt->participantName() }}
            </span>
            @if ($attempt->student_identifier)
                <span class="exam-chip exam-chip-neutral">
                    {{ $attempt->student_identifier }}
                </span>
            @endif
            <span class="exam-chip exam-chip-neutral">
                {{ $attempt->exam->subject->display_name }}
            </span>
            <span class="exam-chip exam-chip-success">
                {{ $attempt->status === 'auto_submitted' ? 'Terkumpul otomatis' : 'Terkumpul manual' }}
            </span>
        </div>
    </x-slot:headerMeta>

    @php
        $totalQuestions = $attempt->exam->questions->count();
        $correctCount = $attempt->correctCount();
        $wrongCount = $attempt->wrongCount();
        $unansweredCount = max($totalQuestions - $attempt->answeredCount(), 0);
    @endphp

    <section class="space-y-6">
        <div class="exam-hero exam-hero-success">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="exam-kicker">Hasil ujian</p>
                    <h1 class="exam-title">{{ $attempt->exam->title }}</h1>
                    <p class="exam-copy">
                        Rekapan ini menampilkan jawaban yang Anda kerjakan, waktu yang dipakai, dan nilai akhir.
                    </p>
                </div>
                <div class="exam-muted-frame w-full px-5 py-4 text-left sm:w-auto sm:text-right">
                    <p class="text-sm" style="color: var(--ui-copy);">Nilai akhir</p>
                    <p class="exam-heading mt-1 text-3xl font-black sm:text-4xl">{{ number_format((float) $attempt->score, 2) }}</p>
                </div>
            </div>
        </div>

        <section class="grid gap-4 md:grid-cols-4">
            <div class="exam-stat-card">
                <p class="exam-label">Nama siswa</p>
                <p class="exam-heading mt-2 text-lg font-semibold">{{ $attempt->participantName() }}</p>
            </div>
            <div class="exam-stat-card">
                <p class="exam-label">Identitas siswa</p>
                <p class="exam-heading mt-2 text-lg font-semibold">{{ $attempt->student_identifier ?: '-' }}</p>
            </div>
            <div class="exam-stat-card">
                <p class="exam-label">Mata pelajaran</p>
                <p class="exam-heading mt-2 text-lg font-semibold">{{ $attempt->exam->subject->display_name }}</p>
            </div>
            <div class="exam-stat-card">
                <p class="exam-label">Waktu dipakai</p>
                <p class="exam-heading mt-2 text-lg font-semibold">{{ $attempt->timeSpentForHumans() }}</p>
            </div>
            <div class="exam-stat-card">
                <p class="exam-label">Status submit</p>
                <p class="exam-heading mt-2 text-lg font-semibold">{{ $attempt->status === 'auto_submitted' ? 'Terkumpul otomatis' : 'Terkumpul manual' }}</p>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-3">
            <div class="exam-stat-card">
                <p class="exam-label">Jawaban benar</p>
                <p class="mt-2 text-3xl font-black" style="color: var(--ui-success-text);">{{ $correctCount }}</p>
            </div>
            <div class="exam-stat-card">
                <p class="exam-label">Jawaban salah</p>
                <p class="mt-2 text-3xl font-black" style="color: var(--ui-danger-text);">{{ $wrongCount }}</p>
            </div>
            <div class="exam-stat-card">
                <p class="exam-label">Tidak terisi</p>
                <p class="mt-2 text-3xl font-black" style="color: var(--ui-warning-text);">{{ $unansweredCount }}</p>
            </div>
        </section>
    </section>
</x-layouts.app>
