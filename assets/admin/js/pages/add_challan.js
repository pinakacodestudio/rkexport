$(document).ready(function () {
    $('#date1').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayBtn: "linked",
    });

    if (ACTION == 1) {
        calculatetotal();
    }
})

$(document).on('keyup', '.amount', function () {
    calculatetotal();
})

function calculatetotal() {
    var totalval = 0
    $(".amount").each(function (index) {
        if ($(this).val() != "") {
            totalval += parseFloat($(this).val());
        }
    });
    $('#totalcount').html(format.format(totalval));
}

function addnewrow() {
    rowcount = ++rowcount;
    var element = 'file' + rowcount;
    $.html = '<div class="row challancheck" id="row' + rowcount + '">\
                <div class="col-md-2 col-sm-6">\
                    <div class="form-group challantype_div" id="challantype_div_' + rowcount + '">\
                        <div class="col-md-12 pr-sm pl-sm">\
                            <select class="selectpicker form-control challantype" name="challantype[' + rowcount + ']" id="challantype' + rowcount + '" data-live-search="true">\
                                <option value="0">Select Challan Type</option>' + challantypedata + '\
                            </select>\
                        </div>\
                    </div>\
                </div>\
                <div class="col-md-2 col-sm-6">\
                    <div class="form-group date_div" id="date_div_' + rowcount + '">\
                        <div class="col-md-12 pl-sm pr-sm">\
                        <div class="input-group">\
                                <input type="text" id="date' + rowcount + '" name="date[' + rowcount + ']" placeholder="Enter Date" class="form-control challanrow date" readonly>\
                                <span class="btn btn-default add-on datepicker_calendar_button" title="Date"><i class="fa fa-calendar fa-lg"></i></span>\
                            </div>\
                        </div>\
                    </div>\
                </div>\
                <div class="col-md-2 col-sm-6">\
                    <div class="form-group amount_div" id="amount_div_' + rowcount + '">\
                        <div class="col-md-12 pl-sm pr-sm">\
                            <input type="text" id="amount' + rowcount + '" name="amount[' + rowcount + ']" placeholder="Enter Amount" class="form-control challanamount amount challanrow text-right" onkeypress="return decimal_number_validation(event, this.value, 14)">\
                        </div>\
                    </div>\
                </div>\
                <div class="col-md-3 col-sm-6" style="padding:0px 10px 0px 10px;">\
                    <div class="form-group" id="file' + rowcount + '_div">\
                        <div class="col-md-12">\
                            <div class="input-group" id="fileupload' + rowcount + '">\
                                <span class="input-group-btn" style="padding: 0 0px 0px 0px;">\
                                    <span class="btn btn-primary btn-raised btn-sm btn-file"><i class="fa fa-upload"></i>\
                                        <input type="file" name="attachment' + rowcount + '" id="file' + rowcount + '" accept=".png,.jpeg,.jpg,.bmp,.gif,.pdf" onchange="validfile($(this),\'file' + rowcount + '\',this)">\
                                    </span>\
                                </span>\
                                <input type="text" id="Filetext' + rowcount + '" class="form-control challanrow" placeholder="Enter File" name="Filetext[]" readonly>\
                            </div>\
                        </div>\
                    </div>\
                </div>\
                <div class="col-md-2 col-sm-6">\
                    <div class="form-group" id="remarks_div_' + rowcount + '">\
                        <div class="col-md-12 pl-sm pr-sm">\
                            <input type="text" id="remarks" name="remarks[' + rowcount + ']" placeholder="Enter Remarks" class="form-control challanrow">\
                        </div>\
                    </div>\
                </div>\
                <div class="col-md-1 addrowbutton">\
                    <div class="form-group" id="metadescription_div">\
                        <div class="col-md-12 pl-sm pr-sm">\
                            <button type="button" class="btn btn-danger btn-raised remove_btn" id="p1" onclick="removerow(' + rowcount + ')" style="padding: 5px 10px;margin-top: 14px;">\
                                <i class="fa fa-minus"></i>\
                                <div class="ripple-container"></div>\
                            </button>\
                            <button type="button" class="btn btn-primary btn-raised add_btn" id="p1" onclick="addnewrow()" style="padding: 5px 10px;margin-top: 14px;">\
                                <i class="fa fa-plus"></i>\
                                <div class="ripple-container"></div>\
                            </button>\
                        </div>\
                    </div>\
                </div>\
            </div>';

    $(".remove_btn:first").show();
    $(".add_btn:last").hide();

    $('#challanmore').append($.html);

    $('#date' + rowcount).datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayBtn: "linked",
        endDate: new Date()
    });
    $('#challantype' + rowcount).selectpicker('refresh');
}

function removerow(rowid) {
    
    $('#row' + rowid).remove();

    $(".add_btn:last").show();
    if ($(".remove_btn:visible").length == 1) {
        $(".remove_btn:first").hide();
    }
    calculatetotal();
}

