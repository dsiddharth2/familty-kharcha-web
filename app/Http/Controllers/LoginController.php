<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Exception;
use \Firebase\JWT\JWT;

class LoginController extends Controller {

    /**
     * Function that logins into the app using a password
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function checkLogin(Request $request) {
        $responseArray  = array();

        $email          = trim($request->input('email'));
        $password       = trim($request->input('password'));

        try {
            
            $user = User::where('userEmail', '=', $email)->first();
            if ($user === null) {
                throw new Exception("Email OR Password does not match", 1);
            }

            $password = md5($password . $user->salt);
            $user = User::where([
                                    ['userEmail', '=', $email],
                                    ['password', '=', $password],
                                ])->first();
            if ($user === null) {
                throw new Exception("User OR Password does not match", 2);
            }

            // Crate JWT Key and data
            $secretKey  = env('JWT_SECRET');
            $data       = array(
                'email'     =>  $email,
                'fullName'  =>  $user->fullName,
                'slack'     =>  $user->userSlack,
                'user_id'   =>  $user->id
            );
            $token = JWT::encode(
                $data,
                $secretKey,
                'HS512'
            );
            $user->token = $token;
            $user->save();

            $responseArray = array(
                'status'    =>  true,
                'token'     =>  $token,
                'reason'    =>  'Login Successful',
            );             
        } catch(Exception $e){
            $responseArray = array(
                'status'    =>  false,
                'reason'    =>  $e->getMessage()
            );
        }

        return $responseArray;
    }
}
