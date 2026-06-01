<div id="question-insights" class="dashboard-card p-6 sm:p-7">
    <x-ui.dashboard-header
        eyebrow="Analitik Soal"
        title="Ringkasan performa setiap soal"
        description="Lihat soal mana yang paling banyak dijawab benar atau salah, lalu buka detailnya untuk memeriksa jawaban semua siswa per nomor soal."
    >
        <x-slot:aside>
            <x-ui.dashboard-pill>{{ $questionInsightSummary['participants'] }} peserta dianalisis</x-ui.dashboard-pill>
        </x-slot:aside>
    </x-ui.dashboard-header>

    <div class="mt-6 grid gap-4 xl:grid-cols-3">
        <div class="dashboard-muted-card p-5">
            <p class="dashboard-kicker">Paling Banyak Benar</p>
            @if ($questionInsightSummary['most_correct'])
                <h3 class="mt-2 text-lg font-bold text-slate-900">Soal {{ $questionInsightSummary['most_correct']['position'] }}</h3>
                <p class="mt-2 text-sm text-slate-500">
                    {{ $questionInsightSummary['most_correct']['correct_count'] }} siswa benar
                    ({{ number_format($questionInsightSummary['most_correct']['correct_percentage'], 1) }}%)
                </p>
            @else
                <p class="mt-2 text-sm text-slate-500">Belum ada jawaban siswa untuk dianalisis.</p>
            @endif
        </div>

        <div class="dashboard-muted-card p-5">
            <p class="dashboard-kicker">Paling Banyak Salah</p>
            @if ($questionInsightSummary['most_wrong'])
                <h3 class="mt-2 text-lg font-bold text-slate-900">Soal {{ $questionInsightSummary['most_wrong']['position'] }}</h3>
                <p class="mt-2 text-sm text-slate-500">
                    {{ $questionInsightSummary['most_wrong']['wrong_count'] }} siswa salah
                    ({{ number_format($questionInsightSummary['most_wrong']['wrong_percentage'], 1) }}%)
                </p>
            @else
                <p class="mt-2 text-sm text-slate-500">Belum ada jawaban siswa untuk dianalisis.</p>
            @endif
        </div>

        <div class="dashboard-muted-card p-5">
            <p class="dashboard-kicker">Acuan Persentase</p>
            <h3 class="mt-2 text-lg font-bold text-slate-900">{{ $questionInsightSummary['participants'] }} peserta</h3>
            <p class="mt-2 text-sm text-slate-500">
                Persentase benar, salah, dan kosong dihitung dari jumlah peserta yang sudah mulai atau mengumpulkan ujian.
            </p>
        </div>
    </div>

    <div class="mt-6 flex flex-wrap items-end justify-between gap-4 rounded-[1.5rem] border border-slate-200 bg-white/80 p-4">
        <div>
            <p class="dashboard-kicker">Urutkan Analitik</p>
            <p class="mt-2 text-sm text-slate-500">
                Tampilkan dulu soal tersulit, termudah, atau urutan lain sesuai fokus review Anda.
            </p>
        </div>

        <form method="GET" action="{{ route('teacher.exams.show', $exam) }}" class="flex flex-wrap items-end gap-3">
            <div class="min-w-[15rem] space-y-2">
                <label for="insight_sort" class="text-sm font-semibold text-slate-600">Urutan soal</label>
                <select
                    id="insight_sort"
                    name="insight_sort"
                    class="dashboard-input py-2.5"
                    data-insight-sort-select
                    data-insight-sort-url="{{ route('teacher.exams.show', $exam) }}"
                    data-insight-sort-anchor="question-insights"
                >
                    <option value="default" @selected($questionInsightSort === 'default')>Urutan nomor soal</option>
                    <option value="hardest" @selected($questionInsightSort === 'hardest')>Soal tersulit dulu</option>
                    <option value="easiest" @selected($questionInsightSort === 'easiest')>Soal termudah dulu</option>
                    <option value="most_wrong" @selected($questionInsightSort === 'most_wrong')>Paling banyak salah</option>
                    <option value="most_correct" @selected($questionInsightSort === 'most_correct')>Paling banyak benar</option>
                </select>
            </div>
        </form>
    </div>

    <x-ui.scroll-panel height="36rem" class="mt-6" content-class="space-y-4 p-2 pr-3" :content-attributes="['data-insight-list' => 'true']">
            @forelse ($questionInsights as $insight)
                <details
                    class="dashboard-muted-card insight-sort-item overflow-hidden p-5"
                    data-insight-item
                    data-position="{{ $insight['position'] }}"
                    data-correct-count="{{ $insight['correct_count'] }}"
                    data-wrong-count="{{ $insight['wrong_count'] }}"
                    data-correct-percentage="{{ $insight['correct_percentage'] }}"
                    data-wrong-percentage="{{ $insight['wrong_percentage'] }}"
                    data-unanswered-percentage="{{ $insight['unanswered_percentage'] }}"
                >
                    <summary class="cursor-pointer list-none">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <x-ui.dashboard-pill>Soal {{ $insight['position'] }}</x-ui.dashboard-pill>
                                    <x-ui.dashboard-pill>{{ $insight['points'] }} poin</x-ui.dashboard-pill>
                                </div>
                                <h3 class="mt-3 text-lg font-bold leading-8 text-slate-900">{!! nl2br(e($insight['prompt'])) !!}</h3>
                                <p class="mt-2 text-sm text-slate-500">
                                    Kunci: {{ $insight['correct_option_position'] ? chr(64 + (int) $insight['correct_option_position']).'. ' : '' }}{{ $insight['correct_option'] ?? 'Belum diatur' }}
                                </p>
                            </div>

                            <div class="w-full max-w-xl space-y-3 lg:w-[25rem]">
                                <div>
                                    <div class="mb-2 flex items-center justify-between text-sm text-slate-600">
                                        <span>Benar</span>
                                        <span>{{ $insight['correct_count'] }} siswa | {{ number_format($insight['correct_percentage'], 1) }}%</span>
                                    </div>
                                    <div class="insight-progress-track">
                                        <div class="insight-progress-fill insight-progress-fill-correct" style="width: {{ min($insight['correct_percentage'], 100) }}%"></div>
                                    </div>
                                </div>

                                <div>
                                    <div class="mb-2 flex items-center justify-between text-sm text-slate-600">
                                        <span>Salah</span>
                                        <span>{{ $insight['wrong_count'] }} siswa | {{ number_format($insight['wrong_percentage'], 1) }}%</span>
                                    </div>
                                    <div class="insight-progress-track">
                                        <div class="insight-progress-fill insight-progress-fill-wrong" style="width: {{ min($insight['wrong_percentage'], 100) }}%"></div>
                                    </div>
                                </div>

                                <div>
                                    <div class="mb-2 flex items-center justify-between text-sm text-slate-600">
                                        <span>Kosong</span>
                                        <span>{{ $insight['unanswered_count'] }} siswa | {{ number_format($insight['unanswered_percentage'], 1) }}%</span>
                                    </div>
                                    <div class="insight-progress-track">
                                        <div class="insight-progress-fill insight-progress-fill-empty" style="width: {{ min($insight['unanswered_percentage'], 100) }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 flex items-center justify-end text-sm font-semibold text-sky-600">
                            Klik untuk lihat jawaban semua siswa
                        </div>
                    </summary>

                    <div class="mt-5 overflow-hidden rounded-[1.5rem] border border-slate-200 bg-white">
                        <div class="flex flex-wrap items-end justify-between gap-3 border-b border-slate-200 bg-slate-50 px-4 py-4">
                            <div>
                                <p class="text-sm font-semibold text-slate-700">Filter jawaban siswa</p>
                                <p class="mt-1 text-xs text-slate-500">Pilih status jawaban yang ingin ditampilkan untuk soal ini.</p>
                            </div>
                            <div class="min-w-[13rem]">
                                <select class="dashboard-input py-2.5 text-sm" data-insight-response-filter>
                                    <option value="all">Semua jawaban</option>
                                    <option value="wrong">Hanya siswa yang salah</option>
                                    <option value="correct">Hanya siswa yang benar</option>
                                    <option value="empty">Hanya yang kosong</option>
                                </select>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-[760px] w-full divide-y divide-slate-200 text-left text-sm sm:min-w-full">
                                <thead class="bg-slate-50 text-slate-500">
                                    <tr>
                                        <th class="px-4 py-3">Siswa</th>
                                        <th class="px-4 py-3">Jawaban</th>
                                        <th class="px-4 py-3">Status</th>
                                        <th class="px-4 py-3">Poin</th>
                                        <th class="px-4 py-3">Waktu</th>
                                        <th class="px-4 py-3">Submit</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 text-slate-700">
                                    @forelse ($insight['responses'] as $response)
                                        <tr
                                            data-insight-response-row
                                            data-response-status="{{ $response['status'] === 'Benar' ? 'correct' : ($response['status'] === 'Salah' ? 'wrong' : 'empty') }}"
                                        >
                                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $response['student_name'] }}</td>
                                            <td class="px-4 py-3">
                                                @if ($response['selected_option'])
                                                    {{ $response['selected_option_position'] ? chr(64 + (int) $response['selected_option_position']).'. ' : '' }}{{ $response['selected_option'] }}
                                                @else
                                                    <span class="text-slate-400">Belum dijawab</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $response['status'] === 'Benar' ? 'bg-emerald-50 text-emerald-700' : ($response['status'] === 'Salah' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">
                                                    {{ $response['status'] }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3">{{ number_format($response['points_awarded'], 2) }}</td>
                                            <td class="px-4 py-3">{{ $response['time_spent'] }}</td>
                                            <td class="px-4 py-3">{{ $response['submit_status'] }}</td>
                                        </tr>
                                    @empty
                                        <tr data-insight-empty-state>
                                            <td colspan="6" class="px-4 py-4 text-sm text-slate-500">
                                                Belum ada jawaban siswa untuk soal ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                    @if ($insight['responses']->isNotEmpty())
                                        <tr data-insight-filter-empty class="hidden">
                                            <td colspan="6" class="px-4 py-4 text-sm text-slate-500">
                                                Tidak ada siswa yang cocok dengan filter ini untuk soal tersebut.
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </details>
            @empty
                <div class="dashboard-muted-card p-5 text-sm text-slate-500">
                    Tambahkan soal dan tunggu siswa mulai mengerjakan agar analitik per soal muncul di sini.
                </div>
            @endforelse
    </x-ui.scroll-panel>
</div>
