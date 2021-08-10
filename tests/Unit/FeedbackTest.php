<?php

namespace Tests\Unit;

use Tests\TestCase;
use JWTAuth;
use App\Models\User;
use Exception;
// use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Str;

class FeedbackTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    protected $user_id = 5;
    protected $message = "Hi, you should send this message soon!";

    public function test_send_feedback()
    {
        $user = User::where('user_id',$this->user_id)->first();
        $token = JWTAuth::fromUser($user);
        JWTAuth::setToken($token);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->post('/api/feedback/send', ['user_id' => $this->user_id, 'description' => $this->message]);

        $response->assertSuccessful();
    }

    public function test_send_empty_msg_value()
    {
        $user = User::where('user_id',$this->user_id)->first();
        $token = JWTAuth::fromUser($user);
        JWTAuth::setToken($token);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->post('/api/feedback/send', ['user_id' => $this->user_id, 'description' => '']);

        $response->assertStatus(400);
    }

    public function test_send_msg_value_greater_than_200_characters()
    {
        $user = User::where('user_id',$this->user_id)->first();
        $token = JWTAuth::fromUser($user);
        JWTAuth::setToken($token);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->post('/api/feedback/send', ['user_id' => $this->user_id, 'description' => Str::random(500)]);

        $response->assertStatus(400);
    }
}
