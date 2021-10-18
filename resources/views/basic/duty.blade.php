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
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5/locales-all.min.js"></script>
    <script>
        var userId = {{$userId}};
    </script>
    <script src="{{ URL::asset('/js/daily.js') }}"></script>
@endsection

@section('content')
    <div class="card">
    <div class="card-header">Basic Page</div>
    <div class="card-body">
    
            <div class="card-body">
            <h5 class="card-title">Name : {{ $contacts->name }}</h5>
            <p class="card-text">Address : {{ $contacts->address }}</p>
            <p class="card-text">Phone : {{ $contacts->mobile }}</p>
            <p class="card-text">Phone : {{ $contacts->mobile }}</p>
            <p class="card-text">Phone : {{ $contacts->mobile }}</p>
    </div>
        
        </hr>
    
    </div>
    </div>
@endsection