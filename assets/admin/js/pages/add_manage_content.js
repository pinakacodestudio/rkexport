function resetdata(){

    $("#contentid_div").removeClass("has-error is-focused");
    $("#description_div").removeClass("has-error is-focused");
    $('.cke_inner').css({"border":"none"});

    if(ACTION==1){
        $('.cke_inner').css({"background-color":"#FFF","border":"1px solid #D2D2D2"});
        var description = $('#description').val();
        CKEDITOR.instances['description'].setData(description);
    }else{
        $('#contentid').val('0');
        $('.cke_inner').css({"background-color":"#FFF","border":"1px solid #D2D2D2"});
        CKEDITOR.instances['description'].setData("");
    }
    $('.selectpicker').selectpicker('refresh');  
    $('html, body').animate({scrollTop:0},'slow');
}




function checkvalidation(){

    var contentid = $("#contentid").val() == undefined ? '' : $("#contentid").val();
    var description = $.trim(CKEDITOR.instances['description'].getData());
    
    description = encodeURIComponent(description);
    CKEDITOR.instances['description'].updateElement();
    var isvaliddescription= isvalidcontentid = 0;
    
    if(contentid == 0){
      $("#contentid_div").addClass("has-error is-focused");
      new PNotify({title: "Please select page title !",styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidcontentid = 0;
    }else { 
      isvalidcontentid = 1;
    }

    if(description.trim() == 0 || description.length < 4){
        $("#description_div").addClass("has-error is-focused");
        $('.cke_inner').css({"background-color":"#FFECED","border":"1px solid #e51c23"});
        new PNotify({title: 'Please enter content !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvaliddescription = 0;
    }else { 
      $('.cke_inner').css({"border":"none"});
      isvaliddescription = 1;
    }
                        
    if(isvaliddescription==1 && isvalidcontentid==1){
                            
      var formData = new FormData($('#managecontentform')[0]);
        if(ACTION == 0){    
          var uurl = SITE_URL+"manage-content/add-manage-content";
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
                new PNotify({title: "Page Content successfully added!",styling: 'fontawesome',delay: '3000',type: 'success'});
                setTimeout(function() { window.location=SITE_URL+"manage-content"; }, 1500);
              }else if(response==2){
                new PNotify({title: "Page Content already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
              }else{
                new PNotify({title: "Page Content not added !",styling: 'fontawesome',delay: '3000',type: 'error'});
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
          var uurl = SITE_URL+"manage-content/update-manage-content";
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
                    new PNotify({title: "Page Content successfully updated !",styling: 'fontawesome',delay: '3000',type: 'success'});
                    setTimeout(function() { window.location=SITE_URL+"manage-content"; }, 1500);
                  }else if(response==2){
                    new PNotify({title: "Page Content already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
                  }else{
                    new PNotify({title: "Page Content not updated !",styling: 'fontawesome',delay: '3000',type: 'error'});
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