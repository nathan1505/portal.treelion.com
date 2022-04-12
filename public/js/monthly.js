window.onload = function(){

    $('#monthly-list-detail').append(
        '<tr>' +
        '<td width="10%">名字</td>' +
        '<td width="10%">基础项目</td>' +
        '<td width="5%">基礎項目分数</td>' +
        '<td width="10%">业绩项目</td>' +
        '<td width="10%">上月业绩項目</td>' +
        '<td width="5%">业绩预测分数</td>' +
        '<td width="5%">业绩实际分数</td>' +
        '<td width="5%">预测总和分数</td>' +
        '<td width="5%">实际总和分数</td>' +
        '<td width="5%">上月业绩预测分数</td>' +
        '<td width="5%">上月业绩实际分数</td>' +
        '<td width="5%">上月预测总和分数</td>' +
        '<td width="5%">上月实际总和分数</td>' +
        '</tr>'
    );

    $.get('/monthly/list-of-point',function(data){
        data = (Object.values(data));
        data.sort((a, b) => (a.name > b.name) ? 1 : -1);
        console.log(data);
        data.forEach((element) => {
            $('#monthly-list-detail').append(
                '<tr>' +
                '<td width="10%">' + element.name + '</td>' +
                '<td width="10%">' + element.basic_no +'</td>' +
                '<td width="5%">' + element.basic_points +'</td>' +
                '<td width="10%">' + element.performance_no +'</td>' +
                '<td width="10%">' + element.performance_no_lastmonth +'</td>' +
                '<td width="5%">' + Math.round(element.point_expected) + '</td>' +
                '<td width="5%">' + Math.round(element.point) + '</td>' +
                '<td width="5%">' + Math.round(element.basic_points+element.point_expected) + '</td>' +
                '<td width="5%">' + Math.round(element.basic_points+element.point) + '</td>' +
                '<td width="5%">' + Math.round(element.point_expected_lastmonth) + '</td>' +
                '<td width="5%">' + Math.round(element.point_lastmonth) + '</td>' +
                '<td width="5%">' + Math.round(element.basic_points+element.point_expected_lastmonth) + '</td>' +
                '<td width="5%">' + Math.round(element.basic_points+element.point_lastmonth) + '</td>' +
                '</tr>'
            );
        });
    });
}