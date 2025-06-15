<?php

namespace App\Http\Requests\Api\V1;

use App\Rules\JDateRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubmitRingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required','string','max:252'],
            'name' => ['required','string','max:50'],
            'national_code' => ['required','string','size:10'],
            'birthdate' => ['required',new JDateRule],
            'postal_code' => ['required','string','size:10'],
            'address' => ['required','string','max:5000'],
            'phone' => ['required','string','min:10','max:12'],
            'level_of_education' => ['required','max:100'],
            'field_of_study' => ['required','max:100'],
            'job' => ['nullable','string','max:100'],
            'sheba_number' => ['nullable','string','max:100'],
            'skill_area' => ['nullable','array','min:1','max:100'],
            'skill_area.*' => ['required','string','max:100'],
            'functional_area' => ['nullable','array','min:1','max:100'],
            'functional_area.*' => ['required','string','max:100'],
            'image' => ['nullable',Rule::imageFile()->max(1024 * 10)],
            'members' => ['array','required','min:1','max:500'],
            'members.*.name' => ['required','string','max:50'],
            'members.*.national_code' => ['required','string','size:10'],
            'members.*.birthdate' => ['required',new JDateRule],
            'members.*.postal_code' => ['required','string','size:10'],
            'members.*.address' => ['required','string','max:3000'],
            'members.*.phone' => ['required','string','min:10','max:12'],
            'members.*.image' => ['nullable',Rule::imageFile()->max(1024 * 10)],
            'members.*.father_name' => ['nullable','string','max:50'],
        ];
    }
}
