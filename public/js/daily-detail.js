var timeArray = ["9:00 - 10:00", "10:00 - 11:00", "11:00 - 12:00", "14:00 - 15:00", "15:00 - 16:00", "16:00 - 17:00", "17:00 - 18:00"];

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

function getDuties() {
    var defer = $.Deferred();
    $.ajax({
        type: "GET",
        url: '/get-performances',
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

function filterByDutyId(array, id) {
    for (duty in array) {
        if (array[duty].id == id)
            return array[duty].performance_no;
    }
}

function getCompany(user_id) {
    var defer = $.Deferred();
    $.ajax({
        type: "GET",
        url: "/daily/get-company-name/" + user_id,
        async: true,
        success: function (data) {
            defer.resolve(data);
        }
    });
    return defer;
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

    //.log(dailyReportId);

    $.get("/daily/get-daily-by-id/" + dailyReportId,
        function (data) {
            data = data[0];
            console.log(data);

            $.when(getDuties()).done(function (dutyArray) {
                $.when(getCompany(data.user_id)).done(function (companyName){
                    $.when(getUserArray()).done(function (userArray) {

                        console.log(dutyArray);
                        console.log(companyName);

                        $('#detail-body').append(
                            '<tr>' +
                            '<td style="width:10%">申报人</td><td style="width:10%">' + filterById(userArray, data.user_id) + '</td><td style="width:10%">所属公司</td><td style="width:10%">' + companyName.company + '</td>' +
                            '<td style="width:10%">填写时间</td><td colspan=5>' + data.timestamp + '</td>' +
                            '</tr>' + 
                            '<tr>' + 
                            '<td style="width:10%">时间段</td><td colspan=3>工作内容</t><td>项目编码</td><td style="width:10%">上级或项目组组长</td><td style="width:10%">完成度</td><td style="width:10%">自我评价</td><td style="width:10%">滞后原因</td><td>滞后补救措施</td>'
                        );

                        for (var i = 0; i < 7; i++){
                            $('#detail-body').append('<tr>');

                            $('#detail-body').append(
                                '<td>' + timeArray[i] + '</td>' +
                                '<td colspan=3">' + data['content_'+i] + '</td>' +
                                '<td>' + filterByDutyId(dutyArray, data['performance_no_'+i]) + '</td>'+
                                '<td>' + companyName.manager + '</td>'
                            );

                            if (data['completeness_' + i] !== null){
                                $('#detail-body').append('<td>' + data['completeness_' + i] + '%</td>');
                            }else{
                                $('#detail-body').append('<td>&nbsp</td>');
                            }

                            if (data['comment_' + i] !== null) {
                                $('#detail-body').append('<td>' + data['comment_' + i] + '</td>');
                            } else {
                                $('#detail-body').append('<td>&nbsp</td>');
                            }

                            if (data['reason_' + i] == null || data['reason_' + i] == "") {
                                $('#detail-body').append('<td>&nbsp</td>');
                            } else {
                                $('#detail-body').append('<td>' + data['reason_' + i] + '</td>');
                            }

                            if (data['measure_' + i] == null || data['measure_' + i] == "") {
                                $('#detail-body').append('<td>&nbsp</td>');
                            } else {
                                $('#detail-body').append('<td>' + data['measure_' + i] + '</td>');
                            }

                            $("#detail-body").append('</tr>');
                        }
                    });
                });
            });
        }
    );
}