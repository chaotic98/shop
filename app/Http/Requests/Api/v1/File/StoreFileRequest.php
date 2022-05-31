<?php

namespace App\Http\Requests\Api\v1\File;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Http\FormRequest;

class StoreFileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'file' => ['required', 'mimes:jpg,png,jpeg,gif,pdf'],
            'directory_id' => ['required','exclude_if:directory_id,0','exists:App\Models\Directory,id']
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        $response = [
            'status' => [
                'code' => 400
            ],
            'error' => $errors
        ];
        throw new HttpResponseException(
            Controller::setResponse($response)
        );
    }
}
