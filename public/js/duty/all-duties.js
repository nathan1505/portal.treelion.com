window.onload = function(){
    ShowStocks();

    $.get("/duties/generate-duties-table",
        function (data) {
            console.log(data);
        }
    );
    
}