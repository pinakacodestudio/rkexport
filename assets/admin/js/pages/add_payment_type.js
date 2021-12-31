function resetdata() {

    $("#name_div").removeClass("has-error is-focused");

    if (ACTION == 1) {

        $('html, body').animate({ scrollTop: 0 }, 'slow');
    } else {
        $('#paymenttype').val('');

        $('html, body').animate({ scrollTop: 0 }, 'slow');
    }
}

function checkvalidation(addtype = 0) {

    var name = $('#paymenttype').val().trim();
    var isvalidname = 0;

    PNotify.removeAll();

    if (name == '') {
        $("#paymenttype_div").addClass("has-error is-focused");
        new PNotify({ title: 'Please enter payment type !', styling: 'fontawesome', delay: '3000', type: 'error' });
        isvalidname = 0;
    } else {
        $("#paymenttype_div").removeClass("has-error is-focused");
        isvalidname = 1;
    }

    if (isvalidname == 1) {

        var formData = new FormData($('#paymenttypeform')[0]);
        if (ACTION == 0) {
            var uurl = SITE_URL + "payment-type/payment-type-add";

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
                    var data = JSON.parse(response);
                    if (data['error'] == 1) {
                        new PNotify({ title: "Payment Type successfully added.", styling: 'fontawesome', delay: '3000', type: 'success' });
                        if (addtype == 1) {
                            resetdata();
                        } else {
                            setTimeout(function() { window.location = SITE_URL + "payment-type"; }, 500);
                        }
                    } else if (data['error'] == 2) {
                        new PNotify({ title: 'Payment Type already exists !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else if (data['error'] == 3) {
                        new PNotify({ title: data['message'], styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else if (data['error'] == 4) {
                        new PNotify({ title: 'This Payment Type not available in portal !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else {
                        new PNotify({ title: 'Payment Type not added !', styling: 'fontawesome', delay: '3000', type: 'error' });
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
            var uurl = SITE_URL + "payment-type/update_payment_type";

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
                    var data = JSON.parse(response);
                    if (data['error'] == 1) {
                        new PNotify({ title: "Payment Type successfully updated.", styling: 'fontawesome', delay: '1500', type: 'success' });
                        setTimeout(function() { window.location = SITE_URL + "payment-type"; }, 1500);
                    } else if (data['error'] == 2) {
                        new PNotify({ title: 'Payment Type already exists !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else if (data['error'] == 3) {
                        new PNotify({ title: data['message'], styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else if (data['error'] == 4) {
                        new PNotify({ title: 'This Payment Type not available in portal !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else {
                        new PNotify({ title: 'Payment Type not updated !', styling: 'fontawesome', delay: '3000', type: 'error' });
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