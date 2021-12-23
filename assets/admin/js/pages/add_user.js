$(document).ready(function() {
  
  
  $('#joindate').datepicker({
    todayHighlight: true,
    format: 'dd/mm/yyyy',
    orientation: 'top',
    autoclose: true,
    todayBtn: "linked"
});
$("#old_receipt_div").hide();

  $('#birthdate').datepicker({
    todayHighlight: true,
    format: 'dd/mm/yyyy',
    orientation: 'top',
    autoclose: true,
    todayBtn: "linked"
});

$("#old_receipt_div").hide();
  $('#anniversarydate').datepicker({
    todayHighlight: true,
    format: 'dd/mm/yyyy',
    orientation: 'top',
    autoclose: true,
    todayBtn: "linked"
});
$("#old_receipt_div").hide();



$('#remove').click(function(){
   $('#removeoldreceipt').val('1');
});

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
  if(ACTION==1){
    if($("#eodmailon").prop("checked")){
      $(".notification_setting").show();
    }else{
      $(".notification_setting").hide();
    }
  }
  $("input[name='eodmail']").click(function(){
    if($(this).val()==1){
      $(".notification_setting").show();
    }else{
      $(".notification_setting").hide();
    }
  });
});

function resetdata(){

 
  $("#name_div").removeClass("has-error is-focused");
  $("#userrole_div").removeClass("has-error is-focused");
  $("#email_div").removeClass("has-error is-focused");
  $("#password_div").removeClass("has-error is-focused");
  $("#mobile_div").removeClass("has-error is-focused");

  if(ACTION==1){

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
    
    $('#userroleid').val(olduserroleid);
    $('.selectpicker').selectpicker('refresh');
    $('html, body').animate({scrollTop:0},'slow');
  }else{

    $('.imageupload').imageupload({
      url: SITE_URL,
      type: '0',
    });

  
    $('#name').val('');
    $('#email').val('');
    $('#password').val('');
    $('#mobileno').val('');
    $('#userroleid').val(0);
     
    $('.selectpicker').selectpicker('refresh');
   
    $('#yes').prop("checked", true);
    $('html, body').animate({scrollTop:0},'slow');

  }
}
function checkvalidation(){

  
  var name = $("#name").val().trim();
  var mobileno = $("#mobileno").val();
  var email = $("#email").val().trim();
  var password = $("#password").val();
  var userroleid = $("#userroleid").val();

  var isvalidname = isvaliduserroleid = isvalidmobileno = isvalidemail = 0;
  var isvalidpassword = 1;

  
  if(name == ''){
    $("#name_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter name !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidname = 0;
  }else {
    if(name.length<2){
      $("#name_div").addClass("has-error is-focused");
      new PNotify({title: 'Name required minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidname = 0;
    }else{
      $("#name_div").removeClass("has-error is-focused");
      isvalidname = 1;
    }
  }
  
 
  if(mobileno == ''){
    $("#mobile_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter mobile no. !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidmobileno = 0;
  }else{
    if(mobileno.length<10){
      $("#mobileno_div").addClass("has-error is-focused");
      new PNotify({title: 'Mobile no. allow minimum 10 digits !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidmobileno = 0;
    }else{
      $("#mobile_div").removeClass("has-error is-focused");
      isvalidmobileno = 1;
    }
  }
  if(email == ''){
    $("#email_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter email !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
  
  if(password==''){
    $("#password_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter password !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidpassword = 0;
  }else{
    if(CheckPassword(password)==false){
      $("#password_div").addClass('has-error is-focused');
      new PNotify({title: 'Please enter password between 6 to 20 characters which contain at least one alphabetic, numeric & special character !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidpassword = 0;
    }else { 
      $("#password_div").removeClass("has-error is-focused");
      isvalidpassword = 1;
    }
  }
  
  if(userroleid == 0){
    $("#userrole_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select employee role !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvaliduserroleid = 0;
  }else { 
    $("#userrole_div").removeClass("has-error is-focused");
    isvaliduserroleid = 1;
  }
  
  if(isvalidname==1 && isvalidmobileno==1 && isvalidemail==1 && isvalidpassword==1 && isvaliduserroleid == 1){

  
    var formData = new FormData($('#userform')[0]);
    if(ACTION==0){

      var uurl = SITE_URL+"user/add-user";
      
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
            new PNotify({title: 'Employee successfully added !',styling: 'fontawesome',delay: '3000',type: 'success'});
            setTimeout(function() { window.location=SITE_URL+"user"; }, 1500);
          }else if(response==2){
            new PNotify({title: 'Employee already register !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(response==3){
            new PNotify({title: 'Employee profile image not uploaded !',styling: 'fontawesome',delay: '3000',type: 'error'});
            
          }else if(response==4){
            new PNotify({title: 'Invalid type of employee profile image !',styling: 'fontawesome',delay: '3000',type: 'error'});
            
          }else  if(response==0){
            new PNotify({title: 'Employee not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      
      var uurl = SITE_URL+"user/update-user";

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
            new PNotify({title: 'Employee successfully Updated !',styling: 'fontawesome',delay: '3000',type: 'success'});
             setTimeout(function() { window.location=SITE_URL+"user"; }, 1500);
          }else if(response==2){
            new PNotify({title: 'Employee email already register !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(response==3){
            new PNotify({title: 'Employee profile image not uploaded !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(response==4){
            new PNotify({title: 'Invalid type of employee profile image !',styling: 'fontawesome',delay: '3000',type: 'error'});
            
          }else  if(response==0){
            new PNotify({title: 'Employee not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

