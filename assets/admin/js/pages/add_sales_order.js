var today = new Date();
var dd = String(today.getDate()).padStart(2, '0');
var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
var yyyy = today.getFullYear();
birthyear = today.getFullYear() - 15;

today = dd + '/' + mm + '/' + yyyy;
dateofbirth = dd + '/' + mm + '/' + birthyear;

function removedata(id) {
    $('#' + id).remove();
}

function removecontectpaertion(id) {
    $('#' + id).remove();
}

var edit_cityid = $('#edit_cityid').val();

function getcity() {
    if (edit_cityid) {
        $(".selectpicker").selectpicker("refresh");
        $('.editcityadd').selectpicker('val', edit_cityid);
    }
}

function countamount(){
alert(111);
}

$(document).ready(function() {

    $('#quotationdate').datepicker({
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
            orientation: "top",
            clearBtn: true,
        });
    });

    $('#openingdate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        orientation: 'top',
        autoclose: true,
        todayBtn: "linked"
    });

    $("#old_receipt_div").hide();
    $('#remove').click(function() {
        $('#removeoldreceipt').val('1');
    });


    $('.fromdate,.duedate').datepicker({
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
            new PNotify({
                title: 'Accept only Image and PDF Files !',
                styling: 'fontawesome',
                delay: '3000',
                type: 'error'
            });
            break;
    }
}

function include(filename, onload) {
    var head = document.getElementsByTagName('head')[0];
    var script = document.createElement('script');
    script.src = filename;
    script.type = 'text/javascript';
    script.onload = script.onreadystatechange = function() {
        if (script.readyState) {
            if (script.readyState === 'complete' || script.readyState === 'loaded') {
                script.onreadystatechange = null;
                onload();
            }
        } else {
            onload();
        }
    };
    head.appendChild(script);
}

function removeproduct(divid) {

    $("#countdocuments" + divid).remove();

    $(".add_btn:last").show();
    if ($(".remove_btn:visible").length == 1) {
        $(".remove_btn:first").hide();
    }
}

function resetdata() {
    $("#voucherno_div").removeClass("has-error is-focused");
    $("#voucherdate_div").removeClass("has-error is-focused");

    if (ACTION == 0) {
        if (latestvoucherno != "") {
            $("#voucherno").val(latestvoucherno);
        }
        $('#narrationid').val('0');
        $(".countdocuments:not(:first)").remove();
        var divid = parseInt($(".countdocuments:first").attr("id").match(/\d+/));
        $('#productid' + divid + ',#priceid' + divid).val("0");
        $('#qty' + divid).val("1");
        $('#price' + divid + ',#totalprice' + divid).val("");
        getproductprice(divid);
        $('.add_btn:first').show();
        $('.remove_btn').hide();
        $(".selectpicker").selectpicker('refresh');
    }
    $('html, body').animate({
        scrollTop: 0
    }, 'slow');
}

