<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\File;
use Illuminate\Support\Facades\Storage;

class WeeklyController extends Controller
{
    public function GetWeeklyList(Request $request){
        $now = Carbon::now();
        $start = $now->startOfWeek()->format('Y-m-d H:i');
        $end = $now->endOfWeek()->format('Y-m-d H:i');

        $performance_duty = DB::table('performance_duty')->get();
        $performance = json_decode($performance_duty, true);
        
        //$duty_node = DB::table('duty_node')->get();
        //$duty = json_decode($duty_node, true);
        $array = array();
        foreach($performance as $p){
            
            $duty_node = DB::table('duty_node')->where('duty_performance_no', '=', $p['performance_no'])->get();
            $duty = json_decode($duty_node, true);
            
            
            $name_list = array();
            $leader = DB::table('users')->where('id', '=', $p['leader'])->get('name');
            $leader_name = json_decode($leader, true);
            $name = array(
                    "user_id" => $p['leader'],
                    "name" => $leader_name[0]['name'],
                    "role" => "组长",
                );
            array_push($name_list, $name);
            

            if($p['second_leader']){
                $leader2 = DB::table('users')->where('id', '=', $p['second_leader'])->get('name');
                $leader2_name = json_decode($leader2, true);
                $name = array(
                        "user_id" => $p['second_leader'],
                        "name" => $leader2_name[0]['name'],
                        "role" => "组长",
                    );
                array_push($name_list, $name);
            }
            
            if($p['members']){
                foreach (json_decode($p['members']) as $m){
                    $member = DB::table('users')->where('id', '=', (int)$m)->get('name');
                    $member_name = json_decode($member, true);
                    $name = array(
                        "user_id" => (int)$m,
                        "name" => $member_name[0]['name'],
                        "role" => "组員",
                    );
                    array_push($name_list, $name);
                }
            }
            
            foreach($duty as $d){
                if($d['node_date'] >= $start && $d['node_date'] <= $end){
                    $node_point = $p['basic_points']*$d['node_point_percentage']/100*$d['node_point_percentage']/100*$d['node_completeness_coefficient'];
                    foreach ($name_list as $name){
                        $column = array(
                            "node_point" => $node_point,
                            "user_id" => $name['user_id'],
                            "performance_no" => $p['performance_no'],
                            "node_no" => $d['node_id'],
                            "name" => $name['name'],
                            "role" => $name['role'],
                        );
                        array_push($array, $column);
                    }
                }
            }
        }
        $array = collect($array)->sortBy('user_id');
        //var_dump($array);
        return $array;
    }
}