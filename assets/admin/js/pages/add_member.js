var today = new Date();
var dd = String(today.getDate()).padStart(2, '0');
var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
var yyyy = today.getFullYear();
birthyear = today.getFullYear()-18;

today = dd + '/' + mm + '/' + yyyy;
dateofbirth = dd + '/' + mm + '/' + birthyear;
$(document).ready(function() {

  generateTabIndex();
  if($('#oldprofileimage').val()!=''){
    var $imageupload = $('.imageupload');
    $imageupload.imageupload({
      url: SITE_URL,
      type: '1',
      allowedFormats: [ 'jpg', 'jpeg', 'png','ico']
    });
  }else{
    var $imageupload = $('.imageupload');
    $imageupload.imageupload({
      url: SITE_URL,
      type: '0',
      allowedFormats: [ 'jpg', 'jpeg', 'png','ico']
    });
  }

  $('#balancedate').datepicker({
    todayHighlight: true,
    format: 'dd/mm/yyyy',
    autoclose: true,
    todayBtn:"linked",
    container:'#memberform',
  }); 
  $('#anniversarydate').datepicker({
    todayHighlight: true,
    format: 'dd/mm/yyyy',
    autoclose: true,
    todayBtn:"linked",
    clearBtn:true,
  }); 
  
  $('.birthdate').datepicker({
    todayHighlight: true,
    format: 'dd/mm/yyyy',
    autoclose: true,
    orientation:"bottom",
    endDate: dateofbirth,
  });
  $('.annidate').datepicker({
    todayHighlight: true,
    format: 'dd/mm/yyyy',
    autoclose: true,
    todayBtn:"linked",
    orientation:"bottom",
    clearBtn: true
  });
  $('#remove').click(function(){
    $('#removeoldImage').val('1');
  });

  getprovince($('#countryid').val());
  getcity($('#provinceid').val());
  
  getsellerchannel($('#channelid').val(),sellerchannelid);
  getsalesperson($('#channelid').val());
  getmembers($('#parentchannelid').val(),'parentmemberid',parentmemberid);
  getmembers(sellerchannelid,'sellermemberid',sellermemberid);
  if(ACTION == 1){
    var channelid = $("#channelid").val();
    // console.log(channelid);
    if(channelid!=0 && GUESTCHANNELID!=channelid){
      //getmembers(channelid,memberid);
    }else{
      $("#member_div").hide();
    }
    if(CRM_SETTING==1){
      getarea($('#cityid').val());
    }
  }
  $("#channelid").change(function(){
    var channelid = $(this).val();
    getsellerchannel(channelid);
    getsalesperson(channelid);

  });
  $("#parentchannelid").change(function(){
    var channelid = $(this).val();
    
    // var memberid = $("#memberid").val();
    if(GUESTCHANNELID==channelid){
      $('#parentmemberid')
        .find('option')
        .remove()
        .end()
        .append('<option value="">Select '+Member_label+'</option>')
        .val('whatever')
      ;
      $(".selectpicker").selectpicker("refresh");
    }else{
      getmembers(channelid,'parentmemberid');
    }
  });
  $("#sellerchannelid").change(function(){
    var channelid = $(this).val();
    
    getmembers(channelid,'sellermemberid');
    if($("#parentmemberid").val()==-1){
      $("#parentchannelid").val(channelid).selectpicker("refresh");
      getmembers(channelid,'parentmemberid');
    }
  });
  $("#sellermemberid").change(function(){
    var sellermemberid = $(this).val();
    if($("#parentmemberid").val()==""){
      $("#parentmemberid").val(sellermemberid).selectpicker("refresh");
    }
  });
  $('#membercode').bind('keyup blur',function(){ 
    var node = $(this);
    node.val(node.val().replace(/[^a-zA-Z0-9]/g,'').toUpperCase() ); 
  });
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
    $('#areaid')
        .find('option')
        .remove()
        .end()
        .append('<option value="0">Select Area</option>')
        .val('0')
    ;
    $('#provinceid').selectpicker('refresh');
    $('#cityid').selectpicker('refresh');
    $('#areaid').selectpicker('refresh');
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
      $('#areaid')
        .find('option')
        .remove()
        .end()
        .append('<option value="0">Select Area</option>')
        .val('0')
      ;
      $('#cityid').selectpicker('refresh');
      $('#areaid').selectpicker('refresh');
      getcity(this.value);
  });
    
  var memberid = $("#id").val();
  $("#companyname").change(function(){
    if($("#companyname").val()!=""){      
      // checkduplicate("companyname",$("#companyname").val(),memberid,$("#name").val());
    }else{
      $("#companynameduplicatemessage").hide();
    }
  });


  $('#cityid').change(function(){
    var cityid = $("#cityid").val();
    
    if(CRM_SETTING==1){
      if(cityid!=0){
        getarea(cityid);
      }else{
        $('#areaid')
            .find('option')
            .remove()
            .end()
            .append('<option value="0">Select Area</option>')
            .val('whatever')
        ;
        $('#areaid').selectpicker('refresh');
      }
    }
  });
  
  $('#defaultcashorbankid').on('change', function (e) {
    var value = $('#defaultcashorbankid option:selected').text()

    $("#defaultbankmethod option").removeAttr('disabled');
    if(value.toLowerCase().trim() == "cash" && this.value!=0){
        $("#defaultbankmethod option[value!=1][value!=0]").prop('disabled', true);
        $("#defaultbankmethod").val('1');
    }else if(value.toLowerCase().trim() != "cash" && this.value!=0){
        $("#defaultbankmethod option[value=1]").prop('disabled', true);
        $("#defaultbankmethod").val('0');
    }else{
        $("#defaultbankmethod").val('0');
    }
    $("#defaultbankmethod").selectpicker('refresh');
  });
  if(ACTION==1){
    if(defaultbankmethod > 1){
        $("#defaultbankmethod option[value=1]").prop('disabled', true);
    }else{
        $("#defaultbankmethod option[value!=1][value!=0]").prop('disabled', true);
    }
    $("#defaultbankmethod").selectpicker('refresh');
  }

  $('.number').on('keypress', function (evt) {
    evt = (evt) ? evt : window.event;
      var charCode = (evt.which) ? evt.which : evt.keyCode;
      if (charCode > 31 && (charCode < 48 || charCode > 57)) {
       return false;
     }
     return true;
  }).on('focusout', function (e) {
    var $this = $(this);
    $this.val($this.val().replace(/[^0-9]/g, ''));
  }).on('paste', function (e) {
    var $this = $(this);
    setTimeout(function () {
        $this.val($this.val().replace(/[^0-9]/g, ''));
    }, 5);
  });

 
});
$(document).on("keyup","#email", function(){
  $(".email:first").val(this.value);
  $('#addressemail').val(this.value);
});  
$(document).on("keyup","#mobileno", function(){
  $(".mobileno:first").val(this.value);
  $('#addressmobile').val(this.value);
});  
$(document).on("change",".mobileno", function(){
  var count = $(this).attr('id').match(/\d+/); 
  if(this.value!=""){  
    var memberid = $("#id").val();
    checkduplicate("mobileno",this.value,memberid,count);
  }else{
    $("#mobilenoduplicatemessage"+count).hide();
  }
});

