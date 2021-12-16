$(document).ready(function() {

  /* if(ACTION == 1){
    var channelid = $("#channelid").val();
    // console.log(channelid);
    if(channelid!=0){
      getmembers(channelid,memberid);
    }
  }
  $("#channelid").change(function(){
    var channelid = $(this).val();
    getmembers(channelid);
    // var memberid = $("#memberid").val();
  }) */
})
function resetdata(){
  if(roletype=='' && ACTION==0){
    var inputs = $("input[type='checkbox']");
    for(var i = 0; i<inputs.length; i++){
      $('#'+inputs[i].id).prop('checked', false);
    }
    $(".selectpicker").val("").selectpicker("refresh");
  }
  $("#userrole_div").removeClass("has-error is-focused");
  $('#userrole').val("");
  $('#userrole').focus();
  $('html, body').animate({scrollTop:0},'slow');
}
function checkvalidation(action){
  var userrole = $("#userrole").val();
  var isvaliduserrole = 0;
  
  PNotify.removeAll();
  if(userrole.trim() == '' || userrole.trim() == 0){
    $("#userrole_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter employee role !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvaliduserrole = 0;
    $('html, body').animate({scrollTop:0},'slow');
  }else { 
    if(userrole.length<4){
      $("#userrole_div").addClass("has-error is-focused");
      new PNotify({title: 'Employee role name require minimum 4 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvaliduserrole = 0;
      $('html, body').animate({scrollTop:0},'slow');
    }else{
      isvaliduserrole = 1;
    }
  }
  
  if(isvaliduserrole == 1){
      var formData = new FormData($('#formuserrole')[0]);  
      if(ACTION == 0){
        var uurl = SITE_URL+"user-role/add-user-role";
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
              new PNotify({title: "Employee role successfully added !",styling: 'fontawesome',delay: '3000',type: 'success'});
              resetdata();
            }else if(response==2){
              new PNotify({title: 'Employee role already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else if(response==3){
              new PNotify({title: 'Selected employee role already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else{
              new PNotify({title: 'Employee role not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
          var uurl = SITE_URL+"user-role/update-user-role";
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
                new PNotify({title: "Employee role successfully updated !",styling: 'fontawesome',delay: '3000',type: 'success'});
                setTimeout(function() { window.location=SITE_URL+"user-role"; }, 1500);
              }else if(response==2){
                new PNotify({title: 'Employee role already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
              }else if(response==3){
                new PNotify({title: 'Selected employee role already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
              }else{
                new PNotify({title: 'Employee role not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

/* function getmembers(channelid,memberid=0){
  var uurl = SITE_URL+"member/getmembers";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {channelid:channelid},
    dataType: 'json',
    async: false,
    success: function(response){
      $('#memberid')
      .find('option')
      .remove()
      .end()
      .append('<option value="">Select Member</option>')
      .val('whatever')
      ;

      for(var i = 0; i < response.length; i++) {

        $('#memberid').append($('<option>', { 
          value: response[i]['id'],
          text : response[i]['name']
        }));

      }
      if(memberid!=0){
        $("#memberid").val(memberid);
      }
      // $('#product'+prow).val(areaid);
      $('#memberid').selectpicker('refresh');
    },
    error: function(xhr) {
          //alert(xhr.responseText);
        },
      });
}
 */
