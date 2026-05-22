<x-layouts.dashboard :title="$exam->title.' | Sistem Ujian'">
    <section class="space-y-6">
        @include('teacher.exams.partials.overview')

        <div class="space-y-6">
            @include('teacher.exams.partials.question-form')

            <div class="grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
                @include('teacher.exams.partials.question-list')

                <aside class="space-y-6">
                    @include('teacher.exams.partials.question-bank-library')
                    @include('teacher.exams.partials.violations-log')
                </aside>
            </div>
        </div>

        @include('teacher.exams.partials.question-insights')

        @include('teacher.exams.partials.scores-summary')
    </section>
</x-layouts.dashboard>
