function resetdata(){

    $("#title_div").removeClass("has-error is-focused");
    $("#mediacategoryid_div").removeClass("has-error is-focused");
    $("#url_div").removeClass("has-error is-focused");
    $("#priority_div").removeClass("has-error is-focused");  
  
    if(ACTION==1){
      $('html, body').animate({scrollTop:0},'slow');
    }else{
      $('#title').val('');      
      $('#mediacategoryid').val(''); 
      $('#title').focus();
      $('#url').val('');
      $('#priority').val('');
     
      $('.selectpicker').selectpicker('refresh');
      $('html, body').animate({scrollTop:0},'slow');    
    }
  }
  function checkvalidation(btntype){
    
    var title = $("#title").val().trim();
    var mediacategoryid = $("#mediacategoryid").val() == undefined ? '' : $("#mediacategoryid").val();
    var url = $("#url").val();
    var priority = $("#priority").val();
    var isvalidtitle = isvalidmediacategoryid = 0;    
    var isvalidurl =  isvalidpriority = 1; 
    
    if(title == ''){
      $("#title_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter title !',styling: 'fontawesome',delay: '3000',type: 'error'});
      $( "#title" ).focus();
      isvalidtitle = 0;
    }else { 
      if(title.length<3){
        $("#title_div").addClass("has-error is-focused");
        new PNotify({title: 'Minmum 3 characters require title !',styling: 'fontawesome',delay: '3000',type: 'error'});
        
        isvalidtitle = 0;
      }else{
        isvalidtitle = 1;
      }      
    }
    if(mediacategoryid == 0){
      $("#mediacategoryid_div").addClass("has-error is-focused");
      new PNotify({title: 'Please select media category !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidmediacategoryid = 0;
    }else {       
      isvalidmediacategoryid = 1;
    }
    if(url.trim() == 0){
      $("#url_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter youtube url !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidurl = 0;
    }else
    if(url!=''){
      if(!validateYouTubeUrl(url)){
        $("#url_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter valid youtube url !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidurl = 0;  
      }else{
        isvalidurl = 1;  
      }
    }
    if(ACTION == 1){
      if(priority == ''){
        $("#priority_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter priority !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidpriority = 0;
      }
    }    
    if(isvalidtitle == 1 && isvalidmediacategoryid == 1 && isvalidurl == 1 && isvalidpriority == 1){
  
      var formData = new FormData($('#videogalleryform')[0]);
      if(ACTION==0){
        var uurl = SITE_URL+"video-gallery/video-gallery-add";
        
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
              new PNotify({title: "Video gallery successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
              if(btntype == 1){              
                setTimeout(function() { window.location=SITE_URL+"video-gallery"; }, 1500);
              } 
              else if(btntype == 0){              
                resetdata();              
              }                 
            }else if(response==2){
              new PNotify({title: "Video gallery already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
            }else{
              new PNotify({title: "Video gallery not added !",styling: 'fontawesome',delay: '3000',type: 'error'});
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
        var uurl = SITE_URL+"video-gallery/update-video-gallery";
        
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
              new PNotify({title: "Video gallery successfully updated.",styling: 'fontawesome',delay: '3000',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"video-gallery"; }, 1500);
            }else if(response==2){
              new PNotify({title: "Video gallery already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
            }else{
              new PNotify({title: "Video gallery not updated !",styling: 'fontawesome',delay: '3000',type: 'error'});
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
  
  