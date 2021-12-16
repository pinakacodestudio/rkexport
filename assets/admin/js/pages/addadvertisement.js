$(document).ready(function() { 
  if($('#oldtestimonialsimage').val()!=''){
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
});

$(document).ready(function() {

  if(ACTION == 1){
    var adpage_id = $("#adpage_id").val();
    getpagesection(adpage_id);
  }
  $("#adpage_id").change(function(){
    var adpage_id = $(this).val();
    getpagesection(adpage_id);
    // var memberid = $("#memberid").val();
  })
})
function resetdata(){

  $("#features_name_div").removeClass("has-error is-focused");

  if(ACTION==1){
    if($('#oldtestimonialsimage').val()!=''){
      var $imageupload = $('.imageupload');
      $('.imageupload img').attr('src',testimonialsimagepath+'/'+$('#oldtestimonialsimage').val());
      $imageupload.imageupload({
        url: SITE_URL,
        type: '1'
      });
    }else{
      $('.imageupload').imageupload({
        url: SITE_URL,
        type: '0',
      });
    }
    
    $('#removeoldImage').val('0');
    
    $('.selectpicker').selectpicker('refresh');
    $('html, body').animate({scrollTop:0},'slow');
  }else{

    $('.imageupload').imageupload({
      url: SITE_URL,
      type: '0',
    });
    $('#features_name').val('');
    $('#yes').prop("checked", true);
    $('#features_name').focus();
  }
  $('html, body').animate({scrollTop:0},'slow');  
}
function checkvalidation(){
  
  // var features_name = $("#features_name").val().trim();
  var adpage_id = $("#adpage_id").val().trim();
  var adpage_section_id = $("#adpage_section_id").val().trim();
  var adtype = $("#adtype").val().trim();
  /* google_ad
amazon_ad */
  var isvalidadpage_id = isvalidadpage_section_id = isvalidadtype = 0;

  // var isvalidfeatures_name = 0 ;
  
  PNotify.removeAll();
  if(adpage_id == '0' || adpage_id == ''){
    $("#adpage_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select page !',styling: 'fontawesome',delay: '3000',type: 'error'});
    // $("#adpage_id").focus();
    isvalidadpage_id = 0;
  }else { 
    isvalidadpage_id = 1;
  }

  if(adpage_section_id == '0' || adpage_section_id == ''){
    $("#adpage_section_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select page section !',styling: 'fontawesome',delay: '3000',type: 'error'});
    $( "#adpage_section_id" ).focus();
    isvalidadpage_section_id = 0;
  }else { 
    isvalidadpage_section_id = 1;
  }
  

  if(adtype == '0' || adtype == ''){
    $("#adtype_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select type !',styling: 'fontawesome',delay: '3000',type: 'error'});
    $( "#adtype_id" ).focus();
    isvalidadtype = 0;
  }else { 
    isvalidadtype = 1;
  }
  if(isvalidadpage_id==1 && isvalidadpage_section_id==1 && isvalidadtype==1){
    // console.log(isvalidadpage_section_id);return false;
    var formData = new FormData($('#advertisementform')[0]);
    if(ACTION==0){
      var uurl = SITE_URL+"advertisement/advertisement_add";
      
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
            new PNotify({title: "Advertisement successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
            setTimeout(function() { window.location=SITE_URL+"advertisement"; }, 1500);
          }else if(response==2){
            new PNotify({title: 'Advertisement already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
            new PNotify({title: 'Advertisement not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"advertisement/updateadvertisement";
      
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
              new PNotify({title: "Advertisement successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"advertisement"; }, 1500);
          }else if(response==2){
            new PNotify({title: 'Advertisement already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
              new PNotify({title: 'Advertisement not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

function getpagesection(page){
  var uurl = SITE_URL+"advertisement/getpagesection";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {page:page},
    dataType: 'json',
    async: false,
    success: function(response){
      $('#adpage_section_id')
      .find('option')
      .remove()
      .end()
      .append('<option value="0">Select Page Section</option>')
      .val('whatever')
      ;

      $.each(response, function(key, value) {
        $.each(response[key], function(key1, value1) {
          $('#adpage_section_id').append($('<option>', { 
            value: key1,
            text : value1
          }));
        })
      })

/*       if(memberid!=0){
        $("#memberid").val(memberid);
      } */
      // $('#product'+prow).val(areaid);
      if(adpage_section_id!=0){
        $("#adpage_section_id").val(adpage_section_id);
      }
      $('#adpage_section_id').selectpicker('refresh');
    },
    error: function(xhr) {
          //alert(xhr.responseText);
        },
      });
}