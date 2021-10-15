<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

function ToNumString(string $str){
    $arrayToString = explode(' ',$str);
    $array = array_slice($arrayToString, 1, sizeof($arrayToString)-2, false);
    foreach ($array as $key => $value){
        $value = preg_replace('/[^0-9]/', '', $value);
        $array[$key] = (int)$value;
    }
    return $array;
}

//To generate the performance_no of the job
function GenerateDutyNum(){
    $current_duties = DB::table('performance_duty')->orderBy('create_timestamp','desc')->get();
    if (isset((json_decode(json_encode($current_duties), true))[0])){
        $last_duty_no = (json_decode(json_encode($current_duties), true))[0]['performance_no'];
        $last_duty_Month = substr($last_duty_no, 1, 2);
        $last_duty_no = (int)substr($last_duty_no, 3);
        if ($last_duty_Month == date('m')){
            $last_duty_no = sprintf("%03d", $last_duty_no + 1);
        }else{
            $last_duty_no = "001";
        }
        return "D".date('m').$last_duty_no;
    }else{
        return "D".date('m')."001";
    }
}

/**
 * Return the number of all members in a performance duty, including leader and members (n+1)
 * @param String Performance Duty No
 * @return Integer total members of a performance duty (including leader)
 */
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

/**
 * Return the string of all names of a duty's member, split by comma
 * @param String members from database
 * @return String members name string, split by comma
 */
function parseMembers($memberStr){
    
    $userArray = DB::table('users')
                    ->get();
    $userArray = json_decode($userArray, true);
    $returnStr = '';

    $membersId = array();
    $memberStrArray = json_decode($memberStr);

    foreach ($memberStrArray as $memberIdStr){
        array_push($membersId, (int) $memberIdStr);
    }

    foreach ($membersId as $memberId){
        foreach ($userArray as $user){
            if($user['id'] == $memberId){
                $returnStr = $returnStr.', '.$user['name'];
                break;
            }
        }
    }

    $returnStr = substr($returnStr, 2);

    return $returnStr;
}

/**
 * Check if a user is of duty's member
 * @param Integer user_id
 * @param String json of duty members
 * @return Boolean True if a user is member
 */
function IsDutyMember($userId, $memberStr){

    $membersId = array();
    $memberArray = json_decode($memberStr, true);

    if (!empty($memberArray)){
        foreach ($memberArray as $memberIdStr){
            array_push($membersId, (int) $memberIdStr);
        }
    }

    foreach ($membersId as $memberId){
        if ($memberId == $userId){
            return true;
        }
    }

    return false;
}


/**
 * @description Calculate the basic points based on coefficients
 * @param Integer Basic points
 * @param String type of duty
 * @param String difficulty of duty
 */
function CalculateBasicPoints ($bp, $type, $difficulty){

    $typeCoefficient = 1.0;
    $difficultyCoefficient = 1.0;

    switch ($type){
        case '一类积分':
            $typeCoefficient = 1.8;
            break;
        case '二类积分':
            $typeCoefficient = 1.4;
            break;
        case '三类积分':
            $typeCoefficient = 1;
            break;
        case '四类积分':
            $typeCoefficient = 0.8;
            break;
    }

    switch ($difficulty){
        case 'difficult':
            $difficultyCoefficient = 1.5;
            break;
        case 'easy':
            $difficultyCoefficient = 0.8;
            break;
        default:
            $difficultyCoefficient = 1;
    }

    return $bp*$typeCoefficient*$difficultyCoefficient;
}

/**
 * @description update the gained points of duty
 * @param String Performance Duty No
 */
function UpdateGainedPoints($dutyNo){
    $basicPoints = DB::table('performance_duty')
                        ->where('performance_no', $dutyNo)
                        ->value('basic_points');

    $response = DB::table('duty_node')
                    ->where('duty_performance_no', $dutyNo)
                    ->orderBy('node_id')
                    ->get();
    $array = json_decode(json_encode($response), true);

    $coefficient = 0.0;
    
    foreach($array as $node){
        $coefficient += ((float)$node['node_point_percentage'])/100 * $node['node_completeness_coefficient'];
    }
    
    return $basicPoints*$coefficient;
}

class PerformancesController extends Controller
{
        //Create a new performance duty, together with its nodes
        public function PostDuty(Request $request){
            $postContent = $request->all();
            if (isset($postContent['members'])){
                $leader = json_encode($postContent['members']);
            } else {
                $leader = "";
            }
    
            $basic_points = CalculateBasicPoints(10.0, $postContent['type'], $postContent['difficulty']);
    
            $performance_no = GenerateDutyNum();
            DB::table('basic_duty')->insert([
                'performance_content' => $postContent["content"],
                'performance_no' => $performance_no,
                'type' => $postContent['type'],
                'property' => $postContent['property'],
                'difficulty' => $postContent['difficulty'],
                'leader' => (int)$postContent['leader'],
                'members' => $leader,
                'start_date' => $postContent['start-date'],
                'end_date' => $postContent['end-date'],
                'node_no' => (int)$postContent['node-no'],
                'total_points' => $basic_points,
                'latest_progress' => "",
                'declarant_id' => (int)$postContent['declarant-id'],
            ]);

    
            //Create Announcement for the duty
            $announcementContent = '【'.Auth::user()->name.'】 创建了基础项目 【'.$performance_no.'】，请主管领导尽快审批';
            DB::table('announcements')->insert([
                'name' => Auth::user()->name,
                'content' =>  $announcementContent,
                'is_important' => 1,
            ]);
    
            return redirect('/')
            ->with('status', "您已成功提交业绩事项 ".$performance_no."！");
        }
}