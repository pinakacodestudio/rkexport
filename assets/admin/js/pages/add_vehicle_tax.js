$(document).ready(function(){

  $(function() {
      var tabindex = 1;
       
      $('input,select,textarea,a').each(function() {
          if (this.type != "hidden") {
              var $input = $(this);
              $input.attr("tabindex", tabindex);
              tabindex++;
          }
      });
    });
  $('#datepicker-range').datepicker({
      todayHighlight: true,
      format: 'dd/mm/yyyy'
  });
  $('#datepicker').datepicker({
      todayHighlight: true,
      format: 'dd/mm/yyyy',
      endDate: "Today"
  });

});
function validfile(obj){
  var val = obj.val();
  var id = obj.attr('id').match(/\d+/);
  var filename = obj.val().replace(/C:\\fakepath\\/i, '');

  switch(val.substring(val.lastIndexOf('.') + 1).toLowerCase()){
    case 'pdf' : case 'gif': case 'bmp': case 'jpg': case 'jpeg': case 'png' :

      $("#textfile").val(filename);
      isvalidfiletext = 1;
      $("#proof_div").removeClass("has-error is-focused");
      break;
    default:
      $("#fileproof").val("");
      $("#textfile").val("");
      isvalidfiletext = 0;
      $("#proof_div").addClass("has-error is-focused");
      new PNotify({title: 'Please upload valid file !',styling: 'fontawesome',delay: '3000',type: 'error'});
      break;
  }
}

function resetdata(){

  $("#employeeid_div").removeClass("has-error is-focused");
  $("#vehicleid_div").removeClass("has-error is-focused");
  $("#receiptno_div").removeClass("has-error is-focused");
  $("#taxdate_div").removeClass("has-error is-focused");
  $("#paymentdate_div").removeClass("has-error is-focused");
  $("#proof_div").removeClass("has-error is-focused");
 
  if(ACTION==1){

    $('html, body').animate({scrollTop:0},'slow');
  }else{
    $('#vehicleid').val(0);
    $('#employeeid').val(0);
    $('#receiptno').val('');
    $('#fromdate').val('');
    $('#todate').val('');
    $('#paymentdate').val('');
    $('#fileproof').val('');
    $('#textfile').val('');
    
    $('.selectpicker').selectpicker('refresh');
    $('#yes').prop("checked", true);
    $('html, body').animate({scrollTop:0},'slow');
  }
  $('.selectpicker').selectpicker('refresh');
}

function checkvalidation(){
  
  var vehicleid = $("#vehicleid").val() ;
  var employeeid = $("#employeeid").val() ;
  var receiptno = $('#receiptno').val();
  var fromdate = $('#fromdate').val();
  var todate = $('#todate').val();
  var paymentdate = $('#paymentdate').val();
  var fileproof = $("#textfile").val().trim();
  var isvalidvehicleid = isvalidemployeeid =  isvalidreceiptno = isvalidfromdate = isvalidpaymentdate = isvalidfileproof = 0;
  
  PNotify.removeAll();

  if(employeeid == 0){
    $("#employeeid_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select employee !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidemployeeid = 0;
  }else { 
    $("#employeeid_div").removeClass("has-error is-focused");
    isvalidemployeeid = 1;
  }

  if(vehicleid == 0){
    $("#vehicleid_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select vehicle !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidvehicleid = 0;
  }else { 
    $("#vehicleid_div").removeClass("has-error is-focused");
    isvalidvehicleid = 1;
  }
  if(receiptno == ''){
    $("#receiptno_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter receipt no. !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidreceiptno = 0;
  }else { 
    if(receiptno.length<4){
      $("#receiptno_div").addClass("has-error is-focused");
      new PNotify({title: 'Receipt no. required minimum 4 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidreceiptno = 0;
    }else if(receiptno.length>20){
      $("#receiptno_div").addClass("has-error is-focused");
      new PNotify({title: 'Receipt no. required maximum 20 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidreceiptno = 0;
    }else{
      $("#receiptno_div").removeClass("has-error is-focused");
      isvalidreceiptno = 1;
    }
  }
  if(fromdate == ''){
    $("#taxdate_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select vehicle tax date !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidfromdate = 0;
  }else { 
    $("#taxdate_div").removeClass("has-error is-focused");
    isvalidfromdate = 1;
  }
  if(paymentdate == ''){
    $("#paymentdate_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select payment date !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidpaymentdate = 0;
  }else { 
    $("#paymentdate_div").removeClass("has-error is-focused");
    isvalidpaymentdate = 1;
  }
  if(fileproof == ''){
    $("#proof_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select file !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidfileproof = 0;
  }else { 
    $("#proof_div").removeClass("has-error is-focused");
    isvalidfileproof = 1;
  }
  
  if(isvalidvehicleid == 1  && isvalidemployeeid == 1 && isvalidreceiptno == 1 && isvalidfromdate == 1 && isvalidpaymentdate == 1 && isvalidfileproof == 1){

    var formData = new FormData($('#vehicletaxform')[0]);
    if(ACTION==0){
      var uurl = SITE_URL+"vehicle-tax/add-vehicle-tax";
      
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
          var data = JSON.parse(response);
          if(data['error']==1){
              new PNotify({title: "Vehicle Tax  successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"vehicle-tax"; }, 1500);
          }else if(data['error']==2){
            new PNotify({title: 'Vehicle Tax already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(data['error']==4){
            new PNotify({title: 'Vehicle Tax file not uploaded !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(data['error']==3){
            new PNotify({title: 'Inavalid file format !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
            new PNotify({title: 'Vehicle Tax not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"vehicle-tax/update-vehicle-tax";
      
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
          var data = JSON.parse(response);
          if(data['error']==1){
            new PNotify({title: "Vehicle Tax successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
            setTimeout(function() { window.location=SITE_URL+"vehicle-tax"; }, 1500);
          }else if(data['error']==2){
            new PNotify({title: 'Vehicle Tax already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(data['error']==4){
            new PNotify({title: 'Vehicle Tax file not uploaded !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(data['error']==3){
            new PNotify({title: 'Inavalid file format !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
              new PNotify({title: 'Vehicle Tax not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

