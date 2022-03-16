@extends('layouts.main')

@section('css')
    <link href="/css/duty.css" rel="stylesheet">
@endsection

@section('script')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ URL::asset('/js/modules/modules.js') }}"></script>
    <script src="{{ URL::asset('/js/duty.js') }}"></script>
@endsection

@section('content')
    <div class="container-fluid" style="margin-top:2%; margin-bottom:30px">
        <div class="row">
            <!--main block-->
            <div class="col-sm-8">
                <div class="card" style="height: 100%">
                    <div class="card-header" id="duty-header">
                    </div>
                    <div class="card-body" id="duty-body">
                        <table class="table table-bordered">
                            <tbody id="duty-table">
                            </tbody>
                        </table>
                    </div>
                    @if (Auth::user()->role != "employee")
                        <div class="card-footer" id="duty-footer">
                            <form method="post" action="check-duty" id="check-duty">
                                @csrf
                                <button type="submit" name="check" class="btn btn-success btn-lg" value="processing" style="margin-right:20px">通过</a>
                                <button type="submit" name="check" class="btn btn-danger btn-lg" value="rejected">拒绝</a>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col">
                <div class="card" style="height: 100%">
                    <div class="card-body">
                        <div>
                            <h5>积分计算</h5>
                            <div id="point-table-div" style="margin-top: 15px">
                                <table class="table table-bordered table-sm" style="text-align: center">
                                    <tbody id="point-table-body">
                                    </tbody>
                                </table>
                            </div>
                            <div id="nodes-table-div" style="margin-top: 15px">
                                <table class="table table-bordered table-sm" style="text-align: center">
                                    <tbody id="nodes-table-body">
                                        <tr>
                                            <td>节点名称</td>
                                            <td>积分比例</td>
                                            <td>完成度系数</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div id="gained-points-div" style="margin-top:15px">
                                <table class="table table-bordered table-sm" style="text-align: center">
                                    <tbody id="gained-table-body">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                            <div id="duty-change-status" style="margin-bottom: 15px">
                            </div>
                    </div>
                </div>
            </div>
        </div>
        
        @if ((Auth::user()->role != "employee"))
        <div class="row" id="nodes-row" style="margin-top: 20px">
        </div>
        @else
        <div class="row" id="nodes-row-employee" style="margin-top: 20px">
        </div>
        @endif

    </div>
    
@endsection