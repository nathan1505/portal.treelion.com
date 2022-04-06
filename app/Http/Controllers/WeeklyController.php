<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\File;
use Illuminate\Support\Facades\Storage;

function CountMembers($performance_no){

    $memberStr = DB::table('performance_duty')
                        ->where('performance_no', $performance_no)
                        ->value('members');

    $membersId = array();
    $memberStrArray = json_decode($memberStr);

    if (!empty($memberStrArray)){
        foreach ($memberStrArray as $memberIdStr){
            array_push($membersId, (int) $memberIdStr);
        }
    }

    return count($membersId) + 1;

}

class WeeklyController extends Controller
{
    public function GetWeeklyList(Request $request){
        $now = Carbon::now();
        $start = $now->startOfWeek()->format('Y-m-d H:i');
        $end = $now->endOfWeek(Carbon::FRIDAY)->format('Y-m-d H:i');
        
        $startlastweek = $now->startOfWeek()->subDays(7)->format('Y-m-d H:i');
        $endlastweek = $now->endOfWeek(Carbon::FRIDAY)->format('Y-m-d H:i');

        $performance_duty = DB::table('performance_duty')->where('status', '!=', 'end')->get();
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
            
                $flag = false;
                $performance_no = "";
                $node_no = "";
                $performance_no_lastweek = "";
                $node_no_lastweek = "";
                $date = "";
                $date_lastweek = "";
                $weekly_points_lastweek = 0;
                $weekly_points = 0;
                $point_lastweek = 0;
                $leader_points_lastweek = 0;
                $point = 0;
                $leader_points = 0;
            
            foreach($duty as $d){
                
                if($d['node_date'] >= $start && $d['node_date'] <= $end){
                    $flag = true;
                    $point = $p['basic_points']*$d['node_point_percentage']/100*$d['node_completeness']/100*$d['node_completeness_coefficient'];
                    //var_dump($p['basic_points']);
                    $members_no = (float)CountMembers($p['performance_no']);
                    //echo $members_no;
                    $leader_points = bcmul($point, 0.2, 2) + bcdiv( bcmul($point, 0.8, 2) , $members_no, 2);
                    
                    //$point_lastweek = 0;
                    //$leader_points_lastweek = 0;
                    
                    $performance_no = $p['performance_no'];
                    $node_no = $d['node_id'];
                    $date = $d['node_date'];

                    //var_dump($name['role']);
                }
                if($d['node_date'] >= $startlastweek && $d['node_date'] <= $endlastweek){
                    $flag = true;
                    $point_lastweek = $p['basic_points']*$d['node_point_percentage']/100*$d['node_completeness']/100*$d['node_completeness_coefficient'];
                    //var_dump($p['basic_points']);
                    $members_no = (float)CountMembers($p['performance_no']);
                    //echo $members_no;
                    $leader_points_lastweek = bcmul($point_lastweek, 0.2, 2) + bcdiv( bcmul($point_lastweek, 0.8, 2) , $members_no, 2);
                    
                    //$point = 0;
                    //$leader_points = 0;
                    
                    $performance_no_lastweek = $p['performance_no'];
                    $node_no_lastweek = $d['node_id'];
                    $date_lastweek = $d['node_date'];
                }
            }
            if(isset($members_no) && $flag){
                foreach ($name_list as $name){
                    if($name['role'] == "组长" && $members_no == 1){
                        $weekly_points = $point;
                        $weekly_points_lastweek = $point_lastweek;
                    }else if($name['role'] == "组长"){
                        $weekly_points = $leader_points;
                        $weekly_points_lastweek = $leader_points_lastweek;
                    }else if($name['role'] == "组員"){
                        if($members_no > 1){
                            $node_points = (float)bcdiv(($point - $leader_points) , ($members_no - 1), 2);
                            $weekly_points = $node_points;
                            $node_points_weeklast = (float)bcdiv(($point_lastweek - $leader_points_lastweek) , ($members_no - 1), 2);
                            $weekly_points_lastweek = $node_points_weeklast;
                        }
                    }
                    $column = array(
                        "node_point_lastweek" => $weekly_points_lastweek,
                        "node_point" => $weekly_points,
                        "user_id" => $name['user_id'],
                        "performance_no" => $performance_no,
                        "node_no" => $node_no,
                        "date" => $date,
                        "performance_no_lastweek" => $performance_no_lastweek,
                        "node_no_lastweek" => $node_no_lastweek,
                        "date_lastweek" => $date_lastweek,
                        "name" => $name['name'],
                        "role" => $name['role'],
                    );
                    array_push($array, $column);
                }
            }
        }
        $array = collect($array)->sortBy('user_id');
        //var_dump($array);
        return $array;
    }
}