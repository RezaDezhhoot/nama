<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class SubmitClientLogRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'context' => ['nullable','string','max:10000000'],
            'client_version' => ['nullable','regex:/^[0-9]{1,}\.[0-9]{1,}\.[0-9]{1,}$/'],
            'platform' => ['nullable','string','max:100']
        ];
    }
}
