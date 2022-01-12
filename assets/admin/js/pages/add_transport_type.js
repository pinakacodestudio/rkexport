function resetdata() {

    $("#type_div").removeClass("has-error is-focused");

    if (ACTION == 1) {

        $('html, body').animate({ scrollTop: 0 }, 'slow');
    } else {
        $('#type').val('');

        $('html, body').animate({ scrollTop: 0 }, 'slow');
    }
}

function checkvalidation(addtype = 0) {
    var name = $('#type').val().trim();
    var isvalidname = 0;
    PNotify.removeAll();
    if (name == '') {
        $("#paymenttype_div").addClass("has-error is-focused");
        new PNotify({ title: 'Please enter transport type !', styling: 'fontawesome', delay: '3000', type: 'error' });
        isvalidname = 0;
    } else {
        $("#paymenttype_div").removeClass("has-error is-focused");
        isvalidname = 1;
    }

    if (isvalidname == 1) {
        var formData = new FormData($('#transporttypeform')[0]);
        if (ACTION == 0) {
            var uurl = SITE_URL + "Transport_type/transport_type_add";
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
                        new PNotify({ title: "transport type successfully added.", styling: 'fontawesome', delay: '3000', type: 'success' });
                        if (addtype == 1) {
                            resetdata();
                        } else {
                            setTimeout(function() { window.location = SITE_URL + "Transport_type"; }, 500);
                        }
                    } else if (data['error'] == 2) {
                        new PNotify({ title: 'transport type already exists !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else if (data['error'] == 3) {
                        new PNotify({ title: data['message'], styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else if (data['error'] == 4) {
                        new PNotify({ title: 'This transport type not available in portal !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else {
                        new PNotify({ title: 'transport type not added !', styling: 'fontawesome', delay: '3000', type: 'error' });
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
            var uurl = SITE_URL + "Transport_type/update_transport_type";

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
                        new PNotify({ title: "transport type successfully updated.", styling: 'fontawesome', delay: '1500', type: 'success' });
                        setTimeout(function() { window.location = SITE_URL + "transport-type"; }, 1500);
                    } else if (data['error'] == 2) {
                        new PNotify({ title: 'transport type already exists !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else if (data['error'] == 3) {
                        new PNotify({ title: data['message'], styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else if (data['error'] == 4) {
                        new PNotify({ title: 'This transport type not available in portal !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else {
                        new PNotify({ title: 'transport type not updated !', styling: 'fontawesome', delay: '3000', type: 'error' });
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