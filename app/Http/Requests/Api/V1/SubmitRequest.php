<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\RequestPlanStatus;
use App\Rules\ShebaNumberRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property mixed $request_plan_id
 */
class SubmitRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'students' => ['required','integer','between:1,1000000000'],
            'amount' => ['required','integer','between:1000,10000000000000'],
            'date' => ['required'],
            'body' => ['nullable','max:10000'],
            'imam_letter' => ['nullable',Rule::file()->extensions([...config('site.files.image.formats'),...config('site.files.document.formats')])->max(5 * 1024)],
            'area_interface_letter' => ['nullable',Rule::file()->extensions([...config('site.files.image.formats'),...config('site.files.document.formats')])->max(5 * 1024)],
            'request_plan_id' => ['required','integer','min:1',Rule::exists('request_plans','id')->where('status',RequestPlanStatus::PUBLISHED)],
            'sheba' => ['nullable',new ShebaNumberRule]
        ];
    }
}
