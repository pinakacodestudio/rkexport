function resetdata() {
    $("#defaultdesignation_div").removeClass("has-error is-focused");
    $("#designation_div").removeClass("has-error is-focused");
    
    if(ACTION==0){
        $('#defaultdesignation').val("0");
        $('#designationid').val("");
        
        $('#yes').prop("checked", true);
        $('#defaultdesignation').focus();
    }
    $('html, body').animate({scrollTop:0},'slow');  
}
function checkvalidation(addtype=0) {
   
    var defaultdesignation = $('#defaultdesignation').val();
    var designationid = $('#designationid').val();
    
    var isvaliddefaultdesignation = isvaliddesignationid = 1;
   
    PNotify.removeAll();
    if(defaultdesignation==0) {
        $("#defaultdesignation_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select default designation !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvaliddefaultdesignation = 0;
    }else {
        $("#defaultdesignation_div").removeClass("has-error is-focused");
    }
    if(designationid==null) {
        $("#designation_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select designation group !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvaliddesignationid = 0;
    } else {
        $("#designation_div").removeClass("has-error is-focused");
    }    
    if(isvaliddefaultdesignation == 1 && isvaliddesignationid == 1){
        var formData = new FormData($('#designationmappingform')[0]);
        if(ACTION == 0){ // INSERT
            var baseurl = SITE_URL + 'designation-mapping/designation-mapping-add';
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
                    $("#defaultdesignation_div").removeClass("has-error is-focused");
                    if(response==1){
                        new PNotify({title: 'Designation mapping successfully added.',styling: 'fontawesome',delay: '3000',type: 'success'});
                        if(addtype==1){
                            resetdata();
                        }else{
                            setTimeout(function() { window.location = SITE_URL + "designation-mapping";}, 500);
                        }
                    }else if(response==2){
                        new PNotify({title: 'Designation mapping already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        $("#defaultdesignation_div").addClass("has-error is-focused");
                    }else{
                        new PNotify({title: 'Designation mapping not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
            var baseurl = SITE_URL + 'designation-mapping/update-designation-mapping';
            $.ajax({
                url: baseurl,
                type: 'POST',
                data: formData,
                beforeSend: function(){
                    $('.mask').show();
                    $('#loader').show();
                },
                success: function(response){
                    $("#defaultdesignation_div").removeClass("has-error is-focused");
                    if(response==1){
                        new PNotify({title: 'Designation mapping successfully updated.',styling: 'fontawesome',delay: '3000',type: 'success'});
                    
                        setTimeout(function() { window.location = SITE_URL + "designation-mapping";}, 500);
                    }else if(response==2){
                        new PNotify({title: 'Designation mapping already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        $("#defaultdesignation_div").addClass("has-error is-focused");
                    }else{
                        new PNotify({title: 'Designation mapping not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
