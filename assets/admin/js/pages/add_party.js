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
    $(".addpro").hide();
    $(".addpro:last").show();
    $(".removepro").show();
    if ($(".removepro:visible").length == 1) {
        $(".removepro:first").hide();
    }
}

var edit_cityid = $('#edit_cityid').val();
function getcity() {
    if (edit_cityid) {
        $(".selectpicker").selectpicker("refresh");
        $('.editcityadd').selectpicker('val', edit_cityid);
    }
}
$(document).ready(function () {
    var edit_country = $('#edit_country').val();
    if(edit_country==""){
        edit_country = 0;
    }

    $('#countryid').val(edit_country).trigger('change');
    var base_url = $('#base_url').val();
    var edit_provinceid = $('#edit_provinceid').val();
    
    if (edit_provinceid != '') {

        var uurl = base_url + "rkinsite/Party/getstate";

        $.ajax({
            url: uurl,
            method: 'post',
            data: { country: edit_country },
            dataType: 'json',
            success: function (response) {
                var option = ' <option value="0">Select State</option>';
                $.each(response, function (index, data) {
                    option += '<option value="' + data['id'] + '">' + data['statename'] + '</option>';
                });
                $('#stateid').html(option);
                $(".selectpicker").selectpicker("refresh");
                $('#stateid').val(edit_provinceid).trigger('change')

            }
        });

    }



    $('.countryid').change(function () {
        var country = $(this).val();
        var uurl = base_url + "rkinsite/Party/getstate";

        // AJAX request
        $.ajax({
            url: uurl,
            method: 'post',
            data: { country: country },
            dataType: 'json',
            success: function (response) {
                var option = ' <option value="0">Select State</option>';
                $.each(response, function (index, data) {
                    option += '<option value="' + data['id'] + '">' + data['statename'] + '</option>';
                });
                $('#stateid').html(option);
               

                $('#stateid').val(edit_country).trigger('change')

            }
        });
    });

    $('#stateid').change(function () {
        var stat = $(this).val();
        var uurl = base_url + "rkinsite/Party/getcity";

        // AJAX request
        $.ajax({
            url: uurl,
            method: 'post',
            data: { stat: stat },
            dataType: 'json',
            success: function (response) {
                var option = ' <option value="0">Select City</option>';
                $.each(response, function (index, data) {
                    option += '<option value="' + data['id'] + '">' + data['cityname'] + '</option>';
                });
                $('#cityid').html(option);
                $(".selectpicker").selectpicker("refresh");
                getcity();
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




    $("#old_receipt_div").hide();
    $('#remove').click(function () {
        $('#removeoldreceipt').val('1');
    });

    $(".selectpicker").selectpicker("refresh");

    $('#countryid').on('change', function (e) {

        // $('#provinceid')
        //     .find('option')
        //     .remove()
        //     .end()
        //     .append('<option value="">Select Province</option>')
        //     .val('0');
        // $('#cityid')
        //     .find('option')
        //     .remove()
        //     .end()
        //     .append('<option value="">Select City</option>')
        //     .val('0');
        // $('#provinceid').selectpicker('refresh');
        // $('#cityid').selectpicker('refresh');
        // getprovince(this.value);
    });
    $('#provinceid').on('change', function (e) {
        // $('#cityid')
        //     .find('option')
        //     .remove()
        //     .end()
        //     .append('<option value="">Select City</option>')
        //     .val('0');
        // $('#cityid').selectpicker('refresh');
        // getcity(this.value);
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

    var websitename = $("#websitename").val().trim();
    var companyid = $("#companyid").val().trim();
    var partycode = $("#partycode").val().trim();
    var partytypeid = $("#partytypeid").val().trim();
    var openingdate = $("#openingdate").val().trim();
    var openingamount = $("#openingamount").val().trim();
    var password = $("#password").val().trim();
    var checkbox4 = $("#checkbox4").val().trim();
    var cloopcount = $("#cloopcount").val().trim();

    if ($('#checkbox4').is(":checked")) {
        checkbox4 = 3;
    } else {
        checkbox4 = 0;
    }

    var isvalidwebsitename = isvalidcompanyid = isvalidpartycode = isvalidpartytypeid = isvalidopeningdate = isvalidopeningamount = isvalidpassword = isvalidcontect = 0;


    PNotify.removeAll();
    if (websitename == '') {
        $("#websitename_div").addClass("has-error is-focused");
        new PNotify({ title: 'Please enter website name !', styling: 'fontawesome', delay: '3000', type: 'error' });
    } else {
        $("#websitename_div").removeClass("has-error is-focused");
        isvalidwebsitename = 1;
    }

    if (companyid == '' || companyid == 0) {
        $("#companyid_div").addClass("has-error is-focused");
        new PNotify({ title: 'Please select company!', styling: 'fontawesome', delay: '3000', type: 'error' });
    } else {
        $("#companyid_div").removeClass("has-error is-focused");
        isvalidcompanyid = 1;
    }

    if (gst == '') {
        $("#gst_div").addClass("has-error is-focused");
        new PNotify({ title: 'Please enter GST !', styling: 'fontawesome', delay: '3000', type: 'error' });
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

    if (partytypeid == '' || partytypeid == 0) {
        $("#partytypeid_div").addClass("has-error is-focused");
        new PNotify({ title: 'Please enter party type !', styling: 'fontawesome', delay: '3000', type: 'error' });
    } else {
        $("#partytypeid_div").removeClass("has-error is-focused");
        isvalidpartytypeid = 1;
    }



    if (pan == 0) {
        $("#partytype_div").addClass("has-error is-focused");
        new PNotify({ title: 'Please enter pan !', styling: 'fontawesome', delay: '3000', type: 'error' });
    } else {
        $("#partytype_div").removeClass("has-error is-focused");
        isvalidpan = 1;
    }

    // if (email == '') {
    //     $("#email_div").addClass("has-error is-focused");
    //     new PNotify({ title: 'Please enter email !', styling: 'fontawesome', delay: '3000', type: 'error' });
    // }

    var gocount = 1;
    while(gocount <= cloopcount){
        
        if($("#firstname_"+gocount)){

            var firstname = $("#firstname_"+gocount).val();
            var lastname = $("#lastname_"+gocount).val();
            var contact = $("input[name='contactno"+gocount+"[]']").val();
            var email = $("#email_"+gocount).val();
        
            if(firstname == ''){
                $("#firstname_"+gocount+"_div").addClass("has-error is-focused");
                new PNotify({ title: 'Please enter first name !', styling: 'fontawesome', delay: '3000', type: 'error' });
            }
            if(lastname == ''){
                $("#lastname_"+gocount+"_div").addClass("has-error is-focused");
                new PNotify({ title: 'Please enter last name !', styling: 'fontawesome', delay: '3000', type: 'error' });
            }
            if(contact == ''){
                $("#contactno_"+gocount+"_div").addClass("has-error is-focused");
                new PNotify({ title: 'Please enter contact no. !', styling: 'fontawesome', delay: '3000', type: 'error' });
            }
            if(email == ''){
                if(!ValidateEmail(email)){
                    $("#email_"+gocount+"_div").addClass("has-error is-focused");
                    new PNotify({title: 'Please enter valid email address !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    isvalidemail = 0;
                }else{
                    $("#email_"+gocount+"_div").removeClass("has-error is-focused");
                    isvalidemail = 1;
                    isvalidcontect=1;
                }
            }

            break;
        }
        gocount++;
    }

    if(isvalidcontect==1){
        var gocount = 1;
        while(gocount <= cloopcount){
            
            if($("#firstname_"+gocount)){

                var email = $("#email_"+gocount).val();
            
                if(email == ''){
                    if(!ValidateEmail(email)){
                        $("#email_"+gocount+"_div").addClass("has-error is-focused");
                        new PNotify({title: 'Please enter valid email address !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        isvalidemail = 0;
                    }else{
                        $("#email_"+gocount+"_div").removeClass("has-error is-focused");
                        isvalidemail = 1;
                    }
                }
            }
            gocount++;
        }
    }

    // if (openingdate == '') {
    //     $("#openingdate_div").addClass("has-error is-focused");
    //     new PNotify({ title: 'Please enter opening date !', styling: 'fontawesome', delay: '3000', type: 'error' });
    // } else {
    //     $("#openingdate_div").removeClass("has-error is-focused");
    //     isvalidopeningdate = 1;
    // }

    // if (openingamount == '') {
    //     $("#openingamount_div").addClass("has-error is-focused");
    //     new PNotify({ title: 'Please enter opening amount !', styling: 'fontawesome', delay: '3000', type: 'error' });
    // } else {
    //     $("#openingamount_div").removeClass("has-error is-focused");
    //     isvalidopeningamount = 1;
    // }
    
    isvalidpassword = 1;

    if (checkbox4 == 3) {
        isvalidpassword = 0;
        if (password == '') {
            $("#password_div").addClass("has-error is-focused");
            new PNotify({ title: 'Please enter password !', styling: 'fontawesome', delay: '3000', type: 'error' });
        } else {
        if(CheckPassword(password)==false){
            $("#password_div").addClass('has-error is-focused');
            new PNotify({title: 'Please enter password between 6 to 20 characters which contain at least one alphabetic, numeric & special character !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidpassword = 0;
            }else { 
            $("#password_div").removeClass("has-error is-focused");
            isvalidpassword = 1;
            }
        }
    }



    var c = 1;
    if (isvalidwebsitename && isvalidpartycode && isvalidpartytypeid && isvalidcompanyid && isvalidcontect && isvalidpassword == 1) {
        
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
                          <button type="button" class="btn btn-danger btn-raised remove_btndoc m-n" onclick="removeDocument(' + cloopdoc + ')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
                      </div>\
                      <div class="col-md-1 addrowbutton pt-md pr-xs">\
                          <button type="button" class="btn btn-primary btn-raised add_btndoc m-n" onclick="addnewproduct()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>\
                           </div>\
                      </div>\
                  ';

    $("#adddocrow").append(datahtml);
    $(".add_btndoc").hide();
    $(".add_btndoc:last").show();

    $(".add_btndoc:last").show();
    $(".remove_btndoc").show();
    if ($(".remove_btndoc:visible").length == 1) {
        $(".remove_btndoc:first").hide();
    }

}


function removeDocument(divid) {

    $("#countdocuments" + divid).remove();

    $(".add_btndoc").hide();
    $(".add_btndoc:last").show();
    if ($(".remove_btndoc:visible").length == 1) {
        $(".remove_btndoc:first").hide();
    }
}

$(".add_btndoc:last").show();
if ($(".remove_btndoc:visible").length == 1) {
    $(".remove_btndoc:first").hide();
}

function addcontectfield(id, countcontactno) {
    var countcontactno = $("#countcontactno").val();
    countcontactno++;
    $("#countcontactno").val(countcontactno);
    var datahtml = '<div class="col-md-4 pl-sm pr-sm visible-md visible-lg addcontectfilelddata'+id+'" id="contecremove' + countcontactno + '">\
    <div class="form-group" id="contactno_'+ id + '_div">\
       <label for="contactno" class="col-md-4 control-label">Contact No <span class="mandatoryfield"> *</span></label>\
       <div class="col-md-4">\
          <input id="contactno" type="text" name="contactno'+ id + '[]" class="form-control"  value="">\
       </div>\
        <div class="form-group col-md-4">\
            <button type="button"  onclick="addcontectfield('+ id + ',' + countcontactno + ')"  class="addprodocitem btn btn-primary btn-raised m-n add_btn" style="margin-top: 7px;"><i class="fa fa-plus"></i></button>\
            <button type="button" class="btn btn-danger btn-raised m-n removeprodocitem" onclick="removecontect('+ id + ',' + countcontactno + ')"><i class="fa fa-minus"></i></button>\
        </div>\
    </div>\
 </div>';

    $(".addcontectfilelddata" + id + "").after(datahtml);
    $("#contectrowdelete_" + id + " .addprodocitem").hide();
    $("#contectrowdelete_" + id + " .addprodocitem:last").show();

    $("#contectrowdelete_" + id + " .removeprodocitem").show();
    if ($("#contectrowdelete_" + id + " .removeprodocitem:visible").length == 1) {
        $("#contectrowdelete_" + id + " .removeprodocitem:first").hide();
    }
    
}
var cloopcount = $('#cloopcount').val();
var i = 0;
while(i<=cloopcount){
    var id = i;
    $("#contectrowdelete_" + id + " .addprodocitem").hide();
    $("#contectrowdelete_" + id + " .addprodocitem:last").show();

    $("#contectrowdelete_" + id + " .removeprodocitem").show();
    if ($("#contectrowdelete_" + id + " .removeprodocitem:visible").length == 1) {
        $("#contectrowdelete_" + id + " .removeprodocitem:first").hide();
    }
    i++;
}

function removecontect(id,divid) {

    var countcontactno = $("#countcontactno").val();
    countcontactno++;
    $("#countcontactno").val(countcontactno);
    $("#contecremove" + divid).remove();

    $("#contectrowdelete_" + id + " .addprodocitem").hide();
    $("#contectrowdelete_" + id + " .addprodocitem:last").show();

    $("#contectrowdelete_" + id + " .removeprodocitem").show();
    if ($("#contectrowdelete_" + id + " .removeprodocitem:visible").length == 1) {
        $("#contectrowdelete_" + id + " .removeprodocitem:first").hide();
    }
}

function addnewcontect() {
    var cloopcount2 = $("#cloopcount").val();
    var countcontactno2 = $("#countcontactno").val();

    cloopcount2++;
    countcontactno2++;
    $("#cloopcount").val(cloopcount2);
    $("#countcontactno").val(countcontactno2);
    var datahtml2 = '<div class="data" id="contectrowdelete_' + cloopcount2 + '">\
    <div class="row">\
    <div class="panel-heading">\
        <h2>Contact Detail ' + cloopcount2 + '</h2>\
        <div style="float:right; margin:0px 0px 0px 5px;">\
            <button type="button" class="addpro btn btn-primary btn-raised m-n" onclick="addnewcontect()"><i class="fa fa-plus"></i></button>\
            <button type="button"  onclick="removecontectpaertion(\'contectrowdelete_'+ cloopcount2 + '\')" class="btn btn-danger btn-raised removepro m-n"><i class="fa fa-minus"></i></button>\
            <div class="form-group" style="float:left; margin:0px 0px 0px 5px;">\
            </div>\
        </div>\
    </div>\
    <div class="clearfix"></div>\
            <div class="col-md-4 pl-sm pr-sm visible-md visible-lg">\
            <input type="hidden" name="contectid_' + cloopcount2 + '" value="0" id="contectid_' + cloopcount2 + '">\
                <div class="form-group" id="firstname_' + cloopcount2 + '_div">\
                    <label for="firstname" class="col-md-4 control-label">First Name <span class="mandatoryfield"> *</span></label>\
                    <div class="col-md-8">\
                        <input id="firstname" type="text" name="firstname_' + cloopcount2 + '" class="form-control" value="" onkeypress="return onlyAlphabets(event)">\
                    </div>\
                </div>\
            </div>\
            <div class="col-md-4 pl-sm pr-sm visible-md visible-lg">\
                <div class="form-group" id="lastname_' + cloopcount2 + '_div">\
                    <label for="lastname" class="col-md-4 control-label">Last Name <span class="mandatoryfield"> *</span></label>\
                    <div class="col-md-8">\
                        <input id="lastname" type="text" name="lastname_' + cloopcount2 + '" class="form-control" value="" onkeypress="return onlyAlphabets(event)">\
                    </div>\
                </div>\
            </div>\
            <div id="contecremove'+countcontactno2+'" class="col-md-4 pl-sm pr-sm visible-md visible-lg addcontectfilelddata' + cloopcount2 + '">\
                <div class="form-group" id="contactno_' + cloopcount2 + '_div">\
                    <label for="contactno" class="col-md-4 control-label">Contact No <span class="mandatoryfield"> *</span></label>\
                    <div class="col-md-4">\
                        <input id="contactno" type="text" name="contactno' + cloopcount2 + '[]" class="form-control" onkeypress="return isNumber(event)" maxlength="10" value="">\
                    </div>\
                    <div class="form-group col-md-4">\
                        <button type="button" onclick="addcontectfield(' + cloopcount2 + ','+countcontactno2+')" class="addprodocitem btn btn-primary add_btn btn-raised m-n"><i class="fa fa-plus"></i></button>\
                        <button type="button" class="btn btn-danger btn-raised m-n removeprodocitem" onclick="removecontect(' + cloopcount2 + ','+countcontactno2+')"><i class="fa fa-minus"></i></button>\
                    </div>\
                 </div>\
            </div>\
            <div class="col-md-4 pl-sm pr-sm visible-md visible-lg">\
                <div class="form-group" id="birthdate_div">\
                    <label for="birthdate" class="col-md-4 control-label">Birth Date</label>\
                    <div class="col-md-8">\
                        <input id="birthdate" type="text" name="birthdate_' + cloopcount2 + '" class="form-control bdate" value="" readonly>\
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
                <div class="form-group" id="email_' + cloopcount2 + '_div">\
                    <label for="email" class="col-md-4 control-label">Email <span class="mandatoryfield">*</span></label>\
                    <div class="col-md-8">\
                        <input id="email" type="text" name="email_' + cloopcount2 + '" class="form-control" value="">\
                    </div>\
                </div>\
            </div>\
            <div class="col-md-4 pl-sm pr-sm visible-md visible-lg">\
            </div>\
        </div>\
    </div>\
</div>';

    $("#addtarget").append(datahtml2);

    $(".addpro").hide();
    $(".addpro:last").show();
    $(".removepro").show();
    if ($(".removepro:visible").length == 1) {
        $(".removepro:first").hide();
    }

    $("#contectrowdelete_" + cloopcount2 + " .addprodocitem").hide();
    $("#contectrowdelete_" + cloopcount2 + " .addprodocitem:last").show();

    $("#contectrowdelete_" + cloopcount2 + " .removeprodocitem").show();
    if ($("#contectrowdelete_" + cloopcount2 + " .removeprodocitem:visible").length == 1) {
        $("#contectrowdelete_" + cloopcount2 + " .removeprodocitem:first").hide();
    }
}


$(".addpro").hide();
$(".addpro:last").show();
$(".removepro").show();
if ($(".removepro:visible").length == 1) {
    $(".removepro:first").hide();
}
