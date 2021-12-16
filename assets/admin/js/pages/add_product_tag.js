$(document).ready(function(){

    $("#tag,#slug").keyup(function (e) {
        var tag = $(this).val();
        $('#slug').val(tag.toLowerCase().replace(/ /g,'-').replace(/[^\w-]+/g,''));
    });
});  
function resetdata(){

    $("#tag_div").removeClass("has-error is-focused");
    $("#slug_div").removeClass("has-error is-focused");
  
    if(ACTION==1){
    }else{
      $('#tag,#slug').val('');
      $('#yes').prop("checked", true);
      $('#tag').focus();
    }
    $('html, body').animate({scrollTop:0},'slow');
}
function checkvalidation(addtype=0) {
    
    var tag = $("#tag").val().trim();
    var slug = $("#slug").val().trim();
    
    var isvalidtag = isvalidslug = 0;
    
    PNotify.removeAll();
    if(tag == ''){
      $("#tag_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter tag !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidtag = 0;
    }else { 
      if(tag.length<2){
        $("#tag_div").addClass("has-error is-focused");
        new PNotify({title: 'Minmum 2 characters require for tag!',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidtag = 0;
      }else{
        isvalidtag = 1;
      }
    }
    if(slug == ''){
        $("#slug_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter slug !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidslug = 0;
    }else { 
        if(slug.length<2){
          $("#slug_div").addClass("has-error is-focused");
          new PNotify({title: 'Minmum 2 characters require for slug!',styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidslug = 0;
        }else{
            isvalidslug = 1;
        }
    }
  
    if(isvalidtag == 1 && isvalidslug == 1){
  
      var formData = new FormData($('#producttagform')[0]);
      if(ACTION==0){
        var uurl = SITE_URL+"product-tag/add-product-tag";
        
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
              if(addtype==1){
                resetdata();
            }else{
                setTimeout(function() { window.location = SITE_URL + "product-tag";}, 500);
            }
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
        var uurl = SITE_URL+"product-tag/update-product-tag";
        
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
                setTimeout(function() { window.location=SITE_URL+"product-tag"; }, 1500);
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
  
  