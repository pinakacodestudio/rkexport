$(document).ready(function(){
  
})
function resetdata(){  
  
    $("#email_div").removeClass("has-error is-focused");
    

    if(ACTION==0){
      $('#email').val('');
    }
    $('html, body').animate({scrollTop:0},'slow');
}
function checkvalidation(){
    
    var email = $("#email").val().trim();
    
    var isvalidemail = 0;
    
    PNotify.removeAll();
    if(email == 0 ){
        $("#email_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter email !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidname = 0;
    }else {
        if(!ValidateEmail(email)){
            $("#email_div").addClass("has-error is-focused");
            new PNotify({title: 'Please enter valid email address !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidemail = 0;
        }else{
            $("#email_div").removeClass("has-error is-focused");
            //isvalidemail = 1;
        }
    }
    
    if(isvalidemail == 0 )
    {
  
      var formData = new FormData($('#subscribeform')[0]);
      if(ACTION==0){
        var uurl = SITE_URL+"subscribe/addsubscribe";
        
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
              new PNotify({title: "SMS gateway successfully added !",styling: 'fontawesome',delay: '3000',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"subscribe"; }, 1500);
            }else if(response==2){
              new PNotify({title: "SMS gateway already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
              $("#name_div").addClass("has-error is-focused");
            }else{
              new PNotify({title: 'SMS gateway not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

