$(document).ready(function(){
  $("#password").keypress(function(event) {
    if (event.which == 13){
      event.preventDefault();
      $("#btnsubmit").click();
    }
  });
  $(".eye").css("display", "none");
});

function showPassword(element){
  if($("#"+element).attr('type') == "password"){
    $("#"+element).attr('type','text');
  } else if($("#"+element).attr('type') == "text") {
    $("#"+element).attr('type','password');
  }
  $("#"+element).next("i").toggleClass("fa fa-eye-slash fa fa-eye eye");
}

$("#password").keyup(function(){
  if($(this).val()){
    $(this).next("i").css("display","block");
  } else {
    $(this).next("i").css("display","none");
  }
});

function checkpassword(){
   
    var password = $.trim($("#password").val());
    var confirmpassword = $.trim($("#confirmpassword").val());
    var userid = $.trim($("#userid").val());
    var verifiedid = $.trim($("#verifiedid").val());
    
    if(CheckPassword(password)==false){
        $("#newpassword_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter password between 6 to 20 characters which contain at least one alphabetic, numeric & special character !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }else{
        if(password == confirmpassword){
            
            var datastr = 'userid='+userid+'&verifiedid='+verifiedid+'&password='+password;
            var baseurl = SITE_URL+'reset-password/update-reset-password';
            $.ajax({
                url: baseurl,
                type: 'POST',
                data: datastr,
                beforeSend: function(){
                    $('.mask').show();
                    $('#loader').show();
                },
                success: function(data){
                    if(data == 1){
                        new PNotify({title: 'Password successfully reset !',styling: 'fontawesome',delay: '3000',type: 'success'});
                        setTimeout(function(){ window.location.href = SITE_URL; }, 4000);
                    }else{
                        new PNotify({title: 'Password can not reset !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }
                },
                complete: function(){
                    $('.mask').hide();
                    $('#loader').hide();
                },
            });
        }else{
            $("#newpassword_div").addClass("has-error is-focused");
            $("#confirmpassword_div").addClass("has-error is-focused");
            new PNotify({title: 'New password and confirm password not same !',styling: 'fontawesome',delay: '3000',type: 'error'});
        }
    }    
}