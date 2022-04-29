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
    
    //Get the monthly point of every members
    public function GetMonthlyList(Request $request){
        
        $yearmonth = $request->yearmonth;

        $start = Carbon::parse($yearmonth)->startOfMonth()->format('Y-m-d');
        $end = Carbon::parse($yearmonth)->endofMonth()->format('Y-m-d');

        //$now = Carbon::now();
        /*
        $start = new Carbon('first day of this month');
        $start = $start->startOfDay()->format('Y-m-d');
        $end = new Carbon('last day of this month');
        $end = $end->endOfDay()->format('Y-m-d');
        */
        
        /*
        $startlastmonth = new Carbon('first day of last month');
        $startlastmonth = $startlastmonth->startOfDay()->format('Y-m-d');
        $endlastmonth = new Carbon('last day of last month');
        $endlastmonth = $endlastmonth->endOfDay()->format('Y-m-d');
        */
        
        $eachuser = DB::table('users')->where('id', '!=', 1)->get();
        $eachuser = json_decode($eachuser, true);
        
        $array = array();
        
        foreach($eachuser as $user){
            $basic_duty = DB::table('basic_duty')->where('member', $user['id'])->where('status', 'approved')->get();
            $basic_duty = json_decode($basic_duty, true);
            
            
            $basic_points = 0;
            $basic_no = "";

            foreach($basic_duty as $b){
                $basic_points += $b['total_points'];
                $basic_no = $basic_no.' '.$b['basic_no'].'';
            }
            
            $performance_duty = DB::table('performance_duty')->where('status', '!=', 'end')->get();
            $performance_duty = json_decode($performance_duty, true);
            
            $performance = array();
            
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

            $performance_no = "";
            $performance_no_lastmonth = "";
            $point = 0;
            $point_lastmonth = 0;
            $point_expected = 0;
            $point_expected_lastmonth = 0;

            foreach($performance as $p){
                $duty_node = DB::table('duty_node')->where('duty_performance_no', '=', $p['performance_no'])->get();
                $duty = json_decode($duty_node, true);
                $members_no = (float)CountMembers($p['performance_no']);
                
                /*
                if($p['start_date'] <= $end && $p['end_date'] >= $start){
                    $performance_no = $performance_no." ".$p['performance_no']."";
                }
                if($p['start_date'] <= $endlastmonth && $p['end_date'] >= $startlastmonth){
                    $performance_no_lastmonth = $performance_no_lastmonth." ".$p['performance_no']."";
                }
                */
                

                foreach($duty as $d){
                    $base_point = $p['basic_points']*$d['node_point_percentage']/100*$d['node_completeness']/100*$d['node_completeness_coefficient'];
                    $base_point_expected = $p['basic_points']*$d['node_point_percentage']/100;
                    $leader_points = bcmul($base_point, 0.2, 2) + bcdiv( bcmul($base_point, 0.8, 2) , $members_no, 2);
                    $leader_points_expected = bcmul($base_point_expected, 0.2, 2) + bcdiv( bcmul($base_point_expected, 0.8, 2) , $members_no, 2);
                    $member_list = json_decode($p['members']);

                    if($d['node_date'] >= $start && $d['node_date'] <= $end){
                        if($user['id'] == $p['leader'] && $members_no == 1){
                            $point += $base_point;
                            $point_expected += $base_point_expected;
                        }else if($user['id'] == $p['leader'] || $user['id'] == $p['second_leader']){
                            $point += $leader_points;
                            $point_expected += $leader_points_expected;
                        }else if($member_list){
                            if(in_array($user['id'], $member_list)){
                                $point += (float)bcdiv(($base_point - $leader_points) , ($members_no - 1), 2);
                                $point_expected += (float)bcdiv(($base_point_expected - $leader_points_expected) , ($members_no - 1), 2);
                            }
                        }

                        
                        $performance_no = $performance_no." ".$p['performance_no']."";
                        $performance_no = implode(' ', array_unique(explode(' ', $performance_no)));
                    }
                    /*
                    if($d['node_date'] >= $startlastmonth && $d['node_date'] <= $endlastmonth){
                        if($user['id'] == $p['leader']){
                            $point_lastmonth += $base_point;
                            $point_expected_lastmonth += $base_point_expected;
                        }else if($user['id'] == $p['leader']){
                            $point_lastmonth += $leader_points;
                            $point_expected_lastmonth += $leader_points_expected;
                        }else if($member_list){
                            if(in_array($user['id'], $member_list)){
                                $point_lastmonth += (float)bcdiv(($base_point - $leader_points) , ($members_no - 1), 2);
                                $point_expected_lastmonth += (float)bcdiv(($base_point_expected - $leader_points_expected) , ($members_no - 1), 2);
                            }
                        }
                        
                        $performance_no_lastmonth = $performance_no_lastmonth." ".$p['performance_no']."";
                        $performance_no_lastmonth = implode(' ', array_unique(explode(' ', $performance_no_lastmonth)));
                    }
                    */
                }
            }
            
            $attendance = DB::table('attendance')->where('month', $yearmonth)->where('user_id', $user['id'])->get();
            
            if($attendance->isempty()){
                $attendance_point = 0;
            }else{
                $attendance = json_decode(json_encode($attendance), true);
                $attendance_point = $attendance[0]['points'];
            }
            
            $point = round($point, 0);
            $basic_points_actual = 0;
            $basic_points_distribute = 0;
            $total = $basic_points+$point;
            
            if($user['pointtype'] == "support"){
                $basic_points_actual = 100;
                $total = $basic_points_actual+$point;
                $basic_points = $basic_points_actual;
            }else if($user['pointtype'] == "regular2"){
                $basic_points = 40;
                $basic_points_actual = 40;

            }else if($user['pointtype'] == "technical" || $basic_points < 40){
                $basic_points_actual = $basic_points;
            }else if($basic_points >= 40){
                $basic_points_actual = 40;
                $basic_points_distribute = $basic_points - 40;
                $total = $basic_points_actual+$point;
            }
            
            if($user['pointtype'] == "regular" && ($basic_points_actual+$point) < 100 && ($basic_points+$point) >= 100){
                $total = 100;
            }else if($user['pointtype'] == "regular"){
                $basic_points_distribute = 0;
            }
            
            $total += $attendance_point;

            $column = array(
                "user_id" => $user['id'],
                "name" => $user['name'],
                "basic_points" => $basic_points,
                "basic_points_actual" => $basic_points_actual,
                "basic_points_distribute" => $basic_points_distribute,
                "basic_no" => $basic_no,
                "performance_no" => $performance_no,
                //"performance_no_lastmonth" => $performance_no_lastmonth,
                "point" => $point,
                "point_expected" => $point_expected,
                //"point_lastmonth" => $point_lastmonth,
                //"point_expected_lastmonth" => $point_expected_lastmonth,
                "total_expected" => $basic_points+$point_expected,
                "total" => $total,
                "attendance" => $attendance_point,
                
            );
            //var_dump($column);
            array_push($array, $column);
        }
        
        $array = collect($array)->sortBy('user_id');
        return $array;

    }
}