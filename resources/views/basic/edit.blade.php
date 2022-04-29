@extends('layouts.main')

@section('css')
    <link href="/css/main.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css" rel="stylesheet" />
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/i18n/defaults-zh_CN.min.js"></script>
@endsection

@section('content')
<div class="container" style="margin-top:5%">
    <h2>基础项目申报</h2>

    <form action="/basic/edit" method="post">
        @csrf
        <div class="form-group">
            <div style="margin-top: 20px">
                <label for="declarant">申报人</label>
                <input id="declarant" type="input" class="form-control" readonly placeholder="{{$name[0]['name']}}">
                <input style="visibility: hidden" name="member" type="input" value="{{Auth::user()->id}}">
                <input style="visibility: hidden" name="duty_id" type="input" value="{{$data[0]['id']}}">
                <input style="visibility: hidden" name="basic_no" type="input" value="{{$data[0]['basic_no']}}">
            </div>
            
            <div class="margin-top: 20px">
                <label for="basic-no">编号</label>
                <input id="basic-no" name="basic-no" type="input" class="form-control" value="{{$data[0]['basic_no']}}" required>
            </div>

            <div style="margin-top: 20px">
                <label for="content">项目内容</label>
                <textarea id="content" name="basic-content" type="input" class="form-control" rows="4" required
                >{{$data[0]['basic_content']}}</textarea>
            </div>

            <div class="row">
                <div class="col" style="margin-top: 20px">
                    <label for="type">项目类别</label>
                    <select id="type" name="type" type="input" class="form-control">
                        <option value="五类积分（管理类）" <?php if($data[0]['type'] =="五类积分（管理类）") echo 'selected="selected"'; ?>>五类积分（管理类）</option>
                        <option value="六类积分（日常类）" <?php if($data[0]['type'] =="六类积分（日常类）") echo 'selected="selected"'; ?>>六类积分（日常类）</option>
                    </select>
                </div>
                
                <div class="col" style="margin-top: 20px">
                    <label>难度</label><br/>
                    <label class="radio-inline">
                        <input type="radio" class="form-control" name="difficulty" id="inlineradio1" value="easy"
                        <?php echo ($data[0]['difficulty'] == "easy" ? 'checked="checked"': ''); ?> required>&nbsp&nbsp简单&nbsp&nbsp
                    </label>
                    <label class="radio-inline">
                        <input type="radio" class="form-control" name="difficulty" id="inlineradio2" value="normal"
                        <?php echo ($data[0]['difficulty'] == "normal" ? 'checked="checked"': ''); ?>>&nbsp&nbsp普通&nbsp&nbsp
                    </label>
                    <label class="radio-inline">
                        <input type="radio" class="form-control" name="difficulty" id="inlineradio3" value="difficult"
                        <?php echo ($data[0]['difficulty'] == "difficult" ? 'checked="checked"': ''); ?>>&nbsp&nbsp困难&nbsp&nbsp
                    </label>
                </div>
            </div>
            
            <div class="row">
                <div class="col-5" style="margin-top: 20px">
                    <label for="status">项目类别</label>
                    @if (Auth::user()->role != "employee")
                    <select id="status" name="status" type="input" class="form-control">
                        <option value="approved" <?php if($data[0]['status'] =="approved") echo 'selected="selected"'; ?>>通过</option>
                        <option value="pending" <?php if($data[0]['status'] =="pending") echo 'selected="selected"'; ?>>待审批</option>
                        <option value="rejected" <?php if($data[0]['status'] =="rejected") echo 'selected="selected"'; ?>>未通过</option>
                        <option value="delete" <?php if($data[0]['status'] =="end") echo 'selected="selected"'; ?>>结束</option>
                    </select>
                    @else
                    <select id="status" name="status" type="input" class="form-control" readonly>
                        <option value="approved" <?php if($data[0]['status'] =="approved") echo 'selected="selected"'; ?>>通过</option>
                        <option value="pending" <?php if($data[0]['status'] =="pending") echo 'selected="selected"'; ?>>待审批</option>
                        <option value="rejected" <?php if($data[0]['status'] =="rejected") echo 'selected="selected"'; ?>>未通过</option>
                        <option value="delete" <?php if($data[0]['status'] =="end") echo 'selected="selected"'; ?>>结束</option>
                    </select>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col" style="margin-top: 20px; text-align:right">
                    <button type="submit" class="btn btn-success">提交</button>
                </div>
            </div>

            </div>
        </div>
    </form>
</div>
@endsection