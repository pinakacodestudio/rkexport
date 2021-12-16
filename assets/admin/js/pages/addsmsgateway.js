function resetdata(){
  
  $("#smsurl_div").removeClass("has-error is-focused");
  $("#mobileparameter_div").removeClass("has-error is-focused");
  $("#messageparameter_div").removeClass("has-error is-focused");
  $("#userid_div").removeClass("has-error is-focused");
  $("#password_div").removeClass("has-error is-focused");
  $("#senderid_div").removeClass("has-error is-focused");

  $('html, body').animate({scrollTop:0},'slow'); 
}
function checkvalidation(){
  var smsurl = $("#smsurl").val();
  var mobileparameter = $("#mobileparameter").val();
  var messageparameter = $("#messageparameter").val();
  var userid = $("#userid").val();
  var password = $("#password").val();
  var senderid = $("#senderid").val();

  var isvalidsmsurl = isvalidmobileparameter = isvalidmessageparameter = isvaliduserid = isvalidpassword = isvalidsenderid = 0;

  PNotify.removeAll();
  if(smsurl.trim() == ''){
    $("#smsurl_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter smsurl !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidsmsurl = 0;
  }else { 
    if(!isUrlValid(smsurl)){
      $("#smsurl_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter valid smsurl',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidsmsurl = 0;
    }else{
      isvalidsmsurl = 1;  
    }
  }
  if(mobileparameter.trim() == ''){
    $("#mobileparameter_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter mobile parameter !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidmobileparameter = 0;
  }else { 
    isvalidmobileparameter = 1;
  }
  if(messageparameter.trim() == ''){
    $("#messageparameter_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter message parameter !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidmessageparameter = 0;
  }else { 
    isvalidmessageparameter = 1;
  }
  if(userid.trim() == '' || userid.trim() == 0){
    $("#userid_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter userid !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvaliduserid = 0;
  }else { 
    isvaliduserid = 1;
  }
  if(password.trim() == ''){
    $("#password_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter password !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidpassword = 0;
  }else { 
    isvalidpassword = 1;
  }
  if(senderid.trim() == ''){
    $("#senderid_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select senderid !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidsenderid = 0;
  }else { 
    isvalidsenderid = 1;
  }
 
  if(isvalidsmsurl == 1 && isvalidmobileparameter == 1 && isvalidmessageparameter == 1 && isvaliduserid == 1 && isvalidpassword == 1 && isvalidsenderid == 1){
    
      var uurl = SITE_URL+"smsgateway/setsmsgateway";
      var formData = new FormData($('#formsmsgateway')[0]);
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
            new PNotify({title: "SMS gateway successfully set.",styling: 'fontawesome',delay: '3000',type: 'success'});
            resetdata();
          }else{
            new PNotify({title: 'SMS gateway not set !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

