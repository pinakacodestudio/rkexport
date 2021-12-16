/*if(ACTION==1){
  var $bannerfileupload = $('.bannerfileupload');
  $bannerfileupload.bannerfileupload({
    url: SITE_URL,
    type: '1',
    allowedFormats: [ 'jpg', 'jpeg', 'png','ico']
  });
}else{
  var $bannerfileupload = $('.bannerfileupload');
  $bannerfileupload.bannerfileupload({
    url: SITE_URL,
    type: '0',
    allowedFormats: [ 'jpg', 'jpeg', 'png','ico']
  });
}*/
function filetype(id,type){
  $("#bannerfile").val("");
  $("#Filetext").val("");
  $("#youtubeurl").val("");
  if(type==1 || type==2){
    $('#fileupload').show();
    $('#youtube').hide();
  }else if(type==3){
    $('#fileupload').hide();
    $('#youtube').show();
  }
}
function validfile(obj){
  var val = obj.val();
  var id = obj.attr('id').match(/\d+/);
  var filename = obj.val().replace(/C:\\fakepath\\/i, '');
  var filetype = $('input[name=filetype]:checked').val();

  switch(val.substring(val.lastIndexOf('.') + 1).toLowerCase()){
    case 'jpeg': case 'jpg': case 'png' : case 'avi' : case 'mov': case 'flv': case 'mp4': case 'wmv':

      if(filetype==1)
      $("#Filetext").val(filename);
      isvalidfiletext = 1;
      $("#bannerfile_div").removeClass("has-error is-focused");
      break;
    default:
      $("#bannerfile").val("");
      $("#Filetext"+id).val("");
      isvalidfiletext = 0;
      $("#bannerfile_div").addClass("has-error is-focused");
      new PNotify({title: 'Please upload valid image or video file !',styling: 'fontawesome',delay: '3000',type: 'error'});
      break;
  }
}
function resetdata(){
  
  $("#title_div").removeClass("has-error is-focused");
  $("#bannerfile_div").removeClass("has-error is-focused");
  $('.cke_inner').css({"border":"none"});

  if(ACTION==1){
    /*var $bannerfileupload = $('.bannerfileupload');
    $('.bannerfileupload img').attr('src',BANNER_bannerfile_URL+$('#oldbanner').val());
    $bannerfileupload.bannerfileupload({
      url: SITE_URL,
      type: '1'
    });*/
  }else{
    $('#title').val('');
    $('.cke_inner').css({"background-color":"#FFF","border":"1px solid #D2D2D2"});
    CKEDITOR.instances['description'].setData("");

    /*var $bannerfileupload = $('.bannerfileupload');
    $bannerfileupload.bannerfileupload({
      url: SITE_URL,
      type: '0'
    });*/
  }
  //$('#bannerimg img').css({"border":"1px solid #f1f1f1"});
  $('html, body').animate({scrollTop:0},'slow');  

} 

function checkvalidation() {

  var title = $("#title").val();
  var description = CKEDITOR.instances['description'].getData();
  description = encodeURIComponent(description);
  CKEDITOR.instances['description'].updateElement();

  var bannerfilebtn = $("#bannerfilebtn").html();

  var isvalidtitle = isvalidfiletext = 0;
  var isvaliddescription = 1;
 
  if(title.trim() == ''){
    $("#title_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter title !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidtitle = 0;
  }else { 
    if(title.length<3){
      $("#title_div").addClass("has-error is-focused");
      new PNotify({title: "Title require minimum 3 characters !",styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidtitle = 0;
    }else{
      isvalidtitle = 1;  
    }
  }
  var filetype = $('input[name=bannerfiletype]:checked').val();

  if(filetype==1 || filetype==2){
    if($("#Filetext").val()==''){
      $("#bannerfile_div").addClass("has-error is-focused");
      new PNotify({title: 'Please select banner file !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidfiletext = 0;
    }else{
      isvalidfiletext = 1;
    }
  }else if(filetype==3){
    if($("#youtubeurl").val()==''){
      $("#bannerfile_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter youtube product video !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidfiletext = 0;
    }else if(!validateYouTubeUrl($("#youtubeurl").val())){
      $("#bannerfile_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter valid youtube product video!',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidfiletext = 0;
    }else{
      isvalidfiletext = 1;
    }
  }
  
  if(isvalidtitle == 1 && isvalidfiletext ==1 && isvaliddescription == 1){

    var formData = new FormData($('#bannerform')[0]);
    if(ACTION == 0){    
      var uurl = SITE_URL+"website-banner/banner-add";
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
            new PNotify({title: "Banner successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
            //resetdata();
            setTimeout(function() { window.location=SITE_URL+"website_banner"; }, 1500);
          }else if(response==2){
            new PNotify({title: 'Banner file not uploaded !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(response==3){
            new PNotify({title: 'Invalid type of banner file !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
            new PNotify({title: "Banner not added !",styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"website-banner/updatebanner";
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
            new PNotify({title: "Banner successfully updated.",styling: 'fontawesome',delay: '3000',type: 'success'});
            setTimeout(function() { window.location=SITE_URL+"website_banner"; }, 1500);
          }else if(response==2){
            new PNotify({title: 'Banner file not uploaded !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(response==3){
            new PNotify({title: 'Invalid type of banner file !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
            new PNotify({title: "Banner not updated !",styling: 'fontawesome',delay: '3000',type: 'error'});
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

