$(document).ready(function() {
 
 
})
function resetdata(){
  
  $("#userid_div").removeClass("has-error is-focused");
  $("#positionid_div").removeClass("has-error is-focused");
  $('#positionid').val('');
  $('#userid').val(0);
  $('#userid').focus();
  $('.selectpicker').selectpicker('refresh');
  $('html, body').animate({scrollTop:0},'slow');
}
function checkvalidation(action){
  var userid = $("#userid").val();
  var positionid = $("#positionid").val();
  //alert(positionid);
  var isvaliduserid = isvalidpositionid = 0;
  
  PNotify.removeAll();

  if(userid == 0){
    $("#userid_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select user !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvaliduserid = 0;
    $('html, body').animate({scrollTop:0},'slow');
  }else{
      isvaliduserid = 1;
    
  }

  if(positionid == null){
    $("#positionid_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select position !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidpositionid = 0;
    $('html, body').animate({scrollTop:0},'slow');
  }else{
      isvalidpositionid = 1;
    
  }
  
  if(isvalidpositionid == 1 && isvaliduserid == 1){
      var formData = new FormData($('#formuserposition')[0]);  
      if(ACTION == 0){
        var uurl = SITE_URL+"user-position/add-user-position";
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
              new PNotify({title: "User position successfully added !",styling: 'fontawesome',delay: '3000',type: 'success'});
              if(action==1){
                setTimeout(function() { window.location.href = SITE_URL+"user-position"; }, 1500);
              }else{
                setTimeout(function() { window.location=SITE_URL+"user-position/user-position-add"; }, 1500);
              }
            }else if(response==2){
              new PNotify({title: 'User already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else{
              new PNotify({title: 'User position not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
          var uurl = SITE_URL+"user-position/update-user-position";
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
                new PNotify({title: "User position successfully updated !",styling: 'fontawesome',delay: '3000',type: 'success'});
                if(action==1){
                  setTimeout(function() { window.location=SITE_URL+"user-position"; }, 1500);
                }else{
                  setTimeout(function() { window.location=SITE_URL+"user-position/user-position-add"; }, 1500);
                }
              }else{
                new PNotify({title: 'User position not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

