function checkvalidation() {
 
  //payumoney 
  var merchantid = $("#merchantid").val().trim();
  var merchantkey = $("#merchantkey").val().trim();
  var merchantsalt = $("#merchantsalt").val().trim();
  var authheader = $("#authheader").val().trim();
  //paytm 
  var paytmmerchantid = $("#paytmmerchantid").val().trim();
  var paytmmerchantkey = $("#paytmmerchantkey").val().trim();
  var merchantwebsiteforweb = $("#merchantwebsiteforweb").val().trim();
  var merchantwebsiteforapp = $("#merchantwebsiteforapp").val().trim();
  var channelidforweb = $("#channelidforweb").val().trim();
  var channelidforapp = $("#channelidforapp").val().trim();
  var industrytypeid = $("#industrytypeid").val().trim();
  //payu
  var payumerchantid = $("#payumerchantid").val().trim();
  var payumerchantkey = $("#payumerchantkey").val().trim();
  var payumerchantsalt = $("#payumerchantsalt").val().trim();
  var payuauthheader = $("#payuauthheader").val().trim();

  var activeplan = $("#activeplan").val().trim();

  PNotify.removeAll();
  var isvalidmerchantkey = isvalidmerchantid = isvalidauthheader = isvalidmerchantsalt = isvalidpaytmmerchantkey = isvalidpaytmmerchantid = isvalidpaytmmerchantwebsiteforweb = isvalidpaytmmerchantwebsiteforapp = isvalidpaytmchannelidforweb = isvalidpaytmchannelidforapp = isvalidpaytmindustrytypeid = isvalidpayumerchantkey = isvalidpayumerchantid = isvalidpayuauthheader = isvalidpayumerchantsalt = isvalidactiveplan = 1;

  //Activeplan validation
  if(activeplan == "" || activeplan == "0"){
    $("#activeplan_div").addClass("has-error is-focused");
    new PNotify({title: "Please Select Plan !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidactiveplan = 0;
  }
  //Payumoney Validation
  if(activeplan==1){
      if(merchantid == ""){
        $("#merchantid_div").addClass("has-error is-focused");
        new PNotify({title: "Please enter Merchant ID",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmerchantid = 0;
      }
      
      if(merchantkey == ""){
        $("#merchantkey_div").addClass("has-error is-focused");
        new PNotify({title: "Please enter merchant key",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmerchantkey = 0;
      }
      
      if(merchantsalt == ""){
        $("#merchantsalt_div").addClass("has-error is-focused");
        new PNotify({title: "Please enter merchant salt",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmerchantsalt = 0;
    
      }
          
      if(authheader == "" ){
        $("#authheader_div").addClass("has-error is-focused");
        new PNotify({title: "Please enter auth header",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidauthheader = 0;
      }
  }
  //Paytm Validation
  if(activeplan==2){
    if(paytmmerchantid == ""){
     $("#paytmmerchantid_div").addClass("has-error is-focused");
     new PNotify({title: "Please enter Paytm Merchant ID",styling: 'fontawesome',delay: '3000',type: 'error'});
     isvalidpaytmmerchantid = 0;
   }
   
   if(paytmmerchantkey == ""){
     $("#paytmmerchantkey_div").addClass("has-error is-focused");
     new PNotify({title: "Please enter Paytm merchant key",styling: 'fontawesome',delay: '3000',type: 'error'});
     isvalidpaytmmerchantkey = 0;
   }
   
   if(merchantwebsiteforweb == ""){
     $("#merchantwebsiteforweb_div").addClass("has-error is-focused");
     new PNotify({title: "Please enter Merchant website for web",styling: 'fontawesome',delay: '3000',type: 'error'});
     isvalidpaytmmerchantwebsiteforweb = 0;
 
   }

   if(merchantwebsiteforapp == "" ){
     $("#merchantwebsiteforapp_div").addClass("has-error is-focused");
     new PNotify({title: "Please enter Merchant website for app",styling: 'fontawesome',delay: '3000',type: 'error'});
     isvalidpaytmmerchantwebsiteforapp = 0;
   }
 
   if(channelidforweb == "" ){
     $("#channelidforweb_div").addClass("has-error is-focused");
     new PNotify({title: "Please enter Channel id for web",styling: 'fontawesome',delay: '3000',type: 'error'});
     isvalidpaytmchannelidforweb = 0;
   }
 
   if(channelidforapp == "" ){
     $("#channelidforapp_div").addClass("has-error is-focused");
     new PNotify({title: "Please enter Channel id for app",styling: 'fontawesome',delay: '3000',type: 'error'});
     isvalidpaytmchannelidforapp = 0;
   }

   if(industrytypeid == "" ){
     $("#industrytypeid_div").addClass("has-error is-focused");
     new PNotify({title: "Please enter Industry type id",styling: 'fontawesome',delay: '3000',type: 'error'});
     isvalidpaytmindustrytypeid = 0;
   }

  }
  //Payu Validation
  if(activeplan==3){
      if(payumerchantid == ""){
        $("#payumerchantid_div").addClass("has-error is-focused");
        new PNotify({title: "Please enter Payu Merchant ID",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidpayumerchantid = 0;
      }
      
      if(payumerchantkey == ""){
        $("#payumerchantkey_div").addClass("has-error is-focused");
        new PNotify({title: "Please enter Payu merchant key",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidpayumerchantkey = 0;
      }
      
      if(payumerchantsalt == ""){
        $("#payumerchantsalt_div").addClass("has-error is-focused");
        new PNotify({title: "Please enter Payu merchant salt",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidpayumerchantsalt = 0;
    
      }
          
      if(payuauthheader == "" ){
        $("#payuauthheader_div").addClass("has-error is-focused");
        new PNotify({title: "Please enter Payu auth header",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidpayuauthheader = 0;
      }
  }

  if(isvalidmerchantkey ==1 && isvalidmerchantid == 1 && isvalidauthheader ==1 && isvalidmerchantsalt == 1 && isvalidpaytmmerchantkey ==1 && isvalidpaytmmerchantid == 1 && isvalidpaytmmerchantwebsiteforweb ==1 && isvalidpaytmmerchantwebsiteforapp == 1 && isvalidpaytmchannelidforweb ==1 && isvalidpaytmchannelidforapp == 1 && isvalidpaytmindustrytypeid ==1 && isvalidpayumerchantkey ==1 && isvalidpayumerchantid == 1 && isvalidpayuauthheader ==1 && isvalidpayumerchantsalt == 1 && isvalidactiveplan == 1){
    
    var formData = new FormData($('#paymentgatewayform')[0]);
    var uurl = SITE_URL+"payment-gateway/update-payment-gateway";
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
        var a = $.parseJSON(response);
          if(response==1){
            new PNotify({title: 'Payement gateway successfully updated.',styling: 'fontawesome',delay: '3000',type: 'success'});
            setTimeout(function() { window.location.href = SITE_URL+"payment-gateway"; }, 1500);
        }
        else{
          new PNotify({title: 'Payement gateway not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
