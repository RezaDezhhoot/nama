<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Ring;
use App\Rules\JDateRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property Ring $ring
 */
class UpdateRingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ring' => ['required'],
            'title' => ['sometimes','required','string','max:252'],
            'name' => ['sometimes','required','string','max:50'],
            'national_code' => ['sometimes','required','string','size:10'],
            'birthdate' => ['sometimes','required',new JDateRule],
            'postal_code' => ['sometimes','required','string','size:10'],
            'address' => ['sometimes','required','string','max:5000'],
            'phone' => ['sometimes','required','string','min:10','max:12'],
            'level_of_education' => ['sometimes','required','max:100'],
            'field_of_study' => ['sometimes','required','max:100'],
            'job' => ['sometimes','nullable','string','max:100'],
            'description' => ['sometimes','nullable','max:10000'],
            'sheba_number' => ['sometimes','nullable','string','max:100'],
            'skill_area' => ['sometimes','nullable','array','min:1','max:100'],
            'skill_area.*' => ['sometimes','required','string','max:100'],
            'functional_area' => ['sometimes','nullable','array','min:1','max:100'],
            'functional_area.*' => ['sometimes','required','string','max:100'],
            'image' => ['sometimes','nullable',Rule::imageFile()->max(1024 * 10)],
            'members' => ['sometimes','nullable','max:500'],
            'members.*.id' => ['sometimes','required','string','max:50',Rule::exists('ring_members','id')->where('ring_id',$this->ring->id)],
            'members.*.name' => Rule::forEach(function ($v , $attr , $i , $c){
                return [
                   ! empty( $c['id']) ? "sometimes" : null,
                    'required','string','max:50'
                ];
            }),
            'members.*.national_code' => Rule::forEach(function ($v , $attr , $i , $c){
                return [
                    ! empty( $c['id']) ? "sometimes" : null,
                    'required','string','max:10'
                ];
            }),
            'members.*.birthdate' => Rule::forEach(function ($v , $attr , $i , $c){
                return [
                    ! empty( $c['id']) ? "sometimes" : null,
                    'required',new JDateRule
                ];
            }),
            'members.*.postal_code' => Rule::forEach(function ($v , $attr , $i , $c){
                return [
                    ! empty( $c['id']) ? "sometimes" : null,
                    'required','string','size:10'
                ];
            }),
            'members.*.address' => Rule::forEach(function ($v , $attr , $i , $c){
                return [
                    ! empty( $c['id']) ? "sometimes" : null,
                    'required','string','max:3000'
                ];
            }),
            'members.*.phone' => Rule::forEach(function ($v , $attr , $i , $c){
                return [
                    ! empty( $c['id']) ? "sometimes" : null,
                    'required','string','min:10','max:12'
                ];
            }),
            'members.*.image' => Rule::forEach(function ($v , $attr , $i , $c){
                return [
                    ! empty( $c['id']) ? "sometimes" : null,
                    'nullable',Rule::imageFile()->max(1024 * 10)
                ];
            }),
            'members.*.father_name' =>  Rule::forEach(function ($v , $attr , $i , $c){
                return [
                    ! empty( $c['id']) ? "sometimes" : null,
                    'nullable','string','max:50'
                ];
            }),
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'ring' => Ring::query()->where('owner_id' , auth()->id())->findOrFail($this->route()->parameter('ring'))
        ]);
    }
}
