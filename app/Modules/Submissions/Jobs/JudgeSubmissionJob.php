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
use App\Modules\Submissions\Helpers\ExecutionConfigHelper;
use Throwable;

class JudgeSubmissionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $submissionId;
    protected CodeExecutionService $executor;
    protected ExecutionConfigHelper $configService;
        
    public function __construct(string $submissionId, CodeExecutionService $executor = null, ExecutionConfigHelper $configService = null)
    {
        $this->submissionId = $submissionId;
        $this->executor = $executor ?? new CodeExecutionService();
        $this->configService = $configService ?? new ExecutionConfigHelper();
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
        $this->cacheStatus(SubmissionStatus::label(SubmissionStatus::RUNNING));
        Log::info("Submission ID {$this->submissionId} marked as RUNNING.");

        $language = $submission->language;
        $code = $submission->code;
        $extension = $this->getFileExtension($language);
        $sourcePath = storage_path("app/code_{$submission->id}.$extension");

        file_put_contents($sourcePath, $code);
        Log::info("Code written to {$sourcePath}");

        try {
            $limits = $this->configService->getLimits($language);
            Log::info("Execution limits: " . json_encode($limits));
            $result = $this->executor->execute($language, $sourcePath, $submission->id, $limits);
            Log::info("Execution result for submission ID {$this->submissionId}: status={$result['status']}");

            $submission->update([
                'status' => $result['status'],
                'output' => $result['output'],
                'error'  => $result['error'],
            ]);

            $this->cacheStatus( SubmissionStatus::label($result['status']), $result['output'], $result['error'], $limits);
        } catch (Throwable $e) {
            $message = "Internal error: " . $e->getMessage();
            Log::error("Exception in judging submission ID {$this->submissionId}: " . $e->__toString());

            $submission->update([
                'status' => SubmissionStatus::FAILED,
                'output' => '',
                'error'  => $message,
            ]);

            $this->cacheStatus(SubmissionStatus::label(SubmissionStatus::FAILED), '', $message);
        } finally {
            @unlink($sourcePath);
            Log::info("Source code file deleted: {$sourcePath}");
        }

        Log::info("Finished judging submission ID: {$this->submissionId}");
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

    protected function cacheStatus(string $status, ?string $output = null, ?string $error = null, ?array $limits = null): void
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
