function resetdata(){

    $("#mailid_div").removeClass("has-error is-focused");
    $("#subject_div").removeClass("has-error is-focused");
    $("#emailbody_div").removeClass("has-error is-focused");
    $('.cke_inner').css({"border":"none"});

    if(ACTION==1){
        $('.cke_inner').css({"background-color":"#FFF","border":"1px solid #D2D2D2"});
        var emailbody = $('#emailbody').val();
        CKEDITOR.instances['emailbody'].setData(emailbody);
    }else{
        $('#mailid').val('0');
        $('#subject').val('');
        $('.cke_inner').css({"background-color":"#FFF","border":"1px solid #D2D2D2"});
        CKEDITOR.instances['emailbody'].setData("");
    }
    $('.selectpicker').selectpicker('refresh');  
    $('html, body').animate({scrollTop:0},'slow');
}

function checkvalidation(){

    var mailid = $("#mailid").val() == undefined ? '' : $("#mailid").val();
    var subject = $("#subject").val().trim();

    var emailbody = CKEDITOR.instances['emailbody'].getData();
    emailbody = encodeURIComponent(emailbody);
    CKEDITOR.instances['emailbody'].updateElement();
    
    var isvalidsubject= isvalidmailid =isvalidemailbody = 0;
    
    if(mailid == 0){
      $("#mailid_div").addClass("has-error is-focused");
      new PNotify({title: "Please select mail !",styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidmailid = 0;
    }else { 
      isvalidmailid = 1;
    }

    if(subject == ''){ 
        $("#subject_div").addClass("has-error is-focused");
        new PNotify({title: "Please enter subject !",styling: 'fontawesome',delay: '3000',type: 'error'});
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
    if(emailbody.trim() == 0 || emailbody.length < 4){
        $("#emailbody_div").addClass("has-error is-focused");
        $('.cke_inner').css({"background-color":"#FFECED","border":"1px solid #e51c23"});
        new PNotify({title: 'Please enter mail content !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidemailbody = 0;
    }else { 
        isvalidemailbody = 1;
        $('.cke_inner').css({"border":"none"});
    }
                        
    if(isvalidsubject==1 && isvalidmailid==1 &&  isvalidemailbody==1){
                            
        var formData = new FormData($('#emailformatform')[0]);
        if(ACTION == 0){    
          var uurl = SITE_URL+"email-format/add-email-format";
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
                new PNotify({title: "Mail Format successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
                setTimeout(function() { window.location=SITE_URL+"email-format"; }, 1500);
              }else if(response==2){
                new PNotify({title: "Mail Format already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
              }else{
                new PNotify({title: "Mail Format not added !",styling: 'fontawesome',delay: '3000',type: 'error'});
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
          var uurl = SITE_URL+"email-format/update-email-format";
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
                    new PNotify({title: "Mail Format successfully updated.",styling: 'fontawesome',delay: '3000',type: 'success'});
                    setTimeout(function() { window.location=SITE_URL+"email-format"; }, 1500);
                  }else if(response==2){
                    new PNotify({title: "Mail Format already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
                  }else{
                    new PNotify({title: "Mail Format not updated !",styling: 'fontawesome',delay: '3000',type: 'error'});
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