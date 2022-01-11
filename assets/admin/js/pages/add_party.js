var today = new Date();
var dd = String(today.getDate()).padStart(2, '0');
var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
var yyyy = today.getFullYear();
birthyear = today.getFullYear() - 18;

today = dd + '/' + mm + '/' + yyyy;
dateofbirth = dd + '/' + mm + '/' + birthyear;

function removedata(id) {
    $('#' + id).remove();
}

function removecontectpaertion(id) {
    $('#' + id).remove();
}

$(document).ready(function() {
    var edit_country = $('#edit_country').val();
    $('#countryid').val(edit_country).trigger('change')
    var base_url = $('#base_url').val();
    var edit_provinceid = $('#edit_provinceid').val();
    var edit_cityid = $('#edit_cityid').val();




    if (edit_provinceid != '') {

        var uurl = base_url + "rkinsite/Party/getstate";

        $.ajax({
            url: uurl,
            method: 'post',
            data: { country: edit_country },
            dataType: 'json',
            success: function(response) {
                var option = ' <option value="0">Select State</option>';
                $.each(response, function(index, data) {
                    option += '<option value="' + data['id'] + '">' + data['statename'] + '</option>';
                });
                $('#stateid').html(option);
                $(".selectpicker").selectpicker("refresh");
                
                $('#stateid').val(edit_provinceid).trigger('change')
                getcity();
            }
        });

    }

    function getcity() {
        if (edit_cityid) {
            var uurl = base_url + "rkinsite/Party/getcity";
            $.ajax({
                url: uurl,
                method: 'post',
                data: { stat: edit_provinceid },
                dataType: 'json',
                success: function(response) {
                    var option = ' <option value="0">Select Ctiy</option>';
                    $.each(response, function(index, data) {
                        option += '<option value="' + data['id'] + '">' + data['cityname'] + '</option>';
                    });
                    $('#cityid').html(option);
                    $(".selectpicker").selectpicker("refresh");
                    $('#cityid').val(edit_cityid).trigger('change');
                    
                }
            });
        }
    }

    $('.countryid').change(function() {
        var country = $(this).val();
        var uurl = base_url + "rkinsite/Party/getstate";

        // AJAX request
        $.ajax({
            url: uurl,
            method: 'post',
            data: { country: country },
            dataType: 'json',
            success: function(response) {
                var option = ' <option value="0">Select State</option>';
                $.each(response, function(index, data) {
                    option += '<option value="' + data['id'] + '">' + data['statename'] + '</option>';
                });
                $('#stateid').html(option);
                $(".selectpicker").selectpicker("refresh");

                $('#stateid').val(edit_country).trigger('change')

            }
        });
    });

    $('#stateid').change(function() {
        var stat = $(this).val();
        var uurl = base_url + "rkinsite/Party/getcity";

        // AJAX request
        $.ajax({
            url: uurl,
            method: 'post',
            data: { stat: stat },
            dataType: 'json',
            success: function(response) {
                var option = ' <option value="0">Select City</option>';
                $.each(response, function(index, data) {
                    option += '<option value="' + data['id'] + '">' + data['cityname'] + '</option>';
                });
                $('#cityid').html(option);
                $(".selectpicker").selectpicker("refresh");
            }
        });
    });


    $('#openingdate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        orientation: 'top',
        autoclose: true,
        todayBtn: "linked"
    });


    $('body').on('focus', ".date", function() {
        $(this).datepicker({
            todayHighlight: true,
            format: 'dd/mm/yyyy',
            orientation: "top left",
            endDate: dateofbirth,
            clearBtn: true,
        });
    });

    $("#old_receipt_div").hide();
    $('#remove').click(function() {
        $('#removeoldreceipt').val('1');
    });

    $(".selectpicker").selectpicker("refresh");

    $('#countryid').on('change', function(e) {

        $('#provinceid')
            .find('option')
            .remove()
            .end()
            .append('<option value="">Select Province</option>')
            .val('0');
        $('#cityid')
            .find('option')
            .remove()
            .end()
            .append('<option value="">Select City</option>')
            .val('0');
        $('#provinceid').selectpicker('refresh');
        $('#cityid').selectpicker('refresh');
        getprovince(this.value);
    });
    $('#provinceid').on('change', function(e) {

        $('#cityid')
            .find('option')
            .remove()
            .end()
            .append('<option value="">Select City</option>')
            .val('0');
        $('#cityid').selectpicker('refresh');
        getcity(this.value);
    });

    $('.fromdate,.duedate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        todayBtn: "linked",
        orientation: "top left",
        clearBtn: true,
    });

    $('#birthdate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        orientation: "top left",
        endDate: dateofbirth,
        clearBtn: true,
    });
    $('#anniversarydate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        todayBtn: "linked",
        orientation: "top left",
        clearBtn: true,
    });

    $(".add_btn").hide();
    $(".add_btn:last").show();
});

