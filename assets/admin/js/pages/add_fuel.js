$(document).ready(function () {

    $('#vehiclefueldate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        autoclose: true,
        endDate: new Date(),
        todayBtn: "linked",
    });

    $(".add_btn").hide();
    $(".add_btn:last").show();
});

function addnewfuelfile() {
    fuelfilecount = ++fuelfilecount;
    var element = 'fuelfile' + fuelfilecount;
    $.html = '<div class="col-md-4 col-sm-6 col-xs-12 fuelfile" id="fuelfilecount' + fuelfilecount + '">\
                <div class="form-group">\
                    <div class="col-md-9 col-xs-9">\
                        <div class="input-group">\
                            <span class="input-group-btn" style="padding: 0 0px 0px 0px; ">\
                                <span class="btn btn-primary btn-raised btn-file">Browse...\
                                    <input type="file" class="fuelfile" name="fuelfile' + fuelfilecount + '" id="fuelfile' + fuelfilecount + '" accept=".jpe,.jpeg,.jpg,.pbm,.png,.pdf" onchange="validfuelfile($(this),\'fuelfile' + fuelfilecount + '\',this)"> \
                                </span>\
                            </span>\
                            <input type="text" readonly="" id="Filetext' + fuelfilecount + '" name="Filetext[]" class="form-control">\
                        </div>\
                    </div>\
                    <div class="col-md-3 col-xs-3 pl-n pr-n mt-sm">\
                        <button type="button" class="btn btn-danger btn-raised remove_btn" id="p' + fuelfilecount + '" onclick="removefuelfile(' + fuelfilecount + ')" style="padding: 5px 10px;">\
                            <i class="fa fa-minus"></i>\
                            <div class="ripple-container"></div>\
                        </button>\
                        <button type="button" class="btn btn-primary btn-raised add_btn" id="p' + fuelfilecount + '" onclick="addnewfuelfile()" style="padding: 5px 10px;">\
                            <i class="fa fa-plus"></i>\
                            <div class="ripple-container"></div>\
                        </button>\
                    </div>\
                </div>\
            </div>';

    $(".remove_btn:first").show();
    $(".add_btn:last").hide();

    $('#fuelfiledata_div').append($.html);

}

function removefuelfile(rowid) {
    if (ACTION == 1 && $('#fuelfileid' + rowid).val() != null) {
        var fuelfileid = $('#fuelfileid').val();
        $('#fuelfileid').val(fuelfileid + ',' + $('#fuelfileid' + rowid).val());
    } 
    $('#fuelfilecount' + rowid).remove();

    $(".add_btn:last").show();
    if ($(".remove_btn:visible").length == 1) {
        $(".remove_btn:first").hide();
    }
}

