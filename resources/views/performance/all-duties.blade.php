@extends('layouts.main')

@section('css')
    <link rel="stylesheet" href="https://unpkg.zhimg.com/bootstrap-table@1.18.3/dist/bootstrap-table.min.css">
    <link rel="stylesheet" type="text/css" href="/css/all-duties.css">
@endsection

@section('script')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://unpkg.zhimg.com/bootstrap-table@1.18.3/dist/bootstrap-table.min.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.18.3/dist/bootstrap-table-locale-all.min.js"></script>
    <script src="{{ URL::asset('/js/modules/modules.js') }}"></script>
    <script src="{{ URL::asset('/js/duty/all-duties.js') }}"></script>
@endsection

@section('content')
    <div class="container-fluid" style="margin-top: 30px; margin-bottom: 30px">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        业绩赋分工作
                        <input type="month" id="yearmonth" name="yearmonth" value="">
                        <a class="btn btn-success" id="export-xlsx" type="button" href='{{url('/duties/export-excel')}}' style="margin-left:15px">汇出成Excel</a>
                    </div>
                    
                    <!-- Start of card-body-->
                    <div class="card-body">

                        <!-- Toolbar of bootstrap table -->
                        <div id="toolbar">
                        </div>

                        <!-- div contains the table -->
                        <div id="duties-table-div">

                            <!-- bootstrap table -->
                            <table id="table"></table>
                          <!--   <table
                                id="table"
                                data-locale="zh-CN"
                                data-toggle="table"
                                data-search="true"
                                data-pagination="true"
                                data-side-pagination="server"
                                data-show-extended-pagination="true"
                                data-total-field="total"
                                data-data-field="row"
                                data-sortable="true"
                                data-url="https://portal.treelion.com/duties/generate-duties-table">
                                <thead>
                                    <th data-field="content" data-width="8" data-width-unit="%">项目标题</th>
                                    <th data-sortable="true" data-field="no">项目编号</th>
                                    <th data-sortable="true" data-field="type">项目类别</th>
                                    <th data-field="property">类别属性</th>
                                    <th data-sortable="true" data-field="difficulty">难度</th>
                                    <th data-field="status" data-width="4" data-width-unit="%">状态</th>
                                    <th data-field="leader" data-width="4" data-width-unit="%">组长</th>
                                    <th data-field="second_leader" data-width="4" data-width-unit="%">第二组长</th>
                                    <th data-field="members" data-width="8" data-width-unit="%">组员</th>
                                    <th data-sortable="true" data-field="start_date" data-width="5" data-width-unit="%">开始日</th>
                                    <th data-sortable="true" data-field="end_date" data-width="5" data-width-unit="%">结束日</th>
                                    <th data-field="entire_completeness">整体完成度(%)</th>
                                    <th data-field="basic_points">基础积分</th>
                                    <th data-field="gained_points">累计获取积分</th>
                                    <th data-field="latest_progress" data-width="15" data-width-unit="%">项目最新进展</th>
                                    <th data-field="node_no">节点数</th>
                                    <th data-sortable="true" data-field="next_date">下一考核时点</th>
                                    <th data-field="next_goal" data-width="15" data-width-unit="%">目标描述</th>
                                    <th data-field="next_percentage">积分比例(%)</th>
                                </thead>
                            </table> -->
                        </div>
                    </div>
                    <!-- End of card body -->

                </div>
                <!-- End of card -->
            </div>
        </div>
    </div>
    <script>
        let date= new Date()
        let month=("0" + (date.getMonth() + 1)).slice(-2)
        let year=date.getFullYear()
        document.getElementById("yearmonth").value = `${year}-${month}`;
        
        const yearmonth = document.getElementById("yearmonth").value;
        
        //var el = document.getElementById('export-xlsx');
        //el.href += yearmonth;
        
        $(document).ready(function(){
            var yearmonth = document.getElementById("yearmonth").value;
            var el = document.getElementById('export-xlsx');
            el.href = "/duties/export-excel/" + yearmonth;

            $('#table').bootstrapTable({
                url: '/duties/generate-duties-table/'+yearmonth,
                columns: [{
                    field: 'content',
                    title: '项目标题'
                }, 
                {
                    field: 'no',
                    title: '项目编号'
                }, {
                    field: 'type',
                    title: '项目类别'
                },
                {
                    field: 'property',
                    title: '类别属性',
                },
                {
                    field: 'difficulty',
                    title: '难度',
                },
                {
                    field: 'status',
                    title: '状态',
                },
                {
                    field: 'leader',
                    title: '组长',
                },
                {
                    field: 'second_leader',
                    title: '第二组长',
                },
                {
                    field: 'members',
                    title: '组员',
                },
                {
                    field: 'start_date',
                    title: '开始日',
                },
                {
                    field: 'end_date',
                    title: '结束日',
                },
                {
                    field: 'entire_completeness',
                    title: '整体完成度(%)',
                },
                {
                    field: 'basic_points',
                    title: '基础积分',
                },
                {
                    field: 'gained_points',
                    title: '累计获取积分',
                },
                {
                    field: 'latest_progress', 
                    title: '项目最新进展',
                },
                {
                    field: 'node_no',
                    title: '节点数',
                },
                {
                    field: 'next_date',
                    title: '下一考核时点',
                },
                {
                    field: 'next_goal',
                    title: '目标描述',
                },
                {
                    field: 'next_percentage',
                    title: '积分比例',
                }],
            })
            
            $("#yearmonth").on("input", function(){
                // Print entered value in a div box
                var yearmonth = document.getElementById("yearmonth").value;
                var el = document.getElementById('export-xlsx');
                el.href = "/duties/export-excel/" + yearmonth;
                //console.log(yearmonth);

                 $.ajax({
                    url: '/duties/generate-duties-table/'+yearmonth,
                    type: "get",
                    //data: {'yearmonth':yearmonth},
                    success: function (response) {
                        console.log(JSON.parse(response));
                       // $('#table').empty();
                        var data = JSON.parse(response);
                        
                
                        $('#table').bootstrapTable('load', data)
                        
                        
                    },
                    error: function(response){
                        alert('Error'+response);
                    }
                });

            });
        });
    </script>

@endsection