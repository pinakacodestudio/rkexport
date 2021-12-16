$(document).ready(function() {

  $('.yesno input[type="checkbox"]').bootstrapToggle({
    on: 'On',
    off: 'Off',
    onstyle: 'primary',
    offstyle: 'danger',
   
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
  
    $('#balancedate').datepicker({
      todayHighlight: true,
      format: 'dd/mm/yyyy',
      autoclose: true,
      todayBtn:"linked",
      container:'#vendorform',
    });
    
    $('#remove').click(function(){
      $('#removeoldImage').val('1');
    });
  
    getprovince($('#countryid').val());
    getcity($('#provinceid').val());
    
    $('#membercode').bind('keyup blur',function(){ 
      var node = $(this);
      node.val(node.val().replace(/[^a-zA-Z0-9]/g,'').toUpperCase() ); 
    });
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
  })
  function resetdata(){
  
    $("#name_div").removeClass("has-error is-focused");
    $("#membercode_div").removeClass("has-error is-focused");
    $("#email_div").removeClass("has-error is-focused");
    $("#password_div").removeClass("has-error is-focused");
    $("#mobile_div").removeClass("has-error is-focused");
    $("#secondarymobileno_div").removeClass("has-error is-focused");
    $("#country_div").removeClass("has-error is-focused");
    $("#province_div").removeClass("has-error is-focused");
    $("#city_div").removeClass("has-error is-focused");
    $("#gstno_div").removeClass("has-error is-focused");
    $("#debitlimit_div").removeClass("has-error is-focused");
    $("#minimumstocklimit_div").removeClass("has-error is-focused");
    $("#paymentcycle_div").removeClass("has-error is-focused");
    $("#addressname_div").removeClass("has-error is-focused");
    $("#addressemail_div").removeClass("has-error is-focused");
    $("#addressmobile_div").removeClass("has-error is-focused");
    $("#memberaddress_div").removeClass("has-error is-focused");
    $("#postalcode_div").removeClass("has-error is-focused");
  
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
    }else{
      
      $("#name").val("");
      $("#email").val("");
      $("#mobileno").val("");
      $("#countrycodeid").val(countrycodeid);
      $("#password").val("");
      $("#membercode").val("");
      $("#countryid").val(countryid);
      $("#secondarymobileno").val("");
      $("#secondarycountrycodeid").val(countrycodeid);
      $("#provinceid").val(0);
      $("#cityid").val(0);
      $("#gstno").val("");
      $("#debitlimit").val("");
      $("#minimumstocklimit").val("");
      $("#paymentcycle").val("");
  
      getprovince(countryid);
      $('.imageupload').imageupload({
        url: SITE_URL,
        type: '0',
      });
  
      $(".selectpicker").selectpicker("refresh");
      $('#yes').prop("checked", true);
    }
    $('html, body').animate({scrollTop:0},'slow');
  }
  
  function checkvalidation(){
  
    var roleid = $("#roleid").val();
    var membercode = $("#membercode").val();
    var countryid = $("#countryid").val();
    var provinceid = $("#provinceid").val();
    var cityid = $("#cityid").val();
    var gstno = $("#gstno").val();
    var name = $("#name").val().trim();
    var email = $("#email").val().trim();
    var mobileno = $("#mobileno").val().trim();
    var countrycodeid = $("#countrycodeid").val();    
    var password = $("#password").val();  
    var secondarycountrycodeid = $("#secondarycountrycodeid").val();    
    var secondarymobileno = $("#secondarymobileno").val().trim();
    var addressname = $("#addressname").val();  
    var addressemail = $("#addressemail").val();  
    var addressmobile = $("#addressmobile").val();  
    var postalcode = $("#postalcode").val();  
    var address = $("#memberaddress").val();  
     
    var isvalidmembercode = isvalidcountryid = isvalidprovinceid = isvalidcityid =
        isvalidname = isvalidemail = isvalidmobileno= isvalidcountrycodeid = 0;
    var isvalidgstno = isvalidpassword = isvalidsecondarymobileno = isvalidsecondarycountrycodeid = isvalidaddressname = isvalidaddressemail = isvalidaddressmobile = isvalidpostalcode = isvalidaddress =  1;
  
    if(name==""){
      $("#name_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter name !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidname = 0;
    } else {
      if(name.length<2){
        $("#name_div").addClass("has-error is-focused");
        new PNotify({title: 'Name required minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidname = 0;
      }else{
        $("#name_div").removeClass("has-error is-focused");
        isvalidname = 1;
      }
    }
  
    if(membercode==""){
      $("#membercode_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter vendor code !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
      if(membercode.length<6){
        $("#membercode_div").addClass("has-error is-focused");
        new PNotify({title: 'Vendor code required minimum 6 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      }else{
        $("#membercode_div").removeClass("has-error is-focused");
        isvalidmembercode = 1;
      }
    }
  
    if(password=="" && roleid!=0) {
        $("#password_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter password !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidpassword = 0;
    } else {
      if(roleid!=0 || password!=""){
        if(CheckPassword(password)==false){
          $("#password_div").addClass("has-error is-focused");
          new PNotify({title: 'Please enter password between 6 to 20 characters which contain at least one alphabetic, numeric & special character !',styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidpassword = 0;
        }else { 
          $("#password_div").removeClass("has-error is-focused");
          isvalidpassword = 1;
        }
      }
      
    }
  
    if(email == ''){
      $("#email_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter email !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidemail = 0;
    }else{
      if(!ValidateEmail(email)){
          $("#email_div").addClass("has-error is-focused");
          new PNotify({title: 'Please enter valid Email !',styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidemail = 0;
      }else{
          $("#email_div").removeClass("has-error is-focused");
          isvalidemail = 1;
      }
    }
  
    if(mobileno=="") {
        $("#mobile_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter mobile number !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
      if(mobileno.length!=10){
        $("#mobile_div").addClass("has-error is-focused");
        new PNotify({title: 'Mobile number allow only 10 digits !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmobileno = 0;
      }else{
        $("#mobile_div").removeClass("has-error is-focused");
        isvalidmobileno = 1;
      }
    }

    if(secondarymobileno!=""){

      if(secondarycountrycodeid=="" || secondarycountrycodeid==0) {
        $("#secondarymobileno_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select secondary mobile country code !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidsecondarycountrycodeid = 0;
      } else {
        $("#secondarymobileno_div").removeClass("has-error is-focused");
      }
      if(secondarymobileno.length!=10){
        $("#secondarymobileno_div").addClass("has-error is-focused");
        new PNotify({title: 'Secondary mobile number allow only 10 digits !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidsecondarymobileno = 0;
      }
    }else{
      $("#secondarymobileno_div").removeClass("has-error is-focused");
    }
  
    if(countrycodeid=="" || countrycodeid==0) {
        $("#countrycodeid_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select country code !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
        $("#countrycodeid_div").removeClass("has-error is-focused");
        isvalidcountrycodeid = 1;
    }
  
    if(countryid==0) {
        $("#country_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select country !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
        $("#country_div").removeClass("has-error is-focused");
        isvalidcountryid = 1;
    }
  
    if(provinceid==0) {
        $("#province_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select province !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
        $("#province_div").removeClass("has-error is-focused");
        isvalidprovinceid = 1;
    }
  
    if(cityid==0) {
        $("#city_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select city !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
        $("#city_div").removeClass("has-error is-focused");
        isvalidcityid = 1;
    }
  
    if(gstno!=''){
      var regexp = /^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]+)$/;
      if (gstno.length != 15) {
        $("#gstno_div").addClass("has-error is-focused");
        new PNotify({title: 'GST number must be 15 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidgstno = 0;
      } else if (!regexp.test(gstno)) {
        $("#gstno_div").addClass("has-error is-focused");
        new PNotify({title: 'GST number should have at least 1 alphabet and 1 digit !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidgstno = 0;
      } else {
        $("#gstno_div").removeClass("has-error is-focused");
      }
    }

 /*    if(ACTION==0){ */

      if(addressname!='' || addressemail!='' || addressmobile!='' || postalcode!='' || address!=''){

          if(addressname ==""){
            $("#addressname_div").addClass("has-error is-focused");
            new PNotify({title: 'Please enter name !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidaddressname = 0;
          } else {
            if(addressname.length<2){
              $("#addressname_div").addClass("has-error is-focused");
              new PNotify({title: 'Name required minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
              isvalidaddressname = 0;
            }else{
              $("#addressname_div").removeClass("has-error is-focused");
              isvalidaddressname = 1;
            }
          }

          if(addressemail == ''){
            $("#addressemail_div").addClass("has-error is-focused");
            new PNotify({title: 'Please enter email !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidaddressemail = 0;
          }else{
            if(!ValidateEmail(addressemail)){
                $("#addressemail_div").addClass("has-error is-focused");
                new PNotify({title: 'Please enter valid  Email !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidaddressemail = 0;
            }else{
                $("#addressemail_div").removeClass("has-error is-focused");
                isvalidaddressemail = 1;
            }
          }

          if(addressmobile=="") {
            $("#addressmobile_div").addClass("has-error is-focused");
            new PNotify({title: 'Please enter mobile number !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidaddressmobile = 0;
          } else {
            if(addressmobile.length!=10){
              $("#addressmobile_div").addClass("has-error is-focused");
              new PNotify({title: 'Mobile number allow only 10 digits !',styling: 'fontawesome',delay: '3000',type: 'error'});
              isvalidaddressmobile = 0;
            }else{
              $("#addressmobile_div").removeClass("has-error is-focused");
              isvalidaddressmobile = 1;
            }
          }

          if(postalcode=="") {
            $("#postalcode_div").addClass("has-error is-focused");
            new PNotify({title: 'Please enter postal code !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidpostalcode = 0;
          }else{
              $("#postalcode_div").removeClass("has-error is-focused");
              isvalidpostalcode = 1;
            
          }

          if(address=="") {
            $("#memberaddress_div").addClass("has-error is-focused");
            new PNotify({title: 'Please enter address !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidaddress = 0;
          } else {
            if(address.length<3){
              $("#memberaddress_div").addClass("has-error is-focused");
              new PNotify({title: 'Address have must be 3 characters!',styling: 'fontawesome',delay: '3000',type: 'error'});
              isvalidaddress = 0;
            }else{
              isvalidaddress = 1;
            }
          }
        }else{
          $("#addressname_div").removeClass("has-error is-focused");
          $("#addressemail_div").removeClass("has-error is-focused");
          $("#addressmobile_div").removeClass("has-error is-focused");
          $("#memberaddress_div").removeClass("has-error is-focused");
          $("#postalcode_div").removeClass("has-error is-focused");
        }

 /*    } */
  
    if(isvalidmembercode==1 && isvalidcountryid==1 && isvalidprovinceid==1 && isvalidcityid &&
        isvalidgstno==1 &&
        isvalidname==1 && isvalidemail==1 && isvalidmobileno==1 && isvalidcountrycodeid==1 && isvalidpassword==1  && isvalidsecondarymobileno == 1 && isvalidsecondarycountrycodeid == 1 && isvalidaddressname==1 && isvalidaddressemail == 1 && isvalidaddressmobile == 1 && isvalidpostalcode == 1 && isvalidaddress==1 ){
              
      var formData = new FormData($('#vendorform')[0]);
      if(ACTION == 0){    
        var uurl = SITE_URL+"vendor/add-vendor";
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
              new PNotify({title: 'Vendor Successfully Added !',styling: 'fontawesome',delay: '3000',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"vendor"; }, 1500);
            }else if(response == 2) {
              new PNotify({title: 'Mobile number already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else if(response == 3) {
              new PNotify({title: 'Email already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else if(response==5){
              new PNotify({title: 'Vendor profile image not uploaded !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else if(response==6){
              new PNotify({title: 'Invalid type of vendor profile image !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else if(response==7){
              new PNotify({title: 'Vendor code already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else if(response==8){
              new PNotify({title: 'Invalid email address !',styling: 'fontawesome',delay: '3000',type: 'error'});
            } else {
              new PNotify({title: 'Vendor data not Added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
        var uurl = SITE_URL+"vendor/update-vendor";
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
                  new PNotify({title: 'Vendor successfully updated !',styling: 'fontawesome',delay: '3000',type: 'success'});
                  
                  setTimeout(function() { window.location=SITE_URL+FROM_URL; }, 1500);
                }else if(response == 2) {
                    new PNotify({title: 'Mobile number already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(response == 3) {
                    new PNotify({title: 'Email already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(response==4){
                  new PNotify({title: 'Vendor profile image not uploaded !',styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(response==5){
                  new PNotify({title: 'Invalid type of vendor profile image !',styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(response==6){
                  new PNotify({title: 'Vendor code already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(response==7){
                  new PNotify({title: 'Invalid email address !',styling: 'fontawesome',delay: '3000',type: 'error'});
                } else {
                  new PNotify({title: 'Vendor data not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
                
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