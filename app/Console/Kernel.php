<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
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

                DB::table('performance_duty')
                    ->where('id', $duty['id'])
                    ->update([
                        'next_date' => $nextAssessingDay,
                        'next_goal' => $nodeArray[$next_id]['node_goal'],
                        'next_percentage' => $nodeArray[$next_id]['node_point_percentage'],
                    ]);
            }
        })->daily();

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
