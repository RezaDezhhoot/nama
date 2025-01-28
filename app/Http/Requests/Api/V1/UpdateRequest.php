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
            'date' => ['sometimes','required','date'],
            'body' => ['sometimes','nullable','max:10000'],
            'imam_letter' => ['sometimes','required',Rule::file()->extensions([...config('site.files.image.formats'),...config('site.files.document.formats')])->max(5 * 1024)],
            'area_interface_letter' => ['sometimes','required',Rule::file()->extensions([...config('site.files.image.formats'),...config('site.files.document.formats')])->max(5 * 1024)],
        ];
    }
}
