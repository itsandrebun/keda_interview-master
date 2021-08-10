<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use DB;

class CustomerController extends Controller
{
    public function get(Request $request){
        try {
            $status = 200;
            $response = (Object)array();

            if(auth()->user()['user_type_id'] == 1){
                $status = 403;
                $response = (Object)array(
                    'status' => $status,
                    'message' => 'You don\'t have any permission to access the data'
                );
            }else{
                $data = User::where('user_type_id',1)->select('user_id','fullname','email',DB::raw('(CASE WHEN deleted_at IS NULL THEN \'active\' ELSE \'deleted\' END) AS status'));
                if($request->input('customer_id') != null){
                    $data->whereIn('user_id',$request->input('customer_id'));
                }
    
                $data = $data->orderBy('status','ASC')->orderBy('fullname','ASC')->get()->toArray();            
        
                $response = (Object)array(
                    'status' => $status,
                    'message' => 'Successfully get data',
                    'data' => (count($data) == 0 ? null : $data)
                );
            }
            return response()->json($response, $status);
        } catch (Exception $e) {
            $response = array(
                'status' => 500,
                'message' => 'Internal Server Error'
            );
            return response()->json($response, 500);
        }
        
    }

    public function delete(Request $request){
        $delete = User::where('user_type_id',1)->whereIn('user_id',$request->input('customer_id'))->whereNull('deleted_at')->update([
            'deleted_at' => date('Y-m-d H:i:s')
        ]);

        $response = array();
        $status = 200;
        if($delete){
            $response = array('status' => $status, 'message' => 'Successfully delete customer');
        }else{
            $status = 400;
            $response = array('status' => $status, 'message' => 'Data not found');
        }
        return response()->json($response, $status);
    }
}
