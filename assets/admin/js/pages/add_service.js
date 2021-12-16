$(document).ready(function () {
  $('#servicedate').datepicker({
      todayHighlight: true,
      format: 'dd/mm/yyyy',
      autoclose: true,
      todayBtn: "linked",
  });
  $('.warrantydate').datepicker({
      todayHighlight: true,
      format: 'dd/mm/yyyy',
      autoclose: true,
      todayBtn: "linked",
      clearBtn: true,
  });
  $('.duedate').datepicker({
    todayHighlight: true,
    format: 'dd/mm/yyyy',
    autoclose: true,
    todayBtn: "linked",
    clearBtn: true,
  });

  $(".add_btn").hide();
  $(".add_btn:last").show();
  $(".file_add_btn").hide();
  $(".file_add_btn:last").show();
  $("#serviceattachmentHeadings2").hide();

  if (ACTION == 1) {
    $(".servicerow").each(function (index) {
    var divid = $(this).attr("id").match(/\d+/);
    calculateAlert(divid);
    });
    if(filecount>1){
      $("#serviceattachmentHeadings2").show();
    }
  }

  $("[data-provide='partname']").each(function () {
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

$(document).on('keyup', '.price', function () {
  var divid = $(this).attr("id").match(/\d+/);
  calculateprice(divid);
});

$(document).on('keyup', '.tax', function () {
  var divid = $(this).attr("id").match(/\d+/);
  
  if(parseFloat(this.value)>=100){
    $("#tax"+divid).val("100");
  }
  calculateprice(divid);
});

function calculateprice(divid) {
  var amount = taxamount = 0;
  
  var price = $("#price"+divid).val();
  var tax = $("#tax"+divid).val();

  if(price!="" && parseFloat(price) > 0){
    amount = parseFloat(price);
    if(tax!="" && parseFloat(tax) > 0){

      taxamount = (parseFloat(amount) * parseFloat(tax) / 100);
      amount = (parseFloat(amount) + parseFloat(taxamount));
    }
  }
  $('#totalprice'+divid).val(parseFloat(amount).toFixed(2));
  $('#inputtaxamount'+divid).val(parseFloat(taxamount).toFixed(2));
  calculatetotal();
}

function calculatetotal() {
  var totalval = totaltax = 0;
  $(".totalprice").each(function (index) {
    var divid = $(this).attr("id").match(/\d+/);
    if ($(this).val() != "") {
      totalval += parseFloat($(this).val());
    }

    if($("#inputtaxamount"+divid).val() != "") {
      totaltax += parseFloat($("#inputtaxamount"+divid).val());
    }
  });
  
  $('#totaltaxamount').val(parseFloat(totaltax).toFixed(2));
  $('#totalpriceamount').val(parseFloat(totalval).toFixed(2));
  $('#totalprice').html(format.format(totalval));
}

function addnewrow(){
  rowcount = ++rowcount;
  $.html = '<div class="col-md-12 col-xs-12 pl-sm pr-sm servicerow" id="row'+rowcount+'">\
              <input type="hidden" id="partid'+rowcount+'" name="partid[]" value="">\
              <div class="col-md-3 col-sm-12 col-xs-12">\
                  <div class="form-group" id="partname_div_'+rowcount+'">\
                      <div class="col-md-12 pr-xs pl-xs">\
                      <label class="control-label">Parts Name <span class="mandatoryfield">*</span></label>\
                          <input type="text" id="partname'+rowcount+'" data-provide="partname" data-url="'+SearchPartsUrl+'" name="partname[]" class="form-control partname">\
                      </div>\
                  </div>\
              </div>\
              <div class="col-md-2 col-sm-6 col-xs-6">\
                  <div class="form-group" id="serialno_div_'+rowcount+'">\
                      <div class="col-md-12 pr-xs pl-xs">\
                      <label class="control-label">Serial No. <span class="mandatoryfield">*</span></label>\
                          <input type="text" id="serialno'+rowcount+'" name="serialno[]" class="form-control serialno">\
                      </div>\
                  </div>\
              </div>\
              <div class="col-md-2 col-sm-6 col-xs-6">\
                  <div class="form-group" id="warrantydate_div_'+rowcount+'">\
                      <div class="col-md-12 pr-xs pl-xs">\
                      <label class="control-label">Warranty End Date</label>\
                      <div class="input-group">\
                          <input type="text" id="warrantydate'+rowcount+'" name="warrantydate[]" class="form-control warrantydate" readonly>\
                            <span class="btn btn-default add-on datepicker_calendar_button" title="Date"><i class="fa fa-calendar fa-lg"></i></span>\
                        </div>\
                      </div>\
                  </div>\
              </div>\
              <div class="col-md-2 col-sm-6 col-xs-6">\
                  <div class="form-group" id="duedate_div_'+rowcount+'">\
                      <div class="col-md-12 pr-xs pl-xs">\
                      <label class="control-label">Due Date</label>\
                          <div class="input-group">\
                          <input type="text" id="duedate'+rowcount+'" class="form-control duedate" name="duedate[]" readonly>\
                            <span class="btn btn-default add-on datepicker_calendar_button" title="Date"><i class="fa fa-calendar fa-lg"></i></span>\
                          </div>\
                      </div>\
                  </div>\
              </div>\
              <div class="col-md-1 col-sm-6 col-xs-6">\
                  <div class="form-group" id="price_div_'+rowcount+'">\
                      <div class="col-md-12 pr-xs pl-xs">\
                      <label class="control-label">Price ('+CURRENCY_CODE+') <span class="mandatoryfield">*</span></label>\
                          <input type="text" id="price'+rowcount+'" class="form-control text-right price" onkeypress="return decimal_number_validation(event, this.value, 8)" name="price[]">\
                      </div>\
                  </div>\
              </div>\
              <div class="col-md-1 col-sm-6 col-xs-6">\
                  <div class="form-group" id="tax_div_'+rowcount+'">\
                      <div class="col-md-12 pr-xs pl-xs">\
                      <label class="control-label">Tax (%)</label>\
                          <input type="text" id="tax'+rowcount+'" name="tax[]" onkeypress="return decimal_number_validation(event, this.value, 3)" class="form-control text-right tax">\
                      </div>\
                  </div>\
              </div>\
              <div class="col-md-1 col-sm-6 col-xs-6">\
                  <div class="form-group" id="totalprice_div_'+rowcount+'">\
                      <div class="col-md-12 pr-xs pl-xs">\
                          <label class="control-label">Amount ('+CURRENCY_CODE+')</label>\
                          <input type="text" id="totalprice'+rowcount+'" class="form-control text-right totalprice" name="totalprice[]" readonly>\
                          <input type="hidden" id="inputtaxamount'+rowcount+'" class="inputtaxamount" name="inputtaxamount[]">\
                      </div>\
                  </div>\
              </div>\
              <div class="col-md-2 col-sm-6 col-xs-6">\
                  <div class="form-group" id="currentkmhr_div_'+rowcount+'">\
                      <div class="col-md-12 pr-xs pl-xs">\
                      <label class="control-label">Current Km / Hr</label>\
                          <input type="text" id="currentkmhr'+rowcount+'" class="form-control currentkmhr" onkeypress="return decimal_number_validation(event, this.value, 8)" name="currentkmhr[]">\
                      </div>\
                  </div>\
              </div>\
              <div class="col-md-2 col-sm-6 col-xs-6">\
                  <div class="form-group" id="changeafter_div_'+rowcount+'">\
                      <div class="col-md-12 pr-xs pl-xs">\
                      <label class="control-label">Change After (Km / Hr)</label>\
                          <input type="text" id="changeafter'+rowcount+'" class="form-control changeafter" onkeypress="return decimal_number_validation(event, this.value, 8)" name="changeafter[]">\
                      </div>\
                  </div>\
              </div>\
              <div class="col-md-2 col-sm-6 col-xs-6">\
                  <div class="form-group" id="alertkmhr_div_'+rowcount+'">\
                      <div class="col-md-12 pr-xs pl-xs">\
                      <label class="control-label">Alert Km / Hr</label>\
                          <input type="text" id="alertkmhr'+rowcount+'" class="form-control" name="alertkmhr[]" readonly>\
                      </div>\
                  </div>\
              </div>\
              <div class="col-md-1 col-sm-6 col-xs-6 p-n pt-xl">\
                  <div class="form-group" id="setalert_div_'+rowcount+'">\
                      <div class="col-md-12 pr-xs">\
                          <div class="checkbox">\
                              <input id="setalert'+rowcount+'" type="checkbox" value="1" name="setalert[]" class="checkradios" checked>\
                              <label for="setalert'+rowcount+'">Set Alert</label>\
                          </div>\
                      </div>\
                  </div>\
              </div>\
              <div class="col-md-2 col-sm-12 col-xs-6 mt-xl addrowbutton">\
                <div class="col-md-12">\
                    <div class="form-group" id="button'+rowcount+'_div">\
                        <button type="button" class="btn btn-danger btn-raised btn-sm remove_btn" onclick="removerow('+rowcount+')" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>\
                        <button type="button" class="btn btn-primary btn-raised add_btn" onclick="addnewrow()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>\
                    </div>\
                </div>\
              </div>\
              <div class="col-md-12 col-xs-12 pr-xs pl-xs"><hr></div>\
          </div>';

  $(".remove_btn:first").show();
  $(".add_btn:last").hide();
  $('#commonpanel').append($.html);

  $('#warrantydate' + rowcount).datepicker({
      todayHighlight: true,
      format: 'dd/mm/yyyy',
      autoclose: true,
      todayBtn: "linked",
      clearBtn: true,
  });
  $('#duedate' + rowcount).datepicker({
    todayHighlight: true,
    format: 'dd/mm/yyyy',
    autoclose: true,
    todayBtn: "linked",
    clearBtn: true,
  });
  $("[data-provide='partname']").each(function () {
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
}

function removerow(rowid){

  $('#row' + rowid).remove();
  
  $(".add_btn:last").show();
  if ($(".remove_btn:visible").length == 1) {
    $(".remove_btn:first").hide();
  }
  calculatetotal();
}

function validservicefile(obj, element, elethis) {
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
              new PNotify({title: 'Accept only Image and PDF Files !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

function addnewservicefile(){
  
  filecount = ++filecount;
  $.html = '<div class="col-md-6 servicefile" id="serivcefilecount'+filecount+'">\
              <div class="col-md-5">\
                    <div class="col-md-12 pl-sm pr-sm">\
                        <div class="form-group">\
                            <input type="text" id="filetitle'+filecount+'" name="filetitle['+filecount+']" placeholder="Enter File Title" class="form-control servicedocrow">\
                        </div>\
                    </div>\
                </div>\
                <div class="col-md-5">\
                    <div class="col-md-12 pl-sm pr-sm">\
                        <div class="form-group" id="servicefile'+filecount+'_div">\
                            <div class="input-group">\
                                <span class="input-group-btn" style="padding: 0 0px 0px 0px; ">\
                                    <span class="btn btn-primary btn-raised btn-file">Browse...\
                                        <input type="file" name="servicefile'+filecount+'" id="servicefile'+filecount+'" accept=".jpe,.jpeg,.jpg,.pbm,.png,.pdf" onchange="validservicefile($(this),\'servicefile'+filecount+'\',this)">\
                                    </span>\
                                </span>\
                                <input type="text" readonly="" id="Filetext'+filecount+'" name="Filetext[]" placeholder="Enter File" class="form-control servicedocrow">\
                            </div>\
                        </div>\
                    </div>\
                </div>\
                <div class="col-md-2 pl-n pr-n addrowbutton">\
                  <button type="button" class="btn btn-danger btn-raised btn-sm file_remove_btn" onclick="removeservicefile('+filecount+')" style="padding: 5px 10px;margin-top:14px;"><i class="fa fa-minus"></i></button>\
                  <button type="button" class="btn btn-primary btn-raised file_add_btn" onclick="addnewservicefile()" style="padding: 5px 10px;margin-top:14px;"><i class="fa fa-plus"></i></button>\
                </div>\
            </div>';

  $(".file_remove_btn:first").show();
  $(".file_add_btn:last").hide();

  $('#serviceattachment').append($.html);

  $("#serviceattachmentHeadings2").hide();
  if($(".servicefile").length>1){
    $("#serviceattachmentHeadings2").show();
  }
}

function removeservicefile(rowid) {
  
  $('#serivcefilecount' + rowid).remove();
  
  $(".file_add_btn:last").show();
  if ($(".file_remove_btn:visible").length == 1) {
    $(".file_remove_btn:first").hide();
  }
  if ($(".servicefile").length == 1) {
    $("#serviceattachmentHeadings2").hide();
  }
}

function resetdata() {

  $("#vehicleid_div").removeClass("has-error is-focused");
  $("#driverid_div").removeClass("has-error is-focused");
  $("#servicetypeid_div").removeClass("has-error is-focused");
  $("#garageid_div").removeClass("has-error is-focused");
  $("#servicedate_div").removeClass("has-error is-focused");
  $("#remarks_div").removeClass("has-error is-focused");

  if (ACTION == 1) {

  } else {
    $("#vehicleid").val(0);
    $("#driverid").val(0);
    $("#servicetypeid").val(0);
    $("#garageid").val(0);
    $("#servicedate").val('');
    $("#remarks").val('');
    
    $(".servicerow:not(:first)").remove();
    var divid = parseInt($(".servicerow:first").attr("id").match(/\d+/));
    $('#partname'+divid+',#serialno'+divid+',#warrantydate'+divid+',#duedate'+divid+',#price'+divid+',#tax'+divid+',#totalprice'+divid+',#currentkmhr'+divid+',#changeafter'+divid+',#alertkmhr'+divid).val("");
    $("#partname_div_"+divid+",#serialno_div_"+divid+",#price_div_"+divid+",#tax_div_"+divid+",#currentkmhr_div_"+divid+",#changeafter_div_"+divid+",#alertkmhr_div_"+divid).removeClass("has-error is-focused");
    $('#setalert'+divid).prop("checked", true);
    
    $("div.servicefile:not(:first)").remove();
    var divid = parseInt($("div.servicefile:first").attr("id").match(/\d+/));
    $('#Filetext'+divid).val("");

    $('.add_btn').show();
    $('.remove_btn').hide();

    $('.file_add_btn').show();
    $('.file_remove_btn').hide();

    $('.selectpicker').selectpicker('refresh');
  }
  // calculatetotal();
  $('html, body').animate({scrollTop: 0}, 'slow');
}

function checkvalidation(addtype=0){
  
  var vehicleid = $("#vehicleid").val();
  var driverid = $("#driverid").val();
  var servicetypeid = $("#servicetypeid").val();
  var garageid = $("#garageid").val();
  var servicedate = $("#servicedate").val();
  var remarks = $("#remarks").val().trim();
  

  var isvalidvehicleid =isvaliddriverid=isvalidservicetypeid=isvalidgarageid =isvalidservicedate= isvalidremarks = 0;
  var isvalidpartname = isvalidserialno = isvalidprice = isvalidcurrentkmhr = isvalidchangeafter = isvalidalertkmhr = 1;
  
  PNotify.removeAll();
  if(vehicleid == 0){
    $("#vehicleid_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select vehicle !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }else{
    $("#vehicleid_div").removeClass("has-error is-focused");
    isvalidvehicleid = 1;
  }
   
  if(driverid == 0){
    $("#driverid_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select driver !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }else{
    $("#driverid_div").removeClass("has-error is-focused");
      isvaliddriverid = 1;
  }

  if(servicetypeid == 0){
    $("#servicetypeid_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select service type !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }else{
    $("#servicetypeid_div").removeClass("has-error is-focused");
    isvalidservicetypeid = 1;
  }

  if(garageid == 0){
    $("#garageid_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select garage !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }else{
    $("#garageid_div").removeClass("has-error is-focused");
    isvalidgarageid = 1;
  }

  if(servicedate == ''){
    $("#servicedate_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select service date !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }else{
    $("#servicedate_div").removeClass("has-error is-focused");
      isvalidservicedate = 1;
  }

  var count = 0;
  var firstpartrow =  parseInt($(".servicerow:first").attr("id").match(/\d+/));
  $(".servicerow").each(function (index) {
    count++;
    var id = $(this).attr('id').match(/\d+/);
    var partname = $("#partname" + id).val();
    var serialno = $("#serialno" + id).val();
    var price = $("#price" + id).val();
    var setalert = ($("#setalert" + id).is(':checked'))?1:0;
    var currentkmhr = $("#currentkmhr" + id).val().trim();
    var changeafter = $("#changeafter" + id).val().trim();
    var alertkmhr = $("#alertkmhr" + id).val();

    if (partname !== '' || serialno !== '' || price !== '' || parseInt(id) == parseInt(firstpartrow)) {
      if (partname == '') {
          $("#partname_div_" + id).addClass("has-error is-focused");
          new PNotify({ title: 'Please enter ' + count + ' part name !',styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidpartname = 0;
      } else {
        if (partname.length<2) {
          $("#partname_div_" + id).addClass("has-error is-focused");
          new PNotify({title: count + ' part name require minimum 2 character !',styling: 'fontawesome',delay: '3000',type: 'error'});
        }else{
          $("#partname_div_" + id).removeClass("has-error is-focused");
        }
      }

      if (serialno == '') {
        $("#serialno_div_" + id).addClass("has-error is-focused");
        new PNotify({title: 'Please enter ' + count + ' serial number !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidserialno = 0;
      } else {
        $("#serialno_div_" + id).removeClass("has-error is-focused");
      }

      if (price == '') {
        $("#price_div_" + id).addClass("has-error is-focused");
        new PNotify({title: 'Please enter ' + count + ' price !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidprice = 0;
      } else {
        $("#price_div_" + id).removeClass("has-error is-focused");
      }

      if(setalert==1){
        if(currentkmhr==0){
          $("#currentkmhr_div_" + id).addClass("has-error is-focused");
          new PNotify({title: 'Please enter ' + count + ' current Km / Hr !',styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidcurrentkmhr = 0;
        }else{
          $("#currentkmhr_div_" + id).removeClass("has-error is-focused");
        }
        if(changeafter==0){
          $("#changeafter_div_" + id).addClass("has-error is-focused");
          new PNotify({title: 'Please enter ' + count + ' change after (Km / Hr) !',styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidchangeafter = 0;
        }else{
          $("#changeafter_div_" + id).removeClass("has-error is-focused");
        }
        if(alertkmhr==0){
          $("#alertkmhr_div_" + id).addClass("has-error is-focused");
          new PNotify({title: 'Please enter ' + count + ' alert Km / Hr !',styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidalertkmhr = 0;
        }else{
          $("#alertkmhr_div_" + id).removeClass("has-error is-focused");
        }
      }
    }else{
      $("#partname_div_" + id).removeClass("has-error is-focused");
      $("#serialno_div_" + id).removeClass("has-error is-focused");
      $("#price_div_" + id).removeClass("has-error is-focused");
      $("#currentkmhr_div_" + id).removeClass("has-error is-focused");
      $("#changeafter_div_" + id).removeClass("has-error is-focused");
      $("#alertkmhr_div_" + id).removeClass("has-error is-focused");
    }
  });

  if(remarks != "" && remarks.length < 2){
    $("#remarks_div").addClass("has-error is-focused");
    new PNotify({title: 'Remarks require minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }else{
    $("#remarks_div").removeClass("has-error is-focused");
    isvalidremarks = 1;
  }
  
  if(isvalidvehicleid == 1 && isvaliddriverid==1 && isvalidservicetypeid==1 && isvalidgarageid==1 && isvalidservicedate==1 && isvalidpartname ==1 && isvalidserialno ==1 && isvalidprice ==1 && isvalidremarks == 1 && isvalidcurrentkmhr==1 && isvalidchangeafter==1 && isvalidalertkmhr==1){
      
    var formData = new FormData($('#form-service')[0]);
    if(ACTION==0){
      var uurl = SITE_URL+"service/service-add";
      
      $.ajax({
        url: uurl,
        type: 'POST',
        data: formData,
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
        },
        success: function(response){
          var data = JSON.parse(response);
          if(data['error']==1){
            new PNotify({title: "Service successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
            if(addtype==1){
              resetdata();
            }else{
              setTimeout(function() { window.location=SITE_URL+"service"; }, 1500);
            }
          }else if(data['error']==2){
            new PNotify({title: "Service already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(data['error']==-2){
            new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#docfile"+data['id']+"_div").addClass("has-error is-focused");
          }else if(data['error']==-1){
            new PNotify({title: 'File is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE)+') !',styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#docfile"+data['id']+"_div").addClass("has-error is-focused");
          }else{
            new PNotify({title: 'Service not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
        complete: function(){
          $('.mask').hide();
          $('#loader').hide();
        },
        cache: false,
        contentType: false,
        processData: false
      });
    }else{
      
      var uurl = SITE_URL+"service/update-service";
      
      $.ajax({
        url: uurl,
        type: 'POST',
        data: formData,
        //async: false,
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
        },
        success: function(response){
          var data = JSON.parse(response);
          if(data['error']==1){
              new PNotify({title: "Service successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
              if(addtype==1){
                setTimeout(function() { window.location=SITE_URL+"service/add-service"; }, 1500);
              }else{
                setTimeout(function() { window.location=SITE_URL+"service"; }, 1500);
              }
          }else if(data['error']==2){
            new PNotify({title: "Service already exists.",styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(data['error']==-2){
            new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#docfile"+data['id']+"_div").addClass("has-error is-focused");
          }else if(data['error']==-1){
            new PNotify({title: 'File is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE)+') !',styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#servicefile"+data['id']+"_div").addClass("has-error is-focused");
          }else{
            new PNotify({title: 'Service not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
        complete: function(){
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

$(document).on('keyup', '.currentkmhr', function () {
  var divid = $(this).attr("id").match(/\d+/);
  calculateAlert(divid);
});

$(document).on('keyup', '.changeafter', function () {
  var divid = $(this).attr("id").match(/\d+/);
  calculateAlert(divid);
});

function calculateAlert(divid) {
  var alertkmhr = 0;
  
  var currentkmhr = $("#currentkmhr"+divid).val();
  var changeafter = $("#changeafter"+divid).val();
  alertkmhr = (currentkmhr!=""?parseFloat(currentkmhr):0);
    if(changeafter!="" && parseFloat(changeafter) > 0){
      alertkmhr = (parseFloat(alertkmhr) + parseFloat(changeafter));
    }
  $('#alertkmhr'+divid).val(parseFloat(alertkmhr).toFixed(2));
}