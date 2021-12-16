function resetdata(){
  $("#menuname_div").removeClass("has-error is-focused");
  $("#menuorder_div").removeClass("has-error is-focused");
  if(ACTION==1){  
    $('#name').focus();
    $('html, body').animate({scrollTop:0},'slow');
  }else{
    $('#name').val('');
    $("#menuicon").val('');
    $("#menuurl").val('');
    $("#inorder").val("");
    $('#name').focus();    
    $("#rightsid").val("");
    $('html, body').animate({scrollTop:0},'slow');  
  }
  $('#inorder,#rightsid').selectpicker('refresh');
}
function checkvalidation(action){
  var name = $("#name").val();
  var inorder = $("#inorder").val();

  var isvalidinorder = isvalidname = 0;
  
  PNotify.removeAll();
  if(name.trim() == 0){
    $("#menuname_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter mainmenu name !',styling: 'fontawesome',delay: '3000',type: 'error'});
    $( "#name" ).focus();
    isvalidname = 0;
  }else if(name.length < 3){
    $("#menuname_div").addClass("has-error is-focused");
    new PNotify({title: 'Minmum 3 characters require for mainmenu !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidname = 0;
  }else { 
    isvalidname = 1;
  }
  if(inorder.trim() == -1){
    $("#menuorder_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select order number !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidinorder = 0;
  }else { 
    isvalidinorder = 1;
  }
  if(isvalidname == 1 && isvalidinorder == 1){

    var formData = new FormData($('#formmainmenu')[0]);
    if(ACTION==0){
      var uurl = SITE_URL+"menu/add-main-menu";
      
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
            new PNotify({title: "Main Menu successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
            $("#inorder option[value='"+inorder+"']").remove();
            resetdata();
        }else if(response==2){
            new PNotify({title: 'Main Menu already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
        }else{
          new PNotify({title: 'Main Menu not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"menu/update-main-menu";
      
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
            new PNotify({title: "Main Menu successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
            setTimeout(function() { window.location=SITE_URL+"menu"; }, 1500);
        }else if(response==2){
            new PNotify({title: 'Main Menu already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
        }else{
            new PNotify({title: 'Main Menu not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

