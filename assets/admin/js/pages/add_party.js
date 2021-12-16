var today = new Date();
var dd = String(today.getDate()).padStart(2, '0');
var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
var yyyy = today.getFullYear();
birthyear = today.getFullYear()-18;

today = dd + '/' + mm + '/' + yyyy;
dateofbirth = dd + '/' + mm + '/' + birthyear;

$(document).ready(function () {
  $("#education").each(function () {
    var $element = $(this);

    $maximumselectionsize = 25;
    if ($element.data("selectionlength") != undefined) {
      $maximumselectionsize = $element.data("selectionlength");
    }
    $element.select2({

      language: {

        inputTooLong: function (args) {
          // args.maximum is the maximum allowed length
          // args.input is the user-typed text
          return "You typed too much";
        },
        noResults: function () {
          return "No results found";
        },
        searching: function () {
          return "Searching...";
        },
        maximumSelected: function (args) {
          // args.maximum is the maximum number of items the user may select
          return "You can enter only 25 keywords";
        }
      },
      allowClear: true,
      minimumInputLength: 3,
      placeholder: $element.attr("placeholder"),
      tokenSeparators: [','],
      multiple: true,
      width: '100%',
      maximumSelectionSize: $maximumselectionsize,
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
      data: [],
      tags: true,
      initSelection: function (element, callback) {
        var id = $(element).val();
        if (id !== "") {
          data = [];
          var result = id.split(',');
          for (var prop in result) {

            keyword = {};
            keyword['id'] = result[prop]
            keyword['text'] = result[prop];
            data.push(keyword);
          }
          callback(data);
        }
      }

    });
  });

  getprovince($('#countryid').val());
  getcity($('#provinceid').val());
  $('#countryid').on('change', function (e) {
        
    $('#provinceid')
      .find('option')
      .remove()
      .end()
      .append('<option value="">Select Province</option>')
      .val('0')
    ;
    $('#cityid')
      .find('option')
      .remove()
      .end()
      .append('<option value="">Select City</option>')
      .val('0')
    ;
    $('#provinceid').selectpicker('refresh');
    $('#cityid').selectpicker('refresh');
    getprovince(this.value);
  });
  $('#provinceid').on('change', function (e) {
      
    $('#cityid')
      .find('option')
      .remove()
      .end()
      .append('<option value="">Select City</option>')
      .val('0')
    ;
    $('#cityid').selectpicker('refresh');
    getcity(this.value);
  });

  $(".allowlogin").hide();
  $('#allowforlogin').click(function() {
    if ($(this).is(':checked')) {
      $(".allowlogin").show();
    }else{
      $(".allowlogin").hide();
    }
  });

  $('.fromdate,.duedate').datepicker({
    todayHighlight: true,
    format: 'dd/mm/yyyy',
    todayBtn:"linked",
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
    todayBtn:"linked",
    orientation: "top left",
    clearBtn: true,
  });

  $(".add_btn").hide();
  $(".add_btn:last").show();
});

