<x-layouts.dashboard :title="'Riwayat Cetak | Sistem Ujian'">
    <section class="space-y-8">
        <div class="dashboard-card p-6 sm:p-7">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="max-w-3xl">
                    <p class="dashboard-kicker">Print History</p>
                    <h1 class="dashboard-section-title mt-3 text-3xl sm:text-4xl">
                        Riwayat cetak dokumen ujian.
                    </h1>
                    <p class="dashboard-copy mt-4 max-w-2xl">
                        Setiap kali guru membuka lembar print ujian, catatan waktunya tersimpan di sini. Halaman ini membantu Anda menelusuri arsip fisik yang pernah dicetak.
                    </p>
                </div>
                <a href="{{ route('teacher.settings.print.edit') }}" class="dashboard-button-return">
                    Kembali ke pengaturan print
                </a>
            </div>

            <div class="mt-6 flex flex-wrap gap-2 text-xs">
                <x-ui.dashboard-pill>{{ $logs->total() }} riwayat tercatat</x-ui.dashboard-pill>
            </div>
        </div>

        <div class="dashboard-card p-6 sm:p-7">
            <div class="overflow-x-auto rounded-[1.5rem] border border-slate-200">
                <table class="min-w-full divide-y divide-slate-200 bg-white">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">
                            <th class="px-5 py-4">Waktu</th>
                            <th class="px-5 py-4">Ujian</th>
                            <th class="px-5 py-4">Mapel</th>
                            <th class="px-5 py-4">Metode</th>
                            <th class="px-5 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-sm text-slate-600">
                        @forelse ($logs as $log)
                            <tr>
                                <td class="px-5 py-4">{{ $log->printed_at?->format('d M Y H:i') ?? '-' }}</td>
                                <td class="px-5 py-4">
                                    <p class="font-bold text-slate-900">{{ $log->exam?->title ?? 'Ujian sudah dihapus' }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    {{ $log->exam?->subject?->display_name ?? '-' }}
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                        {{ strtoupper($log->channel) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end">
                                        @if ($log->exam)
                                            <a href="{{ route('teacher.exams.show', $log->exam) }}" class="dashboard-button-soft px-4 py-2 text-xs">
                                                Buka detail ujian
                                            </a>
                                        @else
                                            <span class="text-xs text-slate-400">Ujian tidak tersedia</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-6 text-sm text-slate-500">
                                    Belum ada riwayat cetak yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($logs->hasPages())
            <div class="dashboard-card p-4">
                {{ $logs->links() }}
            </div>
        @endif
    </section>
</x-layouts.dashboard>
