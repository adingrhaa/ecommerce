<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function login(){
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['email or password is wrong'], 401);
        }

        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 120
        ]);
    }

    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'fullname' => 'required',
            'country' => 'required',
            'city' => 'required',
            'gender' => 'required|in:male,female',
            'detail_alamat' => 'required',
            'no_hp' => 'required',
            'email' => 'required|email',
            'password' => 'required|same:konfirmasi_password',
            'konfirmasi_password' => 'required|same:password',
        ]);
    
        if ($validator->fails()){
            return response()->json(
                $validator->errors(),
                422
            );
        };
    
        $input = $request->all();
        $input['password'] = bcrypt($request->password);
        unset($input['konfirmasi_password']);
        $member = Member::create($input);
    
        return response()->json([
            'data' => $member
        ]);
    }

    // public function login_member(Request $request){
    //     $validator = Validator::make($request->all(), [
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ]);
    
    //     if ($validator->fails()){
    //         return response()->json(
    //             $validator->errors(),
    //             422
    //         );
    //     };

        
    //     $member = Member::where('email', $request->email)->first();
    //     if($member){

    //         if (Hash::check($request->password, $member->password)) {
    //             // $request->session()->regenerate();  //ketika menggunakan web {nyalakan}
    //             return response()->json([
    //                 'message' => 'success',
    //                 'data' => $member
    //             ]);
    //         }else{
    //             return response()->json([
    //                 'message' => 'failed',
    //                 'data' => 'password is wrong'
    //             ]);
    //         }
    //     }else{
    //         return response()->json([
    //             'message' => 'failed',
    //             'data' => 'email is wrong'
    //         ]);
    //     }
    // }

    public function logout(){
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    // public function logout_member(){
    //     Session::flush();

    //     redirect('/login');
    // }
}    