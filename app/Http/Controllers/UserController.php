<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class UserController extends Controller
{
    //login
    public function login(Request $request){
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            $token = $user->createToken('loveCarToken')->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token
            ]);
        }
        return response()->json([
            'data' => null,
            'message' => 'Unauthorized'
        ]);
    }
}

