<x-layouts.app :title="$attempt->exam->title" :hide-auth-nav="true" variant="light" :show-theme-toggle="true">
    <x-slot:headerMeta>
        <div class="flex flex-wrap items-center gap-2 text-xs sm:text-sm">
            <span class="exam-chip exam-chip-info">
                {{ $attempt->participantName() }}
            </span>
            <span class="exam-chip exam-chip-neutral">
                {{ $attempt->exam->subject->display_name }}
            </span>
            <span class="exam-chip exam-chip-success">
                <span data-countdown>Waktu sedang dimuat...</span>
            </span>
        </div>
    </x-slot:headerMeta>

    <section
        class="space-y-6"
        data-exam-session
        data-save-url="{{ route('exam.attempts.save', $attempt) }}"
        data-violation-url="{{ route('exam.attempts.violations', $attempt) }}"
        data-redirect-url="{{ route('home') }}"
        data-max-violations="{{ $attempt->exam->max_violations }}"
        data-expires-at="{{ optional($attempt->expiresAt())->toIso8601String() }}"
        data-total-questions="{{ $attempt->exam->questions->count() }}"
    >
        <div class="exam-hero exam-hero-danger">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="exam-kicker">Mode ujian aktif</p>
                    <h1 class="exam-title">{{ $attempt->exam->title }}</h1>
                </div>
                <button type="button" data-fullscreen-button class="dashboard-button-danger mobile-button rounded-full px-5 py-3 lg:self-start">
                    Aktifkan full-screen
                </button>
            </div>
            <p class="exam-copy">
                Jangan pindah tab, keluar dari full-screen, atau membuka aplikasi lain. Sistem akan membunyikan alarm, mencatat pelanggaran, dan bisa mengirim submit otomatis bila batas pelanggaran terlampaui.
            </p>
            <div class="mt-4 flex flex-wrap gap-2">
                <span class="exam-chip exam-chip-neutral">{{ $attempt->participantName() }}</span>
                <span class="exam-chip exam-chip-neutral">{{ $attempt->exam->subject->display_name }}</span>
                <span class="exam-chip exam-chip-warning">{{ $attempt->exam->duration_minutes }} menit</span>
                <span class="exam-chip exam-chip-warning">Batas pelanggaran {{ $attempt->exam->max_violations }}</span>
            </div>
        </div>

        <form method="POST" action="{{ route('exam.attempts.submit', $attempt) }}" id="exam-form" class="space-y-5">
            @csrf
            @foreach ($attempt->exam->questions as $question)
                <article class="exam-card">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="exam-label">Soal {{ $loop->iteration }}</p>
                            <h2 class="exam-heading mt-2 text-lg font-semibold leading-8">{!! nl2br(e($question->prompt)) !!}</h2>
                        </div>
                        <span class="exam-chip exam-chip-info">{{ $question->points }} poin</span>
                    </div>

                    @if ($question->hasMedia())
                        <div class="exam-media-frame">
                            @if ($question->media_type === 'image')
                                <img src="{{ route('questions.media', $question) }}" alt="Media soal" class="max-h-96 w-full rounded-xl object-contain">
                            @else
                                <video controls controlsList="nodownload" class="max-h-96 w-full rounded-xl">
                                    <source src="{{ route('questions.media', $question) }}">
                                </video>
                            @endif
                        </div>
                    @endif

                    <div class="mt-5 grid gap-3">
                        @foreach ($question->options as $option)
                            <label class="exam-option">
                                <input
                                    type="radio"
                                    name="answers[{{ $question->id }}]"
                                    value="{{ $option->id }}"
                                    class="exam-radio mt-1"
                                    @checked((string) ($savedAnswers[$question->id] ?? '') === (string) $option->id)
                                >
                                <span>{{ $option->option_text }}</span>
                            </label>
                        @endforeach
                    </div>
                </article>
            @endforeach

            <div class="exam-sticky-bar">
                <div class="text-sm" style="color: var(--ui-copy);">
                    Jawaban disimpan otomatis secara berkala.
                </div>
                <button type="submit" class="dashboard-button-success mobile-button rounded-[1.25rem] px-6 py-3">
                    Kumpulkan ujian
                </button>
            </div>
        </form>

        <div data-submit-modal class="fixed inset-0 z-50 hidden items-center justify-center px-4 backdrop-blur-sm sm:px-6" style="background: var(--exam-overlay-bg);">
            <div class="exam-modal-card">
                <p class="exam-kicker" style="color: var(--ui-warning-text);">Konfirmasi submit</p>
                <h2 class="exam-heading mt-3 text-xl font-black sm:text-2xl">Periksa lagi sebelum mengumpulkan</h2>
                <p data-submit-modal-message class="mt-4 text-sm leading-7" style="color: var(--ui-copy);">
                    Sistem sedang menyiapkan ringkasan jawaban Anda.
                </p>
                <div class="mt-6 flex flex-col-reverse gap-3 md:flex-row md:justify-end">
                    <button type="button" data-submit-cancel class="dashboard-button-return mobile-button rounded-[1.25rem] px-5 py-3">
                        Cek lagi jawaban
                    </button>
                    <button type="button" data-submit-confirm class="dashboard-button-success mobile-button rounded-[1.25rem] px-5 py-3">
                        Ya, kumpulkan sekarang
                    </button>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
