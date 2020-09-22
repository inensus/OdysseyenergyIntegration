<?php


namespace Inensus\OdysseyS3Integration\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

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
            'object_path' => 'required',
        ];
    }
}

