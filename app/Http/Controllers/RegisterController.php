<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Family;
use App\User;
use App\UserFamily;
use \Firebase\JWT\JWT;
use Exception;
use DB;

class RegisterController extends Controller {
    
    /**
     * Function that will be called to register a new user
     * @return [type] [description]
     */
    public function registerNewUser(Request $request) {
        $responseArray  = array();

        $email          = trim($request->input('email'));
        $fullName       = trim($request->input('fullName'));
        $displayName    = trim($request->input('displayName'));
        $familyString   = trim($request->input('familyString'));
        $password       = trim($request->input('password'));
        $type           = trim($request->input('type'));
        $salt           = str_random(5);

        DB::beginTransaction();
        try {
            $user = User::where('userEmail', '=', $email)->first();
            if ($user !== null) {
                throw new Exception("User already Exists", 1);
            }

            // Create the user
            $user = new User;
            $user->userEmail    = $email;
            $user->userSlack    = uniqid();
            $user->fullName     = $fullName;
            $user->password     = md5($password . $salt);
            $user->salt         = $salt;
            $user->displayName  = $displayName;            
            $user->save();

            // Crate JWT Key and data
            $secretKey  = env('JWT_SECRET');
            $data       = array(
                'email'     =>  $email,
                'fullName'  =>  $displayName,
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

            if($type == "NEW") {

                // If the family is new, then create the new family
                $family = new Family();
                $family->familyCode     = str_random(10);
                $family->familySlack    = uniqid();
                $family->familyName     = $familyString;
                $family->save();

                // Assign that family code to the person
                $userFamily = new UserFamily();
                $userFamily->user_id            = $user->id;
                $userFamily->user_displayName   = $user->displayName;
                $userFamily->family_id          = $family->id;
                $userFamily->user_familyName    = $family->familyName;
                $userFamily->save();

            } else if($type == "EXISTING") {
                $family = Family::where('familyCode', '=', $familyString)->first();
                if ($family === null) {
                    throw new Exception("User Family Code does not exist", 3);
                }
                
                // Assign that family code to the person
                $userFamily = new UserFamily();
                $userFamily->user_id            = $user->id;
                $userFamily->user_displayName   = $user->displayName;
                $userFamily->family_id          = $family->id;
                $userFamily->user_familyName    = $family->familyName;
                $userFamily->save();

            } else {
                throw new Exception("Type is mismatch", 2);
            }

            DB::commit();

            $responseArray = array(
                'status'        =>  true,
                'token'         =>  $token,
                'familySlack'   =>  $family->familySlack,
                'reason'        =>  'Registration Successful',
            );            

        } catch(Exception $e){
            
            DB::rollback();

            $responseArray = array(
                'status'    =>  false,
                'reason'    =>  $e->getMessage(),
            );
        }
        return $responseArray;
       
    }

    public function test(Request $request) {
        echo "Testing";
    }
}