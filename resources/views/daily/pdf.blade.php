<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>员工日内工作申报单</title>

    <style>
        div {
            text-align: center;
        }
        div:after {
            content: '';
            width: 0;
            height: 100%;
            display: inline-block;
            vertical-align: middle;
        }
        table{
        border-collapse:collapse;
        float: left;
        }
        table tr td
        {border:1px solid black;}
    </style>

</head>
<body>

    <div style="margin-top:30px; margin-bottom: 30px">

        <h2>员工日内工作申报单</h5>
        <h4>申报人：{{$name}} &nbsp&nbsp&nbsp&nbsp 申报时间：{{$timestamp}}</h4>
        
        <table style="width: 75%; margin:auto; line-height:30px">
            <tbody>
                <tr><td colspan="4">9:00 - 10:00</td></tr>
                <tr>
                    <td style="width:25%">项目编号</td>
                    <td style="width:25%">{{$performance_no_0}}</td>
                    <td style="width:25%">组长</td>
                    <td style="width:25%">{{$leader_0}}</td>
                </tr>
                <tr>
                    <td style="width:25%">项目标题</td>
                    <td colspan="3">{{$content_0}}</td>
                </tr>
                <tr>
                    <td style="width:25%">完成度</td>
                    <td style="width:25%">{{$completeness_0}}%</td>
                    <td style="width:25%">自我评价</td>
                    <td style="width:25%">{{$comment_0}}</td>
                </tr>
                
                <tr><td colspan="4">10:00 - 11:00</td></tr>
                <tr>
                    <td style="width:25%">项目编号</td>
                    <td style="width:25%">{{$performance_no_1}}</td>
                    <td style="width:25%">组长</td>
                    <td style="width:25%">{{$leader_1}}</td>
                </tr>
                <tr>
                    <td style="width:25%">项目标题</td>
                    <td colspan="3">{{$content_1}}</td>
                </tr>
                <tr>
                    <td style="width:25%">完成度</td>
                    <td style="width:25%">{{$completeness_1}}%</td>
                    <td style="width:25%">自我评价</td>
                    <td style="width:25%">{{$comment_1}}</td>
                </tr>
                <tr><td colspan="4">11:00 - 12:00</td></tr>
                <tr>
                    <td style="width:25%">项目编号</td>
                    <td style="width:25%">{{$performance_no_2}}</td>
                    <td style="width:25%">组长</td>
                    <td style="width:25%">{{$leader_2}}</td>
                </tr>
                <tr>
                    <td style="width:25%">项目标题</td>
                    <td colspan="3">{{$content_2}}</td>
                </tr>
                <tr>
                    <td style="width:25%">完成度</td>
                    <td style="width:25%">{{$completeness_2}}%</td>
                    <td style="width:25%">自我评价</td>
                    <td style="width:25%">{{$comment_2}}</td>
                </tr>

                <tr><td colspan="4">14:00 - 15:00</td></tr>
                <tr>
                    <td style="width:25%">项目编号</td>
                    <td style="width:25%">{{$performance_no_3}}</td>
                    <td style="width:25%">组长</td>
                    <td style="width:25%">{{$leader_3}}</td>
                </tr>
                <tr>
                    <td style="width:25%">项目标题</td>
                    <td colspan="3">{{$content_3}}</td>
                </tr>
                <tr>
                    <td style="width:25%">完成度</td>
                    <td style="width:25%">{{$completeness_3}}%</td>
                    <td style="width:25%">自我评价</td>
                    <td style="width:25%">{{$comment_3}}</td>
                </tr>
                
                <tr><td colspan="4">15:00 - 16:00</td></tr>
                <tr>
                    <td style="width:25%">项目编号</td>
                    <td style="width:25%">{{$performance_no_4}}</td>
                    <td style="width:25%">组长</td>
                    <td style="width:25%">{{$leader_4}}</td>
                </tr>
                <tr>
                    <td style="width:25%">项目标题</td>
                    <td colspan="3">{{$content_4}}</td>
                </tr>
                <tr>
                    <td style="width:25%">完成度</td>
                    <td style="width:25%">{{$completeness_4}}%</td>
                    <td style="width:25%">自我评价</td>
                    <td style="width:25%">{{$comment_4}}</td>
                </tr>
                
                <tr><td colspan="4">16:00 - 17:00</td></tr>
                <tr>
                    <td style="width:25%">项目编号</td>
                    <td style="width:25%">{{$performance_no_5}}</td>
                    <td style="width:25%">组长</td>
                    <td style="width:25%">{{$leader_5}}</td>
                </tr>
                <tr>
                    <td style="width:25%">项目标题</td>
                    <td colspan="3">{{$content_5}}</td>
                </tr>
                <tr>
                    <td style="width:25%">完成度</td>
                    <td style="width:25%">{{$completeness_5}}%</td>
                    <td style="width:25%">自我评价</td>
                    <td style="width:25%">{{$comment_5}}</td>
                </tr>        

                <tr><td colspan="4">17:00 - 18:00</td></tr>
                <tr>
                    <td style="width:25%">项目编号</td>
                    <td style="width:25%">{{$performance_no_6}}</td>
                    <td style="width:25%">组长</td>
                    <td style="width:25%">{{$leader_6}}</td>
                </tr>
                <tr>
                    <td style="width:25%">项目标题</td>
                    <td colspan="3">{{$content_6}}</td>
                </tr>
                <tr>
                    <td style="width:25%">完成度</td>
                    <td style="width:25%">{{$completeness_6}}%</td>
                    <td style="width:25%">自我评价</td>
                    <td style="width:25%">{{$comment_6}}</td>
                </tr>

            </tbody>
        </table>


    </div>
    
</body>
</html>