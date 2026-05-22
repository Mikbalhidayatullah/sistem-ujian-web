<div class="dashboard-card p-6">
    <x-ui.dashboard-header eyebrow="Bank Soal Mapel" title="Ambil soal siap pakai">
        <x-slot:aside>
            <x-ui.dashboard-pill>{{ $questionBankQuestions->count() }} soal tersimpan</x-ui.dashboard-pill>
        </x-slot:aside>
    </x-ui.dashboard-header>

    <p class="mt-4 text-sm leading-6 text-slate-500">
        Kumpulan soal ini khusus untuk mata pelajaran {{ $exam->subject->display_name }}. Klik impor untuk menyalin soal ke ujian aktif tanpa menulis ulang.
    </p>

    <x-ui.scroll-panel height="20rem" class="mt-5" content-class="space-y-3 p-2 pr-3">
        @forelse ($questionBankQuestions as $questionBank)
            <div class="dashboard-muted-card p-4">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="space-y-2">
                        <p class="text-sm font-bold text-slate-900">{{ $questionBank->prompt }}</p>
                        <div class="flex flex-wrap gap-2 text-xs">
                            <x-ui.dashboard-pill>{{ $questionBank->points }} poin</x-ui.dashboard-pill>
                            @if ($questionBank->source_exam_title)
                                <x-ui.dashboard-pill tone="info">
                                    Dari {{ $questionBank->source_exam_title }}{{ $questionBank->source_question_position ? ' · soal '.$questionBank->source_question_position : '' }}
                                </x-ui.dashboard-pill>
                            @endif
                        </div>
                    </div>
                    <form method="POST" action="{{ route('teacher.exams.question-bank.import', [$exam, $questionBank]) }}">
                        @csrf
                        <button type="submit" class="dashboard-button-primary px-4 py-2 text-xs">
                            Impor ke ujian
                        </button>
                    </form>
                </div>

                <div class="mt-3 grid gap-2 text-sm md:grid-cols-2">
                    @foreach ($questionBank->options as $option)
                        <div class="rounded-2xl border {{ $option->is_correct ? 'border-emerald-200 bg-emerald-50' : 'border-slate-200 bg-white' }} px-3 py-2 text-slate-700">
                            {{ chr(64 + $option->position) }}. {{ $option->option_text }}
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <p class="dashboard-muted-card p-5 text-sm text-slate-500">
                Belum ada soal tersimpan untuk mapel ini. Simpan dulu salah satu soal dari bank soal ujian di sebelah kiri.
            </p>
        @endforelse
    </x-ui.scroll-panel>
</div>
