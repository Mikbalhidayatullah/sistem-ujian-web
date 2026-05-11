<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Sistem Ujian') }}</title>
    <script>
        (() => {
            const fallbackTheme = 'light';

            try {
                const storedTheme = window.localStorage.getItem('ui-theme');
                const activeTheme = storedTheme === 'light' || storedTheme === 'dark' ? storedTheme : fallbackTheme;

                document.documentElement.dataset.theme = activeTheme;
                document.documentElement.style.colorScheme = activeTheme;
            } catch (error) {
                document.documentElement.dataset.theme = fallbackTheme;
                document.documentElement.style.colorScheme = fallbackTheme;
            }
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="ui-app-body theme-surface min-h-screen text-slate-900 antialiased" data-default-theme="light">
    <main class="ui-app-shell relative min-h-screen overflow-hidden">
        <div class="ui-app-glow-wrap">
            <div class="ui-app-glow-one"></div>
            <div class="ui-app-glow-two"></div>
            <div class="ui-app-glow-three"></div>
        </div>
        <div class="relative mx-auto flex min-h-screen max-w-7xl flex-col justify-center px-4 py-8 sm:px-6 sm:py-12">
            <div class="mb-4 flex justify-end">
                <button type="button" data-theme-toggle class="ui-theme-toggle shrink-0" aria-label="Ubah tema">
                    <span class="sr-only">Ubah tema</span>
                </button>
            </div>
            <div class="grid gap-6 lg:grid-cols-[1.15fr_0.85fr] lg:gap-8">
                <section class="dashboard-card p-6 sm:p-8">
                    <span class="inline-flex rounded-full border border-sky-200 bg-sky-50 px-4 py-1 text-sm font-semibold text-sky-700">
                        Sistem Ujian Sekolah
                    </span>

                    <div class="mt-6 space-y-4">
                        <h1 class="text-4xl font-black leading-tight text-slate-900 sm:text-5xl md:text-6xl">
                            Ujian online, nilai otomatis, dan monitoring pelanggaran dalam satu alur.
                        </h1>
                        <p class="max-w-3xl text-sm leading-7 text-slate-500 sm:text-base md:text-lg">
                            Guru dapat membuat bank soal pilihan ganda, membuka akses ujian sesuai jadwal, memantau pelanggaran selama pengerjaan, lalu melihat rekap hasil tanpa pindah aplikasi.
                        </p>
                    </div>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('login') }}" class="dashboard-button-primary w-full md:w-auto">
                            Login operator web / guru
                        </a>
                    </div>

                    
                </section>

                <section class="grid gap-4">
                    <div class="dashboard-card p-5 sm:p-6">
                        <p class="dashboard-kicker">Masuk ujian siswa</p>
                        <h2 class="mt-3 text-2xl font-black text-slate-900">Akses cepat tanpa akun</h2>
                        <p class="mt-3 text-sm leading-7 text-slate-500">
                            Isi nama lengkap, token, dan PIN dari guru. Status ujian akan dicek otomatis sebelum tombol masuk diaktifkan.
                        </p>

                        <form
                            method="POST"
                            action="{{ route('exam.access.start') }}"
                            class="mt-6 space-y-4"
                            data-exam-access-form
                            data-status-url="{{ route('exam.access.status') }}"
                            data-csrf-url="{{ route('csrf.token') }}"
                        >
                            @csrf
                            <div class="space-y-2">
                                <label for="full_name" class="text-sm font-semibold text-slate-600">Nama lengkap</label>
                                <input id="full_name" name="full_name" type="text" value="{{ old('full_name') }}" class="dashboard-input" required>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="space-y-2">
                                    <label for="access_token" class="text-sm font-semibold text-slate-600">Token ujian</label>
                                    <input id="access_token" name="access_token" type="text" value="{{ old('access_token') }}" class="dashboard-input uppercase" required>
                                </div>
                                <div class="space-y-2">
                                    <label for="access_pin" class="text-sm font-semibold text-slate-600">PIN</label>
                                    <input id="access_pin" name="access_pin" type="password" value="{{ old('access_pin') }}" class="dashboard-input" inputmode="numeric" required>
                                </div>
                            </div>
                            <div data-exam-access-status class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-500">
                                Isi token dan PIN, lalu status ujian akan dicek otomatis.
                            </div>
                            <button type="submit" data-exam-access-submit class="dashboard-button-success w-full">
                                Masuk ke ujian
                            </button>
                        </form>
                    </div>

                    <div class="rounded-[1.75rem] border border-amber-200 bg-amber-50 p-5 text-amber-900 shadow-[0_20px_55px_rgba(251,191,36,0.08)] sm:p-6">
                        <p class="text-sm font-semibold uppercase tracking-[0.28em] text-amber-700">Catatan teknis</p>
                        <p class="mt-3 text-sm leading-7">
                            Browser web tidak bisa mengunci perangkat sepenuhnya, jadi sistem memakai kombinasi full-screen, alarm, pencatatan pelanggaran, dan auto-submit saat batas pelanggaran terlampaui.
                        </p>
                    </div>
                </section>
            </div>
        </div>
    </main>
</body>
</html>
