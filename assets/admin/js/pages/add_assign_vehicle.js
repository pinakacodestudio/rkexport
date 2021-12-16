$(document).ready(function () {
    $('#assignvehicledate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayBtn: "linked",
        startDate: new Date(),
    });
});

function resetdata() {

    $("#vehicleid_div").removeClass("has-error is-focused");
    $("#site_div").removeClass("has-error is-focused");
    $("#date_div").removeClass("has-error is-focused");

    if (ACTION == 1) {

    } else {
        $("#vehicleid").val(0);
        $("#siteid").val(0);
        $("#assignvehicledate").val('');
        $('.selectpicker').selectpicker('refresh');
    }
    $('html, body').animate({scrollTop: 0}, 'slow');
}
function checkvalidation(addtype = 0) {

    var vehicleid = $("#vehicleid").val();
    var siteid = $("#siteid").val();
    var date = $("#assignvehicledate").val();

    var isvalidvehicleid = isvalidsiteid = isvaliddate = 0;
    PNotify.removeAll();

    if (vehicleid == null || vehicleid == 0) {
        $("#vehicleid_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select vehicle !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
        $("#vehicleid_div").removeClass("has-error is-focused");
        isvalidvehicleid = 1;
    }

    if (siteid == 0) {
        $("#site_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select site !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
        $("#site_div").removeClass("has-error is-focused");
        isvalidsiteid = 1;
    }

    if (date == '') {
        $("#date_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select date !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
        $("#date_div").removeClass("has-error is-focused");
        isvaliddate = 1;
    }

    if (isvalidvehicleid == 1 && isvalidsiteid == 1 && isvaliddate == 1) {

        var formData = new FormData($('#form-assignvehicle')[0]);
        if (ACTION == 0) {
            var uurl = SITE_URL + "assign-vehicle/assign-vehicle-add";

            $.ajax({
                url: uurl,
                type: 'POST',
                data: formData,
                //async: false,
                beforeSend: function () {
                    $('.mask').show();
                    $('#loader').show();
                },
                success: function (response) {
                    if (response == 1) {
                        new PNotify({title: "Assign vehicle successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
                        if (addtype == 1) {
                            resetdata();
                        } else {
                            setTimeout(function () {window.location = SITE_URL + "assign-vehicle";}, 1500);
                        }
                    } else {
                        new PNotify({title: 'Assign vehicle not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }
                },
                error: function (xhr) {
                    //alert(xhr.responseText);
                },
                complete: function () {
                    $('.mask').hide();
                    $('#loader').hide();
                },
                cache: false,
                contentType: false,
                processData: false
            });
        } else {

            var uurl = SITE_URL + "assign-vehicle/update-assign-vehicle";

            $.ajax({
                url: uurl,
                type: 'POST',
                data: formData,
                //async: false,
                beforeSend: function () {
                    $('.mask').show();
                    $('#loader').show();
                },
                success: function (response) {
                    if (response == 1) {
                        new PNotify({title: "Assign vehicle successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
                        if (addtype == 1) {
                            setTimeout(function () {window.location = SITE_URL + "assign-vehicle/add-assign-vehicle";}, 1500);
                        } else {
                            setTimeout(function () {window.location = SITE_URL + "assign-vehicle";}, 1500);
                        }
                    } else if (response == 2) {
                        new PNotify({title: "Assign vehicle already exists.",styling: 'fontawesome',delay: '3000',type: 'error'});
                    } else {
                        new PNotify({title: 'Assign vehicle not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }
                },
                error: function (xhr) {
                    //alert(xhr.responseText);
                },
                complete: function () {
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