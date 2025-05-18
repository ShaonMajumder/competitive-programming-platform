<?php

namespace App\Modules\Submissions\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubmissionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // 'problem_id' => 'nullable|exists:problems,id',
            'language'   => 'required|string|in:cpp,python,javascript',
            'code'       => 'required|string',
            'input'      => 'nullable|string',
        ];
    }

    public function validatedWithUser(): array
    {
        return array_merge($this->validated(), ['user_id' => $this->user()->id ]);
    }
}
