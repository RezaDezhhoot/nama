<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class AdminStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'comment' => ['required','string'],
            'action' => ['required','in:accept,action_needed,reject'],
            'offer_amount' => ['nullable','integer'],
            'final_amount' => ['nullable','integer'],
        ];
    }
}
