$(document).ready(function (e) {
    $(function () {
        var tabindex = 1;
        $('input,select,textarea,a').each(function () {
            if (this.type != "hidden") {
                var $input = $(this);
                $input.attr("tabindex", tabindex);
                tabindex++;
            }
        });
    });
    $('.yesno input[type="checkbox"]').bootstrapToggle({
        on: 'Yes',
        off: 'No',
        onstyle: 'primary',
        offstyle: 'danger',
        /* style: 'android' */
    });

    $('#dateofregistration').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayBtn: "linked",
        clearBtn: true,
        endDate: new Date()
    }).on('changeDate', function (ev) {
        var dd = String(ev.date.getDate()).padStart(2, '0');
        var mm = String(ev.date.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = ev.date.getFullYear();
        var RegDate = dd + "/" + mm + "/" + yyyy;

        /* 15 Years To Registration Date  */
        var due = new Date(yyyy + 15, mm, dd);
        var dued = String(due.getDate()).padStart(2, '0');
        var duem = String(due.getMonth() + 1).padStart(2, '0'); //January is 0!
        var duey = due.getFullYear();
        var dueDate = dued + "/" + duem + "/" + duey;
        $('#duedateofregistration').val(dueDate);

        $("select.documenttypeid").each(function () {
            var id = $(this).attr('id').match(/\d+/);
            var docuemnttype = $("#documenttypeid" + id + " option:selected").text().trim();
            if (this.value != 0 && (docuemnttype == "RC Book")) {
                $("#fromdate" + id).val(RegDate);
            }
        });
    });
    $('#duedateofregistration').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayBtn: "linked",
        clearBtn: true
    }).on('changeDate', function (ev) {
        var dd = String(ev.date.getDate()).padStart(2, '0');
        var mm = String(ev.date.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = ev.date.getFullYear();
        var Date = dd + "/" + mm + "/" + yyyy;

        $("select.documenttypeid").each(function () {
            var id = $(this).attr('id').match(/\d+/);
            var docuemnttype = $("#documenttypeid" + id + " option:selected").text().trim();
            if (this.value != 0 && (docuemnttype == "RC Book")) {
                $("#duedate" + id).val(Date);
            }
        });
    });
    $('#solddate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayBtn: "linked",
        endDate: new Date()
    });
    $('#paymentdate1').datepicker({
        todayHighlight: true,
        todayBtn: "linked",
        clearBtn: true,
        format: 'dd/mm/yyyy',
    });

    $('.fromdate,.duedate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        todayBtn: "linked",
        orientation: "top left",
        clearBtn: true,
    });

    $('.datepicker-range').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy'
    });

    $('#challandate1').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayBtn: "linked",
    });

    $('#emidate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayBtn: "linked",
    });

    $('input[name="sold"]').change(function () {
        if ($(this).prop("checked") == false) {
            $('#solddate_div,#soldparty_div').hide();
        } else {
            $('#solddate_div,#soldparty_div').show();
        }
    });

    $(".add_btn").hide();
    $(".add_btn:last").show();

    $(".add_btn_insurance").hide();
    $(".add_btn_insurance:last").show();

    $(".add_btn_challan").hide();
    $(".add_btn_challan:last").show();


    $(".Insurance-hr:last").hide();

    calculatetotal();

    $("[data-provide='companyid']").each(function () {
        var $element = $(this);

        $element.select2({
            allowClear: true,
            minimumInputLength: 3,
            width: '100%',
            placeholder: $element.attr("placeholder"),
            createSearchChoice: function (term, data) {
                if ($(data).filter(function () {
                        return this.text.localeCompare(term) === 0;
                    }).length === 0) {
                    return {
                        id: term,
                        text: term
                    };
                }
            },
            ajax: {
                url: $element.data("url"),
                dataType: 'json',
                type: "POST",
                quietMillis: 50,
                data: function (term) {
                    return {
                        term: term,
                    };
                },
                results: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.text,
                                id: item.id
                            }
                        })
                    };
                }
            },
            initSelection: function (element, callback) {
                var id = $(element).val();
                if (id !== "" && id != 0) {
                    $.ajax($element.data("url"), {
                        data: {
                            ids: id,
                        },
                        type: "POST",
                        dataType: "json",
                    }).done(function (data) {
                        callback(data);
                    });
                }
            }
        });
    });

    $("[data-provide='companyname']").each(function () {
        var $element = $(this);

        $element.select2({
            allowClear: true,
            minimumInputLength: 3,
            width: '100%',
            placeholder: $element.attr("placeholder"),
            createSearchChoice: function (term, data) {
                if ($(data).filter(function () {
                        return this.text.localeCompare(term) === 0;
                    }).length === 0) {
                    return {
                        id: term,
                        text: term
                    };
                }
            },
            ajax: {
                url: $element.data("url"),
                dataType: 'json',
                type: "POST",
                quietMillis: 50,
                data: function (term) {
                    return {
                        term: term,
                    };
                },
                results: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.text,
                                id: item.text
                            }
                        })
                    };
                }
            },
            initSelection: function (element, callback) {
                var id = $(element).val();
                if (id !== "" && id != 0) {
                    $.ajax($element.data("url"), {
                        data: {
                            ids: id,
                        },
                        type: "POST",
                        dataType: "json",
                    }).done(function (data) {
                        callback(data);
                    });
                }
            }
        });
    });

});

$(document).on('keyup', '.challanamount', function () {
    calculatetotal();
})
$(document).on('change', '.documenttypeid', function () {
    var id = $(this).attr('id').match(/\d+/);
    var docuemnttype = $("#documenttypeid" + id + " option:selected").text().trim();
    if (docuemnttype == "RC Book") {
        var dateofregistration = $("#dateofregistration").val();
        var duedateofregistration = $("#duedateofregistration").val();

        if (dateofregistration != "") {
            if ($("#fromdate" + id).val() == "") {
                $("#fromdate" + id).val(dateofregistration);
            }
        }
        if (duedateofregistration != "") {
            if ($("#duedate" + id).val() == "") {
                $("#duedate" + id).val(duedateofregistration);
            }
        }
    }
    calculatetotal();
});

$(document).on('change', '.insurancecompany', function () {
    var id = $(this).attr('id').match(/\d+/);
    getInsuranceAgent(id);
});

function getInsuranceAgent(id){
    
    var insurancecompanyname = $("#insurancecompanyname" + id).val();

    $('#insuranceagent'+id)
            .find('option')
            .remove()
            .end()
            .append('<option value="">Select Insurance Agent</option>')
            .val('0')
        ;
        
        $('#insuranceagent'+id).selectpicker('refresh');
    
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
                $('#insuranceagent'+id).append($('<option>', { 
                    value: response[i]['id'],
                    text : response[i]['agentname']
                }));
            }
            $('#insuranceagent'+id).selectpicker('refresh');
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


function calculatetotal() {
    var totalval = 0
    $(".challanamount").each(function (index) {
        if ($(this).val() != "") {
            totalval += parseFloat($(this).val());
        }
    });
    $('#totalcount').html(format.format(totalval));
}

