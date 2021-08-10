<?php

namespace Tests\Unit;

use Tests\TestCase;
use JWTAuth;
use App\Models\User;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class UserTest extends TestCase
{
    // use RefreshDatabase;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    protected $right_email = "customer3@gmail.com";
    protected $right_password = "dummydummy";
    protected $wrong_email = "customerr1@gmail.com";
    protected $wrong_password = "dumydummy";
    protected $user_id = 5;
    protected $wrong_user_id = 3;

    public function test_right_user_login()
    {
        $response = $this->post('/api/auth/login', ['email' => $this->right_email, 'password' => $this->right_password]);

        $response->assertSuccessful();
    }

    public function test_wrong_email_login()
    {
        $response = $this->post('/api/auth/login', ['email' => $this->wrong_email, 'password' => $this->right_password]);

        $response->assertStatus(401);
    }

    public function test_wrong_password_login()
    {
        $response = $this->post('/api/auth/login', ['email' => $this->right_email, 'password' => $this->wrong_password]);

        $response->assertStatus(401);
    }

    public function test_profile()
    {
        $user = User::where('user_id',$this->user_id)->first();
        $token = JWTAuth::fromUser($user);
        JWTAuth::setToken($token);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->json('POST', '/api/auth/profile', ['user_id' => $this->user_id]);

        $response->assertSuccessful();
    }

    public function test_logout()
    {
        $user = User::where('user_id',$this->user_id)->first();
        $token = JWTAuth::fromUser($user);
        JWTAuth::setToken($token);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->json('POST', '/api/auth/profile', ['user_id' => $this->user_id]);

        $response->assertSuccessful();
    }
}
