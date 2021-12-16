$(document).ready(function() {

    if($('#oldbrandimage').val() != ''){
        var $imageupload = $('#brandimageupload');
        $imageupload.imageupload({
            url: SITE_URL,
            type: '1',
            maxFileSizeKb : UPLOAD_MAX_FILE_SIZE,
            allowedFormats: [ 'jpg', 'jpeg', 'png','ico','gif']
        });
    }else{
        var $imageupload = $('#brandimageupload');
        $imageupload.imageupload({
          url: SITE_URL,
          type: '0',
          maxFileSizeKb : UPLOAD_MAX_FILE_SIZE,
          allowedFormats: [ 'jpg', 'jpeg', 'png','ico','gif']
        });
    }

    $('#remove').click(function(){
        $('#removebrandimage').val('1');
    });

});

function checkvalidation(addtype=0) {
   
    var brandname = $('#brandname').val().trim();
    var isvalidbrandname = 0;
   
    PNotify.removeAll();
    if(brandname=="") {
        $("#brandname_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter brand name !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else if(brandname.length < 2){
        $("#brandname_div").addClass("has-error is-focused");
        new PNotify({title: 'Brand name required minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
        $("#brandname_div").removeClass("has-error is-focused");
        isvalidbrandname = 1;
    }
    
    if(isvalidbrandname ==1){
        var formData = new FormData($('#brand-form')[0]);
        if(ACTION == 0){ // INSERT
            var baseurl = SITE_URL + 'brand/brand-add';
            $.ajax({
                
                url: baseurl,
                type: 'POST',
                data: formData,
                //async: false,
                beforeSend: function(){
                    $('.mask').show();
                    $('#loader').show();
                },
                success: function(response){
                    var data = JSON.parse(response);
                    if(data['error']==1){
                        new PNotify({title: 'Brand successfully added.',styling: 'fontawesome',delay: '3000',type: 'success'});
                        if(addtype==1){
                            resetdata();
                        }else{
                            setTimeout(function() { window.location = SITE_URL + "brand";}, 500);
                        }
                    }else if(data['error']==2) {
                        new PNotify({title: 'Brand already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else if(data['error']==3) {
                        new PNotify({title: 'Image does not Uploaded.',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else if(data['error']==4) {
                        new PNotify({title: 'Image type does not valid !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else if(data['error']==5) {
                        new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
                        $("#brandname_div").addClass("has-error is-focused");
                    }else if(data['error']==6) {
                        new PNotify({title: 'File is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE)+')!',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else if(data['error']==0) {
                        new PNotify({title: 'Brand not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }
                },
                error: function(xhr) {
                },
                complete: function(){
                    $('.mask').hide();
                    $('#loader').hide();
                },
                cache: false,
                contentType: false,
                processData: false
            });
        } else {
                 // MODIFY
            var baseurl = SITE_URL + 'brand/update-brand';
            $.ajax({
                url: baseurl,
                type: 'POST',
                data: formData,
                beforeSend: function(){
                    $('.mask').show();
                    $('#loader').show();
                },
                success: function(response){
                    var data = JSON.parse(response);
                    if(data['error']==1){
                        new PNotify({title: 'Brand successfully updated.',styling: 'fontawesome',delay: '3000',type: 'success'});
                    
                        setTimeout(function() { window.location = SITE_URL + "brand";}, 500);
                    }  else if(data['error']==2){
                        new PNotify({title: 'Brand already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    } else if(data['error']==3){
                        new PNotify({title: 'Image does not Uploaded.',styling: 'fontawesome',delay: '3000',type: 'error'});
                    } else if(data['error']==4){
                        new PNotify({title: 'Image type does not valid !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    } else if(data['error']==5){
                        new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
                    } else if(data['error']==6) {
                        new PNotify({title: 'Sorry, Your file is too large. Only '+size+' is allowed !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else if(data['error']==0){
                        new PNotify({title: 'Brand not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }
                },
                error: function(xhr) {
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

function resetdata() {
    $("#brandname_div").removeClass("has-error is-focused");
    
    if(ACTION==0){
        $('#brandname').val("");
        $('#brandimageupload').imageupload('reset');

        $('#yes').prop("checked", true);
        $('#brandname').focus();
    }
    $('html, body').animate({scrollTop:0},'slow');  
}
