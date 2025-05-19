<?php
namespace App\Modules\Submissions\Jobs;

use App\Modules\Submissions\Models\Submission;
use App\Modules\Submissions\Enums\SubmissionStatus;
use App\Modules\Submissions\Services\CodeExecutionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Throwable;

class JudgeSubmissionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $submissionId;
    protected CodeExecutionService $executor;

    public function __construct(string $submissionId, CodeExecutionService $executor = null)
    {
        $this->submissionId = $submissionId;
        $this->executor = $executor ?? new CodeExecutionService();

        Log::info("JudgeSubmissionJob created for submission ID: {$this->submissionId}");
    }

    public function handle(): void
    {
        Log::info("Starting judging submission ID: {$this->submissionId}");

        $submission = Submission::find($this->submissionId);

        if (!$submission) {
            Log::warning("Submission ID {$this->submissionId} not found.");
            return;
        }

        $submission->update(['status' => SubmissionStatus::RUNNING]);
        $this->cacheStatus(SubmissionStatus::RUNNING);
        Log::info("Submission ID {$this->submissionId} marked as RUNNING.");

        $language = $submission->language;
        $code = $submission->code;
        $extension = $this->getFileExtension($language);
        $sourcePath = storage_path("app/code_{$submission->id}.$extension");

        file_put_contents($sourcePath, $code);
        Log::info("Code written to {$sourcePath}");

        try {
            $limits = $this->getConfig($language);
            Log::info("Execution limits: " . json_encode($limits));
            $result = $this->executor->execute($language, $sourcePath, $submission->id, $limits );
            $limitsJson = json_encode($limits);
            Log::info("Execution result for submission ID {$this->submissionId}: status={$result['status']}");

            $submission->update([
                'status' => $result['status'],
                'output' => $result['output'],
                'error'  => $result['error'],
            ]);

            $this->cacheStatus($result['status'], $result['output'], $result['error'], $limitsJson);
        } catch (Throwable $e) {
            $message = "Internal error: " . $e->getMessage();
            Log::error("Exception in judging submission ID {$this->submissionId}: " . $e->__toString());

            $submission->update([
                'status' => SubmissionStatus::FAILED,
                'output' => '',
                'error'  => $message,
            ]);

            $this->cacheStatus(SubmissionStatus::FAILED, '', $message);
        } finally {
            @unlink($sourcePath);
            Log::info("Source code file deleted: {$sourcePath}");
        }

        Log::info("Finished judging submission ID: {$this->submissionId}");
    }

    protected function getConfig(string $langName = null): array
    {
        $getConfigValue = function (string $key, $default) use ($langName) {
            if ($langName) {
                $value = config("submissions.judge.$langName.$key");
                if ($value !== null) {
                    return $value;
                }
            }

            $value = config("submissions.judge.$key");
            return $value !== null ? $value : $default;
        };

        return [
            'cpu_limit' => $getConfigValue('cpu_limit', '0.5'),
            'memory_limit' => $getConfigValue('memory_limit', '128m'),
            'time_limit_seconds' => $getConfigValue('time_limit_seconds', 2),
        ];
    }

    protected function getFileExtension(string $lang): string
    {
        switch ($lang) {
            case 'python':
                return 'py';
            case 'cpp':
                return 'cpp';
            case 'php':
                return 'php';
            case 'java':
                return 'java';
            case 'js':
                return 'js';
            default:
                throw new \RuntimeException("Unsupported language: $lang");
        }
    }

    protected function cacheStatus(string $status, ?string $output = null, ?string $error = null, ?string $limits = null): void
    {
        Redis::setex("submission_status_{$this->submissionId}", 3600, json_encode([
            'status' => $status,
            'output' => $output,
            'error' => $error,
            'limits' => $limits
        ]));
        Log::info("Cached submission status for ID {$this->submissionId}: {$status}");
    }
}
