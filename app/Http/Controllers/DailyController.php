<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

function findDutyNo($dutyArray, $dutyId){
    foreach ($dutyArray as $i => $value){
        if ($value['id'] == $dutyId){
            return $value['performance_no'];
        }
    }
    return "";
}

function findUserName($userArray, $userId){
    foreach ($userArray as $i => $value){
        if ($value['id'] == $userId){
            return $value['name'];
        }
    }
    return "";
}

class DailyController extends Controller
{

    Public function PostDaily(Request $request){
        $postContent = $request->all();
        $userId = $postContent['declarant-id'];

        for ($i=0; $i<7; $i++)
        {
            DB::table('daily_report')
                ->updateOrInsert([
                    'user_id' => $userId,
                    'date' => $postContent['date'],
                ],[
                    'performance_no_'.$i => $postContent['performance_no_'.$i],
                    'leader_'.$i => $postContent['leader_'.$i],
                    'content_'.$i => $postContent['content_'.$i],
                ]
            );
        }

        return redirect("/daily/".$userId);
    }

    //Get all daily reports of someone
    Public function GetDaily(Request $request, $arg){
        $userId = (int)$arg;

        $response = DB::table('daily_report')
                        ->where('user_id', $userId)
                        ->orderBy('timestamp', 'desc')
                        ->get();
        $array = json_decode(json_encode($response), true);
        return $array;
    }

    Public function GetCompany(Request $request, $arg){
        $userId = (int)$arg;

        $response = DB::table('company_employee')
                        ->find($userId);
        $array = json_decode(json_encode($response), true);

        return $array;
    }

    public function GetDailyById(Request $request, $arg){
        $dailyId = (int)$arg;
        $response = DB::table('daily_report')
                        ->where('id', $dailyId)
                        ->get();
        
        $response =json_decode(json_encode($response), true);
        return $response;
    }

    //Get daily report by someone on specific day
    Public function GetDailyUpdate(Request $request, $arg1, $arg2){
        $userId = (int)$arg1;
        $date = $arg2;

        $response = DB::table('daily_report')
                        ->where('user_id', $userId)
                        ->where('date', $date)
                        ->orderBy('timestamp', 'desc')
                        ->first();
        $array = json_decode(json_encode($response), true);
        return $array;
    }

    Public function GeneratePdf(Request $request, $arg){
        $dailyId =  (int)$arg;
        //Get the daily report to be print
        $dailyreport = DB::table('daily_report')
                            ->where('id',$dailyId)
                            ->get();
        $dailyreport = json_decode(json_encode($dailyreport), true);
        $dailyreport = $dailyreport[0];

        $users = DB::table('users')->get();
        $users = json_decode(json_encode($users), true);
        $dutyArray = DB::table('performance_duty')->get();
        $dutyArray = json_decode(json_encode($dutyArray), true);
        //创建数组 performance_no_[]

        $pdfView = view('daily.pdf',[
            'name' => findUserName($users, $dailyreport['user_id']),
            'timestamp' => $dailyreport['timestamp'],

            'performance_no_0' => findDutyNo($dutyArray, $dailyreport['performance_no_0']),
            'performance_no_1' => findDutyNo($dutyArray, $dailyreport['performance_no_1']),
            'performance_no_2' => findDutyNo($dutyArray, $dailyreport['performance_no_2']),
            'performance_no_3' => findDutyNo($dutyArray, $dailyreport['performance_no_3']),
            'performance_no_4' => findDutyNo($dutyArray, $dailyreport['performance_no_4']),
            'performance_no_5' => findDutyNo($dutyArray, $dailyreport['performance_no_5']),
            'performance_no_6' => findDutyNo($dutyArray, $dailyreport['performance_no_6']),

            'leader_0' => findUserName($users, $dailyreport['leader_0']),
            'leader_1' => findUserName($users, $dailyreport['leader_1']),
            'leader_2' => findUserName($users, $dailyreport['leader_2']),
            'leader_3' => findUserName($users, $dailyreport['leader_3']),
            'leader_4' => findUserName($users, $dailyreport['leader_4']),
            'leader_5' => findUserName($users, $dailyreport['leader_5']),
            'leader_6' => findUserName($users, $dailyreport['leader_6']),

            'content_0' => $dailyreport['content_0'],
            'content_1' => $dailyreport['content_1'],
            'content_2' => $dailyreport['content_2'],
            'content_3' => $dailyreport['content_3'],
            'content_4' => $dailyreport['content_4'],
            'content_5' => $dailyreport['content_5'],
            'content_6' => $dailyreport['content_6'],

            'completeness_0' => $dailyreport['completeness_0'],
            'completeness_1' => $dailyreport['completeness_1'],
            'completeness_2' => $dailyreport['completeness_2'],
            'completeness_3' => $dailyreport['completeness_3'],
            'completeness_4' => $dailyreport['completeness_4'],
            'completeness_5' => $dailyreport['completeness_5'],
            'completeness_6' => $dailyreport['completeness_6'],

            'comment_0' => $dailyreport['comment_0'],
            'comment_1' => $dailyreport['comment_1'],
            'comment_2' => $dailyreport['comment_2'],
            'comment_3' => $dailyreport['comment_3'],
            'comment_4' => $dailyreport['comment_4'],
            'comment_5' => $dailyreport['comment_5'],
            'comment_6' => $dailyreport['comment_6'],
        ]);

        $pdf = \PDF::loadHTML($pdfView);
        return $pdf->inline('日内工作申报-'.findUserName($users, $dailyreport['user_id']).'-'.$dailyreport['date']);
    }

