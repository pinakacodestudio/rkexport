function resetdata(){

  $("#tag_div").removeClass("has-error is-focused");

  if(ACTION==1){
  }else{
    $('#tag').val('');
    $('#yes').prop("checked", true);
    $('#tag').focus();
  }
  $('html, body').animate({scrollTop:0},'slow');
}
function checkvalidation(){
  
  var tag = $("#tag").val().trim();
  
  var isvalidtag = 0;
  
  PNotify.removeAll();
  if(tag == ''){
    $("#tag_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter tag !',styling: 'fontawesome',delay: '3000',type: 'error'});
    $( "#tag" ).focus();
    isvalidtag = 0;
  }else { 
    if(tag.length<2){
      $("#tag_div").addClass("has-error is-focused");
      new PNotify({title: 'Minmum 2 characters require for tag!',styling: 'fontawesome',delay: '3000',type: 'error'});
      $( "#tag" ).focus();
      isvalidtag = 0;
    }else{
      isvalidtag = 1;
    }
  }

  if(isvalidtag == 1){

    var formData = new FormData($('#producttagform')[0]);
    if(ACTION==0){
      var uurl = SITE_URL+"Producttag/addproducttag";
      
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
            new PNotify({title: "Product tag successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
            resetdata();
          }else if(response==2){
            new PNotify({title: 'Product tag already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#tag_div").addClass("has-error is-focused");
          }else{
            new PNotify({title: 'Product tag not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"Producttag/updateproducttag";
      
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
              new PNotify({title: "Product tag successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"Producttag"; }, 1500);
          }else if(response==2){
            new PNotify({title: 'Product tag already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#tag_div").addClass("has-error is-focused");
          }else{
              new PNotify({title: 'Product tag not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

