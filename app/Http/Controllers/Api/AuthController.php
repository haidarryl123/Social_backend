<?php

namespace App\Http\Controllers\Api;

use App\Common\Helper;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    protected $helper;

    public function __construct(Helper $helper){
        $this->helper = $helper;
    }

    public function login(Request $request){
        $user = User::query()->where(['email' => $request->email])->first();
        if (isset($user) && $user->is_ban == 1) {
            return response()->json(['success' => false,'data' => null,'message' => 'User get banned.']);
        }

        $auth = $request->only('email','password');
        $token = auth()->attempt($auth);
        if (!$token){
            return response()->json(['success' => false,'data' => null,'message' => 'Wrong password.']);
        }
        $data = [
            'token' => $token,
            'user' => Auth::user()
        ];
        return response()->json(['success' => true,'data' => $data,'message' => 'Login successfully!']);
    }

    public function register(Request $request){
        $email = $request->email;
        $name = $request->name;
        $password = $request->password;

        $validate = $this->validateRegister($email,$password,$name);
        $result = $validate['result'];
        if (!$result){
            $message = $validate['message'];
            return response()->json(["success" => false, "data" => null, "message" => $message]);
        }

        $checkEmail = User::query()->where(['email' => $email])->exists();
        if ($checkEmail){
            return response()->json(["success" => false, "data" => null, "message" => "This email has been used."]);
        }

        DB::beginTransaction();
        try {
            User::query()->create([
                'email' => $email,
                'name' => $name,
                'password' => Hash::make($password)
            ]);
            DB::commit();
            return $this->login($request);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["success" => false, "data" => null, "message" => $e->getMessage()], 500);
        }
    }

    private function validateRegister($email,$password,$name){
        if (!isset($email) || strlen(trim($email)) == 0){
            return ['result' => false,'message' => 'Email is required.'];
        }
        if (!isset($password) || strlen(trim($password)) == 0){
            return ['result' => false,'message' => 'Password is required.'];
        }
        if (!isset($name) || strlen(trim($name)) == 0){
            return ['result' => false,'message' => 'Name is required.'];
        }
        return ['result' => true,'message' => ''];
    }

    public function logout(Request $request){
        try {
            JWTAuth::invalidate(JWTAuth::parseToken($request->token));
            return response()->json(["success" => true, "data" => null, "message" => "Logged out successfully"]);
        } catch (\Exception $e) {
            return response()->json(["success" => false, "data" => null, "message" => $e->getMessage()]);
        }
    }

    public function saveProfile(Request $request){
        $name = $request->name;
        $last_name = $request->last_name;
        $photo = $request->photo;
        try {
            $userId = Auth::id();
            $user = User::query()->find($userId);
            if (!isset($user)){
                return response()->json(["success" => false, "data" => null, "message" => "User not found."]);
            }

            $validate = $this->validateProfileData($name,$last_name);
            $result = $validate['result'];
            if (!$result){
                $message = $validate['message'];
                return response()->json(["success" => false, "data" => null, "message" => $message]);
            }

            $userData = [
                "name" => $name,
                "last_name" => $last_name
            ];

            if (isset($photo) && $photo != ''){
                //$this->helper->checkExistDirectory("public/profile/");
                $uploadFileDirectory = "storage/profile/";
                $image = time().".jpg";
                file_put_contents($uploadFileDirectory.$image,base64_decode($photo));
                $path = "/".$uploadFileDirectory.$image;
                $userData['photo'] = $path;
            }

            User::query()->where(["id" => $userId])->update($userData);
            $user = User::query()->where(["id" => $userId])->first();

            return response()->json(["success" => true, "data" => $user, "message" => "Saved profile successfully"]);
        } catch (\Exception $e) {
            return response()->json(["success" => false, "data" => null, "message" => $e->getMessage()], 500);
        }
    }

    private function validateProfileData($name,$last_name){
        if (!isset($name) || strlen(trim($name)) == 0){
            return ['result' => false,'message' => 'Name is required.'];
        }
        if (!isset($last_name) || strlen(trim($last_name)) == 0){
            return ['result' => false,'message' => 'Last name is required.'];
        }
        return ['result' => true,'message' => ''];
    }
}
