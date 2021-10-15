var timeArray = ["9:00 - 10:00", "10:00 - 11:00", "11:00 - 12:00", "14:00 - 15:00", "15:00 - 16:00", "16:00 - 17:00", "17:00 - 18:00"];
var url = window.location.pathname.toString();
url = url.substring(13);

function GetManagerName(id, managers) {
    for (i in managers){
        if (managers[i].id == id){
            return managers[i].name;
        }
    }
    return "";
}

function GetDutyNo(id, duties) {
    for (i in duties) {
        if (duties[i].id == id) {
            return duties[i].performance_no;
        }
    }
    return "";
}

function getManagerArray() {
    var defer = $.Deferred();
    $.ajax({
        type: "GET",
        url: "/get-managers",
        async: true,
        success: function (data) {
            defer.resolve(data);
        }
    });
    return defer;
}

function getDailyReport() {
    var defer = $.Deferred();
    $.ajax({
        type: "GET",
        url: "/daily/get-daily" + url,
        async: true,
        success: function (data) {
            defer.resolve(data);
        }
    });
    return defer;
}

function GetPerformanceDuties() {
    var defer = $.Deferred();
    $.ajax({
        type: "GET",
        url: "/get-performances",
        async: true,
        success: function (data) {
            defer.resolve(data);
        }
    });
    return defer;
}

function jqAppendInput(num){
    $('#card_body_' + num).append(
        '<div class="row" id="delayed_div_' + num + '">' +
        '<div class="col">' +
        '<div class="form-group">' +
        '<label for="reason_' + num + '">滞后原因</label>' +
        '<input required type="input" class="reason form-control" name="reason_' + num + '" id="reason_' + num + '">' +
        '</div>' +
        '<div class="form-group">' +
        '<label for="measure_' + num + '">补救措施</label>' +
        '<input required type="input" class="measure form-control" name="measure_' + num + '" id="measure_' + num + '">' +
        '</div>' +
        '</div>' +
        '</div>'
    );
}

