<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        error_log($request);
        $validated = $request->validate([
            'name' => 'required|max:255',
            'phone_number' => 'required|unique:users,phone_number',
            'password' => 'required|min:6',
        ]);

        $user = User::create($validated);
        $token = $user->createToken($user->name);

        return [
            'user' => $user,
            'accessToken' => $token->plainTextToken,
        ];
    }
    public function login(Request $request)
    {
        $request->validate([
            'phone_number' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('phone_number', $request->phone_number)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return [
                'message' => 'phone number or password is incorrect !'
            ];
        }
        $token = $user->createToken($user->name);

        return [
            'user' => $user,
            'accessToken' => $token->plainTextToken,
        ];
    }
    public function logout(Request $request)
    {
        if ($request->user()->currentAccessToken()->delete()) {
            return response()->json(['message' => 'you have been successfullt logged out.']);
        }
        return response()->json(['message' => 'server error !'], 500);
    }
}
