$(document).ready(function(){
  resetdata();
});

function resetdata() {

  $("#partytype_div").removeClass("has-error is-focused");

  if (ACTION == 1) {
    $('#partytype').focus();
  } else {
    $("#partytype").val('');
    $('#partytype').focus();
    $("#partytype_div").addClass("is-focused");

    $('#yes').prop("checked", true);
  }
  $('html, body').animate({scrollTop: 0}, 'slow');
}

function checkvalidation(addtype=0){
  
    var name = $("#partytype").val().trim();
    var isvalidname = 0;
    
    PNotify.removeAll();
    if(name == ''){
      $("#partytype_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter party type !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }else if (name.length<2) {
      $("#partytype_div").addClass("has-error is-focused");
      new PNotify({title: 'Party type require minimum 2 character !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }else{
      isvalidname = 1;
    }
   
    if(isvalidname == 1){
       
      var formData = new FormData($('#form-partytype')[0]);
      if(ACTION==0){
        var uurl = SITE_URL+"party-type/party-type-add";
        
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
              new PNotify({title: "Party type successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
              if(addtype==1){
                resetdata();
              }else{
                setTimeout(function() { window.location=SITE_URL+"party-type"; }, 1500);
              }
            }else if(response==2){
              new PNotify({title: "Party type already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
              $("#name_div").addClass("has-error is-focused");
            }else{
              new PNotify({title: 'Party type not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
        
        var uurl = SITE_URL+"party-type/update-party-type";
        
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
                new PNotify({title: "Party type successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
                if(addtype==1){
                  setTimeout(function() { window.location=SITE_URL+"party-type/add-party-type"; }, 1500);
                }
                else{
                  setTimeout(function() { window.location=SITE_URL+"party-type"; }, 1500);
                }
            }else if(response==2){
              new PNotify({title: "Party type already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
              $("#name_div").addClass("has-error is-focused");
            }else{
                new PNotify({title: 'Party type not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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