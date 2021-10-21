@extends('layouts.main')

@section('css')
    <link href="/css/main.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css" rel="stylesheet" />
@endsection

@section('script')
    <script src="{{ URL::asset('/js/daily-register.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/i18n/defaults-zh_CN.min.js"></script>
@endsection

@section('content')
    <div class="container" style="margin-top:5%">
    <h2>员工日内工作申报（早间）</h2>

    <form action="post-duty" method="post">
        @csrf
        <div class="form-group">
            <div style="margin-top: 20px">
                <label for="declarant">申报人</label>
                <input id="declarant" type="input" class="form-control" readonly placeholder="{{Auth::user()->name}}">
            </div>
            <div style="margin-top:20px">
                <label for="date">申报日期</label>
                <input id="date" type="date" name="date" class="form-control" readonly value="{{date("Y-m-d")}}">
                <input hidden name="declarant-id" type="input" value="{{Auth::user()->id}}">
                <input hidden name="period" type="input" value="morning">
            </div>

            <div id="time-slots">
            </div>

            <div id="button-div" style="margin-top:20px">
                <button type="submit" class="btn btn-success form-control">提交</button>
            </div>

        </div>
    </form>
</div>
@endsection