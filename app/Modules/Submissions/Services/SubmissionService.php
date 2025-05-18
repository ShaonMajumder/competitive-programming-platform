<?php

namespace App\Modules\Submissions\Services;

use App\Modules\Submissions\DTOs\SubmissionRequestDTO;
use App\Modules\Submissions\Models\Submission;
use App\Modules\Submissions\Jobs\JudgeSubmissionJob;
use App\Modules\Submissions\Enums\SubmissionStatus;

class SubmissionService
{
    public function submit(SubmissionRequestDTO $dto): Submission
    {
        $data = [
            'problem_id' => $dto->problemId,
            'language' => $dto->language,
            'code' => $dto->code,
            'input' => $dto->input,
            'user_id' => $dto->userId,
            'status' => SubmissionStatus::QUEUED,
        ];

        $submission = Submission::create($data);
        JudgeSubmissionJob::dispatch($submission->id); # ->onQueue('judge');

        return $submission;
    }
}
