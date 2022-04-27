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
                '<p class="card-text">项目内容 : ' + data[0].basic_content + '</p>' +
                '<p class="card-text">项目类别 : ' + data[0].type + '</p>' +
                '<p class="card-text">难度 : ' + diff + '</p>' +
                '<p class="card-text">状态 : ' + data[0].status + '</p>' +
                '<p class="card-text">积分 : ' + data[0].total_points + '</p>' +
                '<p class="card-text">开始日期 : ' + data[0].timestamp + '</p>'
            );
        });

    });
    
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
        $('#month-basic-points').append(Math.round(total_point1));
        
        //$('#month-total-points').val() += total_point1;
        
    });
        
    $.get('/get-monthly-performance', function (data){
        var role;
        var userID = dutyId;
        var total_point2 = 0.0;
        //console.log(data);
        for (var i = 0; i < data.length; i++){
            var points;
            var whole_project;
            
            switch (data[i].difficulty) {
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
            
            if(data[i].leader == userID){
                role = "组长";
                points = data[i].leader_month_actual;
                whole_project = data[i].leader_points;
            }
            else{
                role = "组員";
                points = data[i].member_month_actual;
                whole_project = data[i].member_points;
            }
            
            $('#monthly-performance-table').append(
                '<tr><td style="width:7%">' + data[i].performance_no + '</td>' + 
                '<td style="width:10%">' + data[i].performance_content + '</td>' + 
                '<td style="width:5%">' + data[i].type + '</td>' + 
                '<td style="width:5%">' + diff + '</td>' + 
                '<td style="width:5%">' + role + '</td>' + 
                '<td style="width:5%">' + Math.round(data[i].basic_points) + '</td>' + 
                '<td style="width:5%">' + Math.round(data[i].this_month) + '</td>' + 
                '<td style="width:5%">' + Math.round(whole_project) + '</td>' + 
                '<td style="width:5%">' + data[i].profit_coefficient.toFixed(1) + '</td>' + 
                
                '</tr>'
            );
            
            total_point2+=Math.round(points);

        }
        $('#month-performance-points').append(total_point2);

    });
    
    $.get('/get-total-monthly', function (data){
        $('#month-total-points').append(Math.round(data));
    });
    
    
    $('#monthly-performance-table').append(
        '<tr>' +  //<td>开始日期</td><td>结束日期</td>
        '<td style="width:7%">编号</td><td style="width:10%">项目内容</td><td style="width:5%">项目类别</td><td style="width:5%">难度</td><td style="width:5%">身份</td><td style="width:5%">项目总积分</td><td style="width:5%">今月项目预计得分</td><td style="width:5%">项目实际个人总得分</td><td style="width:5%">貢獻度系數</td>' +
        '</tr>'
    );
    
    $('#basic-duties-approved').append(
        '<tr>' +
        '<td style="width:10%">编号</td><td style="width:25%">项目内容</td><td style="width:20%">项目类别</td><td style="width:20%">难度</td><td style="width:15%">更新日期</td><td style="width:%">积分</td>' +
        '</tr>'
    );
    
    $.get('/performance/get-users',function(users){
        $.get('/get-basic-duties',function(data){
            var color = "";
            var status = "";
            var hidden = "";
            
            data.forEach((element) => {
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
                basic_content: '项目内容',
                member: '申报人',
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
                    if (key === 'status') {
                        switch(row[key]){
                            case '通过':
                                return $('<td></td>').addClass('font-weight-bold table-success').text(row[key]);
                            case '未通过':
                                return $('<td></td>').addClass('font-weight-bold table-danger').text(row[key]);
                            case '结束':
                                return $('<td></td>').addClass('font-weight-bold table-secondary').text(row[key]);
                            default:
                                return $('<td></td>').addClass('font-weight-bold table-warning').text(row[key]);
                        }
                    }
                    if (key === 'member') {
                        return users[row[key]].name;
                    }
                    if (key === 'id') {
                        return (
                            '<td><a href="/basic/' + row[key] + '"><button class="btn btn-secondary">查看</button></a>' + 
                            '<a href="/basic/edit/' + row[key] + '"><button class="btn btn-success">修改</button></a>' + 
                            '<a href="/basic/hide/' + row[key] + '" onclick="return confirm(\'是否确定要删除项目？\')"><button class="btn btn-danger">删除</button></a><td>'
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
        });
    });
}