$(document).ready(function() {
    resetdata();
});

function resetdata() {
    $("#narration_div").removeClass("has-error is-focused");

    if (ACTION == 0) {
        $('#narration').val("");
        $("#narration_div").addClass("is-focused");

        $('#yes').prop("checked", true);
        $('#narration').focus();
    }
    $('html, body').animate({ scrollTop: 0 }, 'slow');
}

function checkvalidation(addtype = 0) {

    var narration = $('#narration').val().trim();
    var isvalidnarration = 0;

    PNotify.removeAll();
    if (narration == "") {
        $("#narration_div").addClass("has-error is-focused");
        new PNotify({ title: 'Please enter narration !', styling: 'fontawesome', delay: '3000', type: 'error' });
    } else if (narration.length < 2) {
        $("#narration_div").addClass("has-error is-focused");
        new PNotify({ title: 'Narration require minimum 2 characters !', styling: 'fontawesome', delay: '3000', type: 'error' });
    } else {
        $("#narration_div").removeClass("has-error is-focused");
        isvalidnarration = 1;
    }

    if (isvalidnarration == 1) {
        var formData = new FormData($('#narration-form')[0]);
        if (ACTION == 0) { // INSERT
            var baseurl = SITE_URL + 'narration/narration-add';
            $.ajax({

                url: baseurl,
                type: 'POST',
                data: formData,
                //async: false,
                beforeSend: function() {
                    $('.mask').show();
                    $('#loader').show();
                },
                success: function(response) {
                    $("#narration_div").removeClass("has-error is-focused");
                    if (response == 1) {
                        new PNotify({ title: 'Narration successfully added.', styling: 'fontawesome', delay: '3000', type: 'success' });
                        if (addtype == 1) {
                            resetdata();
                        } else {
                            setTimeout(function() { window.location = SITE_URL + "narration"; }, 500);
                        }
                    } else if (response == 2) {
                        new PNotify({ title: 'Narration already exist !', styling: 'fontawesome', delay: '3000', type: 'error' });
                        $("#narration_div").addClass("has-error is-focused");
                    } else {
                        new PNotify({ title: 'Narration not updated !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    }
                },
                error: function(xhr) {},
                complete: function() {
                    $('.mask').hide();
                    $('#loader').hide();
                },
                cache: false,
                contentType: false,
                processData: false
            });
        } else {
            // MODIFY
            var baseurl = SITE_URL + 'narration/update-narration';
            $.ajax({
                url: baseurl,
                type: 'POST',
                data: formData,
                beforeSend: function() {
                    $('.mask').show();
                    $('#loader').show();
                },
                success: function(response) {
                    $("#narration_div").removeClass("has-error is-focused");
                    if (response == 1) {
                        new PNotify({ title: 'Narration successfully updated.', styling: 'fontawesome', delay: '3000', type: 'success' });
                        setTimeout(function() { window.location = SITE_URL + "narration"; }, 500);
                    } else if (response == 2) {
                        new PNotify({ title: 'Narration already exist !', styling: 'fontawesome', delay: '3000', type: 'error' });
                        $("#narration_div").addClass("has-error is-focused");
                    } else {
                        new PNotify({ title: 'Narration not updated !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    }
                },
                error: function(xhr) {},
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