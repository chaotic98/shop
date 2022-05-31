<?php

namespace App\Http\Requests\Api\v1\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class StoreRegisterRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $request = $this->request->all();
        $user = User::firstWhere('token', hash('sha256', $request['token']));
        return [
            'token' => [
                'required', 'unique:users,token',
                function ($attribute, $value, $fail) use ($user) {
                    if ($user) {
                        $fail('The user have already been registered');
                    }
                }
            ]
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
