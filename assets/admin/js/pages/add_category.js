$(document).ready(function() {

    if ($('#oldfileimage').val() != '') {
        var $imageupload = $('#fileImg');
        $imageupload.imageupload({
            url: SITE_URL,
            type: '1',
            maxFileSizeKb: UPLOAD_MAX_FILE_SIZE,
            allowedFormats: ['jpg', 'jpeg', 'png', 'ico']
        });
    } else {
        var $imageupload = $('#fileImg');
        $imageupload.imageupload({
            url: SITE_URL,
            type: '0',
            maxFileSizeKb: UPLOAD_MAX_FILE_SIZE,
            allowedFormats: ['jpg', 'jpeg', 'png', 'ico']
        });
    }

    $('#remove').click(function() {
        $('#removeimg').val('1');
    });
    $("#categoryslug").keyup(function(e) {
        $("#categoryslug").val(($("#categoryslug").val()).toLowerCase());
    });

});

function setslug(name) {
    $('#categoryslug').val(name.toLowerCase().replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-'));
}

function validation() {

    $('input[type="text"]').removeClass("has-not-error");
    $('input[type="text"]').removeClass("has-error");

    $('#categoryid').removeClass("has-not-error");
    $('#categoryid').removeClass("has-error");

    var name = $('#name').val().trim();
    var categoryslug = $("#categoryslug").val().trim();
    // var imagename = $('#imagename').val();
    // var fileimage = $('#fileimage').val();
    // var oldfileimage = $('#oldfileimage').val();
    //var maincategoryid = $('#maincategoryid').val();

    var isvalidname = isvalidcategoryslug = 0;
    //var isvalimaincatid = 0;
    //var isvalidimage = 0;

    PNotify.removeAll();
    if (name == "") {
        $("#name_div").addClass("has-error is-focused");
        new PNotify({ title: 'Please enter category name !', styling: 'fontawesome', delay: '3000', type: 'error' });
    } else {
        if (name.length < 2) {
            $("#name_div").addClass("has-error is-focused");
            new PNotify({ title: 'Category name required minimum 2 characters !', styling: 'fontawesome', delay: '3000', type: 'error' });
        } else {
            $("#name").addClass("has-not-error");
            isvalidname = 1;
        }
    }
    if (categoryslug == '') {
        $("#categoryslug_div").addClass("has-error is-focused");
        new PNotify({ title: 'Please enter category link !', styling: 'fontawesome', delay: '3000', type: 'error' });
        isvalidcategoryslug = 0;
    } else {
        if (categoryslug.length < 2) {
            $("#categoryslug_div").addClass("has-error is-focused");
            new PNotify({ title: "Category link require minimum 2 characters !", styling: 'fontawesome', delay: '3000', type: 'error' });
            isvalidcategoryslug = 0;
        } else {
            isvalidcategoryslug = 1;
        }
    }

    if (isvalidname == 1 && isvalidcategoryslug == 1) {
        var formData = new FormData($('#form-category')[0]);

        if (ACTION == 0) { // INSERT
            var baseurl = SITE_URL + 'category/category-add';
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
                    var data = JSON.parse(response);
                    if (data['error'] == 1) {
                        new PNotify({ title: 'Category successfully added.', styling: 'fontawesome', delay: '3000', type: 'success' });
                        setTimeout(function() { window.location = SITE_URL + "category"; }, 500);
                    } else if (data['error'] == 2) {
                        new PNotify({ title: 'Category already exist !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else if (data['error'] == 3) {
                        new PNotify({ title: 'Image does not Uploaded.', styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else if (data['error'] == 4) {
                        new PNotify({ title: 'Image type does not valid !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else if (data['error'] == 5) {
                        new PNotify({ title: data['message'], styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else if (data['error'] == 6) {
                        new PNotify({ title: 'File is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE) + ')!', styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else if (data['error'] == 0) {
                        new PNotify({ title: 'Category not added !', styling: 'fontawesome', delay: '3000', type: 'error' });
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
            var baseurl = SITE_URL + 'category/update-category';
            $.ajax({
                url: baseurl,
                type: 'POST',
                data: formData,
                beforeSend: function() {
                    $('.mask').show();
                    $('#loader').show();
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data['error'] == 1) {
                        new PNotify({ title: 'Category successfully updated.', styling: 'fontawesome', delay: '3000', type: 'success' });

                        setTimeout(function() { window.location = SITE_URL + "category"; }, 500);
                    } else if (data['error'] == 2) {
                        new PNotify({ title: 'Category already exist !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else if (data['error'] == 3) {
                        new PNotify({ title: 'Image does not Uploaded.', styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else if (data['error'] == 4) {
                        new PNotify({ title: 'Image type does not valid !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else if (data['error'] == 5) {
                        new PNotify({ title: data['message'], styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else if (data['error'] == 6) {
                        new PNotify({ title: 'Sorry, Your file is too large. Only ' + size + ' is allowed !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else if (data['error'] == 0) {
                        new PNotify({ title: 'Category not updated !', styling: 'fontawesome', delay: '3000', type: 'error' });
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

function resetdata() {
    $("#name_div").removeClass("has-error is-focused");
    $("#categoryslug_div").removeClass("has-error is-focused");
    if (ACTION == 0) {
        $('.selectpicker').selectpicker('refresh');
        $('#name').val("");
        $('#categoryslug').val('');
        $('#fileImg').imageupload('reset');
    }
}