$(document).ready(function(){

  setTimeout(function(){
      track_sales_person(1);
  }, 1000);

  // var timer = null;
  // var interval = 10000;

  // timer = setInterval(function(){  
  //     var employeeid = $("#employeeid").val();
  //     var vehicleid = $("#vehicleid").val();
  //     var routeid = $("#routeid").val();
  //     var date = $("#date").val().trim();

  //     PNotify.removeAll();

      /* var uurl = SITE_URL+"sales-person-tracking/track-sales-person/";
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {employeeid:employeeid,vehicleid:vehicleid,routeid:routeid,date:date},
        
        success: function(response){
          response = $.parseJSON(response);
          initMap(response.time_array,response.icon_array,response.flightPlanlat_long_arr,response.markerlat_long_arr,response.lat_long_center_point,1,response.info_window);
          if(response.endroute==1){
            clearInterval(timer);
            timer = null;
          }
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
        
      }); */
   
  // }, interval);


  /****EMPLOYEE CHANGE EVENT****/
  $('#employeeid').on('change', function() { 
      // getvehicle(this.value);
      getroute(this.value);
  });

  $('#date').datepicker({
      todayHighlight: true,
      format: 'dd/mm/yyyy',
      todayBtn:"linked",
  });
});

function track_sales_person(type=0){

  var employeeid = $("#employeeid").val();
  var vehicleid = $("#vehicleid").val();
  var routeid = $("#routeid").val();
  var date = $("#date").val().trim();
  
  var isvalidemployeeid = 0;

  PNotify.removeAll();

  if(type==0){

    if(employeeid == 0){
      $("#employeeid_div").addClass("has-error is-focused");
      new PNotify({title: "Please select sales person !",styling: 'fontawesome',delay: '3000',type: 'error'});
    }else{
      $("#employeeid_div").removeClass("has-error is-focused");
      isvalidemployeeid = 1;
    }
  }


  if(isvalidemployeeid == 1){

    var uurl = SITE_URL+"sales-person-tracking/track-sales-person/";
    $.ajax({
        url: uurl,
        type: 'POST',
        data: {employeeid:employeeid,vehicleid:vehicleid,routeid:routeid,date:date},
        
        success: function(response){
            response = $.parseJSON(response);
            if(response.time_array.length==0){
                new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }
            // console.log(response.flightPlanlat_long_arr)
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
}


function view_multiple_followup(){

var fromdate=$("#fromdate").val().trim();
var todate=$("#todate").val().trim();
var spent_time=$("#spent_time").val();

var isvalidfromdate = isvalidtodate = 0 ; 

PNotify.removeAll();

if(fromdate == ''){
  $("#fromdate_div").addClass("has-error is-focused");
  new PNotify({title: 'Please Select From Date !',styling: 'fontawesome',delay: '3000',type: 'error'});
  isvalidfromdate = 0;
}else {
    isvalidfromdate = 1;
}

if(todate == ''){
  $("#todate_div").addClass("has-error is-focused");
  new PNotify({title: 'Please Select To Date !',styling: 'fontawesome',delay: '3000',type: 'error'});
  isvalidtodate = 0;
}else {
    isvalidtodate = 1;
}

if(isvalidfromdate==1 && isvalidtodate==1) {
  var uurl = SITE_URL+"daily-followup/viewmultiplefollowupmap/";
  var multiple_followup_checkbox=$("#multiple_followup_checkbox").val();
  $.ajax({
    url: uurl,
    type: 'POST',
    data: { fromdate : fromdate , todate : todate , spent_time : spent_time,multiple_followup_checkbox : multiple_followup_checkbox},
    beforeSend: function(){
      $('.mask').show();
      $('#loader').show();
    },
    success: function(response){
      if(response!="") {
        response = $.parseJSON(response);
      }
      var cntdata = Object.keys(response).length;
      if(cntdata==0){
        $("#map-error-message").text("Data not found");
      } else{
        $("#map-error-message").text(""); 
      } 
      initMap(response,1);
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
}

function getvehicle(employeeid){

  $("#vehicleid").find('option')
              .remove()
              .end()
              .val('0')
              .append('<option value="0">All Vehicle</option>')
            ;
  $("#vehicleid").selectpicker('refresh');
  $("#capacity").val("");

  if(employeeid!=0){
      var uurl = SITE_URL+"assigned-route/getVehicleByEmployeeId";
      $.ajax({
          url: uurl,
          type: 'POST',
          data: {employeeid:employeeid},
          dataType: 'json',
          async: false,
          success: function(response){
  
              for(var i = 0; i < response.length; i++) {
      
                  $("#vehicleid").append($('<option>', { 
                      value: response[i]['id'],
                      text : response[i]['name']
                  }));
              }
              
          },
          error: function(xhr) {
              //alert(xhr.responseText);
          },
      });
      $("#vehicleid").selectpicker('refresh');
  }
}
function getroute(employeeid){

  $("#routeid").find('option')
              .remove()
              .end()
              .val('0')
              .append('<option value="0">All Route</option>')
            ;
  $("#routeid").selectpicker('refresh');
  
  if(employeeid!=0){
      var uurl = SITE_URL+"assigned-route/getRouteByEmployee";
      $.ajax({
          url: uurl,
          type: 'POST',
          data: {employeeid:employeeid},
          dataType: 'json',
          async: false,
          success: function(response){

              for(var i = 0; i < response.length; i++) {
      
                  $("#routeid").append($('<option>', { 
                      value: response[i]['id'],
                      text : response[i]['route']
                  }));
              }
          },
          error: function(xhr) {
              //alert(xhr.responseText);
          },
      });
      $("#routeid").selectpicker('refresh');
  }
}