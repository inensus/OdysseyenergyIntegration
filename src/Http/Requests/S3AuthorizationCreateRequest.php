<?php


namespace Inensus\OdysseyS3Integration\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class S3AuthorizationCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'access_key' => 'required',
            'access_token' => 'required',
            'region' => 'required',
        ];
    }
}
