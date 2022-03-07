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
    <h2>申报业绩事项获利</h2>
    
    <form action="/performance/edit-approval"method="post"enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <div style="margin-top: 20px">
                <label for="declarant">申报人</label>
                <input id="declarant" type="input" class="form-control" readonly placeholder="{{Auth::user()->name}}">
                <input style="visibility: hidden" name="declarant-id" type="input" value="{{Auth::user()->id}}">
                <input style="visibility: hidden" name="duty_id" type="input" value="{{$data[0]['id']}}">
                <input style="visibility: hidden" name="performance_no" type="input" value="{{$data[0]['performance_no']}}">
                <input style="visibility: hidden" name="amount" type="input" value="{{$data[0]['amount']}}">
                <input style="visibility: hidden" name="direction" type="input" value="{{$data[0]['direction']}}">
            </div>

            <?php if ($data[0]['amount']): ?>
            <div class="row">
                <div class="col" style="margin-top: 20px">
                    <label for="performance-no-edit">项目编号</label>
                    <select id="performance-no-edit" name="performance-no-edit" type="input" class="form-control" disabled="true" required>
                    </select>
                </div>
            </div>
            
            <div class="row">
                <div class="col" style="margin-top: 20px">
                    <label>利润贡献度（指扣除成本後純利）（以人民幣為單位）</label><br/>
                    <input type="text" class="form-control" name="amount" placeholder="輸入金額" value="{{$data[0]['amount']}}" required disabled="true">
                </div>
                <div class="col" style="margin-top: 20px">
                    <label>利润贡献种类</label><br/>
                    <label class="radio-inline">
                        <input type="radio" class="form-control" name="direction" id="inlineradio1" value="direct" required disabled="true"
                        <?php echo ($data[0]['direction'] == "direct" ? 'checked="checked"': ''); ?>>&nbsp&nbsp正向&nbsp&nbsp
                    </label>
                    <label class="radio-inline">
                        <input type="radio" class="form-control" name="direction" id="inlineradio2" value="inverse" disabled="true"
                        <?php echo ($data[0]['direction'] == "inverse" ? 'checked="checked"': ''); ?>>&nbsp&nbsp反向&nbsp&nbsp
                    </label>
                </div>
            </div>
            
            <div class="row">
                <div class="col-5" style="margin-top: 20px">
                    <label>上载文件</label><br/>
                    @csrf
                    <?php if ($data[0]['file_path']): ?>
                        <a href='/performance/profit/download/{{$data[0]['id']}}' target="_blank">
                            <label>查看文件</label><br/>
                        </a>
                    <?php endif; ?>
                </div>
                @if (Auth::user()->role != "employee")
                    <div class="col" style="margin-top: 20px">
                        <label for="type">審批状况</label>
                        <select id="type" name="profit_status" type="input" class="form-control">
                            <option value="">-----</option>
                            <option value="pending" <?php if($data[0]['profit_status'] =="pending") echo 'selected="selected"'; ?>>待审批</option>
                            <option value="rejected" <?php if($data[0]['profit_status'] =="rejected") echo 'selected="selected"'; ?>>未通过</option>
                            <option value="approved" <?php if($data[0]['profit_status'] =="approved") echo 'selected="selected"'; ?>>通过</option>
                        </select>
                    </div>
                @else
                    <div class="col" style="margin-top: 20px">
                        <label for="type">審批状况</label>
                        <select id="type" name="profit_status" type="input" class="form-control" disabled="true">
                            <option value="">-----</option>
                            <option value="pending" <?php if($data[0]['profit_status'] =="pending") echo 'selected="selected"'; ?>>待审批</option>
                            <option value="rejected" <?php if($data[0]['profit_status'] =="rejected") echo 'selected="selected"'; ?>>未通过</option>
                            <option value="approved" <?php if($data[0]['profit_status'] =="approved") echo 'selected="selected"'; ?>>通过</option>
                        </select>
                    </div>
                @endif
            </div>
            <div class="row">
                @if (Auth::user()->role != "employee")
                <div class="col" style="margin-top: 20px">
                    <button type="submit" name="submit" class="btn btn-primary btn-block mt-4">
                        提交
                    </button>
                </div>
                @endif
            </div>
            <?php else: ?>
            <div class="row">
                <div class="col" style="margin-top: 20px">
                    <label>请先输入业绩事项获利表格</label><br/>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </form>
</div>
@endsection