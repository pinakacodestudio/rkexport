$(document).ready(function(){
 
  
});

function resetdata(){
  
  
  $("#email_div").removeClass("has-error is-focused");
  $("#password_div").removeClass("has-error is-focused");
  
  $('html, body').animate({scrollTop:0},'slow');  

} 

function checkvalidation() {

 
  var email = $("#email").val();
  var password = $("#password").val();
  
  var  isvalidemail = isvalidpassword  = 0 ;
 
  if(email.trim() == 0 || email.length < 2){
    $("#email_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter email address !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidemail = 0;
  }
  else if(validemail.test(email) == false){
    
    $("#email_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter valid email address !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidemail = 0;
  }else { 
    isvalidemail = 1;
  }

  if(password.trim() == ''){
    $("#password_div").addClass('has-error is-focused');
    new PNotify({title: 'Please enter password !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidpassword = 0;
  }else { 
    isvalidpassword = 1;
  }
  
  
  if(isvalidemail == 1 && isvalidpassword ==1){

    var uurl = SITE_URL+"Shiprocket-setting/update-settings";
    var formData = new FormData($('#shiprocketsettingform')[0]);
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
        var a = $.parseJSON(response);
          if(response==1){
            new PNotify({title: 'Shiprocket Settings successfully updated.',styling: 'fontawesome',delay: '3000',type: 'success'});
            
            setTimeout(function() { window.location.href = SITE_URL+"Shiprocket-setting"; }, 1500);
        }else{
          new PNotify({title: 'System Settings not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
        }
      },
      error: function(xhr) {
      
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

