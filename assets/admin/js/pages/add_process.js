function resetdata() {
    $("#name_div").removeClass("has-error is-focused");
    $("#description_div").removeClass("has-error is-focused");
    
    if(ACTION==0){
        $('#name').val("");
        $('#description').val("");
        
        $('#yes').prop("checked", true);
        $('#name').focus();
    }
    $('html, body').animate({scrollTop:0},'slow');  
}
function checkvalidation(addtype=0) {
   
    var name = $('#name').val().trim();
    var description = $('#description').val().trim();
    
    var isvalidname = isvaliddescription = 1;
   
    PNotify.removeAll();
    if(name=="") {
        $("#name_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter process name !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidname = 0;
    } else if(name.length < 2){
        $("#name_div").addClass("has-error is-focused");
        new PNotify({title: 'Process name required minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidname = 0;
    } else {
        $("#name_div").removeClass("has-error is-focused");
    }
    if(description!="" && description.length < 3) {
        $("#description_div").addClass("has-error is-focused");
        new PNotify({title: 'Description required minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvaliddescription = 0;
    } else {
        $("#description_div").removeClass("has-error is-focused");
    }    
    if(isvalidname == 1 && isvaliddescription == 1){
        var formData = new FormData($('#process-form')[0]);
        if(ACTION == 0){ // INSERT
            var baseurl = SITE_URL + 'manufacturing-process/process-add';
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
                    $("#name_div").removeClass("has-error is-focused");
                    if(data['error']==1){
                        new PNotify({title: 'Process successfully added.',styling: 'fontawesome',delay: '3000',type: 'success'});
                        if(addtype==1){
                            resetdata();
                        }else{
                            setTimeout(function() { window.location = SITE_URL + "manufacturing-process";}, 500);
                        }
                    }else if(data['error']==2){
                        new PNotify({title: 'Process name already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        $("#name_div").addClass("has-error is-focused");
                    }else if(data['error']==3){
                        new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
                        $("#name_div").addClass("has-error is-focused");
                    }else{
                        new PNotify({title: 'Process not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
            var baseurl = SITE_URL + 'manufacturing-process/update-process';
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
                    $("#name_div").removeClass("has-error is-focused");
                    if(data['error']==1){
                        new PNotify({title: 'Process successfully updated.',styling: 'fontawesome',delay: '3000',type: 'success'});
                    
                        setTimeout(function() { window.location = SITE_URL + "manufacturing-process";}, 500);
                    }else if(data['error']==2){
                        new PNotify({title: 'Process name already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        $("#name_div").addClass("has-error is-focused");
                    }else if(data['error']==3){
                        new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
                        $("#name_div").addClass("has-error is-focused");
                    }else{
                        new PNotify({title: 'Process not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
