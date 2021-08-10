<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\User;
use App\Models\Feedback;

class ReportController extends Controller
{
    public function send(Request $request){
        $status = 200;
        $response = (Object)array(
            "status" => $status,
            "message" => "Successfully report other customer"
        );
        $check_user_status = User::where('user_id', $request->input('customer_id'))->first();
        if($check_user_status == null){
            $status = 400;
            $response = (Object)array(
                "status" => $status,
                "message" => "User not found"
            );
        }else{
            if($request->input('customer_id') == $request->input('user_id')){
                $status = 403;
                $response = (Object)array(
                    "status" => $status,
                    "message" => "You are only able to chat with other person, not yourself."
                );
            }else if(auth()->user()['user_type_id'] == 1 && $check_user_status['user_type_id'] == 2){
                $status = 403;
                $response = (Object)array(
                    "status" => $status,
                    "message" => "You don't have any permission to report this person."
                );
            }else if(strlen($request->input('description')) <= 0 || strlen($request->input('description')) > 200){
                $status = 400;
                $response = (Object)array(
                    "status" => $status,
                    "message" => "The message length must be between 1 - 200 characters"
                );
            }else{
                $report = Report::insertGetId([
                    'description' => $request->input('description'),
                    'reported_user_id' => $request->input('customer_id'),
                    'reported_by' => $request->input('user_id'),
                    'created_at' => date('Y-m-d H:i:s')
                ]);
        
                if($report == 0){
                    $status = 500;
                    $response = (Object)array(
                        "status" => $status,
                        "message" => "Error when reporting other customer"
                    );
                }
            }
        }
        
        return response()->json($response, $status);
    }

    public function feedback(Request $request){
        $status = 200;
        $response = (Object)array(
            "status" => $status,
            "message" => "Successfully send feedback to staff"
        );

        if(strlen($request->input('description')) <= 0 || strlen($request->input('description')) > 200){
            $status = 400;
            $response = (Object)array(
                "status" => $status,
                "message" => "The message length must be between 1 - 200 characters"
            );
        }else{
            $feedback = Feedback::insertGetId([
                'description' => $request->input('description'),
                'made_by' => $request->input('user_id'),
                'created_at' => date('Y-m-d H:i:s')
            ]);
    
            if($feedback == 0){
                $status = 500;
                $response = (Object)array(
                    "status" => $status,
                    "message" => "Error when giving the feedback"
                );
            }
        }
        
        return response()->json($response, $status);
    }
}
