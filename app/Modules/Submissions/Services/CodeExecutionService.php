<?php

namespace App\Modules\Submissions\Services;

use App\Modules\Submissions\Enums\SubmissionStatus;
use Illuminate\Support\Facades\Log;

class CodeExecutionService
{
    public function execute(string $language, string $sourcePath, string $submissionId): array
    {
        $output = [];
        $exitCode = 1;

        Log::info("Executing code for submission ID {$submissionId} in language {$language}");

        switch ($language) {
            case 'cpp':
                $exePath = storage_path("app/code_{$submissionId}_exe");
                exec("g++ $sourcePath -o $exePath 2>&1", $compileOutput, $compileExitCode);

                if ($compileExitCode !== 0) {
                    Log::warning("Compilation failed for submission ID {$submissionId}: " . implode("\n", $compileOutput));
                    return [
                        'status' => SubmissionStatus::COMPILATION_ERROR,
                        'output' => implode("\n", $compileOutput),
                        'error'  => 'Compilation failed',
                    ];
                }

                exec("$exePath 2>&1", $output, $exitCode);
                @unlink($exePath);
                break;

            case 'python':
                exec("python3 $sourcePath 2>&1", $output, $exitCode);
                break;

            case 'php':
                exec("php $sourcePath 2>&1", $output, $exitCode);
                break;

            case 'java':
                Log::warning("Java execution not implemented for submission ID {$submissionId}");
                return [
                    'status' => SubmissionStatus::FAILED,
                    'output' => '',
                    'error' => 'Java execution not implemented',
                ];

            case 'js':
                exec("node $sourcePath 2>&1", $output, $exitCode);
                break;

            default:
                Log::error("Unsupported language in executeCode(): $language");
                return [
                    'status' => SubmissionStatus::FAILED,
                    'output' => '',
                    'error' => 'Unsupported language',
                ];
        }

        if ($exitCode === 0) {
            Log::info("Execution succeeded for submission ID {$submissionId}");
            return [
                'status' => SubmissionStatus::ACCEPTED,
                'output' => implode("\n", $output),
                'error' => null,
            ];
        } else {
            Log::warning("Execution failed for submission ID {$submissionId} with exit code {$exitCode}: " . implode("\n", $output));
            return [
                'status' => SubmissionStatus::RUNTIME_ERROR,
                'output' => implode("\n", $output),
                'error' => 'Execution failed',
            ];
        }
    }
}
