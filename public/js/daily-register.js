var timeArray = ["9:00 - 10:00", "10:00 - 11:00", "11:00 - 12:00", "14:00 - 15:00", "15:00 - 16:00", "16:00 - 17:00", "17:00 - 18:00"];

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

    $.when(getManagerArray()).done(function (response) {
        for (i in timeArray){
            $('#time-slots').append(
                '<div class="card" style="margin-top:15px" id="time-period-card' + i + '">' +
                    '<div class="card-header">' +
                        '<div class="row">'+
                            '<div class="col-4">'+
                            '<h5>'+timeArray[i]+'</h5>' +
                            '</div>'+
                            '<div class="col">'+
                                '<div class="form-check" id="period-check-' + i +'">'+
                                    '<label class="form-check-label">'+
                                        '<input type="checkbox" class="form-check-input" id="check_'+i+'">同上一时段'+
                                    '</label>'+ 
                                '</div>' +
                            '</div>'+
                        '</div>'+
                    '</div>'+
                    '<div class="card-body">' +
                        '<div class="row">'+
                            '<div class="col-md-6" style="margin-top: 15px">'+
                                '<label for="performance_no">项目编号</label>'+
                                '<select id="performance_no_' + i +'" name="performance_no_'+i+'" type="input" class="no-select form-control">'+
                                '</select>'+
                            '</div>'+
                            '<div class="col-md-6" style="margin-top: 15px">'+
                                '<label for="leader">组长</label>'+
                                '<select class="select form-control" id="leader_'+i+'" name="leader_' + i +'" type="input" value="" required>'+
                                '</select>'+
                            '</div>'+
                        '</div>'+
                        '<div class="row" style="margin-top: 15px">'+
                            '<div class="col">'+
                                '<label for="content">工作内容</label>'+
                                '<textarea id="content_' + i +'" name="content_' + i +'" type="input" class="form-control" rows="2" required></textarea>'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                '</div>'
            );
        }

        $(".select").append(
            '<option value=-1 selected>' + "" + '</option>'
        );

        //Append leader names
        for (i in response){
            $(".select").append(
            '<option value='+response[i].id+'>'+response[i].name+'</option>'
            );
        }

        $.get("/get-performances",
            function (dutyResponse) {

                $('.no-select').append(
                    '<option value="-1"" selected=""selected>' + "" + '</option>'
                );

                for (i in dutyResponse){
                    $('.no-select').append(
                        '<option value="'+dutyResponse[i].id+'">'+dutyResponse[i].performance_no + ' ' + dutyResponse[i].performance_content.substr(0, 15) + '</option>'
                    );
                }
            }
        );

        $("#period-check-0").remove();

        $('select').change(function () { 
            console.log($(this).val());
        });

        $('.form-check-input').change(function(){
            if (this.checked){
                var num = parseInt((this.id).substring(6));
                $('#leader_' + num + ' option[value=' + $("#leader_" + (num - 1)).val() +']').attr('selected','selected');
                $('#performance_no_' + num + ' option[value=' + $("#performance_no_" + (num - 1)).val() + ']').attr('selected', 'selected');
                $('#content_' + num).val($('#content_' + (num-1)).val());
                $('#completeness_' + num).val($('#completeness_' + (num - 1)).val());
                $('#comment_' + num).val($('#comment_' + (num - 1)).val());
            }
        });

    });

}