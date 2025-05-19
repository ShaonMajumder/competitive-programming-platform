<?php

namespace App\Modules\Submissions\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Submissions\Requests\StoreSubmissionRequest;
use App\Modules\Submissions\Resources\SubmissionResource;
use App\Modules\Submissions\Services\SubmissionService;
use App\Modules\Submissions\DTOs\SubmissionRequestDTO;
use App\Modules\Submissions\Enums\SubmissionStatus;
use App\Modules\Submissions\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;


class SubmissionController extends Controller
{
    public function create()
    {
        return view('submission::create');
    }

    public function store(StoreSubmissionRequest $request, SubmissionService $service)
    {
        $submissionData = SubmissionRequestDTO::fromArray($request->validated());
        $submission = $service->submit($submissionData);
        return new SubmissionResource($submission);
    }

    public function getSubmissionStatus($id)
    {
        $cacheKey = "submission_status_{$id}";

        $statusJson = Redis::get($cacheKey);
        if ($statusJson) {
            return response()->json(json_decode($statusJson, true));
        }

        // fallback: load from DB if cache miss
        $submission = Submission::findOrFail($id);

        return response()->json([
            'status' => SubmissionStatus::label($submission->status),
            'output' => $submission->output,
            'error' => $submission->error,
            'limits' => $this->getConfig($submission->language)
        ]);
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
}
