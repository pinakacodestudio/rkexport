function resetdata(){

  $("#country_div").removeClass("has-error is-focused");
  $("#name_div").removeClass("has-error is-focused");

  if(ACTION==1){
    
    $('#countryid').val(countryid);
    $('#countryid').selectpicker('refresh');
    
  }else{
    
    $('#name').val('');
    $('#countryid').val('0');
  }
  $('.selectpicker').selectpicker('refresh');  
  $('html, body').animate({scrollTop:0},'slow');
  
}
function checkvalidation(){
  
  var name = $("#name").val().trim();
  var countryid = $("#countryid").val();
  
  var isvalidcountryid = isvalidname = 0 ;
  
  PNotify.removeAll();
  if(countryid == 0){
    $("#country_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select country !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidcountryid = 0;
  }else { 
    $("#country_div").removeClass("has-error is-focused");
    isvalidcountryid = 1;
  }
  if(name == ''){
    $("#name_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter province name !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidname = 0;
  }else {
    if(name.length<3){
      $("#name_div").addClass("has-error is-focused");
      new PNotify({title: 'Province name require minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidname = 0;
    }else{
      isvalidname = 1;
    }
  }

  if(isvalidcountryid == 1 && isvalidname == 1){

    var formData = new FormData($('#provinceform')[0]);
    if(ACTION==0){
      var uurl = SITE_URL+"province/add-province";
      
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
            new PNotify({title: "Province successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
            resetdata();
          }else if(response==2){
            new PNotify({title: "Province name already exists.",styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#name_div").addClass("has-error is-focused");
          }else{
            new PNotify({title: 'Province not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"province/update-province";
      
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
              new PNotify({title: "Province successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"province"; }, 1500);
          }else if(response==2){
            new PNotify({title: "Province name already exists.",styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#name_div").addClass("has-error is-focused");
          }else{
              new PNotify({title: 'Province not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

