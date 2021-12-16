function resetdata(){
  if(roletype=='' && ACTION==0){
    var inputs = $("input[type='checkbox']");
    for(var i = 0; i<inputs.length; i++){
      $('#'+inputs[i].id).prop('checked', false);
    }
  }
  $("#memberrole_div").removeClass("has-error is-focused");
  $('#memberrole').val("");
 
  $('#memberrole').focus();
  $('.selectpicker').selectpicker('refresh');
  $('html, body').animate({scrollTop:0},'slow');
}
function checkvalidation(action){
  
  var memberrole = $("#memberrole").val();

  var isvalidmemberrole = 0;
  
  PNotify.removeAll();
  if(memberrole.trim() == '' || memberrole.trim() == 0){
    $("#memberrole_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter '+member_label+' role !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidmemberrole = 0;
    $('html, body').animate({scrollTop:0},'slow');
  }else { 
    if(memberrole.length<4){
      $("#memberrole_div").addClass("has-error is-focused");
      new PNotify({title: 'Role name require minimum 4 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidmemberrole = 0;
      $('html, body').animate({scrollTop:0},'slow');
    }else{
        isvalidmemberrole = 1;
    }
  }
  
  if(isvalidmemberrole == 1){
      var formData = new FormData($('#formmemberrole')[0]);  
      if(ACTION == 0){
        var uurl = SITE_URL+"member-role/add-member-role";
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
              new PNotify({title: Member_label+" role successfully added !",styling: 'fontawesome',delay: '3000',type: 'success'});
              resetdata();
            }else if(response==2){
              new PNotify({title: Member_label+' role already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else if(response==3){
              new PNotify({title: 'Selected '+member_label+' already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else{
              new PNotify({title: Member_label+' role not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
          var uurl = SITE_URL+"member-role/update-member-role";
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
                new PNotify({title: Member_label+" role successfully updated !",styling: 'fontawesome',delay: '3000',type: 'success'});
                setTimeout(function() { window.location=SITE_URL+"member-role"; }, 1500);
              }else if(response==2){
                new PNotify({title: Member_label+' role already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
              }else{
                new PNotify({title: Member_label+' role not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
