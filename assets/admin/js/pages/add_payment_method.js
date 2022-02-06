if (ACTION == 1 && $('#oldlogo').val() != '') {
    var $imageupload = $('.imageupload');
    $imageupload.imageupload({
        url: SITE_URL,
        type: '1',
        maxFileSizeKb: UPLOAD_MAX_FILE_SIZE,
        allowedFormats: ['jpg', 'jpeg', 'png', 'ico']
    });
} else {
    var $imageupload = $('.imageupload');
    $imageupload.imageupload({
        url: SITE_URL,
        type: '0',
        maxFileSizeKb: UPLOAD_MAX_FILE_SIZE,
        allowedFormats: ['jpg', 'jpeg', 'png', 'ico']
    });


    $('#remove').click(function() {
        $('#removeoldlogo').val('1');
    });

}
function resetdata() {

    $("#paymentmethod_div").removeClass("has-error is-focused");
  
    if (ACTION == 1) {
  
    } else {
        $('#paymentmethod').val('');
    }
    $('#paymentmethod').focus();
    $('html, body').animate({scrollTop: 0}, 'slow');
  }

function checkvalidation(addtype=0) {

    var paymentmethod = $("#paymentmethod").val().trim();
    var paymentgatewaytype = $("#paymentgatewaytype").val();

    var isvalidpaymentmethod = 0;
    var isvalidmerchantkey = isvalidmerchantid = isvalidauthheader = isvalidmerchantsalt = isvalidmerchantwebsiteforweb = isvalidmerchantwebsiteforapp = isvalidchannelidforweb = isvalidchannelidforapp = isvalidindustrytypeid = isvalidkeyid = isvalidkeysecret = isvalidorderurl = isvalidcheckouturl = 1;

    PNotify.removeAll();
    if (paymentmethod == '') {
        $("#paymentmethod_div").addClass("has-error is-focused");
        new PNotify({ title: 'Please enter payment method !', styling: 'fontawesome', delay: '3000', type: 'error' });
        $("#paymentmethod").focus();
        isvalidpaymentmethod = 0;
    } else {
        if (paymentmethod.length < 3) {
            $("#paymentmethod_div").addClass("has-error is-focused");
            new PNotify({ title: 'Minmum 2 characters require for payment method !', styling: 'fontawesome', delay: '3000', type: 'error' });
            $("#paymentmethod").focus();
            isvalidpaymentmethod = 0;
        } else {
            isvalidpaymentmethod = 1;
        }
    }
    if (ACTION == 1) {
        if (paymentgatewaytype == 1 || paymentgatewaytype == 3) {
            //payumoney or payu 
            var merchantid = $("#merchantid").val().trim();
            var merchantkey = $("#merchantkey").val().trim();
            var merchantsalt = $("#merchantsalt").val().trim();
            var authheader = $("#authheader").val().trim();

            if (merchantid == "") {
                $("#merchantid_div").addClass("has-error is-focused");
                new PNotify({ title: "Please enter Merchant ID", styling: 'fontawesome', delay: '3000', type: 'error' });
                isvalidmerchantid = 0;
            }

            if (merchantkey == "") {
                $("#merchantkey_div").addClass("has-error is-focused");
                new PNotify({ title: "Please enter merchant key", styling: 'fontawesome', delay: '3000', type: 'error' });
                isvalidmerchantkey = 0;
            }

            if (merchantsalt == "") {
                $("#merchantsalt_div").addClass("has-error is-focused");
                new PNotify({ title: "Please enter merchant salt", styling: 'fontawesome', delay: '3000', type: 'error' });
                isvalidmerchantsalt = 0;

            }

            if (authheader == "") {
                $("#authheader_div").addClass("has-error is-focused");
                new PNotify({ title: "Please enter auth header", styling: 'fontawesome', delay: '3000', type: 'error' });
                isvalidauthheader = 0;
            }
        } else if (paymentgatewaytype == 2) {
            //paytm 
            var merchantid = $("#merchantid").val().trim();
            var merchantkey = $("#merchantkey").val().trim();
            var merchantwebsiteforweb = $("#merchantwebsiteforweb").val().trim();
            var merchantwebsiteforapp = $("#merchantwebsiteforapp").val().trim();
            var channelidforweb = $("#channelidforweb").val().trim();
            var channelidforapp = $("#channelidforapp").val().trim();
            var industrytypeid = $("#industrytypeid").val().trim();

            if (merchantid == "") {
                $("#merchantid_div").addClass("has-error is-focused");
                new PNotify({ title: "Please enter Merchant ID", styling: 'fontawesome', delay: '3000', type: 'error' });
                isvalidmerchantid = 0;
            }

            if (merchantkey == "") {
                $("#merchantkey_div").addClass("has-error is-focused");
                new PNotify({ title: "Please enter merchant key", styling: 'fontawesome', delay: '3000', type: 'error' });
                isvalidmerchantkey = 0;
            }

            if (merchantwebsiteforweb == "") {
                $("#merchantwebsiteforweb_div").addClass("has-error is-focused");
                new PNotify({ title: "Please enter Merchant website for web", styling: 'fontawesome', delay: '3000', type: 'error' });
                isvalidmerchantwebsiteforweb = 0;
            }

            if (merchantwebsiteforapp == "") {
                $("#merchantwebsiteforapp_div").addClass("has-error is-focused");
                new PNotify({ title: "Please enter Merchant website for app", styling: 'fontawesome', delay: '3000', type: 'error' });
                isvalidmerchantwebsiteforapp = 0;
            }

            if (channelidforweb == "") {
                $("#channelidforweb_div").addClass("has-error is-focused");
                new PNotify({ title: "Please enter Channel id for web", styling: 'fontawesome', delay: '3000', type: 'error' });
                isvalidchannelidforweb = 0;
            }

            if (channelidforapp == "") {
                $("#channelidforapp_div").addClass("has-error is-focused");
                new PNotify({ title: "Please enter Channel id for app", styling: 'fontawesome', delay: '3000', type: 'error' });
                isvalidchannelidforapp = 0;
            }

            if (industrytypeid == "") {
                $("#industrytypeid_div").addClass("has-error is-focused");
                new PNotify({ title: "Please enter Industry type id", styling: 'fontawesome', delay: '3000', type: 'error' });
                isvalidindustrytypeid = 0;
            }
        } else if (paymentgatewaytype == 4) {
            //razorpay
            var keyid = $("#keyid").val().trim();
            var keysecret = $("#keysecret").val().trim();
            var orderurl = $("#orderurl").val().trim();
            var checkouturl = $("#checkouturl").val().trim();

            if (keyid == "") {
                $("#keyid_div").addClass("has-error is-focused");
                new PNotify({ title: "Please enter Key ID !", styling: 'fontawesome', delay: '3000', type: 'error' });
                isvalidkeyid = 0;
            }

            if (keysecret == "") {
                $("#keysecret_div").addClass("has-error is-focused");
                new PNotify({ title: "Please enter key secret !", styling: 'fontawesome', delay: '3000', type: 'error' });
                isvalidkeysecret = 0;
            }

            if (orderurl == "") {
                $("#orderurl_div").addClass("has-error is-focused");
                new PNotify({ title: "Please enter order url !", styling: 'fontawesome', delay: '3000', type: 'error' });
                isvalidorderurl = 0;

            }

            if (checkouturl == "") {
                $("#checkouturl_div").addClass("has-error is-focused");
                new PNotify({ title: "Please enter checkout url !", styling: 'fontawesome', delay: '3000', type: 'error' });
                isvalidcheckouturl = 0;
            }
        }
    }
    if (isvalidpaymentmethod == 1 && isvalidmerchantkey == 1 && isvalidmerchantid == 1 && isvalidauthheader == 1 && isvalidmerchantsalt == 1 && isvalidmerchantwebsiteforweb == 1 && isvalidmerchantwebsiteforapp == 1 && isvalidchannelidforweb == 1 && isvalidchannelidforapp == 1 && isvalidindustrytypeid == 1 && isvalidkeyid == 1 && isvalidkeysecret == 1 && isvalidorderurl == 1 && isvalidcheckouturl == 1) {

        var formData = new FormData($('#paymentmethodform')[0]);
        if (ACTION == 0) {
            var uurl = SITE_URL + "payment-method/payment-method-add";

            $.ajax({
                url: uurl,
                type: 'POST',
                data: formData,
                //async: false,
                beforeSend: function() {
                    $('.mask').show();
                    $('#loader').show();
                },
                success: function(response) {
                    if (response == 1) {
                        new PNotify({ title: "Payment method successfully added.", styling: 'fontawesome', delay: '3000', type: 'success' });
                        if(addtype==1){
                            resetdata();
                            $('#paymentmethod').val('');
                        }else{
                            resetdata();
                            setTimeout(function() { window.location = SITE_URL + "payment-method"; }, 500);
                        }
                        
                    } else if (response == 2) {
                        new PNotify({ title: 'Payment method already exists !', styling: 'fontawesome', delay: '3000', type: 'error' });
                        $("#paymentmethod_div").addClass("has-error is-focused");
                    } else if (response == 3) {
                        new PNotify({ title: 'Logo not uploaded !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else if (response == 4) {
                        new PNotify({ title: 'Invalid type of logo image !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else if (response == 5) {
                        new PNotify({ title: 'File is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE) + ') !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else {
                        new PNotify({ title: 'Payment method not added !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    }
                },
                error: function(xhr) {
                    //alert(xhr.responseText);
                },
                complete: function() {
                    $('.mask').hide();
                    $('#loader').hide();
                },
                cache: false,
                contentType: false,
                processData: false
            });
        } else {
            var uurl = SITE_URL + "payment-method/update-payment-method";

            $.ajax({
                url: uurl,
                type: 'POST',
                data: formData,
                //async: false,
                beforeSend: function() {
                    $('.mask').show();
                    $('#loader').show();
                },
                success: function(response) {
                   
                    if (response == 1) {
                        new PNotify({ title: "Payment method successfully updated.", styling: 'fontawesome', delay: '1500', type: 'success' });
                        if(addtype==1){
                            setTimeout(function() {window.location=SITE_URL+"payment-method/add-payment-method"; }, 1500);
                          }else{
                            setTimeout(function() { window.location=SITE_URL+"payment-method"; }, 1500);
                          }
                    } else if (response == 2) {
                        new PNotify({ title: 'Payment method already exists !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else if (response == 3) {
                        new PNotify({ title: 'Logo not uploaded !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else if (response == 4) {
                        new PNotify({ title: 'Invalid type of logo image !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else if (response == 5) {
                        new PNotify({ title: 'File is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE) + ') !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else {
                        new PNotify({ title: 'Payment method not updated !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    }
                },
                error: function(xhr) {
                    //alert(xhr.responseText);
                },
                complete: function() {
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