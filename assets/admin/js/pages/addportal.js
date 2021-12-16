function resetdata(){

  $("#portal_div").removeClass("has-error is-focused");

  if(ACTION==1){
    $('html, body').animate({scrollTop:0},'slow');
  }else{
    $('#portal').val('');
    $('#yes').prop("checked", true);
    $('#portal').focus();
    $('html, body').animate({scrollTop:0},'slow');  

  }
}
function checkvalidation(){
  
  var portal = $("#portal").val().trim();
  
  var isvalidportal = 0;
  
  PNotify.removeAll();
  if(portal == ''){
    $("#portal_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter portal !',styling: 'fontawesome',delay: '3000',type: 'error'});
    $( "#portal" ).focus();
    isvalidportal = 0;
  }else { 
    if(portal.length<2){
      $("#portal_div").addClass("has-error is-focused");
      new PNotify({title: 'Minmum 2 characters require for portal !',styling: 'fontawesome',delay: '3000',type: 'error'});
      $( "#portal" ).focus();
      isvalidportal = 0;
    }else{
      isvalidportal = 1;
    }
  }

  if(isvalidportal == 1){

    var formData = new FormData($('#portalform')[0]);
    if(ACTION==0){
      var uurl = SITE_URL+"Portal/addportal";
      
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
            new PNotify({title: "Portal successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
            resetdata();
          }else if(response==2){
            new PNotify({title: 'Portal already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#portal_div").addClass("has-error is-focused");
          }else{
            new PNotify({title: 'Portal not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"Portal/updateportal";
      
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
              new PNotify({title: "Portal successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"Portal"; }, 1500);
          }else if(response==2){
            new PNotify({title: 'Portal already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
              new PNotify({title: 'Portal not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

