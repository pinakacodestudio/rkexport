$(document).ready(function() { 
    if($('#oldphotogalleryimage').val()!=''){
      var $imageupload = $('.imageupload');
    $imageupload.imageupload({
        url: SITE_URL,
        type: '1',
        allowedFormats: [ 'jpg', 'jpeg', 'png','ico']
      });
    }else{
      var $imageupload = $('.imageupload');
      $imageupload.imageupload({
        url: SITE_URL,
        type: '0',
        allowedFormats: [ 'jpg', 'jpeg', 'png','ico']
      });
    }
  
    $('#remove').click(function(){
      $('#removeoldImage').val('1');
    });
  });
  
  function resetdata(){
  
      $("#title_div").removeClass("has-error is-focused");    
      $("#mediacategoryid_div").removeClass("has-error is-focused");
      $("#alttag_div").removeClass("has-error is-focused");    
      $("#priority_div").removeClass("has-error is-focused");  
      $("#image_div").removeClass("has-error is-focused");
      $('.imageupload img').css({"border":"none"});
     
      if(ACTION==1){
        
        if($('#oldphotogalleryimage').val()!=''){
          var $imageupload = $('.imageupload');
          $('.imageupload img').attr('src',photogalleryimgpath+'/'+$('#oldphotogalleryimage').val());
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
        $('.selectpicker').selectpicker('refresh');
        $('html, body').animate({scrollTop:0},'slow');
      
      }else{
    
        $('.imageupload').imageupload({
          url: SITE_URL,
          type: '0',
        });
        
        $('#title').val('');  
        $('#mediacategoryid').val('0');    
        $('#alttag').val(''); 
        $('#priority').val('');
        $("#image").val('');            
        $('#title').focus();
        $('#yes').prop("checked", true);      
        $('#image').focus();
      }
      $('.selectpicker').selectpicker('refresh');
      $('html, body').animate({scrollTop:0},'slow');  
  
    }
  
  function checkvalidation(btntype){
  
      var title = $("#title").val();  
      var alttag = $("#alttag").val();  
      var mediacategoryid = $("#mediacategoryid").val() == undefined ? '' : $("#mediacategoryid").val();
      var priority = $("#priority").val();
      var photogalleryimg = $("#image").val();
      var removeoldimage = $("#removeoldImage").val();     
      var isvalidtitle = isvalidphotogalleryimg = isvalidmediacategoryid = 0;
      var isvalidalttag = isvalidpriority = 1;
      
      PNotify.removeAll();
      if(title == ''){
        $("#title_div").addClass("has-error is-focused");
        new PNotify({title: "Please enter title !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidtitle = 0;
      }else {
        if(title.length<3){
          $("#title_div").addClass("has-error is-focused");
          new PNotify({title: "Title require minimum 3 characters !",styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidtitle = 0;
        }else{
          isvalidtitle = 1;
        }    
      }if(mediacategoryid == 0){
        $("#mediacategoryid_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select media category !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmediacategoryid = 0;
      }else {       
        isvalidmediacategoryid = 1;
      }if(ACTION == 0){
        if(photogalleryimg=="") {
          $('.imageupload img').css({"border":"1px solid rgb(229, 28, 35)"});
          new PNotify({title: 'Please select image !',styling: 'fontawesome',delay: '3000',type: 'error'});
        } else {
          isvalidphotogalleryimg = 1;   
          $('.imageupload img').css({"border":"1px solid #f1f1f1"}); 
        }    
      } 
      if(ACTION == 1){ 
        if(photogalleryimg=="" && removeoldimage=="1"){
          $('.imageupload img').css({"border":"1px solid rgb(229, 28, 35)"});
          new PNotify({title: 'Please select image !',styling: 'fontawesome',delay: '3000',type: 'error'});
        } else {
          isvalidphotogalleryimg = 1;   
          $('.imageupload img').css({"border":"1px solid #f1f1f1"}); 
        }   
      }    
      if(alttag!= ''){      
        if(alttag.length<3){
          $("#alttag_div").addClass("has-error is-focused");
          new PNotify({title: "Alternative tag require minimum 3 characters !",styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidalttag = 0;
        }  
      }
      if(ACTION == 1){
        if(priority == ''){
          $("#priority_div").addClass("has-error is-focused");
          new PNotify({title: 'Please enter priority !',styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidpriority = 0;
        }
      }             
      if(isvalidtitle == 1 && isvalidphotogalleryimg == 1 && isvalidmediacategoryid == 1 && isvalidalttag == 1 && isvalidpriority == 1){
                              
          var formData = new FormData($('#photogalleryform')[0]);
          if(ACTION == 0){    
            var uurl = SITE_URL+"photo-gallery/photo-gallery-add";
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
                  new PNotify({title: "Photo Gallery successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
                  if(btntype == 1){              
                    setTimeout(function() { window.location=SITE_URL+"photo-gallery"; }, 1500);
                  } 
                  else if(btntype == 0){              
                    resetdata();              
                  }                 
                }else if(response==2){
                  new PNotify({title: 'Photo Gallery image not uploaded !',styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(response==3){
                  new PNotify({title: 'Invalid type of Photo Gallery image !',styling: 'fontawesome',delay: '3000',type: 'error'});
                }else{
                new PNotify({title: "Photo Gallery not added !",styling: 'fontawesome',delay: '3000',type: 'error'});
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
            var uurl = SITE_URL+"photo-gallery/update-photo-gallery";
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
                      new PNotify({title: "Photo Gallery successfully updated.",styling: 'fontawesome',delay: '3000',type: 'success'});
                      setTimeout(function() { window.location=SITE_URL+"photo-gallery"; }, 1500);
                    }else if(response==2){
                      new PNotify({title: 'Photo Gallery image not uploaded !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else if(response==3){
                      new PNotify({title: 'Invalid type of Photo Gallery image !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else{
                      new PNotify({title: "Photo Gallery not updated !",styling: 'fontawesome',delay: '3000',type: 'error'});
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