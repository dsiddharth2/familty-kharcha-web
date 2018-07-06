<?php

namespace App\Http\Middleware;

use Closure;
use \Exception;
use App\User;

class Authentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $arrayresponse = array();
        try {
            if(!$request->isJson()) {
                throw new Exception("Request should be in josn format", 101);
            }

            $content = json_decode($request->getContent(), true);
            if(json_last_error() != JSON_ERROR_NONE) {
                throw new Exception("JSON Error occured while decoding the request.", 102);
            }

            $token = trim($content['token']);
            $user = User::where('token', '=', $token)->first();
            if ($user === null) {
                throw new Exception("Invalid Token", 103);
            }

            $data = array(
                "user_id"       =>  $user->id,
                "displayName"   =>  $user->displayName,                
            );            
            $request->merge($data);
            
            return $next($request);
        } catch (Exception $e) {
            $arrayresponse = array(
                'status'    =>  false,
                'message'   =>  $e->getMessage(),
                'code'      =>  $e->getCode()
            );
            return response(json_encode($arrayresponse));
        }        
    }
}
