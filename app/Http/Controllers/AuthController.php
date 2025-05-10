<?php

namespace App\Http\Controllers;

use App\Interfaces\AuthRepositoryInterface;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    private $authRepo;
    public function __construct(AuthRepositoryInterface $authRepo){
        $this->authRepo = $authRepo;
    }
    public function index(){
        if(Auth::check()){
            return view("dashboard");
        }
        return view("login");
    }
    public function login(Request $req){
        $validator = Validator::make($req->all(),[
            'email' => 'required|email',
            'password' => 'required|min:8|max:64'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        return $this->authRepo->login($req);
    }
    public function registerPage(){
        if(Auth::check()){
            return redirect('/');
        }
        return view('register');
    }
    public function register(Request $req){
        try{
            $validator = Validator::make($req->all(),[
                'name' => 'required|min:4|max:64',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8|max:64',
                'confirmpassword' => 'required|same:password'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            
            return $this->authRepo->register($req);
        }catch(Exception $e){
            Log::info("AuthConteroller/register: ". $e->getMessage());
            return response()->json(['status'=> false,'message'=> "Internal Server Error"],500);
        }
    }
    public function profile(){
        try{
            $data = User::select('name', 'email')->find(Auth::user()->id);
            
            return response()->json(['status' => true, 'data' => $data], 200);
        }catch(Exception $e){
            Log::info("AuthConteroller/register: ". $e->getMessage());
            return response()->json(['status'=> false,'message'=> "Internal Server Error: ".$e->getMessage()],500);
        }
    }
    public function updateProfile(Request $req){
        try{
            $validator = Validator::make($req->all(),[
                'name' => 'required|min:4|max:64',
                'email' => 'required|email',
                'password' => 'nullable|min:8|max:64',
                'confirmpassword' => 'same:password'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'=> false,
                    'message'=> $validator->errors()
                ],401);
            }

            return $this->authRepo->updateProfile($req);
        }catch(Exception $e){
            Log::info("AuthConteroller/register: ". $e->getMessage());
            return response()->json(['status'=> false,'message'=> "Internal Server Error"],500);
        }
    }
    public function logout(Request $req){
        Auth::logout();
        return response()->json(['message' => 'Successfully logged out'], 200);
    }
}
