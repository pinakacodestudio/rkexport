function resetdata(){
  $("#productid_div").removeClass("has-error is-focused");
  $("#priority_div").removeClass("has-error is-focused");
   $("#menuorder_div").removeClass("has-error is-focused");
  if(ACTION==1){
    $('#productid').focus();
    $('html, body').animate({scrollTop:0},'slow');
  }else{
    $("#productid").val(0);
    $('#priority').val('');
    $("#menuurl").val('');
    
    $("#inorder").val("");
    $('#productid').focus();    
    $('html, body').animate({scrollTop:0},'slow');  
  }
  $('#productid').selectpicker('refresh');
}
function checkvalidation(action){
  var productid = $("#productid").val();
  var priority = $("#priority").val();
  
  
  var isvalidproductid = isvalidpriority =  0;

  PNotify.removeAll();
  
  if(productid.trim() == 0){
    $("#mainmenu_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select productid !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidproductid = 0;
  }else { 
    isvalidproductid = 1;
  }
  if(priority.trim() == 0){
    $("#priority").addClass("has-error is-focused");
    new PNotify({title: 'Please enter product name !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidpriority = 0;
  }else if(priority.length < 1){
    $("#priority").addClass("has-error is-focused");
    new PNotify({title: 'Minmum 3 characters require for product !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidpriority = 0;
  }else { 
    isvalidpriority = 1;
  }
  

  if(isvalidpriority == 1 && isvalidproductid == 1 ){

    var formData = new FormData($('#formsubmenu')[0]);
    if(ACTION==0){
      var uurl = SITE_URL+"most-popular-product/most-popular-product-add";
      
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
            new PNotify({title: "product successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
            setTimeout(function() { window.location=SITE_URL+"most-popular-product"; }, 1000);
            //resetdata();
        }else if(response==2){
            new PNotify({title: 'product already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
        }else{
          new PNotify({title: 'product not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"most-popular-product/update-most-popular-product";
      
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
            new PNotify({title: "product successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
            setTimeout(function() { window.location=SITE_URL+"most-popular-product"; }, 1500);
        }else if(response==2){
            new PNotify({title: 'product already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
        }else{
            new PNotify({title: 'product not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

