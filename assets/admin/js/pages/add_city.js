$(document).ready(function(){

  if(countryid!=0){
    getprovince(countryid);
    var provinceid = $("#provinceid").val();
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
       
  if(countryid!=0){
    getprovince(countryid);
  }
  $('#provinceid').selectpicker('refresh');
});


function resetdata(){

  $("#country_div").removeClass("has-error is-focused");
  $("#province_div").removeClass("has-error is-focused");
  $("#city_div").removeClass("has-error is-focused");

  if(ACTION==1){
    
    $('#countryid').val(countryid);
    $('#countryid').selectpicker('refresh');
    countryid = $("#countryid").val();
    getprovince(countryid);
    
  }else{
    
    $('#name').val('');
    $('#countryid').val('0');
    
    countryid = $("#countryid").val();
    getprovince(countryid);
  }
  $('.selectpicker').selectpicker('refresh');  
  $('html, body').animate({scrollTop:0},'slow');
}
function checkvalidation(){
  
  var countryid = $("#countryid").val();
  var provinceid = $("#provinceid").val();
  var name = $("#name").val().trim();
  
  var isvalidcountryid = isvalidprovinceid = isvalidname = 0 ;
  
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
  if(name == ''){
    $("#city_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter city name !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidname = 0;
  }else {
    if(name.length<3){
      $("#city_div").addClass("has-error is-focused");
      new PNotify({title: 'City name require minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidname = 0;
    }else{
      isvalidname = 1;
    }
  }

  if(isvalidcountryid == 1 && isvalidprovinceid == 1 && isvalidname == 1){

    var formData = new FormData($('#cityform')[0]);
    if(ACTION==0){
      var uurl = SITE_URL+"city/add-city";
      
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
            new PNotify({title: "City successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
            resetdata();
          }else if(response==2){
            new PNotify({title: "City name already exists.",styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#city_div").addClass("has-error is-focused");
          }else{
            new PNotify({title: 'City not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"city/update-city";
      
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
              new PNotify({title: "City successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"city"; }, 1500);
          }else if(response==2){
            new PNotify({title: "City name already exists.",styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#city_div").addClass("has-error is-focused");
          }else{
              new PNotify({title: 'City not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

