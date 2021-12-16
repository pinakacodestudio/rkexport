$(document).ready(function () {
    $('#insurancedate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayBtn: "linked",
    });
    
    if(ACTION==1){
        getinsurancecompany($('#vehicleid').val());
        getpolicynumber();
        getInsuranceAgent();
    }
    
    
    $('#vehicleid').on('change', function (e) {
        getinsurancecompany(this.value);
    });
    $('#insurancecompany').on('change', function (e) {
        getpolicynumber();
        getInsuranceAgent();
    });
    $(".add_btn").hide();
    $(".add_btn:last").show();
});


function getInsuranceAgent(){
    
    var insurancecompanyname = $("#insurancecompany").val();

    $('#agentname')
            .find('option')
            .remove()
            .end()
            .append('<option value="">Select Insurance Agent</option>')
            .val('0')
        ;
        
        $('#agentname').selectpicker('refresh');

    
    if(insurancecompanyname!='' && insurancecompanyname!=null){
        var uurl = SITE_URL+"insurance-agent/getAgentDataByInsurancename";
        
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {insurancecompanyname:String(insurancecompanyname)},
        dataType: 'json',
        async: false,
        beforeSend: function() {
          $('.mask').show();
          $('#loader').show();
        },
        success: function(response){
            // var obj = JSON.parse(response);
            for(var i = 0; i < response.length; i++) {
                $('#agentname').append($('<option>', { 
                    value: response[i]['id'],
                    text : response[i]['agentname']
                }));
            }
            
            if(agent!=0){
                $('#agentname').val(agent);
            }
            $('#agentname').selectpicker('refresh');
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
        complete: function(){
          $('.mask').hide();
          $('#loader').hide();
        },
      });
    }
    
}



function getinsurancecompany(vehicleid) {
   
    $('#insurancecompany')
        .find('option')
        .remove()
        .end()
        .append('<option value="">Select Insurance Company</option>')
        .val('0');

    $('#policynumber')
        .find('option')
        .remove()
        .end()
        .append('<option value="0">Select Policy Number</option>')
        .val('0');

    $('#insurancecompany').selectpicker('refresh');
    $('#policynumber').selectpicker('refresh');

    if (vehicleid != 0) {
        var uurl = SITE_URL + "insurance/getInsuranceCompanyByVehicleId";
        $.ajax({
            url: uurl,
            type: 'POST',
            data: { vehicleid: vehicleid },
            dataType: 'json',
            async: false,
            beforeSend: function () {
                $('.mask').show();
                $('#loader').show();
            },
            success: function (response) {

                for (var i = 0; i < response.length; i++) {
                    $('#insurancecompany').append($('<option>', {
                        value: response[i]['companyname'],
                        text: response[i]['companyname']
                    }));
                }
                if(insurancecompany!=""){
                    $('#insurancecompany').val(insurancecompany);
                }
            },
            complete: function () {
                $('.mask').hide();
                $('#loader').hide();
            },
            error: function (xhr) {
                //alert(xhr.responseText);
            },
        });
    }
    $('#insurancecompany').selectpicker('refresh');
}

function getpolicynumber() {
    
    $('#policynumber')
        .find('option')
        .remove()
        .end()
        .append('<option value="0">Select Policy Number</option>')
        .val('0');

    $('#policynumber').selectpicker('refresh');

    var vehicleid = $('#vehicleid').val();
    var insurancecompany = $('#insurancecompany').val();

    if (vehicleid!=0 && insurancecompany != 0) {
        var uurl = SITE_URL + "insurance/getInsurancePolicyNumberByVehicleOrCompany";
        $.ajax({
            url: uurl,
            type: 'POST',
            data: {vehicleid: vehicleid, insurancecompany: insurancecompany},
            dataType: 'json',
            async: false,
            beforeSend: function () {
                $('.mask').show();
                $('#loader').show();
            },
            success: function (response) {

                for (var i = 0; i < response.length; i++) {
                    $('#policynumber').append($('<option>', {
                        value: response[i]['id'],
                        text: response[i]['policyno']
                    }));
                }
                if(policyno!=0){
                    $('#policynumber').val(policyno);
                }
            },
            complete: function () {
                $('.mask').hide();
                $('#loader').hide();
            },
            error: function (xhr) {
                //alert(xhr.responseText);
            },
        });
        $('#policynumber').selectpicker('refresh');
    }
}