function validdocumentfile(obj, element) {
    var val = obj.val();
    var id = obj.attr('id').match(/\d+/);
    var filename = obj.val().replace(/C:\\fakepath\\/i, '');

    switch (val.substring(val.lastIndexOf('.') + 1).toLowerCase()) {
        case 'pdf':
        case 'gif':
        case 'bmp':
        case 'jpg':
        case 'jpeg':
        case 'png':

            isvalidimageorpdffile = 1;
            $("#isvalid" + element).val("1");
            $("#Filetext" + element).val(filename);
            $("#" + element + "_div").removeClass("has-error is-focused");
            break;
        default:
            $("#isvalid" + element).val("0");
            $("#Filetext" + element).val("");
            $("#" + element + "_div").addClass("has-error is-focused");
            new PNotify({ title: 'Accept only Image and PDF Files !', styling: 'fontawesome', delay: '3000', type: 'error' });
            break;
    }
}

function addNewDocument() {

    var cloopcount = $("#cloopcount").val();
    cloopcount++;
    $("#cloopcount").val(cloopcount);
    var rowcount = parseInt($(".countdocuments:last").attr("id").match(/\d+/)) + 1;
    var datahtml = '<div class="col-sm-12 countdocuments pl-sm pr-sm" id="countdocuments' + rowcount + '">\
                      <input type="hidden" name="documentid[' + rowcount + ']" value="" id="documentid' + rowcount + '">\
                      <div class="col-md-5 col-sm-5">\
                          <div class="form-group" id="documentnumber' + rowcount + '_div">\
                              <div class="col-sm-12 pr-xs pl-xs">\
                                  <input id="documentnumber' + rowcount + '" name="documentnumber[' + rowcount + ']" placeholder="Enter Document Number" class="form-control documentnumber">\
                              </div>\
                          </div>\
                      </div>\
                      <div class="col-md-5 col-sm-5">\
                          <div class="form-group" id="docfile' + rowcount + '_div">\
                              <div class="col-sm-12 pr-xs pl-xs">\
                                  <input type="hidden" id="isvaliddocfile' + rowcount + '" value="0">\
                                  <input type="hidden" name="olddocfile[' + rowcount + ']" id="olddocfile' + rowcount + '" value="">\
                                  <div class="input-group" id="fileupload' + rowcount + '">\
                                      <span class="input-group-btn" style="padding: 0 0px 0px 0px;">\
                                          <span class="btn btn-primary btn-raised btn-file">\
                                          <i class="fa fa-upload"></i>\
                                              <input type="file" name="docfile' + rowcount + '" class="docfile" id="docfile' + rowcount + '" accept=".png,.jpeg,.jpg,.bmp,.gif,.pdf" onchange="validdocumentfile($(this),&apos;docfile' + rowcount + '&apos;)">\
                                          </span>\
                                      </span>\
                                      <input type="text" readonly="" placeholder="Enter File" id="Filetextdocfile' + rowcount + '" class="form-control docfile" name="Filetextdocfile[' + rowcount + ']" value="">\
                                  </div>\
                              </div>\
                          </div>\
                      </div>\
                      <div class="col-md-1 addrowbutton pt-md pr-xs">\
                          <button type="button" class="btn btn-danger btn-raised remove_btn m-n" onclick="removeDocument(' + rowcount + ')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
                          <button type="button" class="btn btn-primary btn-raised add_btn m-n" onclick="addNewDocument()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>\
                      </div>\
                  </div>';

    $(".remove_btn:first").show();
    $(".add_btn:last").hide();
    $("#countdocuments" + (rowcount - 1)).after(datahtml);

}

