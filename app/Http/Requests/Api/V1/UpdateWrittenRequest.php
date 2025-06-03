<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\OperatorRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWrittenRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'title' => ['sometimes','required','string','max:150'],
            'body' => ['sometimes','required','string','max:3000'],
            'letter' => ['sometimes','required',Rule::imageFile()->max(100 * 1024)],
            'sign' => ['sometimes','required',Rule::imageFile()->max(100 * 1024)],
            'reference_to' => ['sometimes','required',Rule::in(OperatorRole::DEPUTY_FOR_PLANNING_AND_PROGRAMMING->value,OperatorRole::EXECUTIVE_VICE_PRESIDENT_MOSQUES->value)]
        ];
    }
}
