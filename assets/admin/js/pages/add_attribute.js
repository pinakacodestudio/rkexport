function resetdata(){  
  
  $("#attribute_div").removeClass("has-error is-focused");
  if(ACTION==0){
    $('#name').val('');
  }
  
  $('html, body').animate({scrollTop:0},'slow');
}
function checkvalidation(){
  
  var name = $("#name").val().trim();
  
  var isvalidname = 0 ;

  PNotify.removeAll();
  if(name == ''){
    $("#attribute_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter attribute name !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidname = 0;
  }else {
    if(name.length<2){
      $("#attribute_div").addClass("has-error is-focused");
      new PNotify({title: 'Attribute name require minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidname = 0;
    }else{
      isvalidname = 1;
    }
  }

  if(isvalidname == 1){

    var formData = new FormData($('#attributeform')[0]);
    if(ACTION==0){
      var uurl = SITE_URL+"attribute/add-attribute";
      
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
            new PNotify({title: "Attribute successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
            setTimeout(function() { window.location=SITE_URL+"attribute"; }, 1500);
          }else if(response==2){
            new PNotify({title: "Attribute name already exists.",styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#attribute_div").addClass("has-error is-focused");
          }else{
            new PNotify({title: 'Attribute not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"attribute/update-attribute";
      
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
              new PNotify({title: "Attribute successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"attribute"; }, 1500);
          }else if(response==2){
            new PNotify({title: "Attribute name already exists.",styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#attribute_div").addClass("has-error is-focused");
          }else{
              new PNotify({title: 'Attribute not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

