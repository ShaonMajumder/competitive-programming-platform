<?php

namespace App\Modules\Submissions\DTOs;

use Illuminate\Support\Str;
use InvalidArgumentException;

class SubmissionRequestDTO
{
    public ?string $problemId;
    public string $language;
    public string $code;
    public ?string $input;
    public ?int $userId;

    public function __construct(
        ?string $problemId,
        string $language,
        string $code,
        ?string $input,
        ?int $userId
    ) {
        $this->problemId = $problemId;
        $this->language = $language;
        $this->code = $code;
        $this->input = $input;
        $this->userId = $userId;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['problem_id'] ?? null,
            $data['language'],
            $data['code'],
            $data['input'] ?? null,
            $data['user_id'] ?? null
        );
    }

    public function validateLanguage(): void
    {
        $allowed = ['cpp', 'python', 'php', 'java', 'js'];

        if (!in_array($this->language, $allowed, true)) {
            throw new InvalidArgumentException("Unsupported language: {$this->language}");
        }
    }

    // sanitize code input
    public function sanitizeCode(): void
    {
        $this->code = trim($this->code);
    }

    // normalize problem ID (e.g., lowercase UUID)
    public function normalizeProblemId(): void
    {
        $this->problemId = Str::lower($this->problemId);
    }
}
