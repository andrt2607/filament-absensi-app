<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request){
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required'
        ]);

        // Check if the user exists
        $user = User::where('email', $request->email)->first();
        if(!$user || !Hash::check($request->password, $user->password)){
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Invalid credentials'
            ], 422);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer'
            ],
            'message' => 'User logged in successfully'
        ]);
    }

    public function getImageUserProfile(){
        $user = auth()->user();
        return response()->json([
            'success' => true,
            'data' => $user->image_url,
            'message' => 'Success get image user profile'
        ]);
    }
}
