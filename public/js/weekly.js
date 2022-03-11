window.onload = function(){

    $('#weekly-list-detail').append(
        '<tr>' +
        '<td width="20%">编号</td><td width="20%">名字</td><td width="20%">身份</td><td width="20%">节点</td><td width="20%">实际分数</td>' +
        '</tr>'
    );

    $.get('/weekly/list-of-point',function(data){
        //console.log(data);
        data = (Object.values(data));
        data.sort((a, b) => (a.name > b.name) ? 1 : -1);
        data.forEach((element) => {
            $('#weekly-list-detail').append(
                '<tr>' +
                '<td width="20%">' + element.performance_no + '</td>' +
                '<td width="20%">' + element.name + '</td>' +
                '<td width="20%">' + element.role + '</td>' +
                '<td width="20%">' + element.node_no + '</td>' +
                '<td width="20%">' + element.node_point + '</td>' +
                '</tr>'
            );
        });
    });
}