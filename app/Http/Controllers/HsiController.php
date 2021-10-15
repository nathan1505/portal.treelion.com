<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;

class HsiController extends Controller
{
    public function GetHsi(){
        $response = DB::table('hsi')->orderBy('timestamp','desc')->first();
        $array = json_decode(json_encode($response), true);
        return $array;
    }

    public function GetElion(){
        $response = DB::table('elion_stock')->orderBy('timestamp','desc')->first();
        $array = json_decode(json_encode($response), true);
        return $array;
    }

    public function GetNews(){
        $response = DB::table('news')->orderBy('updateTime','desc')->get();
        $array = json_decode(json_encode($response), true);
        return $array;
    }
}
