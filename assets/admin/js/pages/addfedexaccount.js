function resetdata(){
  
  $("#accountnumber_div").removeClass("has-error is-focused");
  $("#meternumber_div").removeClass("has-error is-focused");
  $("#apikey_div").removeClass("has-error is-focused");
  $("#password_div").removeClass("has-error is-focused");
  $("#email_div").removeClass("has-error is-focused");

  if(ACTION==0){
    $('#accountnumber').val('');
    $('#meternumber').val('');
    $('#apikey').val('');
    $('#password').val('');
    $('#email').val('');
    $('#yes').prop("checked", true);
    $('#accountnumber').focus();
  }
  $('html, body').animate({scrollTop:0},'slow'); 
}
function checkvalidation(){

  var accountnumber = $("#accountnumber").val().trim();
  var meternumber = $("#meternumber").val().trim();
  var apikey = $("#apikey").val().trim();
  var password = $("#password").val().trim();
  var email = $("#email").val().trim();

  var isvalidaccountnumber = isvalidmeternumber = isvalidapikey = isvalidpassword = isvalidemail = 0;

  PNotify.removeAll();
  if(accountnumber == ''){
    $("#accountnumber_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter account number !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidaccountnumber = 0;
  }else { 
    if(accountnumber.length!=9){
      $("#accountnumber_div").addClass("has-error is-focused");
      new PNotify({title: 'Account number must be 9 digits !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidaccountnumber = 0;
    }else{
      isvalidaccountnumber = 1;  
    }
  }
  if(meternumber == ''){
    $("#meternumber_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter meter number !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidmeternumber = 0;
  }else if(meternumber.length<3){
    $("#meternumber_div").addClass("has-error is-focused");
    new PNotify({title: 'Meter number require minimum 3 digits !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidmeternumber = 0;
  }else { 
    isvalidmeternumber = 1;
  }
  if(apikey == ''){
    $("#apikey_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter api key !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidapikey = 0;
  }else if(apikey.length<5){
    $("#apikey_div").addClass("has-error is-focused");
    new PNotify({title: 'Api key require minimum 5 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidapikey = 0;
  }else { 
    isvalidapikey = 1;
  }
  if(password == ''){
    $("#password_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter password !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidpassword = 0;
  }else if(password.length<5){
    $("#password_div").addClass("has-error is-focused");
    new PNotify({title: 'Password require minimum 5 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidpassword = 0;
  }else { 
    isvalidpassword = 1;
  }
  if(email == ''){
    $("#email_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter email address !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidemail = 0;
  }else if(ValidateEmail(email) == false){
    $("#email_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter valid email address!',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidemail = 0;
  }else {
    isvalidemail = 1;
  }
 
  if(isvalidaccountnumber == 1 && isvalidmeternumber == 1 && isvalidapikey == 1 && isvalidpassword == 1 && isvalidemail == 1){
    
    var formData = new FormData($('#formfedexaccount')[0]);
    if(ACTION==0){
      var uurl = SITE_URL+"fedexaccount/addfedexaccount";
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
            new PNotify({title: "Fedex account detail successfully set.",styling: 'fontawesome',delay: '3000',type: 'success'});
            resetdata();
          }else if(response==2){
            new PNotify({title: 'Fedex account already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
            new PNotify({title: 'Fedex account detail not set !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"fedexaccount/updatefedexaccount";
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
              new PNotify({title: "Fedex account successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"fedexaccount"; }, 1500);
          }else if(response==2){
            new PNotify({title: 'Fedex account already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
              new PNotify({title: 'Fedex account not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