function removeDocument(rowid) {
    $("#countdocuments" + rowid).remove();

    $(".add_btn:last").show();
    if ($(".remove_btn:visible").length == 1) {
        $(".remove_btn:first").hide();
    }
}

function resetdata() {

    $("#websitename_div").removeClass("has-error is-focused");
    $("#companyid_div").removeClass("has-error is-focused");
    $("#gst_div").removeClass("has-error is-focused");
    $("#partycode_div").removeClass("has-error is-focused");
    $("#partytype_div").removeClass("has-error is-focused");
    $("#email_div").removeClass("has-error is-focused");
    $("#contactno_div").removeClass("has-error is-focused");
    $("#partytypeid_div").removeClass("has-error is-focused");
    $("#address_div").removeClass("has-error is-focused");
    $("#password_div").removeClass("has-error is-focused");
    $("#employeerole_div").removeClass("has-error is-focused");

    if (ACTION == 1) {
        $('#websitename').focus();
        $('#websitename_div').addClass('is-focused');

    } else {
        $("#websitename").val('').focus();
        $('#websitename_div').addClass('is-focused');

        $("#companyid,#gst,#partycode,#email,#contactno,#partytypeid,#birthdate,#anniversarydate,#address,#password").val('');
        $("#pan,#countryid,#provinceid,#cityid,#employeerole").val('0');
        $("#male").prop("checked", true);
        $('#allowforlogin').prop("checked", false);
        $(".allowlogin").hide();
        $("#education").select2("val", "");

        $(".countdocuments:not(:first)").remove();
        var divid = parseInt($(".countdocuments:first").attr("id").match(/\d+/));

        $('#documenttypeid' + divid + ',#licencetype' + divid + ',#isvaliddocfile' + divid).val("0");
        $('#documentnumber' + divid + ',#fromdate' + divid + ',#duedate' + divid + ',#olddocfile' + divid + ',#Filetextdocfile' + divid).val("");

        $('#documenttype' + divid + '_div').removeClass("has-error is-focused");
        $('#documentnumber' + divid + '_div').removeClass("has-error is-focused");
        $('#docfile' + divid + '_div').removeClass("has-error is-focused");

        $('.add_btn:first').show();
        $('.remove_btn').hide();

        $(".selectpicker").selectpicker("refresh");
    }
    $('html, body').animate({ scrollTop: 0 }, 'slow');
}

