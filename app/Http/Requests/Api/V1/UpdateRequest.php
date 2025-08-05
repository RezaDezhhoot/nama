<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\RequestPlanStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'students' => ['sometimes','required','integer','between:1,1000000000'],
            'amount' => ['sometimes','required','integer','between:1000,10000000000000'],
            'date' => ['sometimes','required'],
            'body' => ['sometimes','nullable','max:10000'],
            'imam_letter' => $this->fileRules(),
            'area_interface_letter' => $this->fileRules(),

            'other_imam_letter' => ['nullable','array','max:10'],
            'other_imam_letter.*' => $this->fileRules(),

            'other_area_interface_letter' => ['nullable','array','max:10'],
            'other_area_interface_letter.*' => $this->fileRules(),

            'images' => ['nullable','array','max:10'],
            'images.*' => $this->fileRules(),

            'members' => ['array','nullable','max:1000'],
            'members.*' => ['required',Rule::exists('ring_members','id')->where('user_id',auth()->id())]
        ];
    }

    private function fileRules(): array
    {
        return ['sometimes','nullable',Rule::file()->max(100 * 1024 * 5)];
    }
}
