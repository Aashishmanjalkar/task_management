<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function login(Request $request){

        $validator = Validator::make($request->only('email','password'),[
            'email' => 'required|email|exists:users',
            'password' => 'required',
        ]);
        if($validator->fails()){
            $response = ['response_code'=>422,'error'=>$validator->errors()->all()];
            return response()->json($response);
        }

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) return response()->json(['message' => 'Invalid password.'], 401);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('AuthToken')->plainTextToken;
            return response()->json(['token' => $token], 200);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

}

