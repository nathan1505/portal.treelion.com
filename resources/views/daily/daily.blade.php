@extends('layouts.main')

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5/main.min.css" />
    <link href='https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.13.1/css/all.css' rel='stylesheet' />
    <style type="text/css">
        .tableFixHead {
            overflow-y: auto;
            height: 500px;
        }
        .tableFixHead thead th {
            position: sticky;
            top: 0;
        }
        td,th{
            text-align:center;/** 设置水平方向居中 */
            vertical-align:middle;/** 设置垂直方向居中 */
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
                    <div class="tableFixHead">
                    <div class="card-body performance-div">
                        <table class="table table-striped">
                            <tbody id="basic-duties-table">
                            </tbody>
                        </table>
                    </div>
                    </div>
                    <div style="position: absolute; bottom:20px; left: 20px">
                        <!--
                        <a class="btn btn-success" type="button" href="/daily-register">早间申报</a>
                        <a class="btn btn-success" type="button" href='/daily/update/{{Auth::user()->id}}/{{date("Y-m-d")}}'>晚间申报</a>
                        <a class="btn btn-success" type="button" href='{{url('/duties')}}'>查看积分总表</a>
                        -->
                        <a class="btn btn-success" type="button" href="/basic/register">基础项目申报</a>
                    </div>
                </div>
            </div>
            <!--
            <div class="col">
                <div class="card">
                    <div class="card">
                        <div class="card-body">
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>
            </div>
            -->
        </div>
        <!--Row 1 ends-->
        <!--Row 2-->
        <div class="row" style="margin-top:20px; margin-bottom:30px">
            <!--Daily Records-->
            <!--
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
            -->
            <!--End of daily records-->
            <!--Performance Duty-->
            <div class="col">
                <div class="card" style="height:100%">
                    <div class="card-header">
                        我的业绩工作
                        <a class="btn btn-success" type="button" href='{{url('/performance/register')}}' style="margin-left:15px">创建事项</a>
                        <input type="month" id="yearmonth" name="yearmonth" value="">
                    </div>
                    <div class="card-body" style="font-size: 10px">
                        <table class="table table-responsive">
                            <tbody id="duty-table-head"></tbody>
                            <tbody id="duty-table-body"></tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>
    <script>
        let date= new Date()
        let month=("0" + (date.getMonth() + 1)).slice(-2)
        let year=date.getFullYear()
        document.getElementById("yearmonth").value = `${year}-${month}`;
        
        const yearmonth = document.getElementById("yearmonth").value;
        
        $(document).ready(function(){
            $("#yearmonth").on("input", function(){
                // Print entered value in a div box
                var yearmonth = document.getElementById("yearmonth").value;
                //console.log(yearmonth);
                 $.ajax({
                    url: '/get-performance-id/'+yearmonth,
                    type: "get",
                    //data: {'yearmonth':yearmonth},
                    success: function (response) {
                        $.ajax({
                            url: '/get-user',
                            type: "get",
                            success: function (userDetail) {
                                $('#duty-table-body').empty();
                                //console.log(response);
                                data = (Object.values(response));
                                data.sort((a, b) => {
                                    const statusOrder = ['rejected', 'pending', 'processing', 'delayed', 'done', 'postponed'];
                                    
                                    const aStatusIndex = statusOrder.indexOf( a.status );
                                    const bStatusIndex = statusOrder.indexOf( b.status );
                                
                                    if ( aStatusIndex === bStatusIndex )
                                        return ((a.start_date < b.start_date) ? 1 : -1);
                                
                                    return aStatusIndex - bStatusIndex;
                                });
                                //console.log(data);
                    
                                var color = "";
                                var status = "";
                    
                                for (var i = 0; i < data.length; i++) {
                    
                                    if (data[i].status == "processing") {
                                        color = "table-success";
                                        status = "进行中";
                                    } else if (data[i].status == "done") {
                                        color = "table-primary";
                                        status = "完成";
                                    } else if (data[i].status == "delayed") {
                                        color = "table-danger";
                                        status = "延迟";
                                    } else if (data[i].status == "postponed") {
                                        color = "table-secondary";
                                        status = "暂缓";
                                    } else if (data[i].status == "rejected") {
                                        color = "table-danger";
                                        status = "未通过";
                                    } else {
                                        color = "table-warning";
                                        status = "待审批";
                                    }
                                    
                                    var disableTrue = "";    
                                    if(userDetail.role != "admin" && (data[i].status != "pending" && (userDetail.id == data[i].leader || userDetail.id == data[i].declarant_id))){ //element.status == "pending"
                                        disableTrue = "disabled=\"true\"";
                                    };
                    
                                    $('#duty-table-body').append(
                                        '<tr>'+
                                        '<td style="width:10%;text-align:center;" class="' + color + '"><font size="2">' + status + '</font></td>' +
                                        '<td style="width:10%;text-align:center;" class="' + color + '"><font size="2">' + data[i].completeness + '%</font></td>' +
                                        '<td style="width:5%"><font size="2">' + data[i].performance_no + '</font></td>' +
                                        '<td style="width:30%"><font size="2">' + data[i].performance_content + '</font></td>' +
                                        '<td style="width:17%"><font size="2">' + data[i].start_date + '</font></td>' + 
                                        '<td><a href="/duties/' + data[i].id + '"><button class="btn btn-secondary btn-sm" style="float:right">查看</button></a>'+
                                        '<a href="/performance/edit/' + data[i].id + '"><button class="btn btn-success btn-sm" style="float:right"'+disableTrue+'>修改</button></a>' +
                                        '<a href="/performance/delete/' + data[i].id + '" onclick="return confirm(\'是否确定要删除项目？\')"><button class="btn btn-danger btn-sm" style="float:right"'+disableTrue+'>刪除</button></a>' +
                                        '<a href="/performance/edit-approval/' +data[i].id+'"<button class="btn btn-warning btn-sm" style="float:right">获利</button></a>' + 
                                        '</td>' +
                                        '<td><font color="#FF0000" size="2">' + data[i].notification + '</font></td>' +
                                        '</tr>'
                                    );
                                }
                            },
                            error: function(response){
                                alert('Error'+response);
                            },
                        });
                    },
                    error: function(response){
                        alert('Error'+response);
                    },
                });
            });
        });
    </script>

@endsection