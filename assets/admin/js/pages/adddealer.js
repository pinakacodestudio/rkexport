
function resetdata(){
    $("#outlet_div").removeClass("has-error is-focused");
    $("#address_div").removeClass("has-error is-focused");
    $("#city_div").removeClass("has-error is-focused");
    $("#mobile_div").removeClass("has-error is-focused");
    $("#email_div").removeClass("has-error is-focused");
    

    // $('#form-catalog')[0].reset();
    $('#status').bootstrapToggle('on');
}

function checkvalidation(){

    var outletname = $("#outletname").val().trim();
    var address = $("#address").val().trim();
    var city = $("#city").val().trim();
    var mobile = $("#mobile").val().trim();
    var email = $("#email").val().trim();    
   
    var isvalidoutletname= isvalidaddress = isvalidcity = isvalidmobile= isvalidemail= 0;
    
    if(outletname=="" ) {
        $("#outlet_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter Outlet Name !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }else {

      if(outletname.length <= 3){
        $("#outlet_div").addClass("has-error is-focused");
        new PNotify({title: "Outlet name require minimum 3 characters !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidoutletname = 0;
      }else{
        //$("#outlet_div").removeClass("has-error is-focused");
        isvalidoutletname = 1;  
      }
      
    }

    if(address==""){
      $("#address_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter address !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidaddress = 0;
    } else {
      $("#address_div").removeClass("has-error is-focused");
      isvalidaddress = 1;
    }

    if(city=="") {
        $("#city_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter City Name !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidcity = 0;
    } else {
      $("#city_div").removeClass("has-error is-focused");
      isvalidcity = 1;
    }

    if(mobile=="") {
        $("#mobile_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter mobile number !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
     if(mobile.length<10){
        $("#mobile_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter minimum 10 digit mobile number !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmobileno = 0;
      }else{
        $("#mobile_div").removeClass("has-error is-focused");
        isvalidmobile = 1;
      }
    }
    if(email=="") {
        $("#email_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter email address !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
      if(!ValidateEmail(email)){
        $("#email_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter valid email address !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidemail = 0;
      }else{
          $("#email_div").removeClass("has-error is-focused");
          isvalidemail = 1;
      }
    }




                        
    if(isvalidoutletname && isvalidaddress && isvalidcity && isvalidmobile && isvalidemail == 1){
              
      var formData = new FormData($('#form-dealer')[0]);
        if(ACTION == 0){    
          var uurl = SITE_URL+"Dealer/dealeradd";
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
                new PNotify({title: 'Successfully Added',styling: 'fontawesome',delay: '3000',type: 'success'});
                setTimeout(function() { window.location=SITE_URL+"Dealer"; }, 1500);
              }else if(response == 2) {
                new PNotify({title: 'Outlet name already added.',styling: 'fontawesome',delay: '3000',type: 'error'});
              } else {
                new PNotify({title: 'Dealer data not Added',styling: 'fontawesome',delay: '3000',type: 'error'});
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
          var uurl = SITE_URL+"dealer/updatedealer";
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
                    new PNotify({title: 'Successfully updated',styling: 'fontawesome',delay: '3000',type: 'success'});
                setTimeout(function() { window.location=SITE_URL+"Dealer"; }, 1500);
                  }else if(response == 2) {
                      new PNotify({title: 'Outlet name already added.',styling: 'fontawesome',delay: '3000',type: 'error'});
                  } else {
                    new PNotify({title: 'Dealer data not updated',styling: 'fontawesome',delay: '3000',type: 'error'});
                  
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