$("#integratedtax").on('keyup',function(e){
    var val = $(this).val();
      if(val>100){
          $('#integratedtax').val("100.00");
      }
});
function resetdata(){

  $("#description_div").removeClass("has-error is-focused");
  $("#hsncode_div").removeClass("has-error is-focused");
  $("#integratedtax_div").removeClass("has-error is-focused");

  if(ACTION==0){
    
    $('#description').val('');
    $('#hsncode').val('');
    $('#integratedtax').val('');
    $('#yes').prop("checked", true);
    $('#description').focus();
  }
  $('html, body').animate({scrollTop:0},'slow');
}
function checkvalidation(){
  
  var description = $("#description").val().trim();
  var hsncode = $("#hsncode").val().trim();
  var integratedtax = $("#integratedtax").val().trim();
 
  var isvaliddescription = isvalidhsncode = isvalidintegratedtax = 0;
  
  PNotify.removeAll();
  if(description == ''){
    $("#description_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter description !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvaliddescription = 0;
  }else { 
    if(description.length<2){
      $("#description_div").addClass("has-error is-focused");
      new PNotify({title: 'Description require minmum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvaliddescription = 0;
    }else{
      isvaliddescription = 1;
    }
  }
  if(hsncode == ''){
    $("#hsncode_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter HSNcode !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidhsncode = 0;
  }else { 
    if(hsncode.length<2){
      $("#hsncode_div").addClass("has-error is-focused");
      new PNotify({title: 'HSN Code name require minmum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidhsncode = 0;
    }else{
      isvalidhsncode = 1;
    }
  }
  if(integratedtax == '' || integratedtax == 0){
    $("#integratedtax_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter integrated tax !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidintegratedtax = 0;
  }else { 
    isvalidintegratedtax = 1;
  }

  if(isvaliddescription == 1 && isvalidhsncode == 1 && isvalidintegratedtax == 1){

    var formData = new FormData($('#hsncodeform')[0]);
    if(ACTION==0){
      var uurl = SITE_URL+"Hsncode/addhsncode";
      
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
            new PNotify({title: "Hsncode successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
            resetdata();
          }else if(response==2){
            new PNotify({title: 'Hsncode already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#hsncode_div").addClass("has-error is-focused");
          }else{
            new PNotify({title: 'Hsncode not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"Hsncode/updatehsncode";
      
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
              new PNotify({title: "Hsncode successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"Hsncode"; }, 1500);
          }else if(response==2){
            new PNotify({title: 'Hsncode already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#hsncode_div").addClass("has-error is-focused");
          }else{
              new PNotify({title: 'Hsncode not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