function validdocumentfile(obj,element){
  var val = obj.val();
  var id = obj.attr('id').match(/\d+/);
  var filename = obj.val().replace(/C:\\fakepath\\/i, '');

  switch(val.substring(val.lastIndexOf('.') + 1).toLowerCase()){
    case 'pdf': case 'gif': case 'bmp': case 'jpg': case 'jpeg': case 'png':
      
      isvalidimageorpdffile = 1;
      $("#isvalid"+element).val("1");
      $("#Filetext"+element).val(filename);
      $("#"+element+"_div").removeClass("has-error is-focused");
      break;
    default:
      $("#isvalid"+element).val("0");
      $("#Filetext"+element).val("");
      $("#"+element+"_div").addClass("has-error is-focused");
      new PNotify({title: 'Accept only Image and PDF Files !',styling: 'fontawesome',delay: '3000',type: 'error'});
      break;
  }
}
function addNewDocument(){
    
  var rowcount = parseInt($(".countdocuments:last").attr("id").match(/\d+/))+1;
  
  var datahtml = '<div class="col-sm-12 countdocuments pl-sm pr-sm" id="countdocuments'+rowcount+'">\
                      <input type="hidden" name="documentid['+rowcount+']" value="" id="documentid'+rowcount+'">\
                      <div class="col-md-2 col-sm-4">\
                          <div class="form-group" id="documenttype'+rowcount+'_div">\
                            <div class="col-sm-12 pr-xs pl-xs">\
                              <select id="documenttypeid'+rowcount+'" name="documenttypeid['+rowcount+']" class="selectpicker form-control documenttypeid" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                <option value="0">Select Document Type</option>\
                                '+DOCUMENT_TYPE_DATA+'\
                              </select>\
                            </div>\
                          </div>\
                      </div>\
                      <div class="col-md-1 col-sm-4">\
                          <div class="form-group" id="documentnumber'+rowcount+'_div">\
                              <div class="col-sm-12 pr-xs pl-xs">\
                                  <input id="documentnumber'+rowcount+'" name="documentnumber['+rowcount+']" placeholder="Enter Document Number" class="form-control documentnumber">\
                              </div>\
                          </div>\
                      </div>\
                      <div class="col-md-2 col-sm-4">\
                          <div class="form-group" id="fromdate'+rowcount+'_div">\
                              <div class="col-sm-12 pr-xs pl-xs">\
                                  <input type="text" id="fromdate'+rowcount+'" name="fromdate['+rowcount+']" placeholder="Enter Register Date" class="form-control fromdate" readonly>\
                              </div>\
                          </div>\
                      </div>\
                      <div class="col-md-2 col-sm-4">\
                          <div class="form-group" id="duedate'+rowcount+'_div">\
                              <div class="col-sm-12 pr-xs pl-xs">\
                                  <input type="text" id="duedate'+rowcount+'" name="duedate['+rowcount+']" placeholder="Enter Due Date" class="form-control duedate" readonly>\
                              </div>\
                          </div>\
                      </div>\
                      <div class="col-md-2 col-sm-4">\
                        <div class="form-group" id="licencetype'+rowcount+'_div">\
                          <div class="col-sm-12 pr-xs pl-xs">\
                            <select id="licencetype'+rowcount+'" name="licencetype['+rowcount+']" class="selectpicker form-control documenttypeid" data-live-search="true" data-select-on-tab="true" data-size="5">\
                              <option value="0">Select Licence Type</option>\
                              '+LICENCE_TYPE_DATA+'\
                            </select>\
                          </div>\
                        </div>\
                      </div>\
                      <div class="col-md-2 col-sm-4">\
                          <div class="form-group" id="docfile'+rowcount+'_div">\
                              <div class="col-sm-12 pr-xs pl-xs">\
                                  <input type="hidden" id="isvaliddocfile'+rowcount+'" value="0">\
                                  <input type="hidden" name="olddocfile['+rowcount+']" id="olddocfile'+rowcount+'" value="">\
                                  <div class="input-group" id="fileupload'+rowcount+'">\
                                      <span class="input-group-btn" style="padding: 0 0px 0px 0px;">\
                                          <span class="btn btn-primary btn-raised btn-file">\
                                          <i class="fa fa-upload"></i>\
                                              <input type="file" name="docfile'+rowcount+'" class="docfile" id="docfile'+rowcount+'" accept=".png,.jpeg,.jpg,.bmp,.gif,.pdf" onchange="validdocumentfile($(this),&apos;docfile'+rowcount+'&apos;)">\
                                          </span>\
                                      </span>\
                                      <input type="text" readonly="" placeholder="Enter File" id="Filetextdocfile'+rowcount+'" class="form-control docfile" name="Filetextdocfile['+rowcount+']" value="">\
                                  </div>\
                              </div>\
                          </div>\
                      </div>\
                      <div class="col-md-1 addrowbutton pt-md pr-xs">\
                          <button type="button" class="btn btn-danger btn-raised remove_btn m-n" onclick="removeDocument('+rowcount+')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
                          <button type="button" class="btn btn-primary btn-raised add_btn m-n" onclick="addNewDocument()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>\
                      </div>\
                  </div>';
  
  $(".remove_btn:first").show();
  $(".add_btn:last").hide();
  $("#countdocuments"+(rowcount-1)).after(datahtml);

  $('.fromdate,.duedate').datepicker({
    todayHighlight: true,
    format: 'dd/mm/yyyy',
    todayBtn:"linked",
    orientation: "top left",
    clearBtn: true,
  });

  $(".selectpicker").selectpicker("refresh");
}
function removeDocument(rowid){

  /* if(ACTION==1 && $('#productprocesscertificatesid'+rowid).val()!=null){
      var removeproductprocesscertificatesid = $('#removeproductprocesscertificatesid').val();
      $('#removeproductprocesscertificatesid').val(removeproductprocesscertificatesid+','+$('#productprocesscertificatesid'+rowid).val());
  } */
  $("#countdocuments"+rowid).remove();

  $(".add_btn:last").show();
  if ($(".remove_btn:visible").length == 1) {
      $(".remove_btn:first").hide();
  }
}

