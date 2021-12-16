$(document).ready(function(){
  
})
function resetdata(){  
  
    $("#smsformattype_div").removeClass("has-error is-focused");
    $("#smsgateway_div").removeClass("has-error is-focused");
    $("#format_div").removeClass("has-error is-focused");

    if(ACTION==0){
      $('#smsformattype').val(0);
      $('#smsgatewayid').val(0);
      $('#format').val('');
      $('.selectpicker').selectpicker('refresh');
    }
    $('html, body').animate({scrollTop:0},'slow');
}
function checkvalidation(){
  
  var smsformattype = $("#smsformattype").val();
  var smsgatewayid = $("#smsgatewayid").val();
  var format = $("#format").val().trim();
  
  var isvalidsmsformattype = isvalidsmsgatewayid = isvalidformat = 1;
  
  PNotify.removeAll();
  if(smsformattype == 0){
    $("#smsformattype_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select SMS Format type !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidsmsformattype = 0;
  }else {   
    $("#smsformattype_div").removeClass("has-error is-focused");
  }
    if(smsgatewayid == 0){
        $("#smsgateway_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select SMS Gateway !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidsmsgatewayid = 0;
    }else {   
        $("#smsgateway_div").removeClass("has-error is-focused");
    }

    if(format == ''){
        $("#format_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter format !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidformat = 0;
    }else {
        if(format.length < 2){
            $("#format_div").addClass("has-error is-focused");
            new PNotify({title: 'Format require minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidformat = 0;
        }else{
            $("#format_div").removeClass("has-error is-focused");
        }
    }


    if(isvalidsmsgatewayid == 1 && isvalidformat == 1)
    {
  
      var formData = new FormData($('#smsformatform')[0]);
      if(ACTION==0){
        var uurl = SITE_URL+"sms-format/add-sms-format";
        
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
              new PNotify({title: "SMS format successfully added !",styling: 'fontawesome',delay: '3000',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"sms-format"; }, 1500);
            }else if(response==2){
              new PNotify({title: "SMS format already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
              $("#smsformattype_div").addClass("has-error is-focused");
            }else{
              new PNotify({title: 'SMS format not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
        var uurl = SITE_URL+"sms-format/update-sms-format";
        
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
                new PNotify({title: "SMS format successfully updated !",styling: 'fontawesome',delay: '1500',type: 'success'});
                setTimeout(function() { window.location=SITE_URL+"sms-format"; }, 1500);
            }else if(response==2){
              new PNotify({title: "SMS format already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
              $("#smsformattype_div").addClass("has-error is-focused");
            }else{
                new PNotify({title: 'SMS format not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
  
  