isvalidfiletext = 1;
$(document).ready(function() 
{
  if(ACTIONIMG==1){
    var $imageupload = $('.imageupload');
    $imageupload.imageupload({
      url: SITE_URL,
      type: '1',
      maxFileSizeKb : UPLOAD_MAX_FILE_SIZE_CATALOG,
      allowedFormats: [ 'jpg', 'jpeg', 'png','ico']
    });
  }else{
    var $imageupload = $('.imageupload');
    $imageupload.imageupload({
      url: SITE_URL,
      type: '0',
      maxFileSizeKb : UPLOAD_MAX_FILE_SIZE_CATALOG,
      allowedFormats: [ 'jpg', 'jpeg', 'png','ico']
    });
  }

  $('#remove').click(function(){
    $('#fileimage').val("");
    $('#removeimg').val("1");
  });

  if(ACTION==1){
    var channelid = $("#channelid").val();
    getmembers(channelid);
  }
  $("#channelid").change(function(){
    var channelid = $(this).val();
    getmembers(channelid);
  })
});

function resetdata(){

  // $('#form-catalog')[0].reset();
  $("#description_div").removeClass("has-error is-focused");
  $("#catalogname_div").removeClass("has-error is-focused");
  $("#fileimage_div").removeClass("has-error is-focused");
  $("#textfile_div").removeClass("has-error is-focused");
  $("#channel_div").removeClass("has-error is-focused");
  $("#member_div").removeClass("has-error is-focused");
  $('.imageupload img').css({"border":"1px solid #f1f1f1"});

  if(ACTION==0){
    $('.imageupload').imageupload('reset');
    $('.cke_inner').css({"background-color":"#FFF","border":"1px solid #D2D2D2"});
    CKEDITOR.instances['description'].setData("");
    $("#channelid").val('0');
    getmembers(0);
    $(".selectpicker").selectpicker('refresh');
  }
  $('html, body').animate({scrollTop:0},'slow');
}

function validfile(obj){
  var val = obj.val();
  var id = obj.attr('id').match(/\d+/);
  var filename = obj.val().replace(/C:\\fakepath\\/i, '');
 
  switch(val.substring(val.lastIndexOf('.') + 1).toLowerCase()){
    case 'pdf':

      $("#textfile").val(filename);
      isvalidfiletext = 1;
      $("#textfile_div").removeClass("has-error is-focused");
      break;
    default:
      $("#filepdf").val("");
      $("#textfile").val("");
      isvalidfiletext = 0;
      $("#textfile_div").addClass("has-error is-focused");
      new PNotify({title: 'Please upload valid file !',styling: 'fontawesome',delay: '3000',type: 'error'});
      break;
  }
}

function getmembers(channelid){
  $('#memberid')
      .find('option')
      .remove()
      .end()
      .append('')
      .val('whatever')
  ;
  $('#memberid').selectpicker('refresh');

  if(channelid!=0){
    var uurl = SITE_URL+"member/getmembers";
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {channelid:channelid},
      dataType: 'json',
      async: false,
      success: function(response){

        for(var i = 0; i < response.length; i++) {
          
          if(ACTION==1){
            if(memberidarr!=null || memberidarr!=''){
             
              memberidarr = memberidarr.toString().split(',');
             
              if(memberidarr.includes(response[i]['id'])){
                $('#memberid').append($('<option>', { 
                  value: response[i]['id'],
                  selected: "selected",
                  text : ucwords(response[i]['name'])
                }));
              }else{
                $('#memberid').append($('<option>', { 
                  value: response[i]['id'],
                  text : ucwords(response[i]['name'])
                }));
              }
            }
          }else{
            $('#memberid').append($('<option>', { 
              value: response[i]['id'],
              text : ucwords(response[i]['name'])
            }));
          }
        }
      },
      error: function(xhr) {
        //alert(xhr.responseText);
      },
    });
    $('#memberid').selectpicker('refresh');
  }
}