$(document).on("change",".email", function(){
  var count = $(this).attr('id').match(/\d+/); 
  if(this.value!=""){  
    var memberid = $("#id").val();
    checkduplicate("email",this.value,memberid,count);
  }else{
    $("#emailduplicatemessage"+count).hide();
  }
});

$(document).on('paste', '.mobileno', function(e) {
  e.preventDefault();
  var withoutSpaces = e.originalEvent.clipboardData.getData('Text');
  withoutSpaces = withoutSpaces.replace(/\s+/g, '');
  $(this).val(withoutSpaces);
  
  var count = this.id.match(/\d+/); 
  if(withoutSpaces!=""){  
    var memberid = $("#id").val();
    checkduplicate("mobileno",withoutSpaces,memberid,count);
  }else{
    $("#mobilenoduplicatemessage"+count).hide();
  }
});
$(document).on('paste', '.email', function(e) {
  e.preventDefault();
  var withoutSpaces = e.originalEvent.clipboardData.getData('Text');
  withoutSpaces = withoutSpaces.replace(/\s+/g, '');
  $(this).val(withoutSpaces);
  
  var count = this.id.match(/\d+/); 
  if(withoutSpaces!=""){  
    var memberid = $("#id").val();
    checkduplicate("email",withoutSpaces,memberid,count);
  }else{
    $("#emailduplicatemessage"+count).hide();
  }
});

function checkduplicate(duplicatetype,fieldvalue,memberid,id) {
  id = id || "";
  if(memberid==""){
    memberid=0;
  }
  var channelid = $("#channelid").val();
  $.ajax({
    url: SITE_URL+"member/checkduplicate",
    type: 'POST',
    data: {"type":duplicatetype,"value":fieldvalue,"memberid":memberid,"channelid":channelid},
    beforeSend: function(){
    },
    success: function(response){
      
      if(parseInt(response)>0){
        var msg = "";
        if(duplicatetype=="mobileno"){
          msg = "Mobile number already exist !";
        }else if(duplicatetype=="email"){
          msg = "Email already exist !";
        }else if(duplicatetype=="companyname"){
          msg = "Company name already exist !";
        }
        
        $("#"+duplicatetype+"duplicatemessage"+id).html(msg);
        $("#"+duplicatetype+"duplicatemessage"+id).show();
      }else{
        $("#"+duplicatetype+"duplicatemessage"+id).html("");
      }

    },
    error: function(xhr) {
    },
    complete: function(){
    },
  });
}

function setwebsitelink(name){
  $('#websitelink').val(name.toLowerCase().replace(/ /g,'-').replace(/[^\w\/_-]+/g,''));
} 
function resetdata(){

  $("#channelid_div").removeClass("has-error is-focused");
  $("#parentchannelid_div").removeClass("has-error is-focused");
  $("#parentmember_div").removeClass("has-error is-focused");
  $("#sellerchannelid_div").removeClass("has-error is-focused");
  $("#sellermember_div").removeClass("has-error is-focused");
  $("#name_div").removeClass("has-error is-focused");
  $("#membercode_div").removeClass("has-error is-focused");
  $("#email_div").removeClass("has-error is-focused");
  $("#password_div").removeClass("has-error is-focused");
  $("#mobile_div").removeClass("has-error is-focused");
  $("#country_div").removeClass("has-error is-focused");
  $("#secondarymobileno_div").removeClass("has-error is-focused");
  $("#province_div").removeClass("has-error is-focused");
  $("#city_div").removeClass("has-error is-focused");
  $("#gstno_div").removeClass("has-error is-focused");
  $("#debitlimit_div").removeClass("has-error is-focused");
  $("#minimumstocklimit_div").removeClass("has-error is-focused");
  $("#paymentcycle_div").removeClass("has-error is-focused");
  $("#addressname_div").removeClass("has-error is-focused");
  $("#addressemail_div").removeClass("has-error is-focused");
  $("#addressmobile_div").removeClass("has-error is-focused");
  $("#memberaddress_div").removeClass("has-error is-focused");
  $("#postalcode_div").removeClass("has-error is-focused");
  $("#defaultbankmethod_div").addClass("has-error is-focused");

  if(CRM_SETTING==1){
    $("#companyname_div").removeClass("has-error is-focused");
    $("#website_div").removeClass("has-error is-focused");
    $("#areaid_div").removeClass("has-error is-focused");
    $("#employee_div").removeClass("has-error is-focused");
  }

  if(MEMBER_LAT_LONG==1){
    $("#latitude_div").removeClass("has-error is-focused");
    $("#longitude_div").removeClass("has-error is-focused");
  }

  if(ACTION==1){
    if($('#oldprofileimage').val()!=''){
      var $imageupload = $('.imageupload');
      $('.imageupload img').attr('src',profileimgpath+'/'+$('#oldprofileimage').val());
      $imageupload.imageupload({
        url: SITE_URL,
        type: '1'
      });
    }else{
      $('.imageupload').imageupload({
        url: SITE_URL,
        type: '0',
      });
    }
    $('#removeoldImage').val('0');
  }else{

    $("#channelid").val('0'); 
    $("#parentchannelid").val('0'); 
    $("#parentmemberid").val('0'); 
    $("#sellerchannelid").val('0'); 
    $("#sellermemberid").val('0'); 
    $("#name").val("");
    $("#email").val("");
    $("#mobileno").val("");
    $("#countrycodeid").val(countrycodeid);
    $("#secondarymobileno").val("");
    $("#secondarycountrycodeid").val(countrycodeid);
    $("#password").val("");
    $("#membercode").val("");
    $("#countryid").val(countryid);
    $("#provinceid").val(0);
    $("#cityid").val(0);
    $("#gstno").val("");
    $("#debitlimit").val("");
    $("#minimumstocklimit").val("");
    $("#paymentcycle").val("");
    $('#defaultcashorbankid').val(0);
    $('#defaultbankmethod').val(0);

    if(CRM_SETTING==1){
      $("#companyname").val("");
      $("#website").val("");
      $("#areaid").val("");
      $("#employee").val("");
    }

    if(MEMBER_LAT_LONG==1){
      $("#latitude").val("");
      $("#longitude").val("");
    }
    getprovince(countryid);
    $('.imageupload').imageupload({
      url: SITE_URL,
      type: '0',
    });

    $(".selectpicker").selectpicker("refresh");
    $('#yes').prop("checked", true);
  }
  $('html, body').animate({scrollTop:0},'slow');
}

