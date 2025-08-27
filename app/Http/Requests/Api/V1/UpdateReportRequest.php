<?php

namespace App\Http\Requests\Api\V1;

use App\Models\File;
use App\Models\Report;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReportRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->route()->parameter('report');
        $report = Report::query()->withCount('images')->findOrFail($id);
        $images_to_remove = sizeof($this->get('images_to_remove' , []));
        return [
            'students' => ['sometimes','required','integer','between:1,1000000000'],
            'date' => ['sometimes','required'],
            'body' => ['sometimes','nullable','max:10000'],
            'images_to_remove' => ['sometimes','array'],
            'amount' => ['sometimes','nullable','numeric','min:0'],
            'images_to_remove.*' => ['required','integer','min:1',Rule::exists('files','id')->where('fileable_id' , $id)->where('fileable_type' , $report->getMorphClass())],
            'remove_video' => ['sometimes','boolean'],
            'images' => ['sometimes','required','array'],
            'images.*' => ['required',Rule::file()->max(100 * 1024 * 5)],
            'video' => ['sometimes','nullable',Rule::file()->max(100 * 1024 * 5)],
            'otherVideos' => ['nullable','array','max:10'],
            'otherVideos.*' => ['sometimes','nullable',Rule::file()->max(100 * 1024 * 5)],
        ];
    }
}
