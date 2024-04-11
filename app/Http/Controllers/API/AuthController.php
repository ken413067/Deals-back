<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\JWTAuth;



class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }



    public function login(Request $request)
    { 
        
        // return $request;
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        
        
        // 驗證碼答案Cookie擷取  解析前端傳來的驗證碼答案
        $encrypter = app(\Illuminate\Contracts\Encryption\Encrypter::class);  // 是一個方法
        $decryptedString = $encrypter->decrypt($request->captcha, false);  // 使用$encrypter 裡面的decrypt方法 去處理 $request裡面的captcha
        
        // $extracted_part為處理完的驗證碼答案
        $extracted_part = explode('|', $decryptedString)[1];  // 以|為基準做分割 |前為[0] |後為[1]  
        
        
        // 使用者輸入的答案
        $verification = $request->rv;
        
        
        
        // 取得id存進token payload方便後續使用
        $id = DB::table("users")->select("id")->where("email", "=", $request->email)->get();
        $id = $id[0]->id;
        // $id = $user->id;
        
        // 如果輸入的帳密正確，$token會su3得到一個token，否則會得到 false值
        $credentials = $request->only('email', 'password');
        $token = auth() ->claims(['email' => $request->email, 'id' => $id])
                        ->setTTL(120)
                        ->attempt($credentials);
                        
       
        // 得到用戶資訊
        $user = Auth::user();
        // return $extracted_part;
        
        $hasVerified = User::select("email_verified_at")->where('email', "=", $request->email)->first()->email_verified_at;
        // 驗證碼判斷成功而且token有值，才能成功登入
        // if ($extracted_part === $verification && $token && $hasVerified !== null) {
        if ($extracted_part === $verification && $token ) {
            return response()->json([
                // "email_verified_at" => $hasVerified,
                "message" => "驗證登入成功",
                'user' => $user,
                'authorization' => [
                    'token' => $token,
                    'type' => 'bearer',
                    
                ]
            ]);
            
        }   
        else {
            return response()->json([
                "message" => "驗證登入失敗",
            ], 401);

        }
    }

        
        // return response()->json([
        //     "captcha" => $decryptedString ,
        //     // "extracted_part" => $extracted_part,

        // ]);
        



        // $request->validate([
        //     'email' => 'required|string|email',
        //     'password' => 'required|string',
        // ]);
        
        // $credentials = $request->only('email', 'password');
        // $id = DB::table("users")->select("id")->where("email", "=", $request->email)->get();

        // // return response()->json([
        // //     "id" => $id[0]->id
        // // ]);
        // $token = auth() ->claims(['email' => $request->email, 'id' => $id[0]->id])
        //                 ->setTTL(120)
        //                 ->attempt($credentials);
        
        // if (!$token) {
        //     return response()->json([
        //         'message' => 'Unauthorized',
        //     ], 401);
        // } 


        // 這段是將token存到資料庫 當作記住我使用 
        // else { 
        //     $request = User::where("email", $request->email)->update([
        //         'remember_token' => $token,
        //     ]);
                
            
        // }
        
        
    //     $user = Auth::user();
    //     return response()->json([
    //         // "message" => $message,
    //         'user' => $user,
    //         'authorization' => [
    //             'token' => $token,
    //             'type' => 'bearer',
    //         ]
    //     ]);
    // }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    { 
        // die("ok");
        $token = $request->token;
        echo $token;
        // Auth::logout();
        return response()->json([
            'message' => 'Successfully logged out',
            // "authorization" => [
            //     "token" => $token,
            //     "type" => "bearer"
            // ]
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }
}