<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    public function GetMembers(){
        $response = DB::table('users')->get();
        $array = json_decode(json_encode($response), true);
        return $array;
    }

    public function GetCategories(){
        $response = DB::table('categories')->get();
        $array = json_decode(json_encode($response), true);
        return $array;
    }

    public function GetManagers(){
        $response = DB::table('users')->where('role', 'manager')->get();
        $array = json_decode(json_encode($response), true);
        return $array;
    }
    
    public function GetMembersFiltrated(){
        $response = DB::table('users')->get();
        $array = json_decode(json_encode($response), true);
        $returnArray = array();
        foreach ($array as $single){
            if(!$single['is_disable']){
                array_push($returnArray, $single);
            }
        }
        return $returnArray;
    }
}
