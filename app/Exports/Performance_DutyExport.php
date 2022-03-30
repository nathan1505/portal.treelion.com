<?php

namespace App\Exports;

//use App\Models\Duty_Node;
//use App\Models\Student;
//use App\Models\User;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\DB;

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

class Performance_DutyExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings():array{
        return[
            'ID',
            '项目内容',
            '项目编号',
            '项目类别',
            '类别属性',
            '难度',
            '状态',
            '组长',
            '第二组长',
            '组员',
            '开始日',
            '结束日',
            '整体完成度(%)',
            '基础积分',
            '累计获取积分',
            '项目最新进展',
            '节点数',
            '下一考核时点',
            '目标描述',
            '积分比例(%)',
        ];
    }
    public function collection()
    {
        $dutiesArray = DB::table('performance_duty')->get();
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
                'basic_points' => round($dutiesArray[$i]['basic_points']),
                'gained_points' => round($dutiesArray[$i]['gained_points']),
                'latest_progress' => $dutiesArray[$i]['latest_progress'],
                'node_no' => $dutiesArray[$i]['node_no'],
                'next_date' => $dutiesArray[$i]['next_date'],
                'next_goal' => $dutiesArray[$i]['next_goal'],
                'next_percentage' =>  $dutiesArray[$i]['next_percentage'],
                //'next_date' => $dutiesArray[$i][''],
                //'goal' => $dutiesArray[$i][''],
                //'node_percentage' => ,
                //'node_completeness' => ,
                //'detail_url' => "/duties/".$dutiesArray[$i]['id'],
            );
            if($dutiesArray[$i]['status'] != 'end'){
                array_push($row, $unit);
            }
        }
        
        return collect($row);
        
        /*
        $returnJSON = array();
        $returnJSON['total'] = count($dutiesArray);
        $returnJSON['totalNotFiltered'] = count($dutiesArray);
        $returnJSON['row'] = $row;
        return json_encode($returnJSON, true);
        */

        //return $dutiesArray;
    }
}