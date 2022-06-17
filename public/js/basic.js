/** 
 *@method GetCurrentDutyId
 *@discription Get the duty id from the HTTP path
 *@returns the id of the current Performance duty
*/
function GetCurrentDutyId(){
    var path = window.location.pathname;
    var pathSplited = path.split('/');
    var dutyId = parseInt(pathSplited[2]);
    return dutyId;
}

/**
 * @description Select the info of specific user from GET response by its id
 * @param {Array} array the array of users info 
 * @param {int} id ID of specific user
 * @returns 
 */
 function filterById(array, id) {
    for (var user in array) {
        if (array[user].id == id)
            return array[user];
    }
}

/**
 * @description JQuery deferred to get the infomation of all users
 * @returns the array containing all users' info
 */
function getUserArray() {
    var defer = $.Deferred();
    $.ajax({
        type: "GET",
        url: "/performance/get-users",
        async: true,
        success: function (data) {
            defer.resolve(data);
        }
    });
    return defer;
}

window.onload = function(){
    //Add the @csrf-token for dynamically generated form
    var token = $('meta[name="csrf-token"]').attr('content');

    //Initialize the variables of Current Performance ID and the request path
    var dutyId = GetCurrentDutyId();
    var path = "/get-basic-duty/"+dutyId;

    $.get('/get-user',function(user){
        //GET the detail of performance duty 
        //response data as the duty detail
        $.get(path, function(data){
            //console.log(data);
            //console.log(token);
            
            var diff = "";
            
            switch (data[0].difficulty) {
                case "difficult":
                    diff = "困难";
                    break;
                case "normal":
                    diff = "中等";
                    break;
                default:
                    diff = "简单"
                    // code
            }
            
            $.when(getUserArray()).done(function(response){
                $('#basic-table').append(
                    '<h5 class="card-title">编号 : ' + data[0].basic_no + '</h5>' + 
                    '<p class="card-text">申报人 : ' + filterById(response, data[0].member).name + '</p>' +
                    '<p class="card-text">项目标题 : ' + data[0].basic_content + '</p>' +
                    '<p class="card-text">项目类别 : ' + data[0].type + '</p>' +
                    '<p class="card-text">难度 : ' + diff + '</p>' +
                    '<p class="card-text">状态 : ' + data[0].status + '</p>' +
                    '<p class="card-text">积分 : ' + data[0].total_points + '</p>' +
                    '<p class="card-text">开始日期 : ' + data[0].timestamp + '</p>'
                );
            });
    
        });

        $.get('/get-monthly-performance', function (data2){
            $.get('/get-approved-basic-duties',function(data){
                
                var diff = "";
                var total_point1 = 0;
        
                for (var i = 0; i < data.length; i++){
                    
                    switch (data[i].difficulty) {
                        case "difficult":
                            diff = "困难";
                            break;
                        case "normal":
                            diff = "中等";
                            break;
                        default:
                            diff = "简单"
                            // code
                    }
                    
                    $('#basic-duties-approved').append(
                        '<tr><td style="width:10%">' + data[i].basic_no + '</td>' + 
                        '<td style="width:25%">' + data[i].basic_content + '</td>' + 
                        '<td style="width:20%">' + data[i].type + '</td>' + 
                        '<td style="width:20%">' + diff + '</td>' + 
                        '<td style="width:15%">' + data[i].timestamp.substring(0,10) + '</td>' + 
                        '<td style="width:%">' + data[i].total_points + '</td>' + 
                        '</tr>'
                    );
                    
                    total_point1 += data[i].total_points;
                }
                
                var role;
                var userID = dutyId;
                var total_point2 = 0.0;
                var total_expect_point = 0.0;
                var monthly_person_expect = 0.0;
                //console.log(data);
                for (var i = 0; i < data2.length; i++){
                    var points;
                    var whole_project;
                    
                    switch (data2[i].difficulty) {
                        case "difficult":
                            diff = "困难";
                            break;
                        case "normal":
                            diff = "中等";
                            break;
                        default:
                            diff = "简单";
                            // code
                    }
                    
                    if(data2[i].leader == userID){
                        role = "组长";
                        points = data2[i].leader_month_actual;
                        whole_project = data2[i].leader_points;
                        monthly_person_expect = data2[i].leader_month;
                    }
                    else{
                        role = "组員";
                        points = data2[i].member_month_actual;
                        whole_project = data2[i].member_points;
                        monthly_person_expect = data2[i].member_month;
                    }
                    
                    var profit;
                    if(data2[i].amount == null){
                        profit = "-";
                    }else{
                        profit = data2[i].profit_coefficient.toFixed(1);
                    }
                    
                    $('#monthly-performance-table').append(
                        '<tr><td style="width:7%">' + data2[i].performance_no + '</td>' + 
                        '<td style="width:10%">' + data2[i].performance_content + '</td>' + 
                        '<td style="width:5%">' + data2[i].type + '</td>' + 
                        '<td style="width:5%">' + diff + '</td>' + 
                        '<td style="width:5%">' + role + '</td>' + 
                        '<td style="width:5%">' + Math.round(data2[i].basic_points) + '</td>' + 
                        '<td style="width:5%">' + Math.round(data2[i].this_month) + '</td>' + 
                        '<td style="width:5%">' + Math.round(monthly_person_expect) + '</td>' + 
                        '<td style="width:5%">' + Math.round(points) + '</td>' + 
                        '<td style="width:5%">' + profit + '</td>' + 
                        
                        '</tr>'
                    );
                    
                    total_point2+=points;
                    total_expect_point += Math.round(monthly_person_expect);
        
                }
                
                var basic_point = Math.round(total_point1);
                var distributed_point = 0;
                
                if(user.pointtype == "regular" && basic_point > 40){
                    distributed_point = basic_point-40;
                    basic_point = 40;
                }else if(user.pointtype == "regular2"){
                    basic_point = 40;
                }else if(user.pointtype == "support"){
                    basic_point = 100
                }
                
                $('#month-basic-points').append(basic_point);
                
                $('#basic-points-distribute').append(distributed_point);
                
                $('#month-performance-points').append(Math.round(total_point2));
                
                $('#month-expected-points').append(total_expect_point);
                
                if(user.pointtype == "regular" && Math.round(total_point2)+basic_point < 100){
                    if(basic_point+distributed_point+Math.round(total_point2) >= 100) 
                        actual_total = 100;
                    else
                        actual_total = basic_point+distributed_point+Math.round(total_point2);
                }else{
                    actual_total = basic_point+Math.round(total_point2);
                }
                
                $('#month-total-points').append(actual_total);
                
            });
        });
    });
    
    
    $('#monthly-performance-table').append(
        '<tr>' +  //<td>开始日期</td><td>结束日期</td>
        '<td style="width:7%">编号</td><td style="width:10%">项目标题</td><td style="width:5%">项目类别</td><td style="width:5%">难度</td><td style="width:5%">身份</td><td style="width:5%">项目总积分</td><td style="width:5%">今月项目预计得分</td><td style="width:5%">今月项目個人预计得分</td><td style="width:5%">今月实际个人总得分</td><td style="width:5%">貢獻度系數</td>' +
        '</tr>'
    );
    
    $('#basic-duties-approved').append(
        '<tr>' +
        '<td style="width:10%">编号</td><td style="width:25%">项目标题</td><td style="width:20%">项目类别</td><td style="width:20%">难度</td><td style="width:15%">更新日期</td><td style="width:%">积分</td>' +
        '</tr>'
    );
    
    $.get('/performance/get-users',function(users){
        $.get('/get-basic-duties',function(data){

            data = (Object.values(data));
            data = data.filter(element => element.status != 'delete');
            data.sort((a, b) => {
                const statusOrder = ['pending', 'rejected', 'approved', 'delete'];
                
                const aStatusIndex = statusOrder.indexOf( a.status );
                const bStatusIndex = statusOrder.indexOf( b.status );
            
                if ( aStatusIndex === bStatusIndex )
                    return ((a.timestamp < b.timestamp) ? 1 : -1);
            
                return aStatusIndex - bStatusIndex;
            });

            var color = "";
            var status = "";
            var hidden = "";
            
            data.forEach((element) => {
                element.member = users[element.member-1].name;
                switch(element.status){
                    case 'approved':
                        element.status = '通过';
                        break;
                    case 'rejected':
                        element.status = '未通过';
                        break;
                    case 'delete':
                        element.status = '结束';
                        break;
                    default:
                        element.status = '待审批';
                }
            });
            
            var columns = {
                basic_no: '编号',
                basic_content: '项目标题',
                type: '项目类别',
                member: '负责同事',
                status: '项目状态',
                id: '',
            }
            
            //console.log(userDetail);
            //console.log(notifications);
            var table = $('#root').tableSortable({
                data: data,
                columns: columns,
                searchField: '#searchField',
                rowsPerPage: 10,
                pagination: true,
                formatCell: function(row, key) {
                    if (key === 'basic_no') {
                        return $('<td style="width: 5%;"></td>').addClass('').text(row[key]);
                    }
                    if (key === 'basic_content') {
                        return $('<td style="width: 450px;"></td>').addClass('').text(row[key]);
                    }
                    if (key === 'type') {
                        return $('<td style="width: 15%;"></td>').addClass('').text(row[key]);
                    }
                    if (key === 'member') {
                        return $('<td style="width: 10%;"></td>').addClass('').text(row[key]);
                    }
                    if (key === 'status') {
                        switch(row[key]){
                            case '通过':
                                return $('<td style="width: 10%;"></td>').addClass('font-weight-bold table-success').text(row[key]);
                            case '未通过':
                                return $('<td style="width: 10%;"></td>').addClass('font-weight-bold table-danger').text(row[key]);
                            case '结束':
                                return $('<td style="width: 10%;"></td>').addClass('font-weight-bold table-secondary').text(row[key]);
                            default:
                                return $('<td style="width: 10%;"></td>').addClass('font-weight-bold table-warning').text(row[key]);
                        }
                    }
                    if (key === 'id') {
                        return (
                            '<td style="width: 30%;"><a href="/basic/' + row[key] + '"><button class="btn btn-secondary">查看</button></a>' + 
                            '<a href="/basic/edit/' + row[key] + '"><button class="btn btn-success">修改</button></a>' + 
                            '<a href="/basic/hide/' + row[key] + '" onclick="return confirm(\'是否确定要删除项目？\')"><button class="btn btn-danger">删除</button></a>' +
                            '<a href="/basic/approve/' + row[key] + '"><button class="btn btn-warning">通過</button></a></td>'
                            );
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
        });
    });
}