    Public function GenerateImg(Request $request, $arg){
        $dailyId =  (int)$arg;
        //Get the daily report to be print
        $dailyreport = DB::table('daily_report')
                            ->where('id',$dailyId)
                            ->get();
        $dailyreport = json_decode(json_encode($dailyreport), true);
        $dailyreport = $dailyreport[0];

        $users = DB::table('users')->get();
        $users = json_decode(json_encode($users), true);

        $dutyArray = DB::table('performance_duty')->get();
        $dutyArray = json_decode(json_encode($dutyArray), true);
        //创建数组 performance_no_[]

        $pdfView = view('daily.pdf',[
            'name' => findUserName($users, $dailyreport['user_id']),
            'timestamp' => $dailyreport['timestamp'],

            'performance_no_0' => findDutyNo($dutyArray, $dailyreport['performance_no_0']),
            'performance_no_1' => findDutyNo($dutyArray, $dailyreport['performance_no_1']),
            'performance_no_2' => findDutyNo($dutyArray, $dailyreport['performance_no_2']),
            'performance_no_3' => findDutyNo($dutyArray, $dailyreport['performance_no_3']),
            'performance_no_4' => findDutyNo($dutyArray, $dailyreport['performance_no_4']),
            'performance_no_5' => findDutyNo($dutyArray, $dailyreport['performance_no_5']),
            'performance_no_6' => findDutyNo($dutyArray, $dailyreport['performance_no_6']),

            'leader_0' => findUserName($users, $dailyreport['leader_0']),
            'leader_1' => findUserName($users, $dailyreport['leader_1']),
            'leader_2' => findUserName($users, $dailyreport['leader_2']),
            'leader_3' => findUserName($users, $dailyreport['leader_3']),
            'leader_4' => findUserName($users, $dailyreport['leader_4']),
            'leader_5' => findUserName($users, $dailyreport['leader_5']),
            'leader_6' => findUserName($users, $dailyreport['leader_6']),

            'content_0' => $dailyreport['content_0'],
            'content_1' => $dailyreport['content_1'],
            'content_2' => $dailyreport['content_2'],
            'content_3' => $dailyreport['content_3'],
            'content_4' => $dailyreport['content_4'],
            'content_5' => $dailyreport['content_5'],
            'content_6' => $dailyreport['content_6'],

            'completeness_0' => $dailyreport['completeness_0'],
            'completeness_1' => $dailyreport['completeness_1'],
            'completeness_2' => $dailyreport['completeness_2'],
            'completeness_3' => $dailyreport['completeness_3'],
            'completeness_4' => $dailyreport['completeness_4'],
            'completeness_5' => $dailyreport['completeness_5'],
            'completeness_6' => $dailyreport['completeness_6'],

            'comment_0' => $dailyreport['comment_0'],
            'comment_1' => $dailyreport['comment_1'],
            'comment_2' => $dailyreport['comment_2'],
            'comment_3' => $dailyreport['comment_3'],
            'comment_4' => $dailyreport['comment_4'],
            'comment_5' => $dailyreport['comment_5'],
            'comment_6' => $dailyreport['comment_6'],
        ]);

        $img = \SnappyImage::loadHTML($pdfView);
        return $img->inline('日内工作申报-'.findUserName($users, $dailyreport['user_id']).'-'.$dailyreport['date']);
    }

