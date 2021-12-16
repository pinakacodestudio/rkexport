function resetdata(){

  $("#carbrand_div").removeClass("has-error is-focused");

  if(ACTION==1){
    $('html, body').animate({scrollTop:0},'slow');
  }else{
    $('#carbrand').val('');
    $('#yes').prop("checked", true);
    $('#carbrand').focus();
    $('html, body').animate({scrollTop:0},'slow');  

  }
}
function checkvalidation(){
  
  var carbrand = $("#carbrand").val().trim();
  
  var isvalidcarbrand = 0;
  
  PNotify.removeAll();
  if(carbrand == ''){
    $("#carbrand_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter carbrand !',styling: 'fontawesome',delay: '3000',type: 'error'});
    $( "#carbrand" ).focus();
    isvalidcarbrand = 0;
  }else { 
    if(carbrand.length<2){
      $("#carbrand_div").addClass("has-error is-focused");
      new PNotify({title: 'Minmum 2 characters require for carbrand !',styling: 'fontawesome',delay: '3000',type: 'error'});
      $( "#carbrand" ).focus();
      isvalidcarbrand = 0;
    }else{
      isvalidcarbrand = 1;
    }
  }

  if(isvalidcarbrand == 1){

    var formData = new FormData($('#carbrandform')[0]);
    if(ACTION==0){
      var uurl = SITE_URL+"Carbrand/addcarbrand";
      
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
            new PNotify({title: "Carbrand successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
            resetdata();
          }else if(response==2){
            new PNotify({title: 'Carbrand already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#carbrand_div").addClass("has-error is-focused");
          }else{
            new PNotify({title: 'Carbrand not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"Carbrand/updatecarbrand";
      
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
              new PNotify({title: "Carbrand successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"Carbrand"; }, 1500);
          }else if(response==2){
            new PNotify({title: 'Carbrand already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
              new PNotify({title: 'Carbrand not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

