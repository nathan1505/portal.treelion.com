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
        //$.get('/get-notifications', function(notifications){
            var color = "";
            var status = "";
            var hidden = "";
            
            data.forEach((element) => {
                switch(element.status){
                    case 'processing':
                        element.status = '进行中';
                        break;
                    case 'done':
                        element.status = '完成';
                        break;
                    case 'delayed':
                        element.status = '延迟';
                        break;
                    case 'postponed':
                        element.status = '暂缓';
                        break;
                    case 'rejected':
                        element.status = '未通过';
                        break;
                    default:
                        element.status = '待审批';
                }
            });
            
            var columns = {
                performance_no: '编号',
                performance_content: '项目内容',
                property: '项目类别',
                start_date: '开始日期',
                status: '项目状态',
                completeness: '积分',
                notifications: '提醒',
                id: '',
            }
            
            //console.log(userDetail);
            //console.log(notifications);
            if(!$('#performance-status').val() && !$('#performance-property').val()){
                var table = $('#root').tableSortable({
                    data: data,
                    columns: columns,
                    searchField: '#searchField',
                    rowsPerPage: 10,
                    pagination: true,
                    formatCell: function(row, key) {
                        if (key === 'status') {
                            switch(row[key]){
                                case '进行中':
                                    return $('<td></td>').addClass('font-weight-bold table-success').text(row[key]);
                                case '完成':
                                    return $('<td></td>').addClass('font-weight-bold table-primary').text(row[key]);
                                case '延迟':
                                    return $('<td></td>').addClass('font-weight-bold table-danger').text(row[key]);
                                case '暂缓':
                                    return $('<td></td>').addClass('font-weight-bold table-secondary').text(row[key]);
                                case '未通过':
                                    return $('<td></td>').addClass('font-weight-bold table-danger').text(row[key]);
                                default:
                                    return $('<td></td>').addClass('font-weight-bold table-warning').text(row[key]);
                            }
                        }
                        if (key === 'completeness'){
                            return $('<td"></td>').addClass('font-weight-bold').text(row[key]+'%');
                        }
                        if (key === 'notifications') {
                            return $('<td style="color:red;"></td>').addClass('font-weight-bold').text(row[key]);
                        }
                        if (key === 'id'){
                            if(userDetail.role == 'admin'){
                                return $('<td"><button class="btn btn-secondary btn-sm" style="float:right"><a href="/duties/' +row[key]+ '" style="color: white">查看</a></button><button class="btn btn-success btn-sm" style="float:right"><a href="performance/edit/' +row[key]+ '" style="color: white">修改</a></button><button class="btn btn-danger btn-sm" style="float:right"><a href="performance/delete/' +row[key]+ '" style="color: white" onclick="return confirm(\'是否确定要删除项目？\')">刪除</a></button><button class="btn btn-warning btn-sm" style="float:right"><a href="/performance/edit-approval/' +row[key]+'" style="color: black">获利</a></button></td>');
                            }else{
                                return $('<td"><button class="btn btn-secondary btn-sm" style="float:right"><a href="/duties/' +row[key]+ '" style="color: white">查看</a></button><button class="btn btn-warning btn-sm" style="float:right"><a href="/performance/edit-approval/' +row[key]+'" style="color: black">获利</a></button></td>');
                            }
                        }
                        // Finally return cell for rest of columns;
                        return row[key];
                    },
                    tableWillMount: function() {
                        console.log('table will mount')
                    },
                    tableDidMount: function() {
                        console.log('table did mount')
                    },
                    tableWillUpdate: function() {console.log('table will update')},
                    tableDidUpdate: function() {console.log('table did update')},
                    tableWillUnmount: function() {console.log('table will unmount')},
                    tableDidUnmount: function() {console.log('table did unmount')},
                    onPaginationChange: function(nextPage, setPage) {
                        setPage(nextPage);
                    }
                });
            }
            
            $('#changeRows').on('change', function() {
                table.updateRowsPerPage(parseInt($(this).val(), 10));
            })
            
            $("#performance-status, #performance-property").change(function () {
                var dataarray = [];
                data.forEach((element) => {
                    if(($('#performance-status').val() == element.status || !($('#performance-status').val())) &&
                        ($('#performance-property').val() == element.property || !($('#performance-property').val()))){
                            dataarray.push(element);
                        }
                });
                //console.log(dataarray);
                var table = $('#root').tableSortable({
                    data: dataarray,
                    columns: columns,
                    searchField: '#searchField',
                    rowsPerPage: 10,
                    pagination: true,
                    formatCell: function(row, key) {
                        if (key === 'status') {
                            switch(row[key]){
                                case '进行中':
                                    return $('<td></td>').addClass('font-weight-bold table-success').text(row[key]);
                                case '完成':
                                    return $('<td></td>').addClass('font-weight-bold table-primary').text(row[key]);
                                case '延迟':
                                    return $('<td></td>').addClass('font-weight-bold table-danger').text(row[key]);
                                case '暂缓':
                                    return $('<td></td>').addClass('font-weight-bold table-secondary').text(row[key]);
                                case '未通过':
                                    return $('<td></td>').addClass('font-weight-bold table-danger').text(row[key]);
                                default:
                                    return $('<td></td>').addClass('font-weight-bold table-warning').text(row[key]);
                            }
                        }
                        if (key === 'completeness'){
                            return $('<td"></td>').addClass('font-weight-bold').text(row[key]+'%');
                        }
                        if (key === 'notifications') {
                            return $('<td style="color:red;"></td>').addClass('font-weight-bold').text(row[key]);
                        }
                        if (key === 'id'){
                            if(userDetail.role == 'admin'){
                                return $('<td"><button class="btn btn-secondary btn-sm" style="float:right"><a href="/duties/' +row[key]+ '" style="color: white">查看</a></button><button class="btn btn-success btn-sm" style="float:right"><a href="performance/edit/' +row[key]+ '" style="color: white">修改</a></button><button class="btn btn-danger btn-sm" style="float:right"><a href="performance/delete/' +row[key]+ '" style="color: white" onclick="return confirm(\'是否确定要删除项目？\')">刪除</a></button><button class="btn btn-warning btn-sm" style="float:right"><a href="/performance/edit-approval/' +row[key]+'" style="color: black">获利</a></button></td>');
                            }else{
                                return $('<td"><button class="btn btn-secondary btn-sm" style="float:right"><a href="/duties/' +row[key]+ '" style="color: white">查看</a></button><button class="btn btn-warning btn-sm" style="float:right"><a href="/performance/edit-approval/' +row[key]+'" style="color: black">获利</a></button></td>');
                            }
                        }
                        // Finally return cell for rest of columns;
                        return row[key];
                    },
                    tableWillMount: function() {
                        console.log('table will mount')
                    },
                    tableDidMount: function() {
                        console.log('table did mount')
                    },
                    tableWillUpdate: function() {console.log('table will update')},
                    tableDidUpdate: function() {console.log('table did update')},
                    tableWillUnmount: function() {console.log('table will unmount')},
                    tableDidUnmount: function() {console.log('table did unmount')},
                    onPaginationChange: function(nextPage, setPage) {
                        setPage(nextPage);
                    }
                });
                
                $('#changeRows').on('change', function() {
                    table.updateRowsPerPage(parseInt($(this).val(), 10));
                })
                //console.log(table._dataset.dataset);
                table.refresh(true);
            })
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