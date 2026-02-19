<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class SendAuthRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'callback' => ['required','url','max:1500']
        ];
    }
}
