$(document).ready(function(){
  resetdata();
});
function resetdata() {

  $("#insurancecompany_div").removeClass("has-error is-focused");$("#agent_div").removeClass("has-error is-focused");$("#email_div").removeClass("has-error is-focused");$("#mobileno_div").removeClass("has-error is-focused");

  if (ACTION == 1) {

  } else {
    $("#insurancecompany").val("");$("#agent").val("");$("#email").val("");$("#mobileno").val("");
  }
  $('html, body').animate({scrollTop: 0}, 'slow');
}
function checkvalidation(addtype = 0){
  
  var insurancecompany = $("#insurancecompany").val();
  var agent = $("#agent").val();
  var email = $("#email").val();
  var mobileno = $("#mobileno").val();
  var isvalidinsurancecompany = isvalidagent = isvalidemail = isvalidmobileno = 0
  PNotify.removeAll();

  
    if(insurancecompany == null){
        $("#insurancecompany_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select insurance company !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }else {
        $("#insurancecompany_div").removeClass("has-error is-focused");
        isvalidinsurancecompany = 1;
    }
    if(agent == ""){
        $("#agent_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter agent name!',styling: 'fontawesome',delay: '3000',type: 'error'});
    }else if(agent.length < 2){
        $("#agent_div").addClass("has-error is-focused");
        new PNotify({title: 'agent name require minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }else {
        $("#agent_div").removeClass("has-error is-focused");
        isvalidagent = 1;
    }

    if(email == ''){
      $("#email_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter email !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }else{
      if(!ValidateEmail(email)){
          $("#email_div").addClass("has-error is-focused");
          new PNotify({title: 'Please enter valid email address !',styling: 'fontawesome',delay: '3000',type: 'error'});
      }else{
          $("#email_div").removeClass("has-error is-focused");
          isvalidemail = 1;
      }
    }

    if(mobileno == ""){
        $("#mobileno_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter mobileno !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }else if(mobileno.length != 10){
        $("#mobileno_div").addClass("has-error is-focused");
        new PNotify({title: 'mobileno require minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }else {
        $("#mobileno_div").removeClass("has-error is-focused");
        isvalidmobileno = 1;
    }
  
  if(isvalidinsurancecompany == 1 && isvalidagent == 1 && isvalidemail == 1 && isvalidmobileno == 1){
      
    var formData = new FormData($('#form-insuranceagent')[0]);
    if(ACTION==0){
      var uurl = SITE_URL+"insurance-agent/insurance-agent-add";
      
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
            new PNotify({title: "Insurance Agent successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
            if(addtype==1){
              resetdata();
            }else{
              setTimeout(function() { window.location=SITE_URL+"insurance-agent"; }, 1500);
            }
          }else if(response==2){
            new PNotify({title: "Insurance Agent already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#name_div").addClass("has-error is-focused");
          }else{
            new PNotify({title: 'Insurance Agent not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      
      var uurl = SITE_URL+"insurance-agent/update-insurance-agent";
      
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
            new PNotify({title: "Insurance Agent successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
            if(addtype==1){
              setTimeout(function() {window.location=SITE_URL+"insurance-agent/add-insurance-agent"; }, 1500);
            }else{
              setTimeout(function() { window.location=SITE_URL+"insurance-agent"; }, 1500);
            }
          }else if(response==2){
            new PNotify({title: "Insurance Agent already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#name_div").addClass("has-error is-focused");
          }else{
            new PNotify({title: 'Insurance Agent not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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