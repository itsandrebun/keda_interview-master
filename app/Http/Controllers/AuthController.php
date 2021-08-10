<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Session;
use Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    
    public function login()
    {
        $credentials = request(['email', 'password']);
        $status = 200;
        $response = array();
        
        if (! $token = auth()->attempt($credentials)) {
            $status = 401;
            $response = (Object)array(
                'status' => $status,
                'message' => 'Wrong email/password'
            );
        }else{
            if(auth()->user()['deleted_at'] != null){
                $status = 400;
                $response = (Object)array(
                    'status' => $status,
                    'message' => 'User not found'
                );
            }else{
                $response = (Object)array(
                    "status" => $status,
                    "message" => 'Successfully login',
                    "data" => $this->respondWithToken($token)
                );
            }
        }
        
        return response()->json($response,$status);
    }

    public function profile(Request $request)
    {
        $status = 200;
        $data = (Object)array(
            'user_id' => auth()->user()['user_id'],
            'fullname' => auth()->user()['fullname'],
            'email' => auth()->user()['email']
        );
        $response = (Object)array(
            'status' => 200,
            'message' => 'Successfully get profile',
            'data' => $data
        );
        
        return response()->json($response,$status);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout(true);
        $response = (Object)array(
            'status' => 200,
            'message' => 'Successfully logged out'
        );
        return response()->json($response);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }


    protected function respondWithToken($token)
    {
        return (Object)array(
            'access_token' => $token,
            'expires_in' => auth()->factory()->getTTL() * 60
        );
    }
}
