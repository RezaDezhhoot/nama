<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubmitReportRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'students' => ['required','integer','between:1,1000000000'],
            'date' => ['required','date'],
            'body' => ['nullable','max:10000'],
            'images' => ['required','array','min:3','max:10'],
            'images.*' => ['required',Rule::file()->extensions(config('site.files.image.formats'))],
            'video' => ['nullable',Rule::file()->extensions(config('site.files.video.formats'))],
        ];
    }
}
