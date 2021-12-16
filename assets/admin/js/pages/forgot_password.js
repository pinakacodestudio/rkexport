$(document).ready(function(){
  $("input").keypress(function(event) {
    if (event.which == 13){
      event.preventDefault();
      $("#btnsubmit").click();
    }
  });
});
function checkemail(){
  var forgotEmail = $("#forgotEmail").val();
  
  var isvalidforgotEmail=0;

  if(forgotEmail!=''){
    if(forgotEmail!=''){
        if(ValidateEmail(forgotEmail)){
            $("#email_div").removeClass("has-error is-focused");
            isvalidforgotEmail=1;
        }else{
            if(!isNaN(forgotEmail)){
              $("#email_div").removeClass("has-error is-focused");
              isvalidforgotEmail=1;
            }else{
              $("#email_div").addClass("has-error is-focused");
              new PNotify({title: 'Please enter valid Email ID !',styling: 'fontawesome',delay: '3000',type: 'error'});
              isvalidforgotEmail=0;
          }
            
        }
    }else{
        $("#email_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter Email ID Or Mobile!',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidforgotEmail=0;
    }

    
    if(isvalidforgotEmail==1){
      var datastr = 'email='+forgotEmail;
      var baseurl = SITE_URL+'forgot-password/check-email';
      $.ajax({
          url: baseurl,
          type: 'POST',
          data: datastr,
          beforeSend: function(){
              $('.mask').show();
              $('#loader').show();
          },
          success: function(data){
            // console.log(data);
            var obj = JSON.parse(data);
            if(obj['error'] == 0){
              new PNotify({title: 'Your Email ID Or Mobile not register !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else if(obj['error'] == 1){
              new PNotify({title: 'We will send reset password link and OTP message to your email address and mobile !',styling: 'fontawesome',delay: '3000',type: 'success'});
              $("#otp_div").show();
              $("#forgotEmail").prop("disabled",true);
              $("#btnsubmit").attr('onclick','checkotp()');
              $("#userid").val(obj['userid']);
              timer(60);
              //setTimeout(function(){ window.location.href = SITE_URL; }, 4000);
            }else if(obj['error'] == 2){
              new PNotify({title: 'Too many attempts to try again after one hour !',styling: 'fontawesome',delay: '3000',type: 'error'});
              $("#resendbtn").show();
              $("#timer").hide();
            }else if(obj['error'] == 4){
              new PNotify({title: 'We can\'t send reset password link and OTP message to your email address and mobile !',styling: 'fontawesome',delay: '3000',type: 'error'});
              setTimeout(function(){ window.location.href = SITE_URL; }, 1500);
            }else{
              new PNotify({title: 'We can\'t send reset password link and OTP message to your email address and mobile !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }
          },
          complete: function(){
              $('.mask').hide();
              $('#loader').hide();
          },
      });
    }
  }else{
    $("#email_div").addClass("has-error is-focused");
    $("#password_div").addClass("has-error is-focused");
    new PNotify({title: 'Enter Email ID Or Mobile !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
}

function checkotp(){
  var otp = $("#otp").val();
  var userid = $("#userid").val();
  
  var isvalidotp=1;
  PNotify.removeAll();
  if(otp==''){
    $("#otp_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter OTP !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidotp=0;
  }else{
    $("#otp_div").removeClass("has-error is-focused");
  }
    
  if(isvalidotp==1 && userid!=""){
    var datastr = 'userid='+userid+'&otp='+otp;
    var baseurl = SITE_URL+'forgot-password/check-otp';
    $.ajax({
      url: baseurl,
      type: 'POST',
      data: {userid:userid,otp:otp},
      beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
      },
      success: function(data){
        var obj = JSON.parse(data);
        if(obj['error'] == 1){
          new PNotify({title: obj['message'],styling: 'fontawesome',delay: '3000',type: 'success'});
          $("#otp_div").removeClass("has-error is-focused");
          setTimeout(function(){ window.location.href = SITE_URL+obj['redirecturl']; }, 1500);
        }else if(obj['error'] == 2){
          new PNotify({title: obj['message'],delay: '3000',type: 'error'});
          $("#otp_div").addClass("has-error is-focused");
        }
      },
      complete: function(){
          $('.mask').hide();
          $('#loader').hide();
      },
    });
  }
}

let timerOn = true;
function timer(remaining) {
  var m = Math.floor(remaining / 60);
  var s = remaining % 60;
  
  m = m < 10 ? '0' + m : m;
  s = s < 10 ? '0' + s : s;
  document.getElementById('timer').innerHTML = m + ':' + s;
  remaining -= 1;
  
  if(remaining >= 0 && timerOn) {
    setTimeout(function() {
        timer(remaining);
    }, 1000);
    return;
  }

  if(!timerOn) {
    // Do validate stuff here
    return;
  }
  
  // Do timeout stuff here
  $("#timer").hide();
  $("#resendbtn").show();
}
function resendotp(){
  $("#forgotEmail").prop("disabled",false);
  $("#resendbtn").hide();
  checkemail();
  $("#timer").show();
}