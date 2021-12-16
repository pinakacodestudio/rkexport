$(document).ready(function(){
  resetdata();
});
function resetdata() {

  $("#name_div").removeClass("has-error is-focused");

  if (ACTION == 1) {
    $('#name').focus();
    $('#name_div').addClass('is-focused');
  } else {
    $("#name").val('').focus();
    $('#name_div').addClass('is-focused');

    $('#yes').prop("checked", true);
  }
  $('html, body').animate({scrollTop: 0}, 'slow');
}

function checkvalidation(addtype = 0){
  
    var name = $("#name").val().trim();
    var isvalidname = 0;
    
    PNotify.removeAll();
    if(name == ''){
      $("#name_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter company name !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }else if(name.length < 2) {
      $("#name_div").addClass("has-error is-focused");
      new PNotify({title: 'Company name require minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else{
      $("#name_div").removeClass("has-error is-focused");
      isvalidname = 1;
    }
    
    if(isvalidname == 1){
        
      var formData = new FormData($('#form-vehiclecompany')[0]);
      if(ACTION==0){
        var uurl = SITE_URL+"vehicle-company/vehicle-company-add";
        
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
              new PNotify({title: "Vehicle company successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
              if(addtype==1){
                resetdata();
              }else{
                setTimeout(function() { window.location=SITE_URL+"vehicle-company"; }, 1500);
              }
            }else if(response==2){
              new PNotify({title: "Vehicle company already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
              $("#name_div").addClass("has-error is-focused");
            }else{
              new PNotify({title: 'Vehicle company not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
        
        var uurl = SITE_URL+"vehicle-company/update-vehicle-company";
        
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
              new PNotify({title: "Vehicle company successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
              if(addtype==1){
                setTimeout(function() {window.location=SITE_URL+"vehicle-company/add-vehicle-company/"; }, 1500);
              }else{
                setTimeout(function() { window.location=SITE_URL+"vehicle-company"; }, 1500);
              }
            }else if(response==2){
              new PNotify({title: "Vehicle company already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
              $("#name_div").addClass("has-error is-focused");
            }else{
                new PNotify({title: 'Vehicle company not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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