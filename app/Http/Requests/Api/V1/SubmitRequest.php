<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\PlanTypes;
use App\Enums\RequestPlanStatus;
use App\Models\RequestPlan;
use App\Rules\ShebaNumberRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property mixed $request_plan_id
 * @property ?RequestPlan $request_plan
 */
class SubmitRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'students' => [$this->isNormal() ? 'required' : 'nullable' ,'integer','between:1,1000000000'],
            'amount' => [$this->isNormal() ? 'required' : null ,'integer','between:0,10000000000000'],

            'title' => [! $this->isNormal() ? 'required' : 'nullable' ,'string','max:250'],
            'location' => [! $this->isNormal() ? 'required' : 'nullable' ,'string','max:30000'],

            'date' => ['required'],
            'body' => ['nullable','max:10000'],

            'imam_letter' => $this->fileRules(),

            'area_interface_letter' => $this->fileRules(),

            'request_plan_id' => ['required','integer','min:1',Rule::exists('request_plans','id')->where('status',RequestPlanStatus::PUBLISHED)],
            'sheba' => ['nullable',new ShebaNumberRule],
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
        return ['nullable',Rule::file()->max(100 * 1024)];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'request_plan' => RequestPlan::query()->find($this->request_plan_id)
        ]);
    }

    private function isNormal(): bool
    {
        return $this->request_plan?->type === PlanTypes::DEFAULT;
    }
}
