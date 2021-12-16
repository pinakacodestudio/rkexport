$(document).ready(function() { 
    if($('#oldcoverimage').val()!=''){
      var $imageupload = $('.imageupload');
      $imageupload.imageupload({
        url: SITE_URL,
        type: '1',
        maxFileSizeKb : UPLOAD_MAX_FILE_SIZE,
        allowedFormats: [ 'jpg', 'jpeg', 'png','ico']
      });
    }else{
      var $imageupload = $('.imageupload');
      $imageupload.imageupload({
        url: SITE_URL,
        type: '0',
        maxFileSizeKb : UPLOAD_MAX_FILE_SIZE,
        allowedFormats: [ 'jpg', 'jpeg', 'png','ico']
      });
    }
  
    $('#remove').click(function(){
      $('#removeoldImage').val('1');
    });
  }); 
  function resetdata(){
    $("#menuname_div").removeClass("has-error is-focused");
    $("#priority_div").removeClass("has-error is-focused");
    if(ACTION==1){
      $('#name').focus();
      if($('#oldcoverimage').val()!=''){
        var $imageupload = $('.imageupload');
        $('.imageupload img').attr('src',FRONTMENU_COVER_IMAGE+'/'+$('#oldcoverimage').val());
        $imageupload.imageupload({
          url: SITE_URL,
          type: '1'
        });
      }else{
        $('.imageupload').imageupload({
          url: SITE_URL,
          type: '0',
        });
      }
      $('#removeoldImage').val('0');  
      $('html, body').animate({scrollTop:0},'slow');
    }else{
      $('.imageupload').imageupload({
        url: SITE_URL,
        type: '0',
      }); 
      $('#name').val('');
      $("#menuurl").val('');
      $("#menuicon").val('');
      $("#priority").val('');
      $('#name').focus();    
      $('html, body').animate({scrollTop:0},'slow');  
  
    }
  }
  function checkvalidation(btntype){
  
    var name = $("#name").val();
    var priority = $("#priority").val();
    
    var isvalidname = isvalidpriority = 0;
    
    PNotify.removeAll();
    if(name == ''){
      $("#menuname_div").addClass("has-error is-focused");
      new PNotify({title: "Please enter main menu name !",styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidname = 0;
    }else if(name.length<2){
      $("#menuname_div").addClass("has-error is-focused");
      new PNotify({title: "Main name require minimum 2 characters !",styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidname = 0;
    }else{
      $("#menuname_div").removeClass("has-error is-focused");
      isvalidname = 1;
    }
    if(priority=='' && ACTION==1){
      $("#priority_div").addClass("has-error is-focused");
      new PNotify({title: "Please enter valid priority !",styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidpriority = 0;
    }else{
      $("#priority_div").removeClass("has-error is-focused");
        isvalidpriority = 1;
    }
    
    if(isvalidname == 1 && isvalidpriority == 1){
  
      var formData = new FormData($('#frontendmenuform')[0]);
      if(ACTION==0){
        var uurl = SITE_URL+"frontend-main-menu/frontend-main-menu-add";
        
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
              new PNotify({title: "Frontend Menu successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
              if(btntype == 1){              
                setTimeout(function() { window.location=SITE_URL+"frontend-main-menu"; }, 1500);      
              } 
              else if(btntype == 0){              
                resetdata();              
              } 
            }else if(response==2){
              new PNotify({title: 'Frontend Menu already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
              $("#menuname_div").addClass("has-error is-focused");
            }else if(response==3){
              new PNotify({title: 'Cover image not uploaded !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else if(response==4){
              new PNotify({title: 'Cover image type does not valid !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else{
              new PNotify({title: 'Frontend Menu not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
        var uurl = SITE_URL+"frontend-main-menu/update-frontend-main-menu";
        
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
              new PNotify({title: "Frontend Menu successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"frontend-main-menu"; }, 1500);
            }else if(response==2){
              new PNotify({title: 'Frontend Menu already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
              $("#menuname_div").addClass("has-error is-focused");
            }else if(response==3){
              new PNotify({title: 'Cover image not uploaded !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else if(response==4){
              new PNotify({title: 'Cover image type does not valid !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else{
                new PNotify({title: 'Frontend Menu not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
  
  