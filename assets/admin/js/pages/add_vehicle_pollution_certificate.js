$(document).ready(function(){

  $('#datepicker-range').datepicker({
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
  $("#pcno_div").removeClass("has-error is-focused");
  $("#issuingauthority_div").removeClass("has-error is-focused");
  $("#pcdate_div").removeClass("has-error is-focused");
  $("#proof_div").removeClass("has-error is-focused");
 
  if(ACTION==1){

    $('html, body').animate({scrollTop:0},'slow');
  }else{
    $('#vehicleid').val(0);
    $('#pcno').val('');
    $('#issuingauthority').val('');
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
  
  var vehicleid = $("#vehicleid").val() ;
  var pcno = $('#pcno').val().trim();
  var issuingauthority = $('#issuingauthority').val().trim();
  var fromdate = $('#fromdate').val();
  var todate = $('#todate').val();
  var fileproof = $("#textfile").val().trim();

  var isvalidvehicleid = isvalidpcno = isvalidissuingauthority = isvalidfromdate = isvalidfileproof = 0;
  
  PNotify.removeAll();

  if(vehicleid == 0){
    $("#vehicleid_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select vehicle !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidvehicleid = 0;
  }else { 
    $("#vehicleid_div").removeClass("has-error is-focused");
    isvalidvehicleid = 1;
  }
  
  if(pcno == ''){
    $("#pcno_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter pollution certificate no. !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidpcno = 0;
  }else { 
    if(pcno.length<4){
      $("#pcno_div").addClass("has-error is-focused");
      new PNotify({title: 'Pollution Certificate no. required minimum 4 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidpcno = 0;
    }else if(pcno.length>20){
      $("#pcno_div").addClass("has-error is-focused");
      new PNotify({title: 'Pollution Certificate no. required maximum 20 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidpcno = 0;
    }else{
      $("#pcno_div").removeClass("has-error is-focused");
      isvalidpcno = 1;
    }
  }
  if(issuingauthority == ''){
    $("#issuingauthority_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter issuing authority name !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidissuingauthority = 0;
  }else { 
    if(/^([a-zA-Z]+)$/.test(issuingauthority)==false){
      $("#issuingauthority_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter alphabetical characters only !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidcompanyname = 0;
    }else if(issuingauthority.length<3){
      $("#issuingauthority_div").addClass("has-error is-focused");
      new PNotify({title: 'Issuing authority name required minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidissuingauthority = 0;
    }else{
      $("#issuingauthority_div").removeClass("has-error is-focused");
      isvalidissuingauthority = 1;
    }
  }
  if(fromdate == ''){
    $("#pcdate_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select pollution certificate date !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidfromdate = 0;
  }else { 
    $("#pcdate_div").removeClass("has-error is-focused");
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
  
  if(isvalidvehicleid == 1  &&  isvalidpcno == 1 && isvalidissuingauthority == 1 && isvalidfromdate == 1 && isvalidfileproof == 1){

    var formData = new FormData($('#vehiclepollutioncertificateform')[0]);
    if(ACTION==0){
      var uurl = SITE_URL+"vehicle-pollution-certificate/add-vehicle-pollution-certificate";
      
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
              new PNotify({title: "Vehicle Pollution Certificate successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"vehicle-pollution-certificate"; }, 1500);
          }else if(data['error']==2){
            new PNotify({title: 'Vehicle Pollution Certificate already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(data['error']==4){
            new PNotify({title: 'Vehicle Pollution Certificate file not uploaded !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(data['error']==3){
            new PNotify({title: 'Inavalid file format !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
            new PNotify({title: 'Vehicle Pollution Certificate not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"vehicle-pollution-certificate/update-vehicle-pollution-certificate";
      
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
            new PNotify({title: "Vehicle Pollution Certificate successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
            setTimeout(function() { window.location=SITE_URL+"vehicle-pollution-certificate"; }, 1500);
          }else if(data['error']==2){
            new PNotify({title: 'Vehicle Pollution Certificate already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(data['error']==4){
            new PNotify({title: 'Vehicle Pollution Certificate file not uploaded !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(data['error']==3){
            new PNotify({title: 'Inavalid file format !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
              new PNotify({title: 'Vehicle Pollution Certificate not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

