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
        console.log(data);
        console.log(token);
        
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
                '<p class="card-text">更新日期 : ' + data[0].timestamp + '</p>'
            );
        });

    });
}