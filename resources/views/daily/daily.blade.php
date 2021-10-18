@extends('layouts.main')

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5/main.min.css" />
    <link href='https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.13.1/css/all.css' rel='stylesheet' />
    <style type="text/css">
        td,th{
            text-align:center;/** 设置水平方向居中 */
            vertical-align:middle/** 设置垂直方向居中 */
        }
    </style>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5/locales-all.min.js"></script>
    <script>
        var userId = {{$userId}};
    </script>
    <script src="{{ URL::asset('/js/daily.js') }}"></script>
@endsection

@section('content')
    <div class="container-fluid" style="margin-top:2%; align-content: center">

        <!--Row 1-->
        <div class="row">
            <div class="col">
                <div class="card" style="height:100%">
                    <div class="card-header">
                        员工：{{Auth::user()->name}}
                    </div>
                    <div class="card-body performance-div">
                        <table class="table table-striped">
                            <tbody id="basic-duties-table">
                            </tbody> 
                        </table>
                    </div>
                    <div style="position: absolute; bottom:20px; left: 20px">
                        <a class="btn btn-success" type="button" href="/daily-register">早间申报</a>
                        <a class="btn btn-success" type="button" href='/daily/update/{{Auth::user()->id}}/{{date("Y-m-d")}}'>晚间申报</a>
                        <a class="btn btn-success" type="button" href='{{url('/duties')}}'>查看积分总表</a>
                        <a class="btn btn-success" type="button" href="/basic/register">基础项目申报</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card">
                        <div class="card-body">
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--Row 1 ends-->
        <!--Row 2-->
        <div class="row" style="margin-top:20px; margin-bottom:30px">
            <!--Daily Records-->
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        日内工作申报记录
                    </div>
                    <div class="card-body">
                        <div id="daily-report-table" style="margin-top:30px">
                            <table class="table">
                                <tr>
                                    <th style="">申报人</th>
                                    <th style="width:20%">申报日期</th>
                                    <th style="width:20%">申报时间</th>
                                    <th style="width:15%">查看</th>
                                    <th style="width:15%">图片下载</th>
                                    <th style="width:15%">PDF下载</th>
                                </tr>
                                <tbody id="daily-report-tbody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!--End of daily records-->
            <!--Performance Duty-->
            <div class="col">
                <div class="card" style="height:100%">
                    <div class="card-header">
                        我的业绩工作
                        <a class="btn btn-success" type="button" href='{{url('/performance/register')}}' style="margin-left:15px">创建事项</a>
                    </div>
                    <div class="card-body" style="font-size: 10px">
                        <table class="table table-responsive">
                            <tbody id="duty-table-body">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>

@endsection