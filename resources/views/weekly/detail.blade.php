@extends('layouts.main')

@section('css')
    <link href="/css/main.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css" rel="stylesheet" />
@endsection

@section('script')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/i18n/defaults-zh_CN.min.js"></script>
    <script src="{{ URL::asset('js/weekly.js') }}"></script>
@endsection

@section('content')
    <div class="container-fluid" style="margin-top:2%; align-content: center">
         <!--Row 1-->
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        本週基础项目得分
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <tbody id="weekly-list-detail">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--Row 1 ends-->
    </div>
@endsection