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
    
    <form action="profit"method="post"enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <div style="margin-top: 20px">
                <label for="declarant">申报人</label>
                <input id="declarant" type="input" class="form-control" readonly placeholder="{{Auth::user()->name}}">
                <input style="visibility: hidden" name="declarant-id" type="input" value="{{Auth::user()->id}}">
            </div>

            
            <div class="row">
                <div class="col" style="margin-top: 20px">
                    <label for="performance-no">项目编号</label>
                    <select id="performance-no" name="performance-no" type="input" class="form-control" required>
                    </select>
                </div>
            </div>
            
            <div class="row">
                <div class="col" style="margin-top: 20px">
                    <label>利润贡献度（指扣除成本後純利）（以人民幣為單位）</label><br/>
                    <input type="text" class="form-control" name="amount" placeholder="輸入金額" required>
                </div>
                <div class="col" style="margin-top: 20px">
                    <label>利润贡献种类</label><br/>
                    <label class="radio-inline">
                        <input type="radio" class="form-control" name="direction" id="inlineradio1" value="direct" required>&nbsp&nbsp正向&nbsp&nbsp
                    </label>
                    <label class="radio-inline">
                        <input type="radio" class="form-control" name="direction" id="inlineradio2" value="inverse">&nbsp&nbsp反向&nbsp&nbsp
                    </label>
                </div>
            </div>
            
            <div class="row">
                <div class="col-5" style="margin-top: 20px">
                    <label>上载文件</label><br/>
                    @csrf
                    <!--
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success">
                            <strong>{{ $message }}</strong>
                        </div>
                    @endif
                    -->

                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                  <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <div class="custom-file">
                        <input type="file" name="file" class="custom-file-input" id="chooseFile">
                        <label class="custom-file-label" for="chooseFile">选择文件</label>
                        <label id="file-name"></label>
                    </div>
                    <script>
                        $("#chooseFile").change(function(){
                          $("#file-name").text(this.files[0].name);
                        });
                    </script>
                
                    <button type="submit" name="submit" class="btn btn-primary btn-block mt-4">
                        提交
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection