/* function setslug(name){
    $('#slug').val(name.toLowerCase().replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-'));
}
$("#slug").keyup(function (e) {
    $("#slug").val(($("#slug").val()).toLowerCase());
}); */
function resetdata(){

    $("#name_div").removeClass("has-error is-focused");
    $("#slug_div").removeClass("has-error is-focused");
   
    if(ACTION==1){
  
      $('html, body').animate({scrollTop:0},'slow');
    }else{
      $('#name').val('');
      $('#slug').val('');
      $('html, body').animate({scrollTop:0},'slow');
    }
  }
  
  function checkvalidation(addtype=0){
    
    var name = $('#name').val().trim();
    var slug = $('#slug').val().trim();
    
    var isvalidname = isvalidslug = 0;
    
    PNotify.removeAll();
    
    if(name == ''){
      $("#name_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter rights name !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidname = 0;
    }else { 
      $("#name_div").removeClass("has-error is-focused");
      isvalidname = 1;
    }
    if(slug == ''){
      $("#slug_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter slug name !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidslug = 0;
    }else { 
      $("#slug_div").removeClass("has-error is-focused");
      isvalidslug = 1;
    }
    
    if(isvalidname == 1 && isvalidslug == 1){
  
      var formData = new FormData($('#additionalrightsform')[0]);
      if(ACTION==0){
        var uurl = SITE_URL+"additional-rights/additional-rights-add                ";
        
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
            var data = JSON.parse(response);
            if(data['error']==1){
                new PNotify({title: "Rights successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
                if(addtype==1){
                    resetdata();
                }else{
                    setTimeout(function() { window.location = SITE_URL + "additional-rights";}, 500);
                }
            }else if(data['error']==2){
              new PNotify({title: 'Rights already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else if(data['error']==3){
              new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
            }else if(data['error']==4){
              new PNotify({title: 'This rights not available in portal !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else{
              new PNotify({title: 'Rights not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
        var uurl = SITE_URL+"additional-rights/update-additional-rights";
        
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
            var data = JSON.parse(response);
            if(data['error']==1){
              new PNotify({title: "Rights successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"additional-rights"; }, 1500);
            }else if(data['error']==2){
              new PNotify({title: 'Rights already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else if(data['error']==3){
              new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
            }else if(data['error']==4){
              new PNotify({title: 'This rights not available in portal !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else{
                new PNotify({title: 'Rights not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
  
  