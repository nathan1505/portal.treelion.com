window.onload = function(){

    $('#monthly-list-topic').append(
        '<tr>' +
        '<td width="5%">名字</td>' +
        //'<td width="10%">基础项目</td>' +
        '<td width="5%">原本基础赋分</td>' +
        '<td width="5%">本月基础考核得分</td>' +
        '<td width="10%">业绩项目</td>' +
        //'<td width="10%">上月业绩項目</td>' +
        //'<td width="5%">本月业绩预测得分</td>' +
        '<td width="5%">本月业绩考核得分</td>' +
        //'<td width="5%">本月基础＋业绩预测得分</td>' +
        '<td width="5%">本月基础＋业绩考核得分</td>' +
        '<td width="5%">本月考勤分数</td>' +
        '<td width="5%">补助基础赋分</td>' +
        '<td width="5%">最终得分</td>' +
        //'<td width="5%">上月业绩实际分数</td>' +
        //'<td width="5%">上月实际总和分数</td>' +
        '</tr>'
    );

    $.get('/monthly/list-of-point/'+yearmonth,function(data){
        //data = (Object.values(data));
        //data.sort((a, b) => (a.id > b.id) ? 1 : -1);
        //console.log(data);
        data.forEach((element) => {
        var sum = element.basic_points_actual+element.point; 
        
            $('#monthly-list-detail').append(
                '<tr>' +
                '<td width="5%">' + element.name + '</td>' +
                //'<td width="10%">' + element.basic_no +'</td>' +
                '<td width="5%">' + Math.round(element.basic_points) +'</td>' +
                '<td width="5%">' + Math.round(element.basic_points_actual) +'</td>' +
                '<td width="10%">' + element.performance_no +'</td>' +
                //'<td width="10%">' + element.performance_no_lastmonth +'</td>' +
                //'<td width="5%">' + Math.round(element.point_expected) + '</td>' +
                '<td width="5%">' + element.point + '</td>' +
                //'<td width="5%">' + Math.round(element.total_expected) + '</td>' +
                '<td width="5%">' + Math.round(sum) + '</td>' +
                '<td width="5%">' + element.attendance + '</td>' +
                '<td width="5%">' + Math.round(element.basic_points_distribute) +'</td>' +
                '<td width="5%">' + Math.round(element.total)+element.dist + '</td>' +
                //'<td width="5%">' + Math.round(element.point_lastmonth) + '</td>' +
                //'<td width="5%">' + Math.round(element.basic_points+element.point_lastmonth) + '</td>' +
                '</tr>'
            );
        });
    });
}