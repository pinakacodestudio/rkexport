$(document).ready(function(){

  if(countryid!=0){
    getprovince(countryid);
    var provinceid = $("#provinceid").val();
    getcity(provinceid);
  
  }
  $('#provinceid').selectpicker('refresh');
});

$('#countryid').change(function(){
  var countryid = $("#countryid").val();
  $('#provinceid')
      .find('option')
      .remove()
      .end()
      .append('<option value="0">Select Province</option>')
      .val('whatever')
  ;
  $('#cityid')
      .find('option')
      .remove()
      .end()
      .append('<option value="0">Select City</option>')
      .val('whatever')
  ;
       
  if(countryid!=0){
    getprovince(countryid);
  }
  $('#provinceid').selectpicker('refresh');
  $('#cityid').selectpicker('refresh');
});

$('#provinceid').change(function(){
  var countryid = $("#countryid").val();
  var provinceid = $("#provinceid").val();
 
  $('#cityid')
      .find('option')
      .remove()
      .end()
      .append('<option value="0">Select City</option>')
      .val('whatever')
  ;
       
  if(countryid!=0 && provinceid!=0){
    getcity(provinceid);
  }
  $('#cityid').selectpicker('refresh');
});


function resetdata(){

  $("#country_div").removeClass("has-error is-focused");
  $("#province_div").removeClass("has-error is-focused");
  $("#city_div").removeClass("has-error is-focused");
  $("#areaname_div").removeClass("has-error is-focused");
  $("#pincode_div").removeClass("has-error is-focused");

  if(ACTION==1){
    
    $('#countryid').val(countryid);
    $('#countryid').selectpicker('refresh');
    countryid = $("#countryid").val();
    getprovince(countryid);
    var provinceid = $("#provinceid").val();
    getcity(provinceid);
    
  }else{
    
    $('#areaname').val('');
    $('#pincode').val('');
    $('#countryid').val('0');
    
    countryid = $("#countryid").val();
    getprovince(countryid);
  }
  $('.selectpicker').selectpicker('refresh');  
  $('html, body').animate({scrollTop:0},'slow');
}
function checkvalidation(){
  
  var areaname = $("#areaname").val().trim();
  var pincode = $("#pincode").val().trim();
  var countryid = $("#countryid").val();
  var provinceid = $("#provinceid").val();
  var cityid = $("#cityid").val();
  
  var isvalidcountryid = isvalidprovinceid = isvalidcityid = isvalidareaname = isvalidpincode = 0 ;
  
  PNotify.removeAll();
  if(countryid == 0){
    $("#country_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select country !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidcountryid = 0;
  }else { 
    $("#country_div").removeClass("has-error is-focused");
    isvalidcountryid = 1;
  }
  if(provinceid == 0){
    $("#province_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select province !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidprovinceid = 0;
  }else { 
    $("#province_div").removeClass("has-error is-focused");
    isvalidprovinceid = 1;
  }
  if(cityid == 0){
    $("#city_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select city !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidcityid = 0;
  }else { 
    $("#city_div").removeClass("has-error is-focused");
    isvalidcityid = 1;
  }
  if(areaname == ''){
    $("#areaname_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter area name !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidareaname = 0;
  }else {
    if(areaname.length<3){
      $("#areaname_div").addClass("has-error is-focused");
      new PNotify({title: 'Area name require minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidareaname = 0;
    }else{
      isvalidareaname = 1;
    }
  }
  if(pincode == ''){
    $("#pincode_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter pincode !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidpincode = 0;
  }else {
    if(pincode.length<3){
      $("#pincode_div").addClass("has-error is-focused");
      new PNotify({title: 'Pincode require minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidpincode = 0;
    }else{
      isvalidpincode = 1;
    }
  }

  if(isvalidcountryid == 1 && isvalidprovinceid == 1 && isvalidcityid == 1 && isvalidareaname == 1 && isvalidpincode == 1){

    var formData = new FormData($('#area-form')[0]);
    if(ACTION==0){
      var uurl = SITE_URL+"area/add-area";
      
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
            new PNotify({title: "Area successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
            setTimeout(function() { window.location=SITE_URL+"area"; }, 1500);
          }else if(response==2){
            new PNotify({title: "Area name already exists.",styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#areaname_div").addClass("has-error is-focused");
          }else{
            new PNotify({title: 'Area not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"area/update-area";
      
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
              new PNotify({title: "Area successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"area"; }, 1500);
          }else if(response==2){
            new PNotify({title: "Area name already exists.",styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#city_div").addClass("has-error is-focused");
          }else{
              new PNotify({title: 'Area not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

