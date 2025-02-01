<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\OperatorRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubmitWrittenRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required','string','max:150'],
            'body' => ['required','string','max:3000'],
            'letter' => ['required',Rule::imageFile()->max(5 * 1024)],
            'sign' => ['required',Rule::imageFile()->max(5 * 1024)],
            'reference_to' => ['required',Rule::in(OperatorRole::DEPUTY_FOR_PLANNING_AND_PROGRAMMING->value,OperatorRole::EXECUTIVE_VICE_PRESIDENT_MOSQUES->value)]
        ];
    }
}
