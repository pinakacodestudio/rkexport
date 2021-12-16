function resetdata(){
    $("#subject_div").removeClass("has-error is-focused");
    $("#message_div").removeClass("has-error is-focused");
    $('.cke_inner').css({"border":"none"});

    if(ACTION==1){
        $('.cke_inner').css({"background-color":"#FFF","border":"1px solid #D2D2D2"});
        var message = $('#message').val();
        CKEDITOR.instances['message'].setData(message);
    }else{
        $('#subject').val('');
        $('.cke_inner').css({"background-color":"#FFF","border":"1px solid #D2D2D2"});
        CKEDITOR.instances['message'].setData("");
    }
    $('.selectpicker').selectpicker('refresh');  
    $('html, body').animate({scrollTop:0},'slow');
}

function checkvalidation(){

    var subject = $("#subject").val().trim();
    var message = CKEDITOR.instances['message'].getData();
    message = encodeURIComponent(message);
    CKEDITOR.instances['message'].updateElement();
    
    var isvalidsubject = isvalidmessage = 0;
    PNotify.removeAll();
    if(subject == ''){ 
        $("#subject_div").addClass("has-error is-focused");
        new PNotify({title: "Enter enter subject !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidsubject = 0;      
    }else {
      if(subject.length<3){
        $("#subject_div").addClass("has-error is-focused");
        new PNotify({title: "Subject require minimum 3 characters !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidsubject = 0;
      }else{
        isvalidsubject = 1;
      }
    }
    if(message.trim() == 0 || message.length < 4){
        $("#message_div").addClass("has-error is-focused");
        $('.cke_inner').css({"background-color":"#FFECED","border":"1px solid #e51c23"});
        new PNotify({title: 'Please enter message !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmessage = 0;
    }else { 
        isvalidmessage = 1;
        $('.cke_inner').css({"border":"none"});
    }
                        
    if(isvalidsubject==1 &&  isvalidmessage==1){
                            
        var formData = new FormData($('#feedbackreplyform')[0]);
        
        var uurl = SITE_URL+"Feedbackreply/addfeedbackreply";
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
              new PNotify({title: "Feedback reply successfully sended.",styling: 'fontawesome',delay: '3000',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"feedbackreply"; }, 1500);
            }else{
              new PNotify({title: "Feedback reply not sended !",styling: 'fontawesome',delay: '3000',type: 'error'});
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