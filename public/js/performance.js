function GetCurrentDutyId(){
    var path = window.location.pathname;
    var pathSplited = path.split('/');
    var dutyId = parseInt(pathSplited[3]);
    return dutyId;
}

window.onload = function(){
    
    //let original;

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

    $.get('/get-user',function(userDetail){
        $.get('/performance/get-users',function (data){
            $('#leader2').append(
                '<option></option>'
            );
            
            for (user in data) {
                
                if(data[user].id == userDetail.id){
                    $('#leader').append(
                        '<option value="'+ data[user].id +'" selected="selected">' + data[user].name + '</option>'
                    );
                }
                
                $('#leader').append(
                    '<option value="'+ data[user].id +'">' + data[user].name + '</option>'
                );
    
                $('#leader2').append(
                    '<option value="'+ data[user].id +'">' + data[user].name + '</option>'
                );
    
                $('#members').append(
                    '<option value="' + data[user].id + '">' + data[user].name + '</option>'
                );
            }
            
            $('#members').selectpicker('refresh');
        });
    });

    $.get('/performance/get-categories',function (data){
        $('#categories').append(
            '<option>选择类别编号</option>'
        );
        
        for (user in data) {
            $('#categories').append(
                '<option value="'+ data[user].categories_no +'">' + data[user].description + ' - ' + data[user].categories_no + '</option>'
            );

        }
    });

    $.get("/get-performances",function (dutyResponse) {

            $('#performance-no').append(
                '<option value="" selected=""selected>请选择业绩事项</option>'
            );

            for (i in dutyResponse){
                $('#performance-no').append(
                    '<option value="'+dutyResponse[i].performance_no+'">'+dutyResponse[i].performance_no + ' ' + dutyResponse[i].performance_content.substr(0, 15) + '</option>'
                );
            }

            for (i in dutyResponse){
                $('#performance-no-edit').append(
                    '<option value="'+dutyResponse[i].performance_no+
                    '" <?php if($data[0][\'performance_no\'] =="中介服务") echo \'selected="selected"\'; ?>'+
                    dutyResponse[i].performance_no + ' ' + dutyResponse[i].performance_content.substr(0, 15) + '</option>'
                );
            }
        }
    );
    
    /*
    
    var start, end;
    
    $("#date-range").change(function() {
        start = $('.startdate').val();
        end = $('.enddate').val();
        // do something with values
    });
    */
    
    dutyId = GetCurrentDutyId();
    //Start of request of nodes
    path = "/get-duty-detail/" + dutyId;
    $.get(path, function(data){
        //console.log(data);
        let datasum = 0;
        var performance_no = data[0].performance_no;
        //Start of request of nodes
        path = "/get-nodes/" + performance_no;
        $.get(path, data,
            function (data) {
                //console.log(data);
                //original = $('#node-no').val()
                for (var i=1; i<=data.length;i++){
                    datasum += data[i-1].node_point_percentage;
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
                    '<input type="number" id="percentage" name="percentage_' + i + '" class="form-control percentage" min=0 max=100 placeholder="原则上不低于20%" value="'+data[i-1].node_point_percentage+'">' +
                    '</div>' +
                    '</div>' +
                    '<div class="row" style="margin-top:15px">' +
                    '<div class="col">' +
                    '<label for="date">节点考核日期</label>' +
                    '<input type="date" id="date_' + i + '" name="date_' + i + '" class="form-control date" min="" max="" value="'+data[i-1].node_date+'" required></input>' +
                    '</div>' +
                    '</div>' +
                    '<div class="row" style="margin-top:15px">' +
                    '<div class="col">' +
                    '<label for="goal">节点目标</label>' +
                    '<textarea type="input" name="goal_' + i + '" class="form-control" required rows="3">'+data[i-1].node_goal+'</textarea>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>'
                );
            }
            $('#button-div').html(
                '<button type="submit" class="btn btn-success">提交</button>'
            );
        });
    });
    
    $('#node-no').change(function () {
        if(window.location.pathname == "/performance/register"){
            $("#nodes-row").empty();
            $("#button-div").empty();
            if (($(this).val() <= 4) && ($(this).val() >= 1)){
                //let amountSum;
                //let difference = $("#node-no").val() - original;
                //console.log(difference);
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
                        '<input type="date" id="date_' + i + '" name="date_' + i + '" class="form-control date" min="" max="" required></input>' +
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
                    
                    /*
                    $('#nodes-row').on('input', function(){
                        for(var i = 1; i <= $("#node-no").val(); i++){
                            document.getElementById("date_"+i).min = start;
                            document.getElementById("date_"+i).max = end;
                        }
                    });
                    */
                }
    
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
        }else{
            //$("#nodes-row").empty();
            //$("#button-div").empty();
            dutyId = GetCurrentDutyId();
            //Start of request of nodes
            path = "/get-duty-detail/" + dutyId;
            $.get(path, function(data){
                //console.log(data);
                let datasum = 0;
                performance_no = data[0].performance_no;
                //Start of request of nodes
                path = "/get-nodes/" + performance_no;
                $.get(path, data,
                    function (data) {
                        //console.log(data);
                        //original = $('#node-no').val()
                        //console.log(original);
                        //let nodeno;
                        $("#nodes-row").empty();
                        $("#button-div").empty();
                        if (($('#node-no').val() <= 4) && ($('#node-no').val() >= 1)){
                            let amountSum;
                            nodeno = $("#node-no").val();
                            for (var i = 1; i <= $("#node-no").val(); i++) {
                                if(data[i-1]){
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
                                        '<input type="number" id="percentage" name="percentage_' + i + '" class="form-control percentage" min=0 max=100 placeholder="原则上不低于20%" value="'+data[i-1].node_point_percentage+'">' +
                                        '</div>' +
                                        '</div>' +
                                        '<div class="row" style="margin-top:15px">' +
                                        '<div class="col">' +
                                        '<label for="date">节点考核日期</label>' +
                                        '<input type="date" id="date_' + i + '" name="date_' + i + '" class="form-control date" min="" max="" value="'+data[i-1].node_date+'" required></input>' +
                                        '</div>' +
                                        '</div>' +
                                        '<div class="row" style="margin-top:15px">' +
                                        '<div class="col">' +
                                        '<label for="goal">节点目标</label>' +
                                        '<textarea type="input" name="goal_' + i + '" class="form-control" required rows="3">'+data[i-1].node_goal+'</textarea>' +
                                        '</div>' +
                                        '</div>' +
                                        '</div>' +
                                        '</div>' +
                                        '</div>'
                                    );
                                }else{
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
                                        '<input type="number" id="percentage" name="percentage_' + i + '" class="form-control percentage" min=0 max=100 placeholder="原则上不低于20%" value="">' +
                                        '</div>' +
                                        '</div>' +
                                        '<div class="row" style="margin-top:15px">' +
                                        '<div class="col">' +
                                        '<label for="date">节点考核日期</label>' +
                                        '<input type="date" id="date_' + i + '" name="date_' + i + '" class="form-control date" min="" max="" value="" required></input>' +
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

                            }
                        }
                });
            });
        }
    });
}