<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Modules\Submissions\Models\Submission;

class SubmissionApiTest extends TestCase
{
    use RefreshDatabase;  // resets DB after each test

    /** @test */
    public function it_creates_submission_via_api_and_checks_database()
    {
        $payload = [
            'problem_id' => null,
            'language'   => 'python',
            'code'       => 'print("hello")',
            'input'      => null,
            'user_id'    => 1,
        ];

        // Call your API endpoint
        $response = $this->postJson('/submission', $payload);

        // Assert HTTP status 201 Created or 200 OK
        $response->assertStatus(201);

        // Assert JSON structure or content
        $response->assertJsonStructure([
            'data' => [
                'id',
                'user_id',
                'problem_id',
                'language',
                'code',
                'status',
                'output',
                'error',
                'runtime',
                'created_at',
            ],
        ]);

        // Assert database has record
        $this->assertDatabaseHas('submissions', [
            'problem_id' => null,
            'language'   => 'python',
            'code'       => 'print("hello")',
            'user_id'    => null,
        ]);
    }

    /** @test */
    public function it_rejects_submission_with_missing_fields()
    {
        $payload = [
            'code' => 'print("hello")',
        ];

        $response = $this->postJson('/submission', $payload);
        $response->assertStatus(422); // validation error

        $response->assertJsonValidationErrors(['language']);
    }
}