function validdocumentfile(obj, element,elethis) {
    var val = obj.val();
    var id = obj.attr('id').match(/\d+/);
    var filename = obj.val().replace(/C:\\fakepath\\/i, '');
    if (elethis.files[0].size <= UPLOAD_MAX_FILE_SIZE) {
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
                new PNotify({
                    title: 'Accept only Image and PDF Files !',
                    styling: 'fontawesome',
                    delay: '3000',
                    type: 'error'
                });
                break;
        }
    } else {
        $("#" + element).val("");
        $("#Filetext" + element).val("");
        $("#" + element + "_div").addClass("has-error is-focused");
        new PNotify({title: 'File is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE) + ') !',styling: 'fontawesome',delay: '3000',type: 'error'
        });
    }
}

function addNewDocument() {

    var rowcount = parseInt($(".countdocuments:last").attr("id").match(/\d+/)) + 1;

    var datahtml = '<div class="col-md-12 col-sm-12 countdocuments " id="countdocuments' + rowcount + '">\
                      <div class="col-md-2 col-sm-4">\
                          <div class="form-group mt-n" id="documenttype' + rowcount + '_div">\
                            <div class="col-md-12 col-sm-12 pr-xs pl-xs">\
                              <select id="documenttypeid' + rowcount + '" name="documenttypeid[' + rowcount + ']" class="selectpicker form-control documenttypeid" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                <option value="0">Select Document Type</option>\
                                ' + DOCUMENT_TYPE_DATA + '\
                              </select>\
                            </div>\
                          </div>\
                      </div>\
                      <div class="col-md-2 col-sm-4">\
                          <div class="form-group mt-n" id="documentnumber' + rowcount + '_div">\
                              <div class="col-md-12 col-sm-12 pr-xs pl-xs">\
                                  <input id="documentnumber' + rowcount + '" name="documentnumber[' + rowcount + ']" placeholder="Enter Document Number" class="form-control documentrow documentnumber">\
                              </div>\
                          </div>\
                      </div>\
                      <div class="col-md-2 col-sm-4">\
                          <div class="form-group mt-n" id="fromdate' + rowcount + '_div">\
                              <div class="col-md-12 col-sm-12 pr-xs pl-xs">\
                                  <div class="input-group">\
                                    <input type="text" id="fromdate' + rowcount + '" name="fromdate[' + rowcount + ']" placeholder="Enter Register Date" class="form-control documentrow fromdate" readonly>\
                                    <span class="btn btn-default add-on datepicker_calendar_button" title="Date"><i class="fa fa-calendar fa-lg"></i></span>\
                                  </div>\
                              </div>\
                          </div>\
                      </div>\
                      <div class="col-md-2 col-sm-4">\
                          <div class="form-group mt-n" id="duedate' + rowcount + '_div">\
                              <div class="col-md-12 col-sm-12 pr-xs pl-xs">\
                                <div class="input-group">\
                                  <input type="text" id="duedate' + rowcount + '" name="duedate[' + rowcount + ']" placeholder="Enter Due Date" class="form-control documentrow duedate" readonly>\
                                        <span class="btn btn-default add-on datepicker_calendar_button" title="Date"><i class="fa fa-calendar fa-lg"></i></span>\
                                    </div>\
                              </div>\
                          </div>\
                      </div>\
                      <div class="col-md-2 col-sm-4">\
                          <div class="form-group mt-n" id="docfile' + rowcount + '_div">\
                              <div class="col-md-12 col-sm-12 pr-xs pl-xs">\
                                  <input type="hidden" id="isvaliddocfile' + rowcount + '" value="0">\
                                  <input type="hidden" name="olddocfile[' + rowcount + ']" id="olddocfile' + rowcount + '" value="">\
                                  <div class="input-group" id="fileupload' + rowcount + '">\
                                      <span class="input-group-btn" style="padding: 0 0px 0px 0px;">\
                                          <span class="btn btn-primary btn-raised btn-file">\
                                          <i class="fa fa-upload"></i>\
                                              <input type="file" name="docfile' + rowcount + '" class="docfile" id="docfile' + rowcount + '" accept=".png,.jpeg,.jpg,.bmp,.gif,.pdf" onchange="validdocumentfile($(this),&apos;docfile' + rowcount + '&apos;,this)">\
                                          </span>\
                                      </span>\
                                      <input type="text" readonly="" id="Filetextdocfile' + rowcount + '" placeholder="Enter File" class="form-control documentrow" name="Filetextdocfile[' + rowcount + ']" value="">\
                                  </div>\
                              </div>\
                          </div>\
                      </div>\
                      <div class="col-md-1 col-sm-12 addrowbutton pt-sm pr-xs">\
                          <button type="button" class="btn btn-danger btn-raised remove_btn m-n" onclick="removeDocument(' + rowcount + ')" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>\
                          <button type="button" class="btn btn-primary btn-raised add_btn m-n" onclick="addNewDocument()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>\
                      </div>\
                  </div>';

    $(".remove_btn:first").show();
    $(".add_btn:last").hide();
    $("#countdocuments" + (rowcount - 1)).after(datahtml);

    $('#fromdate' + rowcount + ',#duedate' + rowcount).datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        todayBtn: "linked",
        orientation: "top left",
        clearBtn: true,
    });

    $(".selectpicker").selectpicker("refresh");
}

function removeDocument(rowid) {

    $("#countdocuments" + rowid).remove();

    $(".add_btn:last").show();
    if ($(".remove_btn:visible").length == 1) {
        $(".remove_btn:first").hide();
    }
}

