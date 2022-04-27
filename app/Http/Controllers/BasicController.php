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
    $current_duties = DB::table('basic_duty')->orderBy('create_timestamp','desc')->get();
    if (isset((json_decode(json_encode($current_duties), true))[0])){
        $last_duty_no = (json_decode(json_encode($current_duties), true))[0]['basic_no'];
        $last_duty_Month = substr($last_duty_no, 1, 2);
        $last_duty_no = (int)substr($last_duty_no, 3);
        if ($last_duty_Month == date('m')){
            $last_duty_no = sprintf("%03d", $last_duty_no + 1);
        }else{
            $last_duty_no = "001";
        }
        return "G".date('m').$last_duty_no;
    }else{
        return "G".date('m')."001";
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
        case '五类积分（管理类）':
            $typeCoefficient = 1.2;
            break;
        case '六类积分（日常类）':
            $typeCoefficient = 1.0;
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

class BasicController extends Controller
{
    //Hide basic duty
    public function HideBasicDuty(Request $request, $arg){
        $dutyId = (int)$arg;
        DB::table('basic_duty')
                ->where('id', $dutyId)
                ->update([
                    'status' => "delete",
                ]);
                
        return redirect('/daily/{$dutyId}')
        ->with('status', "您已成功删除基础项目！");
    }
    
    //Edit basic duty
    public function EditBasicDuty(Request $request){
        $total_points = CalculateBasicPoints(10.0, $request["type"], $request["difficulty"]);
        $basic_no = $request["basic_no"];
        
        DB::table('basic_duty')
                ->where('id', $request["duty_id"])
                ->update([
                    'basic_content' => $request["basic-content"],
                    'type' => $request["type"],
                    'difficulty' => $request["difficulty"],
                    'status' => $request["status"],
                    'total_points' => $total_points,
                ]);
        //testing output
        //return $request->input();
        
        //Create Announcement for the duty
        $announcementContent = '【'.Auth::user()->name.'】 更改了基础项目 【'.$basic_no.'】，请主管领导尽快审批';
        DB::table('announcements')->insert([
            'name' => Auth::user()->name,
            'content' =>  $announcementContent,
            'is_important' => 1,
        ]);
        
        $duty_id = (int)$request["duty_id"];
        return redirect('/basic/edit/'.$duty_id)
        ->with('status', "您已成功更改基础项目 ".$basic_no."！");
    }
    
    //Show the edit page of basic duty by its ID
    public function ShowBasicDuty(Request $request, $arg){
        $dutyId = (int)$arg;
        $array = DB::table('basic_duty')->where('id',$dutyId)->get();
        $data = json_decode(json_encode($array), true);
        $declarent = DB::table('users')->where('id',$data[0]['member'])->get();
        $name = json_decode(json_encode($declarent), true);
        //print_r($data);
        //print_r($name);
        //return $array;
        return view('basic.edit', ['data' => $data, 'name' => $name]);
    }

    //Get the info of some duty by its ID
    public function SeeBasicDuty(Request $request, $arg){
        //Get id of performance duty as $dutyId
        $dutyId = (int)$arg;
        $response = DB::table('basic_duty')->where('id',$dutyId)->get();
        $array = json_decode(json_encode($response), true);
        return $array;
    }
    
    //Get all basic duty
    public function GetAllBasic(Request $request){
        $basicDuties = DB::table('basic_duty')->orderBy('timestamp','desc')->get();
        return $basicDuties;
    }

    //Get all approved basic duty
    public function GetAllApprovedBasic(Request $request){
        $basicDuties = DB::table('basic_duty')->where('member', Auth::user()->id)
        ->where('status', "approved")
        ->orderBy('timestamp','desc')->get();
        return $basicDuties;
    }

    //Create a new basic duty, together with its nodes
    public function PostDuty(Request $request){
        $postContent = $request->all();

        $total_points = CalculateBasicPoints(10.0, $postContent['type'], $postContent['difficulty']);

        //$basic_no = GenerateDutyNum();
        DB::table('basic_duty')->insert([
            'basic_content' => $postContent["basic-content"],
            'basic_no' => $postContent["basic-no"],
            'type' => $postContent['type'],
            'difficulty' => $postContent['difficulty'],
            'member' => (int)$postContent['member'],
            'total_points' => $total_points,
        ]);


        //Create Announcement for the duty
        $announcementContent = '【'.Auth::user()->name.'】 创建了基础项目 【'.$postContent["basic-no"].'】，请主管领导尽快审批';
        DB::table('announcements')->insert([
            'name' => Auth::user()->name,
            'content' =>  $announcementContent,
            'is_important' => 1,
        ]);
        
        $user = Auth::user()->id;

        return redirect('/daily/'.$user)
        ->with('status', "您已成功提交基础项目 ".$postContent["basic-no"]."！");
    }
}