function checkvalidation(){

  var catalogname = $("#catalogname").val().trim();
  var description = CKEDITOR.instances['description'].getData();
  description = encodeURIComponent(description);
      CKEDITOR.instances['description'].updateElement();
  var fileimage = $('#fileimage').val();    
  var oldfileimage = $('#oldfileimage').val();
  var filepdf = $('#filepdf').val();    
  var removeimg = $('#removeimg').val();    

  var channelid = $("#channelid").val();
  var memberid = $("#memberid").val();

  var svaliddescription= isvalidcatalogname =  0;
  var isvalidmemberid = isvalidchannelid = isvalidfileimage = isvalidfilepdf = 1;

  if(catalogname=="" || catalogname.length <= 3) {
      $("#catalogname_div").addClass("has-error is-focused");
      new PNotify({title: 'Catalog Name required minimum 4 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
  } else {
    $("#catalogname_div").addClass("has-not-error");
    isvalidcatalogname = 1;
  }
  if(CHANNELWISECATALOG==1 && ACTION == 0){
    if(channelid==null || channelid=='') {
      $("#channel_div").addClass("has-error is-focused");
      new PNotify({title: 'Please select channel !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidchannelid = 0;
    } else {
      $("#channel_div").removeClass("has-error is-focused");
    }

    if(memberid==null || memberid=='') {
        $("#member_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select '+member_label+' !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmemberid = 0;
    } else {
        $("#member_div").removeClass("has-error is-focused");
    }
  }
  if(description.trim() == 0 || description.length < 4){
      // $("#description_div").addClass("has-error is-focused");
      $('.cke_inner').css({"background-color":"#FFECED","border":"1px solid #e51c23"});
      new PNotify({title: 'Please enter description',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvaliddescription = 0;
  }else { 
      isvaliddescription = 1;
      $('.cke_inner').css({"border":"none"});
  }


  if(ACTION == 0){
    if(filepdf=="") { 
      if(fileimage=="") {
        $("#fileimage_div").addClass("has-error is-focused");
        $('.imageupload img').css({"border":"1px solid #FFB9BD"});
        new PNotify({title: 'Please select image !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidfileimage = 0;   
        isvalidfilepdf = 0; 
      } else {
        $("#fileimage_div").removeClass("has-error is-focused");
        $('.imageupload img').css({"border":"1px solid #f1f1f1"}); 
        $("#textfile_div").removeClass("has-error is-focused");
        isvalidfileimage = 1;   
        isvalidfilepdf = 1;   
      }
    }    
  
    if(fileimage=="") {
      if(filepdf=="") {
        $("#textfile_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select File!',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidfileimage = 0;   
        isvalidfilepdf = 0; 
      } else {
        $("#textfile_div").removeClass("has-error is-focused");
        $("#fileimage_div").removeClass("has-error is-focused");
        $('.imageupload img').css({"border":"1px solid #f1f1f1"}); 
        isvalidfilepdf = 1;   
        isvalidfileimage = 1;
      }   
    } 
  } 

  if(ACTION == 1){ 
    if(filepdf=="" && oldfilepdf==""){
      if(fileimage=="" && removeimg=="1"){
        $("#fileimage_div").addClass("has-error is-focused");
        $('.imageupload img').css({"border":"1px solid #FFB9BD"});
        new PNotify({title: 'Please select image !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidfileimage = 0;   
        isvalidfilepdf = 0; 
      } else {
        $("#fileimage_div").removeClass("has-error is-focused");
        $('.imageupload img').css({"border":"1px solid #f1f1f1"}); 
        $("#textfile_div").removeClass("has-error is-focused");
        isvalidfileimage = 1;   
        isvalidfilepdf = 1;   
      }   
    }else{
      isvalidfileimage = 1;   
    }
  }

  if(ACTION == 1){ 
    if(fileimage=="" && removeimg=="1"){
      if(filepdf=="" && oldfilepdf==""){
        $("#textfile_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select File!',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidfileimage = 0;   
        isvalidfilepdf = 0; 
      } else {
        $("#textfile_div").removeClass("has-error is-focused");
        $("#fileimage_div").removeClass("has-error is-focused");
        $('.imageupload img').css({"border":"1px solid #f1f1f1"}); 
        isvalidfilepdf = 1;   
        isvalidfileimage = 1;
      }   
    }else{
      isvalidfilepdf = 1;   
    }
  }
  
  if(isvalidchannelid == 1 && isvalidmemberid == 1 && isvalidfileimage==1 && isvalidfilepdf == 1 && isvaliddescription && isvalidcatalogname){
                          
    var formData = new FormData($('#form-catalog')[0]);
      if(ACTION == 0){    
        var uurl = SITE_URL+"catalog/catalog-add";
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
              new PNotify({title: 'Catalog Successfully Added !',styling: 'fontawesome',delay: '3000',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"catalog"; }, 1500);
            }else if(response == 2) {
              new PNotify({title: 'Catalog name already added !',styling: 'fontawesome',delay: '3000',type: 'error'});
            } else if(response == 3){
              new PNotify({title: 'Image not Added.',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else if(response == 4) {
              new PNotify({title: 'Image type does not valid !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else if(response == 5){
              new PNotify({title: 'PDF not Added.',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else if(response == 6){
              new PNotify({title: 'PDF type does not valid !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else if(response == 7){
              new PNotify({title: 'Notification not set active !',styling: 'fontawesome',delay: '3000',type: 'error'});
             setTimeout(function() { window.location=SITE_URL+"Catalog"; }, 1500);
            }else if(response == 8){
              new PNotify({title: 'File is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE_CATALOG)+') !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else{
              new PNotify({title: 'Catalog data not Added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
        var uurl = SITE_URL+"catalog/update-catalog";
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
                  new PNotify({title: 'Catalog Successfully updated !',styling: 'fontawesome',delay: '3000',type: 'success'});
                  setTimeout(function() { window.location=SITE_URL+"catalog"; }, 1500);
                }else if(response == 2) {
                    new PNotify({title: 'Catalog name already added !',styling: 'fontawesome',delay: '3000',type: 'error'});
                } else if(response == 3){
                  new PNotify({title: 'Image not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(response == 4) {
                  new PNotify({title: 'Image type does not valid !',styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(response == 5){
                  new PNotify({title: 'PDF not updted !',styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(response == 6){
                  new PNotify({title: 'PDF type does not valid !',styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(response == 7){
                  new PNotify({title: 'File is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE_CATALOG)+') !',styling: 'fontawesome',delay: '3000',type: 'error'});
                }else{
                  new PNotify({title: 'Catalog data not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
                
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