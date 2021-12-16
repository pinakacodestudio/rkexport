$(document).ready(function(){
      var frontendmenuid = $("#frontendmenuid").val();
      if(frontendmenuid = 0){
        getfrontendsubmenu(frontendmenuid);
      }
    });
    function setslug(name){
      $('#slug').val(name.toLowerCase().replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-'));
    }
    $("#slug").keyup(function (e) {
      $("#slug").val(($("#slug").val()).toLowerCase());
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
    
   $('#frontendmenuid').change(function(){
      var frontendmenuid = $("#frontendmenuid").val();
     
      $('#frontendsubmenuid')
          .find('option')
          .remove()
          .end()
          .append('<option value="0">Select Sub Menu</option>')
          .val('whatever')
      ;
           
    if(frontendsubmenuid = 0){
       getfrontendsubmenu(frontendmenuid);
      }
     $('#frontendsubmenuid').selectpicker('refresh');
    });
    function resetdata(){
    
        $("#title_div").removeClass("has-error is-focused");
        $("#slug_div").removeClass("has-error is-focused");
        $("#description_div").removeClass("has-error is-focused");
        $("#metatitle_div").removeClass("has-error is-focused");
        $("#metakeywords_div").removeClass("has-error is-focused");
        $("#metadescription_div").removeClass("has-error is-focused");
        $('.cke_inner').css({"border":"none"});
    
        if(ACTION==1){
            $('.cke_inner').css({"background-color":"#FFF","border":"1px solid #D2D2D2"});
            var description = $('#description').val();
            CKEDITOR.instances['description'].setData(description);
        }else{
            $('.cke_inner').css({"background-color":"#FFF","border":"1px solid #D2D2D2"});
            CKEDITOR.instances['description'].setData("");
        }
        $('.selectpicker').selectpicker('refresh');  
        $('html, body').animate({scrollTop:0},'slow');
    }
    
    function checkvalidation(){
    
        var title = $("#title").val().trim();
        var slug = $("#slug").val().trim();
        var description = CKEDITOR.instances['description'].getData();
        description = encodeURIComponent(description);
        CKEDITOR.instances['description'].updateElement();
        var metatitle = $("#metatitle").val();
        var metakeyword = $("#metakeywords").val();
        var metadescription = $("#metadescription").val();
        
        var isvaliddescription= isvalidtitle = isvalidslug = 0;
        var isvalidmetatitle = isvalidmetakeyword = isvalidmetadescription = 1;
        
        if(title == ''){
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
    
        if(slug == ''){
          $("#slug_div").addClass("has-error is-focused");
          new PNotify({title: 'Please enter link !',styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidslug = 0;
        }else { 
          if(slug.length<3){
            $("#slug_div").addClass("has-error is-focused");
            new PNotify({title: "Link require minimum 3 characters !",styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidslug = 0;
          }else{
            isvalidslug = 1;  
          }
        }
    
        if(description.trim() == 0 || description.length < 4){
            $("#description_div").addClass("has-error is-focused");
            $('.cke_inner').css({"background-color":"#FFECED","border":"1px solid #e51c23"});
            new PNotify({title: 'Please enter content !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvaliddescription = 0;
        }else { 
            isvaliddescription = 1;
            $('.cke_inner').css({"border":"none"});
        }
        if(metatitle !=''){
          if(metatitle.length < 50 || metatitle.length > 85){
            $("#metatitle_div").addClass("has-error is-focused");
            new PNotify({title: "Enter meta title between 50 to 85 characters !",styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidmetatitle = 0;
          }else{
            isvalidmetatitle = 1;
          }
        }
        var totalkeyword = metakeyword.split(',').length;
        if(metakeyword.trim() != ''){
          if( totalkeyword < 5 || totalkeyword > 25){
            $('#s2id_metakeywords > ul').css({"background-color":"#FFECED","border":"1px solid #FFB9BD"});
            new PNotify({title: "Enter meta keyword between 5 to 25 !",styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidmetakeyword = 0;
          }else{
            isvalidmetakeyword = 1;
            $('#s2id_metakeywords > ul').css({"background-color":"#FFF","border":"1px solid #cccccc"});
          }
        }
        if(metadescription != ''){
          if(metadescription.length < 100 || metadescription.length > 185){
            $("#metadescription_div").addClass("has-error is-focused");
            new PNotify({title: "Enter meta description between 100 to 185 characters !",styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidmetadescription = 0;
          }else{
            isvalidmetadescription = 1;
          }
        }
                            
        if(isvaliddescription==1 && isvalidtitle==1 && isvalidslug==1 && isvalidmetatitle == 1 && isvalidmetakeyword == 1 && isvalidmetadescription == 1){
                                
          var formData = new FormData($('#managewebsitecontentform')[0]);
            if(ACTION == 0){    
              var uurl = SITE_URL+"manage-website-content/manage-website-content-add";
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
                    new PNotify({title: "Website content successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
                    setTimeout(function() { window.location=SITE_URL+"manage-website-content"; }, 1500);
                  }else if(response==2){
                    new PNotify({title: "Website already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
                  }else{
                    new PNotify({title: "Website content not added !",styling: 'fontawesome',delay: '3000',type: 'error'});
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
              var uurl = SITE_URL+"manage-website-content/updatemanagecontent";
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
                        new PNotify({title: "Website content successfully updated.",styling: 'fontawesome',delay: '3000',type: 'success'});
                        setTimeout(function() { window.location=SITE_URL+"manage-website-content"; }, 1500);
                      }else if(response==2){
                        new PNotify({title: "Website already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
                      }else{
                        new PNotify({title: "Website content not updated !",styling: 'fontawesome',delay: '3000',type: 'error'});
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