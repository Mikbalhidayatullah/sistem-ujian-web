<div class="dashboard-card p-6 sm:p-7" data-refresh-interval="20000" data-refresh-key="violations-log">
    <x-ui.dashboard-header eyebrow="Anti-Kecurangan" title="Log pelanggaran">
        <x-slot:aside>
            <div class="flex flex-wrap gap-2">
                <x-ui.dashboard-pill>5 terbaru</x-ui.dashboard-pill>
                <x-ui.dashboard-pill>refresh 20 detik</x-ui.dashboard-pill>
            </div>
        </x-slot:aside>
    </x-ui.dashboard-header>

    <x-ui.scroll-panel height="28rem" class="mt-6" content-class="space-y-3 p-2 pr-3">
            @forelse ($violations as $violation)
                <div class="rounded-[1.5rem] border border-amber-200 bg-amber-50 p-4">
                    <p class="font-semibold text-amber-900">{{ $violation->attempt->participantName() }}</p>
                    <p class="mt-1 text-xs uppercase tracking-[0.3em] text-amber-700">{{ str_replace('_', ' ', $violation->violation_type) }}</p>
                    <p class="mt-2 text-sm text-amber-900">{{ $violation->detail ?: 'Pelanggaran terdeteksi otomatis.' }}</p>
                    <p class="mt-2 text-xs text-amber-700">{{ $violation->happened_at->format('d M Y H:i:s') }}</p>
                </div>
            @empty
                <p class="dashboard-muted-card p-4 text-sm text-slate-500">
                    Belum ada pelanggaran di ujian ini.
                </p>
            @endforelse
    </x-ui.scroll-panel>
</div>
