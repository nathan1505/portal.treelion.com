@extends('layouts.main')

@section('css')
    <link href="/css/main.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css" rel="stylesheet" />
    <style type="text/css">
        #myBtn {
          display: none; /* Hidden by default */
          position: fixed; /* Fixed/sticky position */
          bottom: 20px; /* Place the button at the bottom of the page */
          right: 30px; /* Place the button 30px from the right */
          z-index: 99; /* Make sure it does not overlap */
          border: none; /* Remove borders */
          outline: none; /* Remove outline */
          background-color: Green; /* Set a background color */
          color: white; /* Text color */
          cursor: pointer; /* Add a mouse pointer on hover */
          padding: 15px; /* Some padding */
          border-radius: 10px; /* Rounded corners */
          font-size: 18px; /* Increase font size */
        }
        
        #myBtn:hover {
          background-color: #555; /* Add a dark-grey background on hover */
        }
    </style>
@endsection

@section('script')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/i18n/defaults-zh_CN.min.js"></script>
    <script src="{{ URL::asset('js/monthly.js') }}"></script>
@endsection

@section('content')
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
    <div class="container-fluid" style="margin-top:2%; align-content: center">
         <!--Row 1-->
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        本月基础项目得分
                        <input type="month" id="yearmonth" name="yearmonth" value="">
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <tbody id="monthly-list-topic"></tbody>
                            <tbody id="monthly-list-detail"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--Row 1 ends-->
    </div>
    <script>
    
        let date= new Date()
        let month=("0" + (date.getMonth() + 1)).slice(-2)
        let year=date.getFullYear()
        document.getElementById("yearmonth").value = `${year}-${month}`;
        
        const yearmonth = document.getElementById("yearmonth").value;
        
        $(document).ready(function(){
            $("#yearmonth").on("input", function(){
                // Print entered value in a div box
                var yearmonth = document.getElementById("yearmonth").value;
                //console.log(yearmonth);
                 $.ajax({
                    url: '/monthly/list-of-point/'+yearmonth,
                    type: "get",
                    //data: {'yearmonth':yearmonth},
                    success: function (response) {
                        //console.log(response);
                        $('#monthly-list-detail').empty();
                        data = (Object.values(response));
                        //data.sort((a, b) => (a.name > b.name) ? 1 : -1);
                        //console.log(data);
                        data.forEach((element) => {
                            $('#monthly-list-detail').append(
                                '<tr>' +
                                '<td width="5%">' + element.name + '</td>' +
                                '<td width="5%">' + element.basic_points +'</td>' +
                                '<td width="5%">' + element.basic_points_actual +'</td>' +
                                '<td width="10%">' + element.performance_no +'</td>' +
                                '<td width="5%">' + Math.round(element.point) + '</td>' +
                                '<td width="5%">' + Math.round(element.basic_points_actual+element.point) + '</td>' +
                                '<td width="5%">' + element.attendance + '</td>' +
                                '<td width="5%">' + element.basic_points_distribute +'</td>' +
                                '<td width="5%">' + Math.round(element.total) + '</td>' +
                                '</tr>'
                            );
                        });
                    },
                    error: function(response){
                        alert('Error'+response);
                    }
                });
            });
        });

        //Get the button
        var mybutton = document.getElementById("myBtn");
        
        // When the user scrolls down 20px from the top of the document, show the button
        window.onscroll = function() {scrollFunction()};
        
        function scrollFunction() {
          if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            mybutton.style.display = "block";
          } else {
            mybutton.style.display = "none";
          }
        }
        
        // When the user clicks on the button, scroll to the top of the document
        function topFunction() {
          document.body.scrollTop = 0;
          document.documentElement.scrollTop = 0;
        }
    </script>
@endsection