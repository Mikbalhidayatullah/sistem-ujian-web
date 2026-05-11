@props([
    'title' => null,
    'hideAuthNav' => false,
    'variant' => 'dark',
    'showThemeToggle' => true,
])

@php
    $defaultTheme = $variant === 'light' ? 'light' : 'dark';
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
            const fallbackTheme = '{{ $defaultTheme }}';

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
<body class="ui-app-body theme-surface min-h-screen" data-auth-protected-page="{{ auth()->check() ? 'true' : 'false' }}" data-default-theme="{{ $defaultTheme }}">
    <div class="ui-app-shell">
        <div class="ui-app-glow-wrap">
            <div class="ui-app-glow-one"></div>
            <div class="ui-app-glow-two"></div>
            <div class="ui-app-glow-three"></div>
        </div>

        <header class="ui-app-header">
            <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-4 md:flex-row md:items-center md:justify-between md:px-6">
                <div class="min-w-0">
                    <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" class="ui-app-brand-link">
                        <span class="ui-app-brand-badge">
                            SU
                        </span>
                        <span class="min-w-0">
                            <span class="block truncate text-lg font-black tracking-wide">Sistem Ujian</span>
                            <span class="ui-app-brand-meta">Monitoring dan evaluasi online</span>
                        </span>
                    </a>
                    @isset($headerMeta)
                        <div class="mt-3">
                            {{ $headerMeta }}
                        </div>
                    @elseif (auth()->check())
                        <p class="ui-app-auth-text">{{ auth()->user()->name }} | {{ auth()->user()->isAdmin() ? 'Admin' : 'Guru' }}</p>
                    @endif
                </div>

                <div class="flex w-full flex-col gap-3 md:w-auto md:flex-row md:items-center">
                    @if ($showThemeToggle)
                        <button type="button" data-theme-toggle class="ui-theme-toggle shrink-0">
                            <span class="sr-only">Ubah tema</span>
                        </button>
                    @endif

                    @auth
                        @unless($hideAuthNav)
                            <nav class="flex w-full flex-col gap-3 md:w-auto md:flex-row md:items-center">
                                <a href="{{ route('dashboard') }}" class="ui-app-nav-link">
                                    Dashboard
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="w-full md:w-auto">
                                    @csrf
                                    <button type="submit" class="ui-app-logout">
                                        Keluar
                                    </button>
                                </form>
                            </nav>
                        @endunless
                    @endauth
                </div>
            </div>
        </header>

        <main class="relative mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8">
            @if (session('status'))
                <div class="ui-app-status">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="ui-app-error">
                    <p class="font-semibold">Masih ada data yang perlu dibenahi:</p>
                    <ul class="mt-2 space-y-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ $slot }}
        </main>

        <button
            type="button"
            data-scroll-top
            aria-label="Kembali ke atas"
            class="ui-scroll-top pointer-events-none translate-y-4 opacity-0"
        >
            Ke atas
        </button>
    </div>
    @if (auth()->check())
        <script>
            (() => {
                const reloadProtectedPage = () => {
                    window.location.reload();
                };

                window.addEventListener('pageshow', (event) => {
                    const navigationEntries = performance.getEntriesByType?.('navigation') ?? [];
                    const navigationType = navigationEntries[0]?.type ?? '';

                    if (event.persisted || navigationType === 'back_forward') {
                        reloadProtectedPage();
                    }
                });
            })();
        </script>
    @endif
</body>
</html>