function validfile(obj,elethis) {
    var val = obj.val();
    var id = obj.attr('id').match(/\d+/);
    var filename = obj.val().replace(/C:\\fakepath\\/i, '');
    if (elethis.files[0].size <= UPLOAD_MAX_FILE_SIZE) {
        switch (val.substring(val.lastIndexOf('.') + 1).toLowerCase()) {
            case 'pdf': case 'gif': case 'bmp': case 'jpg': case 'jpeg': case 'png':
                $("#Filetext" + id).val(filename);
                isvalidfiletext = 1;
                $("#attachmentcount" + id).removeClass("has-error is-focused");
                break;
            default:
                $("#attachment").val("");
                $("#Filetext" + id).val("");
                isvalidfiletext = 0;
                $("#attachmentcount" + id).addClass("has-error is-focused");
                new PNotify({ title: 'Accept only Image and PDF Files !', styling: 'fontawesome', delay: '3000', type: 'error' });
                break;
        }
    } else {
        isvaliddocfile = 0;
        $("#" + element).val("");
        $("#Filetext" + id).val("");
        $("#" + element + "_div").addClass("has-error is-focused");
        new PNotify({title: 'File is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE) + ') !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }
}

function resetdata() {

    $("#vehicle_div").removeClass("has-error is-focused");
    $("#insurancecompany_div").removeClass("has-error is-focused");
    $("#policynumber_div").removeClass("has-error is-focused");
    $("#claimnumber_div").removeClass("has-error is-focused");
    $("#amount_div").removeClass("has-error is-focused");
    $("#insurancedate_div").removeClass("has-error is-focused");
    $("#agentname_div").removeClass("has-error is-focused");
    $("#claimamount_div").removeClass("has-error is-focused");

    if (ACTION == 1) {

    } else {
        $("#vehicleid").val(0);
        $("#insurancecompany").val("");
        $("#policynumber").val("0");
        $("#claimnumber").val('');
        $("#amount").val('');
        $("#billnumber").val('');
        $("#insurancedate").val('');
        $("#agentname").val('');
        $("#claimamount").val('');
        
        $(".attachment:not(:first)").remove();
        var divid = parseInt($(".attachment:first").attr("id").match(/\d+/));

        $('#Filetext'+divid+',#attachment'+divid).val("");
        
        $('.remove_btn').hide();
        $('.add_btn:first').show();

        $('#yes').prop("checked", true);
        $('.selectpicker').selectpicker('refresh');
    }
    $('html, body').animate({scrollTop: 0}, 'slow');
}

function checkvalidation(addtype = 0) {

    var vehicleid = $("#vehicleid").val();
    var insurancecompany = $("#insurancecompany").val();
    var policynumber = $("#policynumber").val();
    var agentname = $("#agentname").val().trim();
    var billnumber = $("#billnumber").val().trim();
    var claimnumber = $("#claimnumber").val().trim();
    var amount = $("#amount").val().trim();
    var claimamount = $("#claimamount").val().trim();
    var insurancedate = $("#insurancedate").val().trim();
    
    var isvalidvehicleid = isvalidinsurancecompany = isvalidpolicynumber = isvalidagentname = isvalidbillnumber = isvalidclaimnumber = isvalidamount = isvalidclaimamount = isvalidinsurancedate = 0;

    PNotify.removeAll();
    
    if (vehicleid == 0) {
        $("#vehicle_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select vehicle !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
        $("#vehicle_div").removeClass("has-error is-focused");
        isvalidvehicleid = 1;
    }

    if (insurancecompany == "") {
        $("#insurancecompany_div").addClass("has-error is-focused");
        new PNotify({title: "Please select insurance company !",styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
        $("#insurancecompany_div").removeClass("has-error is-focused");
        isvalidinsurancecompany = 1;
    }

    if (policynumber == 0) {
        $("#policynumber_div").addClass("has-error is-focused");
        new PNotify({title: "Please select policy number !",styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
        $("#policynumber_div").removeClass("has-error is-focused");
        isvalidpolicynumber = 1;
    }
    
    if (agentname == '') {
        $("#agentname_div").addClass("has-error is-focused");
        new PNotify({title: "Please enter agent name !",styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
        $("#agentname_div").removeClass("has-error is-focused");
        isvalidagentname = 1;
    }

    if (billnumber == "") {
        $("#billnumber_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter bill number !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
        $("#billnumber_div").removeClass("has-error is-focused");
        isvalidbillnumber = 1;
    }

    if (claimnumber == '') {
        $("#claimnumber_div").addClass("has-error is-focused");
        new PNotify({title: "Please enter claim number !",styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
        $("#claimnumber_div").removeClass("has-error is-focused");
        isvalidclaimnumber = 1;
    }

    if (amount == 0) {
        $("#amount_div").addClass("has-error is-focused");
        new PNotify({title: "Please enter amount !",styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
        $("#amount_div").removeClass("has-error is-focused");
        isvalidamount = 1;
    }

    if (claimamount == 0) {
        $("#claimamount_div").addClass("has-error is-focused");
        new PNotify({title: "Please enter claim amount !",styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
        $("#claimamount_div").removeClass("has-error is-focused");
        isvalidclaimamount = 1;
    }

    if (insurancedate == 0) {
        $("#insurancedate_div").addClass("has-error is-focused");
        new PNotify({title: "Please select insurance date !",styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
        $("#insurancedate_div").removeClass("has-error is-focused");
        isvalidinsurancedate = 1;
    }

    if (isvalidvehicleid == 1 && isvalidinsurancecompany == 1 && isvalidpolicynumber == 1 && isvalidagentname == 1 && isvalidbillnumber == 1 && isvalidclaimnumber == 1 && isvalidamount == 1 && isvalidclaimamount == 1 && isvalidinsurancedate == 1) {

        var formData = new FormData($('#form-insuranceclaim')[0]);
        if (ACTION == 0) {
            var uurl = SITE_URL + "insurance-claim/insurance-claim-add/";

            $.ajax({
                url: uurl,
                type: 'POST',
                data: formData,
                //  async: false,
                beforeSend: function () {
                    $('.mask').show();
                    $('#loader').show();
                },
                success: function (response) {
                    if (response == 1) {
                        new PNotify({title: "Insurance claim successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
                        if (addtype == 1) {
                            resetdata();
                        } else {
                            setTimeout(function () {window.location = SITE_URL + "insurance-claim";}, 1500);
                        }
                    } else if (response == 2) {
                        new PNotify({title: "Insurance claim already exists !",styling: 'fontawesome',delay: '3000',type: 'error' });
                    } else if(response==-1){
                        new PNotify({title: 'File is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE)+') !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else if(response==-2){
                        new PNotify({title: 'File is not upload !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    } else {
                        new PNotify({title: 'Insurance claim not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

            var uurl = SITE_URL + "insurance-claim/update-insurance-claim/";

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
                        new PNotify({title: "Insurance claim successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
                        if (addtype == 1) {
                            setTimeout(function () {window.location = SITE_URL + "insurance-claim/add-insurance-claim";}, 1500);
                        } else {
                            setTimeout(function () {window.location = SITE_URL + "insurance-claim";}, 1500);
                        }
                    } else if (response == 2) {
                        new PNotify({title: "Insurance claim already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
                    } else if(response==-1){
                        new PNotify({title: 'File is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE)+') !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else if(response==-2){
                        new PNotify({title: 'File is not upload !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    } else {
                        new PNotify({title: 'Insurance claim not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

function addnewattachment() {
    attachmentcount = ++attachmentcount;
    var element = 'attachment' + attachmentcount;
    $.html = '<div class="col-md-4 col-sm-6 attachment" id="attachmentcount' + attachmentcount + '">\
                <div class="form-group">\
                    <div class="col-md-9 col-xs-9">\
                        <div class="input-group">\
                            <span class="input-group-btn" style="padding: 0 0px 0px 0px; ">\
                                <span class="btn btn-primary btn-raised btn-file">Browse...\
                                    <input type="file" class="attachment" name="attachment' + attachmentcount + '" id="attachment' + attachmentcount + '" accept=".jpe,.jpeg,.jpg,.pbm,.png,.pdf" onchange="validfile($(this),this)"> \
                                </span>\
                            </span>\
                            <input type="text" readonly="" id="Filetext' + attachmentcount + '" name="Filetext[]" class="form-control">\
                        </div>\
                    </div>\
                    <div class="col-md-3 col-xs-3 pl-n pr-n mt-sm">\
                        <button type="button" class="btn btn-danger btn-raised remove_btn" id="p' + attachmentcount + '" onclick="removeattachment(' + attachmentcount + ')" style="padding: 5px 10px;">\
                            <i class="fa fa-minus"></i>\
                            <div class="ripple-container"></div>\
                        </button>\
                        <button type="button" class="btn btn-primary btn-raised add_btn" id="p' + attachmentcount + '" onclick="addnewattachment()" style="padding: 5px 10px;">\
                            <i class="fa fa-plus"></i>\
                            <div class="ripple-container"></div>\
                        </button>\
                    </div>\
                </div>\
            </div>';

    $(".remove_btn:first").show();
    $(".add_btn:last").hide();

    $('#attachmentfiledata').append($.html);

}

function removeattachment(rowid) {
    if (ACTION == 1 && $('#attachmentid' + rowid).val() != null) {
        var attachmentid = $('#attachmentid').val();
        $('#attachmentid').val(attachmentid + ',' + $('#attachmentid' + rowid).val());
    } 
    $('#attachmentcount' + rowid).remove();

    $(".add_btn:last").show();
    if ($(".remove_btn:visible").length == 1) {
        $(".remove_btn:first").hide();
    }
} 
