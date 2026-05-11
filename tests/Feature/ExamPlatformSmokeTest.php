<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamPlatformSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_shows_exam_access_and_teacher_login(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Masuk ujian siswa');
        $response->assertSee('Login admin / guru');
        $response->assertDontSee('Daftar');
    }

    public function test_admin_can_login_and_is_redirected_to_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        $response = $this->post(route('login.store'), [
            'email' => $admin->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($admin);

        $dashboard = $this->get(route('dashboard'));

        $dashboard->assertRedirect(route('admin.dashboard'));
    }

    public function test_teacher_cannot_access_admin_accounts_page(): void
    {
        $teacher = User::factory()->create([
            'role' => User::ROLE_TEACHER,
        ]);

        $response = $this->actingAs($teacher)->get(route('admin.accounts.index'));

        $response->assertForbidden();
    }

    public function test_student_can_start_and_submit_exam_without_a_student_account(): void
    {
        $teacher = User::factory()->create([
            'role' => User::ROLE_TEACHER,
        ]);

        $subject = Subject::query()->create([
            'teacher_id' => $teacher->id,
            'name' => 'Matematika',
            'description' => 'Kelas 10',
        ]);

        $exam = Exam::query()->create([
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'title' => 'Ujian Matematika',
            'description' => 'Tes cepat',
            'access_token' => 'MATH10',
            'access_pin' => '123456',
            'duration_minutes' => 60,
            'max_violations' => 3,
            'is_active' => true,
            'start_at' => now()->subHour(),
            'end_at' => now()->addHour(),
        ]);

        $question = Question::query()->create([
            'exam_id' => $exam->id,
            'prompt' => '2 + 2 = ...',
            'points' => 10,
            'position' => 1,
        ]);

        $correctOption = QuestionOption::query()->create([
            'question_id' => $question->id,
            'option_text' => '4',
            'is_correct' => true,
            'position' => 1,
        ]);

        QuestionOption::query()->create([
            'question_id' => $question->id,
            'option_text' => '5',
            'is_correct' => false,
            'position' => 2,
        ]);

        QuestionOption::query()->create([
            'question_id' => $question->id,
            'option_text' => '6',
            'is_correct' => false,
            'position' => 3,
        ]);

        QuestionOption::query()->create([
            'question_id' => $question->id,
            'option_text' => '7',
            'is_correct' => false,
            'position' => 4,
        ]);

        $statusResponse = $this->get(route('exam.access.status', [
            'access_token' => 'MATH10',
            'access_pin' => '123456',
        ]));

        $statusResponse->assertOk();
        $statusResponse->assertJson([
            'can_start' => true,
            'state' => 'open',
        ]);

        $startResponse = $this->post(route('exam.access.start'), [
            'full_name' => 'Budi Santoso',
            'access_token' => 'MATH10',
            'access_pin' => '123456',
        ]);

        $attempt = ExamAttempt::query()->firstOrFail();

        $startResponse->assertRedirect(route('exam.attempts.show', $attempt));

        $submitResponse = $this->post(route('exam.attempts.submit', $attempt), [
            'answers' => [
                $question->id => $correctOption->id,
            ],
        ]);

        $submitResponse->assertRedirect(route('exam.attempts.result', $attempt));

        $attempt->refresh();

        $this->assertSame(ExamAttempt::STATUS_SUBMITTED, $attempt->status);
        $this->assertSame('100.00', $attempt->score);

        $resultResponse = $this->get(route('exam.attempts.result', $attempt));

        $resultResponse->assertOk();
        $resultResponse->assertSee('Budi Santoso');
        $resultResponse->assertSee('100.00');
    }
}
