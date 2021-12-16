$(document).ready(function(){

  $('#datepicker-range').datepicker({
      todayHighlight: true,
      format: 'dd/mm/yyyy'
  });
  $('#datepicker').datepicker({
      todayHighlight: true,
      format: 'dd/mm/yyyy'
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
}
function resetdata(){
  
  $("#vehicleid_div").removeClass("has-error is-focused");
  $("#rcno_div").removeClass("has-error is-focused");
  $("#rcdate_div").removeClass("has-error is-focused");
  $("#proof_div").removeClass("has-error is-focused");
 
  if(ACTION==1){

    $('html, body').animate({scrollTop:0},'slow');
  }else{
    $('#vehicleid').val(0);
    $('#rcno').val('');
    $('#fromdate').val('');
    $('#todate').val('');
    $('#fileproof').val('');
    $('#textfile').val('');
    $('.selectpicker').selectpicker('refresh');
    $('html, body').animate({scrollTop:0},'slow');
  }
  $('.selectpicker').selectpicker('refresh');
}

function checkvalidation(){
  
  var vehicleid = $("#vehicleid").val();
  var rcno = $('#rcno').val().trim();
  var fromdate = $('#fromdate').val();
  var todate = $('#todate').val();
  var fileproof = $("#textfile").val().trim();

  var isvalidvehicleid = isvalidrcno = isvalidfromdate = isvalidfileproof =  0;
  
  PNotify.removeAll();


  if(vehicleid == 0){
    $("#vehicleid_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select vehicle !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidvehicleid = 0;
  }else { 
    $("#vehicleid_div").removeClass("has-error is-focused");
    isvalidvehicleid = 1;
  }
  
  if(rcno == ''){
    $("#rcno_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter registration certificate no. !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidrcno = 0;
  }else { 
    if(rcno.length<4){
      $("#rcno_div").addClass("has-error is-focused");
      new PNotify({title: 'Registration Certificate no. required minimum 4 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidrcno = 0;
    }else if(rcno.length>20){
      $("#rcno_div").addClass("has-error is-focused");
      new PNotify({title: 'Registration Certificate no. required maximum 20 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidrcno = 0;
    }else{
      $("#rcno_div").removeClass("has-error is-focused");
      isvalidrcno = 1;
    }
  }
  
  if(fromdate == ''){
    $("#rcdate_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select pollution certificate date !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidfromdate = 0;
  }else { 
    $("#rcdate_div").removeClass("has-error is-focused");
    isvalidfromdate = 1;
  }
  if(fileproof == ''){
    $("#proof_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select file !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidfileproof = 0;
  }else { 
    $("#proof_div").removeClass("has-error is-focused");
    isvalidfileproof = 1;
  }
  
  if(isvalidvehicleid == 1 &&  isvalidrcno == 1 && isvalidfromdate == 1 && isvalidfileproof == 1){

    var formData = new FormData($('#vehicleregistrationcertificateform')[0]);
    if(ACTION==0){
      var uurl = SITE_URL+"vehicle-registration-certificate/add-vehicle-registration-certificate";
      
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
              new PNotify({title: "Vehicle Registration Certificate successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"vehicle-registration-certificate"; }, 1500);
          }else if(data['error']==2){
            new PNotify({title: 'Vehicle Registration Certificate already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(data['error']==4){
            new PNotify({title: 'Vehicle Registration Certificate file not uploaded !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(data['error']==3){
            new PNotify({title: 'Inavalid file format !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
            new PNotify({title: 'Vehicle Registration Certificate not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"vehicle-registration-certificate/update-vehicle-registration-certificate";
      
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
            new PNotify({title: "Vehicle Registration Certificate successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
            setTimeout(function() { window.location=SITE_URL+"vehicle-registration-certificate"; }, 1500);
          }else if(data['error']==2){
            new PNotify({title: 'Vehicle Registration Certificate already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(data['error']==4){
            new PNotify({title: 'Vehicle Registration Certificate file not uploaded !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(data['error']==3){
            new PNotify({title: 'Inavalid file format !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
              new PNotify({title: 'Vehicle Registration Certificate not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

