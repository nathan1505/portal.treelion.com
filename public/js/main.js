function filterById(array, id) {
    for (user in array) {
        if (array[user].id == id)
            return true;
    }
}

window.onload = function(){
    setInterval(function(){
        //Time
        const today = new Date();
        var currentDate = today.toLocaleDateString('zh-CN');
        var currentTime = today.toLocaleTimeString('zh-CN', { hour12: false });
        //document.getElementById('currentDate').innerText = currentDate;
        //document.getElementById('currentTime').innerText = currentTime;
    }, 1000);

    //weather
    $.ajax({ url:"https://data.weather.gov.hk/weatherAPI/opendata/weather.php?dataType=rhrread&lang=en",success:function(result){
        var temperature = result.temperature.data[16];
        $("#weather").append('<img class="center-block" id="weather-icon" src="https://www.hko.gov.hk/images/HKOWxIconOutline/pic'+result.icon[0]+'.png" alt="Card image" style="width:100px"></img>');
        $("#weather").append("<br/>")
        $("#weather").append("<h5>湾仔区：" + temperature.value + "°C</h5>");
    }});

    //load announcements
    function LoadAnnouncements() {
        $.get('/get-announcements', function (data) {
            for (var i = 0; i < 10; i++) {
                //if (parseInt(data[i].is_important) != 0) {
                if (data[i].is_important) {
                    $('#announcement-table').append(
                        '<tr><td><font color="red">' + data[i].content + '</font></td></tr>'
                    );
                } else {
                    $('#announcement-table').append(
                        '<tr><td style="width:20%">' + data[i].name + '</td><td>' + data[i].content + '</td></tr>'
                    );
                }
            }
        });
    }    
    LoadAnnouncements();

    $.get('/get-user',function(userDetail){
        $.get('/get-performances',function(data){
            var color = "";
            var status = "";
            
            //console.log(userDetail);
            //console.log(data);
            
            //console.log($('#performance-status').val());
            if(!$('#performance-status').val() && !$('#performance-property').val()){
                data.forEach((element) => {
        
                    if (element.status == "processing") {
                        color = "table-success";
                        status = "进行中";
                    } else if (element.status == "done") {
                        color = "table-primary";
                        status = "完成";
                    } else if (element.status == "delayed") {
                        color = "table-danger";
                        status = "延迟";
                    } else if (element.status == "postponed"){
                        color = "table-secondary";
                        status = "暂缓";
                    } else if (element.status == "rejected") {
                        color = "table-danger";
                        status = "未通过";
                    } else {
                        color = "table-warning";
                        status = "待审批";
                    }
                    
                    var disableTrue = "";    
                    if(!(userDetail.id == element.leader || userDetail.id == element.declarant_id || userDetail.role == "admin")){ //element.status == "pending"
                        disableTrue = "disabled=\"true\"";
                    };
                    $('#performance-table').append(
                        '<tr><td style="width:5%"><font size="2">' +
                        element.performance_no + '</font></td><td style="width:20%"><font size="2">' +
                        element.performance_content + '</font></td><td style="width:10%"><font size="2">' +
                        element.property + '</font></td><td style="width:10%"><font size="2">' +
                        element.start_date + '</font></td><td style="width:10%;text-align:center;" class="'+ color +'"><font size="2">' +
                        status + '</font></td><td style="width:10%;text-align:center;" class="' + color + '"><font size="2">' +
                        element.completeness + '%</font></td><td>' +
                        '<a href="/duties/' +element.id+ '"><button class="btn btn-secondary btn-sm" style="float:right">查看</button></a>'+
                        '<a href="performance/edit/' +element.id+ '"><button class="btn btn-success btn-sm" style="float:right"'+disableTrue+'>修改</button></a>' +
                        '<a href="performance/hide/' +element.id+ '"><button class="btn btn-danger btn-sm" style="float:right"'+disableTrue+'>刪除</button></a>' +
                        '<a href="/performance/edit-approval/' +element.id+'"<button class="btn btn-warning btn-sm" style="float:right">获利</button></a>' +
                        '</td></tr>'
                    );
                    
                    $('#performance-table-employee').append(
                        '<tr><td style="width:5%"><font size="2">' +
                        element.performance_no + '</font></td><td style="width:20%"><font size="2">' +
                        element.performance_content + '</font></td><td style="width:10%"><font size="2">' +
                        element.property + '</font></td><td style="width:10%"><font size="2">' +
                        element.start_date + '</font></td><td style="width:10%;text-align:center;" class="'+ color +'"><font size="2">' +
                        status + '</font></td><td style="width:10%;text-align:center;" class="' + color + '"><font size="2">' +
                        element.completeness + '%</font></td><td>' +
                        '<a href="/duties/' +element.id+ '"><button class="btn btn-secondary btn-sm" style="float:right">查看</button></a>'+
                        '<a href="performance/edit/' +element.id+ '"><button class="btn btn-success btn-sm" style="float:right"'+disableTrue+'>修改</button></a>' +
                        '<a href="performance/hide/' +element.id+ '"><button class="btn btn-danger btn-sm" style="float:right"'+disableTrue+'>刪除</button></a>' +
                        '<a href="/performance/edit-approval/' +element.id+'"<button class="btn btn-warning btn-sm" style="float:right">获利</button></a>' +
                        '</td></tr>'
                    );
    
                });
            }
            
            $("#performance-status, #performance-property").change(function () {
                console.log($('#performance-property').val());
                $('#performance-table').empty();
                $('#performance-table-employee').empty();
                data.forEach((element) => {
                    if(($('#performance-status').val() == element.status || !($('#performance-status').val())) &&
                    ($('#performance-property').val() == element.property || !($('#performance-property').val()))){
                        if (element.status == "processing") {
                            color = "table-success";
                            status = "进行中";
                        } else if (element.status == "done") {
                            color = "table-primary";
                            status = "完成";
                        } else if (element.status == "delayed") {
                            color = "table-danger";
                            status = "延迟";
                        } else if (element.status == "postponed"){
                            color = "table-secondary";
                            status = "暂缓";
                        } else if (element.status == "rejected") {
                            color = "table-danger";
                            status = "未通过";
                        } else {
                            color = "table-warning";
                            status = "待审批";
                        }
                        var disableTrue = "";    
                        if(!(userDetail.id == element.leader || userDetail.id == element.declarant_id || userDetail.role == "admin")){ //element.status == "pending"
                            disableTrue = "disabled=\"true\"";
                        };
                        
                        $('#performance-table').append(
                            '<tr><td style="width:5%"><font size="2">' +
                            element.performance_no + '</font></td><td style="width:20%"><font size="2">' +
                            element.performance_content + '</font></td><td style="width:10%"><font size="2">' +
                            element.property + '</font></td><td style="width:10%"><font size="2">' +
                            element.start_date + '</font></td><td style="width:10%;text-align:center;" class="'+ color +'"><font size="2">' +
                            status + '</font></td><td style="width:10%;text-align:center;" class="' + color + '"><font size="2">' +
                            element.completeness + '%</font></td><td>' +
                            '<a href="/duties/' +element.id+ '"><button class="btn btn-secondary btn-sm" style="float:right">查看</button></a>'+
                            '<a href="performance/edit/' +element.id+ '"><button class="btn btn-success btn-sm" style="float:right"'+disableTrue+'>修改</button></a>' +
                            '<a href="performance/hide/' +element.id+ '"><button class="btn btn-danger btn-sm" style="float:right"'+disableTrue+'>刪除</button></a>' +
                            '<a href="/performance/edit-approval/' +element.id+'"<button class="btn btn-warning btn-sm" style="float:right">获利</button></a>' +
                            '</td></tr>'
                        );

                        
                        $('#performance-table-employee').append(
                            '<tr><td style="width:5%"><font size="2">' +
                            element.performance_no + '</font></td><td style="width:20%"><font size="2">' +
                            element.performance_content + '</font></td><td style="width:10%"><font size="2">' +
                            element.property + '</font></td><td style="width:10%"><font size="2">' +
                            element.start_date + '</font></td><td style="width:10%;text-align:center;" class="'+ color +'"><font size="2">' +
                            status + '</font></td><td style="width:10%;text-align:center;" class="' + color + '"><font size="2">' +
                            element.completeness + '%</font></td><td>' +
                            '<a href="/duties/' +element.id+ '"><button class="btn btn-secondary btn-sm" style="float:right">查看</button></a>'+
                            '<a href="performance/edit/' +element.id+ '"><button class="btn btn-success btn-sm" style="float:right"'+disableTrue+'>修改</button></a>' +
                            '<a href="performance/hide/' +element.id+ '"><button class="btn btn-danger btn-sm" style="float:right"'+disableTrue+'>刪除</button></a>' +
                            '<a href="/performance/edit-approval/' +element.id+'"<button class="btn btn-warning btn-sm" style="float:right">获利</button></a>' +
                            '</td></tr>'
                        );
                    }
                });
            });
    
        });
    });



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

    /**For request TRN */
    /*
    $.ajax({ url:"https://api.coingecko.com/api/v3/coins/treelion",success:function(result){
        console.log(result);
        console.log(result.market_data.current_price.usd);
    }});
    */

    /*
    $.get('/get-news', function (data) {
        for (var i = 0; i < 50; i++) {
            if (data[i].content != ""){
                $('#news-table').append(
                    '<tr><td><span style="color:gray">' + data[i].updateTime.substring(11, 16) +'</span>&nbsp&nbsp' + data[i].content + '</td></tr>'
                );
            }
        }
    });
    */

}