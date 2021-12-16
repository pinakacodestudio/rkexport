function resetdata(){  

  $("#attribute_div").removeClass("has-error is-focused");
  $("#variant_div").removeClass("has-error is-focused");
  
  if(ACTION==0){
    $('#name').val('');
    $('#attributeid').val(0);
  }
  $('.selectpicker').selectpicker('refresh');  
  $('html, body').animate({scrollTop:0},'slow');
}
function checkvalidation(){
  
  var name = $("#name").val().trim();
  var attributeid = $("#attributeid").val();
  
  var isvalidattributeidid = isvalidname = 0 ;
  
  PNotify.removeAll();
  if(name == ''){
    $("#variant_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter variant name !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidname = 0;
  }else {
    isvalidname = 1;
  }
  if(attributeid == 0){
    $("#attribute_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select attribute !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidattributeidid = 0;
  }else { 
    $("#attribute_div").removeClass("has-error is-focused");
    isvalidattributeidid = 1;
  }

  if(isvalidname == 1 && isvalidattributeidid == 1){

    var formData = new FormData($('#variantform')[0]);
    if(ACTION==0){
      var uurl = SITE_URL+"variant/add-variant";
      
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
            new PNotify({title: "Variant successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
            setTimeout(function() { window.location=SITE_URL+"variant"; }, 1500);
          }else if(response==2){
            new PNotify({title: "Variant name already exists.",styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#variant_div").addClass("has-error is-focused");
          }else{
            new PNotify({title: 'Variant not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"variant/update-variant";
      
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
              new PNotify({title: "Variant successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"variant"; }, 1500);
          }else if(response==2){
            new PNotify({title: "Variant name already exists.",styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#variant_div").addClass("has-error is-focused");
          }else{
              new PNotify({title: 'Variant not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

