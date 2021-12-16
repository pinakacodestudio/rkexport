if(ACTION==1 && $('#oldlogo').val()!=''){
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

$('#gstno').keyup(function() {
    if (this.value != this.value.replace(/[^A-Za-z0-9 \-\']/g, '')) {
        this.value = this.value.replace(/[^A-Za-z0-9 \-\']/g, '');
    }
    this.value = this.value.toUpperCase();
});

function resetdata(){
  
  $("#businessname_div").removeClass("has-error is-focused");
  $("#businessaddress_div").removeClass("has-error is-focused");
  $("#email_div").removeClass("has-error is-focused");
  $("#gstno_div").removeClass("has-error is-focused");
  $("#image_div").removeClass("has-error is-focused");
  $("#trustid_div").removeClass("has-error is-focused");
  $("#amount_div").removeClass("has-error is-focused");
  $("#notes_div").removeClass("has-error is-focused");
  $("#description_div").removeClass("has-error is-focused");
  $('.cke_inner').css({"border":"none"});

  var $imageupload = $('.imageupload');
  if($('#oldlogo').val()!=''){
    $('#companylogo img').attr('src',MAIN_LOGO_IMAGE_URL+$('#oldlogo').val());
    $imageupload.imageupload({
      url: SITE_URL,
      type: '1'
    });  
  }else{
    $imageupload.imageupload({
      url: SITE_URL,
      type: '0',
      allowedFormats: [ 'jpg', 'jpeg', 'png','ico']
    });
  }
  var description = $('#description').val();
  CKEDITOR.instances['description'].setData(description);

  $('#companylogo img').css({"border":"1px solid #f1f1f1"});
  $('html, body').animate({scrollTop:0},'slow');  

} 

function checkvalidation() {

  var businessname = $("#businessname").val().trim();
  var businessaddress = $("#businessaddress").val().trim();
  var email = $("#email").val().trim();
  var logobtn = $("#logobtn").html();
  var gstno = $("#gstno").val().trim();
  var trustid = $("#trustid").val().trim();
  var amount = $("#amount").val().trim();
  var description = CKEDITOR.instances['description'].getData();
  description = encodeURIComponent(description);
  CKEDITOR.instances['description'].updateElement();
  var notes = $("#notes").val().trim();
  
  var isvalidbusinessname = isvalidbusinessaddress = isvalidemail  = isvalidlogobtn = isvaliddescription = 0;
  var isvalidgstno = isvalidtrustid = isvalidnotes = 1;

  var regexp = /^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]+)$/;
 
  if(businessname == ''){
    $("#businessname_div").addClass("has-error is-focused");
    new PNotify({title: "Please enter business name !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidbusinessname = 0;
  }else { 
    if(businessname.length<3){
      $("#businessname_div").addClass("has-error is-focused");
      new PNotify({title: 'Business name require minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidbusinessname = 0;
    }else { 
      isvalidbusinessname = 1;
    }
  }
  if(businessaddress == ''){
    $("#businessaddress_div").addClass("has-error is-focused");
    new PNotify({title: "Please enter business address !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidbusinessaddress = 0;
  }else { 
    if(businessaddress.length<3){
      $("#businessname_div").addClass("has-error is-focused");
      new PNotify({title: 'Business address require minimum 3 characters!',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidbusinessaddress = 0;
    }else { 
      isvalidbusinessaddress = 1;
    }
  }
  if(email == ''){
    $("#email_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter email address !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidemail = 0;
  }
  else if(ValidateEmail(email) == false){
    $("#email_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter valid email address!',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidemail = 0;
  }else { 
    isvalidemail = 1;
  }
  if(logobtn.trim() == 'Select Image'){
    $('#companylogo img').css({"border":"1px solid #FFB9BD"});
    $("#image_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select company logo!',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidlogobtn = 0;
  }else { 
    $("#image_div").removeClass("has-error is-focused");
    isvalidlogobtn = 1;
  }
  /*if(gstno == ''){
    $("#gstno_div").addClass("has-error is-focused");
    new PNotify({title: "Please enter gst number !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidgstno = 0;
  }else { 
    if(gstno.length!=15){
      $("#gstno_div").addClass("has-error is-focused");
      new PNotify({title: 'GST number must be 15 characters!',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidgstno = 0;
    }else if(!regexp.test(gstno)){
      $("#gstno_div").addClass("has-error is-focused");
      new PNotify({title: 'GST number should have at least 1 alphabet and 1 digit!',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidgstno = 0;
    }else { 
      isvalidgstno = 1;
    }
  }*/
  if(description == ''){
    $("#description_div").addClass("has-error is-focused");
    $('.cke_inner').css({"background-color":"#FFECED","border":"1px solid #e51c23"});
    new PNotify({title: 'Please enter description !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvaliddescription = 0;
  }else if(description.length < 4){
    $("#description_div").addClass("has-error is-focused");
    $('.cke_inner').css({"background-color":"#FFECED","border":"1px solid #e51c23"});
    new PNotify({title: 'Description require minimum 4 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvaliddescription = 0;
  }else { 
      isvaliddescription = 1;
      $('.cke_inner').css({"border":"none"});
  }
  if(amount == 0){
    $("#amount_div").addClass("has-error is-focused");
    new PNotify({title: "Please enter amount !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidamount = 0;
  }else { 
    isvalidamount = 1;
  }
  if(trustid != ''){
    if(trustid.length<3){
      $("#trustid_div").addClass("has-error is-focused");
      new PNotify({title: 'Trust ID require minimum 3 characters!',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidtrustid = 0;
    }else { 
      isvalidtrustid = 1;
    }
  }
  if(notes != ''){
    if(notes.length<3){
      $("#gstno_div").addClass("has-error is-focused");
      new PNotify({title: 'Invoice notes require minimum 3 characters!',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidnotes = 0;
    }else { 
      isvalidnotes = 1;
    }
  }

  if(isvalidbusinessname == 1 && isvalidbusinessaddress ==1 && isvalidemail == 1 && isvalidgstno ==1 && isvalidlogobtn == 1 && isvalidnotes == 1 && isvaliddescription == 1 && isvalidamount == 1 && isvalidtrustid == 1)
  {
    var uurl = SITE_URL+"donationsetting/updatedonationsetting";
    var formData = new FormData($('#donationsettingform')[0]);
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
        // var a = $.parseJSON(response);
          if(response==1){
            new PNotify({title: 'Donation Settings successfully updated!',styling: 'fontawesome',delay: '3000',type: 'success'});
            setTimeout(function() { window.location.href = SITE_URL+"donationsetting"; }, 1500);
        }else if(response==2){
          new PNotify({title: 'Uploaded File is not an Image!',styling: 'fontawesome',delay: '3000',type: 'error'});
        }
        else{
          new PNotify({title: 'Donation Settings not updated!',styling: 'fontawesome',delay: '3000',type: 'error'});
        }
      },
      error: function(xhr) {
      
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

