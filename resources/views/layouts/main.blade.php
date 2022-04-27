<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/images/favicon.ico" rel="icon" type="image/x-ico">
    <link rel="stylesheet" href="https://cdn.staticfile.org/twitter-bootstrap/4.3.1/css/bootstrap.min.css">
    <link href="/css/header.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+SC:wght@400&display=swap" rel="stylesheet">
    <style>
        body{
            font-family: 'Noto Sans SC', sans-serif;
        }
    </style>
    @yield('css')
    <script src="https://cdn.jsdelivr.net/npm/jquery@3/dist/jquery.min.js"></script>
    <script src="https://cdn.staticfile.org/popper.js/1.15.0/umd/popper.min.js"></script>
    <script src="https://cdn.staticfile.org/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
    @yield('script')
    <title>亿利香港公司门户网站</title>
</head>
<body>
    
    <nav class="navbar navbar-expand-sm bg-light navbar-light" id="navbar" >
        <ul class="navbar-nav">
            @if (Route::has('login'))
                @auth
                <!--div class="topnav-right"-->
                    <li class="nav-item" style="position: relative; left: 130px">
                        <a class="nav-link" href="{{ url('/') }}">主页</a>
                    </li>
                    <li class="nav-item" style="position: relative; left: 130px">
                        <a class="nav-link" href="/daily/{{Auth::user()->id}}">工作跟踪</a>
                    </li>
                    <li class="nav-item" style="position: relative; left: 130px">
                        <a class="nav-link" href="/points/{{Auth::user()->id}}">每月积分</a>
                    </li>
                    <li class="nav-item" style="position: relative; left: 130px">
                        <a class="nav-link" href="/weekly/detail">每周统计</a>
                    </li>
                    <li class="nav-item" style="position: relative; left: 130px">
                        <a class="nav-link" href="/monthly/detail">每月统计</a>
                    </li>
                    @if ((Auth::user()->role != "employee"))
                    <li class="nav-item" style="position: relative; left: 130px">
                        <a class="nav-link" href="/attendance/detail">考勤分数</a>
                    </li>
                    @endif
                    <li class="nav-item" style="position: relative; left: 130px">
                        <a class="nav-link" href="/other">公司资料</a>
                    </li>
                <!--/div-->
                <!--div style="float: right;"-->
                    <li class="nav-item" style="position: absolute; right: 80px">
                        <span class="navbar-text">欢迎，{{Auth::user()->name}}！</span>
                    </li>
                    <li class="nav-item" style="position: absolute; right: 30px">
                        <a class="nav-link" href="{{ url('/logout') }}">登出</a>
                    </li>
                <!--/div-->
                @else
                    <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">登入</a>
                    </li>

                    @if (Route::has('register'))
                        <li>
                        <a class="nav-link" href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 underline">Register</a>
                        </li>
                    @endif
                @endauth
            @endif
        </ul>
        <!--hide it
        <div class="card-text" id="trn" style="position: absolute; right:600px"></div>
        <div class="card-text" id="hsi" style="position: absolute; right:400px"></div>
        <div class="card-text" id="elion-stock" style="position: absolute; right:200px"></div>
        -->
        <img class="navbar-brand" src="/images/elion-logo.png" height="25px" style="position: absolute; left: 30px">
    </nav>


    <div class="container-fluid">
        
        @yield('content')

    </div>
    
</body>
</html>