function resetdata() {

  $("#firstname_div").removeClass("has-error is-focused");
  $("#middlename_div").removeClass("has-error is-focused");
  $("#lastname_div").removeClass("has-error is-focused");
  $("#partycode_div").removeClass("has-error is-focused");
  $("#partytype_div").removeClass("has-error is-focused");
  $("#email_div").removeClass("has-error is-focused");
  $("#contactno1_div").removeClass("has-error is-focused");
  $("#contactno2_div").removeClass("has-error is-focused");
  $("#address_div").removeClass("has-error is-focused");
  $("#password_div").removeClass("has-error is-focused");
  $("#employeerole_div").removeClass("has-error is-focused");
  
  if (ACTION == 1) {
    $('#firstname').focus();
    $('#firstname_div').addClass('is-focused');

  } else {
    $("#firstname").val('').focus();
    $('#firstname_div').addClass('is-focused');

    $("#middlename,#lastname,#partycode,#email,#contactno1,#contactno2,#birthdate,#anniversarydate,#address,#password").val('');
    $("#partytypeid,#countryid,#provinceid,#cityid,#employeerole").val('0');
    $("#male").prop("checked", true);
    $('#allowforlogin').prop("checked", false);
    $(".allowlogin").hide();
    $("#education").select2("val", "");

    $(".countdocuments:not(:first)").remove();
    var divid = parseInt($(".countdocuments:first").attr("id").match(/\d+/));

    $('#documenttypeid'+divid+',#licencetype'+divid+',#isvaliddocfile'+divid).val("0");
    $('#documentnumber'+divid+',#fromdate'+divid+',#duedate'+divid+',#olddocfile'+divid+',#Filetextdocfile'+divid).val("");

    $('#documenttype'+divid+'_div').removeClass("has-error is-focused");
    $('#documentnumber'+divid+'_div').removeClass("has-error is-focused");
    $('#docfile'+divid+'_div').removeClass("has-error is-focused");
    
    $('.add_btn:first').show();
    $('.remove_btn').hide();

    $(".selectpicker").selectpicker("refresh");
  }
  $('html, body').animate({scrollTop: 0}, 'slow');
}
function checkvalidation(addtype=0){
  
    var firstname = $("#firstname").val().trim();
    var middlename = $("#middlename").val().trim();
    var lastname = $("#lastname").val().trim();
    var partycode = $("#partycode").val().trim();
    var partytypeid = $("#partytypeid").val();
    var email = $("#email").val().trim();
    var contactno1 = $("#contactno1").val().trim();
    var contactno2 = $("#contactno2").val().trim();
    var address = $("#address").val().trim();
    var password = $("#password").val();
    var employeerole = $("#employeerole").val();
    var allowforlogin = ($("#allowforlogin").is(':checked'))?1:0;
    
    var isvalidfirstname = isvalidmiddlename = isvalidlastname = isvalidpartycode = isvalidpartytypeid = isvalidemail = isvalidcontactno1 = isvalidcontactno2 = isvalidaddress = 0;
    var isvalidpassword = isvalidempolyeerole = isvaliddocumenttypeid = isvaliddocumentnumber = isvalidduedate = 1;

    PNotify.removeAll();
    if(firstname == ''){
      $("#firstname_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter first name !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else if(firstname.length < 2){
      $("#firstname_div").addClass("has-error is-focused");
      new PNotify({title: 'First name require minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else{
      $("#firstname_div").removeClass("has-error is-focused");
      isvalidfirstname = 1;
    }

    if(middlename == ''){
      $("#middlename_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter middle name !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else if(middlename.length < 2){
      $("#middlename_div").addClass("has-error is-focused");
      new PNotify({title: 'Middle name require minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else{
      $("#middlename_div").removeClass("has-error is-focused");
      isvalidmiddlename = 1;
    }

    if(lastname == ''){
      $("#lastname_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter last name !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else if(lastname.length < 2){
      $("#lastname_div").addClass("has-error is-focused");
      new PNotify({title: 'Last name require minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else{
      $("#lastname_div").removeClass("has-error is-focused");
      isvalidlastname = 1;
    }

    if(partycode == ''){
      $("#partycode_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter party code !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else if(partycode.length < 2){
      $("#partycode_div").addClass("has-error is-focused");
      new PNotify({title: 'Party code require minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else{
      $("#partycode_div").removeClass("has-error is-focused");
      isvalidpartycode = 1;
    }
   
    if(partytypeid == 0){
      $("#partytype_div").addClass("has-error is-focused");
      new PNotify({title: 'Please select party type !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else{
      $("#partytype_div").removeClass("has-error is-focused");
      isvalidpartytypeid = 1;
    }

    if(email == ''){
      $("#email_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter email !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }else{
      if(!ValidateEmail(email)){
        $("#email_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter valid email address !',styling: 'fontawesome',delay: '3000',type: 'error'});
      }else{
        $("#email_div").removeClass("has-error is-focused");
        isvalidemail = 1;
      }
    }

    if(contactno1 == ''){
      $("#contactno1_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter contact no. 1 !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }else{
      if(contactno1.length!=10){
        $("#contactno1_div").addClass("has-error is-focused");
        new PNotify({title: 'Contact no. 1 require 10 digits !',styling: 'fontawesome',delay: '3000',type: 'error'});
      }else{
        $("#contactno1_div").removeClass("has-error is-focused");
        isvalidcontactno1 = 1;
      }
    }
    if(contactno2 != '' && contactno2.length!=10){
      $("#contactno2_div").addClass("has-error is-focused");
      new PNotify({title: 'Contact no. 2 require 10 digits !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }else{
      $("#contactno2_div").removeClass("has-error is-focused");
      isvalidcontactno2 = 1;
    }

    if(address != '' && address.length<2){
      $("#address_div").addClass("has-error is-focused");
      new PNotify({title: 'Address require minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }else{
      $("#address_div").removeClass("has-error is-focused");
      isvalidaddress = 1;
    }
    if(allowforlogin==1){
      if(password==''){
        $("#password_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter password !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidpassword = 0;
      }else{
        if(CheckPassword(password)==false){
          $("#password_div").addClass('has-error is-focused');
          new PNotify({title: 'Please enter password between 6 to 20 characters which contain at least one alphabetic, numeric & special character !',styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidpassword = 0;
        }else { 
          $("#password_div").removeClass("has-error is-focused");
        }
      }
      if(employeerole==0){
        $("#employeerole_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter employee role !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidempolyeerole = 0;
      }else{
        $("#employeerole_div").removeClass("has-error is-focused");
      }
    }

    var c=1;
    // var firstdocid = $('.countdocuments:first').attr('id').match(/\d+/);
    $('.countdocuments').each(function(){
        var id = $(this).attr('id').match(/\d+/);
      
        if($("#documenttypeid"+id).val() > 0 || $("#documentnumber"+id).val() != ""){

          if($("#documenttypeid"+id).val() == 0){
            $("#documenttype"+id+"_div").addClass("has-error is-focused");
            new PNotify({title: 'Please select '+(c)+' document type !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvaliddocumenttypeid = 0;
          }else {
            $("#documenttype"+id+"_div").removeClass("has-error is-focused");
          }
          if($("#documentnumber"+id).val() == ""){
            $("#documentnumber"+id+"_div").addClass("has-error is-focused");
            new PNotify({title: 'Please enter '+(c)+' document number !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvaliddocumentnumber = 0;
          }else {
            $("#documentnumber"+id+"_div").removeClass("has-error is-focused");
          }
          if($("#duedate"+id).val() != "" && $("#fromdate"+id).val() != ""){
            var RegDate = $("#fromdate"+id).val().split("/");
            RegDate = new Date(RegDate[2], RegDate[1]-1, RegDate[0]);
            var rdd = String(RegDate.getDate()).padStart(2, '0');
            var rmm = String(RegDate.getMonth() + 1).padStart(2, '0'); //January is 0!
            var ryyyy = RegDate.getFullYear();
            RegDate = ryyyy+"-"+rmm+"-"+rdd;
           
            var DueDate = $("#duedate"+id).val().split("/");
            DueDate = new Date(DueDate[2], DueDate[1]-1, DueDate[0]);
            var dd = String(DueDate.getDate()).padStart(2, '0');
            var mm = String(DueDate.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = DueDate.getFullYear();
            DueDate = yyyy+"-"+mm+"-"+dd;
            
            if(DueDate < RegDate){
              $("#duedate"+id+"_div").addClass("has-error is-focused");
              new PNotify({title: 'Please select '+(c)+' due date greater than of register date !',styling: 'fontawesome',delay: '3000',type: 'error'});
              isvalidduedate = 0;
            }else {
              $("#duedate"+id+"_div").removeClass("has-error is-focused");
            }
          }else{
            $("#duedate"+id+"_div").removeClass("has-error is-focused");
          }
        }else{
          $("#documenttype"+id+"_div").removeClass("has-error is-focused");
          $("#documentnumber"+id+"_div").removeClass("has-error is-focused");
          $("#duedate"+id+"_div").removeClass("has-error is-focused");
        }
        c++;
    });

    if(isvalidfirstname == 1 && isvalidmiddlename == 1 && isvalidlastname == 1 && isvalidpartycode == 1 && isvalidpartytypeid == 1 && isvalidemail == 1 && isvalidcontactno1 == 1 && isvalidcontactno2 == 1 && isvalidaddress == 1 && isvalidempolyeerole == 1 && isvalidpassword == 1 && isvaliddocumenttypeid == 1 && isvaliddocumentnumber == 1 && isvalidduedate == 1){
        
      var formData = new FormData($('#party-form')[0]);
      if(ACTION==0){
        var uurl = SITE_URL+"party/party-add";
        
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
              new PNotify({title: "Party successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
              if (addtype == 1) {
                resetdata();
              } else {
                setTimeout(function () {window.location = SITE_URL + "party";}, 1500);
              }
            }else if(data['error']==2){
              new PNotify({title: "Party code or email or contact number already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
              $("#name_div").addClass("has-error is-focused");
            }else if(data['error']==-2){
              new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
              $("#docfile"+data['id']+"_div").addClass("has-error is-focused");
            }else if(data['error']==-1){
              new PNotify({title: 'File is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE)+') !',styling: 'fontawesome',delay: '3000',type: 'error'});
              $("#docfile"+data['id']+"_div").addClass("has-error is-focused");
            }else{
              new PNotify({title: 'Party not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
        
        var uurl = SITE_URL+"party/update-party";
        
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
              new PNotify({title: "Party successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
              if (addtype == 1) {
                setTimeout(function () { window.location = SITE_URL + "party/add-party";  }, 1500);
              } else {
                setTimeout(function () {window.location = SITE_URL + "party";}, 1500);
              }
            }else if(data['error']==2){
              new PNotify({title: "Party code or email or contact number already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
            }else if(data['error']==-2){
              new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
              $("#docfile"+data['id']+"_div").addClass("has-error is-focused");
            }else if(data['error']==-1){
              new PNotify({title: 'File is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE)+') !',styling: 'fontawesome',delay: '3000',type: 'error'});
              $("#docfile"+data['id']+"_div").addClass("has-error is-focused");
            }else{
                new PNotify({title: 'Party not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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