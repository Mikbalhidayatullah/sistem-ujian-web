<div class="dashboard-card p-6 sm:p-7">
    <x-ui.dashboard-header eyebrow="Bank Soal" title="Daftar soal">
        <x-slot:aside>
            <div class="flex flex-wrap items-center gap-3">
                <x-ui.dashboard-pill>{{ $exam->questions->count() }} soal</x-ui.dashboard-pill>
                @if ($exam->questions->isNotEmpty())
                    <div class="flex flex-wrap items-center gap-2">
                        <form id="default-question-points-form" method="POST" action="{{ route('teacher.exams.questions.default-points.update', $exam) }}" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Poin default</label>
                            <input
                                type="number"
                                min="1"
                                max="100"
                                name="default_points"
                                value="{{ old('default_points', $exam->questions->first()->points ?? 10) }}"
                                class="dashboard-input h-10 w-14 px-2 py-2 text-center text-sm"
                                required
                            >
                        </form>
                        <button type="submit" form="default-question-points-form" class="dashboard-button-soft px-4 py-2 text-xs">
                            Simpan
                        </button>
                        <form method="POST" action="{{ route('teacher.exams.questions.destroy-all', $exam) }}" onsubmit="return confirm('Hapus semua soal pada bank soal ini? Semua jawaban siswa untuk soal-soal ini akan ikut dihapus.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dashboard-button-danger px-4 py-2 text-xs">
                                Hapus semua soal
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </x-slot:aside>
    </x-ui.dashboard-header>

    <x-ui.scroll-panel height="28rem" class="mt-6" content-class="space-y-4 p-2 pr-3">
            @forelse ($exam->questions as $question)
                <div class="dashboard-muted-card p-5">
                    @php
                        $isEditing = (string) old('editing_question_id') === (string) $question->id;
                    @endphp

                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="dashboard-kicker">Soal {{ $question->position }}</p>
                            <h3 class="mt-2 text-lg font-bold text-slate-900">{!! nl2br(e($question->prompt)) !!}</h3>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <x-ui.dashboard-pill>{{ $question->points }} poin</x-ui.dashboard-pill>
                            <button
                                type="button"
                                class="{{ $isEditing ? 'dashboard-button-return' : 'dashboard-button-soft' }} px-4 py-2 text-xs"
                                data-question-editor-toggle
                                aria-expanded="{{ $isEditing ? 'true' : 'false' }}"
                            >
                                {{ $isEditing ? 'Tutup editor' : 'Edit soal' }}
                            </button>
                        </div>
                    </div>

                    @if ($question->hasMedia())
                        <div class="mt-4 overflow-hidden rounded-[1.25rem] border border-slate-200 bg-white p-3">
                            @if ($question->media_type === 'image')
                                <img src="{{ route('questions.media', $question) }}" alt="Media soal" class="max-h-80 w-full rounded-xl object-contain">
                            @else
                                <video controls class="max-h-80 w-full rounded-xl">
                                    <source src="{{ route('questions.media', $question) }}">
                                </video>
                            @endif
                        </div>
                    @endif

                    <div class="mt-4 grid gap-3 md:grid-cols-2">
                        @foreach ($question->options as $option)
                            <div class="rounded-[1.25rem] border {{ $option->is_correct ? 'border-emerald-200 bg-emerald-50' : 'border-slate-200 bg-white' }} p-4 text-sm text-slate-700">
                                {{ $option->position }}. {{ $option->option_text }}
                            </div>
                        @endforeach
                    </div>

                    <div class="question-editor-panel mt-4 {{ $isEditing ? 'is-open' : '' }}" data-question-editor-panel data-open="{{ $isEditing ? 'true' : 'false' }}">
                        <div class="rounded-[1.25rem] border border-slate-200 bg-white p-4">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-sky-600">Editor soal</p>
                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Ubah isi soal, posisi, opsi jawaban, dan media tanpa pindah halaman.
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    class="dashboard-button-return px-4 py-2 text-xs"
                                    data-question-editor-close
                                >
                                    Tutup
                                </button>
                            </div>

                            <div class="mt-4 flex flex-wrap gap-2 text-xs">
                                <x-ui.dashboard-pill>{{ $question->options->count() }} opsi</x-ui.dashboard-pill>
                                @if ($question->hasMedia())
                                    <x-ui.dashboard-pill tone="info">
                                        {{ $question->media_type === 'video' ? 'Media video aktif' : 'Media gambar aktif' }}
                                    </x-ui.dashboard-pill>
                                @endif
                                <x-ui.dashboard-pill tone="slate">
                                    Posisi saat ini: {{ $question->position }}
                                </x-ui.dashboard-pill>
                            </div>

                            <form method="POST" action="{{ route('teacher.exams.questions.update', [$exam, $question]) }}" class="mt-4 space-y-4" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="editing_question_id" value="{{ $question->id }}">

                            <div class="space-y-4">
                                <div class="grid gap-4 md:grid-cols-[1fr_10rem_9rem]">
                                    <div class="space-y-2 md:col-span-3">
                                        <label class="text-sm font-semibold text-slate-600">Judul soal</label>
                                        <textarea name="prompt" rows="4" class="dashboard-input" required>{{ old('editing_question_id') == $question->id ? old('prompt') : $question->prompt }}</textarea>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-sm font-semibold text-slate-600">Poin</label>
                                        <input
                                            type="number"
                                            min="1"
                                            max="100"
                                            name="points"
                                            class="dashboard-input"
                                            value="{{ old('editing_question_id') == $question->id ? old('points', $question->points) : $question->points }}"
                                            required
                                        >
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-sm font-semibold text-slate-600">Posisi</label>
                                        <input
                                            type="number"
                                            min="1"
                                            max="{{ $exam->questions->count() }}"
                                            name="position"
                                            class="dashboard-input"
                                            value="{{ old('editing_question_id') == $question->id ? old('position', $question->position) : $question->position }}"
                                            required
                                        >
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-slate-600">Ganti media soal</label>
                                    <input
                                        type="file"
                                        name="media"
                                        accept="image/jpeg,image/png,image/webp,video/mp4,video/webm,video/ogg"
                                        class="dashboard-input"
                                    >
                                    <p class="text-xs leading-5 text-slate-500">
                                        Kosongkan bila tidak ingin mengganti media. Mendukung gambar dan video hingga 20 MB.
                                    </p>
                                </div>

                                @if ($question->hasMedia())
                                    <label class="inline-flex items-center gap-2 rounded-full border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">
                                        <input
                                            type="checkbox"
                                            name="remove_media"
                                            value="1"
                                            @checked(old('editing_question_id') == $question->id && old('remove_media'))
                                        >
                                        Hapus media lama
                                    </label>
                                @endif

                                <div class="border-t border-slate-200 pt-4">
                                    <p class="text-sm font-semibold text-slate-600">Opsi jawaban</p>
                                    <div class="mt-3 grid gap-3 md:grid-cols-2">
                                        @foreach ($question->options as $optionIndex => $option)
                                            <label
                                                class="question-option-editor rounded-[1.25rem] border border-slate-200 bg-slate-50 p-4"
                                                data-question-option-editor
                                            >
                                                <span class="flex items-center justify-between text-sm font-semibold text-slate-700">
                                                    Opsi {{ chr(65 + $optionIndex) }}
                                                    <span class="flex items-center gap-2 text-xs text-sky-600">
                                                        <input
                                                            type="radio"
                                                            name="correct_option"
                                                            value="{{ $optionIndex }}"
                                                            data-question-option-radio
                                                            @checked((string) (old('editing_question_id') == $question->id ? old('correct_option', $question->options->search(fn ($item) => $item->is_correct)) : $question->options->search(fn ($item) => $item->is_correct)) === (string) $optionIndex)
                                                        >
                                                        Benar
                                                    </span>
                                                </span>
                                                <input
                                                    type="text"
                                                    name="options[]"
                                                    class="dashboard-input mt-3"
                                                    value="{{ old('editing_question_id') == $question->id ? old('options.'.$optionIndex) : $option->option_text }}"
                                                    required
                                                >
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                @if ($exam->attempts->isNotEmpty())
                                    <p class="text-xs leading-6 text-amber-700">
                                        Perubahan kunci jawaban akan langsung memperbarui hasil dan nilai siswa yang sudah mengerjakan ujian ini.
                                    </p>
                                @endif
                            </div>

                                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 pt-4">
                                    <p class="text-xs leading-5 text-slate-500">
                                        Simpan perubahan jika isi soal, kunci jawaban, atau posisinya sudah sesuai.
                                    </p>
                                    <div class="flex flex-wrap items-center gap-3">
                                        <button type="submit" class="dashboard-button-primary">
                                            Simpan perubahan soal
                                        </button>
                                        <button
                                            type="submit"
                                            form="delete-question-{{ $question->id }}"
                                            class="dashboard-button-danger"
                                        >
                                            Hapus soal
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <form
                                id="delete-question-{{ $question->id }}"
                                method="POST"
                                action="{{ route('teacher.exams.questions.destroy', [$exam, $question]) }}"
                                class="hidden"
                                onsubmit="return confirm('Hapus soal ini? Jawaban dan pengaruh nilainya akan ikut diperbarui.')"
                            >
                                @csrf
                                @method('DELETE')
                            </form>

                        </div>
                    </div>
                </div>
            @empty
                <p class="dashboard-muted-card p-5 text-sm text-slate-500">
                    Belum ada soal. Tambahkan soal pertama Anda di formulir atas.
                </p>
            @endforelse
    </x-ui.scroll-panel>
</div>


