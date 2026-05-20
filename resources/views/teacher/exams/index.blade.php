<x-layouts.dashboard :title="'Daftar Ujian | Sistem Ujian'">
    @php
        $totalExams = $exams->count();
    @endphp

    <section class="space-y-8">
        <div class="dashboard-card p-6 sm:p-7">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="max-w-3xl">
                    <p class="dashboard-kicker">Exam Library</p>
                    <h1 class="dashboard-section-title mt-3 text-3xl sm:text-4xl">
                        Daftar ujian yang sudah Anda buat.
                    </h1>
                    <p class="dashboard-copy mt-4 max-w-2xl">
                        Halaman ini fokus untuk membuka kembali, mengedit, atau menghapus ujian tanpa bercampur dengan form pembuatan ujian baru.
                    </p>
                </div>
                <a href="{{ route('teacher.exams.create') }}" class="dashboard-button-primary gap-2">
                    <svg class="dashboard-inline-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    Buat ujian
                </a>
            </div>

            <div class="mt-6 flex flex-wrap gap-2 text-xs">
                <span class="dashboard-pill">{{ $totalExams }} total ujian</span>
            </div>
        </div>

        <div class="dashboard-card p-6 sm:p-7">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="dashboard-kicker">Exam List</p>
                    <h2 class="dashboard-section-title mt-2">Daftar ujian</h2>
                    <p class="dashboard-copy mt-3">
                        Buka detail ujian, ubah pengaturan dasar, atau hapus ujian langsung dari tabel berikut.
                    </p>
                </div>
                <span class="dashboard-pill">{{ $totalExams }} ujian</span>
            </div>

            <div class="mt-6 overflow-x-auto rounded-[1.5rem] border border-slate-200">
                <table class="min-w-full divide-y divide-slate-200 bg-white">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">
                            <th class="px-5 py-4">Judul ujian</th>
                            <th class="px-5 py-4">Mapel</th>
                            <th class="px-5 py-4">Soal</th>
                            <th class="px-5 py-4">Peserta</th>
                            <th class="px-5 py-4">Status</th>
                            <th class="px-5 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-sm text-slate-600">
                        @forelse ($exams as $exam)
                            <tr>
                                <td class="px-5 py-4">
                                    <p class="font-bold text-slate-900">{{ $exam->title }}</p>
                                    <p class="mt-1 text-xs text-slate-500">Token {{ $exam->access_token }} | PIN {{ $exam->access_pin }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700">
                                        {{ $exam->subject?->display_name ?? 'Mapel tidak ditemukan' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">{{ $exam->questions_count }}</td>
                                <td class="px-5 py-4">{{ $exam->attempts_count }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $exam->isOpenNow() ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $exam->isOpenNow() ? 'Bisa diakses' : 'Belum aktif' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('teacher.exams.edit', $exam) }}" class="dashboard-button-return gap-2">
                                            <svg class="dashboard-inline-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="m8 16 7.5-7.5 2 2L10 18H8v-2Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                                <path d="M14.5 6.5 16 5a1.4 1.4 0 0 1 2 0l1 1a1.4 1.4 0 0 1 0 2l-1.5 1.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            Edit
                                        </a>
                                        <a href="{{ route('teacher.exams.show', $exam) }}" class="dashboard-button-soft gap-2">
                                            <svg class="dashboard-inline-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M4 12c1.7-3.4 4.8-5.5 8-5.5s6.3 2.1 8 5.5c-1.7 3.4-4.8 5.5-8 5.5S5.7 15.4 4 12Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                <circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.8"/>
                                            </svg>
                                            Buka detail
                                        </a>
                                        <form method="POST" action="{{ route('teacher.exams.destroy', $exam) }}" onsubmit="return confirm('Hapus ujian ini? Semua soal, sesi, jawaban, dan log pelanggaran akan ikut dihapus.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dashboard-button-danger gap-2">
                                                <svg class="dashboard-inline-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M5 7h14M9 7V5.8c0-.7.5-1.3 1.2-1.3h3.6c.7 0 1.2.6 1.2 1.3V7M8.5 10.5v6M12 10.5v6M15.5 10.5v6M7.5 7l.7 11.1c.1.8.7 1.4 1.5 1.4h4.6c.8 0 1.4-.6 1.5-1.4L16.5 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-6 text-sm text-slate-500">
                                    Belum ada ujian yang dibuat. Gunakan tombol "Buat ujian" untuk mulai menyusun pelaksanaan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</x-layouts.dashboard>
