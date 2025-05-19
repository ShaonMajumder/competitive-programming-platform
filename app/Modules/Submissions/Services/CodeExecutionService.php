<?php

namespace App\Modules\Submissions\Services;

use App\Modules\Submissions\Enums\SubmissionStatus;
use Illuminate\Support\Facades\Log;

class CodeExecutionService
{
    public function execute(string $language, string $sourcePath, string $submissionId, array $limits = []): array
    {
        $cpuLimit = $limits['cpu_limit'] ?? 2; // seconds
        $memoryLimit = $limits['memory_limit'] ?? '128m'; // 128MB
        $memoryLimitBytes = $this->convertMemoryToBytes($memoryLimit);
        $timeLimit = $limits['time_limit_seconds'] ?? 2;

        $output = [];
        $exitCode = 1;

        Log::info("Executing code for submission ID {$submissionId} in language {$language}");

        switch ($language) {
            case 'cpp':
                // $exeName = "code_{$submissionId}_exe";
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

                $cmd = sprintf(
                    'timeout %ds firejail --quiet --private --net=none --rlimit-as=%d --rlimit-cpu=%d %s 2>&1',
                    $timeLimit,
                    $memoryLimitBytes,
                    $cpuLimit,
                    escapeshellarg($exePath)
                );

                exec($cmd, $output, $exitCode);
                @unlink($exePath);
                break;

            case 'python':
                $cmd = sprintf(
                    'timeout %ds firejail --quiet --private --net=none --rlimit-as=%d --rlimit-cpu=%d python3 %s 2>&1',
                    $timeLimit,
                    $memoryLimitBytes,
                    $cpuLimit,
                    escapeshellarg($sourcePath)
                );

                exec($cmd, $output, $exitCode);
                break;

            default:
                Log::error("Unsupported language in execute(): $language");
                return [
                    'status' => SubmissionStatus::FAILED,
                    'output' => '',
                    'error' => 'Unsupported language',
                ];
        }

        if ($exitCode === 0) {
            Log::info("Execution succeeded for submission ID {$submissionId}");
            return [
                'status' => SubmissionStatus::EXECUTED,
                'output' => implode("\n", $output),
                'error' => null,
                'limits' => compact('cpuLimit', 'memoryLimit', 'timeLimit')
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

    private function convertMemoryToBytes(string $memory): int
    {
        $units = ['k' => 1024, 'm' => 1048576, 'g' => 1073741824];
        $unit = strtolower(substr($memory, -1));
        $value = (int) $memory;

        return $units[$unit] * $value ?? 134217728; // fallback 128MB
    }
}