function validfuelfile(obj, element, elethis) {
    var val = obj.val();
    var id = element.match(/\d+/);
    var filename = obj.val().replace(/C:\\fakepath\\/i, '');

    if (elethis.files[0].size <= UPLOAD_MAX_FILE_SIZE) {

        switch (val.substring(val.lastIndexOf('.') + 1).toLowerCase()) {
            case 'jpe': case 'pbm': case 'jpg': case 'jpeg': case 'png': case 'pdf':

                isvalidfuelfile = 1;
                $("#Filetext" + id).val(filename);
                $("#" + element + "_div").removeClass("has-error is-focused");
                break;
            default:
                isvalidfuelfile = 0;
                $("#" + element).val("");
                $("#Filetext" + id).val("");
                $("#" + element + "_div").addClass("has-error is-focused");
                new PNotify({ title: 'Accept only Image and PDF Files !',styling: 'fontawesome',delay: '3000',type: 'error'});
                break;
        }
    } else {
        isvalidfuelfile = 0;
        $("#" + element).val("");
        $("#Filetext" + id).val("");
        $("#" + element + "_div").addClass("has-error is-focused");
        new PNotify({title: 'File is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE) + ') !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }
}

function resetdata() {

    $("#vehicle_div").removeClass("has-error is-focused");
    $("#partyname_div").removeClass("has-error is-focused");
    $("#vehiclefueldate_div").removeClass("has-error is-focused");
    $("#fuel_div").removeClass("has-error is-focused");
    $("#payment_div").removeClass("has-error is-focused");
    $("#liter_div").removeClass("has-error is-focused");
    $("#km_div").removeClass("has-error is-focused");
    $("#amount_div").removeClass("has-error is-focused");
    $("#billno_div").removeClass("has-error is-focused");
    $("#location_div").removeClass("has-error is-focused");
    $("#remarks_div").removeClass("has-error is-focused");

    if (ACTION == 1) {

    } else {
        $("#vehicleid,#partyid,#paymenttype,#fueltype").val(0);
        $("#liter,#km,#amount,#billno,#location,#remarks").val('');
        
        $(".fuelfile:not(:first)").remove();
        var divid = parseInt($(".fuelfile:first").attr("id").match(/\d+/));

        $('#Filetext'+divid+',#fuelfile'+divid).val("");
        
        $('.remove_btn').hide();
        $('.add_btn:first').show();

        $(".selectpicker").selectpicker("refresh");
    }
    $('html, body').animate({scrollTop: 0}, 'slow');
}

function checkvalidation(addtype = 0) {

    var vehicle = $("#vehicleid").val();
    var partyid = $("#partyid").val();
    var vehiclefueldate = $("#vehiclefueldate").val();
    var fueltype = $("#fueltype").val();
    var paymenttype = $("#paymenttype").val();
    var liter = $("#liter").val();
    var km = $("#km").val();
    var amount = $("#amount").val();
    var billno = $("#billno").val();
    var location = $("#location").val();
    var remarks = $("#remarks").val();

    var isvalidvehicle = isvalidpartyid = isvaliddate = isvalidfueltype = isvalidpaymenttype = isvalidliter = isvalidkm = isvalidamount = isvalidbillno = isvalidlocation = isvalidremarks = 0;

    PNotify.removeAll();
    if (vehicle == 0) {
        $("#remarks_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select vehicle !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
        $("#remarks_div").removeClass("has-error is-focused");
        isvalidvehicle = 1;
    }

    if (partyid == 0) {
        $("#partyname_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select driver !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
        $("#partyname_div").removeClass("has-error is-focused");
        isvalidpartyid = 1;
    }

    if (vehiclefueldate == "") {
        $("#vehiclefueldate_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select date !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
        $("#vehiclefueldate_div").removeClass("has-error is-focused");
        isvaliddate = 1;
    }
    if (billno == "") {
        $("#billno_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter bill no. !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
        $("#billno_div").removeClass("has-error is-focused");
        isvalidbillno = 1;
    }

    if (fueltype == 0) {
        $("#fuel_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select fuel !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
        $("#fuel_div").removeClass("has-error is-focused");
        isvalidfueltype = 1;
    }

    if (paymenttype == 0) {
        $("#payment_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select payment type !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
        $("#payment_div").removeClass("has-error is-focused");
        isvalidpaymenttype = 1;
    }

    if (liter == "" || parseFloat(liter) <= 0) {
        $("#liter_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter liter !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
        $("#liter_div").removeClass("has-error is-focused");
        isvalidliter = 1;
    }

    if (km == "" || parseFloat(km) <= 0) {
        $("#km_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter km !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
        $("#km_div").removeClass("has-error is-focused");
        isvalidkm = 1;
    }

    if (amount == "" || parseFloat(amount) <= 0) {
        $("#amount_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter amount !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
        $("#amount_div").removeClass("has-error is-focused");    
        isvalidamount = 1;
    }

    if (location != "" && location.length < 2) {
        $("#location_div").addClass("has-error is-focused");
        new PNotify({title: 'Location require minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
        $("#location_div").removeClass("has-error is-focused");
        isvalidlocation = 1;
    }
    if (remarks != "" && remarks.length < 2) {
        $("#location_div").addClass("has-error is-focused");
        new PNotify({title: 'Remarks require minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
        $("#location_div").removeClass("has-error is-focused");
        isvalidremarks = 1;
    }
    if (isvalidvehicle == 1 && isvalidpartyid == 1 && isvaliddate == 1 && isvalidbillno == 1 && isvalidfueltype == 1 && isvalidpaymenttype == 1 && isvalidliter == 1 && isvalidkm == 1 && isvalidamount == 1 && isvalidlocation == 1 && isvalidremarks == 1) {

        var formData = new FormData($('#form-vehiclefuel')[0]);
        if (ACTION == 0) {
            var uurl = SITE_URL + "fuel/fuel-add";

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
                        new PNotify({title: "Fuel successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
                        if (addtype == 1) {
                            resetdata();
                        } else {
                            setTimeout(function () {window.location = SITE_URL + "fuel";}, 1500);
                        }
                    } else if (response == 2) {
                        new PNotify({title: "Fuel already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
                    } else if(response==-1){
                        new PNotify({title: 'File is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE)+') !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else if(response==-2){
                        new PNotify({title: 'File is not upload !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    } else {
                        new PNotify({title: 'Fuel not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

            var uurl = SITE_URL + "fuel/update-fuel";

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
                        new PNotify({title: "Fuel successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
                        if (addtype == 1) {
                            setTimeout(function () {window.location = SITE_URL + "fuel/add-fuel";}, 1500);
                        } else {
                            setTimeout(function () {window.location = SITE_URL + "fuel";}, 1500);
                        }
                    } else if (response == 2) {
                        new PNotify({title: "Fuel already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
                    } else if(response==-1){
                        new PNotify({title: 'File is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE)+') !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else if(response==-2){
                        new PNotify({title: 'File is not upload !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else {
                        new PNotify({title: 'Fuel not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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