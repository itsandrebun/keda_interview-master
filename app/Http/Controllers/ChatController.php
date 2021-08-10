<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Chat;
use App\Models\ChatPerUser;
use DB;

class ChatController extends Controller
{
    public function history(Request $request){
        $chat_history_data = array();
        $chat_history = array();

        if($request->input('to') == null){
            $chat_history = ChatPerUser::select('last_chat_id','description','first_person_id',DB::raw('(SELECT fullname FROM users WHERE users.user_id = first_person_id) as first_person_name'),'receiver_id',DB::raw('(SELECT fullname FROM users WHERE users.user_id = second_person_id) as second_person_name'),'is_read','read_time',DB::raw('chat_per_user.created_at'),DB::raw('(SELECT COUNT(ch.id) FROM chats AS ch WHERE ch.is_read = 0 AND ch.receiver_id = '.$request->input('user_id').' AND ch.sender_id <> '.$request->input('user_id').' AND (ch.sender_id = first_person_id OR ch.sender_id = second_person_id)) as total_unread_messages'))->join('chats','chats.id','=','chat_per_user.last_chat_id')->orderBy('last_chat_id','DESC')->where(function($query) use($request){
                $query->where('first_person_id', $request->input('user_id'))->orWhere('second_person_id', $request->input('user_id'));
            })->get()->toArray();
        }else{
            $read_chat = Chat::where(['sender_id' => $request->input('to'), 'receiver_id' => $request->input('user_id')])->update(['is_read' => 1,'read_time' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')]);
            $chat_history = Chat::select('id','description','sender_id',DB::raw('(SELECT fullname FROM users WHERE users.user_id = sender_id) as sender_name'),'receiver_id',DB::raw('(SELECT fullname FROM users WHERE users.user_id = receiver_id) as receiver_name'),'is_read','read_time','created_at')->orderBy('id','ASC')->where(function($query) use($request){
                $query->where('sender_id', $request->input('user_id'))->where('receiver_id', $request->input('to'));
            })->orWhere(function($query) use($request){
                $query->where('sender_id', $request->input('to'))->where('receiver_id', $request->input('user_id'));
            })->get()->toArray();
        }

        for ($b=0; $b < count($chat_history); $b++) { 
            $obj = (Object)array(
                "chat_id" => isset($chat_history[$b]['id']) ? $chat_history[$b]['id'] : $chat_history[$b]['last_chat_id'],
                "description" => $chat_history[$b]['description'],
                "sender" => (Object)array(
                    "id" => isset($chat_history[$b]['sender_id']) ? $chat_history[$b]['sender_id'] : $chat_history[$b]['first_person_id'],
                    "name" => isset($chat_history[$b]['sender_name']) ? $chat_history[$b]['sender_name'] : $chat_history[$b]['first_person_name'],
                ),
                "receiver" => (Object)array(
                    "id" => isset($chat_history[$b]['receiver_id']) ? $chat_history[$b]['receiver_id'] : $chat_history[$b]['second_person_id'],
                    "name" => isset($chat_history[$b]['receiver_name']) ? $chat_history[$b]['receiver_name'] : $chat_history[$b]['second_person_name'],
                ),
                "is_read" => $chat_history[$b]['is_read'],
                'read_time' => $chat_history[$b]['read_time'] == null ? null : date('Y-m-d H:i:s', strtotime($chat_history[$b]['read_time'])),
                "created_at" => date("Y-m-d H:i:s",strtotime($chat_history[$b]['created_at'])),
            );

            if(isset($chat_history[$b]['total_unread_messages'])){
                $obj->total_unread_messages = $chat_history[$b]['total_unread_messages'];
            }

            array_push($chat_history_data, $obj);
        }
        
        $status = 200;
        $response = (Object)array(
            "status" => $status,
            "message" => "Successfully get chat history",
            "data" => count($chat_history_data) == 0 ? null : $chat_history_data
        );
        return response()->json($response, $status);
    }

    public function send(Request $request){
        $status = 200;
        $response = (Object)array();

        $receiver_user_type = User::where('user_id',$request->input('to'))->first();

        if($receiver_user_type == null){
            $status = 400;
            $response = (Object)array(
                "status" => $status,
                "message" => "User not found"
            );
        }else{
            if($request->input('to') == $request->input('user_id')){
                $status = 403;
                $response = (Object)array(
                    "status" => $status,
                    "message" => "You are only able to chat with other person, not yourself."
                );
            }else if(auth()->user()['user_type_id'] == 1 && $receiver_user_type['user_type_id'] == 2){
                $status = 403;
                $response = (Object)array(
                    "status" => $status,
                    "message" => "You don't have any permission to chat with this person."
                );
            }else if(strlen($request->input('message')) <= 0 || strlen($request->input('message')) > 200){
                $status = 400;
                $response = (Object)array(
                    "status" => $status,
                    "message" => "The message length must be between 1 - 200 characters"
                );
            }else{
                $send_message = Chat::insertGetId([
                    "description" => $request->input('message'),
                    "sender_id" => $request->input('user_id'),
                    "receiver_id" => $request->input('to'),
                    "created_at" => date('Y-m-d H:i:s')
                ]);

                if($send_message == 0){
                    $status = 500;
                    $response = (Object)array(
                        "status" => $status,
                        "message" => "Error when sending the chat"
                    );
                }else{
                    $response = (Object)array(
                        "status" => $status,
                        "message" => "Successfully send chat"
                    );

                    $check_chat = ChatPerUser::where(function($query) use ($request){
                        $query->where('first_person_id', $request->input('user_id'))->where('second_person_id', $request->input('to'));
                    })->orWhere(function($query) use ($request){
                        $query->where('first_person_id', $request->input('to'))->where('second_person_id', $request->input('user_id'));
                    })->get();

                    if(count($check_chat) == 0){
                        $grouping_chat = ChatPerUser::insert(
                            [
                                'last_chat_id' => $send_message,
                                'first_person_id' => $request->input('user_id'),
                                'second_person_id' => $request->input('to'),
                                "created_at" => date('Y-m-d H:i:s')
                            ]
                        );
                    }else{
                        $update = ChatPerUser::where(function($query) use ($request){
                            $query->where('first_person_id', $request->input('user_id'))->where('second_person_id', $request->input('to'));
                        })->orWhere(function($query) use ($request){
                            $query->where('first_person_id', $request->input('to'))->where('second_person_id', $request->input('user_id'));
                        })->update([
                            'last_chat_id' => $send_message,
                            'first_person_id' => $request->input('user_id'),
                            'second_person_id' => $request->input('to'),
                            "updated_at" => date('Y-m-d H:i:s')
                        ]);
                    }
                }
            }
            
        }
        
        return response()->json($response, $status);
    }
}
