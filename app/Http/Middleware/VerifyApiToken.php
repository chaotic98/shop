<?php

namespace App\Http\Middleware;

use App\Exceptions\TokenMismatchException;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class VerifyApiToken
{
    public function handle(Request $request, Closure $next)
    {
        if ($this->verify($request)) {
            return $next($request);
        }
        throw new TokenMismatchException;
    }

    public function verify($request)
    {
        $user = User::firstWhere([
            'token'  => hash('sha256',$request->header('token')),
            // 'token'  => $request->header('token')
        ]);
        if (!$user) {
            return false;
        }

        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        return true;
    }
}
