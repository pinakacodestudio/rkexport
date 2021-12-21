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
    
    var currency = $('#currency').val().trim();
    var value = $('#value').val().trim();
    
    var isvalidcurrency = isvalidvalue = 0;
    
    PNotify.removeAll();
    
    if(currency == ''){
      $("#currency_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter Currency !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidcurrency = 0;
    }else { 
      $("#currency_div").removeClass("has-error is-focused");
      isvalidcurrency = 1;
    }
    if(value == ''){
      $("#value_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter value !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidvalue = 0;
    }else { 
      $("#value_div").removeClass("has-error is-focused");
      isvalidvalue = 1;
    }
    
    if(isvalidcurrency == 1 && isvalidvalue == 1){
  
      var formData = new FormData($('#addcurrency')[0]);
      if(ACTION==0){
        var uurl = SITE_URL+"Currency-rate/currencyrate-add";
        
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
                setTimeout(function() { window.location = SITE_URL + "currency_rate";}, 500);
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
        var uurl = SITE_URL+"Currency-rate/update-currency-rate";
        
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
            console.log(response);
            var data = JSON.parse(response);
            if(data['error']==1){
              new PNotify({title: "successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"currency-rate"; }, 1500);
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
  
  