const options = { 
    day: '2-digit',
    month: '2-digit', 
    year: 'numeric', 
  };

function resetdata() {
    $("#name_div").removeClass("has-error is-focused");
    $("#slug_div").removeClass("has-error is-focused");
    if (ACTION == 1) {
        $('html, body').animate({ scrollTop: 0 }, 'slow');
    } else {
        $('#commission').val('');
      
        $('html, body').animate({ scrollTop: 0 }, 'slow');
    }
}

$(document).ready(function() {
    $('#date').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        orientation: 'top',
        autoclose: true,
        todayBtn: "linked"
    });
});

$("#old_receipt_div").hide();
$('#remove').click(function() {
    $('#removeoldreceipt').val('1');
});

function checkvalidation(addtype = 0) {

    var commission = $('#commission').val().trim();
    var date = $('#date').val().trim();

    var isvalidcommission = isvaliddate = 0;

    PNotify.removeAll();

    if (commission == '') {
        $("#commission_div").addClass("has-error is-focused");
        new PNotify({ title: 'Please enter commission !', styling: 'fontawesome', delay: '3000', type: 'error' });
        isvalidcommission = 0;
    } else {
        $("#commission_div").removeClass("has-error is-focused");
        isvalidcommission = 1;
    }


    if (isvalidcommission == 1) {

        var formData = new FormData($('#addcommissionform')[0]);
        if (ACTION == 0) {
            var uurl = SITE_URL + "Commission/currencyrate-add";

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
                        new PNotify({ title: "Commission successfully added.", styling: 'fontawesome', delay: '3000', type: 'success' });
                        if (addtype == 1) {
                            resetdata();
                        } else {
                            setTimeout(function() { window.location = SITE_URL + "Commission"; }, 500);
                        }

                    } else if (data['error'] == 2) {
                        new PNotify({ title: 'Commission already exists !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else if (data['error'] == 3) {
                        new PNotify({ title: data['message'], styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else if (data['error'] == 4) {
                        new PNotify({ title: 'This Commission not available in portal !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else {
                        new PNotify({ title: 'Commission not added !', styling: 'fontawesome', delay: '3000', type: 'error' });
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
            var uurl = SITE_URL + "Commission/update-commission";

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
                        new PNotify({ title: "Successfully updated.", styling: 'fontawesome', delay: '1500', type: 'success' });
                        setTimeout(function() { window.location = SITE_URL + "Commission"; }, 1500);
                    } else if (data['error'] == 3) {
                        new PNotify({ title: data['message'], styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else if (data['error'] == 4) {
                        new PNotify({ title: 'This Commission not available in portal !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else {
                        new PNotify({ title: 'Commission not updated !', styling: 'fontawesome', delay: '3000', type: 'error' });
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

$("#commission").on('keyup', function(e) {
    var val = $(this).val();
    if (val > 100) {
        $('#commission').val("100.00");
    }
});