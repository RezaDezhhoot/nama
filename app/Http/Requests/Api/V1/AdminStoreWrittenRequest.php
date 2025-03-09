<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class AdminStoreWrittenRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'action' => ['required','in:accept,action_needed,reject'],
            'countable' => ['required','boolean'],
            'comment' => ['required','string'],
        ];
    }
}
