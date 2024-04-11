<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ResetPassword;
use App\Models\UPda;
use App\Models\UpdateModels;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UpdateController extends Controller
{

    public function delete_cart(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',

        ]);

        $delete_item = User::select('name')->where('id', '=', $request->id)->get();
        User::where('id', '=', $request->id)->delete();

        return response()->json([
            'message' => 'User deleted successfully',
            'delete_item' => $delete_item
        ]);
    }


    // public function update_item(Request $request)
    // {
        
    //     // 接前端送來的token
    //     // $token = $request->token;
    //     // 判斷資料是不是符合格式
    //     // $request->validate([
    //     //     // 'id' => 'required|integer',
    //     //     // 'name' => 'string',
    //     //     // "password" => "integer",
    //     // ]);

    //     // COOKIE版本
    //     $token = $request->token;
    //     // echo $token;
    //     $decoded_token = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', $token)[1]))));
    //     $email = $decoded_token->email;
    //     return $request;



    //     // // TOKEN放資料庫版本
    //     // $token = User::select('remember_token')->where('email', '=', $request->email)->get();
    //     // echo $token[0];
    //     // $token = $token[0]['remember_token'];




    //     // $data = $request->imageApple;
    //     // $data = str_replace("C:\\fakepath\\", "D:\\06\\test\\", $data);
    //     // $imageData = base64_encode($data);
    //     // $src = "base64,$imageData";

    //     // $src = $request->imageApple;


    //     $original_image = User::select('image')->where('email', '=', $email)->get();
    //     $original_name = User::select('name')->where('email', '=', $email)->get();
    //     $original_password = User::select('password')->where('email', '=', $email)->get();

    //     $updateData = [];

    //     // 判斷使用者修改甚麼欄位
    //     if (($request->imageApple) != "" && ($request->imageApple != $original_image)) {
    //         $updateData['image'] = $request->imageApple;
    //         $src = $request->imageApple;
    //     } else if (($request->imageApple) == "") {
    //         $src = $original_image;
    //     }

    //     if (($request->name) != "" && ($request->name != $original_name)) {
    //         $updateData['name'] = $request->name;
    //     }

    //     if (($request->password) != "" && ($request->password != $original_password)) {
    //         $updateData['password'] = Hash::make($request->password);
    //     }





    //     // // 判斷使用者修改甚麼欄位
    //     // if (($request->name) == "" &&  ($request->password) != "") {
    //     //     $updateData = [
    //     //         // "image" => $src,
    //     //         'password' => Hash::make($request->password)
    //     //     ];
    //     //     die("OK");
    //     // } else if (($request->password) == "" && ($request->name) != "") {
    //     //     $updateData = [
    //     //         // "image" => $src,
    //     //         'name' => $request->name
    //     //     ];
    //     // } else {
    //     //     $updateData = [
    //     //         // "image" => $src,
    //     //         'name' => $request->name,
    //     //         'password' => Hash::make($request->password)
    //     //     ];
    //     // };

        
    //     User::where('email', '=', $email)->update($updateData);


    //     return response()->json([
    //         // 'data2' => $data,
    //         "src" => $src,
    //         'message' => 'Item updated successfully',
    //         // "update_item" => $update_item
    //     ]);
    //     // ->header("Access-Control-Allow-Origin", "*");
    // }
    
    public function update_password(Request $request)
    {
        
        $token = $request->token;

        // $decoded_token = json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1]))));
        $password = $request->password;
        $password_confirmation = $request->password_confirmation;
        $email = $request->email;

        $original_password = User::select('password')->where('email', '=', $email)->get();
        $updateData = [];

        // echo $original_image;
        // 判斷使用者修改甚麼欄位
        if ( ($password != $original_password) &&  ($password == $password_confirmation ) ) {
            $updateData['password'] = Hash::make($request->password);
        }
        $user_email = ResetPassword::select('email')->where( "token", "=", $token)->where( "email", "=", $email)->get();//->update($updateData);
        // return response()->json([
        //     'message' => $user_email
        // ]);
        User::where('email', "=", $user_email[0]->email)->update($updateData);
        
        return response()->json([
            'message' => 'Item updated successfully',
        ]);
    }

}

