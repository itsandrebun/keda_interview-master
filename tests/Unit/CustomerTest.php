<?php

namespace Tests\Unit;

use Tests\TestCase;
use JWTAuth;
use App\Models\User;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class CustomerTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */

    protected $user_id = 3;
    protected $multiple_customer_ids = [4,1];
    
    public function test_get_customer()
    {
        $user = User::where('user_id',$this->user_id)->first();
        $token = JWTAuth::fromUser($user);
        JWTAuth::setToken($token);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->post('/api/customer/get', ['user_id' => $this->user_id]);

        $response->assertSuccessful();
    }

    public function test_get_customer_by_id()
    {
        $user = User::where('user_id',$this->user_id)->first();
        $token = JWTAuth::fromUser($user);
        JWTAuth::setToken($token);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->post('/api/customer/get', ['user_id' => $this->user_id, 'customer_id' => $this->multiple_customer_ids]);

        $response->assertSuccessful();
    }

    public function test_delete_customer(){
        $user = User::where('user_id',$this->user_id)->first();
        $token = JWTAuth::fromUser($user);
        JWTAuth::setToken($token);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->post('/api/customer/delete', ['user_id' => $this->user_id, 'customer_id' => $this->multiple_customer_ids]);

        $response->assertSuccessful();
    }
}
