<x-layouts.dashboard :title="$exam->title.' | Sistem Ujian'">
    @php
        $questionBuilderOpen = $errors->has('question_template')
            || $errors->has('default_points')
            || $errors->has('prompt')
            || $errors->has('points')
            || $errors->has('options')
            || $errors->has('correct_option')
            || $errors->has('position')
            || $errors->has('media');

        $examDetailTabs = [
            'summary' => 'Ringkasan',
            'questions' => 'Soal',
            'analytics' => 'Analitik',
            'scores' => 'Rekapan',
            'violations' => 'Pelanggaran',
        ];
        $requestedDetailTab = request('tab');
        $activeDetailTab = $questionBuilderOpen
            ? 'questions'
            : (array_key_exists($requestedDetailTab, $examDetailTabs) ? $requestedDetailTab : 'summary');
    @endphp

    <section class="space-y-6" data-exam-tabs data-default-exam-tab="{{ $activeDetailTab }}">
        @include('teacher.exams.partials.overview')

        <div class="dashboard-card p-3 sm:p-4">
            <div class="exam-detail-tabs">
                @foreach ($examDetailTabs as $tabKey => $tabLabel)
                    <a
                        href="{{ route('teacher.exams.show', ['exam' => $exam, 'tab' => $tabKey]) }}"
                        class="exam-detail-tab {{ $activeDetailTab === $tabKey ? 'is-active' : '' }}"
                        data-exam-tab-button="{{ $tabKey }}"
                        aria-pressed="{{ $activeDetailTab === $tabKey ? 'true' : 'false' }}"
                        @if($activeDetailTab === $tabKey) aria-current="page" @endif
                    >
                        {{ $tabLabel }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="space-y-6 {{ $activeDetailTab === 'summary' ? '' : 'hidden' }}" data-exam-tab-panel="summary">
            <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
                <div class="dashboard-card p-5">
                    <p class="text-sm font-semibold text-slate-500">Soal tersimpan</p>
                    <p class="mt-2 text-3xl font-black text-slate-900">{{ $exam->questions->count() }}</p>
                    <p class="mt-2 text-sm text-slate-500">Semua bank soal yang aktif untuk ujian ini.</p>
                </div>
                <div class="dashboard-card p-5">
                    <p class="text-sm font-semibold text-slate-500">Peserta ujian</p>
                    <p class="mt-2 text-3xl font-black text-slate-900">{{ $exam->attempts->count() }}</p>
                    <p class="mt-2 text-sm text-slate-500">Siswa yang sudah mulai atau mengumpulkan ujian.</p>
                </div>
                <div class="dashboard-card p-5">
                    <p class="text-sm font-semibold text-slate-500">Pelanggaran</p>
                    <p class="mt-2 text-3xl font-black text-slate-900">{{ $totalViolationsCount }}</p>
                    <p class="mt-2 text-sm text-slate-500">Catatan pelanggaran yang sudah terekam.</p>
                </div>
                <div class="dashboard-card p-5">
                    <p class="text-sm font-semibold text-slate-500">Nilai terekam</p>
                    <p class="mt-2 text-3xl font-black text-slate-900">{{ $submittedAttemptsCount }}</p>
                    <p class="mt-2 text-sm text-slate-500">Rekapan nilai yang siap ditinjau atau diekspor.</p>
                </div>
            </div>
        </div>

        <div class="space-y-6 {{ $activeDetailTab === 'questions' ? '' : 'hidden' }}" data-exam-tab-panel="questions">
            <details class="dashboard-card overflow-hidden p-0" @if($questionBuilderOpen) open @endif>
                <summary class="exam-builder-summary flex cursor-pointer list-none items-center justify-between gap-4 p-6 sm:p-7">
                    <div>
                        <p class="dashboard-kicker">Input Soal</p>
                        <h2 class="dashboard-section-title mt-2">Tambah soal ke ujian ini</h2>
                        <p class="dashboard-copy mt-3 max-w-2xl">
                            Panel ini bisa dibuka saat Anda ingin menambah atau mengimpor soal. Kalau sudah tidak dipakai, panel tetap tertutup supaya detail ujian terlihat lebih ringkas.
                        </p>
                    </div>
                    <span class="dashboard-pill shrink-0">Tambah soal</span>
                </summary>
                <div class="border-t border-slate-200 p-6 sm:p-7">
                    @include('teacher.exams.partials.question-form')
                </div>
            </details>

            <div class="grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
                @include('teacher.exams.partials.question-list')

                <aside class="space-y-6">
                    @include('teacher.exams.partials.question-bank-library')
                </aside>
            </div>
        </div>

        <div class="{{ $activeDetailTab === 'analytics' ? '' : 'hidden' }}" data-exam-tab-panel="analytics">
            @include('teacher.exams.partials.question-insights')
        </div>

        <div class="{{ $activeDetailTab === 'scores' ? '' : 'hidden' }}" data-exam-tab-panel="scores">
            @include('teacher.exams.partials.scores-summary')
        </div>

        <div class="{{ $activeDetailTab === 'violations' ? '' : 'hidden' }}" data-exam-tab-panel="violations">
            @include('teacher.exams.partials.violations-log')
        </div>
    </section>
</x-layouts.dashboard>
