<?php


namespace Inensus\OdysseyS3Integration\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class S3SyncCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'bucket_name' => 'required',
            'object_location' => 'required',
            'tag_id' => ['required',Rule::unique('odyssey_s3_sync_objects')->ignore($this->id)],
        ];
    }
}