function generateinstallment(type = 0) {

    $("#installmentdivs").html("");
    totalvalue = $("#installmentTotalamount").val();
    noofinstallmentval = $("#noofinstallment").val();
    noofinstallmentdiv = $(".noofinstallmentdiv").length;
    emidate = $("#emidate").val();
    // emiduration = $("#emiduration").val();
    $("#installmentmaindivheading1").hide();
    $("#installmentmaindivheading2").hide();

    if (totalvalue == '' || noofinstallmentval == "" || noofinstallmentval == "0" || emidate == ""/*  || emiduration == "" || emiduration == "0" */) {


        if (totalvalue == "" || totalvalue == "0") {
            $("#installmentTotalamount_div").addClass("has-error is-focused");
            new PNotify({title: 'Please enter EMI amount !',styling: 'fontawesome',delay: '3000',type: 'error'});
        } else {
            $("#installmentTotalamount_div").removeClass("has-error is-focused");
        }
        if (noofinstallmentval == "" || noofinstallmentval == "0") {
            $("#noofinstallment_div").addClass("has-error is-focused");
            new PNotify({title: 'Please enter no. of installment !',styling: 'fontawesome',delay: '3000',type: 'error'});
        } else {
            $("#noofinstallment_div").removeClass("has-error is-focused");
        }
        if (emidate == "") {
            $("#emidate_div").addClass("has-error is-focused");
            new PNotify({title: 'Please enter EMI start date !',styling: 'fontawesome',delay: '3000',type: 'error'});
        } else {
            $("#emidate_div").removeClass("has-error is-focused");
        }
        if (emiduration == "" || emiduration == "0") {
            $("#emiduration_div").addClass("has-error is-focused");
                new PNotify({title: 'Please enter EMI duration !',styling: 'fontawesome',delay: '3000',type: 'error'});
        } else {
            $("#emiduration_div").removeClass("has-error is-focused");
        }
        return false;
    }

    if (parseFloat(totalvalue) > 0) {
        installmentamount = (parseFloat(totalvalue) / parseFloat(noofinstallmentval)).toFixed(2);

        $("#installmentmaindivheading1").show();
        if(noofinstallmentval>1){
            $("#installmentmaindivheading2").show();
        }

        var datearray = emidate.split("/");
        var emidate = new Date(datearray[2] + "-" + datearray[1] + "-" + datearray[0]);
        emidurationval = 0;
        amounttotal = 0;
        $('#installmentmaindiv').find(".noofinstallmentdiv").slice(noofinstallmentval, noofinstallmentdiv).remove();
        for (var i = 0; i <= noofinstallmentval - 1; i++) {

            if (emidurationval == 0) {
                emidate.setDate(emidate.getDate());
            } else {
                emidate.setMonth(emidate.getMonth() +1/*  parseInt(emiduration) */);
            }
            if (i == noofinstallmentval - 1) {
                installmentamount = (parseFloat(totalvalue) - parseFloat(amounttotal)).toFixed(2);
            }
            amounttotal = parseFloat(amounttotal) + parseFloat(installmentamount);
            emidurationval = 1;
            var dd = String(emidate.getDate()).padStart(2, '0');
            var mm = String(emidate.getMonth()+1).padStart(2, '0');
            var yy = emidate.getFullYear();
            installmentdate = dd + "/" + mm + "/" + yy;

            $("#installmentdivs").append('<div class="col-md-6 col-xs-12 noofinstallmentdiv">\
                <div class="col-md-2 col-xs-2 text-center"><div class="form-group"><div class="col-sm-12 pt-sm">' + (i + 1) + ' </div></div></div>\
                <div class="col-md-4 col-xs-4 text-center">\
                    <div class="form-group">\
                        <div class="col-sm-12 pr-sm pl-sm">\
                            <input type="text" id="installmentamount' + (i + 1) + '" value="' + installmentamount + '" name="installmentamount[]" class="form-control text-right installmentamount" div-id="' + (i + 1) + '" maxlength="5" onkeypress="return decimal(event,this.id);" readonly>\
                        </div>\
                    </div>\
                </div>\
                <div class="col-md-6 col-xs-6 text-center">\
                    <div class="form-group">\
                        <div class="col-sm-12 pr-sm pl-sm">\
                            <div class="input-group">\
                            <input type="text" id="installmentdate' + (i + 1) + '" value="' + installmentdate + '" name="installmentdate[]" class="form-control" div-id="' + (i + 1) + '" maxlength="5">\
                                <span class="btn btn-default add-on datepicker_calendar_button" title="Date"><i class="fa fa-calendar fa-lg"></i></span>\
                            </div>\
                        </div>\
                    </div>\
                </div>\
            </div>');

            $('#installmentdate' + (i + 1)).datepicker({
                todayHighlight: true,
                format: 'dd/mm/yyyy',
                autoclose: true,
                todayBtn: "linked",
            });

        }
    } else {
        $('#installmentdivs').find(".noofinstallmentdiv").remove();
        $('#installmentmaindiv').html("");
        if (type == 1) {
            new PNotify({
                title: 'Please enter amount !',
                styling: 'fontawesome',
                delay: '3000',
                type: 'error'
            });
        }
    }
}


function resetdata() {

    $("#vehiclename_div").removeClass("has-error is-focused");
    $("#engineno_div").removeClass("has-error is-focused");
    $("#vehicleno_div").removeClass("has-error is-focused");
    $("#chassisno_div").removeClass("has-error is-focused");
    $("#company_div").removeClass("has-error is-focused");
    $("#owner_div").removeClass("has-error is-focused");
    $("#vehicletype_div").removeClass("has-error is-focused");
    $("#dateofpurchase_div").removeClass("has-error is-focused");
    $("#address_div").removeClass("has-error is-focused");
    $("#fueltype_div").removeClass("has-error is-focused");
    $("#buyer_div").removeClass("has-error is-focused");

    if (ACTION == 1) {
        $('#vehiclename').focus();
        $('#vehiclename_div').addClass('is-focused');

    } else {
        $("#vehiclename").val('').focus();
        $('#vehiclename_div').addClass('is-focused');

        $("#engineno,#vehicleno,#chassisno,#dateofpurchase,#solddate").val('');
        $("#ownerpartyid,#vehicletype,#commercial,#soldpartyid,#fueltype,#buyer").val('0');
        $("#companyid").select2("val", "0");
        $("#s2id_companyid > a").css({
            "background-color": "#FFF",
            "border": "1px solid #D2D2D2"
        });
        $('.yesno input[type="checkbox"]').bootstrapToggle('off');
        $('#solddate_div,#soldparty_div').hide();

        $(".countdocuments:not(:first)").remove();
        var divid = parseInt($(".countdocuments:first").attr("id").match(/\d+/));

        $('#documenttypeid' + divid + ',#licencetype' + divid + ',#isvaliddocfile' + divid).val("0");
        $('#documentnumber' + divid + ',#fromdate' + divid + ',#duedate' + divid + ',#olddocfile' + divid + ',#Filetextdocfile' + divid).val("");

        $('#documenttype' + divid + '_div').removeClass("has-error is-focused");
        $('#documentnumber' + divid + '_div').removeClass("has-error is-focused");
        $('#docfile' + divid + '_div').removeClass("has-error is-focused");
        $("#duedate" + divid + "_div").removeClass("has-error is-focused");

        $(".countinsurances:not(:first)").remove();
        var insuranceid = parseInt($(".countinsurances:first").attr("id").match(/\d+/));

        $('#insurancecompanyname' + insuranceid + ',#insurancefromdate' + insuranceid + ',#insurancetodate' + insuranceid + ',#policyno' + insuranceid + ',#amount' + insuranceid + ',#paymentdate' + insuranceid).val("");
        $('#s2id_insurancecompanyname' + insuranceid + ' > a').css({
            "background-color": "#FFF",
            "border": "1px solid #D2D2D2"
        });
        $('#insurancedate_div_' + insuranceid).removeClass("has-error is-focused");
        $('#policyno_div_' + insuranceid).removeClass("has-error is-focused");
        $('#amount_div_' + insuranceid).removeClass("has-error is-focused");
        $('#insurancecompanyname' + insuranceid).select2("val", "");

        $(".countchallan:not(:first)").remove();
        var challanid = parseInt($(".countchallan:first").attr("id").match(/\d+/));

        $('#challanfor' + challanid + ',#challantype' + challanid).val("0");
        $('#challandate' + divid + ',#challanamount' + divid + ',#challanFiletext' + divid + ',#challanremarks' + divid).val("");

        $('#accountno_div').removeClass("has-error is-focused");
        $('#wallerid_div').removeClass("has-error is-focused");
        $('#rfidno_div').removeClass("has-error is-focused");

        $("#accountno,#walletid,#rfidno").val('');

        $('.add_btn:first').show();
        $('.remove_btn').hide();
        $('.add_btn_insurance:first').show();
        $('.remove_btn_insurance').hide();
        $('.add_btn_challan:first').show();
        $('.remove_btn_challan').hide();
        $("#yes").prop("checked", true);
        $(".selectpicker").selectpicker("refresh");
    }
    $('html, body').animate({
        scrollTop: 0
    }, 'slow');
}