    public function UpdateDaily(Request $request){
        $content = $request->all();

        for($i = 0; $i < 7; $i ++){

            if (isset($content['reason_'.$i])){
                $reason = $content['reason_'.$i];
            } else {
                $reason = "";
            }

            if (isset($content['measure_'.$i])){
                $measure = $content['measure_'.$i];
            } else {
                $measure = "";
            }

            //Update the daily record
            DB::table('daily_report')
                ->where('user_id', $content['declarant-id'])
                ->where('date', $content['date'])
                ->update([
                    'completeness_'.$i => (int)$content['completeness_'.$i],
                    'comment_'.$i => $content['comment_'.$i],
                    'reason_'.$i => $reason,
                    'measure_'.$i => $measure,
                ]);

        }

        return redirect("/daily/".$content['declarant-id']);
    }

    public function GenerateDailyCalendar(Request $request, $arg){
        $userId = $arg;

        $response = DB::table('daily_report')
                                ->where('user_id', $userId)
                                ->get();

        $array = json_decode(json_encode($response), true);

        $returnArray = array();

        for ($i = 0; $i < count($array); $i++){
            //9:00-10:00
            $cell = array();
            $cell['title'] = $array[$i]['content_0'];
            $cell['start'] = $array[$i]['date'].'T09:00:00';
            $cell['end'] = $array[$i]['date'].'T10:00:00';
            array_push($returnArray, $cell);
            //10:00-11:00
            $cell = array();
            $cell['title'] = $array[$i]['content_1'];
            $cell['start'] = $array[$i]['date'].'T10:00:00';
            $cell['end'] = $array[$i]['date'].'T11:00:00';
            array_push($returnArray, $cell);
            //11:00-12:00
            $cell = array();
            $cell['title'] = $array[$i]['content_2'];
            $cell['start'] = $array[$i]['date'].'T11:00:00';
            $cell['end'] = $array[$i]['date'].'T12:00:00';
            array_push($returnArray, $cell);
            //14:00-15:00
            $cell = array();
            $cell['title'] = $array[$i]['content_3'];
            $cell['start'] = $array[$i]['date'].'T14:00:00';
            $cell['end'] = $array[$i]['date'].'T15:00:00';
            array_push($returnArray, $cell);
            //15:00-16:00
            $cell = array();
            $cell['title'] = $array[$i]['content_4'];
            $cell['start'] = $array[$i]['date'].'T15:00:00';
            $cell['end'] = $array[$i]['date'].'T16:00:00';
            array_push($returnArray, $cell);
            //16:00-17:00
            $cell = array();
            $cell['title'] = $array[$i]['content_5'];
            $cell['start'] = $array[$i]['date'].'T16:00:00';
            $cell['end'] = $array[$i]['date'].'T17:00:00';
            array_push($returnArray, $cell);
            //17:00-18:00
            $cell = array();
            $cell['title'] = $array[$i]['content_6'];
            $cell['start'] = $array[$i]['date'].'T17:00:00';
            $cell['end'] = $array[$i]['date'].'T18:00:00';
            array_push($returnArray, $cell);
        }

        return $returnArray;
    }
}
