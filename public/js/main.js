window.onload = function(){
    setInterval(function(){
        //Time
        const today = new Date();
        var currentDate = today.toLocaleDateString('zh-CN');
        var currentTime = today.toLocaleTimeString('zh-CN', { hour12: false });
        document.getElementById('currentDate').innerText = currentDate;
        document.getElementById('currentTime').innerText = currentTime;
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

    $.get('/get-performances',function(data){
        var color = "";
        var status = "";

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

            $('#performance-table').append(
                '<tr><td style="width:5%">' +
                element.performance_no + '</td><td style="width:25%">' +
                element.performance_content + '</td><td style="width:10%">' +
                element.property + '</td><td style="width:17%">' +
                element.start_date + '</td><td style="width:12%;text-align:center;" class="'+ color +'">' +
                status + '</td><td style="width:4%;text-align:center;" class="' + color + '">' +
                element.completeness + '%</td><td>' +
                '<a href="/duties/' +element.id+ '"><button class="btn btn-secondary btn-sm" style="float:right">查看</button></a>'+
                '</td></tr>'
            );
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