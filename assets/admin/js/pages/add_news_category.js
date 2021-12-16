function setslug(name){
    $('#slug').val(name.toLowerCase().replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-'));
}
$("#slug").keyup(function (e) {
    $("#slug").val(($("#slug").val()).toLowerCase());
});

function resetdata(){

    $("#name_div").removeClass("has-error is-focused");
    $("#slug_div").removeClass("has-error is-focused");

    if(ACTION==0){
        
        $('#name').val("");
        $('#slug').val("");
        $('#yes').prop("checked", true);
    }
    $('html, body').animate({scrollTop:0},'slow');  
}

function checkvalidation(addtype=0){
  
  var name = $("#name").val();
  var slug = $("#slug").val();
 
  var isvalidname = isvalidslug = 0;

  PNotify.removeAll();
  if(name == ""){
    $("#name_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter category name !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }else { 
    $("#name_div").removeClass("has-error is-focused");
    isvalidname = 1;
  }

  if(slug == ""){
    $("#slug_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter category slug !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }else if(slug.match(/\s/g)){
    $("#slug_div").addClass("has-error is-focused");
    new PNotify({title: "category slug does not valid !",styling: 'fontawesome',delay: '3000',type: 'error'});
  }else { 
    $("#slug_div").removeClass("has-error is-focused");
    isvalidslug = 1;
  }
  
 if(isvalidname==1 && isvalidslug==1){
  
    var formData = new FormData($('#newscategoryform')[0]);
    if(ACTION==0){
      var uurl = SITE_URL+"news-category/add-news-category";
      
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
            new PNotify({title: "News category successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
            if(addtype==1){
                resetdata();
            }else{
                setTimeout(function() { window.location=SITE_URL+"news-category"; }, 1500);
            }
          }else if(response==2){
            new PNotify({title: 'News category already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
            new PNotify({title: 'News category not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"news-category/update-news-category";
      
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
            new PNotify({title: "News category successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
            setTimeout(function() { window.location=SITE_URL+"news-category"; }, 1500);
          }else if(response==2){
            new PNotify({title: 'News category already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
            new PNotify({title: 'News category not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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