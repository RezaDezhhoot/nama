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
            'date' => ['sometimes','required','date'],
            'body' => ['sometimes','nullable','max:10000'],
            'images_to_remove' => ['sometimes','array'],
            'amount' => ['sometimes','nullable','numeric','min:0'],
            'images_to_remove.*' => ['required','integer','min:1',Rule::exists('files','id')->where('fileable_id' , $id)->where('fileable_type' , $report->getMorphClass())],
            'remove_video' => ['sometimes','boolean'],
            'images' => [$report->images_count >= 3 ? 'sometimes' : null,'required','array',
                'min:'.(max(min(3 - $report->images_count + $images_to_remove , 3), 0)),
                'max:'.(max(min(10 - $report->images_count + $images_to_remove , 10) , 0))
            ],
            'images.*' => ['required',Rule::file()->extensions(config('site.files.image.formats'))->max(2 * 1024)],
            'video' => ['sometimes','nullable',Rule::file()->extensions(config('site.files.video.formats'))->max(5 * 1024)],
        ];
    }
}
