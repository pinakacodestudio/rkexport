$(document).ready(function(){

  $("#socialmediatype").on("change", function(){
    if(this.value!=0){
      $("#name").val($("#socialmediatype option:selected").text());
    }else{
      $("#name").val("");
    }
  });
});
function resetdata(){

    $("#name_div").removeClass("has-error is-focused");
    $("#socialmediatype_div").removeClass("has-error is-focused");
  
    if(ACTION==1){
      $('html, body').animate({scrollTop:0},'slow');
    }else{
      $('#socialmediatype').val('0').selectpicker("refresh");
      $('#name').val('');
      $('#icon').val('');
      $('#url').val('');
      
      $('#name').focus();
      $('html, body').animate({scrollTop:0},'slow');  
  
    }
  }
    function checkvalidation(){
      
      var socialmediatype = $("#socialmediatype").val();
      var name = $("#name").val().trim();
      var icon = $("#icon").val().trim();
      var url = $("#url").val().trim();
      
      var isvalidsocialmediatype = isvalidname = isvalidicon = isvalidurl = 0;
      
      PNotify.removeAll();

      if(socialmediatype == 0){
        $("#socialmediatype_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select social media !',styling: 'fontawesome',delay: '3000',type: 'error'});
      }else {
        isvalidsocialmediatype = 1;
        $("#socialmediatype_div").removeClass("has-error is-focused");
      }

      if(name == ''){
        $("#name_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter social media name !',styling: 'fontawesome',delay: '3000',type: 'error'});
        $( "#name" ).focus();
      }else { 
        if(name.length<2){
          $("#name_div").addClass("has-error is-focused");
          new PNotify({title: 'Minmum 2 characters require for Social media name !',styling: 'fontawesome',delay: '3000',type: 'error'});
        }else{
          isvalidname = 1;
          $("#name_div").removeClass("has-error is-focused");
        }
      }
    
      if(icon == ''){
        $("#icon_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter icon !',styling: 'fontawesome',delay: '3000',type: 'error'});
      }else {
        isvalidicon = 1;
        $("#icon_div").removeClass("has-error is-focused");
      }
    
      if(url == ''){
        $("#url_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter url !',styling: 'fontawesome',delay: '3000',type: 'error'});
      }else { 
        var re = /^(http[s]?:\/\/){0,1}(www\.){0,1}[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,5}[\.]{0,1}/;
        if (!re.test(url)) {         
          $("#url_div").addClass("has-error is-focused");
          new PNotify({title: 'Please enter valid url !',styling: 'fontawesome',delay: '3000',type: 'error'});
        }else{
          isvalidurl = 1;
          $("#url_div").removeClass("has-error is-focused");
        }
      }
    
      if(isvalidsocialmediatype == 1 && isvalidname == 1 && isvalidicon==1 && isvalidurl==1){
    
        var formData = new FormData($('#socialmediaform')[0]);
        if(ACTION==0){
          var uurl = SITE_URL+"social-media/social-media-add";
          
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
                new PNotify({title: "Social media successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
                setTimeout(function() { window.location=SITE_URL+"social-media"; }, 1500);
              }else if(response==2){
                new PNotify({title: 'Social media already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
                $("#socialmedia_div").addClass("has-error is-focused");
              }else{
                new PNotify({title: 'Social media not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
          var uurl = SITE_URL+"social-media/update-social-media";
          
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
                  new PNotify({title: "Social media successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
                  setTimeout(function() { window.location=SITE_URL+"social-media"; }, 1500);
              }else if(response==2){
                new PNotify({title: 'Social media already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
              }else{
                  new PNotify({title: 'Social media not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
    
    