<x-layouts.dashboard :title="'Mata Pelajaran | Sistem Ujian'">
    @php
        $isEditing = filled($editingSubject);
    @endphp

    <section class="space-y-8">
        <div class="grid gap-6 xl:grid-cols-[1.05fr_0.95fr]">
            <div class="dashboard-card p-6 sm:p-7">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="max-w-3xl">
                        <p class="dashboard-kicker">Subject management</p>
                        <h1 class="dashboard-section-title mt-3 text-3xl sm:text-4xl">
                            Kelola mata pelajaran dan kelas dalam halaman khusus.
                        </h1>
                        <p class="dashboard-copy mt-4 max-w-2xl">
                            Nama mapel sekarang dipisahkan dari kelas, jadi Anda tidak perlu lagi menulis format seperti "Matematika Kelas 10" di satu kolom.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('teacher.exams.create') }}" class="dashboard-button-primary gap-2">
                            <svg class="dashboard-inline-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <rect x="4" y="5" width="16" height="14" rx="2.5" stroke="currentColor" stroke-width="1.8"/>
                                <path d="M8 3.8v2.4M16 3.8v2.4M7.5 10h9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                            Lanjut buat ujian
                        </a>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap gap-2 text-xs">
                    <span class="dashboard-pill">{{ $subjects->count() }} mapel terdaftar</span>
                    <span class="dashboard-pill">Sekarang sudah siap untuk CRUD</span>
                </div>
            </div>

            <div class="dashboard-card p-6 sm:p-7">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="dashboard-kicker">{{ $isEditing ? 'Edit Subject' : 'Subject Form' }}</p>
                        <h2 class="dashboard-section-title mt-2">
                            {{ $isEditing ? 'Perbarui mata pelajaran' : 'Tambah mata pelajaran' }}
                        </h2>
                        <p class="dashboard-copy mt-3">
                            {{ $isEditing ? 'Ubah nama mapel, kelas, atau deskripsi dari form yang sama agar alurnya tetap ringkas.' : 'Isi nama mapel dan kelas secara terpisah agar daftar ujian lebih rapi.' }}
                        </p>
                    </div>

                    @if ($isEditing)
                        <a href="{{ route('teacher.subjects.index') }}" class="dashboard-button-return gap-2">
                            <svg class="dashboard-inline-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Batal edit
                        </a>
                    @endif
                </div>

                <form
                    method="POST"
                    action="{{ $isEditing ? route('teacher.subjects.update', $editingSubject) : route('teacher.subjects.store') }}"
                    class="mt-6 space-y-4"
                >
                    @csrf
                    @if ($isEditing)
                        @method('PUT')
                    @endif

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-600">Nama mapel</label>
                        <input
                            type="text"
                            name="name"
                            class="dashboard-input"
                            value="{{ old('name', $editingSubject->name ?? '') }}"
                            placeholder="Contoh: Matematika"
                            required
                        >
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-600">Kelas</label>
                        <input
                            type="text"
                            name="class_name"
                            class="dashboard-input"
                            value="{{ old('class_name', $editingSubject->class_name ?? '') }}"
                            placeholder="Contoh: Kelas 10 IPA 1"
                            required
                        >
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-600">Deskripsi</label>
                        <textarea
                            name="description"
                            rows="4"
                            class="dashboard-input"
                            placeholder="Keterangan singkat mapel"
                        >{{ old('description', $editingSubject->description ?? '') }}</textarea>
                    </div>
                    <button type="submit" class="dashboard-button-primary w-full gap-2">
                        <svg class="dashboard-inline-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M6 12.5 10 16l8-8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M5 4.5h14v15H5z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                        </svg>
                        {{ $isEditing ? 'Simpan perubahan mapel' : 'Simpan mata pelajaran' }}
                    </button>
                </form>
            </div>
        </div>

        <div class="dashboard-card p-6 sm:p-7">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="dashboard-kicker">Subject Library</p>
                    <h2 class="dashboard-section-title mt-2">Daftar mata pelajaran</h2>
                    <p class="dashboard-copy mt-3">Setiap baris menampilkan ringkasan mapel beserta tombol edit dan hapus.</p>
                </div>
                <span class="dashboard-pill">{{ $subjects->count() }} total</span>
            </div>

            <div class="mt-6 overflow-x-auto rounded-[1.5rem] border border-slate-200">
                <table class="min-w-full divide-y divide-slate-200 bg-white">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">
                            <th class="px-5 py-4">Mata pelajaran</th>
                            <th class="px-5 py-4">Kelas</th>
                            <th class="px-5 py-4">Deskripsi</th>
                            <th class="px-5 py-4">Dipakai</th>
                            <th class="px-5 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-sm text-slate-600">
                        @forelse ($subjects as $subject)
                            <tr class="align-top">
                                <td class="px-5 py-4">
                                    <p class="font-bold text-slate-900">{{ $subject->name }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700">
                                        {{ $subject->class_name ?: 'Kelas belum diatur' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="max-w-md leading-6 text-slate-500">
                                        {{ $subject->description ?: 'Belum ada deskripsi.' }}
                                    </p>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="dashboard-pill">{{ $subject->exams_count }} ujian</span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a
                                            href="{{ route('teacher.subjects.index', ['edit' => $subject->id]) }}"
                                            class="dashboard-button-soft gap-2"
                                        >
                                            <svg class="dashboard-inline-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M4 20h4l10-10-4-4L4 16v4Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                                <path d="m12.5 7.5 4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                            </svg>
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('teacher.subjects.destroy', $subject) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="dashboard-button-danger"
                                                onclick="return confirm('Hapus mata pelajaran ini?')"
                                            >
                                                <svg class="dashboard-inline-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M5 7h14M9 7V5.5h6V7M8.5 10.5v6M12 10.5v6M15.5 10.5v6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                    <path d="M6.5 7h11l-.7 11.1a2 2 0 0 1-2 1.9H9.2a2 2 0 0 1-2-1.9L6.5 7Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                                </svg>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-6 text-sm text-slate-500">
                                    Belum ada mata pelajaran. Tambahkan dulu sebelum membuat ujian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</x-layouts.dashboard>
