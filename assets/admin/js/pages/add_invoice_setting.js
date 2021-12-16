$(document).ready(function() {

  $("#countryid").val(countryid);
  $("#countryid").selectpicker('refresh');

  if(ACTION==1){
    $('#channelid').val(CHANNELID);
    getprovince(countryid);
    getcity(provinceid);
  }

    $('#countryid').on('change', function (e) {
          
      $('#provinceid')
        .find('option')
        .remove()
        .end()
        .append('<option value="">Select Province</option>')
        .val('0')
      ;
      $('#cityid')
        .find('option')
        .remove()
        .end()
        .append('<option value="">Select City</option>')
        .val('0')
      ;
      
      $('#provinceid').selectpicker('refresh');
      $('#cityid').selectpicker('refresh');
      
      getprovince(this.value);
    });
    $('#provinceid').on('change', function (e) {
          
        $('#cityid')
          .find('option')
          .remove()
          .end()
          .append('<option value="">Select City</option>')
          .val('0')
        ;
       
        $('#cityid').selectpicker('refresh');
        
        getcity(this.value);
    });
    $(".selectpicker").selectpicker('refresh');

});

 $(document).ready(function() {
   if(ACTION==1 && (CHANNELID!=0 || CHANNELID!="")){
     getmembers();
    }
    $("#channelid").change(function(){
      getmembers();
    });
});
function getmembers(){
    
  var channelid = $("#channelid").val();

  $('#memberid')
      .find('option')
      .remove()
      .end()
      .append('<option value="0">Select '+Member_label+'</option>')
      .val('whatever')
      ;
  if(channelid!=""){
      var uurl = SITE_URL+"member/getmembers";
      $.ajax({
          url: uurl,
          type: 'POST',
          data: {channelid:channelid},
          dataType: 'json',
          async: false,
          success: function(response){
      
              for(var i = 0; i < response.length; i++) {
          
                  $('#memberid').append($('<option>', { 
                  value: response[i]['id'],
                  text : ucwords(response[i]['name'])
                  }));
          
              }
              if(MEMBERID!=0){
                  $('#memberid').val(MEMBERID);
              }
          },
          error: function(xhr) {
          //alert(xhr.responseText);
          },
      });
  }
  $('#memberid').selectpicker('refresh');
}




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
  $("#image_div").removeClass("has-error is-focused");
  $("#gstno_div").removeClass("has-error is-focused");
  $('.cke_inner').css({"border":"none"});
  $("#cityid_div").removeClass("has-error is-focused");
  $("#memberid_div").removeClass("has-error is-focused");
  $('#s2id_cityid > a').css({"background-color":"#FFF","border":"#D2D2D2"});

  
  $("#provinceid").val(0);
  $("#cityid").val(0);
  $("#channelid").val('');
  $("#memberid").val(0);

  $('.imageupload').imageupload({
    url: SITE_URL,
    type: '0',
  });

  if(ACTION==1){
    $("#countryid").val(countryid);
    $("#channelid").val(CHANNELID);
    getprovince(countryid);
    getcity(provinceid);
    getmembers();
  


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

  }
 
  $(".selectpicker").selectpicker('refresh');

  $('#companylogo img').css({"border":"1px solid #f1f1f1"});
  $('html, body').animate({scrollTop:0},'slow');  

} 

