@extends('layouts.main')

@section('script')
    <script>
        var dailyReportId = {{$dailyReportId}};
    </script>
    <script src="{{ URL::asset('js/daily-detail.js') }}"></script>
@endsection

@section('content')
    <div class="container-fluid" style="margin-top:2%; margin-bottom:30px; align-content: center">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        日内工作报告
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered" style="font-size: 10px">
                            <tbody id="detail-body" style="text-align:center">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection