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

    <form action="post-duty"method="post">
        @csrf
        <div class="form-group">
            <div style="margin-top: 20px">
                <label for="declarant">申报人</label>
                <input id="declarant" type="input" class="form-control" readonly placeholder="{{Auth::user()->name}}">
                <input style="visibility: hidden" name="member" type="input" value="{{Auth::user()->id}}">
            </div>

            <div style="margin-top: 20px">
                <label for="content">项目内容</label>
                <textarea id="content" name="basic-content" type="input" class="form-control" rows="4" required></textarea>
            </div>

            <div class="row">
                <div class="col" style="margin-top: 20px">
                    <label for="type">项目类别</label>
                    <select id="type" name="type" type="input" class="form-control">
                        <option>五类积分（管理类）</option>
                        <option>六类积分（日常类）</option>
                    </select>
                </div>
                
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