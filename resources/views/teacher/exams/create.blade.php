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

                        <label class="dashboard-muted-card flex items-center gap-3 px-4 py-4 text-sm text-slate-700 md:col-span-2">
                            <input
                                type="checkbox"
                                name="shuffle_questions_per_student"
                                value="1"
                                class="rounded border-slate-300 bg-white text-sky-500"
                                @checked(old('shuffle_questions_per_student', $editingExam?->shuffle_questions_per_student))
                            >
                            Acak urutan soal per siswa
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

    </section>
</x-layouts.dashboard>
