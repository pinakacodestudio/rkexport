
$(document).ready(function() { 
  if($('#oldnewsimage').val()!=''){
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
  $("#metakeywords").each(function () {
    var $element = $(this);
    
    $maximumselectionsize=25;
    if($element.data("selectionlength") !=undefined){
        $maximumselectionsize=$element.data("selectionlength");  
    }
    $element.select2({  
      
        language: {
            inputTooLong: function(args) {
              // args.maximum is the maximum allowed length
              // args.input is the user-typed text
              return "You typed too much";
            },
            noResults: function() {
              return "No results found";
            },
            searching: function() {
              return "Searching...";
            },
            maximumSelected: function(args) {
              // args.maximum is the maximum number of items the user may select
              return "You can enter only 25 keywords";
            }
        },
        allowClear: true,
        minimumInputLength: 3,
        tokenSeparators: [','],       
        placeholder: $element.attr("placeholder"),            
        multiple:true,
        width: '100%',
        maximumSelectionSize:$maximumselectionsize,
        createSearchChoice: function(term, data) {
          if ($(data).filter(function() {
            return this.text.localeCompare(term) === 0;
            }).length === 0) {
              return {
              id: term,
              text: term
              };
            }
        },
        data: [],
        tags:true,
        initSelection: function (element, callback) {
            var id = $(element).val();        
            if (id !== "") {
                data=[];
                var result = id.split(',');
                for (var prop in result) {
  
                    keyword = {};
                    keyword['id'] =result[prop]
                    keyword['text'] =result[prop];
                    data.push(keyword);
                }
                callback(data);
            }
        }
    
    });
  });
});



$(document).ready(function() {

  if(ACTION==1){
    var channelid = $("#channelid").val();
    var brandid = $("#brandid").val();
    getmembers(channelid,brandid);
  }
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
function resetdata(){

  $("#newsname_div").removeClass("has-error is-focused");
  $("#description_div").removeClass("has-error is-focused");
  $("#channel_div").removeClass("has-error is-focused");
  $("#member_div").removeClass("has-error is-focused");
  $("#metakeywords").val('').trigger('change.select2');
  $("#metatitle").val('');
  $("#metadescription").val('');

  if(ACTION==1){
    if($('#newsimage').val()!=''){
      var $imageupload = $('.imageupload');
      $('.imageupload img').attr('src',testimonialsimagepath+'/'+$('#newsimage').val());
      $imageupload.imageupload({
        url: SITE_URL,
        type: '1'
      });
    }    
    $('#removeoldImage').val('0');
      $('.cke_inner').css({"background-color":"#FFF","border":"1px solid #D2D2D2"});
      var description = $('#description').val();
      CKEDITOR.instances['description'].setData(description);
  }else{
    $('.imageupload').imageupload({
      url: SITE_URL,
      type: '0',
    });
  
  
  $('#removeoldImage').val('0');
    

      $("#newsname").val("");
      $('.cke_inner').css({"background-color":"#FFF","border":"1px solid #D2D2D2"});
      CKEDITOR.instances['description'].setData("");
      $("#channelid").val('0');
      $("#brandid").val('0');
      getmembers(0);
      $(".selectpicker").selectpicker('refresh');
      $('#yes').prop("checked", true);
      $("#newsname").focus();
      $("#metakeywords").val('').trigger('change.select2');
      $("#metatitle").val('');
      $("#metadescription").val('');
  }
  $('html, body').animate({scrollTop:0},'slow');
}
function checkvalidation(){

    var newsname = $("#newsname").val().trim();
    var channelid = $("#channelid").val();
    var memberid = $("#memberid").val();
    var description = CKEDITOR.instances['description'].getData();
    var metatitle = $("#metatitle").val();
    var metakeyword = $("#metakeywords").val();
    var metadescription = $("#metadescription").val();
  
    
    description = encodeURIComponent(description);
    CKEDITOR.instances['description'].updateElement();
   
    var isvalidmemberid = isvalidchannelid = isvaliddescription = isvalidnewsname = 0;
    var isvalidmetatitle = isvalidmetakeyword = isvalidmetadescription = 1;
    
    PNotify.removeAll();

    if(channelid==null || channelid=='') {
      $("#channel_div").addClass("has-error is-focused");
      new PNotify({title: 'Please select channel !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidchannelid = 0;
    } else {
      $("#channel_div").removeClass("has-error is-focused");
      isvalidchannelid = 1;  
    }

    if(memberid==null || memberid=='') {
        $("#member_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select '+member_label+' !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmemberid = 0;
    } else {
        $("#member_div").removeClass("has-error is-focused");
        isvalidmemberid = 1;  
    }

    if(newsname=='') {
        $("#newsname_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter News Name !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidnewsname = 0;
    } else {
      if(newsname.length <= 3){
        $("#newsname_div").addClass("has-error is-focused");
        new PNotify({title: 'Name require Minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidnewsname = 0;
      }else{
        $("#newsname_div").removeClass("has-error is-focused");
        isvalidnewsname = 1;  
      }
      
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
    if(metatitle !=''){
      if(metatitle.length < 17 || metatitle.length > 70){
        $("#metatitle_div").addClass("has-error is-focused");
        new PNotify({title: "Enter meta title between 16 to 70 characters !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmetatitle = 0;
      }else{
        $("#metatitle_div").removeClass("has-error is-focused");
      }
    }
    var totalkeyword = metakeyword.split(',').length;
    if(metakeyword.trim() != ''){
      if( totalkeyword < 3 || totalkeyword > 10){
        $('#s2id_metakeywords > ul').css({"background-color":"#FFECED","border":"1px solid #FFB9BD"});
        new PNotify({title: "Enter meta keyword between 4 to 10 !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmetakeyword = 0;
      }else{
        $('#s2id_metakeywords > ul').css({"background-color":"#FFF","border":"1px solid #cccccc"});
      }
    }
    if(metadescription != ''){
      if(metadescription.length < 25 || metadescription.length > 150){
        $("#metadescription_div").addClass("has-error is-focused");
        new PNotify({title: "Enter meta description between 25 to 150 characters !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmetadescription = 0;
      }else{
        $("#metadescription_div").removeClass("has-error is-focused");
      }
    }
    
     
    if(isvalidchannelid == 1 && isvalidmemberid == 1 && isvaliddescription == 1 && isvalidnewsname == 1 && isvalidmetatitle == 1 && isvalidmetakeyword == 1 && isvalidmetadescription == 1){
                            
      var formData = new FormData($('#form-news')[0]);
        if(ACTION == 0){    
          var uurl = SITE_URL+"news/news-add";
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
              if(response==1 || response==17){
                new PNotify({title: 'News successfully added !',styling: 'fontawesome',delay: '3000',type: 'success'});
                setTimeout(function() { window.location=SITE_URL+"news"; }, 1000);
              }
              else if(response == 2 || response==19){
                new PNotify({title: 'News Name already added !',styling: 'fontawesome',delay: '3000',type: 'error'});
              }   
              else{
                new PNotify({title: 'News Not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
          var uurl = SITE_URL+"news/news-update";
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
                      new PNotify({title: 'News successfully Updated !',styling: 'fontawesome',delay: '3000',type: 'success'});
                      setTimeout(function() { window.location=SITE_URL+"news"; }, 1500);
                    }
                    else if(response == 2){
                      new PNotify({title: 'News Name already added !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }   
                    else{
                      new PNotify({title: 'News not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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