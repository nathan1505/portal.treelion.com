window.onload = function(){

    $('#monthly-list-detail').append(
        '<tr>' +
        '<td width="10%">名字</td><td width="10%">今周编号（节点）</td><td width="10%">上周编号（节点）</td><td width="5%">今周预测分数</td><td width="5%">今周实际分数</td><td width="5%">上周预测分数</td><td width="5%">上周实际分数</td>' +
        '</tr>'
    );

    $.get('/monthly/list-of-point',function(element){
        element = (Object.values(element));
        element.sort((a, b) => (a.name > b.name) ? 1 : -1);
        //console.log(element);
        
        var temp = {};
	   
	    for(var i=0;i<element.length;i++){
	     
	       if(temp.hasOwnProperty(element[i].user_id)){
	           temp[element[i].user_id]['node_point'] += element[i].node_point;
	           temp[element[i].user_id]['node_point_lastweek'] += element[i].node_point_lastweek;
	           temp[element[i].user_id]['node_point_expected'] += element[i].node_point_expected;
	           temp[element[i].user_id]['node_point_expected_lastweek'] += element[i].node_point_expected_lastweek;
	           temp[element[i].user_id]['performance_no'] += ' '+element[i].performance_no+'';
	           temp[element[i].user_id]['performance_no_lastweek'] += ' '+element[i].performance_no_lastweek+'';
	           temp[element[i].user_id]['node_no'] += ' '+element[i].node_no+'';
	           temp[element[i].user_id]['node_no_lastweek'] += ' '+element[i].node_no_lastweek+'';
	       }else{
	           temp[element[i].user_id] = {
                    "node_point_lastweek": element[i].node_point_lastweek,
                    "node_point": element[i].node_point,
                    "node_point_expected": element[i].node_point_expected,
                    "node_point_expected_lastweek": element[i].node_point_expected_lastweek,
                    "user_id": element[i].user_id,
                    "performance_no": element[i].performance_no,
                    "node_no":element[i].node_no,
                    "date": element[i].date,
                    "performance_no_lastweek": element[i].performance_no_lastweek,
                    "node_no_lastweek": element[i].node_no_lastweek,
                    "date_lastweek": element[i].date_lastweek,
                    "name": element[i].name,
                    "role": element[i].role
                };
	           
	       }
	       
	   }
	   var temparr = [];
	   
	   for(var key in temp){
	       
	       temparr.push(temp[key]);
	   }
	   element = temparr;
	   console.log(element);
	   
        
        
        element.forEach((array) => {
            
            var this_week = "";
            var last_week = "";
            if(array.performance_no){
                var performance_no_array = array.performance_no.toString().split(' ');
                var node_no_array = array.node_no.toString().split(' ');
                
                for(var i=0;i<performance_no_array.length;i++){
                    if(performance_no_array[i] && node_no_array[i]){
                        this_week += performance_no_array[i] + "(" + node_no_array[i] + ")\n";// + " - " + array.date;
                    }
                }
                
            }
            if(array.performance_no_lastweek){
                var performance_no_lastweek_array = array.performance_no_lastweek.toString().split(' ');
                var node_no_lastweek_array = array.node_no_lastweek.toString().split(' ');
                
                for(var j=0;j<performance_no_lastweek_array.length;j++){
                    if(performance_no_lastweek_array[j] && node_no_lastweek_array[j]){
                        last_week += performance_no_lastweek_array[j] + "(" + node_no_lastweek_array[j] + ")\n";// + " - " + array.date_lastweek;
                    }
                }
            }
            $('#monthly-list-detail').append(
                '<tr>' +
                '<td width="10%">' + array.name + '</td>' +
                //'<td width="5%">' + array.role + '</td>' +
                '<td width="10%">' + this_week +'</td>' +
                '<td width="10%">' + last_week +'</td>' +
                '<td width="5%">' + Math.round(array.node_point_expected) + '</td>' +
                '<td width="5%">' + Math.round(array.node_point) + '</td>' +
                '<td width="5%">' + Math.round(array.node_point_expected_lastweek) + '</td>' +
                '<td width="5%">' + Math.round(array.node_point_lastweek) + '</td>' +
                '</tr>'
            );
        });
        
    });
}