<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Form;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property Form $form
 */
class SubmitFormRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'form' => ['required'],
            'items' => ['array','required'],
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->route()->hasParameter('form')) {
            $this->merge([
                'form' => Form::query()->find($this->route()->parameter('form'))
            ]);
        }
    }
}