function checkvalidation(addtype = 0) {

    var vehiclename = $("#vehiclename").val().trim();
    var engineno = $("#engineno").val().trim();
    var vehicleno = $("#vehicleno").val().trim();
    var chassisno = $("#chassisno").val().trim();
    var ownerpartyid = $("#ownerpartyid").val();
    var vehicletype = $("#vehicletype").val();
    var soldpartyid = $("#soldpartyid").val();
    var solddate = $("#solddate").val();
    var remarks = $("#remarks").val().trim();
    var fueltype = $("#fueltype").val().trim();
    var buyer = $("#buyer").val().trim();
    var dateofregistration = $("#dateofregistration").val().trim();
    var duedateofregistration = $("#duedateofregistration").val().trim();

    var isvalidvehiclename = isvalidengineno = isvalidvehicleno = isvalidchassisno = isvalidownerpartyid = isvalidvehicletype = isvalidremarks = isvalidfueltype = isvalidbuyer = isvalidduedateofregistration = 0;
    var isvaliddocumenttypeid = isvaliddocumentnumber = isvalidfromdate = isvalidduedate = isvalidsolddate = isvalidsoldpartyid = isvalidinsurancecompany = isvalidinsurancefromdate = isvalidinsurancetodate = isvalidinsuranceamount = isvalidinsurancepolicyno = isvalidchallanfor = isvalidchallantype = isvalidchallandate = isvalidchallanamount = isvalidduedate = isvalidaccountno = isvalidwalletid = isvalidrfidno = 1;

    PNotify.removeAll();
    if (duedateofregistration != "" && dateofregistration != "") {
        var regDate = dateofregistration.split("/");
        regDate = new Date(regDate[2], regDate[1] - 1, regDate[0]);
        var rd = String(regDate.getDate()).padStart(2, '0');
        var rm = String(regDate.getMonth() + 1).padStart(2, '0'); //January is 0!
        var ry = regDate.getFullYear();
        regDate = ry + "-" + rm + "-" + rd;

        var dueDate = duedateofregistration.split("/");
        dueDate = new Date(dueDate[2], dueDate[1] - 1, dueDate[0]);
        var dd = String(dueDate.getDate()).padStart(2, '0');
        var dm = String(dueDate.getMonth() + 1).padStart(2, '0'); //January is 0!
        var dy = dueDate.getFullYear();
        dueDate = dy + "-" + dm + "-" + dd;

        if (dueDate < regDate) {
            $("#duedateofregistration_div").addClass("has-error is-focused");
            new PNotify({title: 'Please select due date of reg. is greater than of date of reg. !',styling: 'fontawesome',delay: '3000',type: 'error'
            });
        } else {
            isvalidduedateofregistration = 1;
            $("#duedateofregistration_div").removeClass("has-error is-focused");
        }
    } else {
        isvalidduedateofregistration = 1;
        $("#duedate_div").removeClass("has-error is-focused");
    }
    if (vehiclename == '') {
        $("#vehiclename_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter vehicle name !',styling: 'fontawesome',delay: '3000',type: 'error'
        });
    } else if (vehiclename.length < 2) {
        $("#vehiclename_div").addClass("has-error is-focused");
        new PNotify({title: 'Vehicle name require minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'
        });
    } else {
        $("#vehiclename_div").removeClass("has-error is-focused");
        isvalidvehiclename = 1;
    }

    if (engineno == '') {
        $("#engineno_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter engine number !',styling: 'fontawesome',delay: '3000',type: 'error'
        });
    } else if (engineno.length < 2) {
        $("#engineno_div").addClass("has-error is-focused");
        new PNotify({title: 'Engine number require minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'
        });
    } else {
        $("#engineno_div").removeClass("has-error is-focused");
        isvalidengineno = 1;
    }

    if (vehicleno == '') {
        $("#vehicleno_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter vehicle number !',styling: 'fontawesome',delay: '3000',type: 'error'
        });
    } else if (vehicleno.length < 2) {
        $("#vehicleno_div").addClass("has-error is-focused");
        new PNotify({title: 'Vehicle number require minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'
        });
    } else {
        $("#vehicleno_div").removeClass("has-error is-focused");
        isvalidvehicleno = 1;
    }

    if (chassisno == '') {
        $("#chassisno_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter chassis number !',styling: 'fontawesome',delay: '3000',type: 'error'
        });
    } else if (chassisno.length < 2) {
        $("#chassisno_div").addClass("has-error is-focused");
        new PNotify({title: 'Chassis number require 10 characters !',styling: 'fontawesome',delay: '3000',type: 'error'
        });
    } else {
        $("#chassisno_div").removeClass("has-error is-focused");
        isvalidchassisno = 1;
    }

    if (ownerpartyid == 0) {
        $("#owner_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select owner !',styling: 'fontawesome',delay: '3000',type: 'error'
        });
    } else {
        $("#owner_div").removeClass("has-error is-focused");
        isvalidownerpartyid = 1;
    }

    if (vehicletype == 0) {
        $("#vehicletype_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select vehicle type !',styling: 'fontawesome',delay: '3000',type: 'error'
        });
    } else {
        $("#vehicletype_div").removeClass("has-error is-focused");
        isvalidvehicletype = 1;
    }

    if (fueltype == 0) {
        $("#fueltype_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select fuel type !',styling: 'fontawesome',delay: '3000',type: 'error'
        });
    } else {
        $("#fueltype_div").removeClass("has-error is-focused");
        isvalidfueltype = 1;
    }

    if (buyer == 0) {
        $("#buyer_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select purchase company !',styling: 'fontawesome',delay: '3000',type: 'error'
        });
    } else {
        $("#buyer_div").removeClass("has-error is-focused");
        isvalidbuyer = 1;
    }

    if ($('input[name="sold"]').is(":checked")) {
        if (solddate == '') {
            $("#solddate_div").addClass("has-error is-focused");
            new PNotify({title: 'Please select sold date !',styling: 'fontawesome',delay: '3000',type: 'error'
            });
            isvalidsolddate = 0;
        } else {
            $("#solddate_div").removeClass("has-error is-focused");
        }

        if (soldpartyid == 0) {
            $("#soldparty_div").addClass("has-error is-focused");
            new PNotify({title: 'Please select sold party !',styling: 'fontawesome',delay: '3000',type: 'error'
            });
            isvalidsoldpartyid = 0;
        } else {
            $("#soldparty_div").removeClass("has-error is-focused");
        }
    }

    if (remarks != '' && remarks.length < 2) {
        $("#remarks_div").addClass("has-error is-focused");
        new PNotify({title: 'Remarks require minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'
        });
    } else {
        $("#remarks_div").removeClass("has-error is-focused");
        isvalidremarks = 1;
    }

    var c = 1;
    // var firstdocid = $('.countdocuments:first').attr('id').match(/\d+/);
    $('.countdocuments').each(function () {
        var id = $(this).attr('id').match(/\d+/);

        if ($("#documenttypeid" + id).val() > 0 || $("#documentnumber" + id).val() != "" || $("#fromdate" + id).val() != "" || $("#duedate" + id).val() != "") {

            if ($("#documenttypeid" + id).val() == 0) {
                $("#documenttype" + id + "_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select ' + (c) + ' document type !',styling: 'fontawesome',delay: '3000',type: 'error'
                });
                isvaliddocumenttypeid = 0;
            } else {
                $("#documenttype" + id + "_div").removeClass("has-error is-focused");
            }
            if ($("#documentnumber" + id).val() == "") {
                $("#documentnumber" + id + "_div").addClass("has-error is-focused");
                new PNotify({title: 'Please enter ' + (c) + ' document number !',styling: 'fontawesome',delay: '3000',type: 'error'
                });
                isvaliddocumentnumber = 0;
            } else {
                $("#documentnumber" + id + "_div").removeClass("has-error is-focused");
            }

            if ($("#fromdate" + id).val() == "") {
                $("#fromdate" + id + "_div").addClass("has-error is-focused");
                new PNotify({title: 'Please enter ' + (c) + ' register date !',styling: 'fontawesome',delay: '3000',type: 'error'
                });
                isvalidfromdate = 0;
            } else {
                $("#fromdate" + id + "_div").removeClass("has-error is-focused");
            }

            if ($("#duedate" + id).val() == "") {
                $("#duedate" + id + "_div").addClass("has-error is-focused");
                new PNotify({title: 'Please enter ' + (c) + ' due date !',styling: 'fontawesome',delay: '3000',type: 'error'
                });
                isvalidduedate = 0;
            } else {
                $("#duedate" + id + "_div").removeClass("has-error is-focused");
            }

            if ($("#duedate" + id).val() != "" && $("#fromdate" + id).val() != "") {
                var RegDate = $("#fromdate" + id).val().split("/");
                RegDate = new Date(RegDate[2], RegDate[1] - 1, RegDate[0]);
                var rdd = String(RegDate.getDate()).padStart(2, '0');
                var rmm = String(RegDate.getMonth() + 1).padStart(2, '0'); //January is 0!
                var ryyyy = RegDate.getFullYear();
                RegDate = ryyyy + "-" + rmm + "-" + rdd;

                var DueDate = $("#duedate" + id).val().split("/");
                DueDate = new Date(DueDate[2], DueDate[1] - 1, DueDate[0]);
                var dd = String(DueDate.getDate()).padStart(2, '0');
                var mm = String(DueDate.getMonth() + 1).padStart(2, '0'); //January is 0!
                var yyyy = DueDate.getFullYear();
                DueDate = yyyy + "-" + mm + "-" + dd;

                if (DueDate < RegDate) {
                    $("#duedate" + id + "_div").addClass("has-error is-focused");
                    new PNotify({title: 'Please select ' + (c) + ' due date greater than of register date !',styling: 'fontawesome',delay: '3000',type: 'error'
                    });
                    isvalidduedate = 0;
                } else {
                    $("#duedate" + id + "_div").removeClass("has-error is-focused");
                }
            }
        } else {
            $("#documenttype" + id + "_div").removeClass("has-error is-focused");
            $("#documentnumber" + id + "_div").removeClass("has-error is-focused");
            $("#duedate" + id + "_div").removeClass("has-error is-focused");
        }
        c++;
    });

    var count = 1;
    $('.countinsurances').each(function () {
        var id = $(this).attr('id').match(/\d+/);
        var companyname = $('#insurancecompanyname' + id).val().trim();
        var insurancefromdate = $('#insurancefromdate' + id).val().trim();
        var insurancetodate = $('#insurancetodate' + id).val().trim();
        var insuranceamount = $('#amount' + id).val().trim();
        var insurancepolicyno = $('#policyno' + id).val().trim();

        if (companyname != "" || insurancefromdate != '' || insurancetodate != '' || insuranceamount != '' || insurancepolicyno != '') {
            if (companyname == "") {
                $('#insurancecompanyname_div_' + id).addClass("has-error is-focused");
                $('#s2id_insurancecompanyname' + id + ' > a').css({
                    "background-color": "#FFECED",
                    "border": "1px solid #e51c23"
                });
                new PNotify({title: 'Please select ' + (count) + ' insurance company !',styling: 'fontawesome',delay: '3000',type: 'error'
                });
                isvalidinsurancecompany = 0;
            } else {
                $('#insurancecompanyname_div_' + id).removeClass("has-error is-focused");
                $('#s2id_insurancecompanyname' + id + ' > a').css({
                    "background-color": "#FFF",
                    "border": "1px solid #D2D2D2"
                });
            }
            if (insurancefromdate == "") {
                $('#insurancedate_div_' + id).addClass("has-error is-focused");
                new PNotify({title: 'Please select ' + (count) + ' insurance register date !',styling: 'fontawesome',delay: '3000',type: 'error'
                });
                isvalidinsurancefromdate = 0;
            } else {
                $('#insurancedate_div_' + id).removeClass("has-error is-focused");
            }
            if (insurancetodate == "") {
                $('#insurancedate_div_' + id).addClass("has-error is-focused");
                new PNotify({title: 'Please select ' + (count) + ' insurance due date !',styling: 'fontawesome',delay: '3000',type: 'error'
                });
                isvalidinsurancetodate = 0;
            } else {
                $('#insurancedate_div_' + id).removeClass("has-error is-focused");
            }

            if (insuranceamount == "") {
                $('#amount_div_' + id).addClass("has-error is-focused");
                new PNotify({title: 'Please enter ' + (count) + ' insurance amount !',styling: 'fontawesome',delay: '3000',type: 'error'
                });
                isvalidinsuranceamount = 0;
            } else {
                $('#amount_div_' + id).removeClass("has-error is-focused");
            }
            if (insurancepolicyno == "") {
                $('#policyno_div_' + id).addClass("has-error is-focused");
                new PNotify({title: 'Please select ' + (count) + ' insurance policy number !',styling: 'fontawesome',delay: '3000',type: 'error'
                });
                isvalidinsurancepolicyno = 0;
            } else {
                $('#policyno_div_' + id).removeClass("has-error is-focused");
            }
        } else {
            $('#s2id_insurancecompanyname' + id + ' > a').css({
                "background-color": "#FFF",
                "border": "1px solid #D2D2D2"
            });
            $('#insurancedate_div_' + id).removeClass("has-error is-focused");
            $('#policyno_div_' + id).removeClass("has-error is-focused");
            $('#amount_div_' + id).removeClass("has-error is-focused");
        }
        count++;
    });

    var rowcount = 1;
    $('.countchallan').each(function () {
        var id = $(this).attr('id').match(/\d+/);
        var challanfor = $('#challanfor' + id).val();
        var challantype = $('#challantype' + id).val();
        var challandate = $('#challandate' + id).val().trim();
        var challanamount = $('#challanamount' + id).val().trim();
        if (challanfor != 0 || challantype != 0 || challandate != '' || challanamount != 0) {
            if (challanfor == 0) {
                $('#challanfor' + id + '_div').addClass("has-error is-focused");
                new PNotify({title: 'Please select ' + (rowcount) + ' driver !',styling: 'fontawesome',delay: '3000',type: 'error'
                });
                isvalidchallanfor = 0;
            } else {
                $('#challanfor' + id + '_div').removeClass("has-error is-focused");
            }
            if (challantype == 0) {
                $('#challantype' + id + '_div').addClass("has-error is-focused");
                new PNotify({
                    title: 'Please select ' + (rowcount) + ' challan type !',
                    styling: 'fontawesome',
                    delay: '3000',
                    type: 'error'
                });
                isvalidchallantype = 0;
            } else {
                $('#challantype' + id + '_div').removeClass("has-error is-focused");
            }
            if (challandate == '') {
                $('#challandate' + id + '_div').addClass("has-error is-focused");
                new PNotify({title: 'Please select ' + (rowcount) + ' challan date !',styling: 'fontawesome',delay: '3000',type: 'error'
                });
                isvalidchallandate = 0;
            } else {
                $('#challandate' + id + '_div').removeClass("has-error is-focused");
            }
            if (challanamount <= 0) {
                $('#challanamount' + id + '_div').addClass("has-error is-focused");
                new PNotify({title: 'Please enter ' + (rowcount) + ' challan amount !',styling: 'fontawesome',delay: '3000',type: 'error'
                });
                isvalidchallanamount = 0;
            } else {
                $('#challanamount' + id + '_div').removeClass("has-error is-focused");
            }
        } else {
            $('#challanfor' + id + '_div').removeClass("has-error is-focused");
            $('#challantype' + id + '_div').removeClass("has-error is-focused");
            $('#challanamount' + id + '_div').removeClass("has-error is-focused");
            $('#challandate' + id + '_div').removeClass("has-error is-focused");
        }
        rowcount++;
    });

    var accountno = $('#accountno').val();
    var walletid = $('#walletid').val();
    var rfidno = $('#rfidno').val();

    if(accountno != '' || walletid != '' || rfidno != ''){
        if(accountno == ''){
            $("#accountno_div").addClass("has-error is-focused");
            new PNotify({title: 'Please enter account number !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidaccountno = 0;
        } else {
            if(accountno.length != 12){
                $("#accountno_div").addClass("has-error is-focused");
                new PNotify({title: 'Account number require 12 digits!',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidaccountno = 0;
            } else {
                $("#accountno_div").removeClass("has-error is-focused");
                isvalidaccountno = 1;
            }
        }

        if(walletid == ''){
            $("#wallerid_div").addClass("has-error is-focused");
            new PNotify({title: 'Please enter wallet id !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidwalletid = 0;
        } else {
            if(walletid.length != 14){
                $("#wallerid_div").addClass("has-error is-focused");
                new PNotify({title: 'Wallet id require 14 digits!',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidwalletid = 0;
            } else {
                $("#wallerid_div").removeClass("has-error is-focused");
                isvalidwalletid = 1;
            }
        }

        if(rfidno == ''){
            $("#rfidno_div").addClass("has-error is-focused");
            new PNotify({title: 'Please enter rfid number !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidrfidno = 0;
        } else {
            if(rfidno.length != 16){
                $("#rfidno_div").addClass("has-error is-focused");
                new PNotify({title: 'RFID number require 16 digits!',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidrfidno = 0;
            } else {
                $("#rfidno_div").removeClass("has-error is-focused");
                isvalidrfidno = 1;
            }
        }
    }

    if (isvalidvehiclename == 1 && isvalidengineno == 1 && isvalidvehicleno == 1 && isvalidchassisno == 1 && isvalidownerpartyid == 1 && isvalidvehicletype == 1 && isvalidsoldpartyid == 1 && isvalidsolddate == 1 && isvalidremarks == 1 && isvaliddocumenttypeid == 1 && isvaliddocumentnumber == 1 && isvalidbuyer == 1 && isvalidfueltype == 1 && isvalidduedate == 1 && isvalidduedateofregistration == 1 && isvalidchallanfor == 1 && isvalidchallantype == 1 && isvalidchallandate == 1 && isvalidchallanamount == 1 && isvalidinsurancecompany == 1 && isvalidinsurancefromdate == 1 && isvalidinsuranceamount == 1 && isvalidinsurancetodate == 1 && isvalidinsurancepolicyno == 1 && isvalidaccountno == 1 && isvalidwalletid == 1 && isvalidrfidno == 1) {

        var formData = new FormData($('#vehicle-form')[0]);
        if (ACTION == 0) {
            var uurl = SITE_URL + "vehicle/vehicle-add";

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
                    var data = JSON.parse(response);
                    $("#vehicleno_div").removeClass("has-error is-focused");
                    if (data['error'] == 1) {
                        new PNotify({title: "Vehicle successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'
                        });
                        if (addtype == 1) {
                            resetdata();
                        } else {
                            setTimeout(function () {
                                window.location = SITE_URL + "vehicle";
                            }, 1500);
                        }
                    } else if (data['error'] == 2) {
                        new PNotify({title: "Vehicle number already exists !",styling: 'fontawesome',delay: '3000',type: 'error'
                        });
                        $("#vehicleno_div").addClass("has-error is-focused");
                    } else if (data['error'] == -2) {
                        new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'
                        });
                        $("#" + data['id'] + "_div").addClass("has-error is-focused");
                    } else if (data['error'] == -1) {
                        new PNotify({title: 'File is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE) + ') !',styling: 'fontawesome',delay: '3000',type: 'error'
                        });
                        $("#" + data['id'] + "_div").addClass("has-error is-focused");
                    } else {
                        new PNotify({title: 'Vehicle not added !',styling: 'fontawesome',delay: '3000',type: 'error'
                        });
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

            var uurl = SITE_URL + "vehicle/update-vehicle";

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
                    var data = JSON.parse(response);
                    $("#vehicleno_div").removeClass("has-error is-focused");
                    if (data['error'] == 1) {
                        new PNotify({title: "Vehicle successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'
                        });
                        if (addtype == 1) {
                            setTimeout(function () {
                                window.location = SITE_URL + "vehicle/add-vehicle";
                            }, 1500);
                        } else {
                            setTimeout(function () {
                                window.location = SITE_URL + "vehicle";
                            }, 1500);
                        }
                    } else if (data['error'] == 2) {
                        new PNotify({title: "Vehicle number already exists !",styling: 'fontawesome',delay: '3000',type: 'error'
                        });
                        $("#vehicleno_div").addClass("has-error is-focused");
                    } else if (data['error'] == -2) {
                        new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'
                        });
                        $("#" + data['id'] + "_div").addClass("has-error is-focused");
                    } else if (data['error'] == -1) {
                        new PNotify({title: 'File is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE) + ') !',styling: 'fontawesome',delay: '3000',type: 'error'
                        });
                        $("#" + data['id'] + "_div").addClass("has-error is-focused");
                    } else {
                        new PNotify({title: 'Vehicle not updated !',styling: 'fontawesome',delay: '3000',type: 'error'
                        });
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

function addInsuranceRow() {
    var rowinsurancecount = parseInt($(".countinsurances:last").attr("id").match(/\d+/)) + 1;

    var html = '<div class="col-md-12 col-sm-12 countinsurances " id="countinsurance' + rowinsurancecount + '">\
                <div class="col-md-3 col-sm-4">\
                    <div class="form-group" id="insurancecompanyname_div_' + rowinsurancecount + '">\
                        <div class="col-md-12 pr-xs pl-xs">\
                          <label for="insurancecompanyname' + rowinsurancecount + '" class="control-label">Insurance Company <span class="mandatoryfield">*</span></label>\
                            <input id="insurancecompanyname' + rowinsurancecount + '"  type="text" name="insurancecompanyname[' + rowinsurancecount + ']" data-url="' + SearchInsuranceUrl + '" placeholder="Select Insurance Company" data-provide="companyname" class="form-control insurancecompany">\
                        </div>\
                    </div>\
                </div>\
                <div class="col-md-3 col-sm-4">\
                    <div class="form-group" id="insuranceagent_div_' + rowinsurancecount + '">\
                        <div class="col-md-12 pr-xs pl-xs">\
                            <label for="insuranceagent' + rowinsurancecount + '" class="control-label">Insurance Agent</label>\
                            <select name="insuranceagent[' + rowinsurancecount + ']" id="insuranceagent' + rowinsurancecount + '" data-live-search="true" class="selectpicker form-control">\
                                <option value="0">Select Insurance Agent</option>\
                            </select>\
                        </div>\
                    </div>\
                </div>\
                <div class="col-md-2 col-sm-4">\
                    <div class="form-group" id="policyno_div_' + rowinsurancecount + '">\
                        <div class="col-md-12 pr-xs pl-xs">\
                        <label for="policyno' + rowinsurancecount + '" class="control-label">Policy No. <span class="mandatoryfield">*</span></label>\
                            <input id="policyno' + rowinsurancecount + '" type="text" name="policyno[' + rowinsurancecount + ']" class="form-control insurancerow">\
                        </div>\
                    </div>\
                </div>\
                <div class="col-md-2 col-sm-4">\
                    <div class="form-group text-right" id="amount_div_' + rowinsurancecount + '">\
                        <div class="col-md-12 pr-xs pl-xs">\
                        <label for="amount' + rowinsurancecount + '" class="control-label">Amount (' + CURRENCY_CODE + ') <span class="mandatoryfield">*</span></label>\
                            <input id="amount' + rowinsurancecount + '" type="text"  name="amount[' + rowinsurancecount + ']" class="form-control insurancerow text-right" onkeypress="return decimal_number_validation(event, this.value, 8)">\
                        </div>\
                    </div>\
                </div>\
                <div class="col-md-2 col-sm-4">\
                    <div class="form-group" id="paymentdate_div_' + rowinsurancecount + '">\
                        <div class="col-md-12 pr-xs pl-xs">\
                        <label for="paymentdate' + rowinsurancecount + '" class="control-label">Payment Date</label>\
                        <div class="input-group">\
                        <input type="text" class="input-small form-control insurancerow" name="paymentdate[]" id="paymentdate' + rowinsurancecount + '" style="text-align: left;" readonly/>\
                                  <span class="btn btn-default add-on datepicker_calendar_button" title="Date"><i class="fa fa-calendar fa-lg"></i></span>\
                              </div>\
                        </div>\
                    </div>\
                </div>\
                <div class="col-md-3 col-sm-4">\
                    <div class="form-group" id="insurancefile' + rowinsurancecount + '_div">\
                        <div class="col-md-12 pr-xs pl-xs">\
                            <label class="control-label">Proof</label>\
                            <div class="input-group" id="fileupload' + rowinsurancecount + '">\
                                <span class="input-group-btn" style="padding: 0 0px 0px 0px;">\
                                <span class="btn btn-primary btn-raised btn-file"><i class="fa fa-upload"></i>\
                                        <input type="file" name="fileproof' + rowinsurancecount + '"  id="fileproof' + rowinsurancecount + '" accept=".jpe,.jpeg,.jpg,.pbm,.png,.pdf" onchange="validInsurancefile($(this),\'insurancefile' + rowinsurancecount + '\',this)">\
                                    </span>\
                                </span>\
                                <input type="text" id="textfile' + rowinsurancecount + '" class="form-control"  name="textfile[]" readonly >\
                            </div> \
                        </div>\
                    </div> \
                </div> \
                <div class="col-md-4 col-sm-8">\
                    <div class="form-group" id="insurancedate_div_' + rowinsurancecount + '">\
                        <div class="input-daterange input-group datepicker-range" id="datepicker-range' + rowinsurancecount + '">\
                            <div class="col-md-6 col-sm-6 pr-xs pl-xs">\
                            <label class="control-label" for="insurancefromdate' + rowinsurancecount + '" style="text-align: left;">Register Date <span class="mandatoryfield">*</span></label>\
                                <div class="input-group">\
                                    <input type="text" class="input-small form-control insurancerow insurancedate" id="insurancefromdate' + rowinsurancecount + '" style="text-align: left;" name="insurancefromdate[' + rowinsurancecount + ']" readonly/>\
                                      <span class="btn btn-default add-on datepicker_calendar_button" title="Date"><i class="fa fa-calendar fa-lg"></i></span>\
                                  </div>\
                            </div>\
                            <div class="col-md-6 col-sm-6 pr-xs pl-xs">\
                            <label class="control-label" for="insurancetodate' + rowinsurancecount + '" style="text-align: left;">Due Date <span class="mandatoryfield">*</span></label>\
                            <div class="input-group">\
                            <input type="text" class="input-small form-control insurancerow insurancedate" id="insurancetodate' + rowinsurancecount + '" style="text-align: left;" name="insurancetodate[' + rowinsurancecount + ']" readonly/>\
                                      <span class="btn btn-default add-on datepicker_calendar_button" title="Date"><i class="fa fa-calendar fa-lg"></i></span>\
                                  </div>\
                            </div>\
                        </div>\
                    </div>\
                </div>\
                <div class="col-md-3 col-sm-4 pt-md pr-xs addrowbutton add_Insurance_row_button_div">\
                    <button type="button" class="btn btn-danger btn-raised remove_btn_insurance m-n" onclick="removeInsuranceRow(' + rowinsurancecount + ')" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>\
                    <button type="button" class="btn btn-primary btn-raised add_btn_insurance m-n" onclick="addInsuranceRow()"  style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>\
                </div>\
                <div class="col-md-12 col-xs-12 col-sm-12 Insurance-hr p-n"><hr></div>\
              </div>';

    $(".remove_btn_insurance:first").show();
    $(".add_btn_insurance:last").hide();
    $("#countinsurance" + (rowinsurancecount - 1)).after(html);
    $("#insuranceagent" + rowinsurancecount).selectpicker("refresh");

    $(".Insurance-hr").show();
    $(".Insurance-hr:last").hide();

    $('.datepicker-range').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy'
    });

    $('#paymentdate' + rowinsurancecount).datepicker({
        todayHighlight: true,
        todayBtn: "linked",
        clearBtn: true,
        format: 'dd/mm/yyyy',
    });
    $("[data-provide='companyname']").each(function () {
        var $element = $(this);

        $element.select2({
            allowClear: true,
            minimumInputLength: 3,
            width: '100%',
            placeholder: $element.attr("placeholder"),
            createSearchChoice: function (term, data) {
                if ($(data).filter(function () {
                        return this.text.localeCompare(term) === 0;
                    }).length === 0) {
                    return {
                        id: term,
                        text: term
                    };
                }
            },
            ajax: {
                url: $element.data("url"),
                dataType: 'json',
                type: "POST",
                quietMillis: 50,
                data: function (term) {
                    return {
                        term: term,
                    };
                    html
                },
                results: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.text,
                                id: item.text
                            }
                        })
                    };
                }
            },
            initSelection: function (element, callback) {
                var id = $(element).val();
                if (id !== "" && id != 0) {
                    $.ajax($element.data("url"), {
                        data: {
                            ids: id,
                        },
                        type: "POST",
                        dataType: "json",
                    }).done(function (data) {
                        callback(data);
                    });
                }
            }
        });
    });
}

function removeInsuranceRow(rowid) {
    $("#countinsurance" + rowid).remove();

    $(".Insurance-hr").show();
    $(".Insurance-hr:last").hide();

    $(".add_btn_insurance:last").show();
    if ($(".remove_btn_insurance:visible").length == 1) {
        $(".remove_btn_insurance:first").hide();
    }
}

function addChallanRow() {
    var challanrowcount = parseInt($(".countchallan:last").attr("id").match(/\d+/)) + 1;

    var challanhtml = '<div class="col-md-12 col-sm-12 countchallan" id="countchallan' + challanrowcount + '">\
                      <div class="col-md-2 col-sm-4">\
                          <div class="form-group mt-n" id="challanfor' + challanrowcount + '_div">\
                              <div class="col-md-12 pr-xs pl-xs">\
                                  <select id="challanfor' + challanrowcount + '" name="challanfor[' + challanrowcount + ']" class="selectpicker form-control" show-data-subtext="on" data-live-search="true" data-size="5">\
                                      <option value="0">Select Driver</option>\
                                      ' + challanfordata + '\
                                  </select>\
                              </div>\
                          </div>\
                      </div>\
                      <div class="col-md-2 col-sm-4">\
                          <div class="form-group mt-n" id="challantype' + challanrowcount + '_div">\
                              <div class="col-md-12 pr-xs pl-xs">\
                                  <select class="selectpicker form-control challantype" id="challantype' + challanrowcount + '" name="challantype[' + challanrowcount + ']" data-live-search="true">\
                                      <option value="0">Select Challan Type</option>\
                                      ' + challantypedata + '\
                                  </select>\
                              </div>\
                          </div>\
                      </div>\
                      <div class="col-md-2 col-sm-4">\
                          <div class="form-group mt-n" id="challandate' + challanrowcount + '_div">\
                              <div class="col-md-12 pr-xs pl-xs">\
                                  <div class="input-group">\
                                    <input type="text" id="challandate' + challanrowcount + '" name="challandate[' + challanrowcount + ']" class="form-control date challanrow" placeholder="Enter Challan Date" readonly>\
                                        <span class="btn btn-default add-on datepicker_calendar_button" title="Date"><i class="fa fa-calendar fa-lg"></i></span>\
                                    </div>\
                              </div>\
                          </div>\
                      </div>\
                      <div class="col-md-1 col-sm-4">\
                          <div class="form-group mt-n" id="challanamount' + challanrowcount + '_div">\
                              <div class="col-md-12 pr-xs pl-xs">\
                                  <input id="challanamount' + challanrowcount + '" type="text" name="challanamount[' + challanrowcount + ']" class="form-control challanamount text-right challanrow" placeholder="Enter Challan Amount" onkeypress="return decimal_number_validation(event, this.value, 8)">\
                              </div>\
                          </div>\
                      </div>\
                      <div class="col-md-2 col-sm-4">\
                          <div class="form-group mt-n" id="challanfile' + challanrowcount + '_div">\
                              <div class="col-md-12 pr-xs pl-xs">\
                                  <div class="input-group" id="challanfileupload' + challanrowcount + '">\
                                      <span class="input-group-btn" style="padding: 0 0px 0px 0px;">\
                                          <span class="btn btn-primary btn-raised btn-sm btn-file"><i class="fa fa-upload"></i>\
                                              <input type="file" name="challanfile' + challanrowcount + '" id="challanfile' + challanrowcount + '" accept=".png,.jpeg,.jpg,.bmp,.gif,.pdf" onchange="validfile($(this),&apos;challanfile' + challanrowcount + '&apos;,this)">\
                                          </span>\
                                      </span>\
                                      <input type="text" id="challanFiletext' + challanrowcount + '" class="form-control challanrow" placeholder="Enter Challan File" name="challanFiletext[]" readonly>\
                                  </div>\
                              </div>\
                          </div>\
                      </div>\
                      <div class="col-md-2 col-sm-4">\
                          <div class="form-group mt-n" id="challanremarks' + challanrowcount + '_div">\
                              <div class="col-md-12 pr-xs pl-xs">\
                                  <input type="text" id="challanremarks' + challanrowcount + '" name="challanremarks[' + challanrowcount + ']" class="form-control challanrow" placeholder="Enter Challan Remarks">\
                              </div>\
                          </div>\
                      </div>\
                      <div class="col-md-1 col-sm-12 pt-sm pr-xs addrowbutton">\
                          <button type="button" class="btn btn-danger btn-raised remove_btn_challan m-n" onclick="removeChallanRow(' + challanrowcount + ')" style="padding: 5px 10px"><i class="fa fa-minus"></i></button>\
                          <button type="button" class="btn btn-primary btn-raised add_btn_challan m-n" onclick="addChallanRow()"  style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>\
                      </div>\
                    </div>';

    $(".remove_btn_challan:first").show();
    $(".add_btn_challan:last").hide();
    $("#countchallan" + (challanrowcount - 1)).after(challanhtml);

    $('#challandate' + challanrowcount).datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayBtn: "linked",
    });

    $('#challantype' + challanrowcount).selectpicker('refresh');
    $('#challanfor' + challanrowcount).selectpicker('refresh');
    calculatetotal()
}

function removeChallanRow(rowid) {
    $("#countchallan" + rowid).remove();

    $(".add_btn_challan:last").show();
    if ($(".remove_btn_challan:visible").length == 1) {
        $(".remove_btn_challan:first").hide();
    }
    calculatetotal()
}

function validfile(obj, element, elethis) {
    var val = obj.val();
    var id = element.match(/\d+/);
    var filename = obj.val().replace(/C:\\fakepath\\/i, '');

    if (elethis.files[0].size <= UPLOAD_MAX_FILE_SIZE) {

        switch (val.substring(val.lastIndexOf('.') + 1).toLowerCase()) {
            case 'jpe':
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'pdf':

                isvalidfile = 1;
                $("#challanFiletext" + id).val(filename);
                $("#" + element + "_div").removeClass("has-error is-focused");
                break;
            default:
                isvalidfile = 0;
                $("#" + element).val("");
                $("#challanFiletext" + id).val("");
                $("#" + element + "_div").addClass("has-error is-focused");
                new PNotify({title: 'Accept only Image and PDF Files !',styling: 'fontawesome',delay: '3000',type: 'error'
                });
                break;
        }
    } else {
        $("#" + element).val("");
        $("#challanFiletext" + id).val("");
        $("#" + element + "_div").addClass("has-error is-focused");
        new PNotify({title: 'File is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE) + ') !',styling: 'fontawesome',delay: '3000',type: 'error'
        });
    }
}

function validInsurancefile(obj, element, elethis) {
    var val = obj.val();
    var id = obj.attr('id').match(/\d+/);
    var filename = obj.val().replace(/C:\\fakepath\\/i, '');
    if (elethis.files[0].size <= UPLOAD_MAX_FILE_SIZE) {
        switch (val.substring(val.lastIndexOf('.') + 1).toLowerCase()) {
            case 'pdf':
            case 'gif':
            case 'bmp':
            case 'jpg':
            case 'jpeg':
            case 'png':

                isvalidimageorpdffile = 1;
                $("#textfile" + id).val(filename);
                $("#" + element + "_div").removeClass("has-error is-focused");
                break;
            default:
                $("#textfile" + id).val("");
                $("#" + element + "_div").addClass("has-error is-focused");
                new PNotify({title: 'Accept only Image and PDF Files !',styling: 'fontawesome',delay: '3000',type: 'error'
                });
                break;
        }
    } else {
        $("#" + element).val("");
        $("#textfile" + id).val("");
        $("#" + element + "_div").addClass("has-error is-focused");
        new PNotify({title: 'File is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE) + ') !',styling: 'fontawesome',delay: '3000',type: 'error'
        });
    }
}