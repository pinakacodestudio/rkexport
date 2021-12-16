$(document).ready(function(){

  $('#datepicker-range').datepicker({
      todayHighlight: true,
      format: 'dd/mm/yyyy'
  });
  $('#datepicker').datepicker({
      todayHighlight: true,
      format: 'dd/mm/yyyy',
      endDate: "Today"
  });
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
  //validvideo(obj);
}
function resetdata(){

  $("#vehicleid_div").removeClass("has-error is-focused");
  $("#companyname_div").removeClass("has-error is-focused");
  $("#policyno_div").removeClass("has-error is-focused");
  $("#insurancedate_div").removeClass("has-error is-focused");
  $("#paymentdate_div").removeClass("has-error is-focused");
  $("#proof_div").removeClass("has-error is-focused");
 
  if(ACTION==1){

    $('html, body').animate({scrollTop:0},'slow');
  }else{
    $('#vehicleid').val('');
    $('#companyname').val('');
    $('#policyno').val('');
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
  
  var vehicleid = $("#vehicleid").val();
  var companyname = $("#companyname").val();
  var policyno = $('#policyno').val();
  var fromdate = $('#fromdate').val();
  var todate = $('#todate').val();
  var paymentdate = $('#paymentdate').val();
  var fileproof = $("#textfile").val().trim();
  var isvalidvehicleid = isvalidcompanyname = isvalidpolicyno = isvalidfromdate = isvalidpaymentdate = isvalidfileproof = 0;
  
  PNotify.removeAll();
  
  if(vehicleid == 0){
    $("#vehicleid_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select vehicle !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidvehicleid = 0;
  }else { 
    $("#vehicleid_div").removeClass("has-error is-focused");
    isvalidvehicleid = 1;
  }
  if(companyname == ''){
    $("#companyname_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter company name !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidcompanyname = 0;
  }else { 
    if(/^([a-zA-Z0-9]+)$/.test(companyname)==false){
      $("#companyname_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter alpha numeric characters only !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidcompanyname = 0;
    }else if(companyname.length<3){
      $("#companyname_div").addClass("has-error is-focused");
      new PNotify({title: 'Company name required minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidcompanyname = 0;
    }else{
      $("#companyname_div").removeClass("has-error is-focused");
      isvalidcompanyname = 1;
    }
  }
  if(policyno == ''){
    $("#policyno_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter policy no. !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidpolicyno = 0;
  }else { 
    $("#policyno_div").removeClass("has-error is-focused");
    isvalidpolicyno = 1;
  }
  if(fromdate == ''){
    $("#insurancedate_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select insurance date !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidfromdate = 0;
  }else { 
    $("#insurancedate_div").removeClass("has-error is-focused");
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
  
  if(isvalidvehicleid == 1 && isvalidcompanyname == 1 && isvalidpolicyno == 1 && isvalidfromdate == 1 && isvalidpaymentdate == 1 && isvalidfileproof == 1){

    var formData = new FormData($('#vehicleinsuranceform')[0]);
    if(ACTION==0){
      var uurl = SITE_URL+"vehicle-insurance/add-vehicle-insurance";
      
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
              new PNotify({title: "Vehicle Insurance  successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"vehicle-insurance"; }, 1500);
          }else if(data['error']==2){
            new PNotify({title: 'Vehicle Insurance already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(data['error']==4){
            new PNotify({title: 'Vehicle Insurance file not uploaded !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(data['error']==3){
            new PNotify({title: 'Invalid file format !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
            new PNotify({title: 'Vehicle Insurance not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"vehicle-insurance/update-vehicle-insurance";
      
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
            new PNotify({title: "Vehicle Insurance successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
            setTimeout(function() { window.location=SITE_URL+"vehicle-insurance"; }, 1500);
          }else if(data['error']==2){
            new PNotify({title: 'Vehicle Insurance already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(data['error']==4){
            new PNotify({title: 'Vehicle Insurance file not uploaded !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(data['error']==3){
            new PNotify({title: 'Invalid file format !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
              new PNotify({title: 'Vehicle Insurance not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

