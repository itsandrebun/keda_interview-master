<?php

namespace Tests\Unit;

use Tests\TestCase;
use JWTAuth;
use App\Models\User;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Str;

class ChatTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */

    protected $user_id = 5;
    protected $receiver = 1;
    protected $another_receiver = 2;
    protected $message = "Hi, you should send this message soon!";

    public function test_send_chat()
    {
        $user = User::where('user_id',$this->user_id)->first();
        $token = JWTAuth::fromUser($user);
        JWTAuth::setToken($token);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->post('/api/chat/send', ['user_id' => $this->user_id, 'to' => $this->receiver, 'message' => $this->message]);

        $response->assertSuccessful();
    }

    public function test_send_chat_from_customer_to_staff()
    {
        $user = User::where('user_id',$this->user_id)->first();
        $token = JWTAuth::fromUser($user);
        JWTAuth::setToken($token);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->post('/api/chat/send', ['user_id' => $this->user_id, 'to' => $this->another_receiver, 'message' => $this->message]);

        $response->assertStatus(403);
    }

    public function test_send_empty_message()
    {
        $user = User::where('user_id',$this->user_id)->first();
        $token = JWTAuth::fromUser($user);
        JWTAuth::setToken($token);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->post('/api/chat/send', ['user_id' => $this->user_id, 'to' => $this->receiver, 'message' => '']);

        $response->assertStatus(400);
    }

    public function test_send_message_greater_than_200_characters()
    {
        $user = User::where('user_id',$this->user_id)->first();
        $token = JWTAuth::fromUser($user);
        JWTAuth::setToken($token);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->post('/api/chat/send', ['user_id' => $this->user_id, 'to' => $this->receiver, 'message' => Str::random(500)]);

        $response->assertStatus(400);
    }
}
