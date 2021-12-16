$(document).ready(function() {
  
  if($('#oldprofileimage').val()!=''){
    var $imageupload = $('.imageupload');
    $imageupload.imageupload({
      url: SITE_URL,
      type: '1',
      allowedFormats: [ 'jpg', 'jpeg', 'png','ico']
    });
  }else{
    var $imageupload = $('.imageupload');
    $imageupload.imageupload({
      url: SITE_URL,
      type: '0',
      allowedFormats: [ 'jpg', 'jpeg', 'png','ico']
    });
  }

  $('#remove').click(function(){
    $('#removeoldImage').val('1');
  });
});

function resetdata(){

  $("#name_div").removeClass("has-error is-focused");
  $("#email_div").removeClass("has-error is-focused");
  $("#mobile_div").removeClass("has-error is-focused");

  if(ACTION==1){
    var temp = new Array();
    temp = oldbranchid.split(',');

    if($('#oldprofileimage').val()!=''){
      var $imageupload = $('.imageupload');
      $('.imageupload img').attr('src',profileimgpath+'/'+$('#oldprofileimage').val());
      $imageupload.imageupload({
        url: SITE_URL,
        type: '1'
      });
    }else{
      $('.imageupload').imageupload({
        url: SITE_URL,
        type: '0',
      });
    }
    
    $('#removeoldImage').val('0');
    
    $('.selectpicker').selectpicker('refresh');
    $('html, body').animate({scrollTop:0},'slow');
  }
}
function checkvalidation(){

  var name = $("#name").val().trim();
  var mobileno = $("#mobileno").val();
  var email = $("#email").val().trim();

  var isvalidname = isvalidmobileno = isvalidemail = 0;

  PNotify.removeAll();
  if(name == ''){
    $("#firstname_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter name !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidname = 0;
  }else {
    if(name.length<2){
      $("#firstname_div").addClass("has-error is-focused");
      new PNotify({title: 'Name have must be 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidname = 0;
    }else{
      $("#firstname_div").removeClass("has-error is-focused");
      isvalidname = 1;
    }
  }
  if(mobileno == ''){
    $("#mobile_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter mobile no. !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidmobileno = 0;
  }else{
    if(mobileno.length!=10){
      $("#mobile_div").addClass("has-error is-focused");
      new PNotify({title: 'Mobile number allow only 10 digits !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidmobileno = 0;
    }else{
      $("#mobile_div").removeClass("has-error is-focused");
      isvalidmobileno = 1;
    }
  }
  if(email == ''){
    $("#email_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter email address !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidemail = 0;
  }else{
    if(!ValidateEmail(email)){
        $("#email_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter valid email address !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidemail = 0;
    }else{
        $("#email_div").removeClass("has-error is-focused");
        isvalidemail = 1;
    }
  }
  
  if(isvalidname==1 && isvalidmobileno==1 && isvalidemail==1){

    var formData = new FormData($('#userform')[0]);
   
    var uurl = SITE_URL+"user/update-user-profile";

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
          new PNotify({title: "User profile successfully updated !",styling: 'fontawesome',delay: '3000',type: 'success'});
          setTimeout(function() { location.reload(); }, 1500);
        }else if(response==2){
          new PNotify({title: 'User email already register !',styling: 'fontawesome',delay: '3000',type: 'error'});
        }else if(response==3){
          new PNotify({title: 'User profile image not uploaded !',styling: 'fontawesome',delay: '3000',type: 'error'});
        }else if(response==4){
          new PNotify({title: 'Invalid type of user profile image !',styling: 'fontawesome',delay: '3000',type: 'error'});
        }else{
          new PNotify({title: 'User not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

