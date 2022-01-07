$(document).ready(function() {
    $("#integratedtax").on('keyup', function(e) {
        var val = $(this).val();
        if (val > 100) {
            $('#integratedtax').val("100.00");
        }
    });

});

function resetdata() {

    $("#description_div").removeClass("has-error is-focused");
    $("#hsncode_div").removeClass("has-error is-focused");
    $("#integratedtax_div").removeClass("has-error is-focused");
    $("#member_div").removeClass("has-error is-focused");

    if (ACTION == 0) {

        $('#description').val('');
        $('#hsncode').val('');
        $('#integratedtax').val('');
        $('#yes').prop("checked", true);
        $('#hsncode').focus();
        $('.selectpicker').selectpicker('refresh');
    }
    $('html, body').animate({ scrollTop: 0 }, 'slow');
}

function checkvalidation() {

    var description = $("#description").val().trim();
    var hsncode = $("#hsncode").val().trim();
    var integratedtax = $("#integratedtax").val().trim();

    var isvaliddescription = isvalidhsncode = isvalidintegratedtax = 1;

    PNotify.removeAll();
    if (hsncode == '') {
        $("#hsncode_div").addClass("has-error is-focused");
        new PNotify({ title: 'Please enter HSN code !', styling: 'fontawesome', delay: '3000', type: 'error' });
        isvalidhsncode = 0;
    } else {
        if (hsncode.length < 2) {
            $("#hsncode_div").addClass("has-error is-focused");
            new PNotify({ title: 'HSN Code name require minimum 2 characters !', styling: 'fontawesome', delay: '3000', type: 'error' });
            isvalidhsncode = 0;
        } else {
            $("#hsncode_div").removeClass("has-error is-focused");
        }
    }
    if (integratedtax == '') {
        $("#integratedtax_div").addClass("has-error is-focused");
        new PNotify({ title: 'Please enter integrated tax !', styling: 'fontawesome', delay: '3000', type: 'error' });
        isvalidintegratedtax = 0;
    } else {
        $("#integratedtax_div").removeClass("has-error is-focused");
    }
    if (description != '' && description.length < 3) {
        $("#description_div").addClass("has-error is-focused");
        new PNotify({ title: 'Description require minimum 3 characters !', styling: 'fontawesome', delay: '3000', type: 'error' });
        isvaliddescription = 0;
    } else {
        $("#description_div").removeClass("has-error is-focused");
    }

    if (isvaliddescription == 1 && isvalidhsncode == 1 && isvalidintegratedtax == 1) {

        var formData = new FormData($('#hsncodeform')[0]);
        if (ACTION == 0) {
            var uurl = SITE_URL + "hsn-code/add-hsn-code";

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
                        new PNotify({ title: "HSN code successfully added.", styling: 'fontawesome', delay: '3000', type: 'success' });

                        setTimeout(function() { window.location = SITE_URL + "hsn-code"; }, 1500);

                    } else if (data['error'] == 2) {
                        new PNotify({ title: 'HSN code already exists !', styling: 'fontawesome', delay: '3000', type: 'error' });
                        $("#hsncode_div").addClass("has-error is-focused");
                    } else if (data['error'] == 3) {
                        new PNotify({ title: data['message'], styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else {
                        new PNotify({ title: 'HSN code not added !', styling: 'fontawesome', delay: '3000', type: 'error' });
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
            var uurl = SITE_URL + "hsn-code/update-hsn-code";

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
                        new PNotify({ title: "HSN code successfully updated.", styling: 'fontawesome', delay: '1500', type: 'success' });
                        setTimeout(function() { window.location = SITE_URL + "hsn-code"; }, 1500);
                    } else if (data['error'] == 2) {
                        new PNotify({ title: 'HSN code already exists !', styling: 'fontawesome', delay: '3000', type: 'error' });
                        $("#hsncode_div").addClass("has-error is-focused");
                    } else if (data['error'] == 3) {
                        new PNotify({ title: data['message'], styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else {
                        new PNotify({ title: 'HSN code not updated !', styling: 'fontawesome', delay: '3000', type: 'error' });
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