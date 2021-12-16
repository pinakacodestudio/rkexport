$(document).ready(function(){
  $('.yesno input[type="checkbox"]').bootstrapToggle({
    on: 'Yes',
    off: 'No',
    onstyle: 'primary',
    offstyle: 'danger'
  });
 
  $('.price input[type="checkbox"]').bootstrapToggle({
    on: 'Without GST',
    off: 'With GST',
    onstyle: 'primary',
    offstyle: 'danger'
  });
  $('.listing input[type="checkbox"]').bootstrapToggle({
    on: 'Scroll',
    off: 'Pagination',
    onstyle: 'primary',
    offstyle: 'danger'
  });
  $('.applayout input[type="checkbox"]').bootstrapToggle({
    on: 'New',
    off: 'Old',
    onstyle: 'primary',
    offstyle: 'danger'
  });
  $('.menulayout input[type="checkbox"]').bootstrapToggle({
    on: 'Horizontal',
    off: 'Vertical',
    onstyle: 'primary',
    offstyle: 'danger'
  });
  $('.stockcalculation input[type="checkbox"]').bootstrapToggle({
    on: 'Invoice',
    off: 'Order',
    onstyle: 'primary',
    offstyle: 'danger'
  });
  $('.memberlatlong input[type="checkbox"]').bootstrapToggle({
    on: 'Mandatory',
    off: 'Optional',
    onstyle: 'primary',
    offstyle: 'danger'
  });
  if($("#oldbrandinglogo").val()!=""){
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
 /*  $('#expirydate').datepicker({
    todayHighlight: true,
    format: 'dd/mm/yyyy',
    autoclose: true,
    todayBtn:"linked",
    clearBtn: true
   
  }); */
    var date = new Date();
    // date.setHours(date.getHours() + 1);

  $('.input-datetime').datetimepicker({
    todayHighlight: true,
    format: 'dd/mm/yyyy HH:i P',
    viewMode: 'days',
    // minView : 1,
    // minuteStep: 10,
    showMeridian: true,
    todayBtn: "linked",
    showClear: true,
    startDate: date,
    autoclose: true,
    container:'#maintenancedatetime_div',
    
  });
  $('#datepicker-range').datepicker({
    // todayHighlight: true,
    format: 'dd/mm/yyyy',
    autoclose: true,
    todayBtn:"linked",
    /* startDate: new Date(), */
  });


  $('.datetimepicker-dropdown-bottom-right:last').removeClass('datetimepicker-dropdown-bottom-right').addClass('datetimepicker-dropdown-bottom-left')
  
});

function getallow(){
  var input =$("#brandingallow").val();
  if($("#brandingallow").prop('checked') == true){
  document.getElementById("pioneeredby").disabled = false;
  document.getElementById("poweredby").disabled = false;

  var a = input.checked ? "1" : "0";
    document.getElementById('brandingallow').val = a;
  }else{
    document.getElementById("pioneeredby").disabled = true;
    document.getElementById("poweredby").disabled = true;
    var a = input.checked ? "1" : "0";
    document.getElementById('brandingallow').val = a;
  }
}
function resetdata() {
  $("#bucketname_div").removeClass("has-error is-focused");
  $("#commonbucket_div").removeClass("has-error is-focused");
  $("#clientname_div").removeClass("has-error is-focused");
  $("#iamkey_div").removeClass("has-error is-focused");
  $("#iamsecret_div").removeClass("has-error is-focused");
  $("#region_div").removeClass("has-error is-focused");
  $("#awslink_div").removeClass("has-error is-focused");
  $("#storagespace_div").removeClass("has-error is-focused");
  $("#noofproduct_div").removeClass("has-error is-focused");
  $("#paymenttitleinapp_div").removeClass("has-error is-focused");
  $("#brandingurl_div").removeClass("has-error is-focused");

  var $imageupload = $('.imageupload');
  $('#brandinglogofile img').attr('src',MAIN_LOGO_IMAGE_URL+$('#oldbrandinglogo').val()).css({"border":"1px solid #f1f1f1"});
  $imageupload.imageupload({
    url: SITE_URL,
    type: '1'
  });

  $('html, body').animate({scrollTop:0},'slow');  
}
$('#synctoaws').click(function() {
  // $('#synctoaws').attr("disabled", "disabled");
  var formData = new FormData($('#settingform')[0]);
  var uurl = SITE_URL+"system_configuration/synctoaws";

  $.ajax({
    url: uurl,
    type: 'POST',
    data: formData,
    //async: false,
    beforeSend: function () {
      $('.mask').show();
      $('#loader').show();
    },
    success: function(response){
      // var a = $.parseJSON(response);
      if(response==2){
        new PNotify({title: 'Credentials are Empty ! So Sync is not Possible',styling: 'fontawesome',delay: '3000',type: 'success'});
      }else{
        new PNotify({title: 'Sync successfully',styling: 'fontawesome',delay: '3000',type: 'success'});
      }
    },
    error: function(xhr) {
    },
    complete: function(){
      $('.mask').hide();
      $('#loader').hide();
    },
    cache: false,
    contentType: false,
    processData: false
  });
});
function checkvalidation() {
  var allows3 = $("input[name='allows3']:checked").val();
  
  var brandingurl = $("#brandingurl").val();
  var brandinglogobtn = $("#brandinglogobtn").html();
  var startdate = $("#startdate").val();
  var expirydate = $("#expirydate").val();
  var startdatetime = $("#startdatetime").val();
  var expirydatetime = $("#expirydatetime").val();
  var storagespace = $("#storagespace").val();
  
  PNotify.removeAll();
  var isvalidbrandingurl = isvalidbrandinglogobtn = 0;
  var isvalidaws = isvalidexpirydate =  isvalidstartdate = isvalidstoragespace = isvalidnoofproduct = isvaliddatetime =1;

  if(brandingurl==''){
    $("#brandingurl_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter branding url !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidbrandingurl = 0;
  }else{
      $("#brandingurl_div").removeClass("has-error is-focused");
      isvalidbrandingurl = 1;
  }
  if(brandinglogobtn.trim() == 'Select Image'){
    $('#brandinglogofile img').css({"border":"1px solid #FFB9BD"});
    new PNotify({title: 'Please select branding logo !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidbrandinglogobtn = 0;
  }else { 
    isvalidbrandinglogobtn = 1;
  }

  if(expirydate=='' || startdate==''){
    $("#expirydate_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select expiry date !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidexpirydate = 0;
  }else{
      $("#expirydate_div").removeClass("has-error is-focused");
  }
  
  if(startdatetime > expirydatetime){
    $('#expirydatetime_div').addClass("has-error is-focused");
    new PNotify({title: 'Maintenance end time should be greater then start time !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvaliddatetime = 0;
  }else { 
    isvaliddatetime = 1;
  }
  
  
  if(storagespace==''){
    $("#storagespace_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter storage space !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidstoragespace = 0;
  }else{
      $("#storagespace_div").removeClass("has-error is-focused");
  }

  if(noofproduct==''){
    $("#noofproduct_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter number of product !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidnoofproduct = 0;
  }else{
      $("#noofproduct_div").removeClass("has-error is-focused");
  }
  
  if(allows3 == '1') {
    var bucketname = $('#bucketname').val().trim();
    var clientname = $('#clientname').val().trim();
    var commonbucket = $('#commonbucket').val().trim();
    var iamkey = $('#iamkey').val().trim();
    var iamsecret = $('#iamsecret').val().trim();
    var region = $('#region').val().trim();
    var awslink = $('#awslink').val().trim();
    var isvalidbucketname = isvalidiamkey = isvalidiamsecret = isvalidregion = isvalidawslink = isvalidclientname = isvalidcommonbucket = 0;

    if(bucketname == "" || bucketname.length < 3){
      $("#bucketname_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter bucket name!',styling: 'fontawesome',delay: '3000',type: 'error'});
    }else{
      $("#bucketname_div").removeClass("has-error is-focused");
      isvalidbucketname = 1;
    }

    if(commonbucket == ""){
      $("#commonbucket_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter common bucket name!',styling: 'fontawesome',delay: '3000',type: 'error'});
    }else{
      $("#commonbucket_div").removeClass("has-error is-focused");
      isvalidcommonbucket = 1;
    }

    if(clientname == "" || clientname != "/"){
      $("#clientname_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter client name or put only "/" !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }else{
      $("#clientname_div").removeClass("has-error is-focused");
      isvalidclientname = 1;
    }

    if(iamkey == "" || iamkey.length < 3){
      $("#iamkey_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter IAM key!',styling: 'fontawesome',delay: '3000',type: 'error'});
    }else{
      $("#iamkey_div").removeClass("has-error is-focused");
      isvalidiamkey = 1;
    }

    if(iamsecret == "" || iamsecret.length < 3){
      $("#iamsecret_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter IAM secret!',styling: 'fontawesome',delay: '3000',type: 'error'});
    }else{
      $("#iamsecret_div").removeClass("has-error is-focused");
      isvalidiamsecret = 1;
    }

    if(region == "" || region.length < 3){
      $("#region_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter region!',styling: 'fontawesome',delay: '3000',type: 'error'});
    }else{
      $("#region_div").removeClass("has-error is-focused");
      isvalidregion = 1;
    }

    if(awslink == "" || awslink.length < 3){
      $("#awslink_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter AWS link!',styling: 'fontawesome',delay: '3000',type: 'error'});
    }else{
      $("#awslink_div").removeClass("has-error is-focused");
      isvalidawslink = 1;
    }

    if(isvalidbucketname == 1 && isvalidiamkey == 1 && isvalidiamsecret == 1 && isvalidregion == 1 && isvalidawslink && isvalidclientname && isvalidcommonbucket) {
      isvalidaws = 1;
    } else {
      isvalidaws = 0;
    }
  }else{
    $("#bucketname_div").removeClass("has-error is-focused");
    $("#commonbucket_div").removeClass("has-error is-focused");
    $("#clientname_div").removeClass("has-error is-focused");
    $("#iamkey_div").removeClass("has-error is-focused");
    $("#iamsecret_div").removeClass("has-error is-focused");
    $("#region_div").removeClass("has-error is-focused");
    $("#awslink_div").removeClass("has-error is-focused");
  }

  if(isvalidaws == 1 && isvalidbrandingurl == 1 && isvalidbrandinglogobtn == 1 && isvalidexpirydate == 1 && isvalidstoragespace == 1 && isvaliddatetime ==1){
    var uurl = SITE_URL+"system_configuration/update_system_configuration";
    var formData = new FormData($('#settingform')[0]);
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
          new PNotify({title: 'System Configuration successfully updated.',styling: 'fontawesome',delay: '3000',type: 'success'});
          setTimeout(function() { location.reload(); }, 1500);
        }else{
          new PNotify({title: 'System Configuration not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
        }
      },
      error: function(xhr) {
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

