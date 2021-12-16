$(document).ready(function() {
    $('#lastchange').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        orientation: 'top',
        autoclose: true,
        todayBtn: "linked"
    });
});
function setsitemapslug(name){
  $('#slug').val(name.toLowerCase().replace(/ /g,'-').replace(/[^\w\/_-]+/g,''));
}
function resetdata(){

  $("#slug_div").removeClass("has-error is-focused");
  
  if(ACTION==1){
    $('.selectpicker').selectpicker('refresh');
  }else{
    $('#slug').val('');
    $('#priority').val('10');
    $('#changefrequency').val('0');
    $('.selectpicker').selectpicker('refresh');
    $('#yes').prop("checked", true);
  }
  
  $('html, body').animate({scrollTop:0},'slow');
}
  function checkvalidation(addtype=0){
    
    var slug = $("#slug").val();
    var isvalidslug =  0;
    
    PNotify.removeAll();
    if(slug == ''){
      $("#slug_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter sitemap name !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidslug = 0;
    }else {
      if(slug.length<2){
        $("#slug_div").addClass("has-error is-focused");
        new PNotify({title: 'Sitemap name require minmum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidslug = 0;
      }else{
        isvalidslug = 1;
      }
    }  
    if(isvalidslug == 1){
  
      var formData = new FormData($('#sitemap')[0]);
      if(ACTION==0){
        var uurl = SITE_URL+"sitemap/sitemap-add";
        
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
              new PNotify({title: "Sitemap  successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
              if(addtype==1){
                resetdata();
              }else{
                setTimeout(function() { window.location=SITE_URL+"sitemap"; }, 1500);
              }
            }else if(response==2){
              new PNotify({title: 'Sitemap already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
              $("#email_div").addClass("has-error is-focused");
            }else{
              new PNotify({title: 'Sitemap  not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
        var uurl = SITE_URL+"sitemap/sitemap-update";
        
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
                new PNotify({title: " Sitemap successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
                setTimeout(function() { window.location=SITE_URL+"sitemap"; }, 1500);
            }else if(response==2){
              new PNotify({title: 'Sitemap already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
              $("#email_div").addClass("has-error is-focused");
            }else{
                new PNotify({title: 'Sitemap not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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