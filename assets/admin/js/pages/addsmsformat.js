function resetdata(){

    $("#smsid_div").removeClass("has-error is-focused");
    $("#smsbody_div").removeClass("has-error is-focused");

    if(ACTION==1){
    }else{
        $('#smsid').val('0');
    }
    $('.selectpicker').selectpicker('refresh');  
    $('html, body').animate({scrollTop:0},'slow');
}

function checkvalidation(){

    var smsid = $("#smsid").val() == undefined ? '' : $("#smsid").val();
    var smsbody = $("#smsbody").val().trim();
    
    var isvalidsmsid = isvalidsmsbody = 0;
    
    if(smsid == 0){
      $("#smsid_div").addClass("has-error is-focused");
      new PNotify({title: "Please select sms type !",styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidsmsid = 0;
    }else { 
      isvalidsmsid = 1;
    }

    if(smsbody == ''){
        $("#smsbody_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter sms template !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidsmsbody = 0;
    }else if(smsbody.length<4){
        $("#smsbody_div").addClass("has-error is-focused");
        new PNotify({title: 'SMS template required minimum 4 character !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidsmsbody = 0;
    }else { 
        isvalidsmsbody = 1;
    }
                        
    if(isvalidsmsid==1 &&  isvalidsmsbody==1){
                            
        var formData = new FormData($('#smsformatform')[0]);
        if(ACTION == 0){    
          var uurl = SITE_URL+"Smsformat/addsmsformat";
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
                new PNotify({title: "SMS Format successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
                setTimeout(function() { window.location=SITE_URL+"smsformat"; }, 1500);
              }else if(response==2){
                new PNotify({title: "SMS Format already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
              }else{
                new PNotify({title: "SMS Format not added !",styling: 'fontawesome',delay: '3000',type: 'error'});
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
          var uurl = SITE_URL+"smsformat/updatesmsformat";
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
                    new PNotify({title: "SMS Format successfully updated.",styling: 'fontawesome',delay: '3000',type: 'success'});
                    setTimeout(function() { window.location=SITE_URL+"smsformat"; }, 1500);
                  }else if(response==2){
                    new PNotify({title: "SMS Format already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
                  }else{
                    new PNotify({title: "SMS Format not updated !",styling: 'fontawesome',delay: '3000',type: 'error'});
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