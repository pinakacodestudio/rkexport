function resetdata(){

    $("#name_div").removeClass("has-error is-focused");    
  
    if(ACTION==1){
      $('#name').focus();
      $('html, body').animate({scrollTop:0},'slow');
    }else{
      $('#name').val('');
      $('#name').focus();
      
      $('html, body').animate({scrollTop:0},'slow');    
    }
  }
  function checkvalidation(btntype){
    
    var name = $("#name").val();    

    PNotify.removeAll();
    if(name.trim() == ''){
      $("#name_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter name !',styling: 'fontawesome',delay: '3000',type: 'error'});
      $( "#name" ).focus();
      isvalidname = 0;
    }else if(name.length<3){
        $("#name_div").addClass("has-error is-focused");
        new PNotify({title: 'Minmum 3 characters require name !',styling: 'fontawesome',delay: '3000',type: 'error'});        
        isvalidname = 0;
      }else{
        $("#name_div").removeClass("has-error is-focused");    
        isvalidname = 1;       
    }
      
    if(isvalidname == 1){
  
      var formData = new FormData($('#mediacategoryform')[0]);
      if(ACTION == 0){
        var uurl = SITE_URL+"media-category/media-category-add";
        
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
              new PNotify({title: "Media category successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
              if(btntype == 1){              
                setTimeout(function() { window.location=SITE_URL+"media-category"; }, 1500);
              } 
              else if(btntype == 0){              
                resetdata();              
              }                 
            }else if(response==2){
              new PNotify({title: "Media category already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
              $("#name_div").addClass("has-error is-focused");
            }else{
              new PNotify({title: "Media category not added !",styling: 'fontawesome',delay: '3000',type: 'error'});
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
        var uurl = SITE_URL+"media-category/update-media-category";
        
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
              new PNotify({title: "Media category successfully updated.",styling: 'fontawesome',delay: '3000',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"media-category"; }, 1500);
            }else if(response==2){
              new PNotify({title: "Media category already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
              $("#name_div").addClass("has-error is-focused");
            }else{
              new PNotify({title: "Media category not updated !",styling: 'fontawesome',delay: '3000',type: 'error'});
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
  
  