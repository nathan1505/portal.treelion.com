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
@endsection

@section('content')
    <div class="container-fluid" style="margin-top:2%; align-content: center">
         <!--Row 1-->
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        今月基础项目得分
                    </div>
                    <div class="card-body">
                            <table class="table table-striped">
                            <tbody id="basic-duties-approved">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--Row 1 ends-->
        <!--Row 2-->
        <div class="row" style="margin-top:20px; margin-bottom:30px">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        今月业绩项目得分
                    </div>
                    <div class="card-body">
                            <table class="table table-striped">
                            <tbody id="monthly-performance-table">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--Row 2 ends-->
        <!--Row 3-->
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        今月总得分
                    </div>
                </div>
            </div>
        </div>
        <!--Row 3 ends-->
    </div>
@endsection