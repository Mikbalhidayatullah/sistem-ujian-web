@php
    $defaultBuilderTab = $errors->has('question_template') || $errors->has('default_points') ? 'quick' : 'manual';
@endphp

<div class="dashboard-card p-6 sm:p-7" data-question-builder data-default-tab="{{ $defaultBuilderTab }}">
    <x-ui.dashboard-header
        eyebrow="Input Soal"
        title="Tambah soal ke ujian ini"
        description="Pilih mode input yang paling nyaman. Anda bisa mengetik satu soal manual atau paste banyak soal sekaligus dari template."
    >
        <x-slot:aside>
            <div class="flex flex-wrap gap-3">
                <button
                    type="button"
                    class="dashboard-button-primary"
                    data-question-builder-tab="manual"
                    aria-pressed="{{ $defaultBuilderTab === 'manual' ? 'true' : 'false' }}"
                >
                    Input Manual
                </button>
                <button
                    type="button"
                    class="dashboard-button-soft"
                    data-question-builder-tab="quick"
                    aria-pressed="{{ $defaultBuilderTab === 'quick' ? 'true' : 'false' }}"
                >
                    Input Cepat
                </button>
            </div>
        </x-slot:aside>
    </x-ui.dashboard-header>

    <div class="mt-6 space-y-6">
        <div data-question-builder-panel="manual" @class(['hidden' => $defaultBuilderTab !== 'manual'])>
            <form method="POST" action="{{ route('teacher.exams.questions.store', $exam) }}" enctype="multipart/form-data" class="mt-5 space-y-5">
                @csrf

                <div class="space-y-4">
                    <div class="dashboard-muted-card p-5">
                        <p class="dashboard-kicker">Mode Manual</p>
                        <h3 class="mt-2 text-xl font-bold text-slate-900">Tambah satu soal baru</h3>
                        <p class="mt-3 text-sm leading-7 text-slate-500">
                            Isi pertanyaan, atur poin, lalu pilih satu jawaban yang benar. Opsi E boleh dikosongkan jika tidak dipakai.
                        </p>
                    </div>

                    <div class="grid gap-2 text-xs sm:flex sm:flex-wrap">
                        <x-ui.dashboard-pill>4-5 opsi jawaban</x-ui.dashboard-pill>
                        <x-ui.dashboard-pill tone="slate">Urutan awal: {{ old('position', $exam->questions->count() + 1) }}</x-ui.dashboard-pill>
                        <x-ui.dashboard-pill tone="info">Mendukung gambar atau video</x-ui.dashboard-pill>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-600">Pertanyaan</label>
                        <textarea name="prompt" rows="5" class="dashboard-input" placeholder="Tulis soal pilihan ganda di sini..." required>{{ old('prompt') }}</textarea>
                    </div>

                    <div class="grid gap-4 md:grid-cols-[1fr_10rem_10rem]">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-600">Media</label>
                            <input type="file" name="media" accept="image/*,video/*" class="dashboard-input file:mr-4 file:rounded-full file:border-0 file:bg-sky-100 file:px-4 file:py-2 file:font-semibold file:text-slate-700">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-600">Poin</label>
                            <input type="number" min="1" max="100" name="points" class="dashboard-input" value="{{ old('points', 10) }}" required>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-600">Urutan</label>
                            <input type="number" min="1" name="position" class="dashboard-input" value="{{ old('position', $exam->questions->count() + 1) }}">
                        </div>
                    </div>

                    <div class="border-t border-slate-200 pt-4">
                        <p class="text-sm font-semibold text-slate-600">Opsi jawaban</p>
                        <div class="mt-3 grid gap-3 md:grid-cols-2">
                            @for ($i = 0; $i < 5; $i++)
                                <label class="question-option-editor dashboard-muted-card p-4" data-question-option-editor>
                                    <span class="flex items-center justify-between text-sm font-semibold text-slate-700">
                                        Opsi {{ chr(65 + $i) }}
                                        <span class="flex items-center gap-2 text-xs text-sky-600">
                                            <input
                                                type="radio"
                                                name="correct_option"
                                                value="{{ $i }}"
                                                data-question-option-radio
                                                @checked(old('correct_option', 0) == $i)
                                            >
                                            Benar
                                        </span>
                                    </span>
                                    <input
                                        type="text"
                                        name="options[]"
                                        class="dashboard-input mt-3"
                                        value="{{ old('options.'.$i) }}"
                                        placeholder="{{ $i === 4 ? 'Opsional, isi jika butuh jawaban E' : '' }}"
                                        @required($i < 4)
                                    >
                                </label>
                            @endfor
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 pt-4">
                    <p class="text-xs leading-5 text-slate-500">
                        Setelah disimpan, soal baru akan langsung muncul di daftar `Bank Soal`.
                    </p>
                    <button type="submit" class="dashboard-button-primary">
                        Tambah soal
                    </button>
                </div>
            </form>
        </div>

        <div data-question-builder-panel="quick" @class(['hidden' => $defaultBuilderTab !== 'quick'])>
            <form method="POST" action="{{ route('teacher.exams.questions.import-template', $exam) }}" class="mt-5 space-y-5">
                @csrf
                <div class="grid gap-5 xl:grid-cols-[0.9fr_1.1fr]">
                    <div class="space-y-5">
                        <div class="dashboard-muted-card p-5">
                            <p class="dashboard-kicker">Mode Cepat</p>
                            <h3 class="mt-2 text-xl font-bold text-slate-900">Template soal otomatis</h3>
                            <p class="mt-3 text-sm leading-7 text-slate-500">
                                Kalau soal sudah ada di Word atau Excel, Anda bisa copy lalu paste ke format ini agar sistem membuat banyak soal sekaligus.
                            </p>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-600">Poin default per soal</label>
                            <input type="number" min="1" max="100" name="default_points" class="dashboard-input" value="{{ old('default_points', 10) }}" required>
                            @error('default_points')
                                <p class="text-sm text-rose-500">{{ $message }}</p>
                            @enderror
                            <p class="text-xs leading-6 text-slate-500">
                                Nilai ini dipakai jika pada blok soal tidak ada baris <span class="font-semibold text-sky-600">Poin:</span>.
                            </p>
                        </div>

                        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-4 text-xs leading-6 text-slate-600">
                            <p class="font-semibold uppercase tracking-[0.2em] text-sky-600">Format template</p>
                            <pre class="mt-3 overflow-x-auto whitespace-pre-wrap font-mono text-[11px] leading-6 text-slate-600">Soal: Ibukota Indonesia adalah?
A. Jakarta
B. Bandung
C. Surabaya
D. Medan
E. Bogor
Jawaban: A
Poin: 10

Soal: 5 x 6 = ...
A. 25
B. 30
C. 35
D. 40
Jawaban: B</pre>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-600">Template soal</label>
                        <textarea
                            name="question_template"
                            rows="18"
                            class="dashboard-input font-mono text-sm"
                            placeholder="Paste beberapa soal sekaligus di sini..."
                            required
                        >{{ old('question_template') }}</textarea>
                        @error('question_template')
                            <p class="text-sm text-rose-500">{{ $message }}</p>
                        @enderror
                        <p class="text-xs leading-6 text-slate-500">
                            Pisahkan setiap soal dengan satu baris kosong. Gunakan opsi A sampai D, atau tambahkan E bila diperlukan, lalu akhiri dengan baris <span class="font-semibold text-sky-600">Jawaban:</span> atau <span class="font-semibold text-sky-600">Kunci:</span>.
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 pt-4">
                    <p class="text-xs leading-5 text-slate-500">
                        Mode ini cocok saat Anda ingin mengimpor banyak soal sekaligus dalam satu tempel.
                    </p>
                    <button type="submit" class="dashboard-button-soft">
                        Buat soal otomatis dari template
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
