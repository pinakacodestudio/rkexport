$(document).ready(function(){
  
})
function resetdata(){  
  
    $("#name_div").removeClass("has-error is-focused");
    $("#gatewaylink_div").removeClass("has-error is-focused");
    $("#userid_div").removeClass("has-error is-focused");
    $("#password_div").removeClass("has-error is-focused");
    $("#senderid_div").removeClass("has-error is-focused");
    $("#description_div").removeClass("has-error is-focused");

    if(ACTION==0){
      $('#name').val('');
      $('#gatewaylink').val('');
      $('#userid').val('');
      $('#password').val('');
      $('#senderid').val('');
      $('#description').val('');
    }
    $('html, body').animate({scrollTop:0},'slow');
}
function checkvalidation(){
    
    var name = $("#name").val().trim();
    var gatewaylink = $("#gatewaylink").val().trim();
    var userid = $("#userid").val().trim();
    var password = $("#password").val().trim();
    var senderid = $("#senderid").val().trim();
    var description = $("#description").val().trim();
    
    var isvalidgatewaylink = isvaliduserid = isvalidpassword = isvalidsenderid = isvaliddescription = isvalidname = 1;
    
    PNotify.removeAll();
    if(name == ''){
        $("#name_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter name !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidname = 0;
    }else {
        if(name.length < 2){
            $("#name_div").addClass("has-error is-focused");
            new PNotify({title: 'Name require minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidname = 0;
        }else{
            $("#name_div").removeClass("has-error is-focused");
        }
    }

    if(gatewaylink == ''){
        $("#gatewaylink_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter Gateway Link !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidgatewaylink = 0;
    }else {   
        $("#gatewaylink_div").removeClass("has-error is-focused");
    }

    if(userid == ''){
        $("#userid_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter userid !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvaliduserid = 0;
    }else {
        $("#userid_div").removeClass("has-error is-focused");
    }

    if(password==''){
        $("#password_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter user password !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidpassword = 0;
    }else{
        $("#password_div").removeClass("has-error is-focused");
    }

    if(senderid == ''){
        $("#senderid_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter senderid !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidsenderid = 0;
    }else {
        $("#senderid_div").removeClass("has-error is-focused");
    }
   
    if(description != '' && description.length < 2){
        $("#description_div").addClass("has-error is-focused");
        new PNotify({title: 'Description require minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvaliddescription = 0;
    }else{
        $("#description_div").removeClass("has-error is-focused");
    }
   

    if(isvalidname == 1 && isvalidgatewaylink == 1 && isvaliduserid == 1 && isvalidpassword == 1 && isvalidsenderid == 1 && isvaliddescription == 1)
    {
  
      var formData = new FormData($('#smsgatewayform')[0]);
      if(ACTION==0){
        var uurl = SITE_URL+"sms-gateway/add-sms-gateway";
        
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
              setTimeout(function() { window.location=SITE_URL+"sms-gateway"; }, 1500);
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
      }else{
        var uurl = SITE_URL+"sms-gateway/update-sms-gateway";
        
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
                new PNotify({title: "SMS gateway successfully updated !",styling: 'fontawesome',delay: '1500',type: 'success'});
                setTimeout(function() { window.location=SITE_URL+"sms-gateway"; }, 1500);
            }else if(response==2){
              new PNotify({title: "SMS gateway already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
              $("#name_div").addClass("has-error is-focused");
            }else{
                new PNotify({title: 'SMS gateway not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
  
  