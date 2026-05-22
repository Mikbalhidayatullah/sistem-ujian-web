<x-layouts.dashboard :title="'Bank Soal | Sistem Ujian'">
    <section class="space-y-8">
        <div class="dashboard-card p-6 sm:p-7">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="max-w-3xl">
                    <p class="dashboard-kicker">Question Bank</p>
                    <h1 class="dashboard-section-title mt-3 text-3xl sm:text-4xl">
                        Bank soal per mata pelajaran.
                    </h1>
                    <p class="dashboard-copy mt-4 max-w-2xl">
                        Semua soal yang pernah Anda simpan dari detail ujian akan terkumpul di sini. Soal-soal ini bisa dipanggil lagi saat Anda membuka detail ujian dengan mapel yang sama.
                    </p>
                </div>

                <form method="GET" action="{{ route('teacher.question-bank.index') }}" class="flex flex-wrap items-center gap-3">
                    <label class="text-sm font-semibold text-slate-600">Filter mapel</label>
                    <select name="subject" class="dashboard-select min-w-56">
                        <option value="">Semua mata pelajaran</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}" @selected(optional($selectedSubject)->id === $subject->id)>
                                {{ $subject->display_name }} ({{ $subject->question_banks_count }})
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="dashboard-button-soft">Terapkan</button>
                </form>
            </div>

            <div class="mt-6 flex flex-wrap gap-2 text-xs">
                <x-ui.dashboard-pill>{{ $questionBanks->total() }} soal tersimpan</x-ui.dashboard-pill>
                @if ($selectedSubject)
                    <x-ui.dashboard-pill tone="info">Mapel aktif: {{ $selectedSubject->display_name }}</x-ui.dashboard-pill>
                @endif
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            @forelse ($questionBanks as $questionBank)
                <div class="dashboard-card p-6">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="space-y-2">
                            <p class="dashboard-kicker">{{ $questionBank->subject->display_name }}</p>
                            <h2 class="text-lg font-bold text-slate-900">{{ $questionBank->prompt }}</h2>
                            <div class="flex flex-wrap gap-2 text-xs">
                                <x-ui.dashboard-pill>{{ $questionBank->points }} poin</x-ui.dashboard-pill>
                                @if ($questionBank->source_exam_title)
                                    <x-ui.dashboard-pill tone="slate">
                                        Asal: {{ $questionBank->source_exam_title }}{{ $questionBank->source_question_position ? ' · soal '.$questionBank->source_question_position : '' }}
                                    </x-ui.dashboard-pill>
                                @endif
                            </div>
                        </div>

                        <form method="POST" action="{{ route('teacher.question-bank.destroy', $questionBank) }}" onsubmit="return confirm('Hapus soal ini dari bank soal mapel?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dashboard-button-danger px-4 py-2 text-xs">
                                Hapus
                            </button>
                        </form>
                    </div>

                    @if ($questionBank->hasMedia())
                        <div class="mt-4 rounded-[1.25rem] border border-slate-200 bg-white p-3 text-xs font-semibold text-slate-500">
                            Soal ini menyimpan media dan akan ikut terbawa saat diimpor ke ujian.
                        </div>
                    @endif

                    <div class="mt-4 grid gap-3 md:grid-cols-2">
                        @foreach ($questionBank->options as $option)
                            <div class="rounded-[1.25rem] border {{ $option->is_correct ? 'border-emerald-200 bg-emerald-50' : 'border-slate-200 bg-white' }} p-4 text-sm text-slate-700">
                                {{ chr(64 + $option->position) }}. {{ $option->option_text }}
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="dashboard-card p-6 text-sm text-slate-500 lg:col-span-2">
                    Belum ada soal di bank soal. Buka detail ujian lalu gunakan tombol <span class="font-semibold text-slate-700">Simpan ke bank soal</span> pada salah satu soal.
                </div>
            @endforelse
        </div>

        @if ($questionBanks->hasPages())
            <div class="dashboard-card p-4">
                {{ $questionBanks->links() }}
            </div>
        @endif
    </section>
</x-layouts.dashboard>
