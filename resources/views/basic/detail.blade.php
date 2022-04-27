@extends('layouts.main')

@section('css')
    <link href="/css/main.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css" rel="stylesheet" />
@endsection

@section('script')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/i18n/defaults-zh_CN.min.js"></script>
    <script src="{{ URL::asset('/js/basic.js') }}"></script>
    <script src="{{ URL::asset('js/table-sortable.js') }}"></script>
@endsection

@section('content')
    <div class="container-fluid" style="margin-top: 1%">
        <div class="column" style="margin-top:20px">
            <div class="card" style="height:100%">
                <div class="card-header">
                    <h5 style="display:inline-block;float:left;">基础项目列表</h5>
                    <input type="text" class="form-control" style="float:left;width:15%;margin-left:15px" placeholder="表格中搜索" id="searchField">
                    <div class="d-flex justify-content-end" style="float:left;margin-left:15px">
                        <select class="custom-select" name="rowsPerPage" id="changeRows">
                            <option value="1">1</option>
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="15">15</option>
                        </select>
                    </div>
                </div>
                <div class="table-striped" id="root" ></div>
            </div>
        </div>
    </div>
@endsection