function validate(){
    if ($('.reason').val() == "" || $('.measure').val() == ""){
        alert("请将滞后内容填写完整！");
        return false;
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

    $.when(getDailyReport()).done(function (dailyResponse) {
        $.when(getManagerArray()).done(function (response) {
            $.when(GetPerformanceDuties()).done(function (dutiesResponse){

                var performance_no = new Array();
                performance_no.push(dailyResponse.performance_no_0);
                performance_no.push(dailyResponse.performance_no_1);
                performance_no.push(dailyResponse.performance_no_2);
                performance_no.push(dailyResponse.performance_no_3);
                performance_no.push(dailyResponse.performance_no_4);
                performance_no.push(dailyResponse.performance_no_5);
                performance_no.push(dailyResponse.performance_no_6);

                var leaders = new Array();
                leaders.push(dailyResponse.leader_0);
                leaders.push(dailyResponse.leader_1);
                leaders.push(dailyResponse.leader_2);
                leaders.push(dailyResponse.leader_3);
                leaders.push(dailyResponse.leader_4);
                leaders.push(dailyResponse.leader_5);
                leaders.push(dailyResponse.leader_6);

                var contents = new Array();
                contents.push(dailyResponse.content_0);
                contents.push(dailyResponse.content_1);
                contents.push(dailyResponse.content_2);
                contents.push(dailyResponse.content_3);
                contents.push(dailyResponse.content_4);
                contents.push(dailyResponse.content_5);
                contents.push(dailyResponse.content_6);


                for (var i = 0; i < timeArray.length; i++){
                    $('#time-slots').append(
                        '<div class="card" style="margin-top:15px" id="time-period-card' + i + '">' +
                            '<div class="card-header">' +
                                '<div class="row">'+
                                    '<div class="col-4">'+
                                        '<h5>'+ timeArray[i] + '</h5>' +
                                    '</div>'+
                                    '<div class="col">'+
                                        '<div class="form-check" id="period-check-' + i +'">'+
                                            '<label class="form-check-label">'+
                                                '<input type="checkbox" class="same-as-above form-check-input" id="check_' + i + '">同上一时段'+
                                            '</label>'+ 
                                        '</div>' +
                                    '</div>'+
                                '</div>'+
                            '</div>'+
                            '<div class="card-body" id="card_body_' + i + '">' +
                                '<div class="row">' +
                                    '<div class="col-6">' +
                                        '<table class="table-bordered">'+
                                            '<tbody>'+
                                                '<tr>'+
                                                    '<td style="width:25%; text-align:center">项目编号</td>' +
                                                    '<td style="width:25%; text-align:center">'+GetDutyNo(performance_no[i], dutiesResponse)+'</td>'+
                                                    '<td style="width:25%; text-align:center">组长</td>'+
                                                    '<td style="width:25%; text-align:center">'+GetManagerName(leaders[i], response)+'</td>'+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td colspan=4 style="width:25%; text-align:center">工作内容</td>'+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td colspan=4 style="text-align:center">' + contents[i] + '</td>' +
                                                '</tr>'+ 
                                            '</tbody>'+
                                        '</table>'+
                                    '</div>' +
                                    '<div class="col-6">' +
                                        '<div class="form-group">'+
                                            '<label for="completeness">完成度 %</label>'+
                                            '<input id="completeness_' + i + '" name="completeness_' + i +'" type="number" class="form-control" min=0 max=100 step=5 value=100 required>'+
                                        '</div>'+
                                        '<div class="form-group" style="margin-top: 15px">'+
                                            '<label for="comment">自我评价</label>'+
                                            '<input id="comment_' + i +'" name="comment_' + i +'" type="input" class="form-control" value="已完成">'+
                                        '</div>'+
                                        '<div class="form-check" style="margin-top: 15px">' +
                                            '<label class="form-check-label" for="is_delayed_' + i + '">' +
                                                '<input class="is-delayed form-check-input " type="checkbox" name="is_delayed_' + i + '" id="is_delayed_' + i + '">是否滞后' +
                                            '</label >'+
                                    '</div>' +
                                '</div>' +
                            '</div>'+
                        '</div>'
                    );
                }//end of for loop to add cards
                $("#period-check-0").remove();

                $('.same-as-above').change(function () {
                    //Get number of the check
                    var num = parseInt((this.id).substring(6));
                    if (this.checked) {
                        //Fill in the completeness and comment
                        $('#completeness_' + num).val($('#completeness_' + (num - 1)).val());
                        $('#comment_' + num).val($('#comment_' + (num - 1)).val());

                        if ($('#is_delayed_' + (num-1)).attr("checked") == false){
                        //The last one is not checked
                            $('#is_delayed_' + num).attr("checked", false);
                            $('#delayed_div_' + num).remove();
                        } else {
                            $('#delayed_div_' + num).remove();
                            $('#is_delayed_' + num).attr("checked", "checked");
                            jqAppendInput(num);
                            $('#reason_' + num).val($('#reason_' + (num - 1)).val());
                            $('#measure_' + num).val($('#measure_' + (num - 1)).val());
                        }
                    } else {
                        $('#completeness_' + num).val(null);
                        $('#comment_' + num).val(null);
                        $('#is_delayed_'+ num).attr("checked", false);
                        $('#delayed_div_' + num).remove();
                    }
                });

                //If the duty is delayed, append two input for reason and methods
                $('.is-delayed').change(function () {
                    //Get number of the check
                    var num = parseInt((this.id).substring(11));
                    if (this.checked) {
                        $('#card_body_' + num).append(
                            '<div class="row" id="delayed_div_' + num + '">' +
                                '<div class="col">' +
                                    '<div class="form-group">' +
                                        '<label for="reason_' + num + '">滞后原因</label>' +
                                        '<input required type="input" class="reason form-control" name="reason_' + num + '" id="reason_' + num + '">' +
                                    '</div>' +
                                    '<div class="form-group">' +
                                        '<label for="measure_' + num + '">补救措施</label>' +
                                        '<input required type="input" class="measure form-control" name="measure_' + num + '" id="measure_' + num + '">' +
                                    '</div>' +
                                '</div>' +
                            '</div>'
                        );
                    } else {
                        $('#delayed_div_' + num).remove();
                    }
                });

            });
        });
    });
}