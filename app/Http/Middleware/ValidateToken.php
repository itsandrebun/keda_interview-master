<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class ValidateToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            if($request->input('user_id') == ""){
                $response = (Object)array(
                    'status' => 401,
                    'message' => 'You should login first'
                );
                return response()->json($response,401);
            }else if(auth()->user()['deleted_at'] != null){
                auth()->logout();
                $response = (Object)array(
                    'status' => 404,
                    'message' => 'User not found'
                );
                return response()->json($response,404);
            }else if($request->input('user_id') != auth()->user()['user_id']){
                $response = (Object)array(
                    'status' => 401,
                    'message' => 'Unauthorized login'
                );
                return response()->json($response,401);
            }else{
                $user = JWTAuth::setRequest($request)->parseToken()->authenticate();
            }
        } catch (Exception $e) {
            if($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                $response = (Object)array(
                    'status' => 401,
                    'message' => 'Invalid token'
                );
                return response()->json($response, 401);
            }else if($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                $response = (Object)array(
                    'status' => 401,
                    'message' => 'Expired token'
                );
                return response()->json($response, 401);
            }else{
                $response = (Object)array(
                    'status' => 404,
                    'message' => 'Authorization token not found'
                );
                return response()->json($response, 404);
            }
        }
        return $next($request);
    }
}
