<x-layouts.app :title="$attempt->exam->title" :hide-auth-nav="true" variant="light" :show-theme-toggle="false">
    <section
        class="exam-player"
        data-exam-session
        data-exam-player
        data-save-url="{{ route('exam.attempts.save', $attempt) }}"
        data-violation-url="{{ route('exam.attempts.violations', $attempt) }}"
        data-redirect-url="{{ route('home') }}"
        data-max-violations="{{ $attempt->exam->max_violations }}"
        data-violations-enabled="{{ $attempt->exam->violations_enabled ? '1' : '0' }}"
        data-attempt-locked="{{ $attempt->isLocked() ? '1' : '0' }}"
        data-locked-reason="{{ $attempt->locked_reason ?: 'Terdeteksi aktivitas yang melanggar aturan ujian.' }}"
        data-expires-at="{{ optional($attempt->expiresAt())->toIso8601String() }}"
        data-total-questions="{{ $orderedQuestions->count() }}"
        data-attempt-id="{{ $attempt->id }}"
    >
        <div class="exam-player-topbar">
            <div class="exam-player-brand">
                <span class="exam-player-icon">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M4 5h16v14H4z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        <path d="m9 9-3 3 3 3M15 9l3 3-3 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span>Paket: {{ $attempt->exam->title }}</span>
            </div>

            <div class="exam-player-meta">
                <span class="exam-player-participant">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM4.5 20a7.5 7.5 0 0 1 15 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    Peserta {{ $attempt->participantName() }}
                </span>
                <span class="exam-player-timer">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 7v5l3 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="12" cy="13" r="8" stroke="currentColor" stroke-width="2"/>
                        <path d="M9 3h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <span data-countdown>00:00:00</span>
                </span>
                <button type="button" class="exam-player-grid-button" data-question-nav-toggle aria-expanded="false" aria-label="Tampilkan nomor soal">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M4 4h5v5H4zM15 4h5v5h-5zM4 15h5v5H4zM15 15h5v5h-5z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                    </svg>
                </button>
                <button type="button" data-theme-toggle class="exam-player-theme-button">
                    <span class="sr-only">Ubah tema</span>
                </button>
            </div>
        </div>

        <div class="exam-player-question-panel" data-question-nav data-mobile-collapsed="true">
            <div class="exam-player-question-panel-head">
                <div>
                    <p class="exam-player-mini-label">Navigasi soal</p>
                    <h2>Pilih nomor soal</h2>
                </div>
                <span>{{ $orderedQuestions->count() }} soal</span>
            </div>
            <div class="exam-player-question-grid" data-question-nav-track>
                @foreach ($orderedQuestions as $question)
                    <a
                        href="#question-{{ $question->id }}"
                        class="exam-question-nav-button {{ isset($savedAnswers[$question->id]) ? 'is-answered' : '' }}"
                        data-question-nav-button
                        data-question-id="{{ $question->id }}"
                    >
                        {{ $loop->iteration }}
                    </a>
                @endforeach
            </div>
        </div>

        <form method="POST" action="{{ route('exam.attempts.submit', $attempt) }}" id="exam-form" class="exam-player-form">
            @csrf
            @foreach ($orderedQuestions as $question)
                <article
                    class="exam-player-card {{ $loop->first ? 'is-active' : 'hidden' }}"
                    id="question-{{ $question->id }}"
                    data-question-card
                    data-question-id="{{ $question->id }}"
                    data-question-position="{{ $loop->iteration }}"
                >
                    <div class="exam-player-card-head">
                        <h1>SOAL NO. {{ $loop->iteration }}</h1>
                        <span>PG</span>
                    </div>

                    <div class="exam-player-prompt">
                        {!! nl2br(e($question->prompt)) !!}
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

                    <div class="exam-player-options">
                        @foreach ($question->options as $option)
                            @php($optionLabel = chr(65 + $loop->index))
                            <label class="exam-player-option">
                                <input
                                    type="radio"
                                    name="answers[{{ $question->id }}]"
                                    value="{{ $option->id }}"
                                    class="exam-player-radio"
                                    @checked((string) ($savedAnswers[$question->id] ?? '') === (string) $option->id)
                                >
                                <span class="exam-player-radio-mark"></span>
                                <span class="exam-player-option-letter">{{ $optionLabel }}.</span>
                                <span class="exam-player-option-text">{{ $option->option_text }}</span>
                            </label>
                        @endforeach
                    </div>
                </article>
            @endforeach

            <div class="exam-player-actions">
                <button type="button" class="exam-player-action exam-player-action-prev" data-question-nav-prev>
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="m15 6-6 6 6 6" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span data-question-prev-label>Sebelumnya</span>
                </button>
                <button type="button" class="exam-player-action exam-player-action-doubt" data-question-doubt-toggle>
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M9.2 9a3 3 0 1 1 4.7 2.5c-1.1.7-1.9 1.3-1.9 2.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <path d="M12 18h.01" stroke="currentColor" stroke-width="2.8" stroke-linecap="round"/>
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    Ragu
                </button>
                <button type="button" class="exam-player-action exam-player-action-next" data-question-nav-next>
                    <span data-question-next-label>Selanjutnya</span>
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="m9 6 6 6-6 6" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        </form>

        <div
            data-violation-lock-overlay
            class="exam-violation-lock {{ $attempt->isLocked() ? 'is-open' : '' }}"
            aria-live="assertive"
            aria-modal="true"
            role="dialog"
        >
            <div class="exam-violation-lock-card">
                <div class="exam-violation-lock-strip"></div>
                <div class="exam-violation-lock-icon">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 8v5M12 16.5h.01" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/>
                        <path d="M10.1 4.4 3.8 15.3A2.4 2.4 0 0 0 5.9 19h12.2a2.4 2.4 0 0 0 2.1-3.7L13.9 4.4a2.2 2.2 0 0 0-3.8 0Z" fill="currentColor"/>
                    </svg>
                </div>
                <h2>Pelanggaran Ujian!</h2>
                <p class="exam-violation-lock-reason">
                    Alasan: <span data-violation-lock-reason>{{ $attempt->locked_reason ?: 'Terdeteksi aktivitas yang melanggar aturan ujian.' }}</span>
                </p>
                <div class="exam-violation-lock-message">
                    Sistem mendeteksi aktivitas yang melanggar aturan ujian.
                    <strong>Ujian dihentikan sementara.</strong>
                    Hubungi guru agar sesi Anda dibuka kembali dari monitoring guru.
                </div>
                <button type="button" class="exam-violation-lock-button" data-violation-lock-refresh>
                    Cek status dari guru
                </button>
            </div>
        </div>

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