function getChannelUsercount(channelid){
  var channelusercount = 0;
  $.ajax({
    url: SITE_URL+"member/getChannelUsercount",
    type: 'POST',
    data: {"channelid":channelid},
    async: false,
    success: function(response){
      // console.log(1);
      channelusercount = response;
      
    },
    error: function(xhr) {
    },
    
  });
  //console.log(channelusercount);
  return channelusercount;
}

function checkvalidation(){

  var channelid = $("#channelid").val();
  var name = $("#name").val().trim();
  var parentchannelid = $("#parentchannelid").val();
  var parentmemberid = $("#parentmemberid").val();
  var sellerchannelid = $("#sellerchannelid").val();
  var sellermemberid = $("#sellermemberid").val();
  var roleid = $("#roleid").val();
  var membercode = $("#membercode").val();
  var countryid = $("#countryid").val();
  var provinceid = $("#provinceid").val();
  var cityid = $("#cityid").val();
  var gstno = $("#gstno").val();
  var panno = $("#panno").val();
  var email = $("#email").val().trim();
  var countrycodeid = $("#countrycodeid").val();    
  var mobileno = $("#mobileno").val().trim();
  var secondarycountrycodeid = $("#secondarycountrycodeid").val();    
  var secondarymobileno = $("#secondarymobileno").val().trim();
  var password = $("#password").val();  
  var addressname = $("#addressname").val();  
  var addressemail = $("#addressemail").val();  
  var addressmobile = $("#addressmobile").val();  
  var postalcode = $("#postalcode").val();  
  var address = $("#memberaddress").val();  
 
  var companyname = (CRM_SETTING==1?$("#companyname").val().trim():"");
  var website = (CRM_SETTING==1?$("#website").val():"");
  var leadsource = (CRM_SETTING==1?$("#leadsource").val():"");
  var defaultcashorbankid = $("#defaultcashorbankid").val();    
  var defaultbankmethod = $("#defaultbankmethod").val();

  var latitude = $("#latitude").val();
  var longitude = $("#longitude").val();

  var isvalidparentchannelid = isvalidsellerchannelid = isvalidmembercode = isvalidcountryid = isvalidprovinceid = isvalidcityid =
      isvalidname = isvalidemail = isvalidmobileno= isvalidcountrycodeid = isvalidchannelid = isvalidnoofuserinchannel =  0;
  var isvalidparentmemberid = isvalidsellermemberid = isvalidgstno = isvalidpassword = isvalidpanno = isvalidsecondarymobileno = isvalidsecondarycountrycodeid = isvalidleadsource = isvalidwebsite = isvalidcompanyname = isvalidmemberrole = isvalidaddressname = isvalidaddressemail = isvalidaddressmobile = isvalidpostalcode = isvalidaddress = isvaliddefaultbankmethod = isvalidlatitude = isvalidlongitude = 1;

  if(channelid==0) {
      $("#channelid_div").addClass("has-error is-focused");
      new PNotify({title: 'Please select new '+member_label+' channel !',styling: 'fontawesome',delay: '3000',type: 'error'});
  } else {
      $("#channelid_div").removeClass("has-error is-focused");
      isvalidchannelid = 1;

      var count = getChannelUsercount(channelid);
      count = parseInt(parseInt(count) + parseInt(($('#yes').prop("checked")==true)?1:0));
      
      if(parseInt(count) > parseInt(NOOFUSERINCHANNEL)){
        new PNotify({title: 'Maximum '+parseInt(NOOFUSERINCHANNEL)+' '+member_label+' allowed in this channel !',styling: 'fontawesome',delay: '3000',type: 'error'});
        
        isvalidnoofuserinchannel = 0;
      }else{
        isvalidnoofuserinchannel = 1;
      }
      
  }

  if(isvalidnoofuserinchannel == 1){

      if(sellerchannelid==-1) {
          $("#sellerchannelid_div").addClass("has-error is-focused");
          new PNotify({title: 'Please select seller channel !',styling: 'fontawesome',delay: '3000',type: 'error'});
      } else {
          $("#sellerchannelid_div").removeClass("has-error is-focused");
          isvalidsellerchannelid = 1;
      }

      if(sellermemberid==0 && sellerchannelid!=0) {
          $("#sellermember_div").addClass("has-error is-focused");
          new PNotify({title: 'Please select seller '+member_label+' !',styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidsellermemberid = 0;
      } else {
          $("#sellermember_div").removeClass("has-error is-focused");
      }

      /* if(parentchannelid==-1) {
        $("#parentchannelid_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select parent channel !',styling: 'fontawesome',delay: '3000',type: 'error'});
      } else {
          $("#parentchannelid_div").removeClass("has-error is-focused");
          isvalidparentchannelid = 1;
      } */

      if(parentmemberid==0 && parentchannelid>0) {
          $("#parentmember_div").addClass("has-error is-focused");
          new PNotify({title: 'Please select referral '+member_label+' !',styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidparentmemberid = 0;
      } else {
          $("#parentmember_div").removeClass("has-error is-focused");
      }

      if(name==""){
        $("#name_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter name !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidname = 0;
      } else {
        if(name.length<2){
          $("#name_div").addClass("has-error is-focused");
          new PNotify({title: 'Name required minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidname = 0;
        }else{
          $("#name_div").removeClass("has-error is-focused");
          isvalidname = 1;
        }
      }

      if(membercode==""){
        $("#membercode_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter '+member_label+' code !',styling: 'fontawesome',delay: '3000',type: 'error'});
      } else {
        if(membercode.length!=8){
          $("#membercode_div").addClass("has-error is-focused");
          new PNotify({title: Member_label+' code required 8 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
        }else{
          $("#membercode_div").removeClass("has-error is-focused");
          isvalidmembercode = 1;
        }
      }

      if(channelid == GUESTCHANNELID || channelid == VENDORCHANNELID || channelid == CUSTOMERCHANNELID){
          $("#roleid_div").removeClass("has-error is-focused");
          isvalidmemberrole = 1;
      }else{
        
        if(roleid==0){
          $("#roleid_div").addClass("has-error is-focused");
          new PNotify({title: 'Please select '+member_label+' role  !',styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidmemberrole = 0;

        }else{
           $("#roleid_div").removeClass("has-error is-focused");
           isvalidmemberrole = 1;
        }

      }

      if(password==""/*  && roleid!=0 */) {
          $("#password_div").addClass("has-error is-focused");
          new PNotify({title: 'Please enter password !',styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidpassword = 0;
      } else {
        //if(roleid!=0 || password!=""){
          if(CheckPassword(password)==false){
            $("#password_div").addClass("has-error is-focused");
            new PNotify({title: 'Please enter password between 6 to 20 characters which contain at least one alphabetic, numeric & special character !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidpassword = 0;
          }else { 
            $("#password_div").removeClass("has-error is-focused");
            isvalidpassword = 1;
          }
        //}
        
      }

      if(email == ''){
        $("#email_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter email !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidemail = 0;
      }else{
        if(!ValidateEmail(email)){
            $("#email_div").addClass("has-error is-focused");
            new PNotify({title: 'Please enter valid Email !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidemail = 0;
        }else{
            $("#email_div").removeClass("has-error is-focused");
            isvalidemail = 1;
        }
      }
      if(countrycodeid=="" || countrycodeid==0) {
        $("#countrycodeid_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select primary mobile country code !',styling: 'fontawesome',delay: '3000',type: 'error'});
      } else {
          $("#countrycodeid_div").removeClass("has-error is-focused");
          isvalidcountrycodeid = 1;
      }

      if(mobileno=="") {
          $("#mobile_div").addClass("has-error is-focused");
          new PNotify({title: 'Please enter primary mobile number !',styling: 'fontawesome',delay: '3000',type: 'error'});
      } else {
        if(mobileno.length!=10){
          $("#mobile_div").addClass("has-error is-focused");
          new PNotify({title: 'Primary mobile number allow only 10 digits !',styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidmobileno = 0;
        }else{
          $("#mobile_div").removeClass("has-error is-focused");
          isvalidmobileno = 1;
        }
      }
      
      if(secondarymobileno!=""){

        if(secondarycountrycodeid=="" || secondarycountrycodeid==0) {
          $("#secondarymobileno_div").addClass("has-error is-focused");
          new PNotify({title: 'Please select secondary mobile country code !',styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidsecondarycountrycodeid = 0;
        } else {
          $("#secondarymobileno_div").removeClass("has-error is-focused");
        }
        if(secondarymobileno.length!=10){
          $("#secondarymobileno_div").addClass("has-error is-focused");
          new PNotify({title: 'Secondary mobile number allow only 10 digits !',styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidsecondarymobileno = 0;
        }
      }else{
        $("#secondarymobileno_div").removeClass("has-error is-focused");
      }
      if(countryid==0) {
          $("#country_div").addClass("has-error is-focused");
          new PNotify({title: 'Please select country !',styling: 'fontawesome',delay: '3000',type: 'error'});
      } else {
          $("#country_div").removeClass("has-error is-focused");
          isvalidcountryid = 1;
      }

      if(provinceid==0) {
          $("#province_div").addClass("has-error is-focused");
          new PNotify({title: 'Please select province !',styling: 'fontawesome',delay: '3000',type: 'error'});
      } else {
          $("#province_div").removeClass("has-error is-focused");
          isvalidprovinceid = 1;
      }

      if(cityid==0) {
          $("#city_div").addClass("has-error is-focused");
          new PNotify({title: 'Please select city !',styling: 'fontawesome',delay: '3000',type: 'error'});
      } else {
          $("#city_div").removeClass("has-error is-focused");
          isvalidcityid = 1;
      }

      if(gstno!=''){
        var regexp = /^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]+)$/;
        if (gstno.length != 15) {
          $("#gstno_div").addClass("has-error is-focused");
          new PNotify({title: 'GST number must be 15 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidgstno = 0;
        } else if (!regexp.test(gstno)) {
          $("#gstno_div").addClass("has-error is-focused");
          new PNotify({title: 'GST number should have at least 1 alphabet and 1 digit !',styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidgstno = 0;
        } else {
          $("#gstno_div").removeClass("has-error is-focused");
        }
      }
      if(panno!='' && panno.length != 10){
        $("#panno_div").addClass("has-error is-focused");
        new PNotify({title: 'PAN number allow only 10 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidpanno = 0;
      } else {
        $("#panno_div").removeClass("has-error is-focused");
      }
      if(defaultcashorbankid!=0 && defaultbankmethod==0) {
        $("#defaultbankmethod_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select default method !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvaliddefaultbankmethod = 0;
      } else {
        $("#defaultbankmethod_div").removeClass("has-error is-focused");
      }
      if(CRM_SETTING == 1){
        if(companyname!="" && companyname.length<3){
          $("#companyname_div").addClass("has-error is-focused");
          new PNotify({title: 'Company name require minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidcompanyname = 0;
        }else{
          isvalidcompanyname = 1;
        }
        if(website.trim() != ""){
          if(!isUrl(website)){
            $("#website_div").addClass("has-error is-focused");
            new PNotify({title: "Please enter valid website !",styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidwebsite = 0;
          }else{
            isvalidwebsite = 1;  
          }
        }
        /* if(leadsource == 0 || leadsource==null){
          $("#leadsource_div").addClass("has-error is-focused");
          new PNotify({title: "Please select lead source !",styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidleadsource = 0;
        }else {
          isvalidleadsource = 1;
        } */
      }

      var emailerror = invalidmobilenoerror = invalidemailerror = duplicateemailerror = "";
      var isvalidcontactemail = 1;
      emailarr = [];
      
      $('.email').each(function (index, value) {
        email = $(this).val();
        divid = $(this).attr("div-id");
        emailindex = $('.email').index(this);
        mobileno = $("#mobileno"+(parseInt(emailindex)+1)).val();
        
        if(email!=""){
          if(emailarr.includes($(this).val())){
            duplicateemailerror = "Contact "+(index+1)+" : Enter different email !<br>";
          }
          emailarr.push(email);
        }

        if( email == '' && mobileno==""){
          $("#email_div"+divid).addClass("has-error is-focused");
          emailerror += "Contact "+(index+1)+" : Please enter Email !<br>";
          isvalidcontactemail = 0;
          $('html, body').animate({scrollTop:0},'slow');
        }else {
          $("#email_div"+divid).removeClass("has-error is-focused");
          if(email != ''){
            var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,3})$/;         
            if(reg.test(email) == false){ 
                $("#email_div"+divid).addClass("has-error is-focused");
                invalidemailerror += "Contact "+(index+1)+" : Please enter valid email address !<br>";
                isvalidcontactemail = 0;
                $('html, body').animate({scrollTop:0},'slow');
            }else{
                $("#email_div"+divid).removeClass("has-error is-focused");
            }
          }
        }
      });

      var mobilenoerror = duplicatemobilerror="";
      var isvalidcontactmobileno = 1;
      mobilenoarr = [];

      $('.mobileno').each(function (index, value) {
        mobileno = $(this).val();
        divid = $(this).attr("div-id");
        mobilenoindex = $('.mobileno').index(this);
        email = $("#email"+(parseInt(mobilenoindex)+1)).val();
        
        if(mobileno!=""){
          if(mobilenoarr.includes($(this).val())){
            duplicatemobilerror = "Contact "+(index+1)+" : Enter different mobile number !<br>";
          }
          mobilenoarr.push(mobileno);
        }
        if(mobileno == '' && email==""){
          $("#mobile_div"+divid).addClass("has-error is-focused");
          mobilenoerror += "Contact "+(index+1)+" : Please enter mobile number !<br>";
          isvalidcontactmobileno = 0;
          $('html, body').animate({scrollTop:0},'slow');
        }else {
          $("#mobile_div"+divid).removeClass("has-error is-focused");
          if(mobileno != ''){
            if(mobileno.length != 10){
              $("#mobile_div"+divid).addClass("has-error is-focused");
              invalidmobilenoerror += "Contact "+(index+1)+" : Please enter minimum 10 digit mobile number !<br>";
              isvalidcontactmobileno = 0;
              $('html, body').animate({scrollTop:0},'slow');
            }else{
              $("#mobile_div"+divid).removeClass("has-error is-focused");
            }
          }
        }
      })

      if(emailerror!="" && isvalidcontactmobileno==1){
        isvalidcontactemail = 1;
        isvalidcontactmobileno = 1;
        $('.mobileno').each(function (index, value) {
          divid = $(this).attr("div-id");
          $("#mobile_div"+divid).removeClass("has-error is-focused");
        })
        $('.email').each(function (index, value) {
          divid = $(this).attr("div-id");
          $("#email_div"+divid).removeClass("has-error is-focused");
        })
      }
      if(mobilenoerror!="" && isvalidcontactemail==1){
        isvalidcontactmobileno = 1;
        isvalidcontactemail = 1;
        $('.mobileno').each(function (index, value) {
          divid = $(this).attr("div-id");
          $("#mobile_div"+divid).removeClass("has-error is-focused");
        })
        $('.email').each(function (index, value) {
          divid = $(this).attr("div-id");
          $("#email_div"+divid).removeClass("has-error is-focused");
        })
      }

      if(isvalidcontactmobileno==0 && isvalidcontactemail==0){
        if(emailerror!=""){
          new PNotify({title: emailerror,styling: 'fontawesome',delay: '3000',type: 'error'});
        }
        if(mobilenoerror!=""){
          new PNotify({title: mobilenoerror,styling: 'fontawesome',delay: '3000',type: 'error'});
        }
      }

      if(invalidmobilenoerror!=""){
        new PNotify({title: invalidmobilenoerror,styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidcontactmobileno=0;
      }

      if(invalidemailerror!=""){
        new PNotify({title: invalidemailerror,styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidcontactemail=0;
      }

      if(duplicatemobilerror!=""){
        new PNotify({title: duplicatemobilerror,styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidcontactmobileno=0;
      }

      if(duplicateemailerror!=""){
        new PNotify({title: duplicateemailerror,styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidcontactemail=0;
      }

      if(ACTION==0){

        if(addressname!='' || addressemail!='' || addressmobile!='' || postalcode!='' || address!=''){


            if(addressname ==""){
              $("#addressname_div").addClass("has-error is-focused");
              new PNotify({title: 'Please enter name !',styling: 'fontawesome',delay: '3000',type: 'error'});
              isvalidaddressname = 0;
            } else {
              if(addressname.length<2){
                $("#addressname_div").addClass("has-error is-focused");
                new PNotify({title: 'Name required minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidaddressname = 0;
              }else{
                $("#addressname_div").removeClass("has-error is-focused");
                isvalidaddressname = 1;
              }
            }

            if(addressemail == ''){
              $("#addressemail_div").addClass("has-error is-focused");
              new PNotify({title: 'Please enter email !',styling: 'fontawesome',delay: '3000',type: 'error'});
              isvalidaddressemail = 0;
            }else{
              if(!ValidateEmail(addressemail)){
                  $("#addressemail_div").addClass("has-error is-focused");
                  new PNotify({title: 'Please enter valid  Email !',styling: 'fontawesome',delay: '3000',type: 'error'});
                  isvalidaddressemail = 0;
              }else{
                  $("#addressemail_div").removeClass("has-error is-focused");
                  isvalidaddressemail = 1;
              }
            }

            if(addressmobile=="") {
              $("#addressmobile_div").addClass("has-error is-focused");
              new PNotify({title: 'Please enter mobile number !',styling: 'fontawesome',delay: '3000',type: 'error'});
              isvalidaddressmobile = 0;
            } else {
              if(addressmobile.length!=10){
                $("#addressmobile_div").addClass("has-error is-focused");
                new PNotify({title: 'Mobile number allow only 10 digits !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidaddressmobile = 0;
              }else{
                $("#addressmobile_div").removeClass("has-error is-focused");
                isvalidaddressmobile = 1;
              }
            }

            if(postalcode=="") {
              $("#postalcode_div").addClass("has-error is-focused");
              new PNotify({title: 'Please enter postal code !',styling: 'fontawesome',delay: '3000',type: 'error'});
              isvalidpostalcode = 0;
            }else{
                $("#postalcode_div").removeClass("has-error is-focused");
                isvalidpostalcode = 1;
              
            }

            if(address=="") {
              $("#memberaddress_div").addClass("has-error is-focused");
              new PNotify({title: 'Please enter address !',styling: 'fontawesome',delay: '3000',type: 'error'});
              isvalidaddress = 0;
            } else {
              if(address.length<3){
                $("#memberaddress_div").addClass("has-error is-focused");
                new PNotify({title: 'Address have must be 3 characters!',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidaddress = 0;
              }else{
                isvalidaddress = 1;
              }
            }
          }else{
            $("#addressname_div").removeClass("has-error is-focused");
            $("#addressemail_div").removeClass("has-error is-focused");
            $("#addressmobile_div").removeClass("has-error is-focused");
            $("#memberaddress_div").removeClass("has-error is-focused");
            $("#postalcode_div").removeClass("has-error is-focused");
          }
      } 

      if(MEMBER_LAT_LONG == 1){
          if(latitude == ""){
              $("#latitude_div").addClass("has-error is-focused");
              new PNotify({title: 'Please enter latitude !',styling: 'fontawesome',delay: '3000',type: 'error'});
              isvalidlatitude = 0;
          } else {
            $("#latitude_div").removeClass("has-error is-focused");
            isvalidlatitude = 1;
          }

          if(longitude == ""){
            $("#longitude_div").addClass("has-error is-focused");
            new PNotify({title: 'Please enter longitude !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidlongitude = 0;
          } else {
            $("#longitude_div").removeClass("has-error is-focused");
            isvalidlongitude = 1;
          }

      }

      if(isvalidsellerchannelid==1 && isvalidmembercode==1 && isvalidcountryid==1 && isvalidprovinceid==1 && isvalidcityid &&
          isvalidparentmemberid==1 && isvalidsellermemberid==1 && isvalidgstno==1 &&
          isvalidname==1 && isvalidemail==1 && isvalidmobileno==1 && isvalidcountrycodeid==1 && isvalidchannelid==1 && isvalidpassword==1 && isvalidcompanyname==1 && isvalidwebsite==1 && isvalidleadsource==1 && isvalidpanno == 1 && isvalidsecondarymobileno == 1 && isvalidsecondarycountrycodeid == 1 && isvalidmemberrole==1 && isvalidaddressname==1 && isvalidaddressemail == 1 && isvalidaddressmobile == 1 && isvalidpostalcode == 1 && isvalidaddress==1 && isvaliddefaultbankmethod == 1 && isvalidcontactmobileno == 1 && isvalidcontactemail == 1 && isvalidlatitude == 1 && isvalidlongitude == 1) {
                
        var formData = new FormData($('#memberform')[0]);
        if(ACTION == 0){    
          var uurl = SITE_URL+"member/add-member";
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
              if(response==1){
                new PNotify({title: Member_label+' Successfully Added !',styling: 'fontawesome',delay: '3000',type: 'success'});
                setTimeout(function() { window.location=SITE_URL+"member"; }, 1500);
              }else if(response == 2) {
                new PNotify({title: 'Mobile Number already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
              }else if(response == 3) {
                new PNotify({title: 'Email already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
              }else if(response == 4) {
                new PNotify({title: 'Guest '+member_label+' already added.',styling: 'fontawesome',delay: '3000',type: 'error'});
              }else if(response==5){
                new PNotify({title: Member_label+' profile image not uploaded !',styling: 'fontawesome',delay: '3000',type: 'error'});
              }else if(response==6){
                new PNotify({title: 'Invalid type of '+member_label+' profile image !',styling: 'fontawesome',delay: '3000',type: 'error'});
              }else if(response==7){
                new PNotify({title: Member_label+' code already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
              }else if(response==8){
                new PNotify({title: 'Invalid email address !',styling: 'fontawesome',delay: '3000',type: 'error'});
              }else if(response == 9) {
                new PNotify({title: 'Website link already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
              }else if(response == 10) {
                new PNotify({title: 'Contact mobile number already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
              }else if(response == 11) {
                new PNotify({title: 'Contact email already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
              } else {
                new PNotify({title: Member_label+' data not Added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
          var uurl = SITE_URL+"member/update-member";
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
                  if(response==1){
                    new PNotify({title: Member_label+' successfully updated !',styling: 'fontawesome',delay: '3000',type: 'success'});
                    setTimeout(function() { window.location=SITE_URL+"member"; }, 1500);
                  }else if(response == 2) {
                      new PNotify({title: 'Mobile '+member_label+' already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                  }else if(response == 3) {
                      new PNotify({title: 'Email already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                  }else if(response==4){
                    new PNotify({title: Member_label+' profile image not uploaded !',styling: 'fontawesome',delay: '3000',type: 'error'});
                  }else if(response==5){
                    new PNotify({title: 'Invalid type of '+member_label+' profile image !',styling: 'fontawesome',delay: '3000',type: 'error'});
                  }else if(response==6){
                    new PNotify({title: Member_label+' code already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                  }else if(response==7){
                    new PNotify({title: 'Invalid email address !',styling: 'fontawesome',delay: '3000',type: 'error'});
                  }else if(response == 9) {
                    new PNotify({title: 'Website link already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                  }else if(response == 10) {
                    new PNotify({title: 'Contact mobile number already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                  }else if(response == 11) {
                    new PNotify({title: 'Contact email already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                  } else {
                    new PNotify({title: Member_label+' data not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
}

function getmembers(channelid,membertxtid,memberid){
  membertxtid = membertxtid || 'memberid';
  memberid = memberid || 0;

  var notmemberid = $("#id").val() || 0;
  $('#'+membertxtid)
      .find('option')
      .remove()
      .end()
      .append('<option value="0">Select '+Member_label+'</option>')
      .val('0')
      ;
    if(channelid!=0 && channelid!=-1){
      var uurl = SITE_URL+"member/getmembers";
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {channelid:channelid,notmemberid:notmemberid},
      dataType: 'json',
      async: false,
      success: function(response){
  
        for(var i = 0; i < response.length; i++) {
  
          $('#'+membertxtid).append($('<option>', { 
            value: response[i]['id'],
            text : ucwords(response[i]['name'])
          }));
  
        }
        if(memberid!=0){
          $("#"+membertxtid).val(memberid);
        }
        $('#'+membertxtid).selectpicker('refresh');
      },
      error: function(xhr) {
        //alert(xhr.responseText);
      },
    });
  }
  $('#'+membertxtid).selectpicker('refresh');
}

function getsellerchannel(channelid,sellerchannelid){
  sellerchannelid = sellerchannelid || 0;
  $('#sellerchannelid')
      .find('option')
      .remove()
      .end()
      .append('<option value="-1">Select Channel</option>')
      .val('-1');
  $('#sellermemberid')
      .find('option')
      .remove()
      .end()
      .append('<option value="-1">Select '+Member_label+'</option>')
      .val('-1');

  if(channelid!=0 && channelid!=-1){
    var uurl = SITE_URL+"member/getsellerchannel";
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {channelid:channelid},
      dataType: 'json',
      async: false,
      success: function(response){
  
        for(var i = 0; i < response.length; i++) {
          $('#sellerchannelid').append($('<option>', { 
            value: response[i]['id'],
            text : ucwords(response[i]['name'])
          }));
        }
        $("#sellerchannelid").val(sellerchannelid);
        
        $('#sellerchannelid').selectpicker('refresh');
        $('#sellermemberid').selectpicker('refresh');
      },
      error: function(xhr) {
        //alert(xhr.responseText);
      },
    });
  }
  $('#sellerchannelid').selectpicker('refresh');
  $('#sellermemberid').selectpicker('refresh');
}

function getsalesperson(channelid,){

  if(channelid!=0){
    var uurl = SITE_URL+"member/getsalesperson";
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {channelid:channelid},
      dataType: 'json',
      async: false,
      success: function(response){

        $('#salespersonid')
          .find('option')
          .remove()
          .end()
          .append()
          .val('whatever');

        for(var i = 0; i < response.length; i++) {
          $('#salespersonid').append($('<option>', { 
            value: response[i]['id'],
            text : ucwords(response[i]['name'])
          }));
        }
      
        if(salespersonid != 0){
         
          $('#salespersonid').selectpicker('val',salespersonid.split(','));
        }

        $('#salespersonid').selectpicker('refresh');
      },
      error: function(xhr) {
        //alert(xhr.responseText);
      },
    });
  } else {
    $('#salespersonid').val('');
    $('#salespersonid')
            .find('option')
            .remove()
            .end()
            .append()
            .val('whatever');
    $('#salespersonid').selectpicker('refresh');
  }
  
}

function latlng_validation(event,val){
  if (((event.which != 46 || (event.which == 46 && val == '')) ||
          val.indexOf('.') != -1) && ((event.which != 45 || (event.which == 45 && val == '')) ||
          val.indexOf('-') != -1) && (event.which < 48 || event.which > 57)) {
      event.preventDefault();
  }
}

function addnewcontact(){
  contactcount = $(".contactdiv:last").attr("div-id");
  contactheading = $(".contactheading:last").attr("heading-id");
  
  divid = parseInt(contactcount)+1;
  
  contacthtml = '<div class="contactdiv" id="contactdiv'+divid+'" div-id="'+divid+'">\
                  <div class="row">\
                    <div class="col-md-12"><hr></div>\
                    <div class="col-md-6">\
                      <div class="radio1">\
                        <label for="inquirycontact'+divid+'" div-id="'+divid+'" class="contactheading" heading-id="'+(parseInt(contactheading)+1)+'">Contact '+(parseInt(contactheading)+1)+'</label>\
                      </div>\
                    </div>\
                    <div class="col-md-6 text-right">\
                      <button type="button" class="btn btn-primary btn-raised btn-label btn-sm pull-right" id="contactdivbtn'+(parseInt(contactheading)+1)+'" onclick="addnewcontact();"><i class="fa fa-plus"></i> Add Contact</button>\
                      <button type="button" class="btn btn-danger btn-raised btnremove btn-sm pull-right mr-7" id="contactdivbtn'+divid+'" onclick="removecontact('+divid+')"><i class="fa fa-remove"></i> Remove</button>\
                    </div>\
                  </div>\
                  <div class="row">\
                    <div class="col-md-3">\
                        <div class="form-group" id="firstname_div'+divid+'">\
                          <div class="col-md-12 pr-sm">\
                            <label class="control-label" for="firstname'+divid+'">First Name </label>\
                            <input type="text" id="firstname'+divid+'" name="contactfirstname[]" class="form-control" onkeypress="return onlyAlphabets(event)">\
                          </div>\
                        </div>\
                    </div>\
                    <div class="col-md-3">\
                        <div class="form-group" id="lastname_div'+divid+'">\
                          <div class="col-md-12 pl-sm pr-sm">\
                            <label class="control-label" for="lastname'+divid+'">Last Name </label>\
                            <input type="text" id="lastname'+divid+'" name="contactlastname[]" class="form-control"  onkeypress="return onlyAlphabets(event)">\
                          </div>\
                        </div>\
                    </div>\
                    <div class="col-md-3">\
                      <div class="form-group" id="mobile_div'+divid+'">\
                        <div class="col-md-12 pl-sm pr-sm">\
                          <label class="control-label" for="mobileno'+divid+'">Mobile No <span class="mandatoryfield" style="color:#800080">*</span></label>\
                          <input id="mobileno'+divid+'" type="text" name="contactmobileno[]" class="form-control mobileno number" maxlength="10" div-id="'+divid+'" onkeypress="return isNumber(event)">\
                          <span class="mandatoryfield" id="mobilenoduplicatemessage'+divid+'" div-id="1"></span>\
                        </div>\
                      </div>\
                    </div>\
                    <div class="col-md-3">\
                      <div class="form-group" id="email_div'+divid+'">\
                        <div class="col-md-12 pl-sm">\
                          <label class="control-label" for="email'+divid+'">Email <span class="mandatoryfield" style="color:#800080"> *</span></label>\
                          <input id="email'+divid+'" type="text" name="contactemail[]" class="form-control email" div-id="'+divid+'">\
                          <span class="mandatoryfield" id="emailduplicatemessage'+divid+'" div-id="'+divid+'"></span>\
                        </div>\
                      </div>\
                    </div>\
                  </div>\
                  <div class="row">\
                    <div class="col-md-3">\
                        <div class="form-group" id="designation_div'+divid+'">\
                          <div class="col-md-12 pr-sm">\
                            <label class="control-label" for="designation'+divid+'">Designation </label>\
                            <input type="text" id="designation'+divid+'" name="contactdesignation[]" class="form-control">\
                          </div>\
                        </div>\
                    </div>\
                    <div class="col-md-3">\
                      <div class="form-group" id="department_div'+divid+'">\
                        <div class="col-md-12 pl-sm pr-sm">\
                          <label class="control-label" for="department'+divid+'">Department </label>\
                          <input type="text" id="department'+divid+'" name="contactdepartment[]" class="form-control">\
                        </div>\
                      </div>\
                    </div>\
                    <div class="col-md-3">\
                      <div class="form-group" id="birthdate_div'+divid+'">\
                        <div class="col-md-12 pl-sm pr-sm">\
                          <label class="control-label" for="birthdate'+divid+'">Birth Date </label>\
                          <input id="birthdate'+divid+'" type="text" name="contactbirthdate[]" class="form-control" readonly>\
                        </div>\
                      </div>\
                    </div>\
                    <div class="col-md-3">\
                      <div class="form-group" id="annidate_div'+divid+'">\
                        <div class="col-md-12 pl-sm">\
                          <label class="control-label" for="annidate'+divid+'">Anniversary Date </label>\
                          <input id="annidate'+divid+'" type="text" name="contactannidate[]" class="form-control" value="" readonly>\
                        </div>\
                      </div>\
                    </div>\
                  </div>\
                </div>';

  $("#contactdivs").append(contacthtml);

  $('#birthdate'+divid).datepicker({
    todayHighlight: true,
    format: 'dd/mm/yyyy',
    endDate: dateofbirth,
    autoclose: true,
    orientation:"bottom"
  });
  $('#annidate'+divid).datepicker({
      todayHighlight: true,
      format: 'dd/mm/yyyy',
      autoclose: true,
      todayBtn:"linked",
      orientation:"bottom",
      clearBtn: true
  });
  $('#mobileno'+divid).on('keypress', function (evt) {
    evt = (evt) ? evt : window.event;
      var charCode = (evt.which) ? evt.which : evt.keyCode;
      if (charCode > 31 && (charCode < 48 || charCode > 57)) {
       return false;
     }
     return true;
  }).on('focusout', function (e) {
    var $this = $(this);
    $this.val($this.val().replace(/[^0-9]/g, ''));
  }).on('paste', function (e) {
    var $this = $(this);
    setTimeout(function () {
        $this.val($this.val().replace(/[^0-9]/g, ''));
    }, 5);
  });

  generateTabIndex();
}

function removecontact(divid){
  $("#contactdiv"+divid).remove();
  $('.contactheading').each(function (index, value) {
    $(this).html("Contact "+(index+1));
    $(this).attr("heading-id",(index+1));
    divid = $(this).attr("div-id");
    $('#inquirycontact'+divid).val(index+1);
  })
}

