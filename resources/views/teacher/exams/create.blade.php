<x-layouts.dashboard :title="'Buat Ujian | Sistem Ujian'">
    @php
        $isEditingExam = filled($editingExam);
        $startAtValue = old('start_at', $editingExam?->start_at?->format('Y-m-d\TH:i'));
        $endAtValue = old('end_at', $editingExam?->end_at?->format('Y-m-d\TH:i'));
    @endphp
    <section class="space-y-8">
        @if ($subjects->isEmpty())
            <div class="dashboard-card p-6 sm:p-7">
                <div class="rounded-[1.75rem] border border-amber-200 bg-amber-50 p-6">
                    <p class="dashboard-kicker">Belum siap</p>
                    <h2 class="dashboard-section-title mt-2">Tambahkan mata pelajaran terlebih dahulu</h2>
                    <p class="dashboard-copy mt-3">
                        Form ujian belum bisa dipakai karena Anda belum memiliki mata pelajaran. Buat mapel dari dashboard guru, lalu kembali ke halaman ini.
                    </p>
                    <a href="{{ route('teacher.dashboard') }}" class="dashboard-button-return mt-6 gap-2">
                        <svg class="dashboard-inline-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Kembali ke dashboard guru
                    </a>
                    <a href="{{ route('teacher.subjects.index') }}" class="dashboard-button-soft mt-3 gap-2">
                        <svg class="dashboard-inline-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M6 5.5h8a3 3 0 0 1 3 3V18l-5-2-5 2V5.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                            <path d="M9 9.5h5M9 12.5h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        Buka halaman mata pelajaran
                    </a>
                </div>
            </div>
        @else
            <div class="grid gap-6 xl:grid-cols-[1.12fr_0.88fr]">
                <div class="dashboard-card p-6 sm:p-7">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="dashboard-kicker">{{ $isEditingExam ? 'Edit Ujian' : 'Form Ujian' }}</p>
                            <h2 class="dashboard-section-title mt-2">{{ $isEditingExam ? 'Perbarui informasi dasar ujian' : 'Informasi dasar ujian' }}</h2>
                            <p class="dashboard-copy mt-3">
                                {{ $isEditingExam
                                    ? 'Gunakan form yang sama seperti pembuatan ujian untuk mengubah tanggal, token, PIN, durasi, dan pengaturan inti lainnya.'
                                    : 'Isi seluruh data inti ujian di bawah ini. Anda masih bisa melanjutkan pengelolaan soal dan akses siswa setelah ujian berhasil dibuat.' }}
                            </p>
                        </div>
                        <span class="dashboard-pill">{{ $isEditingExam ? 'Mode edit ujian' : 'Langkah 1 dari alur pembuatan ujian' }}</span>
                    </div>

                    <form method="POST" action="{{ $isEditingExam ? route('teacher.exams.update', $editingExam) : route('teacher.exams.store') }}" class="mt-6 grid gap-5 md:grid-cols-2">
                        @csrf
                        @if ($isEditingExam)
                            @method('PUT')
                        @endif

                        <div class="space-y-2 md:col-span-2">
                            <label class="text-sm font-semibold text-slate-600">Mata pelajaran</label>
                            <select name="subject_id" class="dashboard-input" required>
                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject->id }}" @selected(old('subject_id', $editingExam?->subject_id) == $subject->id)>{{ $subject->display_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <label class="text-sm font-semibold text-slate-600">Judul ujian</label>
                            <input type="text" name="title" class="dashboard-input" value="{{ old('title', $editingExam?->title) }}" placeholder="Contoh: Ujian Tengah Semester Matematika" required>
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <label class="text-sm font-semibold text-slate-600">Deskripsi</label>
                            <textarea name="description" rows="4" class="dashboard-input" placeholder="Catatan singkat untuk membantu identifikasi ujian">{{ old('description', $editingExam?->description) }}</textarea>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-600">Token ujian</label>
                            <input type="text" name="access_token" class="dashboard-input uppercase" value="{{ old('access_token', $editingExam?->access_token) }}" placeholder="{{ $isEditingExam ? 'Kosongkan untuk tetap memakai token lama' : 'Kosongkan untuk generate otomatis' }}" maxlength="20" autocomplete="off">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-600">PIN ujian</label>
                            <input type="text" name="access_pin" class="dashboard-input" value="{{ old('access_pin', $editingExam?->access_pin) }}" placeholder="{{ $isEditingExam ? 'Kosongkan untuk tetap memakai PIN lama' : '6 digit, kosongkan untuk otomatis' }}" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="off">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-600">Mulai</label>
                            <input type="datetime-local" name="start_at" class="dashboard-input" value="{{ $startAtValue }}">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-600">Selesai</label>
                            <input type="datetime-local" name="end_at" class="dashboard-input" value="{{ $endAtValue }}">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-600">Durasi (menit)</label>
                            <input type="number" min="1" max="300" name="duration_minutes" class="dashboard-input" value="{{ old('duration_minutes', $editingExam?->duration_minutes ?? 60) }}" required>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-600">Batas pelanggaran</label>
                            <input type="number" min="1" max="20" name="max_violations" class="dashboard-input" value="{{ old('max_violations', $editingExam?->max_violations ?? 3) }}" required>
                        </div>

                        <label class="dashboard-muted-card flex items-center gap-3 px-4 py-4 text-sm text-slate-700 md:col-span-2">
                            <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 bg-white text-sky-500" @checked(old('is_active', $isEditingExam ? $editingExam->is_active : true))>
                            {{ $isEditingExam ? 'Biarkan akses manual tetap aktif' : 'Aktifkan ujian setelah dibuat' }}
                        </label>

                        <div class="md:col-span-2 action-row">
                            <button type="submit" class="dashboard-button-primary gap-2">
                                <svg class="dashboard-inline-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M6 12.5 10 16l8-8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M5 4.5h14v15H5z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                </svg>
                                {{ $isEditingExam ? 'Simpan perubahan ujian' : 'Simpan ujian' }}
                            </button>
                            <a href="{{ $isEditingExam ? route('teacher.exams.show', $editingExam) : route('teacher.dashboard') }}" class="dashboard-button-return gap-2">
                                <svg class="dashboard-inline-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                {{ $isEditingExam ? 'Batal edit' : 'Batal dan kembali' }}
                            </a>
                        </div>
                    </form>
                </div>

                <div class="space-y-6">
                    <div class="dashboard-card p-6 sm:p-7">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="dashboard-kicker">Subject Library</p>
                                <h2 class="dashboard-section-title mt-2">Mapel tersedia</h2>
                            </div>
                            <span class="dashboard-pill">{{ $subjects->count() }} total</span>
                        </div>

                        <div class="mt-6 space-y-3">
                            @foreach ($subjects as $subject)
                                <div class="dashboard-muted-card p-4">
                                    <p class="font-bold text-slate-900">{{ $subject->name }}</p>
                                    <p class="mt-2 inline-flex rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700">
                                        {{ $subject->class_name ?: 'Kelas belum diatur' }}
                                    </p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $subject->description ?: 'Belum ada deskripsi mapel.' }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="dashboard-card p-6 sm:p-7">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="dashboard-kicker">Exam Library</p>
                    <h2 class="dashboard-section-title mt-2">Ujian yang sudah Anda buat</h2>
                    <p class="dashboard-copy mt-3">
                        Daftar ini membantu Anda membuka kembali ujian lama tanpa harus mencari URL detailnya secara manual.
                    </p>
                </div>
                <span class="dashboard-pill">{{ $exams->count() }} total</span>
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
                                    Belum ada ujian yang dibuat. Setelah membuat ujian pertama, link detailnya akan muncul di sini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</x-layouts.dashboard>
