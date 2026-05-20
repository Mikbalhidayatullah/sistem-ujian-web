<x-layouts.dashboard title="Pengaturan Print">
    <div class="space-y-6">
        <section class="dashboard-card p-6 sm:p-7">
            <p class="dashboard-kicker">Pengaturan Print</p>
            <h1 class="dashboard-section-title mt-3">Header kop untuk cetak ujian</h1>
            <p class="dashboard-copy mt-4 max-w-3xl">
                Data di halaman ini akan dipakai otomatis pada tombol <strong>Print</strong> di detail ujian. Selama tidak Anda ubah lagi, semua lembar print tetap memakai logo dan identitas sekolah yang sama.
            </p>
        </section>

        <section class="dashboard-card p-6 sm:p-7">
            <form
                method="POST"
                action="{{ route('teacher.settings.print.update') }}"
                enctype="multipart/form-data"
                class="space-y-6"
                data-print-settings-preview
            >
                @csrf
                @method('PUT')

                <div class="grid gap-6 lg:grid-cols-[0.85fr_1.15fr]">
                    <div class="space-y-4">
                        <div class="dashboard-muted-card p-5">
                            <p class="text-sm font-semibold text-slate-500">Logo header</p>

                            @if ($previewLogoUrl)
                                <div class="mt-4 rounded-[1.5rem] border border-slate-200 bg-white p-4">
                                    <img
                                        src="{{ $previewLogoUrl }}"
                                        alt="Logo print"
                                        class="h-32 w-auto object-contain"
                                    >
                                </div>
                            @else
                                <div class="mt-4 rounded-[1.5rem] border border-dashed border-slate-300 bg-white px-4 py-8 text-center text-sm text-slate-500">
                                    Belum ada logo khusus. Sistem akan memakai logo fallback jika tersedia.
                                </div>
                            @endif

                            <div class="mt-4 space-y-3">
                                <label class="block">
                                    <span class="mb-2 block text-sm font-semibold text-slate-700">Upload logo baru</span>
                                    <input
                                        type="file"
                                        name="logo"
                                        accept=".png,.jpg,.jpeg,.webp"
                                        data-print-preview-logo-input
                                        class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm transition focus:border-sky-400 focus:outline-none focus:ring-4 focus:ring-sky-100"
                                    >
                                </label>

                                @if ($setting->logo_path)
                                    <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                                        <input type="checkbox" name="remove_logo" value="1" data-print-preview-remove-logo class="rounded border-slate-300 text-rose-500 focus:ring-rose-200">
                                        <span>Hapus logo saat ini</span>
                                    </label>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="block">
                                <span class="mb-2 block text-sm font-semibold text-slate-700">Nama sekolah</span>
                                <input
                                    type="text"
                                    name="school_name"
                                    value="{{ old('school_name', $setting->school_name) }}"
                                    data-print-preview-school-name
                                    class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm transition focus:border-sky-400 focus:outline-none focus:ring-4 focus:ring-sky-100"
                                    required
                                >
                            </label>

                            <label class="block">
                                <span class="mb-2 block text-sm font-semibold text-slate-700">Jurusan</span>
                                <input
                                    type="text"
                                    name="school_department"
                                    value="{{ old('school_department', $setting->school_department) }}"
                                    data-print-preview-school-department
                                    class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm transition focus:border-sky-400 focus:outline-none focus:ring-4 focus:ring-sky-100"
                                    required
                                >
                            </label>
                        </div>

                        <label class="block">
                            <span class="mb-2 block text-sm font-semibold text-slate-700">Alamat header</span>
                            <input
                                type="text"
                                name="school_address"
                                value="{{ old('school_address', $setting->school_address) }}"
                                data-print-preview-school-address
                                class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm transition focus:border-sky-400 focus:outline-none focus:ring-4 focus:ring-sky-100"
                                required
                            >
                        </label>

                        <div class="dashboard-muted-card p-5 text-sm text-slate-600">
                            <p class="font-semibold text-slate-700">Catatan penggunaan</p>
                            <ul class="mt-3 space-y-2">
                                <li>Pengaturan ini dipakai untuk semua print ujian milik akun guru Anda.</li>
                                <li>Jika logo tidak diupload di sini, sistem tetap bisa memakai logo cadangan yang sudah ada sebelumnya.</li>
                                <li>Format upload logo yang disarankan adalah PNG dengan latar transparan.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <button type="submit" class="dashboard-button-primary">
                        Simpan pengaturan print
                    </button>
                    <a href="{{ route('teacher.dashboard') }}" class="dashboard-button-return">
                        Kembali ke dashboard
                    </a>
                </div>
            </form>
        </section>

        <section class="dashboard-card p-6 sm:p-7">
            <p class="dashboard-kicker">Preview Aktif</p>
            <h2 class="dashboard-section-title mt-3">Tampilan kop yang sedang dipakai print</h2>
            <p class="dashboard-copy mt-4 max-w-3xl">
                Preview ini mengikuti data yang sudah tersimpan saat ini. Setelah Anda menekan tombol simpan, tampilan print detail ujian akan memakai format yang sama.
            </p>

            <div class="mt-6 overflow-hidden rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_24px_60px_rgba(15,23,42,0.08)]" data-print-preview-panel>
                <div class="relative min-h-[6rem] px-20">
                    <div class="absolute left-0 top-0 w-16">
                        @if ($previewLogoUrl)
                            <img
                                src="{{ $previewLogoUrl }}"
                                alt="Preview logo kop"
                                class="h-14 w-14 object-contain"
                                data-print-preview-logo-image
                                data-original-src="{{ $previewLogoUrl }}"
                            >
                            <div class="hidden grid h-14 w-14 place-items-center rounded-2xl border border-slate-300 bg-slate-50 text-xs font-black text-slate-500" data-print-preview-logo-placeholder>
                                LOGO
                            </div>
                        @else
                            <img
                                src=""
                                alt="Preview logo kop"
                                class="hidden h-14 w-14 object-contain"
                                data-print-preview-logo-image
                                data-original-src=""
                            >
                            <div class="grid h-14 w-14 place-items-center rounded-2xl border border-slate-300 bg-slate-50 text-xs font-black text-slate-500" data-print-preview-logo-placeholder>
                                LOGO
                            </div>
                        @endif
                    </div>

                    <div class="text-center">
                        <h3 class="text-[1.1rem] font-black uppercase tracking-[0.04em] text-slate-900" data-print-preview-school-name-target>
                            {{ $setting->school_name }}
                        </h3>
                        <p class="mt-2 text-sm font-bold text-slate-900" data-print-preview-school-department-target>
                            Jurusan : {{ $setting->school_department }}
                        </p>
                        <p class="mt-1 text-sm text-slate-800" data-print-preview-school-address-target>
                            {{ $setting->school_address }}
                        </p>
                    </div>
                </div>

                <div class="mt-4 border-t-2 border-black"></div>
                <div class="mt-1 border-t border-black"></div>

                <div class="mt-4 text-center">
                    <p class="text-[0.7rem] font-semibold uppercase tracking-[0.32em] text-slate-500">
                        Pemrograman Dasar | 10 Multimedia
                    </p>
                    <p class="mt-3 text-[1.15rem] font-black uppercase tracking-[0.03em] text-slate-950">
                        Contoh Judul Ujian
                    </p>
                </div>
            </div>
        </section>
    </div>
</x-layouts.dashboard>
