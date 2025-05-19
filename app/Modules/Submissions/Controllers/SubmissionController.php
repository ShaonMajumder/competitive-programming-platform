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
use App\Modules\Submissions\Helpers\ExecutionConfigHelper;

class SubmissionController extends Controller
{
    protected ExecutionConfigHelper $configService;
        
    public function __construct(ExecutionConfigHelper $configService = null)
    {
        $this->configService = $configService ?? new ExecutionConfigHelper();
    }

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
            'limits' => $this->configService->getLimits($submission->language)
        ]);
    }
}
