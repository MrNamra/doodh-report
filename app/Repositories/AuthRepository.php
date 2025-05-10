<?php

namespace App\Repositories;

use App\Interfaces\AuthRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
//use Your Model

/**
 * Class AuthRepository.
 */
class AuthRepository implements AuthRepositoryInterface
{
    public function login($req) {
        if (Auth::attempt(['email' => $req->email, 'password' => $req->password])) {
            $user = Auth::user();
            $token = $user->createToken('YourApp')->plainTextToken;
            
            return response()->json(['status' => true, 'message' => 'Login SuccessFull!', 'token' => $token], 200);
        }
        return response()->json(['status' => false, 'message' => 'Email OR Password not match!'], 401);
    }
    public function register($request) {
        User::create([
            'name'=> $request->name,
            'email'=> $request->email,
            'password'=> bcrypt($request->password)
        ]);

        return response()->json(['status' => true, 'message' => 'User Register SuccessFully!'], 200);
    }
    public function updateProfile($request) {
        $user = User::findOrFail(Auth::user()->id);
        $user->name = $request->name;
        $user->email = $request->email;
        if($request->password) {
            if($request->password == $request->confirmpassword){
                $user->password = bcrypt($request->password);
            } else {
                return response()->json(['status' => false, 'message' => 'Passowrd and ConfirmPassowrd is not match!'], 400);
            }
        }
        $user->save();
        return response()->json(['status' => true, 'message' => 'Profile Update SuccessFully'], 200);
    }
}
