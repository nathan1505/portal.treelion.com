@extends('layouts.main')

@section('css')
    <link href="/css/main.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css" rel="stylesheet" />
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/i18n/defaults-zh_CN.min.js"></script>
    <script src="{{ URL::asset('js/attendance.js') }}"></script>
@endsection

@section('content')
    <div class="container">
        <div class="container-fluid" style="margin-top:2%; align-content: center">
            <form action="update" method="post">
            @csrf
            <div class="form-group">
                <div class="card">
                    <div class="card-header">
                        输入每月考勤分数
                        <input type="month" id="yearmonth" name="yearmonth" value="">
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <tbody id="users-attendance">
                            </tbody> 
                        </table>
                    </div>
                    <div class="card-footer">
                        <button onclick="myFunction()" type="submit" class="btn btn-success">提交</button>
                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>
    <script>
        function myFunction() {
          confirm("是否确定要输入考勤分数？");
        }
        
        let date= new Date()
        let month=("0" + (date.getMonth() + 1)).slice(-2)
        let year=date.getFullYear()
        document.getElementById("yearmonth").value = `${year}-${month}`;
        
        const yearmonth = document.getElementById("yearmonth").value;
        
        $(document).ready(function(){
            $("#yearmonth").on("input", function(){
                // Print entered value in a div box
                var yearmonth = document.getElementById("yearmonth").value;
                //console.log(yearmonth);
                 $.ajax({
                    url: '/attendance/update/'+yearmonth,
                    type: "get",
                    //data: {'yearmonth':yearmonth},
                    success: function (response) {
                        $('#users-attendance').empty();
                        $.get('/performance/get-users',function (data){
                            $.get('/attendance/update/'+yearmonth, function(attendancedata){
                                
                                data = (Object.values(data));
                                data.sort((a, b) => (a.id > b.id) ? 1 : -1);
                                data = data.filter(function( obj ) {
                                    return obj.id !== 1;
                                });
                                
                                //console.log(attendancedata);
                                data.forEach((element) => {
                                    const findattendance = attendancedata.find((a) => a.user_id === element.id);
                                    if(findattendance){
                                        $('#users-attendance').append(
                                            '<tr><td style="width:10% float:center">' + element.name + '</td>' +
                                            '<td style="width:10% float:center"><input id="attendance-' + element.id + '" name="attendance-' + element.id + '" type="number" value=' + findattendance.points + '></td></tr>'
                                        );
                                    }else{
                                        $('#users-attendance').append(
                                            '<tr><td style="width:10% float:center">' + element.name + '</td>' +
                                            '<td style="width:10% float:center"><input id="attendance-' + element.id + '" name="attendance-' + element.id + '" type="number" value=0></td></tr>'
                                        );
                                    }
                                });
                            });
                        });
                    },
                    error: function(response){
                        alert('Error'+response);
                    }
                });
            });
        });
    </script>
@endsection