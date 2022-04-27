<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\File;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    public function ShowAttendance(Request $request){
        $yearmonth = $request->yearmonth;
        
        //var_dump($yearmonth);
        
        $attendance = DB::table('attendance')->where('month',$yearmonth)->get();
        $attendance = json_decode(json_encode($attendance), true);
        //dd($attendance);
        
        return $attendance;
    }
    
    //insert attendance point for every employee
    public function UpdateAttendance(Request $request){
        $postContent = $request->all();
        
        $arrays = array_filter($postContent, function($key) {
            return strpos($key, 'attendance-') === 0;
        }, ARRAY_FILTER_USE_KEY);
        
        //var_dump($arrays);
        
        //add the data into the "attendance" table
        foreach ($arrays as $id => $value){
            $user_id = substr(strrchr($id, '-'), 1);
            //var_dump($user_id);
            $data = DB::table('attendance')->where('month',$postContent['yearmonth'])->where('user_id',$user_id)->get();
            if($data->isempty()){
                DB::table('attendance')->insert([
                    'user_id' => $user_id,
                    'points' => $value,
                    'month' => $postContent['yearmonth'],
                ]);
            }else{
                DB::table('attendance')->where('month',$postContent['yearmonth'])
                ->where('user_id',$user_id)
                ->update([
                    'points' => $value,
                ]);
            }
            //var_dump($key);
            //var_dump($value);
        }

        return redirect('/attendance/detail');
    }
}