function setslug(name){
  $('#slug').val(name.toLowerCase().replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-'));
}
$("#slug").keyup(function (e) {
  $("#slug").val(($("#slug").val()).toLowerCase());
});
function resetdata(){  
  
  $("#name_div").removeClass("has-error is-focused");
    
  if(ACTION==1){
    $('html, body').animate({scrollTop:0},'slow');
  }else{
    $('#name').val('');
   
   
    $('#name').focus();
    $('html, body').animate({scrollTop:0},'slow');  

  }
}
function checkvalidation(){
  
      var name = $("#name").val().trim();
     
      var isvalidname  = 0;
     
    
      PNotify.removeAll();
      if(name == ''){
        $("#name_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter blog category name !',styling: 'fontawesome',delay: '3000',type: 'error'});
        $( "#name" ).focus();
        isvalidname = 0;
      }else { 
        if(name.length<2){
          $("#name_div").addClass("has-error is-focused");
          new PNotify({title: 'Minmum 2 characters require for blog category name !',styling: 'fontawesome',delay: '3000',type: 'error'});
          
          isvalidname = 0;
        }else{
          isvalidname = 1;
        }
      }
    
    
     
      
      
      
    
 if(isvalidname == 1){

  var formData = new FormData($('#blogcategoryform')[0]);
        if(ACTION==0){
          var uurl = SITE_URL+"blog-category/blog-category-add";
          
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
                new PNotify({title: "Blog category successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
                //resetdata();
                setTimeout(function() { window.location=SITE_URL+"blog-category"; }, 1500);
              }else if(response==2){
                new PNotify({title: 'Blog category  already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
                $("#name_div").addClass("has-error is-focused");
              }else{
                new PNotify({title: 'Blog category not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
          var uurl = SITE_URL+"blog-category/update-blog-category";
          
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
                  new PNotify({title: "Blog category  successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
                  setTimeout(function() { window.location=SITE_URL+"blog-category"; }, 1500);
              }else if(response==2){
                new PNotify({title: 'Blog categoryalready exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
                $("#email_div").addClass("has-error is-focused");
              }else{
                  new PNotify({title: 'Blog category not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
    
    
    