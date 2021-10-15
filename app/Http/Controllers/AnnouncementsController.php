<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class AnnouncementsController extends Controller
{
    public function postannouncement(Request $request){
        $postContent = $request->input('announcement');
        if (null !== $request->input('is_important')){
            DB::table('announcements')->insert(
                [
                    'name' => Auth::user()["name"],
                    'content' => $postContent,
                    'is_important' => 1
                ]
            );
            return redirect('/');
        }else{
            DB::table('announcements')->insert(
                [
                    'name' => Auth::user()["name"],
                    'content' => $postContent,
                    'is_important' => 0
                ]
            );
            return redirect('/')->with('status', "您已成功发送公告!");
        }

    }

    public function GetAnnouncements(Request $request){
        $announcements = DB::table('announcements')->orderBy('timestamp','desc')->get();
        return $announcements;
    }
}
