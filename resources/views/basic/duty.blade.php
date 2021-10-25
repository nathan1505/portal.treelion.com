@extends('layouts.main')

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5/main.min.css" />
    <link href='https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.13.1/css/all.css' rel='stylesheet' />
    <style type="text/css">
        td,th{
            text-align:center;/** 设置水平方向居中 */
            vertical-align:middle/** 设置垂直方向居中 */
        }
    </style>
@endsection

@section('script')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5/locales-all.min.js"></script>
    <script src="{{ URL::asset('/js/basic.js') }}"></script>
@endsection

@section('content')
    <div class="container-fluid" style="margin-top:2%; align-content: center">
        <div class="card">
        <div class="card-header">基础项目內容</div>
        <div class="card-body">
        
            <div class="card-body">
                <div id="basic-table">
                </div>
            </div>
            
            </hr>
        
        </div>
        </div>
    </div>
@endsection