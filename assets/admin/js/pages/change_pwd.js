function resetdata(){
  $("#oldpassword_div").removeClass('has-error is-focused');
  $("#newpassword_div").removeClass('has-error is-focused');
  $("#confirmpassword_div").removeClass('has-error is-focused');

  $('#oldpassword').val('');
  $('#newpassword').val('');
  $('#confirmpassword').val('');
  $('#oldpassword').focus();
}
function checkvalidation(){
  var password = $("#oldpassword").val();
  var newpassword = $("#newpassword").val();
  var confirmpassword = $("#confirmpassword").val();

  var isvalidoldpassword=0,isvalidPassword=0,isvalidConfirmPassword=0;

  if(password.trim() != '' || confirmpassword.trim() != '' || newpassword.trim() != ''){
    if(password.trim() == ''){
      $("#oldpassword_div").addClass('has-error is-focused');
      new PNotify({title: 'Please enter old password !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidoldpassword = 1;
    }else { 
      isvalidoldpassword = 0;
    }
    if(CheckPassword(newpassword)==false){
      $("#newpassword_div").addClass('has-error is-focused');
      new PNotify({title: 'Please enter new password between 6 to 20 characters which contain at least one alphabetic, numeric & special character !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidPassword = 1;
    }else { 
      isvalidPassword = 0;
    }
    if(confirmpassword.trim() == ''){
      $("#confirmpassword_div").addClass('has-error is-focused');
      new PNotify({title: 'Please enter confirm password !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidConfirmPassword = 1;
    }else { 
      isvalidConfirmPassword = 0;
    }

    
    if(isvalidoldpassword==0 && isvalidPassword==0 && isvalidConfirmPassword==0){
      if(newpassword == confirmpassword){
        
        var datastr = 'password='+password+'&newpassword='+newpassword;
        var baseurl = SITE_URL+'user/update-password';
        $.ajax({
          url: baseurl,
          type: 'POST',
          data: {password:password,newpassword:newpassword},
          success: function(response){
            if(response == 1){
              new PNotify({title: 'Password successfully changed !',styling: 'fontawesome',delay: '3000',type: 'success'});
              resetdata();
            }else if(response == 2){
              new PNotify({title: 'Old Password does not match !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else{
              new PNotify({title: 'Password not changed !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }
          }
        });
      }else{
        new PNotify({title: 'Confirm password does not match !',styling: 'fontawesome',delay: '3000',type: 'error'});
        $("#confirmpassword_div").addClass('has-error is-focused');
        $('#confirmpassword').focus();
      }
    }
  }else{
    new PNotify({title: 'Please enter Old,New and Confirm passsword !',styling: 'fontawesome',delay: '3000',type: 'error'});
    $("#oldpassword_div").addClass('has-error is-focused');
    $("#newpassword_div").addClass('has-error is-focused');
    $("#confirmpassword_div").addClass('has-error is-focused');
  }
  
}