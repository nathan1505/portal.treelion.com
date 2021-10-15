window.onload = function(){

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

    $.get('/performance/get-users',function (data){
        for (user in data) {
            $('#leader').append(
                '<option value="'+ data[user].id +'">' + data[user].name + '</option>'
            );

            $('#members').append(
                '<option value="' + data[user].id + '">' + data[user].name + '</option>'
            );
        }
        $('#members').selectpicker('refresh');
    });
    
    var start, end;
    
    $("#date-range").change(function() {
        start = $('.startdate').val();
        end = $('.enddate').val();
        // do something with values
    });

    $('#node-no').change(function () {
        $("#nodes-row").empty();
        $("#button-div").empty();
        if (($(this).val() <= 4) && ($(this).val() >= 1)){
            let amountSum;
            for (var i = 1; i <= $("#node-no").val(); i++) {
                $('#nodes-row').append(
                    '<div class="col-6">' +
                    '<div class="card" id="duty_card_' + i + '1" style="margin-top:20px">' +
                    '<div class="card-header" id="duty_card_header_' + i + '">' +
                    '<h5>节点#' + i + '</h5>' +
                    '</div>' +

                    '<div class="card-body" id="duty_card_body_' + i + '">' +
                    '<div class="form-group">' +
                    '<div class="row">' +
                    '<div class="col">' +
                    '<label for="percentage">积分比例(%)</label>' +
                    '<input type="number" id="percentage" name="percentage_' + i + '" class="form-control percentage" min=0 max=100 placeholder="原则上不低于20%">' +
                    '</div>' +
                    '</div>' +
                    '<div class="row" style="margin-top:15px">' +
                    '<div class="col">' +
                    '<label for="date">节点考核日期</label>' +
                    '<input type="date" id="date" name="date_' + i + '" class="form-control date" min="" max="" required></input>' +
                    '</div>' +
                    '</div>' +
                    '<div class="row" style="margin-top:15px">' +
                    '<div class="col">' +
                    '<label for="goal">节点目标</label>' +
                    '<textarea type="input" name="goal_' + i + '" class="form-control" required rows="3"></textarea>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>'
                );
            }
            
            $('#nodes-row').on('input', function(){
                document.getElementById("date").min = start;
                document.getElementById("date").max = end;
                console.log("asdf: "+start+"  "+end);
            });

            $('#nodes-row').on('input', '.percentage', function(){
                amountSum = [...$('.percentage')]
                    .map(input => Number(input.value))
                    .reduce((a, b) => a + b, 0);
                if(amountSum == 100){
                    $('#button-div').html(
                        '<button type="submit" class="btn btn-success">提交</button>'
                    );
                }else{
                    $('#button-div').html(
                        '<p><b>提示：</b>积分比总和必须为100%才能提交项目</p>'
                    );
                }
            });

        }else{
            window.alert("请输入有效节点数：1-4");
        }
    });
}