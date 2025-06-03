<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\OperatorRole;
use App\Enums\WrittenRequestRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubmitWrittenRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required','string','max:150'],
            'body' => ['required','string','max:3000'],
            'letter' => ['nullable',Rule::imageFile()->max(100 * 1024)],
            'sign' => ['nullable',Rule::imageFile()->max(100 * 1024)],
            'reference_to' => ['required',Rule::enum(WrittenRequestRole::class)]
        ];
    }
}
