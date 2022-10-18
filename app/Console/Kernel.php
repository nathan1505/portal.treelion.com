<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;

function send_to_wecom($text, $wecom_cid, $wecom_aid, $wecom_secret, $wecom_touid)
{
    $info = @json_decode(file_get_contents("https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=".urlencode($wecom_cid)."&corpsecret=".urlencode($wecom_secret)), true);
                
    if ($info && isset($info['access_token']) && strlen($info['access_token']) > 0) {
        $access_token = $info['access_token'];
        $url = 'https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token='.urlencode($access_token);
        $data = new \stdClass();
        $data->touser = $wecom_touid;
        $data->agentid = $wecom_aid;
        $data->msgtype = "text";
        $data->text = ["content"=> $text];
        $data->duplicate_check_interval = 600;

        $data_json = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        return $response;
    }
    return false;
}

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\DatabaseBackUp'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //Update Hang Seng Index
        $schedule->call(function(){
            $url = 'http://api.k780.com/?app=finance.globalindex&inxids=1015&appkey=59941&sign=4693de078ee2921a09757796bdd6e2ad&format=json' ;
            $resBody = (string) file_get_contents ( $url );
            $resBody = json_decode($resBody, true);
            DB::table('hsi')->insert([
                'time' => $resBody['result']['lists']['1015']['uptime'],
                'last_price' => $resBody['result']['lists']['1015']['last_price'],
                'rise_fall' => $resBody['result']['lists']['1015']['rise_fall'],
                'rise_fall_per' => $resBody['result']['lists']['1015']['rise_fall_per']
            ]);
        })->everyTwoMinutes();

        //Update elion market price
        $schedule->call(function(){
            $url = 'http://api.k780.com/?app=finance.stock_realtime&symbol=sh600277&appkey=59941&sign=4693de078ee2921a09757796bdd6e2ad&format=json' ;
            $resBody = (string) file_get_contents ( $url );
            $resBody = json_decode($resBody, true);
            DB::table('elion_stock')->insert([
                'time' => $resBody['result']['lists']['sh600277']['uptime'],
                'last_price' => $resBody['result']['lists']['sh600277']['last_price'],
                'rise_fall' => $resBody['result']['lists']['sh600277']['rise_fall'],
                'rise_fall_per' => $resBody['result']['lists']['sh600277']['rise_fall_per']
            ]);
        })->everyMinute();

        //Update latest news
        $schedule->call(function(){
            DB::table('news')->truncate();

            date_default_timezone_set( 'Asia/Shanghai' );
            $host = "https://kxapi.market.alicloudapi.com";
            $path = "/live/v3/";
            $method = "GET";
            $appcode = "1b0a45e3544a4bd1aa6e51cfb3ac04a1";
            $headers = array();
            array_push($headers, "Authorization:APPCODE " . $appcode);
            $querys = "";
            $bodys = "";
            $url = $host . $path;

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_FAILONERROR, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            if (1 == strpos("$".$host, "https://"))
            {
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            }

            $output = curl_exec($curl);
            $array = json_decode('{"data":['.substr($output,1).'}', true);//get 100 latest news in $array

            $restoreDB = DB::table('news')->orderBy('updateTime')->get();
            $restoreDB = json_decode(json_encode($restoreDB), true);

            for($i = 0; $i < 50; $i++){
                $utcTime= substr(str_replace(array('T','Z'),' ',$array['data'][$i]['time']), 0, 19);
                $dateTime = new DateTime($utcTime, new DateTimeZone('UTC'));
                $currentTimestamp = $dateTime->setTimezone(new DateTimeZone('Asia/Shanghai'))->getTimestamp();

                $updateTime= date('Y-m-d H:i:s', $currentTimestamp);
                
                DB::table('news')->insert([
                    'id' => $array['data'][$i]['id'],
                    'updateTime' => $updateTime,
                    'content' => $array['data'][$i]['content']
                ]);
            }
        })->between('23:00','12:00')->everyFiveMinutes();

        //Truncate tables
        $schedule->call(function(){
            DB::table('hsi')->truncate();
            DB::table('elion_stock')->truncate();
        })->daily();

        //Update the assessing day of performance duties
        //Frequency: per day
        $schedule->call(function(){

            $today = Carbon::now();

            $dutyArray = DB::table('performance_duty')
                            ->get();
            $dutyArray = json_decode($dutyArray, true);

            foreach ($dutyArray as $duty){

                $dutyNo = $duty['performance_no'];

                $nodeArray = DB::table('duty_node')
                                ->where('duty_performance_no', $dutyNo)
                                ->orderBy('node_id', 'asc')
                                ->get();

                $nodeArray = json_decode($nodeArray, true);

                $nextAssessingDay = Carbon::parse($duty['start_date']);
                $next_id = 0;
                $next_goal = '';
                $next_percentage = 0;
                
                if (count($nodeArray) == 1){
                    $nextAssessingDay = $nodeArray[0]['node_date'];
                }else{
                    for ($i = 0; $i < count($nodeArray); $i++){
                        if (Carbon::parse($nodeArray[$i]['node_date'])->isToday()){
                            $nextAssessingDay = Carbon::parse($nodeArray[$i]['node_date']);
                            $next_id = $i;
                            //$next_id = $nodeArray[$i]['node_id'];
                            break;
                        }
                        else if(Carbon::parse($nodeArray[$i]['node_date'])->isFuture()){
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

                DB::table('performance_duty')
                    ->where('id', $duty['id'])
                    ->update([
                        'next_date' => $nextAssessingDay,
                        'next_goal' => $nodeArray[$next_id]['node_goal'],
                        'next_percentage' => $nodeArray[$next_id]['node_point_percentage'],
                    ]);
            }
        })->hourly();

        $schedule->command('database:backup')->daily();

        //Create the month_points table for each user, on the first day of each month
        $schedule->call(function(){
            $userArray = DB::table('users')->get();
            $userArray = json_decode($userArray, true);

            $firstDay = Carbon::now()->firstOfMonth();
            $firstDayStr = $firstDay->toDateString();
            
            foreach ($userArray as $user){
                DB::table('month_points')->insert([
                    'user_id' =>  $user['id'],
                    'month' => $firstDayStr
                ]);
            }
        })->monthly();
        
        $schedule->call(function(){
            $performances_get = DB::table('performance_duty')
            ->where('status',"!=","end")->orderBy('timestamp','desc')->get();
            
            $performances_get = json_decode(json_encode($performances_get), true);
            
            $message = "";
            
            foreach ($performances_get as $p){
                $today = Carbon::today()->format('Y-m-d');
                    
                $status = DB::table('performance_duty')
                ->where('performance_no', $p['performance_no'])
                ->value('status');
                
                $leader = DB::table('users')->where('id', $p['leader'])->value('name');
                
                if($p['next_date'] == $today && $p['completeness'] < 100){
                    $p['notifications'] = "今天有节点申报";
                    $message = $p['performance_content']." (".$p['performance_no']."): ".$p['notifications']." 请组长尽快申报\r\n";
                    $wechat_id = DB::table('users')->where('id', $p['leader'])->value('wechat_id');
                    send_to_wecom($message, "ww189e06c1b93e38e2", "1000002", "nV1Ri4V1AS3kLfOi7XUMUV5r_WEbJIzLx-2chxmbmjY", $wechat_id);
                }
            }
            //print_r($ret);
            //return $message;
        })->dailyAt('15:00'); //everyTwoMinutes(); //dailyAt('15:00');
        
        $schedule->call(function(){
            $fromDate = Carbon::now()->startOfMonth()->format('Y-m-d');
            $toDate = Carbon::now()->endOfMonth()->format('Y-m-d');
            $monthwithoutzero = Carbon::now()->format('n');
            $monthwithzero = date('m');
            $fridays = [];
            $startDate = Carbon::parse($fromDate)->modify('this friday'); 
            $endDate = Carbon::parse($toDate);
            
            for ($date = $startDate; $date->lte($endDate); $date->addWeek()) {
                $fridays[] = $date->format('Y-m-d');
            }
//-------------------种子铅笔推广销售---------------//
            DB::table('performance_duty')->insert([
                'performance_content' => "种子铅笔推广销售（".$monthwithoutzero."月）",
                'performance_no' => "D01".$monthwithzero, //$postContent["performance-no"], 
                'type' => "三类积分",
                'property' => "销售",
                'difficulty' => "normal",
                'leader' => 2,
                'members' => "[\"9\",\"17\"]",
                'start_date' => Carbon::now()->startOfMonth()->format('Y-m-d'),
                'end_date' => Carbon::now()->endOfMonth()->format('Y-m-d'),
                'node_no' => 4,
                'basic_points' => 18,
                'latest_progress' => "",
                'declarant_id' => 1,
                'status' => "processing",
            ]);
    
            //Generate nodes
            for ($i=1; $i<= 4; $i++){
                DB::table('duty_node')->insert([
                    'duty_performance_no' => "D01".$monthwithzero, //$postContent["performance-no"], 
                    'node_id' => $i,
                    'node_date' => $fridays[$i-1],
                    'node_point_percentage' => 25,
                    'node_goal' => "每周考核进度",
                ]);
            }
            
            //Create Announcement for the duty
            $announcementContent = '【管理員】 创建了业绩事项 【D01'.$monthwithzero.'】，请主管领导尽快审批';
            DB::table('announcements')->insert([
                'name' => "管理員",
                'content' =>  $announcementContent,
                'is_important' => 1,
            ]);
//-------------------end of 种子铅笔推广销售---------------//
//-------------------社群宣发工作--------------------------//
            DB::table('performance_duty')->insert([
                'performance_content' => "社群宣发工作（".$monthwithoutzero."月）",
                'performance_no' => "D03".$monthwithzero, //$postContent["performance-no"], 
                'type' => "三类积分",
                'property' => "宣发",
                'difficulty' => "easy",
                'leader' => 2,
                'members' => "[\"9\",\"27\"]",
                'start_date' => Carbon::now()->startOfMonth()->format('Y-m-d'),
                'end_date' => Carbon::now()->endOfMonth()->format('Y-m-d'),
                'node_no' => 4,
                'basic_points' => 14.4,
                'latest_progress' => "",
                'declarant_id' => 1,
                'status' => "processing",
            ]);
    
            //Generate nodes
            for ($i=1; $i<= 4; $i++){
                DB::table('duty_node')->insert([
                    'duty_performance_no' => "D03".$monthwithzero, //$postContent["performance-no"], 
                    'node_id' => $i,
                    'node_date' => $fridays[$i-1],
                    'node_point_percentage' => 25,
                    'node_goal' => "根据《社群宣发》管理办法展开社群工作",
                ]);
            }

    
            //Create Announcement for the duty
            $announcementContent = '【管理員】 创建了业绩事项 【D03'.$monthwithzero.'】，请主管领导尽快审批';
            DB::table('announcements')->insert([
                'name' => "管理員",
                'content' =>  $announcementContent,
                'is_important' => 1,
            ]);
//-------------------end of 社群宣发工作--------------------------//
//-------------------牌照公司业务推广-----------------------------//
            DB::table('performance_duty')->insert([
                'performance_content' => "牌照公司业务推广（".$monthwithoutzero."月）",
                'performance_no' => "D04".$monthwithzero, //$postContent["performance-no"], 
                'type' => "三类积分",
                'property' => "宣发",
                'difficulty' => "easy",
                'leader' => 5,
                'members' => "[\"11\"]",
                'start_date' => Carbon::now()->startOfMonth()->format('Y-m-d'),
                'end_date' => Carbon::now()->endOfMonth()->format('Y-m-d'),
                'node_no' => 4,
                'basic_points' => 14.4,
                'latest_progress' => "",
                'declarant_id' => 1,
                'status' => "processing",
            ]);
    
            //Generate nodes
            for ($i=1; $i<= 4; $i++){
                DB::table('duty_node')->insert([
                    'duty_performance_no' => "D04".$monthwithzero, //$postContent["performance-no"], 
                    'node_id' => $i,
                    'node_date' => $fridays[$i-1],
                    'node_point_percentage' => 25,
                    'node_goal' => "每周至少发布一篇业务推广文章",
                ]);
            }

    
            //Create Announcement for the duty
            $announcementContent = '【管理員】 创建了业绩事项 【D04'.$monthwithzero.'】，请主管领导尽快审批';
            DB::table('announcements')->insert([
                'name' => "管理員",
                'content' =>  $announcementContent,
                'is_important' => 1,
            ]);
//-----------------end of 牌照公司业务推广------------------------//
//-------------------抖音短视频制作-------------------------------//
            DB::table('performance_duty')->insert([
                'performance_content' => "抖音短视频制作（".$monthwithoutzero."月）",
                'performance_no' => "D05".$monthwithzero, //$postContent["performance-no"], 
                'type' => "四类积分",
                'property' => "宣发",
                'difficulty' => "easy",
                'leader' => 9,
                'members' => "",
                'start_date' => Carbon::now()->startOfMonth()->format('Y-m-d'),
                'end_date' => Carbon::now()->endOfMonth()->format('Y-m-d'),
                'node_no' => 2,
                'basic_points' => 11.52,
                'latest_progress' => "",
                'declarant_id' => 1,
                'status' => "processing",
            ]);

                        //Generate nodes
            DB::table('duty_node')->insert([
                'duty_performance_no' => "D05".$monthwithzero, //$postContent["performance-no"], 
                'node_id' => 1,
                'node_date' => $fridays[1],
                'node_point_percentage' => 50,
                'node_goal' => "每兩周制作一个抖音短视频",
            ]);
            
            DB::table('duty_node')->insert([
                'duty_performance_no' => "D05".$monthwithzero, //$postContent["performance-no"], 
                'node_id' => 2,
                'node_date' => $fridays[3],
                'node_point_percentage' => 50,
                'node_goal' => "每兩周周制作一个抖音短视频",
            ]);

    
            //Create Announcement for the duty
            $announcementContent = '【管理員】 创建了业绩事项 【D05'.$monthwithzero.'】，请主管领导尽快审批';
            DB::table('announcements')->insert([
                'name' => "管理員",
                'content' =>  $announcementContent,
                'is_important' => 1,
            ]);
//-----------------end of 抖音短视频制作----------------------------//
//-------------------政策新闻研究-------------------------------//
            DB::table('performance_duty')->insert([
                'performance_content' => "政策新闻研究（".$monthwithoutzero."月）",
                'performance_no' => "D06".$monthwithzero, //$postContent["performance-no"], 
                'type' => "四类积分",
                'property' => "行业研究",
                'difficulty' => "normal",
                'leader' => 5,
                'members' => "",
                'start_date' => Carbon::now()->startOfMonth()->format('Y-m-d'),
                'end_date' => Carbon::now()->endOfMonth()->format('Y-m-d'),
                'node_no' => 4,
                'basic_points' => 14.4,
                'latest_progress' => "",
                'declarant_id' => 1,
                'status' => "processing",
            ]);
    
            //Generate nodes
            for ($i=1; $i<= 4; $i++){
                DB::table('duty_node')->insert([
                    'duty_performance_no' => "D06".$monthwithzero, //$postContent["performance-no"], 
                    'node_id' => $i,
                    'node_date' => $fridays[$i-1],
                    'node_point_percentage' => 25,
                    'node_goal' => "提供5份每日新闻要点",
                ]);
            }

    
            //Create Announcement for the duty
            $announcementContent = '【管理員】 创建了业绩事项 【D06'.$monthwithzero.'】，请主管领导尽快审批';
            DB::table('announcements')->insert([
                'name' => "管理員",
                'content' =>  $announcementContent,
                'is_important' => 1,
            ]);
//-----------------end of 政策新闻研究----------------------------//
//-------------3060/Green Passport合作伙伴推进--------------------//
            DB::table('performance_duty')->insert([
                'performance_content' => "3060/Green Passport 合作伙伴推进（".$monthwithoutzero."月）",
                'performance_no' => "D08".$monthwithzero, //$postContent["performance-no"], 
                'type' => "三类积分",
                'property' => "商务拓展",
                'difficulty' => "normal",
                'leader' => 2,
                'members' => "[\"9\",\"19\"]",
                'start_date' => Carbon::now()->startOfMonth()->format('Y-m-d'),
                'end_date' => Carbon::now()->endOfMonth()->format('Y-m-d'),
                'node_no' => 2,
                'basic_points' => 18,
                'latest_progress' => "",
                'declarant_id' => 1,
                'status' => "processing",
            ]);
    
            //Generate nodes
            DB::table('duty_node')->insert([
                'duty_performance_no' => "D08".$monthwithzero, //$postContent["performance-no"], 
                'node_id' => 1,
                'node_date' => $fridays[1],
                'node_point_percentage' => 50,
                'node_goal' => "二周一次考核",
            ]);
            
            DB::table('duty_node')->insert([
                'duty_performance_no' => "D08".$monthwithzero, //$postContent["performance-no"], 
                'node_id' => 2,
                'node_date' => $fridays[3],
                'node_point_percentage' => 50,
                'node_goal' => "二周一次考核",
            ]);

    
            //Create Announcement for the duty
            $announcementContent = '【管理員】 创建了业绩事项 【D08'.$monthwithzero.'】，请主管领导尽快审批';
            DB::table('announcements')->insert([
                'name' => "管理員",
                'content' =>  $announcementContent,
                'is_important' => 1,
            ]);
//-------------end of 3060/Green Passport合作伙伴推进-------------//
//-------------------牌照公司基金产品发行-------------------------//
            DB::table('performance_duty')->insert([
                'performance_content' => "牌照公司基金产品发行（".$monthwithoutzero."月）",
                'performance_no' => "D09".$monthwithzero, //$postContent["performance-no"], 
                'type' => "三类积分",
                'property' => "销售",
                'difficulty' => "normal",
                'leader' => 6,
                "second_leader" => 12,
                'members' => "[\"5\",\"11\"]",
                'start_date' => Carbon::now()->startOfMonth()->format('Y-m-d'),
                'end_date' => Carbon::now()->endOfMonth()->format('Y-m-d'),
                'node_no' => 4,
                'basic_points' => 18,
                'latest_progress' => "",
                'declarant_id' => 1,
                'status' => "processing",
            ]);
    
            //Generate nodes
            for ($i=1; $i<= 4; $i++){
                DB::table('duty_node')->insert([
                    'duty_performance_no' => "D09".$monthwithzero, //$postContent["performance-no"], 
                    'node_id' => $i,
                    'node_date' => $fridays[$i-1],
                    'node_point_percentage' => 25,
                    'node_goal' => "每周考核进度",
                ]);
            }

    
            //Create Announcement for the duty
            $announcementContent = '【管理員】 创建了业绩事项 【D09'.$monthwithzero.'】，请主管领导尽快审批';
            DB::table('announcements')->insert([
                'name' => "管理員",
                'content' =>  $announcementContent,
                'is_important' => 1,
            ]);
//--------------------end of 牌照公司基金产品发行------------------//
//--------------绿色金融联盟App前台会员反馈优化----------------//
            DB::table('performance_duty')->insert([
                'performance_content' => "绿色金融联盟App前台会员反馈优化（".$monthwithoutzero."月）",
                'performance_no' => "D13".$monthwithzero, //$postContent["performance-no"], 
                'type' => "三类积分",
                'property' => "商务拓展",
                'difficulty' => "normal",
                'leader' => 11,
                'members' => "[\"5\"]",
                'start_date' => Carbon::now()->startOfMonth()->format('Y-m-d'),
                'end_date' => Carbon::now()->endOfMonth()->format('Y-m-d'),
                'node_no' => 4,
                'basic_points' => 18,
                'latest_progress' => "",
                'declarant_id' => 1,
                'status' => "postponed",
            ]);
    
            //Generate nodes
            DB::table('duty_node')->insert([
                'duty_performance_no' => "D13".$monthwithzero, //$postContent["performance-no"], 
                'node_id' => 1,
                'node_date' => $fridays[0],
                'node_point_percentage' => 10,
                'node_goal' => "收集会员反馈",
            ]);
            
            DB::table('duty_node')->insert([
                'duty_performance_no' => "D13".$monthwithzero, //$postContent["performance-no"], 
                'node_id' => 2,
                'node_date' => $fridays[1],
                'node_point_percentage' => 20,
                'node_goal' => "与技术人员沟通如何实现反馈和优化App",
            ]);
            
            DB::table('duty_node')->insert([
                'duty_performance_no' => "D13".$monthwithzero, //$postContent["performance-no"], 
                'node_id' => 3,
                'node_date' => $fridays[2],
                'node_point_percentage' => 30,
                'node_goal' => "优化APP",
            ]);
            
            DB::table('duty_node')->insert([
                'duty_performance_no' => "D13".$monthwithzero, //$postContent["performance-no"], 
                'node_id' => 4,
                'node_date' => $fridays[3],
                'node_point_percentage' => 40,
                'node_goal' => "配置到GOOGLE",
            ]);

    
            //Create Announcement for the duty
            $announcementContent = '【管理員】 创建了业绩事项 【D13'.$monthwithzero.'】，请主管领导尽快审批';
            DB::table('announcements')->insert([
                'name' => "管理員",
                'content' =>  $announcementContent,
                'is_important' => 1,
            ]);
//--------------------end of 绿色金融联盟App前台会员反馈优化------------------//
//-------------------每周文案編寫-----------------------//
            DB::table('performance_duty')->insert([
                'performance_content' => "每周文案編寫（".$monthwithoutzero."月）",
                'performance_no' => "D14".$monthwithzero, //$postContent["performance-no"], 
                'type' => "四类积分",
                'property' => "文件编写",
                'difficulty' => "difficult",
                'leader' => 17,
                'members' => "[\"9\"]",
                'start_date' => Carbon::now()->startOfMonth()->format('Y-m-d'),
                'end_date' => Carbon::now()->endOfMonth()->format('Y-m-d'),
                'node_no' => 4,
                'basic_points' => 21.6,
                'latest_progress' => "",
                'declarant_id' => 1,
                'status' => "processing",
            ]);
    
            //Generate nodes
            for ($i=1; $i<= 4; $i++){
                DB::table('duty_node')->insert([
                    'duty_performance_no' => "D14".$monthwithzero, //$postContent["performance-no"], 
                    'node_id' => $i,
                    'node_date' => $fridays[$i-1],
                    'node_point_percentage' => 25,
                    'node_goal' => "每周編寫兩篇不同的文案",
                ]);
            }

    
            //Create Announcement for the duty
            $announcementContent = '【管理員】 创建了业绩事项 【D14'.$monthwithzero.'】，请主管领导尽快审批';
            DB::table('announcements')->insert([
                'name' => "管理員",
                'content' =>  $announcementContent,
                'is_important' => 1,
            ]);
//------------------------end of 每周文案編寫------------------------//
//-------区块链溯源方面的相关政府政策搜集，更新，支持业务推进--------//
            DB::table('performance_duty')->insert([
                'performance_content' => "区块链溯源方面的相关政府政策搜集，更新，支持业务推进（".$monthwithoutzero."月）",
                'performance_no' => "D15".$monthwithzero, //$postContent["performance-no"], 
                'type' => "四类积分",
                'property' => "行业研究",
                'difficulty' => "normal",
                'leader' => 17,
                'members' => "",
                'start_date' => Carbon::now()->startOfMonth()->format('Y-m-d'),
                'end_date' => Carbon::now()->endOfMonth()->format('Y-m-d'),
                'node_no' => 2,
                'basic_points' => 14.4,
                'latest_progress' => "",
                'declarant_id' => 1,
                'status' => "processing",
            ]);

            //Generate nodes
            DB::table('duty_node')->insert([
                'duty_performance_no' => "D15".$monthwithzero, //$postContent["performance-no"], 
                'node_id' => 1,
                'node_date' => $fridays[1],
                'node_point_percentage' => 50,
                'node_goal' => "将收集来的政府政策相关汇总进行汇报一次，更新PPT、话术、Q&A",
            ]);
            
            DB::table('duty_node')->insert([
                'duty_performance_no' => "D15".$monthwithzero, //$postContent["performance-no"], 
                'node_id' => 2,
                'node_date' => $fridays[3],
                'node_point_percentage' => 50,
                'node_goal' => "将收集来的政府政策相关汇总进行汇报一次，更新PPT、话术、Q&A",
            ]);

            //Create Announcement for the duty
            $announcementContent = '【管理員】 创建了业绩事项 【D15'.$monthwithzero.'】，请主管领导尽快审批';
            DB::table('announcements')->insert([
                'name' => "管理員",
                'content' =>  $announcementContent,
                'is_important' => 1,
            ]);
//-------end of 区块链溯源方面的相关政府政策搜集，更新，支持业务推进----------//
//-------------溯源系统客户拜访-----------------//
            DB::table('performance_duty')->insert([
                'performance_content' => "溯源系统客户拜访（".$monthwithoutzero."月）",
                'performance_no' => "D16".$monthwithzero, //$postContent["performance-no"], 
                'type' => "三类积分",
                'property' => "商务拓展",
                'difficulty' => "normal",
                'leader' => 18,
                'members' => "",
                'start_date' => Carbon::now()->startOfMonth()->format('Y-m-d'),
                'end_date' => Carbon::now()->endOfMonth()->format('Y-m-d'),
                'node_no' => 4,
                'basic_points' => 18,
                'latest_progress' => "",
                'declarant_id' => 1,
                'status' => "processing",
            ]);
    
            //Generate nodes
            for ($i=1; $i<= 4; $i++){
                DB::table('duty_node')->insert([
                    'duty_performance_no' => "D16".$monthwithzero, //$postContent["performance-no"], 
                    'node_id' => $i,
                    'node_date' => $fridays[$i-1],
                    'node_point_percentage' => 25,
                    'node_goal' => "每周汇报本周拜访情况",
                ]);
            }

    
            //Create Announcement for the duty
            $announcementContent = '【管理員】 创建了业绩事项 【D16'.$monthwithzero.'】，请主管领导尽快审批';
            DB::table('announcements')->insert([
                'name' => "管理員",
                'content' =>  $announcementContent,
                'is_important' => 1,
            ]);
//--------------end of 溯源系统客户拜访--------------//
        })->monthly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
