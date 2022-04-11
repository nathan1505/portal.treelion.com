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

class MonthlyController extends Controller
{
    public function GetMonthlyList(Request $request){
        $now = Carbon::now();
        $start = new Carbon('first day of this month');
        $start = $start->startOfDay();
        $end = new Carbon('last day of this month');
        $end = $end->endOfDay();

        $startlastmonth = new Carbon('first day of last month');
        $startlastmonth = $startlastmonth->startOfDay();
        $endlastmonth = new Carbon('last day of last month');
        $endlastmonth = $endlastmonth->endOfDay();
        
        $eachuser = DB::table('users')->where('id', '!=', 1)->get();
        $eachuser = json_decode($eachuser, true);
        
        foreach($eachuser as $user){
            $basic_duty = DB::table('basic_duty')->where('member', $user['id'])->where('status', 'approved')->get();
            $basic_duty = json_decode($basic_duty, true);
            
            $array = array();
            
            $basic_points = 0;
            $basic_no = "";
            foreach($basic_duty as $b){
                $basic_points += $b['total_points'];
                $basic_no = $basic_no.' '.$b['basic_no'].'';
            }
            
            $performance_duty = DB::table('performance_duty')->where('status', '!=', 'end')->get();
            $performance_duty = json_decode($performance_duty, true);
            
            $performance = array();
            
            //dd(json_decode($performance_duty[0]['members']));
            
            foreach($performance_duty as $p){
                $member_list = json_decode($p['members']);
                if($user['id'] == $p['leader'] || $user['id'] == $p['second_leader']){
                    array_push($performance, $p);
                }
                if($member_list){
                    if(in_array($user['id'], $member_list)){
                        array_push($performance, $p);
                    }
                }
            }
            
            $flag = false;
            $performance_no = "";
            $performance_no_lastmonth = "";
            $monthly_points = 0;
            $monthly_points_lastmonth = 0;
            $monthly_points_expected = 0;
            $monthly_points_expected_lastmonth = 0;
            $point = 0;
            $point_lastmonth = 0;
            $point_expected = 0;
            $point_expected_lastmonth = 0;
            $leader_points = 0;
            $leader_points_lastmonth= 0;
            $leader_points_expected = 0;
            $leader_points_expected_lastmonth = 0;

            foreach($performance as $p){
                $duty_node = DB::table('duty_node')->where('duty_performance_no', '=', $p['performance_no'])->get();
                $duty = json_decode($duty_node, true);
                $members_no = (float)CountMembers($p['performance_no']);
                
                if($p['start_date'] >= $start && $p['end_date'] <= $end){
                    $performance_no = $performance_no." ".$p['performance_no']."";
                }
                if($p['node_date'] >= $startlastmonth && $p['node_date'] <= $endlastmonth){
                    $performance_no_lastmonth = $performance_no_lastmonth." ".$p['performance_no']."";
                }
                

                foreach($duty as $d){
                    if($d['node_date'] >= $start && $d['node_date'] <= $end){
                        $member_list = json_decode($p['members']);
                        if($user['id'] == $p['leader']){
                            $point += $p['basic_points']*$d['node_point_percentage']/100*$d['node_completeness']/100*$d['node_completeness_coefficient'];
                            $point_expected += $p['basic_points']*$d['node_point_percentage']/100;
                        }else if($user['id'] == $p['leader']){
                            
                        }else if($member_list){
                            if(in_array($user['id'], $member_list)){
                                
                            }
                        }
                    }
                    if($d['node_date'] >= $endlastmonth && $d['node_date'] <= $endlastmonth){
                        $member_list = json_decode($p['members']);
                        if($user['id'] == $p['leader']){
        
                        }else if($user['id'] == $p['leader']){
                            
                        }else if($member_list){
                            if(in_array($user['id'], $member_list)){
                                
                            }
                        }
                    }
                }
            }
        }
        
        $array = collect($array)->sortBy('user_id');
        //dd($array);
        return $array;
        
        /*
        $performance_duty = DB::table('performance_duty')->where('status', '!=', 'end')->get();
        $performance = json_decode($performance_duty, true);
        
        //$duty_node = DB::table('duty_node')->get();
        //$duty = json_decode($duty_node, true);
        $array = array();
        foreach($performance as $p){
            
            $flag = false;
            $performance_no = "";
            $node_no = "";
            $performance_no_lastweek = "";
            $node_no_lastweek = "";
            $date = "";
            $date_lastweek = "";
            $weekly_points = 0;
            $weekly_points_lastweek = 0;
            $weekly_points_expected = 0;
            $weekly_points_expected_lastweek = 0;
            $point = 0;
            $point_lastweek = 0;
            $point_expected = 0;
            $point_expected_lastweek = 0;
            $leader_points = 0;
            $leader_points_lastweek = 0;
            $leader_points_expected = 0;
            $leader_points_expected_lastweek = 0;
            
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
                    $flag = true;
                    $point = $p['basic_points']*$d['node_point_percentage']/100*$d['node_completeness']/100*$d['node_completeness_coefficient'];
                    $point_expected = $p['basic_points']*$d['node_point_percentage']/100;
                    
                    $members_no = (float)CountMembers($p['performance_no']);
                    $leader_points = bcmul($point, 0.2, 2) + bcdiv( bcmul($point, 0.8, 2) , $members_no, 2);
                    $leader_points_expected = bcmul($point_expected, 0.2, 2) + bcdiv( bcmul($point_expected, 0.8, 2) , $members_no, 2);
                    
                    $performance_no = $p['performance_no'];
                    $node_no = $d['node_id'];
                    $date = $d['node_date'];
                }
                if($d['node_date'] >= $startlastweek && $d['node_date'] <= $endlastweek){
                    $flag = true;
                    $point_lastweek = $p['basic_points']*$d['node_point_percentage']/100*$d['node_completeness']/100*$d['node_completeness_coefficient'];
                    $point_expected_lastweek = $p['basic_points']*$d['node_point_percentage']/100;
                    
                    $members_no = (float)CountMembers($p['performance_no']);
                    $leader_points_lastweek = bcmul($point_lastweek, 0.2, 2) + bcdiv( bcmul($point_lastweek, 0.8, 2) , $members_no, 2);
                    $leader_points_expected_lastweek = bcmul($point_expected_lastweek, 0.2, 2) + bcdiv( bcmul($point_expected_lastweek, 0.8, 2) , $members_no, 2);
                    
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
                        $weekly_points_expected = $point_expected;
                        $weekly_points_expected_lastweek = $point_expected_lastweek;
                    }else if($name['role'] == "组长"){
                        $weekly_points = $leader_points;
                        $weekly_points_lastweek = $leader_points_lastweek;
                        $weekly_points_expected = $leader_points_expected;
                        $weekly_points_expected_lastweek = $leader_points_expected_lastweek;
                    }else if($name['role'] == "组員"){
                        if($members_no > 1){
                            $weekly_points = (float)bcdiv(($point - $leader_points) , ($members_no - 1), 2);
                            $weekly_points_lastweek = (float)bcdiv(($point_lastweek - $leader_points_lastweek) , ($members_no - 1), 2);
                            
                            $weekly_points_expected = (float)bcdiv(($point_expected - $leader_points_expected) , ($members_no - 1), 2);
                            $weekly_points_expected_lastweek = (float)bcdiv(($point_expected_lastweek - $leader_points_expected_lastweek) , ($members_no - 1), 2);
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
                        "node_point_expected" => $weekly_points_expected,
                        "node_point_expected_lastweek" => $weekly_points_expected_lastweek,
                    );
                    array_push($array, $column);
                }
            }
        }
        */

    }
}