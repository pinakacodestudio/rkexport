function resetdata(){  
  
    $("#productsection_div").removeClass("has-error is-focused");
    if(ACTION==0){
      $('#name').val('');
    }
    
    $('html, body').animate({scrollTop:0},'slow');
  }
  function checkvalidation(){
    
    var name = $("#name").val().trim();
    var maxhomeproduct = $("#maxhomeproduct").val().trim();
    
    var isvalidname = isvalidmaxhomeproduct = 0 ;
    
    PNotify.removeAll();
    if(name == ''){
      $("#productsection_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter name !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidname = 0;
    }else {
      if(name.length<3){
        $("#productsection_div").addClass("has-error is-focused");
        new PNotify({title: 'Name require minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidname = 0;
      }else{
        isvalidname = 1;
      }
    }

    if(maxhomeproduct == ''){
      $("#maxhomeproduct_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter maximum display product !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidmaxhomeproduct = 0;
    }else {
      isvalidmaxhomeproduct = 1;
    }
  
    if(isvalidname == 1 && isvalidmaxhomeproduct == 1){
  
      var formData = new FormData($('#productsectionform')[0]);
      if(ACTION==0){
        var uurl = SITE_URL+"product-section/add-product-section";
        
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
              new PNotify({title: "Product Section successfully added !",styling: 'fontawesome',delay: '3000',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"product-section"; }, 1500);
            }else if(response==2){
              new PNotify({title: "Product Section already exist !",styling: 'fontawesome',delay: '3000',type: 'error'});
              $("#productsection_div").addClass("has-error is-focused");
            }else{
              new PNotify({title: 'Product Section not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
        var uurl = SITE_URL+"product-section/update-product-section";
        
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
                new PNotify({title: "Product Section successfully updated !",styling: 'fontawesome',delay: '1500',type: 'success'});
                setTimeout(function() { window.location=SITE_URL+"product-section"; }, 1500);
            }else if(response==2){
              new PNotify({title: "Product Section already exist !",styling: 'fontawesome',delay: '3000',type: 'error'});
              $("#productsection_div").addClass("has-error is-focused");
            }else{
                new PNotify({title: 'Product Section not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
  
  