/* function checkvalidation() {

  var businessname = $("#businessname").val().trim();
  var businessaddress = $("#businessaddress").val().trim();
  var email = $("#email").val().trim();
  var logobtn = $("#logobtn").html();
  var gstno = $("#gstno").val().trim();
  var invoicenotes = CKEDITOR.instances['invoicenotes'].getData();
  invoicenotes = encodeURIComponent(invoicenotes);
  CKEDITOR.instances['invoicenotes'].updateElement();
  var countryid = $("#countryid").val();
  var provinceid = $("#provinceid").val();
  var cityid = $("#cityid").val();
  var postcode = $("#postcode").val();
  
  var isvalidbusinessname = isvalidemail = isvalidgstno = isvalidbusinessaddress = isvalidcountryid = isvalidprovinceid = isvalidcityid = isvalidpostcode = isvalidlogobtn = isvalidinvoicenotes = 1;
  
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
      $("#businessname_div").removeClass("has-error is-focused");
    }
  }
  if(email == ''){
    $("#email_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter email address !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidemail = 0;
  } else if(ValidateEmail(email) == false){
    $("#email_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter valid email address!',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidemail = 0;
  }else { 
    $("#email_div").removeClass("has-error is-focused");
  }
  if(businessaddress == ''){
    $("#businessaddress_div").addClass("has-error is-focused");
    new PNotify({title: "Please enter business address !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidbusinessaddress = 0;
  }else { 
    if(businessaddress.length<3){
      $("#businessaddress_div").addClass("has-error is-focused");
      new PNotify({title: 'Business address require minimum 3 characters!',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidbusinessaddress = 0;
    }else { 
      $("#businessaddress_div").removeClass("has-error is-focused");
    }
  }
  if(gstno != ''){
    if(gstno.length!=15){
      $("#gstno_div").addClass("has-error is-focused");
      new PNotify({title: 'GST number must be 15 characters!',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidgstno = 0;
    }else if(!regexp.test(gstno)){
      $("#gstno_div").addClass("has-error is-focused");
      new PNotify({title: 'GST number should have at least 1 alphabet and 1 digit!',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidgstno = 0;
    }else { 
      $("#gstno_div").removeClass("has-error is-focused");
    }
  }

  if(countryid==0) {
    $("#country_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select country !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidcountryid = 0;
  } else {
    $("#country_div").removeClass("has-error is-focused");
  }

  if(provinceid==0) {
    $("#province_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select province !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidprovinceid = 0;
  } else {
    $("#province_div").removeClass("has-error is-focused");
  }

  if(cityid==0) {
      $("#city_div").addClass("has-error is-focused");
      new PNotify({title: 'Please select city !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidcityid = 0;
  } else {
      $("#city_div").removeClass("has-error is-focused");
  }
  if(postcode == ''){
    $('#postcode_div').addClass("has-error is-focused");
    new PNotify({title: 'Please enter postcode !',styling: 'fontawesome',delay: '3000',type: 'error'});  
    isvalidpostcode = 0;
  }else if(postcode < 4){
      $('#postcode_div').addClass("has-error is-focused");
      new PNotify({title: 'Please enter minimum 4 digit post number !',styling: 'fontawesome',delay: '3000',type: 'error'});  
      isvalidpostcode = 0;
  }else{
    $('#postcode_div').removeClass("has-error is-focused");
  }

  if(logobtn.trim() == 'Select Image'){
    $('#companylogo img').css({"border":"1px solid #FFB9BD"});
    $("#image_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select company logo!',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidlogobtn = 0;
  }else { 
    $("#image_div").removeClass("has-error is-focused");
  }
  if(invoicenotes != ''){
    if(invoicenotes.length<3){
      $("#invoicenotes_div").addClass("has-error is-focused");
      $('.cke_inner').css({"background-color":"#FFECED","border":"1px solid #e51c23"});
      new PNotify({title: 'Invoice notes require minimum 3 characters!',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidinvoicenotes = 0;
    }else { 
      $("#invoicenotes_div").removeClass("has-error is-focused");
      $('.cke_inner').css({"border":"none"});
    }
  }else{
    $("#invoicenotes_div").removeClass("has-error is-focused");
    $('.cke_inner').css({"border":"none"});
  }
  
  if(isvalidbusinessname==1 && isvalidbusinessaddress==1 && isvalidemail==1 && isvalidgstno==1 && isvalidcountryid==1 && isvalidprovinceid==1 && isvalidcityid==1 && isvalidpostcode==1 && isvalidlogobtn==1 && isvalidinvoicenotes==1) {
    
    var uurl = SITE_URL+"invoice-setting/update-invoice-setting";
    var formData = new FormData($('#invoicesettingform')[0]);

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
          new PNotify({title: 'Invoice Settings successfully updated!',styling: 'fontawesome',delay: '3000',type: 'success'});
          setTimeout(function() { window.location.href = SITE_URL+"invoice-setting"; }, 1500);
        }else if(response==2){
          new PNotify({title: 'Uploaded File is not an Image!',styling: 'fontawesome',delay: '3000',type: 'error'});
        } else{
          new PNotify({title: 'Invoice Settings not updated!',styling: 'fontawesome',delay: '3000',type: 'error'});
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
} */

