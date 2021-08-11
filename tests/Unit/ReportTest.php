<?php

namespace Tests\Unit;

use Tests\TestCase;
use JWTAuth;
use App\Models\User;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Str;

class ReportTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    protected $user_id = 5;
    protected $staff_id = 2;
    protected $receiver = 1;
    protected $message = "Hi, you should send this message soon!";

    public function test_report()
    {
        $user = User::where('user_id',$this->user_id)->first();
        $token = JWTAuth::fromUser($user);
        JWTAuth::setToken($token);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->post('/api/report/send', ['user_id' => $this->user_id, 'customer_id' => $this->receiver, 'description' => $this->message]);

        $response->assertSuccessful();
    }

    public function test_report_staff()
    {
        $user = User::where('user_id',$this->user_id)->first();
        $token = JWTAuth::fromUser($user);
        JWTAuth::setToken($token);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->post('/api/report/send', ['user_id' => $this->user_id, 'customer_id' => $this->staff_id, 'description' => $this->message]);

        $response->assertStatus(403);
    }

    public function test_send_empty_msg_value()
    {
        $user = User::where('user_id',$this->user_id)->first();
        $token = JWTAuth::fromUser($user);
        JWTAuth::setToken($token);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->post('/api/report/send', ['user_id' => $this->user_id, 'customer_id' => $this->receiver, 'description' => '']);

        $response->assertStatus(400);
    }

    public function test_send_msg_value_greater_than_200_characters()
    {
        $user = User::where('user_id',$this->user_id)->first();
        $token = JWTAuth::fromUser($user);
        JWTAuth::setToken($token);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->post('/api/report/send', ['user_id' => $this->user_id, 'customer_id' => $this->receiver, 'description' => Str::random(500)]);

        $response->assertStatus(400);
    }
}
