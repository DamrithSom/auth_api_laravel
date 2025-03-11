<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
       try{
        $validatedData = $request->validate([
            'name' => 'required|max:55',
            'email' => 'email|required|unique:users',
            'password' => 'required|confirmed'
        ]);
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);
        $token = $user->createToken('api-token')->plainTextToken;
        return response()->json([
            'status' => '200 OK',
            'message' => 'success',
            'token' => $token,
            'token_type' => 'Bearer',
        ]); 
       }catch(Exception $e){
           return response()->json(['message' => 'Validation failed'], 422);
       }
    }
    public function login(Request $request)
    {
        try{
            $validatedData = $request->validate([
                'email' => 'email|required|exists:users,email',
                'password' => 'required'
            ]);
            $user = User::where('email', $validatedData['email'])->first();
            if(!$user || !Hash::check($validatedData['password'], $user->password)){
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }
            $token = $user->createToken('api-token')->plainTextToken;
            return response()->json([
                'status' => '200 OK',
                'message' => 'Login success',
                'token' => $token,
                'token_type' => 'Bearer',
            ]); 
           }catch(Exception $e){
               return response()->json(['message' => 'Validation failed'], 422);
           }
    }
}
