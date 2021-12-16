$(document).ready(function() {

  if($('#oldprofileimage').val()!=''){
    var $imageupload = $('.imageupload');
    $imageupload.imageupload({
      url: SITE_URL,
      type: '1',
      allowedFormats: [ 'jpg', 'jpeg', 'png','ico']
    });
  }else{
    var $imageupload = $('.imageupload');
    $imageupload.imageupload({
      url: SITE_URL,
      type: '0',
      allowedFormats: [ 'jpg', 'jpeg', 'png','ico']
    });
  }

  $('#remove').click(function(){
    $('#removeoldImage').val('1');
  });

  $("#channelid").change(function(){

    var channelid = $(this).val();
    $('#productid')
        .find('option')
        .remove()
        .end()
        .append('<option value="0">Select Product</option>')
        .val('whatever')
        ;
    if(channelid!='' && channelid!=0){
      getproduct(channelid);
    }
    $('#productid').selectpicker('refresh');
  });
  var channelid = $("#channelid").val();
  getproduct(channelid);
});
function getproduct(channelid=0){
  var uurl = SITE_URL+"product/getProductByChannelId";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {channelid:channelid},
    dataType: 'json',
    async: false,
    success: function(response){

      for(var i = 0; i < response.length; i++) {

        var productname = response[i]['name'].replace("'","&apos;");
        if(DROPDOWN_PRODUCT_LIST==0){
            
            $('#productid').append($('<option>', { 
                value: response[i]['id'],
                text : productname
            }));
        }else{
            
            $('#productid').append($('<option>', { 
                value: response[i]['id'],
                //text : ucwords(response[i]['name'])
                "data-content" :'<img src="'+PRODUCT_PATH+response[i]['image']+'" style="width:40px">  ' + productname
            }));
        }
        
        if(productid!='' || productid!='0'){
          $('#productid').val(productid);
        }
      }
      $('#productid').selectpicker('refresh');
    },
    error: function(xhr) {
          //alert(xhr.responseText);
    },
  });
}
function resetdata(){

  $("#channelid_div").removeClass("has-error is-focused");
  $("#homebanner_div").removeClass("has-error is-focused");
  $("#subtitle_div").removeClass("has-error is-focused");

  if(ACTION==1){
  
  }else{
    $('#channelid').val('0');
    $('#homebanner_name').val('');
    $('.imageupload img').css({"border":"1px solid #f1f1f1"});

    var $imageupload = $('.imageupload');
    $('.imageupload img').attr('src',defaultprofileimgpath);
    $imageupload.imageupload({
      url: SITE_URL,
      type: '0'
    });
    $('#yes').prop("checked", true);
    $('.selectpicker').selectpicker('refresh');
    $('#homebanner_name').focus();
  }
  $('html, body').animate({scrollTop:0},'slow');  
}
function checkvalidation(){
  
  var channelid = $('#channelid').val();
  var titlename = $("#title").val().trim();
  var subtitle = $("#subtitle").val().trim();
    
  var fileimage = $('#profile_image').val();    
  var oldfileimage = $('#oldprofileimage').val();
  var removeimg = $('#removeoldImage').val();    
  var isvalidtitlename = isvalidfileimage = 0;
  var isvalidsubtitle = 1;
  
  PNotify.removeAll();
/*   if(titlename == ''){
    $("#homebanner_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter title !',styling: 'fontawesome',delay: '3000',type: 'error'});
    $( "#titlename" ).focus();
    isvalidtitlename = 0;
  }else { 
    if(titlename.length<1){
      $("#homebanner_div").addClass("has-error is-focused");
      new PNotify({title: 'Minmum 1 characters require for title !',styling: 'fontawesome',delay: '3000',type: 'error'});
      $( "#titlename" ).focus();
      isvalidtitlename = 0;
    }else{
      isvalidtitlename = 1;
    }
  }
  if(subtitle != ''){
    if(subtitle.length<1){
      $("#subtitle_div").addClass("has-error is-focused");
      new PNotify({title: 'Minmum 1 characters require for subtitle !',styling: 'fontawesome',delay: '3000',type: 'error'});
      $( "#subtitle" ).focus();
      isvalidsubtitle = 0;
    }
  } */

  isvalidsubtitle = 1;
  isvalidtitlename = 1;
  isvalidchannelid = 1;
  
  if(channelid == "" || channelid == null){
    $("#channelid_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select channel !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidchannelid = 0;
  }
  if(ACTION == 0){
    if(fileimage=="") {
      $('.imageupload img').css({"border":"1px solid #FFB9BD"});
      new PNotify({title: 'Please select image !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
      isvalidfileimage = 1;   
      $('.imageupload img').css({"border":"1px solid #f1f1f1"}); 
    }    
  } 

  if(ACTION == 1){ 
    if(fileimage=="" && removeimg=="1"){
      $('.imageupload img').css({"border":"1px solid #FFB9BD"});
      new PNotify({title: 'Please select image !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
      isvalidfileimage = 1;   
      $('.imageupload img').css({"border":"1px solid #f1f1f1"}); 
    }   
  }


  if(isvalidchannelid == 1 && isvalidtitlename == 1 && isvalidfileimage==1 && isvalidsubtitle==1){

    var formData = new FormData($('#homebannerform')[0]);
    if(ACTION==0){
      var uurl = SITE_URL+"home-banner/add-home-banner";
      
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
            new PNotify({title: "Home Banner successfully added !",styling: 'fontawesome',delay: '3000',type: 'success'});
            setTimeout(function() { window.location=SITE_URL+"home-banner"; }, 1500);
          }else if(response==2){
            new PNotify({title: 'Home Banner already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
            new PNotify({title: 'Home Banner not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"home-banner/update-home-banner";
      
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
              new PNotify({title: "Home Banner successfully updated !",styling: 'fontawesome',delay: '1500',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"home-banner"; }, 1500);
          }else if(response==2){
            new PNotify({title: 'Home Banner already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
              new PNotify({title: 'Home Banner not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

