<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Exception;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) return sendError('Validation Error.', $validator->errors(), 422); 

        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials)) {
            
            $user = Auth::user();
            $success['name'] = $user->name;
            $success['token'] = $user->createToken('accessToken')->accessToken;

            return sendResponse($success, 'Login Successful.');

        } else {
            return sendError('Unauthorized', ['error' => 'Unauthorized'], 401);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) return sendError('Validation Error.', $validator->errors(), 422);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $success['name'] = $user->name;
            $message = 'Register Successful.';
            $success['token'] = $user->createToken('accessToken')->accessToken;
        } catch (Exception $e) {
            $success['token'] = [];
            $message = 'Register Failed.';
        }

        return sendResponse($success, $message);
    }

    public function logout()
    {
        try {
            Auth::logout();
            $success['token'] = [];
            $message = 'Logout Successful.';
        } catch (Exception $e) {
            $success['token'] = [];
            $message = 'Logout Failed.';
        }

        return sendResponse($success, $message);
    }
}
