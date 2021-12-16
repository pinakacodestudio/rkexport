$(document).ready(function(){

  $('#fromdate').datepicker({
    todayHighlight: true,
    format: 'dd/mm/yyyy',
    todayBtn:"linked",
  });

  $('#todate').datepicker({
    todayHighlight: true,
    format: 'dd/mm/yyyy',
    todayBtn:"linked",
  });

});

$(function() {
  var tabindex = 1;
  $('input,select,textarea,a,button,radio,checkbox').each(function() {
      if (this.type != "hidden") {
        var $input = $(this);
        $input.attr("tabindex", tabindex);
        tabindex++;
      }
  });
});

function resetdata(){

  //$("#employeeid_div").removeClass("has-error is-focused");
  $("#fromdate_div").removeClass("has-error is-focused");
  $("#todate_div").removeClass("has-error is-focused");
  $("#remark_div").removeClass("has-error is-focused");

  if(ACTION==0){
     // $("#employeeid_div").val("");
    $("#fromdate_div").val("");
    $("#todate_div").val("");
    $("#remark_div").val("");
  }
 
  $('html, body').animate({scrollTop:0},'slow');
  
}
function checkvalidation(){


 // var employeeid=$("#employeeid").val();
  var fromdate=$("#fromdate").val();
  var todate=$("#todate").val();
  var remark=$("#remark").val();
  var reason=$("#reason").val();
  
    var isvalidfromdate = isvalidtodate = isvalidremark = isvalidreason = 0 ;
   

  PNotify.removeAll();
  /* if(employeeid == 0){
    $("#employeeid_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select employee !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidemployeeid = 0;
  }else {
      isvalidemployeeid = 1;
  }
 */
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

  /* if(remark == ''){
    $("#remark_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter remark !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidremark = 0;
  }else {
    if(remark.length<2)
    {
      $("#remark_div").addClass("has-error is-focused");
      new PNotify({title: 'Remark require minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidremark = 0;
    }
    else
    {
      isvalidremark = 1;
    }
  } */
  if(reason == ''){
    $("#reason_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter reason !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidreason = 0;
  }else {
    if(reason.length<2)
    {
      $("#reason_div").addClass("has-error is-focused");
      new PNotify({title: 'Reason require minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidreason = 0;
    }
    else
    {
      isvalidreason = 1;
    }
  }

  if(isvalidfromdate==1 && isvalidtodate==1 && isvalidreason==1)
  {

    var formData = new FormData($('#leaveform')[0]);
    if(ACTION==0){
      var uurl = SITE_URL+"Leave/addleave";
      
      $.ajax({
        url: uurl,
        type: 'POST',
        data: formData,
        //async: false,
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
        },
        success: function(response){
        //alert(response);
          if(response==1){
            new PNotify({title: "Leave successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
            setTimeout(function() { window.location=SITE_URL+"leave"; }, 1500);
          }else if(response==2){
            new PNotify({title: "Leave already exists on the same date!",styling: 'fontawesome',delay: '3000',type: 'success'});
            setTimeout(function() { window.location=SITE_URL+"leave"; }, 1500);
          }else{
            new PNotify({title: 'Leave not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
        complete: function(){
          $('.mask').hide();
          $('#loader').hide();
        },
        cache: false,
        contentType: false,
        processData: false
      });
    }else{
      var uurl = SITE_URL+"Leave/updateleave";
      
      $.ajax({
        url: uurl,
        type: 'POST',
        data: formData,
        //async: false,
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
        },
        success: function(response){
          if(response==1){
              new PNotify({title: "Leave successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"leave"; }, 1500);
          }else{
              new PNotify({title: 'Leave not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
        complete: function(){
          $('.mask').hide();
          $('#loader').hide();
        },
        cache: false,
        contentType: false,
        processData: false
      });
    }
  }
}

