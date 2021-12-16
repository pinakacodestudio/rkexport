$(document).ready(function(){
  
  $(".selectcolor").minicolors({
    control: $(this).attr('data-control') || 'hue',
    defaultValue: $(this).attr('data-defaultValue') || '',
    format: $(this).attr('data-format') || 'hex',
    keywords: $(this).attr('data-keywords') || '',
    inline: $(this).attr('data-inline') === 'true',
    letterCase: $(this).attr('data-letterCase') || 'lowercase',
    opacity: $(this).attr('data-opacity'),
    position: $(this).attr('data-position') || 'bottom',
    swatches: $(this).attr('data-swatches') ? $(this).attr('data-swatches').split('|') : [],
    change: function(value, opacity) {
      if( !value ) return;
      if( opacity ) value += ', ' + opacity;
      if( typeof console === 'object' ) {
      }
    },
    theme: 'bootstrap'
  });
  
  $('#datepicker-range').datepicker({
      // todayHighlight: true,
      format: 'dd/mm/yyyy',
      autoclose: true,
      startDate: new Date(),
  });
  $('#cleardatebtn').click(function(){
      $("#startdate").val("");
      $("#enddate").val("");
   })

  $("#percentageval").keyup(function(e){
    if($(this).val()>100){
      $(this).val('100.00');  
    }
  });
  if(ACTION==1){
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
  $('.yesno input[type="checkbox"]').bootstrapToggle({
    on: 'Yes',
    off: 'No',
    onstyle: 'primary',
    offstyle: 'danger'
  });
  $(".selectpicker").selectpicker("refresh");
  $('form').on('reset', function(e)
  {
      setTimeout(function() {resetdata();});
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

  $(".add-mobile").hide();
  $(".add-mobile:last").show();

  $(".add-email").hide();
  $(".add-email:last").show();
});

function addnewmobile(){

  var divcount = parseInt($(".countmobile:last").attr("id").match(/\d+/))+1;
  
  var html = '<div class="row m-n countmobile" id="countmobile'+divcount+'">\
                    <div class="form-group mt-n" id="mobileno'+divcount+'_div">\
                      <div class="col-sm-8">\
                          <input id="mobileno'+divcount+'" type="text" name="mobileno[]" class="form-control mobileno" maxlength="10" onkeypress="return isNumber(event)">\
                      </div>\
                      <div class="col-md-2 m-n p-sm pt-sm">\
                          <button type = "button" class = "btn btn-default btn-raised rm-mobile" onclick="removemobile('+divcount+')" style="padding: 5px 10px;"> <i class = "fa fa-minus"></i></button> \
                          <button type="button" class="btn btn-default btn-raised add-mobile" onclick="addnewmobile()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button> \
                      </div>\
                    </div>\
                </div>';

  $(".rm-mobile:first").show();
  $(".add-mobile:last").hide();
  
  $("#countmobile"+(divcount-1)).after(html);
}

function removemobile(divid){

  $("#countmobile"+divid).remove();

  $(".add-mobile:last").show();
  if ($(".rm-mobile:visible").length == 1) {
      $(".rm-mobile:first").hide();
  }
}

function addnewemail(){

  var divcount = parseInt($(".countemail:last").attr("id").match(/\d+/))+1;
  
  var html = '<div class="row m-n countemail" id="countemail'+divcount+'">\
                    <div class="form-group mt-n" id="email'+divcount+'_div">\
                      <div class="col-sm-8">\
                          <input id="email'+divcount+'" type="text" name="email[]" class="form-control email">\
                      </div>\
                      <div class="col-md-2 m-n p-sm pt-sm">\
                          <button type = "button" class = "btn btn-default btn-raised rm-email" onclick="removeemail('+divcount+')" style="padding: 5px 10px;"> <i class = "fa fa-minus"></i></button> \
                          <button type="button" class="btn btn-default btn-raised add-email" onclick="addnewemail()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button> \
                      </div>\
                    </div>\
                </div>';

  $(".rm-email:first").show();
  $(".add-email:last").hide();
  
  $("#countemail"+(divcount-1)).after(html);
}

function removeemail(divid){

  $("#countemail"+divid).remove();

  $(".add-email:last").show();
  if ($(".rm-email:visible").length == 1) {
      $(".rm-email:first").hide();
  }
}

function resetdata(){
  
  $("#name_div").removeClass("has-error is-focused");
  $("#website_div").removeClass("has-error is-focused");
  $("#email_div").removeClass("has-error is-focused");
  $("#address_div").removeClass("has-error is-focused");
  $("#mobileno_div").removeClass("has-error is-focused");
  $("#country_div").removeClass("has-error is-focused");
  $("#province_div").removeClass("has-error is-focused");
  $("#city_div").removeClass("has-error is-focused");
  $("#locationrange_div").removeClass("has-error is-focused");
  $("#locationinterval_div").removeClass("has-error is-focused");
  $("#syncinterval_div").removeClass("has-error is-focused");
  
  var $imageupload = $('.imageupload');
  $('#faviconiconfile img').attr('src',MAIN_LOGO_IMAGE_URL+$('#oldfaviconicon').val());
  $imageupload.imageupload({
    url: SITE_URL,
    type: '1'
  });

  var $imageupload = $('.imageupload');
  $('#companylogo img').attr('src',MAIN_LOGO_IMAGE_URL+$('#oldlogo').val());
  $imageupload.imageupload({
    url: SITE_URL,
    type: '1'
  });
  $('#companydarklogo img').attr('src',MAIN_LOGO_IMAGE_URL+$('#olddarklogo').val());
  $imageupload.imageupload({
    url: SITE_URL,
    type: '1'
  });

  $("#countryid").val(countryid);
  $('#countryid').selectpicker('refresh');
  getprovince(countryid);
  getcity(provinceid);



  $('#companydarklogo img').css({"border":"1px solid #f1f1f1"});
  $('html, body').animate({scrollTop:0},'slow');  

} 

function checkvalidation() {

  var companyname = $("#name").val();
  var website = $("#website").val();
  var email = $("#email").val();
  var address = $("#address").val();
  var mobileno = $("#mobileno").val();
  var faviconbtn = $("#faviconbtn").html();
  var logobtn = $("#logobtn").html();
  var darklogobtn = $("#darklogobtn").html();
  var defaultimage = $("#productdefaultimagebtn").html();
  var defaultcategoryimage = $("#defaultimagecategorybtn").html();
  var countryid = $("#countryid").val();
  var provinceid = $("#provinceid").val();
  var cityid = $("#cityid").val();
  var footerbgcolor = $("#footerbgcolor").val();
  var themecolor = $("#themecolor").val();
  var fontcolor = $("#fontcolor").val();
  var linkcolor = $("#linkcolor").val();
  var tableheadercolor = $("#tableheadercolor").val();
  var sidebarbgcolor = $("#sidebarbgcolor").val();
  var sidebarmenuactivecolor = $("#sidebarmenuactivecolor").val();
  var sidebarsubmenubgcolor = $("#sidebarsubmenubgcolor").val();
  var sidebarsubmenuactivecolor = $("#sidebarsubmenuactivecolor").val();
 
  var locationrange = (CRM_SETTING==1?$("#locationrange").val():'');
  var locationinterval = (CRM_SETTING==1?$("#locationinterval").val():'');
  var syncinterval = (CRM_SETTING==1?$("#syncinterval").val():'');
  
  var isvalidcompanyname = isvalidwebsite = isvalidaddress = isvalidfaviconbtn = isvalidlogobtn = isvaliddarklogobtn = isvalidshippingamount = isvalidcodamount = isvalidfreeshippingamountforcod = isvaliddefaultimage = isvaliddefaultcategoryimage = isvalidcountryid = isvalidprovinceid = isvalidcityid = isvalidfooterbgcolor = isvalidlinkcolor =  isvalidthemecolor = isvalidfontcolor = isvalidtableheadercolor = isvalidsidebarbgcolor = isvalidsidebarmenuactivecolor = isvalidsidebarsubmenubgcolor = isvalidsidebarsubmenuactivecolor = 0 ;
  var isvalidgoogletrackingcode = isvalidlocationrange = isvalidlocationinterval = isvalidsyncinterval = isvalidmobileno2 = isvalidemail2 = 1;
 
  if(companyname.trim() == 0 || companyname.length < 2){
    $("#name_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter company name !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidcompanyname = 0;
  }else { 
    isvalidcompanyname = 1;
  }
  if(website.trim() == 0){
    $("#website_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter website name !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidwebsite = 0;
  }else { 
    if(!isValidWebsite(website)){
      $("#website_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter valid website !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidwebsite = 0;
    }else{
      isvalidwebsite = 1;  
    }
  }
 
  if(address.trim() == 0 || address.length < 2){
    $("#address_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter address !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidaddress = 0;
  }else { 
    isvalidaddress = 1;
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
 /*  if(mobileno.trim() == 0){
    $("#mobileno_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter mobileno !',styling: 'fontawesome',delay: '3000',type: 'error'});
    
    isvalidmobileno = 0;
  }else if(mobileno.length<10){
    $("#mobileno_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter valid mobileno !',styling: 'fontawesome',delay: '3000',type: 'error'});
    
    isvalidmobileno = 0;
  }else { 
    isvalidmobileno = 1;
  } */
  
  var m=1;
  var firstmobileid = $('.countmobile:first').attr('id').match(/\d+/);
  $('.countmobile').each(function(){
      var id = $(this).attr('id').match(/\d+/);
    
      if(parseInt(id)==parseInt(firstmobileid) && $("#mobileno"+id).val().trim() == 0){
        $("#mobileno"+id+"_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter '+(m)+' mobile number !',styling: 'fontawesome',delay: '3000',type: 'error'});
        
        isvalidmobileno2 = 0;
      }else{
        if($("#mobileno"+id).val() != "" && $("#mobileno"+id).val().length != 10){
            $("#mobileno"+id+"_div").addClass("has-error is-focused");
            new PNotify({title: 'Mobile no. '+(m)+' require 10 digits !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidmobileno2 = 0;
        }else {
            $("#mobileno"+id+"_div").removeClass("has-error is-focused");
        }
      }
      
      m++;
  });
  var e=1;
  var firstemailid = $('.countemail:first').attr('id').match(/\d+/);
  $('.countemail').each(function(){
      var id = $(this).attr('id').match(/\d+/);
  
      if(parseInt(id)==parseInt(firstemailid) && ($("#mobileno"+id).val().trim() == 0 || $("#mobileno"+id).val().length < 2)){
        $("#email"+id+"_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter '+(e)+' email address !',styling: 'fontawesome',delay: '3000',type: 'error'});
        
        isvalidemail2 = 0;
      }else{
        if($("#email"+id).val() != "" && validemail.test($("#email"+id).val()) == false){
            $("#email"+id+"_div").addClass("has-error is-focused");
            new PNotify({title: 'Please enter '+(e)+' valid email address !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidemail2 = 0;
        }else {
            $("#email"+id+"_div").removeClass("has-error is-focused");
        }
      }
      e++;
  });

  if(CRM_SETTING==1){
    if(locationrange.trim() == 0){
      $("#locationrange_div").addClass("has-error is-focused");
      new PNotify({title: "Please enter location range !",styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidlocationrange = 0;
    }else { 
      isvalidlocationrange = 1;
    }
  
    if(locationinterval.trim() == 0){
      $("#locationinterval_div").addClass("has-error is-focused");
      new PNotify({title: "Please enter location interval !",styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidlocationinterval = 0;
    }else { 
      isvalidlocationinterval = 1;
    }
  
    if(syncinterval.trim() == 0){
      $("#syncinterval_div").addClass("has-error is-focused");
      new PNotify({title: "Please enter sync. interval !",styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidsyncinterval = 0;
    }else { 
      isvalidsyncinterval = 1;
    }
  }

  if(faviconbtn.trim() == 'Select Image'){
    $('#companylogo img').css({"border":"1px solid #FFB9BD"});
    new PNotify({title: 'Please select favicon icon !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidfaviconbtn = 0;
  }else { 
    isvalidfaviconbtn = 1;
  }
  if(logobtn.trim() == 'Select Image'){
    $('#companylogo img').css({"border":"1px solid #FFB9BD"});
    new PNotify({title: 'Please select company light logo !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidlogobtn = 0;
  }else { 
    isvalidlogobtn = 1;
  }
  if(darklogobtn.trim() == 'Select Image'){
    $('#companydarklogo img').css({"border":"1px solid #FFB9BD"});
    new PNotify({title: 'Please select company dark logo !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvaliddarklogobtn = 0;
  }else { 
    isvaliddarklogobtn = 1;
  }
  if(defaultimage.trim() == 'Select Image'){
    $('#productdefaultimagediv img').css({"border":"1px solid #FFB9BD"});
    new PNotify({title: 'Please select product default image !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvaliddefaultimage = 0;
  }else { 
    isvaliddefaultimage = 1;
  }
  if(defaultcategoryimage.trim() == 'Select Image'){
    $('#defaultimagecategorydiv img').css({"border":"1px solid #FFB9BD"});
    new PNotify({title: 'Please select category default image !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvaliddefaultcategoryimage = 0;
  }else { 
    isvaliddefaultcategoryimage = 1;
  }

  if(footerbgcolor == ""){
    $("#footerbgcolor_div").addClass("has-error is-focused");
    new PNotify({title: 'Please Select Footer Color !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidfooterbgcolor = 0;
  }
  else{
    isvalidfooterbgcolor = 1;
  }

  if(themecolor == ""){
    $("#themecolor_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select theme color !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidthemecolor = 0;
  }
  else{
    isvalidthemecolor = 1;
  }

   if(fontcolor == ""){
    $("#fontcolor_div").addClass("has-error is-focused");
    new PNotify({title: 'Please Select Font Color !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidfontcolor = 0;
  }
  else{
    isvalidfontcolor = 1;    
  }

  if(linkcolor == ""){
    $("#linkcolor_div").addClass("has-error is-focused");
    new PNotify({title: 'Please Select Link Color !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidlinkcolor = 0;
  }
  else{
    isvalidlinkcolor = 1;
  }

  if(tableheadercolor == ""){
    $("#tableheadercolor_div").addClass("has-error is-focused");
    new PNotify({title: 'Please Select Table Header Color !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidtableheadercolor = 0;
  }
  else{
    isvalidtableheadercolor = 1;
  }

  if(sidebarbgcolor == ""){
    $("#sidebarbgcolor_div").addClass("has-error is-focused");
    new PNotify({title: 'Please Select Sidebar Background Color !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidsidebarbgcolor = 0;
  }
  else{
    isvalidsidebarbgcolor = 1;
  }

  if(sidebarmenuactivecolor == ""){
    $("#sidebarmenuactivecolor_div").addClass("has-error is-focused");
    new PNotify({title: 'Please Select Sidebar Menu Active Color !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidsidebarmenuactivecolor = 0;
  }
  else{
    isvalidsidebarmenuactivecolor = 1;
  }

  if(sidebarsubmenubgcolor == ""){
    $("#sidebarsubmenubgcolor_div").addClass("has-error is-focused");
    new PNotify({title: 'Please Select Sidebar Submenu Background Color !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidsidebarsubmenubgcolor = 0;
  }
  else{
    isvalidsidebarsubmenubgcolor = 1;
  }

  if(sidebarsubmenuactivecolor	 == ""){
    $("#sidebarsubmenuactivecolor	_div").addClass("has-error is-focused");
    new PNotify({title: 'Please Select Sidebar Submenu Active Color !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidsidebarsubmenuactivecolor	 = 0;
  }
  else{
    isvalidsidebarsubmenuactivecolor	 = 1;
  }

  
  if(isvalidcompanyname == 1  && isvalidwebsite ==1 && isvalidaddress ==1 && isvalidcountryid==1 && isvalidprovinceid==1 && isvalidcityid && isvalidfaviconbtn == 1 && isvalidlogobtn == 1 && isvaliddarklogobtn == 1 && isvaliddefaultimage==1 && isvaliddefaultcategoryimage == 1 && isvalidfooterbgcolor == 1 && isvalidthemecolor == 1 && isvalidfontcolor == 1 && isvalidlinkcolor == 1 && isvalidtableheadercolor == 1 && isvalidsidebarbgcolor == 1 && isvalidsidebarmenuactivecolor == 1 && isvalidsidebarsubmenubgcolor == 1 && isvalidsidebarsubmenuactivecolor == 1 && isvalidlocationrange==1 && isvalidlocationinterval==1 && isvalidsyncinterval==1 && isvalidmobileno2==1 && isvalidemail2==1){

    var uurl = SITE_URL+"setting/update-settings";
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
        var a = $.parseJSON(response);
          if(response==1){
            new PNotify({title: 'System Settings successfully updated.',styling: 'fontawesome',delay: '3000',type: 'success'});
            
            setTimeout(function() { window.location.href = SITE_URL+"setting"; }, 1500);
        }else if(response==2){
          new PNotify({title: 'Uploaded File is not an Image !',styling: 'fontawesome',delay: '3000',type: 'error'});
        }else if(response==3){
          new PNotify({title: 'Company code already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
        }
        else{
          new PNotify({title: 'System Settings not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

