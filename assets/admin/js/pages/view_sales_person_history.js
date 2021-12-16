$(document).ready(function() {
  
    setTimeout(function(){
        view_sales_person_history();
    }, 1000);
    
});

function view_sales_person_history(){
    
    var salespersonrouteid = $("#salespersonrouteid").val();
    var memberid = ($("#memberid").val()!=null?$("#memberid").val():"");
   
    PNotify.removeAll();

    var uurl = SITE_URL+"sales-person-history/getSalesPersonHistoryByMember/";
    $.ajax({
        url: uurl,
        type: 'POST',
        data: {memberid:memberid,salespersonrouteid:salespersonrouteid},
        
        success: function(response){
            response = $.parseJSON(response);
            if(response.time_array.length==0){
                new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }
            initMap(response.time_array,response.icon_array,response.flightPlanlat_long_arr,response.markerlat_long_arr,response.lat_long_center_point,1,response.info_window);
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
        complete: function(){
            $('.mask').hide();
            $('#loader').hide();
        },
    });
  
}