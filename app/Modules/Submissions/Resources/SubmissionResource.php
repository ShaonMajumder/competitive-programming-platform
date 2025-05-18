<?php

namespace App\Modules\Submissions\Resources;

use App\Modules\Submissions\Enums\SubmissionStatus;
use Illuminate\Http\Resources\Json\JsonResource;

class SubmissionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'user_id'    => $this->user_id,
            'problem_id' => $this->problem_id,
            'language'   => $this->language,
            'code'       => $this->code,
            'status'     => SubmissionStatus::label( $this->status ),
            'output'     => $this->output,
            'error'      => $this->error,
            'runtime'    => $this->runtime,
            'created_at' => $this->created_at,
        ];
    }
}
