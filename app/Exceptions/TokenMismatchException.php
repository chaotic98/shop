<?php

namespace App\Exceptions;

use Exception;
use App\Http\Controllers\Controller;

class TokenMismatchException extends Exception
{
    public function report()
    {
        //
    }

    public function render($request)
    {
        $response = [
            'status' => [
                'code'=>401
            ]
        ];
        return Controller::setResponse($response);
    }
}
