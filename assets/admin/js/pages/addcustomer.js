
function resetdata(){

      $('#form-catalog')[0].reset();
     $('#status').bootstrapToggle('on');
}

function checkvalidation(){

    var name = $("#name").val().trim();
    var email = $("#email").val().trim();
    var mobileno = $("#mobileno").val().trim();
    var countrycodeid = $("#countrycodeid").val();    
   
    var isvalidname = isvalidemail = isvalidmobileno= isvalidcountrycodeid= 0;

    if(name==""){
      $("#name_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter name !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidname = 0;
    } else {
      $("#name_div").removeClass("has-error is-focused");
      isvalidname = 1;
    }

    if(email == ''){
      $("#email_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter email!',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidemail = 0;
    }else{
      if(!ValidateEmail(email)){
          $("#email_div").addClass("has-error is-focused");
          new PNotify({title: 'Please enter valid Email',styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidemail = 0;
      }else{
          $("#email_div").removeClass("has-error is-focused");
          isvalidemail = 1;
      }
    }

    if(mobileno=="") {
        $("#mobile_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter mobile number !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
     if(mobileno.length<10){
        $("#mobile_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter minimum 10 digit mobile number !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmobileno = 0;
      }else{
        $("#mobile_div").removeClass("has-error is-focused");
        isvalidmobileno = 1;
      }
    }

     if(countrycodeid=="" || countrycodeid==0) {
        $("#countrycodeid_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select country code !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
        $("#countrycodeid_div").removeClass("has-error is-focused");
        isvalidcountrycodeid = 1;
    }


                        
    if(isvalidname==1 && isvalidemail==1 && isvalidmobileno==1 && isvalidcountrycodeid==1){
              
      var formData = new FormData($('#customerform')[0]);
        if(ACTION == 0){    
          var uurl = SITE_URL+"Customer/customeradd";
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
                setTimeout(function() { window.location=SITE_URL+"Customer"; }, 1500);
              }else if(response == 2) {
                new PNotify({title: 'Outlet name already added.',styling: 'fontawesome',delay: '3000',type: 'error'});
              } else {
                new PNotify({title: 'Customer data not Added',styling: 'fontawesome',delay: '3000',type: 'error'});
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
          var uurl = SITE_URL+"customer/updatecustomer";
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
                setTimeout(function() { window.location=SITE_URL+"Customer"; }, 1500);
                  }else if(response == 2) {
                      new PNotify({title: 'Mobile number already exist',styling: 'fontawesome',delay: '3000',type: 'error'});
                  }else if(response == 3) {
                      new PNotify({title: 'Email already exist',styling: 'fontawesome',delay: '3000',type: 'error'});
                  } else {
                    new PNotify({title: 'Customer data not updated',styling: 'fontawesome',delay: '3000',type: 'error'});
                  
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