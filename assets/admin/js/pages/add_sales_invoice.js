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
$(document).ready(function () {
  

    $("#productdiscount1").on('keyup', function(e) {
        var val = $(this).val();
        if (val > 100) {
            $('#productdiscount1').val("100.00");
        }
    });

    $('#invoicedate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        orientation: 'top',
        autoclose: true,
        todayBtn: "linked"
    });

    $('#openingdate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        orientation: 'top',
        autoclose: true,
        todayBtn: "linked"
    });


    $('body').on('focus', ".date", function () {
        $(this).datepicker({
            todayHighlight: true,
            format: 'dd/mm/yyyy',
            orientation: "top left",
            endDate: dateofbirth,
            clearBtn: true,
        });
    });

    $('body').on('focus', ".pc100", function () {
       
        $(this).on('keyup', function(e) {
            var val = $(this).val();
            if (val > 100) {
                $(this).val("100.00");
            }
        });
    });

    $("#old_receipt_div").hide();
    $('#remove').click(function () {
        $('#removeoldreceipt').val('1');
    });

    $(".selectpicker").selectpicker("refresh");

   
 
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
            new PNotify({ title: 'Accept only Image and PDF Files !', styling: 'fontawesome', delay: '3000', type: 'error' });
            break;
    }
}

function include(filename, onload) {
    var head = document.getElementsByTagName('head')[0];
    var script = document.createElement('script');
    script.src = filename;
    script.type = 'text/javascript';
    script.onload = script.onreadystatechange = function () {
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
    $('html, body').animate({ scrollTop: 0 }, 'slow');
}

function checkvalidation(addtype = 0) {
    var party = $("#party").val().trim();
    var discount = $("#discount").val().trim();
    var deliverydate = $("#deliverydate").val().trim();
    var discountamount = $("#discountamount").val().trim();
    
    var isvalidwebsitename  = isvalidcategory = isvalidproduct = isvalidproductamount = isvaliddocumentname = isvaliddocumentname = 0;

    PNotify.removeAll();
    if (party == '' || party == 0) {
        $("#party_div").addClass("has-error is-focused");
        new PNotify({ title: 'Please select party name !', styling: 'fontawesome', delay: '3000', type: 'error' });
    } else {
        $("#party_div").removeClass("has-error is-focused");
        isvalidwebsitename = 1;
    }

    var c=1;
    $('.countinvoice').each(function(){
        var id = $(this).attr('id').match(/\d+/);
        if($("#category"+id).val() > 0 || $("#invoiceid"+id).val() > 0 || $("#invoiceid"+id).val() != "" ){
            if($("#category"+id).val() == 0){
                $("#category"+id+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(c)+' category !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidcategory = 0;
            }else {
                $("#category"+id+"_div").removeClass("has-error is-focused");
            }

            if($("#product"+id).val() == 0){
                $("#product"+id+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(c)+' product  !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidproduct = 0;
            }else {
                $("#product"+id+"_div").removeClass("has-error is-focused");
            }
            if($("#invoiceamount"+id).val() == 0){
                $("#invoiceamount"+id+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(c)+' product  !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidproduct = 0;
            }else {
                $("#invoiceamount"+id+"_div").removeClass("has-error is-focused");
            }
            
            if($("#productamount"+id).val() == 0){
                $("#productamount"+id+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(c)+' product amount !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidproductamount = 0;
            }else {
                $("#productamount"+id+"_div").removeClass("has-error is-focused");
            }
          
        } else{
            $("#invoice"+id+"_div").removeClass("has-error is-focused");
            $("#price"+id+"_div").removeClass("has-error is-focused");
            $("#invoiceprice"+id+"_div").removeClass("has-error is-focused");
            
        }
        c++;
    });

    var cd=1;
    
  

    if (deliverydate == '' ) {
        $("#delivery_div").addClass("has-error is-focused");
        new PNotify({ title: 'Please enter delivery date !', styling: 'fontawesome', delay: '3000', type: 'error' });
    } else {
        $("#delivery_div").removeClass("has-error is-focused");
        isvaliddeliverydate = 1;
    }

    if (discount == '' ) {
        $("#discount_div").addClass("has-error is-focused");
        new PNotify({ title: 'Please enter discount !', styling: 'fontawesome', delay: '3000', type: 'error' });
    } else {
        $("#discount_div").removeClass("has-error is-focused");
        isvaliddiscount = 1;
    }

    if (discountamount == '' ) {
        $("#discountamount_div").addClass("has-error is-focused");
        new PNotify({ title: 'Please enter discount amount !', styling: 'fontawesome', delay: '3000', type: 'error' });
    } else {
        $("#discountamount_div").removeClass("has-error is-focused");
        isvaliddiscountamount = 1;
    }

    $('.countinvoiceb').each(function(){
        var id = $(this).attr('id').match(/\d+/);
      
        if($("#documentname"+id).val() == "" ){
            if($("#documentname"+id).val() == ""){
               
                $("#documentname"+id+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(cd)+' document name !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvaliddocumentname = 0;
            }else {
                alert(2);
                $("#documentname"+id+"_div").removeClass("has-error is-focused");
            }
        } else{
            alert(3);
            $("#documentname"+id+"_div").removeClass("has-error is-focused");
        }
        cd++;
    });
  


    var c = 1;
    if (isvalidwebsitename && isvaliddocumentname && isvaliddiscountamount && isvalidproductamount && isvaliddiscountamount && isvaliddocumentname == 1) {
        var formData = new FormData($('#party-form')[0]);
        if (ACTION == 0) {
            var uurl = SITE_URL + "party/party-add";
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
                        new PNotify({ title: "Party successfully added.", styling: 'fontawesome', delay: '3000', type: 'success' });
                        if (addtype == 1) {
                            resetdata();
                        } else {
                            setTimeout(function () { window.location = SITE_URL + "Party"; }, 1500);
                        }
                    } else if (response == 2) {
                        new PNotify({ title: "Party code or email or contact number already exists !", styling: 'fontawesome', delay: '3000', type: 'error' });
                        $("#name_div").addClass("has-error is-focused");
                    } else {
                        new PNotify({ title: 'Party not added !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    }
                },
                error: function (xhr) { },
                complete: function () {
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
                beforeSend: function () {
                    $('.mask').show();
                    $('#loader').show();
                },
                success: function (response) {
                    var data = JSON.parse(response);
                    if (response == 1) {
                        new PNotify({ title: "Party successfully updated.", styling: 'fontawesome', delay: '1500', type: 'success' });
                        if (addtype == 1) {
                            setTimeout(function () { window.location = SITE_URL + "party/add-party"; }, 1500);
                        } else {
                            setTimeout(function () { window.location = SITE_URL + "party"; }, 1500);
                        }
                    } else if (response == 2) {
                        new PNotify({ title: "Party code or email or contact number already exists !", styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else {
                        new PNotify({ title: 'Party not updated !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    }
                },
                error: function (xhr) { },
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

function addnewproduct() {
    var cloopdoc = $("#cloopdoc").val();
    cloopdoc++;
    $("#cloopdoc").val(cloopdoc);
    var datahtml = '\
    <div class="col-sm-6 countdocuments pl-sm pr-sm" id="countdocuments' + cloopdoc + '">\
                      <input type="hidden" name="doc_id_' + cloopdoc + '" value="0" id="doc_id_' + cloopdoc + '">\
                      <div class="col-md-5 col-sm-5">\
                          <div class="form-group" id="documentnumber_' + cloopdoc + '">\
                              <div class="col-sm-12 pr-xs pl-xs">\
                                  <input id="documentname_' + cloopdoc + '" name="documentname_' + cloopdoc + '" placeholder="Enter Document Name" class="form-control documentnumber">\
                              </div>\
                          </div>\
                      </div>\
                      <div class="col-md-5 col-sm-5">\
                          <div class="form-group" id="docfile' + cloopdoc + '">\
                              <div class="col-sm-12 pr-xs pl-xs">\
                                  <input type="hidden" id="isvaliddocfile' + cloopdoc + '" value="0">\
                                  <input type="hidden" name="olddocfile[' + cloopdoc + ']" id="olddocfile' + cloopdoc + '" value="">\
                                  <div class="input-group" id="fileupload' + cloopdoc + '">\
                                      <span class="input-group-btn" style="padding: 0 0px 0px 0px;">\
                                          <span class="btn btn-primary btn-raised btn-file">\
                                          <i class="fa fa-upload"></i>\
                                              <input type="file" name="olddocfile_' + cloopdoc + '" class="docfile" id="olddocfile_' + cloopdoc + '" accept=".png,.jpeg,.jpg,.bmp,.gif,.pdf" onchange="validdocumentfile($(this),&apos;docfile' + cloopdoc + '&apos;)">\
                                          </span>\
                                      </span>\
                                      <input type="text" readonly="" placeholder="Enter File" id="Filetextdocfile' + cloopdoc + '" class="form-control docfile" name="Filetextdocfile_' + cloopdoc + '" value="">\
                                  </div>\
                              </div>\
                          </div>\
                      </div>\
                      <div class="col-md-1 addrowbutton pt-md pr-xs">\
                          <button type="button" class="btn btn-danger btn-raised remove_btn m-n" onclick="removeDocument(' + cloopdoc + ')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
                      </div>\
                      <div class="col-md-1 addrowbutton pt-md pr-xs">\
                          <button type="button" class="btn btn-primary btn-raised remove_btn m-n" onclick="addnewproduct()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>\
                           </div>\
                      </div>\
                  ';

    $("#adddocrow").append(datahtml);

}
function addcontectfield(id,countcontactno) {
    var countcontactno = $("#countcontactno").val();
    countcontactno++;
    $("#countcontactno").val(countcontactno);
    var datahtml = '<div class="col-md-4 pl-sm pr-sm visible-md visible-lg" id="contecremove'+countcontactno+'">\
    <div class="form-group" id="contactno_div">\
       <label for="contactno" class="col-md-4 control-label">Contact No <span class="mandatoryfield"> *</span></label>\
       <div class="col-md-6">\
          <input id="contactno" type="text" name="contactno'+id+'[]" class="form-control"  value="">\
       </div>\
        <div class="form-group col-md-3">\
            <button type="button"  onclick="addcontectfield('+ id + ','+ countcontactno +')"  class="addprodocitem btn-primary btn-xs" style="margin-top: 7px;"><i class="fa fa-plus"></i></button>\
            <button type="button" class="btn-danger btn-xs" onclick="removecontect(' + countcontactno + ')"><i class="fa fa-minus"></i></button>\
        </div>\
    </div>\
 </div>';

    $(".addcontectfilelddata"+id+"").after(datahtml);

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
                        <input id="firstname" type="text" name="firstname_' + cloopcount2 + '" class="form-control" value="1111111" onkeypress="return onlyAlphabets(event)">\
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
                        <input id="birthdate" type="text" name="birthdate_' + cloopcount2 + '" class="form-control date" value="" >\
                    </div>\
                </div>\
            </div>\
            <div class="col-md-4 pl-sm pr-sm visible-md visible-lg">\
                <div class="form-group" id="birthdate_div">\
                    <label for="birthdate" class="col-md-4 control-label">Birth Date</label>\
                    <div class="col-md-8">\
                        <input id="birthdate" type="text" name="birthdate_' + cloopcount2 + '" class="form-control date" value="" >\
                    </div>\
                </div>\
            </div>\
            <div class="col-md-4 pl-sm pr-sm visible-md visible-lg">\
                <div class="form-group" id="anniversarydate_div">\
                    <label for="anniversarydate" class="col-md-4 control-label">Anniversary Date</label>\
                    <div class="col-md-8">\
                        <input id="anniversarydate" type="text" name="anniversarydate_' + cloopcount2 + '" class="form-control date" value="" >\
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
                <button type="button" style="float:left; margin:10px 19px 0px 20px;" onclick="removecontectpaertion(\'contectrowdelete_'+ cloopcount2 + '\')" class="btn-danger">Remove</button>\
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


function addnewextracharges() {
    var extrachargescount = $("#extrachargescount").val();
    extrachargescount++;
    $("#extrachargescount").val(extrachargescount);
    var datahtml2 = '<div class="data" id="addnewextracharges_' + extrachargescount + '">\
    <div class="row">\
        <div class="col-sm-12">\
            <div class="form-group" id="paymenttype_div">\
            <div class="col-sm-5">\
                <input id="discount" type="text" name="discountamount" value="" class="form-control" >\
            </div>\
            <div class="col-sm-4">\
                <input id="discount" type="text" name="discountamount" value="" class="form-control" >\
            </div>\
            <div class="col-md-3 addrowbutton pt-md pr-xs" >\
                <button type="button" class="btn btn-primary btn-raised remove_btn m-n" onclick="addnewextracharges('+extrachargescount+')" style="padding: 3px 8px; "><i class="fa fa-plus"></i></button>\
                <button type="button" class="btn btn-danger btn-raised remove_btn m-n" onclick="removeextracharges(' + extrachargescount + ')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
            </div>\
            </div>\
        </div>\
    </div>';
    $("#addnewextracharges").append(datahtml2);
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
                    <input id="birthdate" type="text" name="birthdate_<?=$cloopcount?>" class="form-control pc100" value="" readonly>\
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

function removeextracharges(divid) {
    $("#addnewextracharges_" + divid).remove();
    $(".add_btn:last").show();
    if ($(".remove_btn:visible").length == 1) {
        $(".remove_btn:first").hide();
    }
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

{/* <div class="col-md-4 pl-sm pr-sm visible-md visible-lg">\
                <button type="button" style="float:left; margin:10px 19px 0px 20px;" onclick="removecontectpaertion(\'contectrowdelete_'+ cloopcount2 + '\')" class="btn-danger">Remove</button>\
                <div class="form-group" style="float:left; margin:10px 19px 0px 5px;">\
                <button type="button" class="addpro btn-primary" onclick="addnewcontect()">Add\
                Data</button>\
                </div>\
            </div>\ */}

function addnewinvoicetransaction() {

    var rowcount = parseInt($(".countinvoice:last").attr("id").match(/\d+/)) + 1;
    var datahtml = '<div class="countinvoice" id="countinvoice' + rowcount + '">\
                    <div class="row m-n">\
                        <div class="col-md-2">\
                            <div class="form-group" id="category'+ rowcount + '_div">\
                                <div class="col-sm-12">\
                                    <select id="category'+ rowcount + '" name="category[]" class="selectpicker form-control invoiceid" data-live-search="true" data-select-on-tab="true" data-size="6">\
                                        <option value="0">Select Category</option>\
                                    </select>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-2">\
                            <div class="form-group" id="product'+ rowcount + '_div">\
                                <div class="col-md-12">\
                                    <select id="product'+ rowcount + '" name="product[]" class="selectpicker form-control product" data-live-search="true" data-select-on-tab="true" data-size="6">\
                                        <option value="0">Select product</option>\
                                    </select>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-2">\
                            <div class="form-group" id="invoiceamount'+ rowcount + '_div">\
                                <div class="col-md-12">\
                                    <input type="text" id="invoiceamount'+ rowcount + '" class="form-control invoiceamount text-right" name="invoiceamount[]" value="" onkeypress="return decimal_number_validation(event, this.value, 10)">\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-1">\
                            <div class="form-group" id="remainingamount'+ rowcount + '_div">\
                                <div class="col-md-12">\
                                    <input type="text" id="remainingamount'+ rowcount + '" class="form-control text-right remainingamount" value="" >\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-1">\
                            <div class="form-group" id="remainingamount'+ rowcount + '_div">\
                                <div class="col-md-12">\
                                    <input type="text" id="remainingamount'+ rowcount + '" class="form-control text-right remainingamount pc100" value="" onkeypress="return decimal_number_validation(event, this.value, 10)" >\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-1">\
                            <div class="form-group" id="remainingamount'+ rowcount + '_div">\
                                <div class="col-md-12">\
                                    <input type="text" id="remainingamount'+ rowcount + '" class="form-control text-right remainingamount" value=""  >\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-1">\
                            <div class="form-group" id="productamount'+ rowcount + '_div">\
                                <div class="col-md-12">\
                                    <input type="text" id="productamount'+ rowcount + '" class="form-control text-right " value="" onkeypress="return decimal_number_validation(event, this.value, 10)">\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-2 pt-md">\
                            <button type="button" class="btn btn-danger btn-raised remove_invoice_btn m-n" onclick="removetransaction('+ rowcount + ')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
                            <button type="button" class="btn btn-primary btn-raised add_invoice_btn m-n" onclick="addnewinvoicetransaction()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>\
                        </div>\
                    </div>\
                </div>';

    $(".remove_invoice_btn:first").show();
    $(".add_invoice_btn:last").hide();
    $("#countinvoice" + (rowcount - 1)).after(datahtml);

    $("#invoiceid" + rowcount).selectpicker("refresh");
    
    $("#product" + rowcount).selectpicker("refresh");
    $("#category" + rowcount).selectpicker("refresh");

   


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
                            <div class="form-group" id="documentname'+ rowcount + '_div">\
                                <div class="col-md-12">\
                                    <input type="text" id="documentname'+ rowcount + '" class="form-control documentname"  placeholder="Enter Document Name" name="documentname[]" value="" >\
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
                            <button type="button" class="btn btn-danger btn-raised remove_doc_btn m-n" onclick="removedoc('+ rowcount + ')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
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

    changenetamounttotal();
}


function addnewex() {

    var rowcount = parseInt($(".countinvoicec:last").attr("id").match(/\d+/)) + 1;
    var datahtml = '<div class="countinvoicec" id="countinvoicec' + rowcount + '">\
                    <div class="row m-n">\
                        <div class="col-md-5">\
                            <div class="form-group" id="invoiceamount'+ rowcount + '_div">\
                                <div class="col-md-12">\
                                    <select id="product'+ rowcount + '" name="product[]" class="selectpicker form-control product" data-live-search="true" data-select-on-tab="true" data-size="6">\
                                        <option value="0">Extra Charges</option>\
                                    </select>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-3 col-sm-5">\
                          <div class="form-group" id="docfile' + rowcount + '">\
                            <div class="col-md-12">\
                                <input type="text" id="invoiceamount'+ rowcount + '" class="form-control invoiceamount"  placeholder="" name="invoiceamount[]" value="" onkeypress="return decimal_number_validation(event, this.value, 10)">\
                            </div>\
                          </div>\
                      </div>\
                        <div class="col-md-3 pt-md">\
                            <button type="button" class="btn btn-danger btn-raised remove_ex_btn m-n" onclick="removeex('+ rowcount + ')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
                            <button type="button" class="btn btn-primary btn-raised add_ex_btn m-n" onclick="addnewex()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>\
                        </div>\
                    </div>\
                </div>';

    $(".remove_ex_btn:first").show();
    $(".add_ex_btn:last").hide();
    $("#countinvoicec" + (rowcount - 1)).after(datahtml);
    $("#product" + rowcount).selectpicker("refresh");
}
function removeex(rowid) {

    
    $("#countinvoicec" + rowid).remove();

    $(".add_ex_btn:last").show();
    if ($(".remove_ex_btn:visible").length == 1) {
        $(".remove_ex_btn:first").hide();
    }


}
