@props([
    'title' => null,
])

@php
    $user = auth()->user();

    $adminNav = [
        [
            'label' => 'Dashboard Admin',
            'route' => route('admin.dashboard'),
            'active' => request()->routeIs('admin.dashboard'),
            'icon' => 'dashboard',
        ],
        [
            'label' => 'Kelola Akun',
            'route' => null,
            'active' => request()->routeIs('admin.accounts.*'),
            'icon' => 'teachers',
            'children' => [
                [
                    'label' => 'Guru',
                    'route' => route('admin.accounts.index'),
                    'active' => request()->routeIs('admin.accounts.index'),
                ],
                [
                    'label' => 'Tambahkan akun',
                    'route' => route('admin.accounts.create'),
                    'active' => request()->routeIs('admin.accounts.create'),
                ],
            ],
        ],
    ];

    $teacherNav = [
        [
            'label' => 'Dashboard Guru',
            'route' => route('teacher.dashboard'),
            'active' => request()->routeIs('teacher.dashboard'),
            'icon' => 'dashboard',
        ],
        [
            'label' => 'Mata Pelajaran',
            'route' => route('teacher.subjects.index'),
            'active' => request()->routeIs('teacher.subjects.*'),
            'icon' => 'subjects',
        ],
        [
            'label' => 'Ujian',
            'route' => null,
            'active' => request()->routeIs('teacher.exams.*'),
            'icon' => 'exams',
            'children' => [
                [
                    'label' => 'Daftar ujian',
                    'route' => route('teacher.exams.index'),
                    'active' => request()->routeIs('teacher.exams.index') || request()->routeIs('teacher.exams.show'),
                ],
                [
                    'label' => 'Buat ujian',
                    'route' => route('teacher.exams.create'),
                    'active' => request()->routeIs('teacher.exams.create') || request()->routeIs('teacher.exams.edit'),
                ],
            ],
        ],
        [
            'label' => 'Monitoring',
            'route' => route('teacher.monitoring'),
            'active' => request()->routeIs('teacher.monitoring'),
            'icon' => 'monitoring',
        ],
        [
            'label' => 'Pengaturan',
            'route' => null,
            'active' => request()->routeIs('teacher.settings.*'),
            'icon' => 'settings',
            'children' => [
                [
                    'label' => 'Print',
                    'route' => route('teacher.settings.print.edit'),
                    'active' => request()->routeIs('teacher.settings.print.*'),
                ],
            ],
        ],
    ];

    $navItems = $user?->isAdmin() ? $adminNav : $teacherNav;
    $dashboardTitle = $user?->isAdmin() ? 'Dashboard Admin' : 'Dashboard Guru';
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Sistem Ujian') }}</title>
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
<body class="ui-app-body theme-surface min-h-screen text-slate-900" data-default-theme="light">
    <div class="theme-surface flex h-[100dvh] overflow-hidden" data-dashboard-shell data-sidebar-open="false">
        <div class="pointer-events-none fixed inset-0 z-40 bg-slate-950/45 opacity-0 transition duration-200 lg:hidden" data-dashboard-overlay data-dashboard-close></div>

        <aside class="ui-dashboard-sidebar fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col overflow-y-auto px-5 py-5 transition duration-200 lg:static lg:z-auto lg:translate-x-0" data-dashboard-sidebar>
            <div class="flex items-center justify-between gap-3 border-b border-slate-200 pb-5">
                <div>
                    <a href="{{ route('dashboard') }}" class="text-xl font-black tracking-tight text-slate-900">
                        Sistem Ujian
                    </a>
                    <p class="mt-1 text-xs font-semibold uppercase tracking-[0.28em] text-violet-500">
                        {{ $user?->isAdmin() ? 'Admin Mode' : 'Teacher Mode' }}
                    </p>
                </div>

                <button type="button" class="rounded-2xl p-2 text-slate-500 transition hover:bg-slate-100 lg:hidden" data-dashboard-close aria-label="Tutup sidebar">
                    <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current">
                        <path d="M18.3 5.71 12 12l6.3 6.29-1.41 1.41L10.59 13.4 4.29 19.7 2.88 18.29 9.17 12 2.88 5.71 4.29 4.29l6.3 6.3 6.29-6.3z" />
                    </svg>
                </button>
            </div>

            <nav class="mt-6 space-y-2">
                <p class="px-3 text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">Navigasi</p>
                @foreach ($navItems as $item)
                    @if (!empty($item['children']))
                        <details class="dashboard-nav-group" @if($item['active']) open @endif>
                            <summary class="{{ $item['active'] ? 'dashboard-nav-link dashboard-nav-link-active' : 'dashboard-nav-link' }}">
                                <span class="grid h-10 w-10 place-items-center rounded-2xl {{ $item['active'] ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' }}">
                                    @switch($item['icon'])
                                        @case('teachers')
                                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" aria-hidden="true">
                                                <path d="M16 20a4 4 0 0 0-8 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                <circle cx="12" cy="8" r="3.2" stroke="currentColor" stroke-width="1.8"/>
                                                <path d="M5 18.5c.6-2.1 2.4-3.5 4.5-3.5M19 18.5c-.6-2.1-2.4-3.5-4.5-3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                            </svg>
                                            @break
                                        @case('exams')
                                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" aria-hidden="true">
                                                <rect x="4" y="5" width="16" height="14" rx="2.5" stroke="currentColor" stroke-width="1.8"/>
                                                <path d="M8 3.8v2.4M16 3.8v2.4M7.5 10h9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                            </svg>
                                            @break
                                        @case('settings')
                                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" aria-hidden="true">
                                                <path d="M12 8.5A3.5 3.5 0 1 0 12 15.5 3.5 3.5 0 0 0 12 8.5Z" stroke="currentColor" stroke-width="1.8"/>
                                                <path d="M19.4 15a1 1 0 0 0 .2 1.1l.1.1a1.2 1.2 0 0 1 0 1.7l-1.2 1.2a1.2 1.2 0 0 1-1.7 0l-.1-.1a1 1 0 0 0-1.1-.2 1 1 0 0 0-.6.9v.2a1.2 1.2 0 0 1-1.2 1.2h-1.7a1.2 1.2 0 0 1-1.2-1.2v-.2a1 1 0 0 0-.7-.9 1 1 0 0 0-1.1.2l-.1.1a1.2 1.2 0 0 1-1.7 0l-1.2-1.2a1.2 1.2 0 0 1 0-1.7l.1-.1a1 1 0 0 0 .2-1.1 1 1 0 0 0-.9-.6h-.2A1.2 1.2 0 0 1 2 13.8v-1.7A1.2 1.2 0 0 1 3.2 10.9h.2a1 1 0 0 0 .9-.6 1 1 0 0 0-.2-1.1L4 9.1a1.2 1.2 0 0 1 0-1.7l1.2-1.2a1.2 1.2 0 0 1 1.7 0l.1.1a1 1 0 0 0 1.1.2 1 1 0 0 0 .6-.9v-.2A1.2 1.2 0 0 1 9.9 4h1.7a1.2 1.2 0 0 1 1.2 1.2v.2a1 1 0 0 0 .6.9 1 1 0 0 0 1.1-.2l.1-.1a1.2 1.2 0 0 1 1.7 0L18 7.2a1.2 1.2 0 0 1 0 1.7l-.1.1a1 1 0 0 0-.2 1.1 1 1 0 0 0 .9.6h.2a1.2 1.2 0 0 1 1.2 1.2v1.7a1.2 1.2 0 0 1-1.2 1.2h-.2a1 1 0 0 0-.9.6Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
                                            </svg>
                                            @break
                                    @endswitch
                                </span>
                                <span class="flex-1">{{ $item['label'] }}</span>
                                <svg viewBox="0 0 24 24" class="dashboard-nav-caret h-4 w-4" fill="none" aria-hidden="true">
                                    <path d="m8 10 4 4 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </summary>

                            <div class="dashboard-nav-children">
                                @foreach ($item['children'] as $child)
                                    <a href="{{ $child['route'] }}" class="{{ $child['active'] ? 'dashboard-nav-sublink dashboard-nav-sublink-active' : 'dashboard-nav-sublink' }}">
                                        <span>{{ $child['label'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </details>
                    @else
                        <a href="{{ $item['route'] }}" class="{{ $item['active'] ? 'dashboard-nav-link dashboard-nav-link-active' : 'dashboard-nav-link' }}">
                            <span class="grid h-10 w-10 place-items-center rounded-2xl {{ $item['active'] ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' }}">
                                @switch($item['icon'])
                                    @case('dashboard')
                                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" aria-hidden="true">
                                            <path d="M4.5 5.5h6v6h-6zM13.5 5.5h6v9h-6zM4.5 14.5h6v4h-6zM13.5 17.5h6v1h-6z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                        </svg>
                                        @break
                                    @case('subjects')
                                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" aria-hidden="true">
                                            <path d="M6 5.5h8a3 3 0 0 1 3 3V18l-5-2-5 2V5.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                            <path d="M9 9.5h5M9 12.5h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        </svg>
                                        @break
                                    @case('exams')
                                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" aria-hidden="true">
                                            <rect x="4" y="5" width="16" height="14" rx="2.5" stroke="currentColor" stroke-width="1.8"/>
                                            <path d="M8 3.8v2.4M16 3.8v2.4M7.5 10h9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        </svg>
                                        @break
                                    @case('monitoring')
                                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" aria-hidden="true">
                                            <path d="M4 12c1.7-3.4 4.8-5.5 8-5.5s6.3 2.1 8 5.5c-1.7 3.4-4.8 5.5-8 5.5S5.7 15.4 4 12Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                            <circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.8"/>
                                        </svg>
                                        @break
                                @endswitch
                            </span>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endif
                @endforeach
            </nav>

        </aside>

        <div class="ui-dashboard-main relative flex min-w-0 flex-1 flex-col overflow-y-auto overflow-x-hidden">
            <header class="ui-dashboard-header sticky top-0 z-30">
                <div class="mx-auto flex w-full max-w-[90rem] items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-3">
                        <button type="button" class="ui-dashboard-panel inline-flex h-11 w-11 items-center justify-center rounded-2xl lg:hidden" data-dashboard-open aria-label="Buka sidebar">
                            <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current">
                                <path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z" />
                            </svg>
                        </button>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.32em] text-violet-500">{{ $user?->isAdmin() ? 'Executive View' : 'Teaching View' }}</p>
                            <h1 class="mt-1 text-xl font-black tracking-tight text-slate-900 sm:text-2xl">{{ $dashboardTitle }}</h1>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="button" data-theme-toggle class="ui-theme-toggle shrink-0" aria-label="Ubah tema">
                            <span class="sr-only">Ubah tema</span>
                        </button>
                        <a href="{{ route('profile.show') }}" class="ui-dashboard-panel inline-flex h-11 w-11 items-center justify-center rounded-2xl sm:hidden" aria-label="Buka profil">
                            <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current">
                                <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Zm0 2c-3.87 0-7 2.24-7 5v1h14v-1c0-2.76-3.13-5-7-5Z" />
                            </svg>
                        </a>
                        <a href="{{ route('profile.show') }}" class="ui-dashboard-panel hidden rounded-2xl px-4 py-2 text-right sm:block">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">{{ $user?->isAdmin() ? 'Admin' : 'Guru' }}</p>
                            <p class="mt-1 text-sm font-semibold text-slate-700">{{ $user?->name }}</p>
                            <p class="mt-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-sky-500">Profil</p>
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-rose-500 to-orange-400 px-4 py-3 text-sm font-semibold text-white shadow-[0_14px_32px_rgba(244,63,94,0.2)] transition hover:brightness-105">
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="grow px-4 py-6 sm:px-6 lg:px-8">
                <div class="mx-auto w-full max-w-[90rem]">
                    @if (session('status'))
                        <div class="mb-6 rounded-[1.5rem] border border-emerald-200 bg-emerald-50 px-4 py-4 text-sm text-emerald-700 shadow-[0_14px_35px_rgba(16,185,129,0.08)] sm:px-5">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-6 rounded-[1.5rem] border border-rose-200 bg-rose-50 px-4 py-4 text-rose-700 shadow-[0_14px_35px_rgba(244,63,94,0.08)] sm:px-5">
                            <p class="font-semibold">Masih ada data yang perlu dibenahi:</p>
                            <ul class="mt-2 space-y-1 text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
</body>
</html>
