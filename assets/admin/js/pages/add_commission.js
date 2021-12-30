function resetdata() {
    $("#name_div").removeClass("has-error is-focused");
    $("#slug_div").removeClass("has-error is-focused");
    if (ACTION == 1) {
        $('html, body').animate({ scrollTop: 0 }, 'slow');
    } else {
        $('#commission_type').val('');
        $('#date').val('');
        $('html, body').animate({ scrollTop: 0 }, 'slow');
    }
}


$('#date').datepicker({
    todayHighlight: true,
    format: 'dd/mm/yyyy',
    orientation: 'top',
    autoclose: true,
    todayBtn: "linked"
});
$("#old_receipt_div").hide();
$('#remove').click(function() {
    $('#removeoldreceipt').val('1');
});

function checkvalidation(addtype = 0) {

    var commission_type = $('#commission_type').val().trim();
    var date = $('#date').val().trim();

    var isvalidcommission_type = isvaliddate = 0;

    PNotify.removeAll();

    if (commission_type == '') {
        $("#currency_div").addClass("has-error is-focused");
        new PNotify({ title: 'Please enter Currency !', styling: 'fontawesome', delay: '3000', type: 'error' });
        isvalidcommission_type = 0;
    } else {
        $("#currency_div").removeClass("has-error is-focused");
        isvalidcommission_type = 1;
    }


    if (isvalidcommission_type == 1) {

        var formData = new FormData($('#addcommissionform')[0]);
        if (ACTION == 0) {
            var uurl = SITE_URL + "Commission/currencyrate_add";

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
                        new PNotify({ title: "Rights successfully added.", styling: 'fontawesome', delay: '3000', type: 'success' });
                        if (addtype == 1) {
                            resetdata();
                        } else {
                            setTimeout(function() { window.location = SITE_URL + "Commission"; }, 500);
                        }

                    } else if (data['error'] == 2) {
                        new PNotify({ title: 'Rights already exists !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else if (data['error'] == 3) {
                        new PNotify({ title: data['message'], styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else if (data['error'] == 4) {
                        new PNotify({ title: 'This rights not available in portal !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else {
                        new PNotify({ title: 'Rights not added !', styling: 'fontawesome', delay: '3000', type: 'error' });
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
            var uurl = SITE_URL + "Commission/update_commission";

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
                    console.log(response);
                    var data = JSON.parse(response);
                    if (data['error'] == 1) {
                        new PNotify({ title: "successfully updated.", styling: 'fontawesome', delay: '1500', type: 'success' });
                        setTimeout(function() { window.location = SITE_URL + "Commission"; }, 1500);
                    } else if (data['error'] == 3) {
                        new PNotify({ title: data['message'], styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else if (data['error'] == 4) {
                        new PNotify({ title: 'This rights not available in portal !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else {
                        new PNotify({ title: 'Rights not updated !', styling: 'fontawesome', delay: '3000', type: 'error' });
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