@extends('layouts.main')

@section('css')
    <link href="/css/main.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css" rel="stylesheet" />
@endsection

@section('script')
    <script src="{{ URL::asset('/js/performance.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/i18n/defaults-zh_CN.min.js"></script>
@endsection

@section('content')
<div class="container" style="margin-top:5%">
    <h2>业绩事项申报</h2>

    <form action="post-duty"method="post">
        @csrf
        <div class="form-group">
            <div style="margin-top: 20px">
                <label for="declarant">申报人</label>
                <input id="declarant" type="input" class="form-control" readonly placeholder="{{Auth::user()->name}}">
                <input style="visibility: hidden" name="declarant-id" type="input" value="{{Auth::user()->id}}">
            </div>

            <div class="margin-top: 20px">
                <label for="performance-no">编号</label>
                <input id="performance-no" name="performance-no" type="input" class="form-control" required>
            </div>

            <div style="margin-top: 20px">
                <label for="content">项目主题</label>
                <textarea id="content" name="content" type="input" class="form-control" rows="4" required></textarea>
            </div>

            <div class="row">
                <div class="col" style="margin-top: 20px">
                    <label for="type">项目类别</label>
                    <select id="type" name="type" type="input" class="form-control">
                        <option>一类积分</option>
                        <option>二类积分</option>
                        <option>三类积分</option>
                        <option>四类积分</option>
                    </select>
                </div>

                <div class="col" style="margin-top: 20px">
                    <label for="property">类别属性</label>
                    <select id="property" name="property" type="input" class="form-control">
                        <option>宣发</option>
                        <option>销售</option>
                        <option>商务拓展</option>
                        <option>融资</option>
                        <option>行业研究</option>
                        <option>证件申请</option>
                        <option>商标注册</option>
                        <option>中介服务</option>
                        <option>技术开发</option>
                        <option>技术测试</option>
                        <option>文件编写</option>
                        <option>参会</option>
                        <option>政企合作</option>
                        <option>团队搭建</option>
                        <option>资质申请</option>
                        <option>管理创新</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col" style="margin-top: 20px">
                    <label>难度</label><br/>
                    <label class="radio-inline">
                        <input type="radio" class="form-control" name="difficulty" id="inlineradio1" value="easy" required>&nbsp&nbsp简单&nbsp&nbsp
                    </label>
                    <label class="radio-inline">
                        <input type="radio" class="form-control" name="difficulty" id="inlineradio2" value="normal">&nbsp&nbsp普通&nbsp&nbsp
                    </label>
                    <label class="radio-inline">
                        <input type="radio" class="form-control" name="difficulty" id="inlineradio3" value="difficult">&nbsp&nbsp困难&nbsp&nbsp
                    </label>
                </div>

                <div class="col" style="margin-top: 20px">
                    <label for="node-no">节点数</label><br/>
                    <input type="number" class="form-control" name="node-no" id="node-no" min=1 max=4 step=1 required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4" style="margin-top: 20px">
                    <label for="leader">组长</label>
                    <select id="leader" name="leader" type="input" class="form-control" required>
                    </select>
                </div>

                <div class="col-md-8" style="margin-top: 20px">
                    <label for="members">组员</label>
                    <select id="members" name="members[]" type="checkbox" class="form-control selectpicker" data-live-search="true" multiple data-style="btn-light">
                    </select>
                </div>
            </div>

            @if ((Auth::user()->role != "employee"))
            <div class="row">
                <div class="col-md-4" style="margin-top: 20px">
                    <label for="leader2">第二组长</label>
                    <select id="leader2" name="leader2" type="input" class="form-control">
                    </select>
                </div>
            </div>
            @else
            <div class="row" style="visibility: hidden">
                <div class="col-md-4" style="margin-top: 20px">
                    <label for="leader2">第二组长</label>
                    <select id="leader2" name="leader2" type="input" class="form-control">
                    </select>
                </div>
            </div>
            @endif

            <div class="row" id="date-range" style="margin-top: 20px">
                <div class="col">
                    <label for="start-date">开始考核日期</label><br/>
                    <input type="date" name="start-date" id="startdate" class="form-control startdate" onblur="findEndDateMin()" required>
                </div>
                <div class="col">
                    <label for="end-date">结束考核日期</label><br/>
                    <input type="date" name="end-date" id="enddate" class="form-control enddate" required>
                </div>
                <script>
                    var minToDate;
                    $("#enddate").attr("disabled", "true");

                    function findEndDateMin() {
                      minToDate = document.getElementById("startdate").value;
                      document.getElementById("enddate").setAttribute("min", minToDate);
                    }

                    $(".startdate").change(function(){
                        if ($("#startdate").val() != "") {
                            $("#enddate").removeAttr("disabled");
                        } else {
                            $("#enddate").attr("disabled", "true");        
                        }
                    });
                </script>
            </div>

            <div class="row" id="nodes-row" style="margin-top: 20px">
            </div>

            <div style="margin-top: 30px; text-align:right" id="button-div">
            </div>
        </div>
    </form>
</div>
@endsection