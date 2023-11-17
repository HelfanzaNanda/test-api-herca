<?php

namespace App\Http\Controllers;

use App\Enums\StatusEnum;
use App\Helpers\MailHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\StringHelper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        $email = $request->email;
        $password = $request->password;
        $user = User::query()
        ->whereRaw('lower(email) = lower(?)', [$email])
        ->first();

        if (!$user) {
            return response()->json(ResponseHelper::warning( message: 'Unauthorized', code: 401), 401);
        }


        $credentials = [
            'email' => $user->email,
            'password' => $request->password,
        ];

        $accessToken = auth()->attempt($credentials);
        if(!$accessToken){
            return response()->json(ResponseHelper::warning( message: 'Unauthorized', code: 401), 401);
        }
        $user = Auth::user();

        $role = $user->getRoleNames()[0];
        $user['role'] = $role;
        unset($user['roles']);
        unset($user['email_verified_at']);
        unset($user['created_at']);
        unset($user['updated_at']);
        unset($user['deleted_at']);
        unset($user['created_by']);
        unset($user['updated_by']);
        unset($user['deleted_by']);
        $data = [
            'user' => $user,
            'jwt' => [
                'access_token' => $accessToken,
                'token_type' => 'bearer',
                'expires_in' => 60 * 60
            ]
        ];
        return response()->json(ResponseHelper::success(data: $data), 200);
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(ResponseHelper::success(message: "Successfully logged out"), 200);
    }
}
