function resetdata(){

  $("#name_div").removeClass("has-error is-focused");

  if(ACTION==1){
    $('html, body').animate({scrollTop:0},'slow');
  }else{
    $('#name').val('');
    $('#icon').val('');
    $('#url').val('');
   
    $('#name').focus();
    $('html, body').animate({scrollTop:0},'slow');  

  }
}
function checkvalidation(){
  
  var name = $("#name").val().trim();
  var icon = $("#icon").val().trim();
  var url = $("#url").val().trim();
  
  var isvalidname = 0;
  
  PNotify.removeAll();
  if(name == ''){
    $("#name_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter social media name !',styling: 'fontawesome',delay: '3000',type: 'error'});
    $( "#name" ).focus();
    isvalidname = 0;
  }else { 
    if(name.length<2){
      $("#name_div").addClass("has-error is-focused");
      new PNotify({title: 'Minmum 2 characters require for Social media name !',styling: 'fontawesome',delay: '3000',type: 'error'});
      
      isvalidname = 0;
    }else{
      isvalidname = 1;
    }
  }

  if(icon == ''){
    $("#icon_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter icon !',styling: 'fontawesome',delay: '3000',type: 'error'});
    
    isvalidicon = 0;
  }else {
      isvalidicon = 1;
  }

  if(url == ''){
    $("#url_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter url !',styling: 'fontawesome',delay: '3000',type: 'error'});
    
    isvalidurl = 0;
  }else { 
    var re = /^(http[s]?:\/\/){0,1}(www\.){0,1}[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,5}[\.]{0,1}/;
    if (!re.test(url)) {         
      $("#url_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter valid url !',styling: 'fontawesome',delay: '3000',type: 'error'});
      
      isvalidurl = 0;
    }else{
      isvalidurl = 1;
    }
  }

  if(isvalidname == 1 && isvalidicon==1 && isvalidurl==1){

    var formData = new FormData($('#socialmediaform')[0]);
    if(ACTION==0){
      var uurl = SITE_URL+"Socialmedia/addsocialmedia";
      
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
            new PNotify({title: "Socialmedia successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
            resetdata();
          }else if(response==2){
            new PNotify({title: 'Socialmedia already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#socialmedia_div").addClass("has-error is-focused");
          }else{
            new PNotify({title: 'Socialmedia not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"Socialmedia/updatesocialmedia";
      
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
              new PNotify({title: "Socialmedia successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"Socialmedia"; }, 1500);
          }else if(response==2){
            new PNotify({title: 'Socialmedia already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
              new PNotify({title: 'Socialmedia not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

