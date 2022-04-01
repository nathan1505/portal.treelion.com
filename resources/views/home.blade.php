@extends('layouts.main')

@section('css')
    <link href="/css/main.css" rel="stylesheet">
@endsection

@section('script')
    <script src="{{ URL::asset('js/main.js') }}"></script>
@endsection

@section('content')
    <div class="container-fluid">
            <div class="container-fluid" style="margin-top: 1%">
                
                @if (session('status'))
                    <div class="row" style="margin-top: 30px">
                        <div class="col-md-4">
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        </div>
                    </div>
                @elseif (session('danger'))
                    <div class="row" style="margin-top: 30px">
                        <div class="col-md-4">
                            <div class="alert alert-danger">
                                {{ session('danger') }}
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <!--block2 Annoucements-->
                    <div class="col-md col-lg col-xl">
                        <div class="card" style="height:100%">
                            <div class="card-header">
                                <h5 class="card-text" style="display: inline-block; height: 15px">公告</h5>
                                <img src="images/announcement.png" style="display: inline-block;vertical-align: middle;width:15px"></img>
                                    <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#demo" style="float: right;">↑／↓</button>
                            </div>
                            <div id="demo" class="collapse">
                                <div class="card-body out-div" id="announcement-body">
                                    <table class="table">
                                        <tbody id="announcement-table">
                                        </tbody> 
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                <form action="post-announcement" method="POST">
                                    @csrf
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" required placeholder="发送200字以内公告" name="announcement">
                                        <div class="input-group-append">
                                            <button class="btn btn-success" type="submit">发送</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!--block2 ends-->
                    <!--block3-->
                    <!--
                    <div class="col-md-3 col-lg-4 col-xl-4">
                        <div class="card" style="height:100%" id="card-3">
                            <div class="card-body out-div">
                                <table class="table" id="news-table">
                                    <tbody>
                                    </tbody> 
                                </table>
                            </div>
                        </div>
                    </div>
                    -->
                </div>

                <!--row2-->
                <div class="row" style="margin-top:20px">
                    <!--block3-->
                    <div class="col">
                        <div class="card" id="performance-card">
                            <div class="card-header">
                                <div>
                                    <h5 style="display:inline-block;float:left;">业绩事项</h5>
                                </div>
                                <a class="btn btn-success" type="button" href='{{url('/performance/register')}}' style="float:left;margin-left:15px">创建事项</a>
                                <a class="btn btn-success" type="button" href='{{url('/duties')}}' style="float:left;margin-left:15px">查看积分总表</a>
                                <a class="btn btn-warning" type="button" href='{{url('/performance/profit')}}' style="float:left;margin-left:15px">申报获利</a>
                                <select id='performance-status' name='performance-status' class="form-control" style="float:left;width:20%;margin-left:15px">
                                    <option value="">--选择状态--</option>
                                    <option value="pending">待审批</option>
                                    <option value="processing">进行中</option>
                                    <option value="done">完成</option>
                                    <option value="delayed">延迟</option>
                                    <option value="rejected">未通过</option>
                                    <option value="postponed">暂缓</option>
                                </select>
                                <select id='performance-property' name='performance-property' type="input" class="form-control" style="float:left;width:20%;margin-left:15px">
                                    <option value="">--选择类别属性--</option>
                                    <option value="宣发">宣发</option>
                                    <option value="销售">销售</option>
                                    <option value="商务拓展">商务拓展</option>
                                    <option value="融资">融资</option>
                                    <option value="行业研究">行业研究</option>
                                    <option value="证件申请">证件申请</option>
                                    <option value="商标注册">商标注册</option>
                                    <option value="中介服务">中介服务</option>
                                    <option value="技术开发">技术开发</option>
                                    <option value="技术测试">技术测试</option>
                                    <option value="文件编写">文件编写</option>
                                    <option value="参会">参会</option>
                                    <option value="政企合作">政企合作</option>
                                    <option value="团队搭建">团队搭建</option>
                                    <option value="资质申请">资质申请</option>
                                    <option value="管理创新">管理创新</option>
                                    <option value="商业创新">商业创新</option>
                                </select>
                            </div>
                            <div class="card-body performance-div">
                                <table>
                                <tr>
                                    <td style="width:7%">编号</td>
                                    <td style="width:20%">项目内容</td>
                                    <td style="width:11%">项目类别</td>
                                    <td style="width:12%">开始日期</td>
                                    <td style="width:11%">项目状态</td>
                                    <td style="width:35%">积分</td>
                                </tr>
                                </table>
                                <table class="table table-striped">
                                    @if ((Auth::user()->role != "employee"))
                                        <tbody id="performance-table">
                                        </tbody>
                                    @else
                                        <tbody id="performance-table-employee">
                                        </tbody>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                    <!--block 4-->
                    <!--
                    <div class="col-4">
                        <div class="card" style="height:100%" id="daily-post">
                            <div class="card-header">
                                <div>
                                    <h5 style="display:inline-block;float:left;">个人信息</h5>
                                </div>
                            </div>
                            <div class="card-body">
                            </div>
                        </div>
                    </div>
                    -->
                </div>

            </div>
        </div>
    </div>

@stop