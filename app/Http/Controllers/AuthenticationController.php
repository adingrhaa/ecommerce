<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends Controller
{
    public function login_member(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        $user = Member::where('email', $request->email)->first();
    
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
    
        // Set status is_active menjadi true
        $user->is_active = true;
        $user->save();
    
        return $user->createToken('user login')->plainTextToken;
    }
    
    public function logout_member(Request $request)
    {
        $user = $request->user();
    
        if ($user) {
            // Set status is_active menjadi false
            $user->is_active = false;
            $user->save();
    
            // Hapus token akses saat ini
            $request->user()->currentAccessToken()->delete();
        }
    
        return response()->json(['message' => 'Logged out successfully'], 200);
    }

    public function me(Request $request){
        return response()->json(Auth::user());
    }
}