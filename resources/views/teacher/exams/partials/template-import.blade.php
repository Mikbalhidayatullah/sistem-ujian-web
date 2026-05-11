<div class="dashboard-card p-6 sm:p-7">
    <div>
        <p class="dashboard-kicker">Input Cepat</p>
        <h2 class="dashboard-section-title mt-2">Template soal otomatis</h2>
        <p class="dashboard-copy mt-3">
            Kalau soal sudah ada di Word atau Excel, Anda bisa copy lalu paste ke format ini agar sistem membuat banyak soal sekaligus.
        </p>
    </div>

    <div class="mt-6 rounded-[1.5rem] border border-slate-200 bg-slate-50 p-4 text-xs leading-6 text-slate-600">
        <p class="font-semibold uppercase tracking-[0.2em] text-sky-600">Format template</p>
        <pre class="mt-3 overflow-x-auto whitespace-pre-wrap font-mono text-[11px] leading-6 text-slate-600">Soal: Ibukota Indonesia adalah?
A. Jakarta
B. Bandung
C. Surabaya
D. Medan
Jawaban: A
Poin: 10

Soal: 5 x 6 = ...
A. 25
B. 30
C. 35
D. 40
Jawaban: B</pre>
    </div>

    <form method="POST" action="{{ route('teacher.exams.questions.import-template', $exam) }}" class="mt-6 space-y-5">
        @csrf
        <div class="grid gap-5 md:grid-cols-[0.8fr_1.2fr]">
            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-600">Poin default per soal</label>
                <input type="number" min="1" max="100" name="default_points" class="dashboard-input" value="{{ old('default_points', 10) }}" required>
                @error('default_points')
                    <p class="text-sm text-rose-500">{{ $message }}</p>
                @enderror
                <p class="text-xs leading-6 text-slate-500">
                    Nilai ini dipakai jika pada blok soal tidak ada baris <span class="font-semibold text-sky-600">Poin:</span>.
                </p>
            </div>
            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-600">Template soal</label>
                <textarea
                    name="question_template"
                    rows="18"
                    class="dashboard-input font-mono text-sm"
                    placeholder="Paste beberapa soal sekaligus di sini..."
                    required
                >{{ old('question_template') }}</textarea>
                @error('question_template')
                    <p class="text-sm text-rose-500">{{ $message }}</p>
                @enderror
                <p class="text-xs leading-6 text-slate-500">
                    Pisahkan setiap soal dengan satu baris kosong. Gunakan opsi A sampai D lalu akhiri dengan baris <span class="font-semibold text-sky-600">Jawaban:</span> atau <span class="font-semibold text-sky-600">Kunci:</span>.
                </p>
            </div>
        </div>
        <button type="submit" class="dashboard-button-soft">
            Buat soal otomatis dari template
        </button>
    </form>
</div>
