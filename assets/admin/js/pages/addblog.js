$("[data-provide='blogcategoryid']").each(function () {
   var $element = $(this);
  
  $element.select2({    
    allowClear: true,
    minimumInputLength: 3,     
    width: '100%',  
    placeholder: $element.attr("placeholder"),         
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
    ajax: {
      url: $element.data("url"),
      dataType: 'json',
      type: "POST",
      quietMillis: 50,
      data: function (term) {
        return {
            term: term,
        };
      },
      results: function (data) {            
        return {
          results: $.map(data, function (item) {
            return {
                text: item.text,                        
                id: item.id
            }
          })
        };
      }
    },
    initSelection: function (element, callback) {
      var id = $(element).val();        
      if (id !== "" && id != 0) {
        $.ajax($element.data("url"), {
            data: {
                ids: id,
            },
            type: "POST",
            dataType: "json",
        }).done(function (data) {                
            callback(data);
        });
      }
    }
  });
});
$("#metakeywords").each(function () {
    var $element = $(this);
    
    $maximumselectionsize=10;
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
              return "You can enter only 10 keywords";
            }
        },
        allowClear: true,
        minimumInputLength: 3,          
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
if(ACTION==1 && $('#oldblog').val()!=''){
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
function resetdata(){
  
  $("#title_div").removeClass("has-error is-focused");
  $("#metatitle_div").removeClass("has-error is-focused");
  $("#metakeywords_div").removeClass("has-error is-focused");
  $("#metadescription_div").removeClass("has-error is-focused");
  $('.cke_inner').css({"border":"none"});

  if(ACTION==1){
    var $imageupload = $('.imageupload');
    if($('#oldblog').val()!=''){
      $('.imageupload img').attr('src',BLOG_IMAGE_URL+$('#oldblog').val());
      $imageupload.imageupload({
        url: SITE_URL,
        type: '1'
      });
    }else{
      $imageupload.imageupload({
        url: SITE_URL,
        type: '0'
      });
    }
    
  }else{
    $('#title').val('');
    $('.cke_inner').css({"background-color":"#FFF","border":"1px solid #D2D2D2"});
    CKEDITOR.instances['description'].setData("");
    $("#blogcategoryid").select2("val", "0");
    var $imageupload = $('.imageupload');
    $imageupload.imageupload({
      url: SITE_URL,
      type: '0'
    });
  }
  $('#blogimg img').css({"border":"1px solid #f1f1f1"});
  $('html, body').animate({scrollTop:0},'slow');  

} 

function checkvalidation() {

  var title = $("#title").val();
  var metatitle = $("#metatitle").val();
  var metakeyword = $("#metakeywords").val();
  var metadescription = $("#metadescription").val();
  var description = CKEDITOR.instances['description'].getData();
  description = encodeURIComponent(description);
  CKEDITOR.instances['description'].updateElement();

  var isvalidtitle = isvaliddescription = 0;
  var isvalidmetatitle = isvalidmetakeyword = isvalidmetadescription = 1;
 
  PNotify.removeAll();
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
  
  if(description.trim() ==''){
    $("#description_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter description !',styling: 'fontawesome',delay: '3000',type: 'error'});
    $('.cke_inner').css({"background-color":"#FFECED","border":"1px solid #e51c23"});
    isvaliddescription = 0;
  }else{
    if(description.length<3){
      $("#description_div").addClass("has-error is-focused");
      $('.cke_inner').css({"background-color":"#FFECED","border":"1px solid #e51c23"});
      new PNotify({title: "Description require minimum 3 characters !",styling: 'fontawesome',delay: '3000',type: 'error'});
      isvaliddescription = 0;
    }else{
      isvaliddescription = 1;
      $('.cke_inner').css({"border":"none"});
    }
  }
  if(metatitle !=''){
    if(metatitle.length < 17 || metatitle.length > 70){
      $("#metatitle_div").addClass("has-error is-focused");
      new PNotify({title: "Enter meta title between 16 to 70 characters !",styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidmetatitle = 0;
    }else{
      isvalidmetatitle = 1;
    }
  }
  var totalkeyword = metakeyword.split(',').length;
  if(metakeyword.trim() != ''){
    if( totalkeyword < 4 || totalkeyword > 10){
      $('#s2id_metakeywords > ul').css({"background-color":"#FFECED","border":"1px solid #FFB9BD"});
      new PNotify({title: "Enter meta keyword between 4 to 10 !",styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidmetakeyword = 0;
    }else{
      isvalidmetakeyword = 1;
      $('#s2id_metakeywords > ul').css({"background-color":"#FFF","border":"1px solid #cccccc"});
    }
  }
  if(metadescription != ''){
    if(metadescription.length < 25 || metadescription.length > 150){
      $("#metadescription_div").addClass("has-error is-focused");
      new PNotify({title: "Enter meta description between 25 to 150 characters !",styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidmetadescription = 1;
    }else{
      isvalidmetadescription = 1;
    }
  }
  
  if(isvalidtitle == 1 && isvaliddescription == 1 && isvalidmetatitle == 1 && isvalidmetakeyword == 1 && isvalidmetadescription == 1){

    var formData = new FormData($('#blogform')[0]);
    if(ACTION == 0){    
      var uurl = SITE_URL+"blog/addblog";
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
            new PNotify({title: "Blog successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
            resetdata();
          }else if(response==2){
            new PNotify({title: 'Blog image not uploaded !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(response==3){
            new PNotify({title: 'Invalid type of blog image !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
            new PNotify({title: "Blog not added !",styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"blog/updateblog";
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
            new PNotify({title: "Blog successfully updated.",styling: 'fontawesome',delay: '3000',type: 'success'});
            setTimeout(function() { window.location=SITE_URL+"blog"; }, 1500);
          }else if(response==2){
            new PNotify({title: 'Blog image not uploaded !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(response==3){
            new PNotify({title: 'Invalid type of blog image !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
            new PNotify({title: "Blog not updated !",styling: 'fontawesome',delay: '3000',type: 'error'});
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

