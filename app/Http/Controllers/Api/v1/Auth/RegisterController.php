<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Auth\StoreRegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function store(StoreRegisterRequest $request)
    {
        $user = User::create([
            'token' =>  hash('sha256', $request->token)
        ]);

        $response = [
            'status' => [
                'code' => 201,
            ],
            'data' =>
            [
                'user' => [
                    'id' => $user->id,
                    'api_token' => $request->token
                ]

            ]

        ];

        return Controller::setResponse($response);
    }
}
