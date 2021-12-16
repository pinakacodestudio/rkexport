function resetdata() {
    $("#inputunitid_div").removeClass("has-error is-focused");
    $("#inputunitvalue_div").removeClass("has-error is-focused");
    $("#outputunitid_div").removeClass("has-error is-focused");
    $("#outputunitvalue_div").removeClass("has-error is-focused");

    if(ACTION==0){
        $('#productid').val("0");
        $('#inputunitid').val("0");
        $('#inputunitvalue').val("");
        $('#outputunitid').val("0");
        $('#outputunitvalue').val("");
    }
    $('html, body').animate({scrollTop:0},'slow');  
}
function checkvalidation(addtype=0) {
   
    var inputunitid = $('#inputunitid').val();
    var inputunitvalue = $('#inputunitvalue').val().trim();
    var outputunitid = $('#outputunitid').val();
    var outputunitvalue = $('#outputunitvalue').val().trim();

    var isvalidinputunitid = isvalidinputunitvalue = isvalidoutputunitid = isvalidoutputunitvalue = 1;
   
    PNotify.removeAll();
    if(inputunitid==0) {
        $("#inputunitid_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select input unit !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidinputunitid = 0;
    } else {
        $("#inputunitid_div").removeClass("has-error is-focused");
    }
    if(inputunitvalue==0) {
        $("#inputunitvalue_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter input unit value !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidinputunitvalue = 0;
    } else {
        $("#inputunitvalue_div").removeClass("has-error is-focused");
    }
    if(outputunitid==0) {
        $("#outputunitid_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select output unit !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidoutputunitid = 0;
    } else {
        $("#outputunitid_div").removeClass("has-error is-focused");
    }
    if(outputunitvalue==0) {
        $("#outputunitvalue_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter output unit value !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidoutputunitvalue = 0;
    } else {
        $("#outputunitvalue_div").removeClass("has-error is-focused");
    }
    if(isvalidinputunitid==1 && isvalidoutputunitid==1) {
        if(inputunitid == outputunitid){
           $("#inputunitid_div,#outputunitid_div").addClass("has-error is-focused");
            new PNotify({title: 'Please select different value of input & output unit !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidoutputunitid = 0;
        } else {
            $("#inputunitid_div,#outputunitid_div").removeClass("has-error is-focused");
        }
    }
    if(isvalidinputunitid == 1 && isvalidinputunitvalue == 1 && isvalidoutputunitid == 1 && isvalidoutputunitvalue == 1){
        var formData = new FormData($('#unit-conversation-form')[0]);
        if(ACTION == 0){ // INSERT
            var baseurl = SITE_URL + 'unit-conversation/unit-conversation-add';
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
                        new PNotify({title: 'Unit conversation successfully added.',styling: 'fontawesome',delay: '3000',type: 'success'});
                        if(addtype==1){
                            resetdata();
                        }else{
                            setTimeout(function() { window.location = SITE_URL + "unit-conversation";}, 500);
                        }
                    }else if(data['error']==2){
                        new PNotify({title: 'Unit conversation already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else if(data['error']==3){
                        new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else{
                        new PNotify({title: 'Unit conversation not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
            var baseurl = SITE_URL + 'unit-conversation/update-unit-conversation';
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
                        new PNotify({title: 'Unit conversation successfully updated.',styling: 'fontawesome',delay: '3000',type: 'success'});
                    
                        setTimeout(function() { window.location = SITE_URL + "unit-conversation";}, 500);
                    }else if(data['error']==2){
                        new PNotify({title: 'Unit conversation already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else if(data['error']==3){
                        new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else{
                        new PNotify({title: 'Unit conversation not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
