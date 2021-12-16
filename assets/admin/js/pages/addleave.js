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
  $("#halfleave_div").hide();

  $('#half').on('change',function(){
    if($(this).prop('checked') == true){
      $("#halfleave_div").show();
      $("#todate_div").hide();
    }else{
      $("#halfleave_div").hide();
      $("#todate_div").show();
      $('#todate').val('');
    }
  })
  $('#full').on('change',function(){
    if($(this).prop('checked') == true){
      $("#halfleave_div").hide();
      $("#todate_div").show();
      $('#todate').val('');
    }
  })
  if(ACTION == 1)
  {
    // alert(FullDay);
    if(FullDay == 1){
      $("#todate_div").show();
      $("#halfleave_div").hide();
    }else if(FullDay == 0){
      $("#halfleave_div").show();
      $("#todate_div").hide();
      
    }
  }
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
  var fromdate=$("#fromdate").val().trim();
  var todate=$("#todate").val().trim();
  var remark=$("#remark").val();
  var reason=$("#reason").val();
  
    var isvalidfromdate = isvalidtodate = isvalidremark = isvalidreason = isvalidrangedate = 0 ;
   

  PNotify.removeAll();
  /* if(employeeid == 0){
    $("#employeeid_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select employee !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidemployeeid = 0;
  }else {
      isvalidemployeeid = 1;
  }
 */
  if (fromdate != "" && todate != "") {
    var fromDate = fromdate.split("/");
    fromDate = new Date(fromDate[2], fromDate[1] - 1, fromDate[0]);
    var rd = String(fromDate.getDate()).padStart(2, '0');
    var rm = String(fromDate.getMonth() + 1).padStart(2, '0'); //January is 0!
    var ry = fromDate.getFullYear();
    fromDate = ry + "-" + rm + "-" + rd;

    var toDate = todate.split("/");
    toDate = new Date(toDate[2], toDate[1] - 1, toDate[0]);
    var dd = String(toDate.getDate()).padStart(2, '0');
    var dm = String(toDate.getMonth() + 1).padStart(2, '0'); //January is 0!
    var dy = toDate.getFullYear();
    toDate = dy + "-" + dm + "-" + dd;

    if (toDate < fromDate && ($('#full').prop('checked') == true)) {
        $("#todate_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select to date greater than from date  !',styling: 'fontawesome',delay: '3000',type: 'error'
        });
    } else {
      isvalidrangedate = 1;
        $("#todate_div").removeClass("has-error is-focused");
    }
  } else {
    isvalidrangedate = 1;
    $("#todate_div").removeClass("has-error is-focused");
  }

  if(fromdate == ''){
    $("#fromdate_div").addClass("has-error is-focused");
    new PNotify({title: 'Please Select From Date !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidfromdate = 0;
  }else {
      isvalidfromdate = 1;
  }

   if(todate == '' && ($('#full').prop('checked')== true)){
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

  if(isvalidfromdate==1 && isvalidtodate==1 && isvalidreason==1 && isvalidrangedate == 1)
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

