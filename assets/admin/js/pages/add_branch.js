$(document).ready(function(){
  

     /****COUNTRY CHANGE EVENT****/
     $('#countryid').on('change', function (e) {
      $('#provinceid')
          .find('option')
          .remove()
          .end()
          .append('<option value="">Select State</option>')
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
  /****PROVINCE CHANGE EVENT****/
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
  
  getprovince($('#countryid').val());
  getcity($('#provinceid').val());

});
function resetdata() {

  $("#branchname_div").removeClass("has-error is-focused");
  $("#company_div").removeClass("has-error is-focused");
  $("#email_div").removeClass("has-error is-focused");
  $("#address_div").removeClass("has-error is-focused");
  $("#countryid_div").removeClass("has-error is-focused");
  $("#provinceid_div").removeClass("has-error is-focused");
  $("#cityid_div").removeClass("has-error is-focused");

  if (ACTION == 1) {

  } else {
    $("#branchname").val("");
    $("#company").val("");
    $("#email").val("");
    $("#address").val("");
    $("#countryid").val(countryid);
    $("#provinceid").val("0");
    $("#cityid").val("0");
    $('.selectpicker').selectpicker('refresh')
  }
  $('html, body').animate({scrollTop: 0}, 'slow');
}
function checkvalidation(addtype = 0){
  
  var branchname = $("#branchname").val().trim();
  var email = $("#email").val().trim();
  var address = $("#address").val().trim();
  var countryid = $("#countryid").val();
  var provinceid = $("#provinceid").val();
  var cityid = $("#cityid").val();
  var isvalidbranchname = isavalidemail = isvalidaddress = isvalidcountryid = isvalidprovinceid = isvalidcityid = 0
  PNotify.removeAll();

  
  if(branchname == ""){
      $("#branchname_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter branch name !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }else if(branchname.length < 2){
      $("#branchname_div").addClass("has-error is-focused");
      new PNotify({title: 'Branch name require minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }else {
      $("#branchname_div").removeClass("has-error is-focused");
      isvalidbranchname = 1;
  }
  if(email == ""){
    $("#email_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter email !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }else if(ValidateEmail(email) == false){
      $("#email_div").addClass("has-error is-focused");
      new PNotify({title: 'Email not valid !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }else {
      $("#email_div").removeClass("has-error is-focused");
      isavalidemail = 1;
  }
  if(address == ""){
      $("#address_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter address !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }else if(address.length < 2){
      $("#address_div").addClass("has-error is-focused");
      new PNotify({title: 'Address require minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }else {
      $("#address_div").removeClass("has-error is-focused");
      isvalidaddress = 1;
  }
  if(countryid == 0){
      $("#countryid_div").addClass("has-error is-focused");
      new PNotify({title: 'Please select country !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }else {
      $("#countryid_div").removeClass("has-error is-focused");
      isvalidcountryid = 1;
  }
  if(provinceid == 0){
      $("#provinceid_div").addClass("has-error is-focused");
      new PNotify({title: 'Please select state !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }else {
      $("#provinceid_div").removeClass("has-error is-focused");
      isvalidprovinceid = 1;
  }
  if(cityid == 0){
      $("#cityid_div").addClass("has-error is-focused");
      new PNotify({title: 'Please select city !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }else {
      $("#cityid_div").removeClass("has-error is-focused");
      isvalidcityid = 1;
  }

  
  
  if(isvalidbranchname == 1 && isavalidemail==1 && isvalidaddress == 1 && isvalidcountryid == 1 && isvalidprovinceid == 1 && isvalidcityid == 1){
      
    var formData = new FormData($('#form-branch')[0]);
    if(ACTION==0){
      var uurl = SITE_URL+"branch/branch-add";
      
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
            new PNotify({title: "Branch successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
            if(addtype==1){
              resetdata();
            }else{
              setTimeout(function() { window.location=SITE_URL+"branch"; }, 1500);
            }
          }else if(response==2){
            new PNotify({title: "Branch already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
            new PNotify({title: 'Branch not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      
      var uurl = SITE_URL+"branch/update-branch";
      
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
            new PNotify({title: "Branch successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
            if(addtype==1){
              setTimeout(function() {window.location=SITE_URL+"branch/add-branch"; }, 1500);
            }else{
              setTimeout(function() { window.location=SITE_URL+"branch"; }, 1500);
            }
          }else if(response==2){
            new PNotify({title: "Branch already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
            new PNotify({title: 'Branch not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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