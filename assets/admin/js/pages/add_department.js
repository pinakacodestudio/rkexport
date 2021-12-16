function resetdata(){

  $("#name_div").removeClass("has-error is-focused");

  if(ACTION==0){
    $('#name').val('');
    $('#yes').prop('checked',true);
  }
 
  $('html, body').animate({scrollTop:0},'slow');
  
}
function checkvalidation(btntype=0){
  
  var name = $("#name").val().trim();
  
  var isvalidname  = 0 ;
  
  PNotify.removeAll();
  if(name == ''){
    $("#name_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter department name !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidname = 0;
  }else {
    if(name.length<3){
      $("#name_div").addClass("has-error is-focused");
      new PNotify({title: 'Department name require minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidname = 0;
    }else{
      isvalidname = 1;
    }
  }

  if(isvalidname == 1)
  {

    var formData = new FormData($('#departmentform')[0]);
    if(ACTION==0){
      var uurl = SITE_URL+"department/department-add";
      
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
            new PNotify({title: "Department successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
            if(btntype==1){
              resetdata();
            }else{
              setTimeout(function() { window.location=SITE_URL+"department"; }, 1500);
            }
          }else if(response==2){
            new PNotify({title: "Department name already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#name_div").addClass("has-error is-focused");
          }else{
            new PNotify({title: 'Department not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"department/update-department";
      
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
              new PNotify({title: "Department successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"department"; }, 1500);
          }else if(response==2){
            new PNotify({title: "Department name already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#name_div").addClass("has-error is-focused");
          }else{
              new PNotify({title: 'Department not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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