function checkvalidation(addtype = 0) {

    var websitename = $("#websitename").val().trim();
    var companyid = $("#companyid").val().trim();
    //alert(companyid);
    var isvalidwebsitename = isvalidcompanyid = isvalidgst = isvalidpartycode = isvalidpan = isvalidemail = isvalidcontactno = isvalidpartytypeid = isvalidaddress = isvalidcountryid = isvalidstateid = isvalidcityid = isvalidbillingaddress = isvalidshippingaddress = isvalidcourieraddress = isvalidfirstname = isvalidlastname = isvalidbirthdate = isvalidanniversarydate = 0;

    var isvalidwebsitename = 1;

    PNotify.removeAll();
    if (websitename == '') {
        $("#websitename_div").addClass("has-error is-focused");
        new PNotify({ title: 'Please enter Website Name !', styling: 'fontawesome', delay: '3000', type: 'error' });
    } else {
        $("#websitename_div").removeClass("has-error is-focused");
        isvalidwebsitename = 1;
    }



    if (companyid == '' || companyid == 0) {
        $("#companyid_div").addClass("has-error is-focused");
        new PNotify({ title: 'Please Select Company!', styling: 'fontawesome', delay: '3000', type: 'error' });
    } else {
        $("#companyid_div").removeClass("has-error is-focused");
        isvalidcompanyid = 1;
    }

    if (gst == '') {
        $("#gst_div").addClass("has-error is-focused");
        new PNotify({ title: 'Please enter Gst !', styling: 'fontawesome', delay: '3000', type: 'error' });
    } else if (gst.length < 10) {
        $("#gst_div").addClass("has-error is-focused");
        new PNotify({ title: 'require minimum 14 characters !', styling: 'fontawesome', delay: '3000', type: 'error' });
    } else {
        $("#gst_div").removeClass("has-error is-focused");
        isvalidgst = 1;
    }

    if (partycode == '') {
        $("#partycode_div").addClass("has-error is-focused");
        new PNotify({ title: 'Please enter party code !', styling: 'fontawesome', delay: '3000', type: 'error' });
    } else if (partycode.length < 2) {
        $("#partycode_div").addClass("has-error is-focused");
        new PNotify({ title: 'Party code require minimum 2 characters !', styling: 'fontawesome', delay: '3000', type: 'error' });
    } else {
        $("#partycode_div").removeClass("has-error is-focused");
        isvalidpartycode = 1;
    }

    if (pan == 0) {
        $("#partytype_div").addClass("has-error is-focused");
        new PNotify({ title: 'Please Enter Pan !', styling: 'fontawesome', delay: '3000', type: 'error' });
    } else {
        $("#partytype_div").removeClass("has-error is-focused");
        isvalidpan = 1;
    }

    if (email == '') {
        $("#email_div").addClass("has-error is-focused");
        new PNotify({ title: 'Please enter email !', styling: 'fontawesome', delay: '3000', type: 'error' });
    }
    var c = 1;
    if (isvalidwebsitename == 1) {
        var formData = new FormData($('#party-form')[0]);
        if (ACTION == 0) {
            var uurl = SITE_URL + "party/party-add";
            $.ajax({
                url: uurl,
                type: 'POST',
                data: formData,
                beforeSend: function() {
                    $('.mask').show();
                    $('#loader').show();
                },
                success: function(response) {
                    alert(response);
                    if (response == 1) {
                        new PNotify({ title: "Party successfully added.", styling: 'fontawesome', delay: '3000', type: 'success' });
                        if (addtype == 1) {
                            resetdata();
                        } else {
                            setTimeout(function() { window.location = SITE_URL + "Party"; }, 1500);
                        }
                    } else if (response == 2) {
                        new PNotify({ title: "Party code or email or contact number already exists !", styling: 'fontawesome', delay: '3000', type: 'error' });
                        $("#name_div").addClass("has-error is-focused");
                    } else {
                        new PNotify({ title: 'Party not added !', styling: 'fontawesome', delay: '3000', type: 'error' });
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

            var uurl = SITE_URL + "party/update-party";

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
                    if (response == 1) {
                        new PNotify({ title: "Party successfully updated.", styling: 'fontawesome', delay: '1500', type: 'success' });
                        if (addtype == 1) {
                            setTimeout(function() { window.location = SITE_URL + "party/add-party"; }, 1500);
                        } else {
                            setTimeout(function() { window.location = SITE_URL + "party"; }, 1500);
                        }
                    } else if (response == 2) {
                        new PNotify({ title: "Party code or email or contact number already exists !", styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else {
                        new PNotify({ title: 'Party not updated !', styling: 'fontawesome', delay: '3000', type: 'error' });
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