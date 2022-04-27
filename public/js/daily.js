//var userId
function getUserArray() {
    var defer = $.Deferred();
    $.ajax({
        type: "GET",
        url: '/performance/get-users',
        async: true,
        success: function (data) {
            defer.resolve(data);
        }
    });
    return defer;
}

function getDailyArray() {
    var defer = $.Deferred();
    $.ajax({
        type: "GET",
        url: '/daily/get-daily/' + userId,
        async: true,
        success: function (data) {
            defer.resolve(data);
        }
    });
    return defer;
}

function filterById(array, id) {
    for (user in array) {
        if (array[user].id == id)
            return array[user].name;
    }
}

window.onload = function () {

    $.get('/get-hsi', function (data) {
        if (data.rise_fall > 0) {
            $('#hsi').append(
                '恒生指数&nbsp&nbsp<span style="color:green">' + data.last_price + '&nbsp&nbsp&#9650&nbsp&nbsp' + data.rise_fall_per + '</span>'
            );
        } else {
            $('#hsi').append(
                '恒生指数&nbsp&nbsp<span style="color:red">' + data.last_price + '&nbsp&nbsp&#9660&nbsp&nbsp' + data.rise_fall_per + '</span>'
            );
        }
    });

    $.get('/get-elion', function (data) {
        if (data.rise_fall > 0) {
            $('#elion-stock').append(
                '亿利洁能&nbsp&nbsp<span style="color:green">' + data.last_price + '&nbsp&nbsp&#9650&nbsp&nbsp' + data.rise_fall_per + '%</span>'
            );
        } else {
            $('#elion-stock').append(
                '亿利洁能&nbsp&nbsp<span style="color:red">' + data.last_price + '&nbsp&nbsp&#9660&nbsp&nbsp' + data.rise_fall_per + '%</span>'
            );
        }
    });

    $.when(getDailyArray()).done(function (dailyResponse) {
        $.when(getUserArray()).done(function (userResponse) {

            for (var i = 0; i < 6; i++){
                $('#daily-report-tbody').append(
                    '<tr>'+
                        '<td style="text-align:center">' + filterById(userResponse, dailyResponse[i].user_id) + '</td>' +
                        '<td style="width: 20%;text-align:center">' + dailyResponse[i].timestamp.substring(0,10) + '</td>' +
                        '<td style="width: 20%;text-align:center">' + dailyResponse[i].timestamp.substring(11) + '</td>' +
                        '<td style="width: 15%;text-align:center"><a href="/daily/detail/' + dailyResponse[i].id +'"><button class="btn btn-success">详情</button></a></td>' +
                        '<td style="width: 15%;text-align:center"><a href="/daily/img/'+ dailyResponse[i].id +'"><button class="btn btn-success">图片</button></a></td>' +
                        '<td style="width: 15%;text-align:center"><a href="/daily/pdf/' + dailyResponse[i].id +'"><button class="btn btn-success">PDF</button></a></td>'+
                    '</tr>'
                );
            }
        });
    });
    
    $.get('/get-user',function(userDetail){
        $.get('/get-basic-duties',function(data){
            var color = "";
            var status = "";
    
            for (var i = 0; i < data.length; i++){
                
                if (data[i].status == "approved") {
                    color = "table-success";
                    status = "通过";
                } else if (data[i].status == "rejected") {
                    color = "table-danger";
                    status = "未通过";
                } else if(data[i].status == "end"){
                    color = "table-secondary";
                    status = "结束";
                } else {
                    color = "table-warning";
                    status = "待审批";
                }

                if(userDetail.id == data[i].member || (data[i].status == "pending" && userDetail.role != "employee")){
                    if(data[i].status != "delete"){
                        $('#basic-duties-table').append(
                            '<tr><td style="width:10%">' + data[i].basic_no + '</td>' + 
                            '<td style="width:30%">' + data[i].basic_content + '</td>' + 
                            '<td style="width:20%;text-align:center;" class="'+ color +'">' + status + '</td>' +
                            '<td><a href="/basic/' + data[i].id + '"><button class="btn btn-secondary">查看</button></a>' + 
                            '<a href="/basic/edit/' + data[i].id + '"><button class="btn btn-success">修改</button></a>' + 
                            '<a href="/basic/hide/' + data[i].id + '"><button class="btn btn-danger">删除</button></a><td>' + 
                            '</tr>'
                        );
                    }
                }
            }
        });
    });

    $.get('/get-user',function(userDetail){
            $.get("/get-performance-id", function (data) {
                    data = (Object.values(data));
                    data.sort((a, b) => (a.start_date < b.start_date) ? 1 : -1);
                    //console.log(data);
                    $('#duty-table-body').append(
                        '<tr>' +
                        '<font size="2"><td>状态</td><td>完成度</td><td>编号</td><td>项目内容</td><td>开始时间</td><td>详情</td></font>' +
                        '</tr>'
                    );
        
                    var color = "";
                    var status = "";
        
                    for (var i = 0; i < 100; i++) {
        
                        if (data[i].status == "processing") {
                            color = "table-success";
                            status = "进行中";
                        } else if (data[i].status == "done") {
                            color = "table-primary";
                            status = "完成";
                        } else if (data[i].status == "delayed") {
                            color = "table-danger";
                            status = "延迟";
                        } else if (data[i].status == "postponed") {
                            color = "table-secondary";
                            status = "暂缓";
                        } else if (data[i].status == "rejected") {
                            color = "table-danger";
                            status = "未通过";
                        } else {
                            color = "table-warning";
                            status = "待审批";
                        }
                        
                        var disableTrue = "";    
                        if(userDetail.role != "admin" && (data[i].status != "pending" && (userDetail.id == data[i].leader || userDetail.id == data[i].declarant_id))){ //element.status == "pending"
                            disableTrue = "disabled=\"true\"";
                        };
        
                        $('#duty-table-body').append(
                            '<tr>'+
                            '<td style="width:10%;text-align:center;" class="' + color + '"><font size="2">' + status + '</font></td>' +
                            '<td style="width:10%;text-align:center;" class="' + color + '"><font size="2">' + data[i].completeness + '%</font></td>' +
                            '<td style="width:5%"><font size="2">' + data[i].performance_no + '</font></td>' +
                            '<td style="width:30%"><font size="2">' + data[i].performance_content + '</font></td>' +
                            '<td style="width:17%"><font size="2">' + data[i].start_date + '</font></td>' + 
                            '<td><a href="/duties/' + data[i].id + '"><button class="btn btn-secondary btn-sm" style="float:right">查看</button></a>'+
                            '<a href="/performance/edit/' + data[i].id + '"><button class="btn btn-success btn-sm" style="float:right"'+disableTrue+'>修改</button></a>' +
                            '<a href="/performance/delete/' + data[i].id + '" onclick="return confirm(\'是否确定要删除项目？\')"><button class="btn btn-danger btn-sm" style="float:right"'+disableTrue+'>刪除</button></a>' +
                            '<a href="/performance/edit-approval/' +data[i].id+'"<button class="btn btn-warning btn-sm" style="float:right">获利</button></a>' + 
                            '</td>' +
                            '<td><font color="#FF0000" size="2">' + data[i].notification + '</font></td>' +
                            '</tr>'
                        );
                    }
                }
            );
        }
    );

    var calendarE1 = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarE1, {
        initialView: 'timeGridWeek',
        themeSystem: 'bootstrap',
        nowIndicator: true,
        weight: '100%',
        height: 600,
        aspectRatio: 1.5,
        scrollTime: '9:00:00',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        timeZone: 'Asia/Shanghai',
        locale: 'zh-cn',
        businessHours: {
            // days of week. an array of zero-based day of week integers (0=Sunday)
            daysOfWeek: [1, 2, 3, 4, 5], // Monday - Thursday

            startTime: '9:00', // a start time (10am in this example)
            endTime: '18:00', // an end time (6pm in this example)
        },
        eventSources:[
            {
                url: '/daily/generate-daily-calendar/' + userId
            }
        ],
        displayEventTime: false
    });
    calendar.render();
    
}