function checkvalidation(){
 
  var businessname = $("#businessname").val().trim();
  var businessaddress = $("#businessaddress").val().trim();
  var email = $("#email").val().trim();
  var logobtn = $("#logobtn").html();
  var gstno = $("#gstno").val().trim();
  var invoicenotes = CKEDITOR.instances['invoicenotes'].getData();
  invoicenotes = encodeURIComponent(invoicenotes);
  CKEDITOR.instances['invoicenotes'].updateElement();
  var countryid = $("#countryid").val();
  var provinceid = $("#provinceid").val();
  var cityid = $("#cityid").val();
  var postcode = $("#postcode").val();
  var channelid = $("#channelid").val();
  var memberid = $("#memberid").val();

  var isvalidbusinessname = isvalidemail = isvalidgstno = isvalidbusinessaddress = isvalidcountryid = isvalidprovinceid = isvalidcityid = isvalidpostcode = isvalidlogobtn = isvalidinvoicenotes =  isvalidmemberid = 1;
  
  
  PNotify.removeAll();

  var regexp = /^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]+)$/;

  if(channelid!="" && channelid!="0"){
    if(memberid == 0){
      $("#member_div").addClass("has-error is-focused");
      new PNotify({title: 'Please select '+member_label+' !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidmemberid = 0;
    }else{
      $("#member_div").removeClass("has-error is-focused");
    }
  }else{
    $("#member_div").removeClass("has-error is-focused");
  }

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
      $("#businessname_div").removeClass("has-error is-focused");
    }
  }
  if(email == ''){
    $("#email_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter email address !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidemail = 0;
  } else if(ValidateEmail(email) == false){
    $("#email_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter valid email address!',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidemail = 0;
  }else { 
    $("#email_div").removeClass("has-error is-focused");
  }
  if(businessaddress == ''){
    $("#businessaddress_div").addClass("has-error is-focused");
    new PNotify({title: "Please enter business address !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidbusinessaddress = 0;
  }else { 
    if(businessaddress.length<3){
      $("#businessaddress_div").addClass("has-error is-focused");
      new PNotify({title: 'Business address require minimum 3 characters!',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidbusinessaddress = 0;
    }else { 
      $("#businessaddress_div").removeClass("has-error is-focused");
    }
  }
  if(gstno != ''){
    if(gstno.length!=15){
      $("#gstno_div").addClass("has-error is-focused");
      new PNotify({title: 'GST number must be 15 characters!',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidgstno = 0;
    }else if(!regexp.test(gstno)){
      $("#gstno_div").addClass("has-error is-focused");
      new PNotify({title: 'GST number should have at least 1 alphabet and 1 digit!',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidgstno = 0;
    }else { 
      $("#gstno_div").removeClass("has-error is-focused");
    }
  }

  if(countryid==0) {
    $("#country_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select country !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidcountryid = 0;
  } else {
    $("#country_div").removeClass("has-error is-focused");
  }

  if(provinceid==0) {
    $("#province_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select province !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidprovinceid = 0;
  } else {
    $("#province_div").removeClass("has-error is-focused");
  }

  if(cityid==0) {
      $("#city_div").addClass("has-error is-focused");
      new PNotify({title: 'Please select city !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidcityid = 0;
  } else {
      $("#city_div").removeClass("has-error is-focused");
  }
  if(postcode == ''){
    $('#postcode_div').addClass("has-error is-focused");
    new PNotify({title: 'Please enter postcode !',styling: 'fontawesome',delay: '3000',type: 'error'});  
    isvalidpostcode = 0;
  }else if(postcode < 4){
      $('#postcode_div').addClass("has-error is-focused");
      new PNotify({title: 'Please enter minimum 4 digit post number !',styling: 'fontawesome',delay: '3000',type: 'error'});  
      isvalidpostcode = 0;
  }else{
    $('#postcode_div').removeClass("has-error is-focused");
  }

  if(logobtn.trim() == 'Select Image'){
    $('#companylogo img').css({"border":"1px solid #FFB9BD"});
    $("#image_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select company logo!',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidlogobtn = 0;
  }else { 
    $("#image_div").removeClass("has-error is-focused");
  }
  if(invoicenotes != ''){
    if(invoicenotes.length<3){
      $("#invoicenotes_div").addClass("has-error is-focused");
      $('.cke_inner').css({"background-color":"#FFECED","border":"1px solid #e51c23"});
      new PNotify({title: 'Invoice notes require minimum 3 characters!',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidinvoicenotes = 0;
    }else { 
      $("#invoicenotes_div").removeClass("has-error is-focused");
      $('.cke_inner').css({"border":"none"});
    }
  }else{
    $("#invoicenotes_div").removeClass("has-error is-focused");
    $('.cke_inner').css({"border":"none"});
  }
  
  

  if(isvalidbusinessname==1 && isvalidbusinessaddress==1 && isvalidemail==1 && isvalidgstno==1 && isvalidcountryid==1 && isvalidprovinceid==1 && isvalidcityid==1 && isvalidpostcode==1 && isvalidlogobtn==1 && isvalidinvoicenotes==1 && isvalidmemberid == 1) {
    
   
    var formData = new FormData($('#invoicesettingform')[0]);
    if(ACTION==0){
      var uurl = SITE_URL+"invoice-setting/add-invoice-setting";
      
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
            new PNotify({title: 'Invoice Settings successfully added!',styling: 'fontawesome',delay: '3000',type: 'success'});
            setTimeout(function() { window.location.href = SITE_URL+"invoice-setting"; }, 1500);
          }else if(response==2){
            new PNotify({title: 'Invoice setting already exists!',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(response==3){
            new PNotify({title: 'Uploaded File is not an Image!',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(response==3){
            new PNotify({title: 'Image not uploaded!',styling: 'fontawesome',delay: '3000',type: 'error'});
          } else{
            new PNotify({title: 'Invoice Settings not added!',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"invoice-setting/update-invoice-setting";
      
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
            new PNotify({title: 'Invoice Settings successfully updated!',styling: 'fontawesome',delay: '3000',type: 'success'});
            setTimeout(function() { window.location.href = SITE_URL+"invoice-setting"; }, 1500);
          }else if(response==2){
            new PNotify({title: 'Invoice setting already exists!',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(response==3){
            new PNotify({title: 'Uploaded File is not an Image!',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(response==3){
            new PNotify({title: 'Image not uploaded!',styling: 'fontawesome',delay: '3000',type: 'error'});
          } else{
            new PNotify({title: 'Invoice Settings not updated!',styling: 'fontawesome',delay: '3000',type: 'error'});
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

