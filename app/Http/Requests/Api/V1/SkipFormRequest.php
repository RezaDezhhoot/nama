<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SkipFormRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'forms' => ['required','array','min:1','max:100'],
            'forms.*' => ['required',Rule::exists('forms','id')]
        ];
    }
}
