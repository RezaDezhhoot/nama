<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\RequestStep;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'comment' => ['nullable','string'],
            'action' => ['required','in:accept,action_needed,reject'],
            'offer_amount' => ['nullable','integer'],
            'final_amount' => ['nullable','integer'],
            'to' => [$this->get('action' == "action_needed") ? "required":"nullable",Rule::enum(RequestStep::class)]
        ];
    }
}
