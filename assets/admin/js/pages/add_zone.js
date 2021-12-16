function resetdata(){

    $("#zone_div").removeClass("has-error is-focused");
  
    if(ACTION==0){
      $('#zone').val('');
    }
   
    $('html, body').animate({scrollTop:0},'slow');
    
}
function checkvalidation(){
    
    var zone = $("#zone").val().trim();
    
    var isvalidzone  = 0 ;
    
    PNotify.removeAll();
    if(zone == ''){
      $("#zone_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter zone !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidzone = 0;
    }else {
      if(zone.length<3){
        $("#zone_div").addClass("has-error is-focused");
        new PNotify({title: 'Zone name require minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidzone = 0;
      }else{
        isvalidzone = 1;
      }
    }
  
    if(isvalidzone == 1)
    {
  
      var formData = new FormData($('#zoneform')[0]);
      if(ACTION==0){
        var uurl = SITE_URL+"zone/add-zone";
        
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
              new PNotify({title: "Zone successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"zone"; }, 1500);
            }else if(response==2){
              new PNotify({title: "Zone name already exists.",styling: 'fontawesome',delay: '3000',type: 'error'});
              $("#zone_div").addClass("has-error is-focused");
            }else{
              new PNotify({title: 'Zone not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
        var uurl = SITE_URL+"zone/update-zone";
        
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
                new PNotify({title: "Zone successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
                setTimeout(function() { window.location=SITE_URL+"zone"; }, 1500);
            }else if(response==2){
              new PNotify({title: "Zone name already exists.",styling: 'fontawesome',delay: '3000',type: 'error'});
              $("#zone_div").addClass("has-error is-focused");
            }else{
                new PNotify({title: 'Zone not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
  
  
  