function validfile(obj, element, elethis) {
    var val = obj.val();
    var id = element.match(/\d+/);
    var filename = obj.val().replace(/C:\\fakepath\\/i, '');

    if (elethis.files[0].size <= UPLOAD_MAX_FILE_SIZE) {

        switch (val.substring(val.lastIndexOf('.') + 1).toLowerCase()) {
            case 'jpe': case 'jpg': case 'jpeg': case 'png': case 'pdf':

                isvalidfile = 1;
                $("#Filetext" + id).val(filename);
                $("#" + element + "_div").removeClass("has-error is-focused");
                break;
            default:
                isvalidfile = 0;
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

function checkvalidation(addtype = 0) {
    
    var vehicleid = $("#vehicle").val();
    var challanfor = $("#challanfor").val();
    var isvalidvehicleid = isvalidchallanfor = 0;
    var isvalidchallantype = isvaliddate = isvalidamount = 1;

    PNotify.removeAll();
    if (vehicleid == 0) {
        $("#vehicle_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select vehicle !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
        $("#vehicle_div").removeClass("has-error is-focused");
        isvalidvehicleid = 1;
    }
    if (challanfor == 0) {
        $("#challanfor_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select driver !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
        $("#challanfor_div").removeClass("has-error is-focused");
        isvalidchallanfor = 1;
    }

    var count = 0;
    $(".challancheck").each(function (index) {
        count++;
        var id = $(this).attr('id').match(/\d+/);
        var challantype = $("#challantype" + id).val();
        var date = $("#date" + id).val();
        var amount = $("#amount" + id).val();

        if (challantype == 0) {
            $("#challantype_div_" + id).addClass("has-error is-focused");
            new PNotify({title: 'Please select ' + count + ' challan type !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidchallantype = 0;
        } else {
            $("#challantype_div_" + id).removeClass("has-error is-focused");
        }

        if (date == '') {
            $("#date_div_" + id).addClass("has-error is-focused");
            new PNotify({title: 'Please select ' + count + ' date !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvaliddate = 0;
        } else {
            $("#date_div_" + id).removeClass("has-error is-focused");
        }

        if (amount == '') {
            $("#amount_div_" + id).addClass("has-error is-focused");
            new PNotify({title: 'Please enter ' + count + ' amount !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidamount = 0;
        } else {
            $("#amount_div_" + id).removeClass("has-error is-focused");
        }
    });

    if (isvalidvehicleid == 1 && isvalidchallanfor == 1 && isvalidchallantype == 1 && isvaliddate == 1 && isvalidamount == 1) {

        var formData = new FormData($('#form-challan')[0]);
        if (ACTION == 0) {
            var uurl = SITE_URL + "challan/challan-add";

            $.ajax({
                url: uurl,
                type: 'POST',
                data: formData,
                beforeSend: function () {
                    $('.mask').show();
                    $('#loader').show();
                },
                success: function (response) {

                    if (response == 1) {
                        new PNotify({title: "Challan successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
                        if (addtype == 1) {
                            resetdata();
                        } else {
                            setTimeout(function () {window.location = SITE_URL + "challan";}, 1500);
                        }
                    } else if (response == 2) {
                        new PNotify({title: "Challan already exists.",styling: 'fontawesome',delay: '3000',type: 'error'});
                    } else if(response==-1){
                        new PNotify({title: 'File is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE)+') !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    } else if(response==-2){
                        new PNotify({title: 'File is not upload !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    } else {
                        new PNotify({title: 'Challan not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }
                },
                error: function (xhr) {
                    // alert(xhr.responseText);
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

            var uurl = SITE_URL + "challan/update-challan";

            $.ajax({
                url: uurl,
                type: 'POST',
                data: formData,
                beforeSend: function () {
                    $('.mask').show();
                    $('#loader').show();
                },
                success: function (response) {
                    if (response == 1) {
                        new PNotify({title: "Challan successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
                        if (addtype == 1) {
                            setTimeout(function () {window.location = SITE_URL + "challan/add-challan/";}, 1500);
                        } else {
                            setTimeout(function () {window.location = SITE_URL + "challan";}, 1500);
                        }
                    } else if (response == 2) {
                        new PNotify({title: "Challan name already exists.",styling: 'fontawesome',delay: '3000',type: 'error'});
                    } else if(response==-1){
                        new PNotify({title: 'File is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE)+') !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    } else if(response==-2){
                        new PNotify({title: 'File is not upload !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else {
                        new PNotify({title: 'Challan not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

function resetdata() {
    $("#vehicle_div").removeClass("has-error is-focused");
    $("#challanfor_div").removeClass("has-error is-focused");

    if (ACTION == 1) {
        
    } else {
        $("#vehicle").val(0);
        $("#challanfor").val(0);
        
        $(".challancheck:not(:first)").remove();
        var divid = parseInt($(".challancheck:first").attr("id").match(/\d+/));

        $('#challantype'+divid).val("0");
        $('#amount'+divid+',#attachment'+divid+',#Filetext'+divid+',#remaks'+divid+',#date'+divid).val("");
        
        $("#challantype_div_"+divid+",#date_div_"+divid+",#amount_div_"+divid+",#file"+divid+"_div").removeClass("has-error is-focused");

        $('.add_btn:first').show();
        $('.remove_btn').hide();
        $('.selectpicker').selectpicker('refresh');
    }
    calculatetotal();
    $('html, body').animate({scrollTop: 0}, 'slow');
}