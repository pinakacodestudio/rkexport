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
    $("#url_div").removeClass("has-error is-focused");
    $("#priority_div").removeClass("has-error is-focused");
    $("#image_div").removeClass("has-error is-focused");
      $('.imageupload img').css({"border":"none"});

    if(ACTION==1){
      $('#name').focus();
      if($('#oldcoverimage').val()!=''){
        var $imageupload = $('.imageupload');
        $('.imageupload img').attr('src',OURCLIENT_COVER_IMAGE+'/'+$('#oldcoverimage').val());
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
      $("#url").val('');
      $("#priority").val('');
      $("#coverimage").val('');
      $('#name').focus();    
      $('html, body').animate({scrollTop:0},'slow');  
  
    }
  }
  function checkvalidation(btntype){
  
    var name = $("#name").val();
    var priority = $("#priority").val();
    var url = $("#url").val();
    var coverimage = $("#coverimage").val();
    
    var removeoldImage = $("#removeoldimage").val();  
   
   
    
    var isvalidname = isvalidpriority = isvalidurl=  isvalidcoverimage= 0 ;
    
    PNotify.removeAll();
    if(name == ''){
      $("#menuname_div").addClass("has-error is-focused");
      new PNotify({title: "Please enter client name !",styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidname = 0;
    }else if(name.length<2){
      $("#menuname_div").addClass("has-error is-focused");
      new PNotify({title: "Main name require minimum 2 characters !",styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidname = 0;
    }else{
      $("#menuname_div").removeClass("has-error is-focused");
      isvalidname = 1;
    }
    if(priority==''){
      $("#priority_div").addClass("has-error is-focused");
      new PNotify({title: "Please enter valid priority !",styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidpriority = 0;
    }else if(priority.length<0){        
      $("#priority_div").removeClass("has-error is-focused");
        isvalidpriority = 1;
    }
    else{
        $("#priority_div").removeClass("has-error is-focused");
        isvalidpriority = 1;
      }
      
      
    if(url==''){
        $("#url_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter website url !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidurl = 0;
      }else if(url.length < 4){
        $("#url_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter  website valid  url !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidurl = 0;
      }else { 
        isvalidurl = 1;
      } 
      if(ACTION == 0){
        if(coverimage=="") {
          $('.imageupload img').css({"border":"1px solid rgb(229, 28, 35)"});
          new PNotify({title: 'Please select image !',styling: 'fontawesome',delay: '3000',type: 'error'});
        } else {
          isvalidcoverimage = 1;   
          $('.imageupload img').css({"border":"1px solid #f1f1f1"}); 
        }    
      } 
      if(ACTION == 1){ 
        if(coverimage=="" && removeoldImage=="1"){
          $('.imageupload img').css({"border":"1px solid rgb(229, 28, 35)"});
          new PNotify({title: 'Please select image !',styling: 'fontawesome',delay: '3000',type: 'error'});
        } else {
          isvalidcoverimage = 1;   
          $('.imageupload img').css({"border":"1px solid #f1f1f1"}); 
        }   
      }    
      
        
     
    if(isvalidname == 1 && isvalidpriority == 1 && isvalidurl == 1  && isvalidcoverimage == 1 ){
  
      var formData = new FormData($('#clientform')[0]);
      if(ACTION==0){
        var uurl = SITE_URL+"Our-client/our_client_add";
        
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
              new PNotify({title: "Client successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"our-client"; }, 1500);      
            }else if(response==2){
              new PNotify({title: 'Client already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
              $("#menuname_div").addClass("has-error is-focused");
            }else if(response==3){
              new PNotify({title: 'Cover image not uploaded !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else if(response==4){
              new PNotify({title: 'Cover image type does not valid !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else{
              new PNotify({title: 'Client not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
        var uurl = SITE_URL+"our-client/update_our_client";
        
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
              new PNotify({title: "Client successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"our-client"; }, 1500);
            }else if(response==2){
              new PNotify({title: 'Client already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
              $("#menuname_div").addClass("has-error is-focused");
            }else if(response==3){
              new PNotify({title: 'Cover image not uploaded !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else if(response==4){
              new PNotify({title: 'Cover image type does not valid !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else{
                new PNotify({title: 'Client not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
  
  