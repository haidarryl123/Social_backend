<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(){
        if (Auth::guard("admin")->check()){
            return redirect()->route("user_management");
        }
        return view("onstagram.auth.login");
    }

    public function postLogin(Request $request){
        $email = $request->email;
        $password = $request->password;

        $user = Admin::query()->where(["email" => $email])->first();
        if (!isset($user)){
            return response()->json(["result" => "error","data" => null,"message" => "This email is not registered"]);
        }

        if ($user->role !== ROLE_ADMIN){
            return response()->json(["result" => "error","data" => null,"message" => "Permission denied."]);
        }

        $credentials = [
            'email' => $email,
            'password' => $password
        ];
        $check = Auth::guard("admin")->attempt($credentials);
        if ($check) {
            $result = "success";
            $message = "Welcome back.";
        } else {
            $result = "error";
            $message = "Invalid password.";
        }

        return response()->json(["result" => $result,"data" => $check,"message" => $message]);
    }

    public function logout(){
        if (Auth::guard("admin")->check()){
            Auth::guard("admin")->logout();
        }
        return redirect()->route("login");
    }
}
