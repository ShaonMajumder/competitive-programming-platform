<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Modules\Submissions\Jobs\JudgeSubmissionJob;
use App\Modules\Submissions\Models\Submission;
use App\Modules\Submissions\Enums\SubmissionStatus;
use App\Modules\Submissions\Services\CodeExecutionService;
use Mockery;

class JudgeSubmissionJobTest extends TestCase
{
    /** @test */
    public function it_checks_cpp_submission_output_only()
    {
        $submission = Submission::create([
            'id' => 'cpp-test-id',
            'problem_id' => 'test-problem',
            'language' => 'cpp',
            'code' => <<<'CPP'
#include <iostream>
int main() {
    std::cout << "Hello from manually compiled C++!" << std::endl;
    return 0;
}
CPP,
            'status' => SubmissionStatus::QUEUED,
            'user_id' => 1,
        ]);

        // Mock the CodeExecutionService
        $executorMock = Mockery::mock(CodeExecutionService::class);
        $executorMock->shouldReceive('execute')
            ->once()
            ->with('cpp', \Mockery::type('string'), $submission->id)
            ->andReturn([
                'status' => SubmissionStatus::ACCEPTED,
                'output' => "Hello from manually compiled C++!\n",
                'error' => null,
            ]);

        $job = new JudgeSubmissionJob($submission->id, $executorMock);

        $job->handle();

        $submission->refresh();

        $this->assertEquals(SubmissionStatus::ACCEPTED, $submission->status);
        $this->assertEquals("Hello from manually compiled C++!\n", $submission->output);
    }

    /** @test */
    public function it_checks_python_submission_output_only()
    {
        $submission = Submission::create([
            'id' => 'python-test-id',
            'problem_id' => 'test-problem',
            'language' => 'python',
            'code' => 'print("hello")',
            'status' => SubmissionStatus::QUEUED,
            'user_id' => 1,
        ]);

        $executorMock = Mockery::mock(CodeExecutionService::class);
        $executorMock->shouldReceive('execute')
            ->once()
            ->with('python', \Mockery::type('string'), $submission->id)
            ->andReturn([
                'status' => SubmissionStatus::ACCEPTED,
                'output' => "hello\n",
                'error' => null,
            ]);

        $job = new JudgeSubmissionJob($submission->id, $executorMock);

        $job->handle();

        $submission->refresh();

        $this->assertEquals(SubmissionStatus::ACCEPTED, $submission->status);
        $this->assertEquals("hello\n", $submission->output);
    }

    protected function tearDown(): void
    {
        Submission::whereIn('id', ['cpp-test-id', 'python-test-id'])->delete();
        Mockery::close();
        parent::tearDown();
    }
}
