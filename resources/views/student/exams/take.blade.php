<x-layouts.app :title="$attempt->exam->title" :hide-auth-nav="true" variant="light" :show-theme-toggle="true">
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
        data-violations-enabled="{{ $attempt->exam->violations_enabled ? '1' : '0' }}"
        data-expires-at="{{ optional($attempt->expiresAt())->toIso8601String() }}"
        data-total-questions="{{ $attempt->exam->questions->count() }}"
    >
        <div class="exam-hero exam-hero-danger" data-exam-hero>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="exam-kicker">Mode ujian aktif</p>
                    <h1 class="exam-title">{{ $attempt->exam->title }}</h1>
                </div>
            </div>
            <p class="exam-copy">
                @if ($attempt->exam->violations_enabled)
                    Jangan pindah tab, keluar dari full-screen, atau membuka aplikasi lain. Sistem akan membunyikan alarm, mencatat pelanggaran, dan bisa mengirim submit otomatis bila batas pelanggaran terlampaui.
                @else
                    Mode ujian fleksibel sedang aktif. Siswa tetap mengerjakan dari halaman ini, tetapi perpindahan tab atau keluar dari full-screen tidak akan dicatat sebagai pelanggaran.
                @endif
            </p>
            <div class="mt-4 flex flex-wrap gap-2">
                <span class="exam-chip exam-chip-neutral">{{ $attempt->participantName() }}</span>
                @if ($attempt->student_identifier)
                    <span class="exam-chip exam-chip-neutral">{{ $attempt->student_identifier }}</span>
                @endif
                <span class="exam-chip exam-chip-neutral">{{ $attempt->exam->subject->display_name }}</span>
                <span class="exam-chip exam-chip-warning">{{ $attempt->exam->duration_minutes }} menit</span>
                @if ($attempt->exam->violations_enabled)
                    <span class="exam-chip exam-chip-warning">Batas pelanggaran {{ $attempt->exam->max_violations }}</span>
                @else
                    <span class="exam-chip exam-chip-info">Pelanggaran dinonaktifkan</span>
                @endif
            </div>
        </div>

        <div class="exam-session-dock" data-exam-session-dock>
            <div class="exam-session-dock-stack" data-exam-session-dock-stack>
                <div class="exam-timer-dock">
                    <div class="space-y-2">
                        <p class="exam-kicker exam-dock-label">Sisa waktu ujian</p>
                        <span class="exam-chip exam-chip-warning exam-chip-countdown">
                            <span data-countdown>Waktu sedang dimuat...</span>
                        </span>
                    </div>
                    <div class="exam-timer-meta flex flex-wrap items-center gap-2 text-xs sm:text-sm">
                        <span class="exam-chip exam-chip-neutral">
                            {{ $attempt->exam->duration_minutes }} menit
                        </span>
                        @if ($attempt->exam->violations_enabled)
                            <span class="exam-chip exam-chip-neutral">
                                Maks pelanggaran {{ $attempt->exam->max_violations }}
                            </span>
                        @else
                            <span class="exam-chip exam-chip-info">
                                Mode fleksibel aktif
                            </span>
                        @endif
                    </div>
                </div>

                <div class="exam-question-nav-dock" data-question-nav>
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="exam-kicker exam-dock-label">Navigasi soal</p>
                            <p class="text-sm exam-dock-copy" style="color: var(--ui-copy);">
                                Lompat langsung ke nomor soal yang ingin dicek kembali.
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 text-xs">
                            <span class="exam-chip exam-chip-neutral">
                                {{ $attempt->exam->questions->count() }} soal
                            </span>
                            <span class="exam-chip exam-chip-info exam-dock-hint">
                                Nomor terjawab akan ikut ditandai
                            </span>
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap items-center justify-between gap-3 exam-dock-actions">
                        <div class="flex flex-wrap items-center gap-2">
                            <button type="button" class="dashboard-button-return exam-question-nav-action px-4 py-2 text-xs" data-question-nav-prev>
                                Sebelumnya
                            </button>
                            <button type="button" class="dashboard-button-soft exam-question-nav-action px-4 py-2 text-xs" data-question-nav-next>
                                Berikutnya
                            </button>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="text-xs exam-dock-copy" style="color: var(--ui-copy);">
                                Gunakan nomor, atau tombol sebelumnya dan berikutnya.
                            </p>
                            <button
                                type="button"
                                class="dashboard-button-soft exam-question-nav-toggle px-3 py-2 text-[0.7rem] sm:hidden"
                                data-question-nav-toggle
                                aria-expanded="false"
                            >
                                <span data-question-nav-toggle-label>Tampilkan nomor</span>
                            </button>
                        </div>
                    </div>

                    <div class="exam-question-nav-track" data-question-nav-track>
                        @foreach ($attempt->exam->questions as $question)
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
            </div>
        </div>

        <form method="POST" action="{{ route('exam.attempts.submit', $attempt) }}" id="exam-form" class="space-y-5">
            @csrf
            @foreach ($attempt->exam->questions as $question)
                <article class="exam-card scroll-mt-80" id="question-{{ $question->id }}" data-question-card data-question-id="{{ $question->id }}">
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
