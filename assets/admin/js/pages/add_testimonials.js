$(document).ready(function() { 
    if($('#oldtestimonialsimage').val()!=''){
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
    
    $("#name_div").removeClass("has-error is-focused");
    $("#testimonials_div").removeClass("has-error is-focused");   
  
    if(ACTION==1){
      
      if($('#oldtestimonialsimage').val()!=''){
        var $imageupload = $('.imageupload');
        $('.imageupload img').attr('src',testimonialsimagepath+'/'+$('#oldtestimonialsimage').val());
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
      
      $('#name').val('');
      $('#testimonials').val('');      
      
      $('#yes').prop("checked", true);
      $('html, body').animate({scrollTop:0},'slow');

    }
  } 
  

  function checkvalidation(btntype) {
  
    var testimonials = $("#testimonials").val();
    var isvalidtestimonials = 0;   

    if(testimonials.trim() == ''){
        $("#testimonials_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter testimonials !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidtestimonials = 0;
      }else { 
        if(testimonials.length<10 || testimonials.length > 185){
          $("#testimonials_div").addClass("has-error is-focused");
          new PNotify({title: "Enter testimonials between 10 to 185 characters",styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidtestimonials = 0;
        }else{
          isvalidtestimonials = 1;  
        }
      }

    if(isvalidtestimonials == 1){
  
      var formData = new FormData($('#testimonialsform')[0]);
      if(ACTION == 0){    
        var uurl = SITE_URL+"testimonials/testimonials-add";
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
            // console.log(response);
            if(response==1){
              new PNotify({title: "Testimonials successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"testimonials"; }, 1500);          
            }else if(response==2){
              new PNotify({title: 'Testimonials image not uploaded !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else if(response==3){
              new PNotify({title: 'Invalid type of testimonials image !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else{
              new PNotify({title: "testimonials not added !",styling: 'fontawesome',delay: '3000',type: 'error'});
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
        var uurl = SITE_URL+"testimonials/update-testimonials";
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
              new PNotify({title: "Testimonials successfully updated.",styling: 'fontawesome',delay: '3000',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"testimonials"; }, 1500);
            }else if(response==2){
              new PNotify({title: 'Testimonials image not uploaded !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else if(response==3){
              new PNotify({title: 'Invalid type of testimonials image !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else{
              new PNotify({title: "Testimonials not updated !",styling: 'fontawesome',delay: '3000',type: 'error'});
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
  
  
