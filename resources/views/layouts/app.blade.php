<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Sistem Ujian') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <div class="min-h-screen bg-[radial-gradient(circle_at_top_right,_rgba(22,163,74,0.15),_transparent_20%),radial-gradient(circle_at_bottom_left,_rgba(8,145,178,0.18),_transparent_30%),linear-gradient(180deg,_#020617,_#0f172a)]">
        <header class="border-b border-white/10 bg-slate-950/70 backdrop-blur">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
                <div>
                    <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" class="text-lg font-black tracking-wide text-white">
                        Sistem Ujian
                    </a>
                    @auth
                        <p class="text-sm text-slate-400">{{ auth()->user()->name }} · {{ auth()->user()->isTeacher() ? 'Guru' : 'Siswa' }}</p>
                    @endauth
                </div>

                @auth
                    <nav class="flex items-center gap-3">
                        <a href="{{ route('dashboard') }}" class="rounded-full border border-white/10 px-4 py-2 text-sm text-slate-200 hover:bg-white/10">
                            Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-full bg-rose-500 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-400">
                                Keluar
                            </button>
                        </form>
                    </nav>
                @endauth
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-6 py-8">
            @if (session('status'))
                <div class="mb-6 rounded-2xl border border-emerald-300/20 bg-emerald-400/10 px-5 py-4 text-emerald-100">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-2xl border border-rose-300/20 bg-rose-400/10 px-5 py-4 text-rose-100">
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
    </div>
</body>
</html>
