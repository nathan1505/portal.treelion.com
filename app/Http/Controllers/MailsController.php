<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;
use App\Mail\TestMail;

class MailsController extends Controller
{
    public function TestMail(){
        Mail::raw('你好，我是PHP程序！', function ($message) {
            $to = 'xschen@treelion.com';
            $message ->to($to)->subject('纯文本信息邮件测试');
        });
    }
}