function checkvalidation(addtype = 0) {
    var party = $("#party").val().trim();
    var isvalidparty = isvaliddeliverydate = 0;
    PNotify.removeAll();

    if (party == '' || party == 0) {
        $("#party_div").addClass("has-error is-focused");
        new PNotify({
            title: 'Please select party !',
            styling: 'fontawesome',
            delay: '3000',
            type: 'error'
        });
    } else {
        $("#party_div").removeClass("has-error is-focused");
        isvalidparty = 1;
    }
    var c = 1;

    $('.countproducts').each(function() {
        var id = $(this).attr('id').match(/\d+/);

        if ($("#cetegory" + id).val() != "") {

            if ($("#cetegory" + id).val() == 0) {

                $("#cetegory" + id + "_div").addClass("has-error is-focused");
                new PNotify({
                    title: 'Please select ' + (c) + ' product !',
                    styling: 'fontawesome',
                    delay: '3000',
                    type: 'error'
                });
                isvalidcetegory = 0;
            } else {
                $("#cetegory" + id + "_div").removeClass("has-error is-focused");
            }

            if ($("#product" + id).val() == 0) {
                $("#product" + id + "_div").addClass("has-error is-focused");
                new PNotify({
                    title: 'Please select ' + (c) + ' price !',
                    styling: 'fontawesome',
                    delay: '3000',
                    type: 'error'
                });
                isvalidproduct = 0;
            } else {
                $("#product" + id + "_div").removeClass("has-error is-focused");
            }
            if ($("#price" + id).val() == 0) {
                $("#price" + id + "_div").addClass("has-error is-focused");
                new PNotify({
                    title: 'Please select ' + (c) + ' price !',
                    styling: 'fontawesome',
                    delay: '3000',
                    type: 'error'
                });
                isvalidprice = 0;
            } else {
                $("#price" + id + "_div").removeClass("has-error is-focused");
            }
            if ($("#qty" + id).val() == 0) {
                $("#qty" + id + "_div").addClass("has-error is-focused");
                new PNotify({
                    title: 'Please select ' + (c) + ' qty !',
                    styling: 'fontawesome',
                    delay: '3000',
                    type: 'error'
                });
                isvalidqty = 0;
            } else {
                $("#qty" + id + "_div").removeClass("has-error is-focused");
            }

        } else {
            $("#invoice" + id + "_div").removeClass("has-error is-focused");
            $("#price" + id + "_div").removeClass("has-error is-focused");
            $("#invoiceprice" + id + "_div").removeClass("has-error is-focused");
            $("#qty" + id + "_div").removeClass("has-error is-focused");
        }
        c++;
    });

    var c = 1;
    if (isvalidparty && isvaliddeliverydate && isvalidcetegory && isvalidproduct && isvalidprice && isvalidqty == 1) {
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
                    if (response == 1) {
                        new PNotify({
                            title: "Party successfully added.",
                            styling: 'fontawesome',
                            delay: '3000',
                            type: 'success'
                        });
                        if (addtype == 1) {
                            resetdata();
                        } else {
                            setTimeout(function() {
                                window.location = SITE_URL + "Party";
                            }, 1500);
                        }
                    } else if (response == 2) {
                        new PNotify({
                            title: "Party code or email or contact number already exists !",
                            styling: 'fontawesome',
                            delay: '3000',
                            type: 'error'
                        });
                        $("#name_div").addClass("has-error is-focused");
                    } else {
                        new PNotify({
                            title: 'Party not added !',
                            styling: 'fontawesome',
                            delay: '3000',
                            type: 'error'
                        });
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
                        new PNotify({
                            title: "Party successfully updated.",
                            styling: 'fontawesome',
                            delay: '1500',
                            type: 'success'
                        });
                        if (addtype == 1) {
                            setTimeout(function() {
                                window.location = SITE_URL + "party/add-party";
                            }, 1500);
                        } else {
                            setTimeout(function() {
                                window.location = SITE_URL + "party";
                            }, 1500);
                        }
                    } else if (response == 2) {
                        new PNotify({
                            title: "Party code or email or contact number already exists !",
                            styling: 'fontawesome',
                            delay: '3000',
                            type: 'error'
                        });
                    } else {
                        new PNotify({
                            title: 'Party not updated !',
                            styling: 'fontawesome',
                            delay: '3000',
                            type: 'error'
                        });
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

// function addnewproduct() {
//     var cloopdoc = $("#cloopdoc").val();
//     cloopdoc++;
//     $("#cloopdoc").val(cloopdoc);
//     var datahtml = '\
//     <div class="col-sm-6 countdocuments pl-sm pr-sm" id="countdocuments' + cloopdoc + '">\
//                       <input type="hidden" name="doc_id_' + cloopdoc + '" value="0" id="doc_id_' + cloopdoc + '">\
//                       <div class="col-md-5 col-sm-5">\
//                           <div class="form-group" id="documentnumber_' + cloopdoc + '">\
//                               <div class="col-sm-12 pr-xs pl-xs">\
//                                   <input id="documentname_' + cloopdoc + '" name="documentname_' + cloopdoc + '" placeholder="Enter Document Name" class="form-control documentnumber">\
//                               </div>\
//                           </div>\
//                       </div>\
//                       <div class="col-md-5 col-sm-5">\
//                           <div class="form-group" id="docfile' + cloopdoc + '">\
//                               <div class="col-sm-12 pr-xs pl-xs">\
//                                   <input type="hidden" id="isvaliddocfile' + cloopdoc + '" value="0">\
//                                   <input type="hidden" name="olddocfile[' + cloopdoc + ']" id="olddocfile' + cloopdoc + '" value="">\
//                                   <div class="input-group" id="fileupload' + cloopdoc + '">\
//                                       <span class="input-group-btn" style="padding: 0 0px 0px 0px;">\
//                                           <span class="btn btn-primary btn-raised btn-file">\
//                                           <i class="fa fa-upload"></i>\
//                                               <input type="file" name="olddocfile_' + cloopdoc + '" class="docfile" id="olddocfile_' + cloopdoc + '" accept=".png,.jpeg,.jpg,.bmp,.gif,.pdf" onchange="validdocumentfile($(this),&apos;docfile' + cloopdoc + '&apos;)">\
//                                           </span>\
//                                       </span>\
//                                       <input type="text" readonly="" placeholder="Enter File" id="Filetextdocfile' + cloopdoc + '" class="form-control docfile" name="Filetextdocfile_' + cloopdoc + '" value="">\
//                                   </div>\
//                               </div>\
//                           </div>\
//                       </div>\
//                       <div class="col-md-1 addrowbutton pt-md pr-xs">\
//                           <button type="button" class="btn btn-danger btn-raised remove_btn m-n" onclick="removeDocument(' + cloopdoc + ')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
//                       </div>\
//                       <div class="col-md-1 addrowbutton pt-md pr-xs">\
//                           <button type="button" class="btn btn-primary btn-raised remove_btn m-n" onclick="addnewproduct()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>\
//                            </div>\
//                       </div>\
//                   ';

//     $("#adddocrow").append(datahtml);

// }

function addnewproduct() {

    //  productoptionhtml = salesproducthtml;
    // if(PRODUCT_DISCOUNT==0){
    //     discount = "display:none;";
    // }else{ 
    //     discount = "display:block;"; 
    // }
    // var readonly = "readonly";
    // if(EDITTAXRATE_CHANNEL==1 && EDITTAXRATE_SYSTEM==1){
    //     readonly = "";
    // }
    divcount = parseInt($(".amounttprice:last").attr("div-id")) + 1;
    producthtml = '<tr class="countproducts" id="quotationproductdiv' + divcount + '">\
        <td>\
            <input type="hidden" name="producttax[]" id="producttax' + divcount + '">\
            <input type="hidden" name="productrate[]" id="productrate' + divcount + '">\
            <input type="hidden" name="originalprice[]" id="originalprice' + divcount + '">\
            <input type="hidden" name="uniqueproduct[]" id="uniqueproduct' + divcount + '">\
            <input type="hidden" name="referencetype[]" id="referencetype' + divcount + '">\
            <div class="form-group" id="cetegory' + divcount + '_div">\
                <div class="col-sm-12">\
                    <select id="cetegory' + divcount + '" name="cetegory[]" data-width="90%" class="selectpicker form-control cetegory" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="' + divcount + '">\
                        <option value="0">Select Cetegory</option>\
                        '+PRODUCT_CETEGORY_DATA+'\
                    </select>\
                </div>\
            </div>\
        </td>\
        <td>\
            <div class="form-group" id="product' + divcount + '_div">\
                <div class="col-md-12">\
                    <select id="product' + divcount + '" name="product[]" data-width="90%" onchange="getprice('+ divcount +')" class="selectpicker form-control product" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="' + divcount + '">\
                        <option value="">Select Product</option>\
                        '+PRODUCT_DORPDOWN_DATA+'\
                    </select>\
                </div>\
            </div>\
        </td>\
        <td>\
            <div class="form-group" id="price' + divcount + '_div">\
                <div class="col-sm-12">\
                    <select id="price' + divcount + '" name="price[]" data-width="150px" class="selectpicker form-control price" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="' + divcount + '">\
                        <option value="">Price</option>\
                    </select>\
                </div>\
            </div>\
            <div class="form-group" id="actualprice' + divcount + '_div">\
                <div class="col-sm-12">\
                    <label for="actualprice' + divcount + '" class="control-label">Rate (' + CURRENCY_CODE + ')</label>\
                    <input type="text" class="form-control actualprice text-right" id="actualprice' + divcount + '" name="actualprice[]" value="" onkeypress="return decimal_number_validation(event, this.value)" style="display: block;" div-id="' + divcount + '">\
                </div>\
            </div>\
        </td>\
        <td>\
            <div class="form-group" id="qty' + divcount + '_div">\
                <div class="col-md-12">\
                    <input type="text" class="form-control qty" id="qty' + divcount + '" name="qty[]" value="" onkeypress="' + (MANAGE_DECIMAL_QTY == 1 ? "return decimal_number_validation(event, this.value,8);" : "return isNumber(event);") + '" style="display: block;" div-id="' + divcount + '">\
                </div>\
            </div>\
        </td>\
        <td>\
            <div class="form-group" id="tax' + divcount + '_div">\
                <div class="col-sm-12">\
                    <input type="text" class="form-control text-right tax" id="tax' + divcount + '" name="tax[]" value="" div-id="' + divcount + '" >	\
                    <input type="hidden" value="" id="ordertax' + divcount + '">\
                </div>\
            </div>\
        </td>\
        <td>\
            <div class="form-group" id="amount' + divcount + '_div">\
                <div class="col-sm-12">\
                    <input type="text" class="form-control amounttprice" id="amount' + divcount + '" name="amount[]" value="" readonly="" div-id="' + divcount + '">\
                    <input type="hidden" class="producttaxamount" id="producttaxamount' + divcount + '" name="producttaxamount[]" value="" div-id="' + divcount + '">\
                </div>\
            </div>\
        </td>\
        <td>\
            <div class="form-group pt-sm">\
                <div class="col-sm-12 pr-n">\
                    <button type = "button" class = "btn btn-default btn-raised  add_remove_btn_product" onclick = "removeproduct(' + divcount + ')" style="padding: 5px 10px;"> <i class = "fa fa-minus"></i></button> \
                    <button type="button" class="btn btn-default btn-raised  add_remove_btn" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button> \
                </div>\
            </div>\
        </td>\
    </div>';

    $(".add_remove_btn_product:first").show();
    $(".add_remove_btn:last").hide();
    $("#quotationproducttable tbody").append(producthtml);

    // $("#qty"+divcount).TouchSpin(touchspinoptions);

    $(".selectpicker").selectpicker("refresh");
}

function removeproduct(divid) {

    if ($('select[name="productid[]"]').length != 1 && ACTION == 1 && $('#quotationproductsid' + divid).val() != null) {
        var removequotationproductid = $('#removequotationproductid').val();
        $('#removequotationproductid').val(removequotationproductid + ',' + $('#quotationproductsid' + divid).val());
    }
    $("#quotationproductdiv" + divid).remove();

    $(".add_remove_btn:last").show();
    if ($(".add_remove_btn_product:visible").length == 1) {
        $(".add_remove_btn_product:first").hide();
    }
    changeproductamount(divid);
    changeextrachargesamount();
}

function addcontectfield(id, countcontactno) {
    var countcontactno = $("#countcontactno").val();
    countcontactno++;
    $("#countcontactno").val(countcontactno);
    var datahtml = '<div class="col-md-4 pl-sm pr-sm visible-md visible-lg" id="contecremove' + countcontactno + '">\
    <div class="form-group" id="contactno_div">\
       <label for="contactno" class="col-md-4 control-label">Contact No <span class="mandatoryfield"> *</span></label>\
       <div class="col-md-6">\
          <input id="contactno" type="text" name="contactno' + id + '[]" class="form-control"  value="">\
       </div>\
        <div class="form-group col-md-3">\
            <button type="button"  onclick="addcontectfield(' + id + ',' + countcontactno + ')"  class="addprodocitem btn-primary btn-xs" style="margin-top: 7px;"><i class="fa fa-plus"></i></button>\
            <button type="button" class="btn-danger btn-xs" onclick="removecontect(' + countcontactno + ')"><i class="fa fa-minus"></i></button>\
        </div>\
    </div>\
 </div>';

    $(".addcontectfilelddata" + id + "").after(datahtml);

}

function removecontect(divid) {
    var countcontactno = $("#countcontactno").val();
    countcontactno++;
    $("#countcontactno").val(countcontactno);

    $("#contecremove" + divid).remove();

    $(".add_btn:last").show();
    if ($(".remove_btn:visible").length == 1) {
        $(".remove_btn:first").hide();
    }
}

function addnewcontect() {
    var cloopcount2 = $("#cloopcount").val();
    cloopcount2++;
    $("#cloopcount").val(cloopcount2);
    var datahtml2 = '<div class="data" id="contectrowdelete_' + cloopcount2 + '">\
    <div class="row">\
    <div class="clearfix"></div>\
            <div class="col-md-4 pl-sm pr-sm visible-md visible-lg">\
            <input type="hidden" name="contectid_' + cloopcount2 + '" value="0" id="contectid_' + cloopcount2 + '">\
                <div class="form-group" id="firstname_div">\
                    <label for="firstname" class="col-md-4 control-label">First Name <span class="mandatoryfield"> *</span></label>\
                    <div class="col-md-8">\
                        <input id="firstname" type="text" name="firstname_' + cloopcount2 + '" class="form-control" value="" onkeypress="return onlyAlphabets(event)">\
                    </div>\
                </div>\
            </div>\
            <div class="col-md-4 pl-sm pr-sm visible-md visible-lg">\
                <div class="form-group" id="lastname_div">\
                    <label for="lastname" class="col-md-4 control-label">Last Name <span class="mandatoryfield"> *</span></label>\
                    <div class="col-md-8">\
                        <input id="lastname" type="text" name="lastname_' + cloopcount2 + '" class="form-control" value="" onkeypress="return onlyAlphabets(event)">\
                    </div>\
                </div>\
            </div>\
            <div class="col-md-4 pl-sm pr-sm visible-md visible-lg addcontectfilelddata' + cloopcount2 + '">\
                <div class="form-group" id="contactno_div">\
                    <label for="contactno" class="col-md-4 control-label">Contact No <span class="mandatoryfield"> *</span></label>\
                    <div class="col-md-6">\
                        <input id="contactno" type="text" name="contactno' + cloopcount2 + '[]" class="form-control" onkeypress="return isNumber(event)" maxlength="10" value="">\
                    </div>\
                    <div class="form-group col-md-2">\
                        <button type="button" onclick="addcontectfield(' + cloopcount2 + ')" class="addprodocitem btn-primary"><i class="fa fa-plus"></i></button>\
                    </div>\
                </div>\
            </div>\
            <div class="col-md-4 pl-sm pr-sm visible-md visible-lg">\
                <div class="form-group" id="birthdate_div">\
                    <label for="birthdate" class="col-md-4 control-label">Birth Date</label>\
                    <div class="col-md-8">\
                        <input id="birthdate" type="text" name="birthdate_' + cloopcount2 + '" class="form-control date" value="" readonly>\
                    </div>\
                </div>\
            </div>\
            <div class="col-md-4 pl-sm pr-sm visible-md visible-lg">\
                <div class="form-group" id="anniversarydate_div">\
                    <label for="anniversarydate" class="col-md-4 control-label">Anniversary Date</label>\
                    <div class="col-md-8">\
                        <input id="anniversarydate" type="text" name="anniversarydate_' + cloopcount2 + '" class="form-control date" value="" readonly>\
                    </div>\
                </div>\
            </div>\
            <div class="col-md-4 pl-sm pr-sm visible-md visible-lg">\
                <div class="form-group" id="email_div">\
                    <label for="email" class="col-md-4 control-label">Email <span class="mandatoryfield">*</span></label>\
                    <div class="col-md-8">\
                        <input id="email" type="text" name="email_' + cloopcount2 + '" class="form-control" value="">\
                    </div>\
                </div>\
            </div>\
            <div class="col-md-4 pl-sm pr-sm visible-md visible-lg">\
                <button type="button" style="float:left; margin:10px 19px 0px 20px;" onclick="removecontectpaertion(\'contectrowdelete_' + cloopcount2 + '\')" class="btn-danger">Remove</button>\
                <div class="form-group" style="float:left; margin:10px 19px 0px 5px;">\
                <button type="button" class="addpro btn-primary" onclick="addnewcontect()">Add\
                Data</button>\
                </div>\
            </div>\
        </div>\
    </div>\
</div>';
    $("#addtarget").append(datahtml2);
}

function addnewproductdetails() {
    var cloopcount2 = $("#cloopcount").val();
    cloopcount2++;
    $("#cloopcount").val(cloopcount2);
    var datahtml2 = '<div class="data" id="addnewproductdetails_' + cloopcount2 + '">\
    <div class="row">\
    <div class="clearfix"></div>\
            <div class="col-md-2 pl-sm pr-sm visible-md visible-lg">\
                <div class="form-group" id="firstname_div">\
                    <div class="col-md-12">\
                        <input id="firstname" type="text" name="firstname_<?=$cloopcount?>" \class="form-control" value="">\
                    </div>\
                </div>\
            </div>\
            <div class="col-md-2 pl-sm pr-sm visible-md visible-lg">\
                <div class="form-group" id="lastname_div">\
                <div class="col-md-12">\
                    <input id="lastname" type="text" name="lastname_<?=$cloopcount?>" class="form-control" value="">\
                </div>\
                </div>\
            </div>\
            <div class="col-md-2 pl-sm pr-sm visible-md visible-lg">\
                <div class="form-group" id="contactno_div">\
                <div class="col-md-12">\
                    <input id="contactno" type="text" name="contactno" class="form-control"  value="">\
                </div>\
                </div>\
            </div>\
            <div class="col-md-1 pl-sm pr-sm visible-md visible-lg">\
                <div class="form-group" id="birthdate_div">\
                <div class="col-md-12">\
                    <input id="birthdate" type="text" name="birthdate_<?=$cloopcount?>" class="form-control" value="" readonly>\
                </div>\
                </div>\
            </div>\
            <div class="col-md-1 pl-sm pr-sm visible-md visible-lg">\
                <div class="form-group" id="anniversarydate_div">\
                <div class="col-md-12">\
                    <input id="anniversarydate" type="text" name="anniversarydate_<?=$cloopcount?>" class="form-control" value="" readonly>\
                </div>\
                </div>\
            </div>\
            <div class="col-md-2 pl-sm pr-sm visible-md visible-lg">\
                <div class="form-group" id="email_div">\
                <div class="col-md-12">\
                    <input id="email" type="text" name="email_<?=$cloopcount?>" class="form-control" value="">\
                </div>\
                </div>\
            </div>\
            <div class="col-md-1 addrowbutton pt-md pr-xs">\
                <button type="button" class="btn btn-danger btn-raised remove_btn m-n" onclick="removeProductdeteile(' + cloopcount2 + ')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
                <button type="button" class="btn btn-primary btn-raised remove_btn m-n" onclick="addnewproductdetails()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>\
            </div>\
        </div>\
    </div>\
</div>';
    $("#addtarget").append(datahtml2);
}

function removeDocument(divid) {
    $("#countdocuments" + divid).remove();
    $(".add_btn:last").show();
    if ($(".remove_btn:visible").length == 1) {
        $(".remove_btn:first").hide();
    }
}

function removeProductdeteile(divid) {
    $("#addnewproductdetails_" + divid).remove();
    $(".add_btn:last").show();
    if ($(".remove_btn:visible").length == 1) {
        $(".remove_btn:first").hide();
    }
}

function addnewinvoicetransaction() {

    var rowcount = parseInt($(".countinvoice:last").attr("id").match(/\d+/)) + 1;
    var datahtml = '<div class="countinvoice" id="countinvoice' + rowcount + '">\
                    <div class="row m-n">\
                        <div class="col-md-2">\
                            <div class="form-group" id="invoice' + rowcount + '_div">\
                                <div class="col-sm-12">\
                                    <select id="invoiceid' + rowcount + '" name="invoiceid[]" class="selectpicker form-control invoiceid" data-live-search="true" data-select-on-tab="true" data-size="6">\
                                        <option value="0">Select Invoice</option>\
                                    </select>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-2">\
                            <div class="form-group" id="product' + rowcount + '_div">\
                                <div class="col-md-12">\
                                    <select id="product' + rowcount + '" name="product[]" class="selectpicker form-control product" data-live-search="true" data-select-on-tab="true" data-size="6">\
                                        <option value="0">Select product</option>\
                                    </select>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-2">\
                            <div class="form-group" id="invoiceamount' + rowcount + '_div">\
                                <div class="col-md-12">\
                                    <input type="text" id="invoiceamount' + rowcount + '" class="form-control invoiceamount text-right" name="invoiceamount[]" value="" onkeypress="return decimal_number_validation(event, this.value, 10)">\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-2">\
                            <div class="form-group" id="remainingamount' + rowcount + '_div">\
                                <div class="col-md-12">\
                                    <input type="text" id="remainingamount' + rowcount + '" class="form-control text-right remainingamount" value="" onkeypress="return decimal_number_validation(event, this.value, 10)">\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-2">\
                            <div class="form-group" id="remainingamount' + rowcount + '_div">\
                                <div class="col-md-12">\
                                    <input type="text" id="remainingamount' + rowcount + '" class="form-control text-right remainingamount" value="" >\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-2 pt-md">\
                            <button type="button" class="btn btn-danger btn-raised remove_invoice_btn m-n" onclick="removetransaction(' + rowcount + ')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
                            <button type="button" class="btn btn-primary btn-raised add_invoice_btn m-n" onclick="addnewinvoicetransaction()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>\
                        </div>\
                    </div>\
                </div>';

    $(".remove_invoice_btn:first").show();
    $(".add_invoice_btn:last").hide();
    $("#countinvoice" + (rowcount - 1)).after(datahtml);

    $("#invoiceid" + rowcount).selectpicker("refresh");
    $("#product" + rowcount).selectpicker("refresh");

    /****INVOICE CHANGE EVENT****/
    $("#invoiceid" + rowcount).on('change', function(e) {
        var divid = $(this).attr("id").match(/\d+/);
        $("#amountdue" + divid + ",#invoiceamount" + divid + ",#remainingamount" + divid).val('');
        if (this.value != 0) {
            var invoiceamount = $("#invoiceid" + divid + " option:selected").attr("data-invoiceamount");
            $("#amountdue" + divid).val(parseFloat(invoiceamount).toFixed(2));
        }
        // calculateamount();
    });
    $("#invoiceid1" + rowcount).selectpicker("refresh");

    /****INVOICE CHANGE EVENT****/
    $("#product" + rowcount).on('change', function(e) {
        var divid = $(this).attr("id").match(/\d+/);

        if (this.value != 0) {
            var invoiceamount = $("#product" + divid + " option:selected").attr("data-invoiceamount");
            $("#amountdue" + divid).val(parseFloat(invoiceamount).toFixed(2));
        }
        // calculateamount();
    });

    /****AMOUNT KEYUP EVENT****/
    // $("#invoiceamount" + rowcount).on('keyup', function (e) {
    //     var divid = $(this).attr("id").match(/\d+/);
    //     var amountdue = $("#amountdue" + divid).val();

    //     if (amountdue != "" && this.value != "") {
    //         if (parseFloat(this.value) > parseFloat(amountdue)) {
    //             $(this).val(parseFloat(amountdue).toFixed(2));
    //         }

    //         var remainingamount = parseFloat(amountdue) - parseFloat(this.value);
    //         $("#remainingamount" + divid).val(parseFloat(remainingamount).toFixed(2));
    //     }
    //     calculateamount();
    // });

}

function removetransaction(rowid) {

    if ($('select[name="invoiceid[]"]').length != 1 && ACTION == 1 && $('#paymentreceipttransactionsid' + rowid).val() != null) {
        var removepaymentreceipttransactionsid = $('#removepaymentreceipttransactionsid').val();
        $('#removepaymentreceipttransactionsid').val(removepaymentreceipttransactionsid + ',' + $('#paymentreceipttransactionsid' + rowid).val());
    }
    $("#countinvoice" + rowid).remove();

    $(".add_invoice_btn:last").show();
    if ($(".remove_invoice_btn:visible").length == 1) {
        $(".remove_invoice_btn:first").hide();
    }

    changenetamounttotal();
}

function addnewdoc() {

    var rowcount = parseInt($(".countinvoiceb:last").attr("id").match(/\d+/)) + 1;
    var datahtml = '<div class="countinvoiceb" id="countinvoiceb' + rowcount + '">\
                    <div class="row m-n">\
                        <div class="col-md-3">\
                            <div class="form-group" id="invoiceamount' + rowcount + '_div">\
                                <div class="col-md-12">\
                                    <input type="text" id="invoiceamount' + rowcount + '" class="form-control invoiceamount"  placeholder="Enter Document Name" name="invoiceamount[]" value="" onkeypress="return decimal_number_validation(event, this.value, 10)">\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-3 col-sm-3">\
                          <div class="form-group" id="docfile' + rowcount + '">\
                              <div class="col-sm-12 pr-xs pl-xs">\
                                  <input type="hidden" id="isvaliddocfile' + rowcount + '" value="0">\
                                  <input type="hidden" name="olddocfile[' + rowcount + ']" id="olddocfile' + rowcount + '" value="">\
                                  <div class="input-group" id="fileupload' + rowcount + '">\
                                      <span class="input-group-btn" style="padding: 0 0px 0px 0px;">\
                                          <span class="btn btn-primary btn-raised btn-file">\
                                          <i class="fa fa-upload"></i>\
                                              <input type="file" name="olddocfile_' + rowcount + '" class="docfile" id="olddocfile_' + rowcount + '" accept=".png,.jpeg,.jpg,.bmp,.gif,.pdf" onchange="validdocumentfile($(this),&apos;docfile' + rowcount + '&apos;)">\
                                          </span>\
                                      </span>\
                                      <input type="text" readonly="" placeholder="Enter File" id="Filetextdocfile' + rowcount + '" class="form-control docfile" name="Filetextdocfile_' + rowcount + '" value="">\
                                  </div>\
                              </div>\
                          </div>\
                      </div>\
                        <div class="col-md-2 pt-md">\
                            <button type="button" class="btn btn-danger btn-raised remove_doc_btn m-n" onclick="removedoc(' + rowcount + ')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
                            <button type="button" class="btn btn-primary btn-raised add_doc_btn m-n" onclick="addnewdoc()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>\
                        </div>\
                    </div>\
                </div>';

    $(".remove_doc_btn:first").show();
    $(".add_doc_btn:last").hide();
    $("#countinvoiceb" + (rowcount - 1)).after(datahtml);

}

function removedoc(rowid) {


    $("#countinvoiceb" + rowid).remove();

    $(".add_doc_btn:last").show();
    if ($(".remove_doc_btn:visible").length == 1) {
        $(".remove_doc_btn:first").hide();
    }


}

function addattachfile() {

    var rowcount = parseInt($(".countfiles:last").attr("id").match(/\d+/)) + 1;
    var element = "file" + rowcount;
    var datahtml = '<div class="col-md-6 p-n countfiles" id="countfiles' + rowcount + '">\
                        <div class="col-md-7">\
                            <div class="form-group" id="file' + rowcount + '_div">\
                                <div class="col-md-12 pl-n">\
                                    <div class="input-group" id="fileupload' + rowcount + '">\
                                        <span class="input-group-btn" style="padding: 0 0px 0px 0px;">\
                                            <span class="btn btn-primary btn-raised btn-file"><i class="fa fa-upload"></i>\
                                                <input type="file" name="file' + rowcount + '" class="file" id="file' + rowcount + '" accept=".bmp,.bm,.gif,.ico,.jfif,.jfif-tbnl,.jpe,.jpeg,.jpg,.pbm,.png,.svf,.tif,.tiff,.wbmp,.x-png,.doc,.docx,.pdf" onchange="validattachmentfile($(this),&apos;' + element + '&apos;,this)">\
                                            </span>\
                                        </span>\
                                        <input type="text" readonly="" id="Filetext' + rowcount + '" class="form-control" name="Filetext[]" value="">\
                                    </div>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-3">\
                            <div class="form-group" id="fileremarks' + rowcount + '_div">\
                                <input type="text" class="form-control" name="fileremarks' + rowcount + '" id="fileremarks' + rowcount + '" value="">\
                            </div>\
                        </div>\
                        <div class="col-md-2 pl-sm pr-sm mt-md">\
                            <button type="button" class="btn btn-default btn-raised remove_file_btn m-n" onclick="removeattachfile(' + rowcount + ')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
                            <button type="button" class="btn btn-default btn-raised add_file_btn m-n" onclick="addattachfile()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>\
                        </div>\
                    </div>';

    $(".remove_file_btn:first").show();
    $(".add_file_btn:last").hide();
    $("#countfiles" + (rowcount - 1)).after(datahtml);
    if ($(".countfiles").length == 1) {
        $("#filesheading2").hide();
    } else {
        $("#filesheading2").show();
    }
}

function removeattachfile(rowid) {

    if ($('.countfiles').length != 1 && ACTION == 1 && $('#transactionattachmentid' + rowid).val() != null) {
        var removetransactionattachmentid = $('#removetransactionattachmentid').val();
        $('#removetransactionattachmentid').val(removetransactionattachmentid + ',' + $('#transactionattachmentid' + rowid).val());
    }
    $("#countfiles" + rowid).remove();
    if ($(".countfiles").length == 1) {
        $("#filesheading2").hide();
    } else {
        $("#filesheading2").show();
    }
    $(".add_file_btn:last").show();
    if ($(".remove_file_btn:visible").length == 1) {
        $(".remove_file_btn:first").hide();
    }
}