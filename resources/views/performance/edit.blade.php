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

    <form action="/performance/edit" method="post">
        @csrf
        <div class="form-group">
            <div style="margin-top: 20px">
                <label for="declarant">申报人</label>
                <input id="declarant" type="input" class="form-control" readonly placeholder="{{$id_name[0]['name']}}">
                <input style="visibility: hidden" name="member" type="input" value="{{Auth::user()->id}}">
                <input style="visibility: hidden" name="duty_id" type="input" value="{{$performancedata[0]['id']}}">
                <input style="visibility: hidden" name="performance_no" type="input" value="{{$performancedata[0]['performance_no']}}">
            </div>

            <div class="margin-top: 20px">
                <label for="performance-no">编号</label>
                <input id="performance-no" name="performance-no" type="input" class="form-control" value="{{$performancedata[0]['performance_no']}}">
            </div>

            <div style="margin-top: 20px">
                <label for="content">项目内容</label>
                <textarea id="content" name="content" type="input" class="form-control" rows="4" required>{{$performancedata[0]['performance_content']}}</textarea>
            </div>

            <div class="row">
                <div class="col" style="margin-top: 20px">
                    <label for="type">项目类别</label>
                    <select id="type" name="type" type="input" class="form-control">
                        <option value="一类积分" <?php if($performancedata[0]['type'] =="一类积分") echo 'selected="selected"'; ?>>一类积分</option>
                        <option value="二类积分" <?php if($performancedata[0]['type'] =="二类积分") echo 'selected="selected"'; ?>>二类积分</option>
                        <option value="三类积分" <?php if($performancedata[0]['type'] =="三类积分") echo 'selected="selected"'; ?>>三类积分</option>
                        <option value="四类积分" <?php if($performancedata[0]['type'] =="四类积分") echo 'selected="selected"'; ?>>四类积分</option>
                    </select>
                </div>

                <div class="col" style="margin-top: 20px">
                    <label for="property">类别属性</label>
                    <select id="property" name="property" type="input" class="form-control">
                        <option value="宣发" <?php if($performancedata[0]['property'] =="宣发") echo 'selected="selected"'; ?>>宣发</option>
                        <option value="销售" <?php if($performancedata[0]['property'] =="销售") echo 'selected="selected"'; ?>>销售</option>
                        <option value="商务拓展" <?php if($performancedata[0]['property'] =="商务拓展") echo 'selected="selected"'; ?>>商务拓展</option>
                        <option value="融资" <?php if($performancedata[0]['property'] =="融资") echo 'selected="selected"'; ?>>融资</option>
                        <option value="行业研究" <?php if($performancedata[0]['property'] =="行业研究") echo 'selected="selected"'; ?>>行业研究</option>
                        <option value="证件申请" <?php if($performancedata[0]['property'] =="证件申请") echo 'selected="selected"'; ?>>证件申请</option>
                        <option value="商标注册" <?php if($performancedata[0]['property'] =="商标注册") echo 'selected="selected"'; ?>>商标注册</option>
                        <option value="中介服务" <?php if($performancedata[0]['property'] =="中介服务") echo 'selected="selected"'; ?>>中介服务</option>
                        <option value="技术开发" <?php if($performancedata[0]['property'] =="技术开发") echo 'selected="selected"'; ?>>技术开发</option>
                        <option value="技术测试" <?php if($performancedata[0]['property'] =="技术测试") echo 'selected="selected"'; ?>>技术测试</option>
                        <option value="文件编写" <?php if($performancedata[0]['property'] =="文件编写") echo 'selected="selected"'; ?>>文件编写</option>
                        <option value="参会" <?php if($performancedata[0]['property'] =="参会") echo 'selected="selected"'; ?>>参会</option>
                        <option value="政企合作" <?php if($performancedata[0]['property'] =="政企合作") echo 'selected="selected"'; ?>>政企合作</option>
                        <option value="团队搭建" <?php if($performancedata[0]['property'] =="团队搭建") echo 'selected="selected"'; ?>>团队搭建</option>
                        <option value="资质申请" <?php if($performancedata[0]['property'] =="资质申请") echo 'selected="selected"'; ?>>资质申请</option>
                        <option value="管理创新" <?php if($performancedata[0]['property'] =="管理创新") echo 'selected="selected"'; ?>>管理创新</option>
                        <option value="商业创新" <?php if($performancedata[0]['property'] =="商业创新") echo 'selected="selected"'; ?>>商业创新</option>
                        <option value="設計" <?php if($performancedata[0]['property'] =="設計") echo 'selected="selected"'; ?>>設計</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col" style="margin-top: 20px">
                    <label>难度</label><br/>
                    <label class="radio-inline">
                        <input type="radio" class="form-control" name="difficulty" id="inlineradio1" value="easy" required
                        <?php echo ($performancedata[0]['difficulty'] == "easy" ? 'checked="checked"': ''); ?>>&nbsp&nbsp简单&nbsp&nbsp
                    </label>
                    <label class="radio-inline">
                        <input type="radio" class="form-control" name="difficulty" id="inlineradio2" value="normal"
                        <?php echo ($performancedata[0]['difficulty'] == "normal" ? 'checked="checked"': ''); ?>>&nbsp&nbsp普通&nbsp&nbsp
                    </label>
                    <label class="radio-inline">
                        <input type="radio" class="form-control" name="difficulty" id="inlineradio3" value="difficult"
                        <?php echo ($performancedata[0]['difficulty'] == "difficult" ? 'checked="checked"': ''); ?>>&nbsp&nbsp困难&nbsp&nbsp
                    </label>
                </div>

                <div class="col" style="margin-top: 20px">
                    <label for="node-no">节点数</label><br/>
                    <input type="number" class="form-control" name="node-no" id="node-no" min=1 max=4 step=1 value="{{$performancedata[0]['node_no']}}" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4" style="margin-top: 20px">
                    <label for="leader-edit">组长</label>
                    <select id="leader-edit" name="leader" type="input" class="form-control" required>
                        <?php
                        $selected = $performancedata[0]['leader']; // Put value from database here.

                        foreach ($user as $key) {
                        ?>
                            <option value="{{$key['id']}}" <?php
                            if ($key['id'] == $selected) {
                                echo 'selected="selected"';
                            }
                            ?> > {{$key['name']}}</option>
                        <?php
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-8" style="margin-top: 20px">
                    <label for="members-edit">组员</label>
                    <select id="members-edit" name="members[]" type="checkbox" class="form-control selectpicker" data-live-search="true" multiple data-style="btn-light">
                        <?php
                        $selected = json_decode($performancedata[0]['members']);
                        foreach ($user as $key) {
                        ?>
                            <option value="{{$key['id']}}" <?php
                            //var_dump($selected);
                            $int2str = strval($key['id']);
                            if($selected == null){
                                echo '';
                            }else if (in_array($int2str, $selected)) {
                                echo 'selected="selected"';
                            }
                            ?>>{{$key['name']}}</option>
                        }
                        <?php
                        }
                        ?>
                    </select>
                </div>
            </div>
            
            @if ((Auth::user()->role != "employee"))
            <div class="row">
                <div class="col-md-4" style="margin-top: 20px">
                    <label for="leader2-edit">第二组长</label>
                    <select id="leader2-edit" name="leader2" type="input" class="form-control">
                        <?php
                        $selected = $performancedata[0]['second_leader']; // Put value from database here.

                        foreach ($user as $key) {
                        ?>
                            <?php
                            if ($key['id'] == $selected) {
                                echo '<option value="'.$key['id'].'" selected="selected">'.$key['name'].'</option>';
                            }else if ($key['id'] == ''){
                                echo '<option></option>';
                            }
                            ?>
                        <?php
                        }
                        ?>
                    </select>
                </div>
            </div>
            @endif

            <div class="row" id="date-range" style="margin-top: 20px">
                <div class="col">
                    <label for="start-date">开始考核日期</label><br/>
                    <input type="date" name="start-date" id="startdate" class="form-control startdate" onblur="findEndDateMin()" value="{{$performancedata[0]['start_date']}}" required>
                </div>
                <div class="col">
                    <label for="end-date">结束考核日期</label><br/>
                    <input type="date" name="end-date" id="enddate" class="form-control enddate" value="{{$performancedata[0]['end_date']}}" required>
                </div>
                <script>
                    var minToDate;
                    //$("#enddate").attr("disabled", "true");

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