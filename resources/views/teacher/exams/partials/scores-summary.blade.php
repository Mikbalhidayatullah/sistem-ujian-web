<div class="dashboard-card p-6 sm:p-7">
    <x-ui.dashboard-header eyebrow="Nilai Otomatis" title="Rekapan nilai siswa">
        <x-slot:aside>
            <div class="flex flex-wrap items-center gap-3">
                <x-ui.dashboard-pill>{{ $exam->attempts->count() }} peserta</x-ui.dashboard-pill>
                @if ($exam->attempts->isNotEmpty())
                    <form method="POST" action="{{ route('teacher.exams.attempts.destroy-all', $exam) }}" onsubmit="return confirm('Hapus semua data peserta untuk ujian ini? Jawaban, nilai, dan pelanggaran peserta akan ikut dihapus.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="dashboard-button-danger px-4 py-2 text-xs">
                            Hapus semua peserta
                        </button>
                    </form>
                @endif
            </div>
        </x-slot:aside>
    </x-ui.dashboard-header>

    <x-ui.scroll-panel height="34rem" class="mt-6 border-slate-200" content-class="overflow-x-auto">
            <table class="min-w-[760px] w-full divide-y divide-slate-200 text-left text-sm sm:min-w-full">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Siswa</th>
                        <th class="px-4 py-3">Nilai</th>
                        <th class="px-4 py-3">Benar</th>
                        <th class="px-4 py-3">Salah</th>
                        <th class="px-4 py-3">Dijawab</th>
                        <th class="px-4 py-3">Waktu</th>
                        <th class="px-4 py-3">Pelanggaran</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white text-slate-700">
                    @forelse ($exam->attempts as $attempt)
                        <tr>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-900">{{ $attempt->participantName() }}</p>
                                @if ($attempt->student_identifier)
                                    <p class="mt-1 text-xs text-slate-500">{{ $attempt->student_identifier }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ number_format((float) $attempt->score, 2) }}</td>
                            <td class="px-4 py-3">{{ $attempt->correctCount() }}</td>
                            <td class="px-4 py-3">{{ $attempt->wrongCount() }}</td>
                            <td class="px-4 py-3">{{ $attempt->answeredCount() }}</td>
                            <td class="px-4 py-3">{{ $attempt->timeSpentForHumans() }}</td>
                            <td class="px-4 py-3">{{ $attempt->violation_count }}</td>
                            <td class="px-4 py-3">{{ $attempt->status }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end">
                                    <form method="POST" action="{{ route('teacher.exams.attempts.destroy', [$exam, $attempt]) }}" onsubmit="return confirm('Hapus data peserta {{ $attempt->participantName() }}? Jawaban, nilai, dan pelanggarannya akan ikut dihapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dashboard-button-danger px-4 py-2 text-xs">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-4 text-sm text-slate-500">
                                Belum ada siswa yang mengerjakan ujian ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
    </x-ui.scroll-panel>

    <div class="mt-4 space-y-3 lg:hidden">
        @forelse ($exam->attempts as $attempt)
            <div class="dashboard-muted-card p-4">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="font-semibold text-slate-900">{{ $attempt->participantName() }}</p>
                        @if ($attempt->student_identifier)
                            <p class="mt-1 text-xs text-slate-500">{{ $attempt->student_identifier }}</p>
                        @endif
                    </div>
                    <p class="text-lg font-black text-sky-600">{{ number_format((float) $attempt->score, 2) }}</p>
                </div>
                <div class="mt-2 flex flex-wrap gap-3 text-xs text-slate-500">
                    <span>Benar: {{ $attempt->correctCount() }}</span>
                    <span>Salah: {{ $attempt->wrongCount() }}</span>
                    <span>Dijawab: {{ $attempt->answeredCount() }}</span>
                    <span>Waktu: {{ $attempt->timeSpentForHumans() }}</span>
                    <span>Pelanggaran: {{ $attempt->violation_count }}</span>
                    <span>Status: {{ $attempt->status }}</span>
                </div>
                <form method="POST" action="{{ route('teacher.exams.attempts.destroy', [$exam, $attempt]) }}" class="mt-4" onsubmit="return confirm('Hapus data peserta {{ $attempt->participantName() }}? Jawaban, nilai, dan pelanggarannya akan ikut dihapus.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="dashboard-button-danger w-full text-xs">
                        Hapus data peserta
                    </button>
                </form>
            </div>
        @empty
            <p class="dashboard-muted-card p-4 text-sm text-slate-500">
                Belum ada siswa yang mengerjakan ujian ini.
            </p>
        @endforelse
    </div>
</div>
