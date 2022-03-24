<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\File;
use Illuminate\Support\Facades\Storage;

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
        $last_duty_no = (int)(json_decode(json_encode($current_duties), true))[0]['id'] + 1;
        //$last_duty_Month = substr($last_duty_no, 1, 2);
        //$last_duty_no = (int)substr($last_duty_no, 3);
        /*
        if ($last_duty_Month == date('m')){
            $last_duty_no = sprintf("%03d", $last_duty_no + 1);
        }else{
            $last_duty_no = "001";
        }
        */
        return $last_duty_no; //"D".date('m').$last_duty_no;
    }else{
        return "001"; //"D".date('m')."001";
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

function MonthlyPoints($dutyNo){
    $basicPoints = DB::table('performance_duty')
                        ->where('performance_no', $dutyNo)
                        ->value('basic_points');

    $currentMonth = date('m');

    $response = DB::table('duty_node')
                    ->where('duty_performance_no', $dutyNo)
                    ->whereMonth('node_date', '=' , $currentMonth)
                    ->orderBy('node_id')
                    ->get();

    $array = json_decode(json_encode($response), true);
    //var_dump($array);
    $coefficient = 0.0;
    
    foreach($array as $node){
        $coefficient += ((float)$node['node_point_percentage'])/100 * $node['node_completeness_coefficient'];
    }
    
    return $basicPoints*$coefficient;
}

class PerformancesController extends Controller
{
    //Hide performance duty
    public function HidePerformanceDuty(Request $request, $arg){
        $dutyId = (int)$arg;
        DB::table('performance_duty')
                ->where('id', $dutyId)
                ->update([
                    'status' => "end",
                ]);
                
        return redirect('/')
        ->with('status', "您已成功删除业绩事项！");
    }
    
    //Get all duties
    public function GetAllPerformances(Request $request){
        $performances = DB::table('performance_duty')
        ->where('status',"!=","end")->orderBy('timestamp','desc')->get();
        return $performances;
    }
    
    //Get all notification
    public function GetAllNotifications(Request $request){
        $performances = DB::table('performance_duty')
        ->where('status',"!=","end")->orderBy('timestamp','desc')->get();
        
        $performances = json_decode($performances, true);
        
        $notifications = array();
        
        foreach ($performances as $p){
            $today = Carbon::today()->format('Y-m-d');
            if($p['next_date'] == $today){
                array_push($notifications, "今天有节点申报");
            }else{
                array_push($notifications, "");
            }
        }
        
        return $notifications;
    }

    //Get duties by user ID
    public function GetPerformanceDutiesByUserId(Request $request, $arg){
        $userId = (int)$arg;
        $response = DB::table('performance_duty')
                            ->where('status',"!=","end")
                            ->orderBy('status','desc')
                            ->get();
        $array = json_decode(json_encode($response), true);
        $today = Carbon::today()->format('Y-m-d');

        $returnArray = array();

        foreach ($array as $duty){
            $userArray = array();
            array_push($userArray, (int)$duty['leader']);

            $members = json_decode($duty['members']);

            if (!empty($members))
            foreach ($members as $member){
                array_push($userArray, (int)$member);
            }
            
            if (in_array($userId, $userArray)){
                $latest_node = DB::table('duty_node')
                    ->where('duty_performance_no',"=",$duty['performance_no'])
                    ->where('node_date',"=",$today)
                    ->get();
                $latest_node = json_decode(json_encode($latest_node), true);    
                //var_dump($latest_node);
                if($duty['next_date'] == $today && !isset($latest_node["node_completeness"])){
                    $duty['notification'] = "今天你有节点未申报";
                }else if($duty['next_date'] == $today && ($latest_node["node_completeness"] == 100 || $latest_node["node_completeness"] == 0)){
                    $duty['notification'] = "今天节点已申报";
                }else{
                    $duty['notification'] = "";
                }
                array_push($returnArray, $duty);
            }
        }
        
        return $returnArray;
    }

    //Get User Details
    public function GetUserDetails(Request $request){
        $userArray = Auth::user();
        return $userArray;
    }

    //Create a new performance duty, together with its nodes
    public function PostDuty(Request $request){
        $postContent = $request->all();
        if (isset($postContent['members'])){
            $leader = json_encode($postContent['members']);
        } else {
            $leader = "";
        }

        $basic_points = CalculateBasicPoints(18.0, $postContent['type'], $postContent['difficulty']);

        //$performance_no = $postContent["categories"].GenerateDutyNum();
        DB::table('performance_duty')->insert([
            'performance_content' => $postContent["content"],
            'performance_no' => $postContent["performance-no"], //$performance_no,
            'type' => $postContent['type'],
            'property' => $postContent['property'],
            'difficulty' => $postContent['difficulty'],
            'leader' => (int)$postContent['leader'],
            'second_leader' => (int)$postContent['leader2'],
            'members' => $leader,
            'start_date' => $postContent['start-date'],
            'end_date' => $postContent['end-date'],
            'node_no' => (int)$postContent['node-no'],
            'basic_points' => $basic_points,
            'latest_progress' => "",
            'declarant_id' => (int)$postContent['declarant-id'],
        ]);

        //Generate nodes
        for ($i=1; $i<= (int)$postContent['node-no']; $i++){
            DB::table('duty_node')->insert([
                'duty_performance_no' => $postContent["performance-no"], //$performance_no,
                'node_id' => $i,
                'node_date' => $postContent['date_'.$i],
                'node_point_percentage' => $postContent['percentage_'.$i],
                'node_goal' => $postContent['goal_'.$i]
            ]);
        }

        //Create Announcement for the duty
        $announcementContent = '【'.Auth::user()->name.'】 创建了业绩事项 【'.$postContent["performance-no"].'】，请主管领导尽快审批';
        DB::table('announcements')->insert([
            'name' => Auth::user()->name,
            'content' =>  $announcementContent,
            'is_important' => 1,
        ]);

        return redirect('/')
        ->with('status', "您已成功提交业绩事项 ".$postContent["performance-no"]."！");
    }

    //Get the info of some duty by its ID
    public function SeeDuty(Request $request, $arg){
        //Get id of performance duty as $dutyId
        $dutyId = (int)$arg;
        $response = DB::table('performance_duty')->where('id',$dutyId)->get();
        $array = json_decode(json_encode($response), true);
        return $array;
    }

    //Get the info of nodes of specific performance duty, searched by the performance duty ID
    public function SeeNodes(Request $request, $arg){
        $response = DB::table('duty_node')->where('duty_performance_no', $arg)->orderBy('node_id')->get();
        $array = json_decode(json_encode($response), true);
        return $array;
    }

    //Update the info of the specific node
    public function UpdateNode(Request $request){
        $postContent = $request->all();

        $expectedDate = DB::table('duty_node')
                        ->where('duty_performance_no', $postContent['performance_no'])
                        ->where('node_id',$postContent['node_id'])
                        ->value('node_date');

        $nodeBeginningDate;

        if ($postContent['node_id'] == '1'){
            //The first node, begining date is same as duty beginning date
            $nodeBeginningDate = DB::table('performance_duty')
                                    ->where('performance_no', $postContent['performance_no'])
                                    ->value('start_date');
            $nodeBeginningDate = Carbon::parse($nodeBeginningDate);
        }else{
            //The rest nodes, beginning date equals to the complete date +1 of the last node
            $nodeBeginningDate = DB::table('duty_node')
                                    ->where('duty_performance_no', $postContent['performance_no'])
                                    ->where('node_id', (int)$postContent['node_id'] - 1)
                                    ->value('node_date');
            $nodeBeginningDate = Carbon::parse($nodeBeginningDate)->addDay();
        }
        
        
        $expectedDate = Carbon::parse($expectedDate);
        if($postContent['finish-date'] == null){
            $finishDate = Carbon::today();
        }else{
            $finishDate = Carbon::parse($postContent['finish-date']);
        }
        
        $nodeDuration = $nodeBeginningDate->diffInDays($expectedDate);

        $earlyDays = $finishDate->diffInDays($expectedDate, false);

        $completeDegree = 1.0;
        if($nodeDuration == 0) $nodeDuration = 1.0;
        $completeDegree = (float)($earlyDays) / (float)($nodeDuration);

        $completenessCoefficient = 1.0;

        switch (true){
            case ($completeDegree >= 0.5):
                $completenessCoefficient = 1.5;
                break;
            case ($completeDegree >= 0.2 and $completeDegree < 0.5):
                $completenessCoefficient = 1.2;
                break;
            case ($completeDegree >= -0.2 and $completeDegree < 0.2):
                $completenessCoefficient = 1.0;
                break;
            case ($completeDegree < -0.2 and $completeDegree >= -0.3):
                $completenessCoefficient = 0.8;
                break;
            case ($completeDegree < -0.3 and $completeDegree >= -0.5):
                $completenessCoefficient = 0.7;
                break;
            case ($completeDegree < -0.5):
                $completenessCoefficient = 0.5;
                break;
        }

        if ($earlyDays > 0){
            $delayed_days = 0;
        }else{
            $delayed_days = 0 - $earlyDays;
        }

        //Update the table `duty_node` by the request info
        DB::table('duty_node')
        ->where('duty_performance_no', $postContent['performance_no'])
        ->where('node_id', $postContent['node_id'])
        ->update([
            "node_completeness" => $postContent["completeness"],
            "node_progress" => $postContent['progress'],
            "confirmed_date" => $postContent['current_date'],
            "node_completeness_coefficient" => $completenessCoefficient,
            "delayed_days" => $delayed_days,
            "confirmed_date" => $finishDate
        ]);

        $members_no = (float) CountMembers($postContent['performance_no']);

        $total_points = (float) UpdateGainedPoints($postContent['performance_no']);

        $leader_points = bcmul($total_points, 0.2, 2) + bcdiv( bcmul($total_points, 0.8, 2) , $members_no, 2);

        $member_points;
        if ($members_no == 1.0){
            $member_points = 0.0;
        } else {
            $member_points = (float)bcdiv(($total_points - $leader_points) , ($members_no - 1), 2);
        }

        //Update the completeness, add by the current node completeness

        $nodeArray = DB::table('duty_node')
                            ->where('duty_performance_no', $postContent['performance_no'])
                            ->get();
        
        $nodeArray = json_decode($nodeArray, true);

        $updateCompleteness = 0.0;

        foreach ($nodeArray as $node){
            $updateCompleteness += bcdiv($node['node_completeness']*$node['node_point_percentage'], 100, 2);
        }

        /*
        echo 'beginning date: '.$nodeBeginningDate.'<br/>';
        echo 'expected: '.$expectedDate.'<br/>';
        echo 'finished: '.$finishDate.'<br/>';
        echo 'expected duration: '.$nodeDuration.'<br/>';
        echo 'early days: '.$earlyDays.'<br/>';
        echo 'complete degree: '.$completeDegree.'<br/>';
        echo 'completeness cofficient: '.$completenessCoefficient.'<br/>';
        */

        //update the performance_duty table, by the performance duty no
        DB::table('performance_duty')->where('performance_no', $postContent['performance_no'])->update([
            'latest_progress' => $postContent['progress'],
            'gained_points' => $total_points,
            'leader_points' => $leader_points,
            'member_points' => $member_points,
            'completeness' => $updateCompleteness,
        ]);

        return redirect('/')
        ->with('status', "您已更新【".$postContent['performance_no']."】的节点信息 【节点#".$postContent['node_id']."】！");
    }

    //Change the status of the duty
    public function CheckDuty(Request $request){
        $postContent = $request->all();
        DB::table('performance_duty')
        ->where('id', $postContent['performance_id'])
        ->update([
            'status' => $postContent["check"],
        ]);
        
        return redirect('/')
        ->with('status', "业绩项目审批完成！");
    }

    /**
     * Return JSON to generate the performance duties table
     * @param String content
     * @param String no
     * @param String type
     * @param String property
     */
    public function GetDutiesTable(Request $request){
        $dutiesArray = DB::table('performance_duty')->orderBy('timestamp','desc')->get();
        $dutiesArray = json_decode($dutiesArray, true);

        $userArray = DB::table('users')->get();
        $userArray = json_decode($userArray, true);

        $nodeArray = DB::table('duty_node')->get();
        $nodeArray = json_decode($nodeArray, true);

        $row = array();

        for ($i = 0; $i < count($dutiesArray); $i++){

            $difficulty = '';
            switch ($dutiesArray[$i]['difficulty']){
                case 'difficult':
                    $difficulty = "困难";
                    break;
                case 'normal':
                    $difficulty = "中等";
                    break;
                case 'easy':
                    $difficulty = "简单";
                    break;
            }

            $status = '';
            switch ($dutiesArray[$i]['status']){
                case 'pending':
                    $status = "待审批";
                    break;
                case 'rejected':
                    $status = "未通过";
                    break;
                case 'processing':
                    $status = "进行中";
                    break;
                case 'done':
                    $status = "完成";
                    break;
                case 'delayed':
                    $status = "延迟";
                    break;
                case 'postponed':
                    $status = "暂缓";
                    break;
            }

            $leader = '';
            foreach ($userArray as $user){
                if ($user['id'] == $dutiesArray[$i]['leader']){
                    $leader = $user['name'];
                    break;
                }
            }

            $second_leader = '';
            foreach ($userArray as $user){
                if ($user['id'] == $dutiesArray[$i]['second_leader']){
                    $second_leader = $user['name'];
                    break;
                }
            }

            $members = '';
            if ($dutiesArray[$i]['members'] != ""){
                $members = parseMembers($dutiesArray[$i]['members']);
            }

            $unit = array(
                'id' => $dutiesArray[$i]['id'],
                'content' => $dutiesArray[$i]['performance_content'],
                'no' => $dutiesArray[$i]['performance_no'],
                'type' => $dutiesArray[$i]['type'],
                'property' => $dutiesArray[$i]['property'],
                'difficulty' => $difficulty,
                'status' => $status,
                'leader' => $leader,
                'second_leader' => $second_leader,
                'members' => $members,
                'start_date' => $dutiesArray[$i]['start_date'],
                'end_date' => $dutiesArray[$i]['end_date'],
                'entire_completeness' => $dutiesArray[$i]['completeness'],
                'basic_points' => $dutiesArray[$i]['basic_points'],
                'gained_points' => $dutiesArray[$i]['gained_points'],
                'latest_progress' => $dutiesArray[$i]['latest_progress'],
                'node_no' => $dutiesArray[$i]['node_no'],
                'next_date' => $dutiesArray[$i]['next_date'],
                'next_goal' => $dutiesArray[$i]['next_goal'],
                'next_percentage' =>  $dutiesArray[$i]['next_percentage'],
                //'next_date' => $dutiesArray[$i][''],
                //'goal' => $dutiesArray[$i][''],
                //'node_percentage' => ,
                //'node_completeness' => ,
                'detail_url' => "/duties/".$dutiesArray[$i]['id'],
            );

            array_push($row, $unit);
        }

        $returnJSON = array();
        $returnJSON['total'] = count($dutiesArray);
        $returnJSON['totalNotFiltered'] = count($dutiesArray);
        $returnJSON['row'] = $row;
        return json_encode($returnJSON, true);
    }

    /**
     * Update the performance duty table, with the latest next node
     * Has been loaded in Laravel schedule
     */
    public function UpdatePerformanceDuty(Request $request){
        $today = Carbon::now();

        $dutyArray = DB::table('performance_duty')
                        ->get();
        $dutyArray = json_decode($dutyArray, true);

        foreach ($dutyArray as $duty){

            $dutyNo = $duty['performance_no'];
            echo 'No: '.$dutyNo.'<br/>';

            $nodeArray = DB::table('duty_node')
                            ->where('duty_performance_no', $dutyNo)
                            ->orderBy('node_id', 'asc')
                            ->get();

            $nodeArray = json_decode($nodeArray, true);

            echo 'Count Nodes: '.count($nodeArray).'<br/>';

            $nextAssessingDay = Carbon::parse($duty['start_date']);
            $next_id = 0;
            $next_goal = '';
            $next_percentage = 0;

            echo "Start Day: ".$nextAssessingDay.'<br/>';
            
            if (count($nodeArray) == 1){

                echo 'Here: '.$nodeArray[0]['node_date'].'<br/>';
                $nextAssessingDay = $nodeArray[0]['node_date'];

            }else{
                for ($i = 0; $i < count($nodeArray); $i++){
                    echo "next: ".Carbon::parse($nodeArray[$i]['node_date']).'<br/>';

                    if (Carbon::parse($nodeArray[$i]['node_date'])->isFuture()){
                        $nextAssessingDay = Carbon::parse($nodeArray[$i]['node_date']);
                        $next_id = $i;
                        //$next_id = $nodeArray[$i]['node_id'];
                        break;
                    }else{
                        if(Carbon::parse($nodeArray[$i]['node_date'])->gt($nextAssessingDay)){
                            $nextAssessingDay = Carbon::parse($nodeArray[$i]['node_date']);
                            $next_id = $i;
                            //$next_id = $nodeArray[$i]['node_id'];
                        }
                    }
                }
            }

            echo 'Next assessing day: '.$nextAssessingDay.'<br/>';
            echo 'Next node: '.$next_id.'<br/>';

            DB::table('performance_duty')
                ->where('id', $duty['id'])
                ->update([
                    'next_date' => $nextAssessingDay,
                    'next_goal' => $nodeArray[$next_id]['node_goal'],
                    'next_percentage' => $nodeArray[$next_id]['node_point_percentage'],
                ]);
            
            echo '<br/>';
        }
    }

    /**
     * Update the table `user_total_point`
     */
    public function UpdateTotalPoints(Request $request){
        $userArray = DB::table('users')
                        ->get();
        $userArray = json_decode($userArray, true);

        $dutiesArray = DB::table('performance_duty')
                        ->orderBy('create_timestamp', 'desc')
                        ->get();
        $dutiesArray = json_decode($dutiesArray, true);

        //For each user, count its total points.
        //Total points initialized as 0.
        foreach ($userArray as $user){
            echo 'User: '.$user['id'].'<br/>';

            $user_id = $user['id'];
            $total_points = 0.0;

            //For each duty, check whether the current user is leader or member
            foreach ($dutiesArray as $duty){
                //User is the leader
                if ($duty['leader'] == $user_id){
                    $total_points += $duty['leader_points'];
                    echo 'User '.$user_id.' is leader of duty '.$duty['id'].'. Add points '.$duty['leader_points'].'.<br/>';
                    continue;
                }else{
                //User is one of members
                    if ($duty['members'] != ''){
                        if (IsDutyMember($user_id, $duty['members'])){
                            $total_points += $duty['member_points'];
                            echo 'User '.$user_id.' is a member of duty '.$duty['id'].'. Add points '.$duty['member_points'].'.<br/>';
                            continue;
                        }else{
                            continue;
                        }
                    }else{
                        continue;
                    }
                }
            }

            echo "User total points: ".$total_points.'<br/><br/>';

            DB::table('user_total_points')
                ->updateOrInsert(
                    ['id' => $user_id],
                    ['total_points' => $total_points]
                );
        }
    }

    public function MonthlyPerformancePoint(Request $request){
        
        $dutyArray_demo = DB::table('performance_duty')->
                         where(function ($query){
                            $user_id = Auth::user()->id;
                            $query->where('leader', $user_id)->
                                    orWhere('members', 'like', "%\"{$user_id}\"%");
                         });
                         
        $currentMonth = date('m');
        
        $dutyArray_demo1 = $dutyArray_demo
                            ->whereMonth('start_date', '<=' , $currentMonth)
                            ->whereMonth('end_date', '>=' , $currentMonth);
        
        $dutyArray_demo2 = $dutyArray_demo1->
                        whereNotIn('status', ['rejected', 'end']);

        $dutyArray = $dutyArray_demo2->select(['*', DB::raw("0 as leader_month, 0 as member_month")])->get();

        //$performanceArray = array();
        //$dutyArray = $dutyArray->DB::raw('0 AS leader_month, 0 AS member_month');
        
        $monthlyPoint = array();
        
        foreach ($dutyArray as $element){
            $members_no = (float) CountMembers($element->performance_no);

            $total_points = (float) MonthlyPoints($element->performance_no);
            //echo "AAAAAAA"+$total_points;
    
            $leader_points = bcmul($total_points, 0.2, 2) + bcdiv( bcmul($total_points, 0.8, 2) , $members_no, 2);
    
            $member_points;
            if ($members_no == 1.0){
                $member_points = 0.0;
            } else {
                $member_points = (float)bcdiv(($total_points - $leader_points) , ($members_no - 1), 2);
            }
            
            //array_push($monthlyPoint, [$leader_points, $member_points]);
            //$element = $element->addSelect(DB::raw("{$leader_points} as leader_month"));
            //$element = $element->addSelect(DB::raw("{$member_points} as member_month"));
            $element->leader_month = $leader_points;
            $element->member_month = $member_points;
        }
        
        //$currentMonth = date('m');

        //$monthlyNodes = DB::table('duty_node')->whereIn('duty_performance_no', $performanceArray)->
        //whereRaw('MONTH(node_date) = ?',[$currentMonth])->get();
        
        //$groupByDate = DB::table('duty_node')->groupBy('confirmed_date')->get();
        return $dutyArray;
    }

    /**
     * Update the `month_point` table
     */
    public function UpdateMonthPoints(Request $request){
        $date = Carbon::now()->firstOfMonth();
        echo $date;
    }

    //Show the edit page of performance duty by its ID
    public function ShowPerformanceDuty(Request $request, $arg){
        $dutyId = (int)$arg;
        $array = DB::table('performance_duty')->where('id',$dutyId)->get();
        $performancedata = json_decode(json_encode($array), true);

        $array2 = DB::table('users')->get();
        $user = json_decode(json_encode($array2), true);
        
        $array3 = DB::table('duty_node')->where('duty_performance_no',$performancedata[0]['performance_no'])
        ->orderBy('node_id')->get();
        $nodedata = json_decode(json_encode($array3), true);
        //return $array;
        return view('performance.edit', ['performancedata' => $performancedata, 'user' => $user, 'nodedata' => $nodedata]);
    }
    
    public function getTotalMonthlyPoints(Request $request){
        $BasicEvents = DB::table('basic_duty')->where('member', Auth::user()->id)
        ->where('status', "approved")
        ->orderBy('timestamp','desc')->get();
        $basic_total = 0.0;
        foreach ($BasicEvents as $element){
            $basic_total += $element->total_points;
        }
        
        $PerformanceEvents = self::MonthlyPerformancePoint($request);
        
        $performance_total = 0.0;
        foreach ($PerformanceEvents as $element){
            if($element->leader == Auth::user()->id){
                $performance_total += $element->leader_month;
            }
            else{
                $performance_total += $element->member_month;
            }
        }
        
        return $performance_total+$basic_total;
    }
    
    public function EditPerformanceDuty(Request $request){
        if (isset($request['members'])){
            $members = json_encode($request['members']);
        } else {
            $members = "";
        }
        
        echo $request;
        
        DB::table('performance_duty')
                ->where('performance_no', $request["performance_no"])
                ->update([
                    'performance_no' => $request["performance-no"],
                    'performance_content' => $request["content"],
                    'type' => $request["type"],
                    'property' => $request["property"],
                    'difficulty' => $request["difficulty"],
                    'node_no' => (int) $request["node-no"],
                    'leader' => (int) $request["leader"],
                    'second_leader' => (int)$request['leader2'],
                    'members' => $members,
                    'start_date' => $request["start-date"],
                    'end_date' => $request["end-date"],
                ]);
        
        //Update the table `duty_node` by the request info
        DB::table('duty_node')
        ->where('duty_performance_no', $request['performance-no'])
        ->delete();
        
        //Generate nodes
        for ($i=1; $i<= (int)$request['node-no']; $i++){
            DB::table('duty_node')->insert([
                'duty_performance_no' => $request["performance-no"],
                'node_id' => $i,
                'node_date' => $request['date_'.$i],
                'node_point_percentage' => $request['percentage_'.$i],
                'node_goal' => $request['goal_'.$i]
            ]);
        }

        return redirect('/')
        ->with('status', "您已更新【".$request['performance-no']."】！");
    }
    
    public function PostProfit(Request $request){
        $request->validate([
            'file' => 'required|mimes:docx,jpeg,pdf,png|max:2048'
        ]);
        
        if($request->event_image instanceof \Illuminate\Http\UploadedFile) {

            $event->event_image = $request->event_image->store('event_images', 'public');
        }

        if($request->hasFile('file')){
            $fileName = time().'_'.$request->file->getClientOriginalName();
            $filePath = $request->file('file')->storeAs('uploads', $fileName, 'public');
    
            DB::table('performance_duty')
                    ->where('performance_no', $request["performance-no"])
                    ->update([
                        'file_name' => time().'_'.$request->file->getClientOriginalName(),
                        'file_path' => '/storage/'.$filePath,
                        'upload_at' => Carbon::now(),
                        'direction' => $request["direction"],
                        'amount' => $request["amount"],
                        'profit_status' => "pending"
                    ]);
        }

        //$fileModel = new File;
        return redirect('/')
        ->with('status', "您已申报业绩事项获利【".$request["performance-no"]."】！");
/*
        return back()
        ->with('success','File has been uploaded.')
        ->with('file', $fileName);
        //return $request;

        if($request->file()) {
            $fileName = time().'_'.$request->file->getClientOriginalName();
            $filePath = $request->file('file')->storeAs('uploads', $fileName, 'public');

            $fileModel->name = time().'_'.$request->file->getClientOriginalName();
            $fileModel->file_path = '/storage/' . $filePath;
            $fileModel->save();

            return back()
            ->with('success','File has been uploaded.')
            ->with('file', $fileName);
        }
*/
   }
   
    //Show the edit page of performance no by its ID
    public function ShowProfitDuty(Request $request, $arg){
        $dutyId = (int)$arg;
        $array = DB::table('performance_duty')->where('id',$dutyId)->get();
        $data = json_decode(json_encode($array), true);

        //return $array;
        return view('performance.profit-approval', ['data' => $data]);
    }
    
    public function DownloadProfit(Request $request, $arg){
        $dutyId = (int)$arg;
        $array = DB::table('performance_duty')->where('id',$dutyId)->get();
        $data = json_decode(json_encode($array), true);
        
        //echo "uploads/".$data[0]['file_name'];
        //print_r("uploads/".$data[0]['file_name']);
        if(Storage::disk('public')->exists("uploads/".$data[0]['file_name'])){
            //$path = Storage::disk('public')->path("uploads/".$data[0]['file_name']);
            
            return response()->file("/www/wwwroot/portal.treelion.com/storage/app/public/uploads/".$data[0]['file_name']);
            
            //$content = file_get_contents($path);
            //return response()->file("/www/wwwroot/portal.treelion.com/storage/app/public/uploads/1636508891_Strapi Optimization.png");
            //return response($content)->withHeaders([
            //    'Content-Types'=>mime_content_type($path)
            //]);
        }
        return redirect('/404');
    }
    
    public function EditProfitDuty(Request $request){
        $money = $request["amount"];
        $direction = $request["direction"];
        $coefficient = 1.0;
        
        if($direction == "direct"){
            if($money>=50000001 && $money<100000000)
                $coefficient = 5000;
            else if($money>=20000001 && $money<50000001)
                $coefficient = 2000;
            else if($money>=10000001 && $money<20000001)
                $coefficient = 1000;
            else if($money>=5000001 && $money<10000001)
                $coefficient = 500;
            else if($money>=2000001 && $money<5000001)
                $coefficient = 200;
            else if($money>=1000001 && $money<2000001)
                $coefficient = 100;
            else if($money>=500001 && $money<1000001)
                $coefficient = 50;
            else if($money>=200001 && $money<500001)
                $coefficient = 20;
            else if($money>=100001 && $money<200001)
                $coefficient = 10;
            else if($money>=50001 && $money<100001)
                $coefficient = 5;
            else if($money>=20001 && $money<50001)
                $coefficient = 2;
            else if($money>=10001 && $money<20001)
                $coefficient = 1;
            else if($money>=5001 && $money<10001)
                $coefficient = 0.9;
            else if($money>=1001 && $money<5001)
                $coefficient = 0.8;
            else if($money>=1 && $money<1001)
                $coefficient = 0.7;
            else
                $coefficient = 0.0;
        }else{
            if($money>=50000001 && $money<100000001)
                $coefficient = 500;
            else if($money>=20000001 && $money<50000001)
                $coefficient = 200;
            else if($money>=10000001 && $money<20000001)
                $coefficient = 100;
            else if($money>=5000001 && $money<10000001)
                $coefficient = 50;
            else if($money>=2000001 && $money<5000001)
                $coefficient = 20;
            else if($money>=1000001 && $money<2000001)
                $coefficient = 10;
            else if($money>=500001 && $money<1000001)
                $coefficient = 5;
            else if($money>=200001 && $money<500001)
                $coefficient = 2;
            else if($money>=10001 && $money<200001)
                $coefficient = 1;
            else if($money>=5001 && $money<10001)
                $coefficient = 0.9;
            else if($money>=1001 && $money<5001)
                $coefficient = 0.8;
            else if($money>=1 && $money<1001)
                $coefficient = 0.7;
            else
                $coefficient = 0.0;
        }
        //return $request;
  
        if($request["profit_status"] == "approved"){
            $statement = "【".$request["performance_no"]."】的获利申报已经批准";
            $announcementContent = '【'.Auth::user()->name.'】 已经批准 【'.$request["performance_no"].'】的获利申报';
            $color = 'status';
        }else{
            $statement = "【".$request["performance_no"]."】的获利申报已被驳回";
            $announcementContent = '【'.Auth::user()->name.'】 已经驳回 【'.$request["performance_no"].'】的获利申报，请重新填写';
            $color = 'danger';
            $coefficient = 0;
        }
        
        DB::table('performance_duty')
                ->where('performance_no', $request["performance_no"])
                ->update([
                    'profit_status' => $request["profit_status"],
                    'profit_coefficient' => $coefficient
                ]);
        
        //Create Announcement for the duty
        DB::table('announcements')->insert([
            'name' => Auth::user()->name,
            'content' =>  $announcementContent,
            'is_important' => 1,
        ]);
        return redirect('/')
        ->with($color, $statement);
    }
}
