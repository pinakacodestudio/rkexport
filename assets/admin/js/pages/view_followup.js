$(document).ready(function(){

    $('input[name="datefilter"]').daterangepicker({
      timePicker: true,
      locale: {
        format: 'DD/MM/YYYY hh:mm A'
      },
      ranges: {
          'Today': [moment(), moment()],
          'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days': [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month': [moment().startOf('month'), moment().endOf('month')],
          'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      },
    });

    var timer = null;
    var interval = 5000;

    timer = setInterval(function(){  
      var datefilter=$("#datefilter").val().trim();
      var spent_time=$("#spent_time").val();
      var taskid=$("#taskid").val();

      var isvaliddatefilter = isvalidtaskid = 0 ;
    
      PNotify.removeAll();

      if(datefilter != ''){
        isvaliddatefilter = 1;
      }
      if(taskid != 0){
        isvalidtaskid = 1;
      }

      if(isvaliddatefilter==1 && isvalidtaskid==1){

        var uurl = SITE_URL+"daily-followup/view-followup-map/"+followupid;
        $.ajax({
          url: uurl,
          type: 'POST',
          data: {datefilter:datefilter,spent_time:spent_time,taskid:taskid},
          
          success: function(response){
            response = $.parseJSON(response);
            initMap(response.time_array,response.icon_array,response.flightPlanlat_long_arr,response.markerlat_long_arr,response.lat_long_center_point,1);
            if(response.endtask==1){
              clearInterval(timer);
              timer = null;
            }
          },
          error: function(xhr) {
          //alert(xhr.responseText);
          },
          
        });
      }
    }, interval);

});

function view_followup(){

	var datefilter=$("#datefilter").val().trim();
  var spent_time=$("#spent_time").val();
  var taskid=$("#taskid").val();

  var isvaliddatefilter = isvalidtaskid = 0 ;
   
  PNotify.removeAll();

  if(datefilter == ''){
    $("#datefilter_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select date !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }else {
    $("#datefilter_div").removeClass("has-error is-focused");
    isvaliddatefilter = 1;
  }

  if(taskid == 0){
    $("#taskid_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select task !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }else {
    $("#taskid_div").removeClass("has-error is-focused");
    isvalidtaskid = 1;
  }

  if(isvaliddatefilter==1 && isvalidtaskid==1){

    var uurl = SITE_URL+"daily-followup/view-followup-map/"+followupid;
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {datefilter:datefilter,spent_time:spent_time,taskid:taskid},
      beforeSend: function(){
        $('.mask').show();
        $('#loader').show();
      },
      success: function(response){
        response = $.parseJSON(response);
        if(response.time_array.length==0){
          new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
        }
        initMap(response.time_array,response.icon_array,response.flightPlanlat_long_arr,response.markerlat_long_arr,response.lat_long_center_point,1);
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

function renametaskname(){

	var taskname=$("#taskname").val().trim();
  var taskid=$("#taskid").val();

  var isvalidtaskid = 0 ;
  var isvalidtaskname = 1;
   
  PNotify.removeAll();

  if(taskid == 0){
    $("#taskid_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select task !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }else {
    $("#taskid_div").removeClass("has-error is-focused");
    isvalidtaskid = 1;
  }

  if(taskname != ''){
    if(taskname.length<3){
      $("#taskid_div").addClass("has-error is-focused");
      new PNotify({title: 'Task name require minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }else{
      $("#taskid_div").removeClass("has-error is-focused");
      isvalidtaskname = 1;
    }
  }else {
    isvalidtaskname = 1;
  }

  if(isvalidtaskid==1 && isvalidtaskname==1){

    var uurl = SITE_URL+"daily-followup/renametaskname";
    
    $.ajax({
      url: uurl,
      type: 'POST',
      data: { taskid : taskid, taskname : taskname},
      beforeSend: function(){
        $('.mask').show();
        $('#loader').show();
      },
      success: function(response){
        if(response==1){
          new PNotify({title: "Task name successfully rename.",styling: 'fontawesome',delay: '3000',type: 'success'});

          if(taskname==''){
            taskname = 'Cycle - '+taskid;
          }
          $("#taskid option:selected").text(taskname);
          $('#taskid').selectpicker('refresh');
          $("#taskname").val('');
        }else{
          new PNotify({title: "Task name not rename",styling: 'fontawesome',delay: '3000',type: 'error'});
        } 
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

