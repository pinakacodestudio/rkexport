function resetdata(){
    $("#submenu_div").removeClass("has-error is-focused");
    $("#menuname_div").removeClass("has-error is-focused");
    $("#menuurl_div").removeClass("has-error is-focused");
    $("#menuorder_div").removeClass("has-error is-focused");
    if(ACTION==1){
      $('#mainmenu').focus();
      $('html, body').animate({scrollTop:0},'slow');
    }else{
      $('#name').val('');
      $("#menuurl").val('');
      $("#mainmenu").val(0);
      $("#inorder").val("");
      $('#mainmenu').focus();    
      $('html, body').animate({scrollTop:0},'slow');  
    }
    $('#mainmenu').selectpicker('refresh');
  }
  function checkvalidation(action){
    var submenu = $("#submenu").val();
    var name = $("#name").val();
    var menuurl = $("#menuurl").val();
    
    var isvalidsubmenu = isvalidname = isvalidmenuurl = 0;
    PNotify.removeAll();
    
    if(submenu.trim() == 0){
      $("#submenu_div").addClass("has-error is-focused");
      new PNotify({title: 'Please select submenu !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }else { 
      isvalidsubmenu = 1;
    }
    if(name.trim() == 0){
      $("#menuname_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter submenu name !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidname = 0;
    }else if(name.length < 3){
      $("#menuname_div").addClass("has-error is-focused");
      new PNotify({title: 'Minmum 3 characters require for submenu !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidname = 0;
    }else { 
      isvalidname = 1;
    }
    if(menuurl.trim() == 0){
      $("#menuurl_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter submenu url !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidmenuurl = 0;
    }else if(menuurl.length < 4){
      $("#menuurl_div").addClass("has-error is-focused");
      new PNotify({title: 'Minmum 4 characters require for submenu url !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidmenuurl = 0;
    }else { 
      isvalidmenuurl = 1;
    }
  
    if(isvalidname == 1 && isvalidsubmenu == 1 && isvalidmenuurl == 1){
  
      var formData = new FormData($('#formchannelsubmenu')[0]);
      if(ACTION==0){
        var uurl = SITE_URL+"channel-third-sub-menu/channel-third-sub-menu-add";
        
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
              new PNotify({title: "Channel third sub menu successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
              resetdata();
          }else if(response==2){
              new PNotify({title: 'Channel third sub menu already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
            new PNotify({title: 'Channel third sub menu not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
        var uurl = SITE_URL+"channel-third-sub-menu/update-channel-third-sub-menu";
        
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
              new PNotify({title: "Channel third sub menu successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"channel-third-sub-menu"; }, 1500);
          }else if(response==2){
              new PNotify({title: 'Channel third sub menu already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
              new PNotify({title: 'Channel third sub menu not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
  
  