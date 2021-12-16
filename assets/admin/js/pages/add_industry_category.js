function resetdata(){

  $("#industrycategory_div").removeClass("has-error is-focused");

  if(ACTION==0){
    $('#name').val('');
  }
 
  $('html, body').animate({scrollTop:0},'slow');
  
}
function checkvalidation(addtype=0){
  
  var name = $("#name").val().trim();
  
  var isvalidname  = 0 ;
  
  PNotify.removeAll();
  if(name == ''){
    $("#industrycategory_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter Industry Category name !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidname = 0;
  }else {
    if(name.length<3){
      $("#industrycategory_div").addClass("has-error is-focused");
      new PNotify({title: 'Industry Category name require minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidname = 0;
    }else{
      isvalidname = 1;
    }
  }

  if(isvalidname == 1)
  {

    var formData = new FormData($('#industrycategoryform')[0]);
    if(ACTION==0){
      var uurl = SITE_URL+"industry-category/add-industry-category";
      
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
            new PNotify({title: "Industry Category successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
            if(addtype==1){
              resetdata();
            }else{
            setTimeout(function() { window.location=SITE_URL+"industry-category"; }, 1500);
            }
          }else if(response==2){
            new PNotify({title: "Industry Category name already exists.",styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#name_div").addClass("has-error is-focused");
          }else{
            new PNotify({title: 'Industry Category not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"industry-category/update-industry-category";
      
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
              new PNotify({title: "Industry Category successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"industry-category"; }, 1500);
          }else if(response==2){
            new PNotify({title: "Industry Category name already exists.",styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#name_div").addClass("has-error is-focused");
          }else{
              new PNotify({title: 'Industry Category not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

