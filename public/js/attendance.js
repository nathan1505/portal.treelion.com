window.onload = function () {
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
}