window.onload = function(){

    $('#weekly-list-detail').append(
        '<tr>' +
        '<td width="10%">名字</td><td width="5%">身份</td><td width="10%">今周编号（节点）- 日期</td><td width="10%">上周编号（节点）- 日期</td><td width="10%">今周实际分数</td><td width="10%">上週实际分数</td>' +
        '</tr>'
    );

    $.get('/weekly/list-of-point',function(data){
        data = (Object.values(data));
        data.sort((a, b) => (a.name > b.name) ? 1 : -1);
        console.log(data);
        data.forEach((element) => {
            var this_week = "";
            var last_week = "";
            if(element.performance_no){
                this_week = element.performance_no + "(" + element.node_no + ")" + " - " + element.date 
            }
            if(element.performance_no_lastweek){
                last_week = element.performance_no_lastweek + "(" + element.node_no_lastweek + ")" + " - " + element.date_lastweek 
            }
            $('#weekly-list-detail').append(
                '<tr>' +
                '<td width="10%">' + element.name + '</td>' +
                '<td width="5%">' + element.role + '</td>' +
                '<td width="10%">' + this_week +'</td>' +
                '<td width="10%">' + last_week +'</td>' +
                '<td width="10%">' + Math.round(element.node_point) + '</td>' +
                '<td width="10%">' + Math.round(element.node_point_lastweek) + '</td>' +
                '</tr>'
            );
        });
    });
}