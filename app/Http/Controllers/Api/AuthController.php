<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse {
        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8|max:255',
        ]);
        $user = User::where('email', $request->email)->first();
        if(!$user || !Hash::check($request->password, $user->password)){
            return response()->json([
                'message' => 'Incorrect information'
            ], 401);
        }
        $token = $user->createToken($user->name.'Auth-Token')->plainTextToken;
        return response()->json([
            'message' => 'Logged in successfully.',
            'token-type' => 'Bearer',
            'token' => $token
        ], 200);
    }
    public function register(Request $request): JsonResponse {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|unique:users,email|email|max:255',
            'password' => 'required|string|min:8|max:255|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        if($user){
            $token = $user->createToken($user->name.'Auth-Token')->plainTextToken;
            return response()->json([
                'message' => 'User created successfully.',
                'token-type' => 'Bearer',
                'token' => $token
            ], 200);
        }else{
            return response()->json([
                'message' => 'Something Went Wrong while registration.'
            ], 500);
        }
    }
    public function logout(Request $request) {
        $user = User::where('id', $request->user()->id)->first();
        if($user){
            $user->tokens()->delete();
            return response()->json([
                'message' => 'Logged Out Successfully.'
            ], 200);
        }else{
            return response()->json([
                'message' => 'User Not Found!'
            ], 404);
        }
    }
    public function profile(Request $request) {
        if($request->user()){
            return response()->json([
                'message' => 'Profile Fetched',
                'data' => $request->user()
            ], 200);
        }else{
            return response()->json([
                'message' => 'Unauthorized'
            ], 201);
        }
    }
}

