$(document).ready(function() {

  /* if(ACTION == 1){
    var channelid = $("#channelid").val();
    // console.log(channelid);
    if(channelid!=0){
      getmembers(channelid,memberid);
    }
  } */
  $("#channelid").change(function(){
    var channelid = $(this).val();
    var brandid = $("#brandid").val();
    getmembers(channelid,brandid);
  });
  $("#brandid").change(function(){
    var brandid = $(this).val();
    var channelid = $("#channelid").val();
    getmembers(channelid,brandid);
  });
});
function resetdata(){
    
    $("#channel_div").removeClass("has-error is-focused");
    $("#member_div").removeClass("has-error is-focused");
    $("#description_div").removeClass("has-error is-focused");

    if(ACTION==1){
        $('.cke_inner').css({"background-color":"#FFF","border":"1px solid #D2D2D2"});
        var description = $('#description').val();
        CKEDITOR.instances['description'].setData(description);
    }else{
        $("#channelid").val('0');
        $("#brandid").val('0');
        getmembers(0);
        $('.cke_inner').css({"background-color":"#FFF","border":"1px solid #D2D2D2"});
        CKEDITOR.instances['description'].setData("");
        $('.selectpicker').selectpicker('refresh');
    }
    $('html, body').animate({scrollTop:0},'slow');
}
function getmembers(channelid,brandid=0){
  $('#memberid')
      .find('option')
      .remove()
      .end()
      .append('')
      .val('whatever')
  ;
  $('#memberid').selectpicker('refresh');

  if(!$.isEmptyObject(channelid)){
    var uurl = SITE_URL+"member/get-multiple-channel-members";
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {channelid:channelid,brandid:brandid},
      dataType: 'json',
      async: false,
      success: function(response){

        for(var i = 0; i < response.length; i++) {

          $('#memberid').append($('<option>', { 
            value: response[i]['id'],
            text : ucwords(response[i]['name'])
          }));

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

    var channelid = $("#channelid").val();
    var membername = $("#memberid").val();
    var description = CKEDITOR.instances['description'].getData();
    description = encodeURIComponent(description);
    CKEDITOR.instances['description'].updateElement();
    
    var isvaliddescription = isvalidmember = isvalidchannelid = 0;
    
    if(channelid==null || channelid=='') {
      $("#channel_div").addClass("has-error is-focused");
      new PNotify({title: 'Please select channel !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidchannelid = 0;
    } else {
      $("#channel_div").removeClass("has-error is-focused");
      isvalidchannelid = 1;  
    }

    if(membername==null || membername=='') {
        $("#member_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select '+member_label+' !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmember = 0;
    } else {
        $("#member_div").removeClass("has-error is-focused");
        isvalidmember = 1;  
    }

    if(description.trim() == ''){
        $("#description_div").addClass("has-error is-focused");
        $('.cke_inner').css({"background-color":"#FFECED","border":"1px solid #e51c23"});
        new PNotify({title: 'Please enter description !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvaliddescription = 0;
    }else {
      if(description.length < 3){
        $("#description_div").addClass("has-error is-focused");
        $('.cke_inner').css({"background-color":"#FFECED","border":"1px solid #e51c23"});
        new PNotify({title: 'Description require minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvaliddescription = 0;
      }else {
          $("#description_div").removeClass("has-error is-focused");
          $('.cke_inner').css({"background-color":"#FFF","border":"1px solid #D2D2D2"});
          isvaliddescription = 1;
      }
    }
     
    if(isvalidchannelid == 1 && isvaliddescription == 1 && isvalidmember == 1){
                            
      var formData = new FormData($('#form-notification')[0]);
        if(ACTION == 0){    
          var uurl = SITE_URL+"notification/notification-add";
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
                new PNotify({title: 'Notification successfully Send !',styling: 'fontawesome',delay: '3000',type: 'success'});
                resetdata();
              }
              else if(response == 2){
                new PNotify({title: 'Notification not set !',styling: 'fontawesome',delay: '3000',type: 'error'});
              }   
              else{
                new PNotify({title: 'Notification Not Send !',styling: 'fontawesome',delay: '3000',type: 'error'});
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