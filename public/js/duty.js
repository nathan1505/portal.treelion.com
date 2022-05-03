var typeCoefficientsList = [1.8, 1.4, 1.0, 0.8];
var difficultyCoefficientsList = [1.5, 1.0, 0.8];
var completenessCoefficientsList = [1.5, 1.2, 1.0, 0.8, 0.6, 0.4];

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
    for (user in array) {
        if (array[user].id == id)
            return array[user].name;
    }
}

function filterByIdBoolean(array, id) {
    for (user in array) {
        if (array[user].id == id)
            return true;
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

/**
 * @description parse the string of duty members to the array of members' id
 * @param {String} str 
 * @returns array of members' id
 */
function GetMemberId (str){
    var numArr = str.match(/\d+/g);
    for (i in numArr){
        numArr[i] = parseInt(numArr[i]);
    }
    return numArr;
}

/**
 * Generate the string of all members' names
 * @param {*} userObject 
 * @param {*} memberIdArray 
 * @returns A string of all members' names
 */
function GetMemberNameString(userObject, memberIdArray){
    memberstr = "";
    for (i in memberIdArray) {
        memberstr += filterById(userObject, memberIdArray[i]);
        memberstr += "，"
    }
    memberstr = memberstr.substring(0, memberstr.length - 1);
    return memberstr;
}

/**
 * @description Return current data in format "YYYY-MM-DD"
 * @return {String} Date in "YYYY-MM-DD"
 */
function CurrentDate(){
    // 获取当前日期
    var date = new Date();

    // 获取当前月份
    var nowMonth = date.getMonth() + 1;

    // 获取当前是几号
    var strDate = date.getDate();

    // 添加分隔符“-”
    var seperator = "-";

    // 对月份进行处理，1-9月在前面添加一个“0”
    if (nowMonth >= 1 && nowMonth <= 9) {
        nowMonth = "0" + nowMonth;
    }

    // 对月份进行处理，1-9号在前面添加一个“0”
    if (strDate >= 0 && strDate <= 9) {
        strDate = "0" + strDate;
    }

    // 最后拼接字符串，得到一个格式为(yyyy-MM-dd)的日期
    return date.getFullYear() + seperator + nowMonth + seperator + strDate;
}

window.onload = function(){
    ShowStocks();

    //Add the @csrf-token for dynamically generated form
    var token = $('meta[name="csrf-token"]').attr('content');

    //Initialize the variables of Current Performance ID and the request path
    var dutyId = GetCurrentDutyId();
    var path = "/get-duty-detail/"+dutyId;

    //GET the detail of performance duty 
    //response data as the duty detail
    $.get(path, function(data){
        var disableTrue = "";
        $.get('/get-user',function(userDetail){
            if(!(userDetail.id == data[0].leader || userDetail.id == data[0].declarant_id || data[0].members.includes(userDetail.id)) && userDetail.role == "employee"){
                disableTrue = "disabled=\"true\"";
            }
            
            //console.log(data[0].members.includes(userDetail.id));
    
            //determine the color of difficulty, etc.
            var difficultyColor = "";
            var difficulty = "";
            var statusColor = "";
            var status = "";
    
            switch (data[0].difficulty) {
                case "difficult":
                    difficultyColor = "red";
                    difficulty = "困难";
                    break;
                case "normal":
                    difficultyColor = "#D4AC0D";
                    difficulty = "中等";
                    break;
                default:
                    difficultyColor = "green";
                    difficulty = "简单";
                    break;
            }
    
            switch (data[0].status) {
                case "postponed":
                    status = "暂缓";
                    statusColor = "#6c757d";
                    break;
                case "processing":
                    status = "进行中";
                    statusColor = "#28a745";
                    break;
                case "done":
                    status = "已完成";
                    statusColor = "#007bff";
                    break;
                case "rejected":
                    status = "审批未通过";
                    statusColor = "#dc3545";
                    break;
                case "delayed":
                    status = "延迟";
                    statusColor = "#dc3545";
                    break;
                //Case 'pending'
                default:
                    status = "待审批";
                    statusColor = "#fd7e14";
                    break;
            }
    
            $('#duty-header').append(
                '<h5 style="display: inline - block; float: left;">业绩事项&nbsp' + data[0].performance_no +"</h5>"
            );
    
            $('#duty-table').append(
                '<tr><td style="width:15%">项目编号</td><td style="width:35%">' + data[0].performance_no + '</td><td style="width:15%">难度</td><td><span style="color:' + difficultyColor + '">'+ difficulty + '</span></td></tr>' +
                '<tr><td>项目内容</td><td colspan=3>' + data[0].performance_content + '</td></tr>' +
                '<tr><td>项目类别</td><td>' + data[0].type + '</td><td>类别属性</td><td>' + data[0].property + '</td></tr>'
            );
            
            //Append leader and members
            memberIdArray = GetMemberId(data[0].members);
            $.when(getUserArray()).done(function(response){
                var second_leader = (filterById(response, data[0].second_leader) === undefined) ? "" : filterById(response, data[0].second_leader) ;
                $('#duty-table').append(
                    '<tr><td>组长</td><td>' + filterById(response, data[0].leader) + '</td>' +
                    '<td>第二组长</td><td>' + second_leader + '</td></tr>' +
                    '<tr><td>组员</td><td colspan=3>' + GetMemberNameString(response, memberIdArray) + '</td></tr>'+
                    '<tr><td>开始日期</td><td>' + data[0].start_date + '</td><td>结束日期</td><td>' + data[0].end_date + '</td></tr>' +
                    '<tr><td>节点数</td><td>' + data[0].node_no + '</td><td>完成度</td><td>' + data[0].completeness + '%</td></tr>' +
                    '<tr><td>基础赋分</td><td>' + Math.round(data[0].basic_points) + '</td><td>已获得积分</td><td>' + Math.round(data[0].gained_points) + '</td></tr>' +
                    '<tr><td>最新进展</td><td colspan=3>' + data[0].latest_progress + '</td></tr>' +
                    '<tr><td>状态</td><td><span style="color:'+ statusColor +'">' + status + '</td><td>申报人</td><td>' + filterById(response, data[0].declarant_id) + '</td></tr>'
                );
    
                $('#check-duty').append(
                    '<input hidden name="performance_id" value="'+ data[0].id +'">'
                );
                
                if (data[0].status != "pending" && (userDetail.id == data[0].leader || userDetail.id == data[0].declarant_id || userDetail.role == "admin")){
                    $('#duty-footer').remove();
                }
    
                if (data[0].status != "pending" && userDetail.role == "admin"){
                    $("#duty-change-status").append(
    
                        '<div class="form-group">' +
                            '<form method="post" action="check-duty" id="check-duty">' +
                            '<input hidden name="performance_id" value="' + data[0].id + '">' +
                            '<input name="_token" value="' + token + '" hidden>' +
    
                                '<div class="row">' + 
                                    '<div class="col-8">' +
                                        '<select class="form-control" required name="check">' +
                                            '<option selected disable hidden>更改工作状态</option>' +
                                            '<option value="processing">进行中</option>' +
                                            '<option value="done">已完成</option>' +
                                            '<option value="delayed">延迟</option>' +
                                            '<option value="postponed">暂缓</option>' +
                                        '</select>' +
                                    '</div>' +
    
                                    '<div class="col">' + 
                                        '<button class="btn btn-success" type="submit">更改</button>' +
                                    '</div>' +
                                '</div>' +
                            '</from>' +
                        '</div>'
                    );
                }
            });
    
            var typeCoefficient = 1.0;
            switch (data[0].type) {
                case '一类积分':
                    typeCoefficient = typeCoefficientsList[0];
                    break;
                case '二类积分':
                    typeCoefficient = typeCoefficientsList[1];
                    break;
                case '三类积分':
                    typeCoefficient = typeCoefficientsList[2];
                    break;
                case '四类积分':
                    typeCoefficient = typeCoefficientsList[3];
                    break;
            }
    
            var difficultyCoefficient = 1.0;
            switch (data[0].difficulty){
                case 'difficult':
                    difficultyCoefficient = difficultyCoefficientsList[0];
                    break;
                case 'normal':
                    difficultyCoefficient = difficultyCoefficientsList[1];
                    break;
                case 'easy':
                    difficultyCoefficient = difficultyCoefficientsList[2];
                    break;
            }
    
            //Show the tabel of coefficients
            $('#point-table-body').append(
                '<tr>' +
                '<td style="width:50%">基础分值</td><td>18</td>' +
                '</tr><tr>' +
                '<td>类别系数</td><td>' + typeCoefficient + '</td>' +
                '</tr><tr>' +
                '<td>难易系数</td><td>' + difficultyCoefficient + '</td>' +
                '</tr><tr>' +
                '<td>基础赋分</td><td>' + Math.round(data[0].basic_points) + '</td>' +
                '</tr>'
            );
    
            $('#gained-table-body').append(
                '<tr>' +
                '<td>已获取积分</td>' +
                '<td>' + Math.round(data[0].gained_points) + '</td>' +
                '</tr><tr>' +
                '<td>组长分配</td><td>' + Math.round(data[0].leader_points) + '</td>' +
                '</tr><tr>' +
                '<td>组员分配</td><td>' + Math.round(data[0].member_points) + '</td>' +
                '</tr>'
            );
    
            performance_no = data[0].performance_no;
            //Start of request of nodes
            path = "/get-nodes/" + performance_no;
            $.get(path, data,
                function (data, textStatus, jqXHR) {
                    //console.log(data);
    
                    for (i in data){
                        var cdate = "";
                        var hidden = "";
                        var finish_title = "节点完成度";
                        if(data[i].confirmed_date && data[i].node_completeness == 100){
                            cdate = data[i].confirmed_date;
                        }
                        if(data[i].node_completeness == 100){
                            hidden = "visibility: hidden;";
                            finish_title = "节点已完成";
                        }

                        //append the completeness table
                        $('#nodes-table-body').append(
                            '<tr>' +
                            '<td>节点#' + data[i].node_id + '</td><td>' + data[i].node_point_percentage + '%</td>' +
                            '<td>' + data[i].node_completeness_coefficient +'</td>' +
                            '</tr>'
                        );
    
                        $('#nodes-row').append(
                            '<div class="col-6">' +
                            '<div class="card" id="duty_card_' + i + '1" style="margin-top:20px">' +
                            '<div class="card-header" id="duty_card_header_' + i + '">' +
                            '<h5>节点#' + data[i].node_id + '&nbsp&nbsp&nbsp&nbsp节点完成度: ' + data[i].node_completeness + '%</h5>' +
                            '</div>' +
                            '<div class="card-body" id="duty_card_body_' + i + '">' +
                            //Print the table of nodes info

                            '<table class="table table-bordered">' +
                                '<tbody>' +
                                    '<tr><td>积分比例</td><td>' +  data[i].node_point_percentage + '%</td></tr>' +
                                    '<tr><td>考核时间</td><td>' + data[i].node_date + '</td></tr>' +
                                    '<tr><td>节点目标</td><td>' + data[i].node_goal + '</td></tr>' +
                                '</tbody>' +
                            '</table>'+
                            '<div class="scrollit">' +
                                '<table class="table table-bordered">' +
                                    '<tbody>' +
                                        '<tr><td>节点进展</td><td>' + data[i].node_progress + '</td></tr>' +
                                    '</tbody>' +
                                '</table>' +
                            '</div>' +
    
                            '<form action="post-node" method="post">' +
                                '<div class="form-group">' +
                                    '<input type="input" hidden name="current_date" value="' + CurrentDate() + '"></input>' +
                                    '<div class="row" style="margin-top:10px">' +
                                        '<div class="col">' +
                                            '<label for="completeness">' + finish_title + '</label><br>' +
                                            '<select id="completeness" name="completeness" type="input" class="form-control" style="float:left;width:20%;">' +
                                                '<option value=0>未完成</option>' +
                                                '<option value=100>已完成</option>' +
                                            '</select>' +
                                        '</div>' +
                                    '</div>' +
                                    '<div class="row" style="margin-top:15px">' +
                                        '<div class="col">' +
                                            '<label for="finish-date">更新日期：' + cdate + '</label>' +
                                            '<input type="date" name="finish-date" id="finish-date" class="form-control" value="' + data[i].confirmed_date+'" required>' +
                                        '</div>' +
                                    '</div>' +
                                    '<div class="row" style="margin-top:15px">' +
                                        '<div class="col">' +
                                            '<label for="progress">节点进展描述</label>' +
                                            '<textarea type="input" name="progress" class="form-control" rows="3" required></textarea>' +
                                            '<input name="performance_no" value="'+ data[i].duty_performance_no +'" hidden>' +
                                            '<input name="node_id" value="' + data[i].node_id + '" hidden>' +
                                            '<input name="_token" value="' + token + '" hidden>' +
                                        '</div>' +
                                    '</div>' +
                                    '<div class="row" style="margin-top:15px">' +
                                        '<div class="col" class="node-update-submit-div">' +
                                            '<button type="submit" class="btn btn-warning node-update-submit-button" ' + disableTrue + '>更新完成</button>' +
                                        '</div>' +
                                    '</div>' +
                                '</div>' +
                            '</form>' +
                            '</div>' +
                            '</div>' +
                            '</div>'
                        );
                        
                        $('#nodes-row-employee').append(
                            '<div class="col-6">' +
                            '<div class="card" id="duty_card_' + i + '1" style="margin-top:20px">' +
                            '<div class="card-header" id="duty_card_header_' + i + '">' +
                            '<h5>节点#' + data[i].node_id + '&nbsp&nbsp&nbsp&nbsp节点完成度: ' + data[i].node_completeness + '%</h5>' +
                            '</div>' +
                            '<div class="card-body" id="duty_card_body_' + i + '">' +
                            //Print the table of nodes info
                            '<table class="table table-bordered">' +
                                '<tbody>' +
                                    '<tr><td>积分比例</td><td>' +  data[i].node_point_percentage + '%</td></tr>' +
                                    '<tr><td>考核时间</td><td>' + data[i].node_date + '</td></tr>' +
                                    '<tr><td>节点目标</td><td>' + data[i].node_goal + '</td></tr>' +
                                '</tbody>' +
                            '</table>'+
                            '<div class="scrollit">' +
                                '<table class="table table-bordered">' +
                                    '<tbody>' +
                                        '<tr><td>节点进展</td><td>' + data[i].node_progress + '</td></tr>' +
                                    '</tbody>' +
                                '</table>' +
                            '</div>' +
    
                            '<form action="post-node" method="post">' +
                                '<div class="form-group">' +
                                    '<input type="input" hidden name="current_date" value="' + CurrentDate() + '"></input>' +
                                    '<div class="row" style="margin-top:10px">' +
                                        '<div class="col">' +
                                            '<label for="completeness">' + finish_title + '</label><br>' +
                                            '<select id="completeness" name="completeness" type="input" class="form-control" style="float:left;width:20%;' + hidden + '">' +
                                                '<option value=0>未完成</option>' +
                                                '<option value=100>已完成</option>' +
                                            '</select>' +
                                        '</div>' +
                                    '</div>' +
                                    '<div class="row" style="margin-top:15px">' +
                                        '<div class="col">' +
                                            '<label for="finish-date">更新日期：' + cdate + '</label>' +
                                            '<input type="date" name="finish-date" id="finish-date" class="form-control" value="" style="visibility: hidden" readonly>' +
                                        '</div>' +
                                    '</div>' +
                                    '<div class="row" style="margin-top:15px">' +
                                        '<div class="col">' +
                                            '<label for="progress">节点进展描述</label>' +
                                            '<textarea type="input" name="progress" class="form-control" rows="3" required></textarea>' +
                                            '<input name="performance_no" value="'+ data[i].duty_performance_no +'" hidden>' +
                                            '<input name="node_id" value="' + data[i].node_id + '" hidden>' +
                                            '<input name="_token" value="' + token + '" hidden>' +
                                        '</div>' +
                                    '</div>' +
                                    '<div class="row" style="margin-top:15px">' +
                                        '<div class="col" class="node-update-submit-div">' +
                                            '<button type="submit" class="btn btn-warning node-update-submit-button" ' + disableTrue + 'style="' + hidden + '">更新完成</button>' +
                                        '</div>' +
                                    '</div>' +
                                '</div>' +
                            '</form>' +
                            '</div>' +
                            '</div>' +
                            '</div>'
                        );
                    }
                },
            );
        });

        /*
        console.log(data);
        console.log(data[0].status);
        if (data[0].status != "processing"){
            $('.node-update-submit-button').remove();
            $('.node-update-submit-div').remove();
        }
        */
        

    });
    //End of request of Duty
}