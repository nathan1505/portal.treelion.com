@extends('layouts.main')

@section('css')
    <link href="/css/main.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css" rel="stylesheet" />
@endsection

@section('script')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/i18n/defaults-zh_CN.min.js"></script>
    <script src="{{ URL::asset('js/main.js') }}"></script>
@endsection

@section('content')
<div class="container-fluid">
    <div class="container-fluid" style="margin-top: 1%">
        <div class="row">
            <!--block1-->
            <div class="col-xl">
                <div class="card" style="height:100%" id="card-1">
                    <div class="card-body" id="block1">
                        <!--
                        <h5 class="card-text" id="currentDate"></h5>
                        <h5 class="card-text" id="currentTime"></h5>
                        -->
                        <div class="card-text" id="weather" style="margin-top:10px"></div>
                        <div style="bottom:20px">
                            <a href={{url('/daily-register')}}><button class="btn btn-primary" style="margin-top: 5px">早间工作申报</button></a>
                            <a href="https://exmail.qq.com/login"><button class="btn btn-info" style="margin-top: 5px">工作邮箱</button></a>
                            <button class="btn btn-warning" style="margin-top: 5px">报销申请</button>
                            <a href={{url('/pdf_contact_list')}}><button class="btn btn-success" style="margin-top: 5px" target="_blank">公司通讯录</button></a>
                            <a href={{url('/pdf_dayoff_application')}}><button class="btn btn-danger" style="margin-top: 5px" target="_blank">请假申请</button></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection