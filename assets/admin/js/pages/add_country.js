function resetdata(){

  $("#name_div").removeClass("has-error is-focused");
  $("#sortname_div").removeClass("has-error is-focused");
  $("#phonecode_div").removeClass("has-error is-focused");

  if(ACTION==0){
    $('#name').val('');
    $('#sortname').val('');
    $('#phonecode').val('');
  }
 
  $('html, body').animate({scrollTop:0},'slow');
  
}
function checkvalidation(){
  
  var name = $("#name").val().trim();
  var sortname = $("#sortname").val().trim();
  var phonecode = $("#phonecode").val().trim();
  
  var isvalidname = isvalidsortname = isvalidphonecode = 0 ;
  
  PNotify.removeAll();
  if(name == ''){
    $("#name_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter country name !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidname = 0;
  }else {
    if(name.length<3){
      $("#name_div").addClass("has-error is-focused");
      new PNotify({title: 'Country name require minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidname = 0;
    }else{
      isvalidname = 1;
    }
  }
  if(sortname == ''){
    $("#sortname_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter sort name !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidsortname = 0;
  }else {
    if(sortname.length<2){
      $("#sortname_div").addClass("has-error is-focused");
      new PNotify({title: 'Country sortname require minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidsortname = 0;
    }else{
      isvalidsortname = 1;
    }
  }
  if(phonecode == ''){
    $("#phonecode_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter phone code !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidphonecode = 0;
  }else {
    if(phonecode.length<2){
      $("#phonecode_div").addClass("has-error is-focused");
      new PNotify({title: 'Phone code require minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidphonecode = 0;
    }else if(phonecode.substring(0, 1) != '+'){
      $("#phonecode_div").addClass("has-error is-focused");
      new PNotify({title: 'First characters must be + sign !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidphonecode = 0;
    }else{
      isvalidphonecode = 1;
    }
  }

  if(isvalidname == 1 && isvalidsortname == 1 && isvalidphonecode == 1){

    var formData = new FormData($('#countryform')[0]);
    if(ACTION==0){
      var uurl = SITE_URL+"country/add-country";
      
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
            new PNotify({title: "Country successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
            resetdata();
          }else if(response==2){
            new PNotify({title: "Country name already exists.",styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#name_div").addClass("has-error is-focused");
          }else{
            new PNotify({title: 'Country not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"country/update-country";
      
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
              new PNotify({title: "Country successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"country"; }, 1500);
          }else if(response==2){
            new PNotify({title: "Country name already exists.",styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#name_div").addClass("has-error is-focused");
          }else{
              new PNotify({title: 'Country not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

