function resetdata() {
    $("#name_div").removeClass("has-error is-focused");
    
    if(ACTION==0){
        $('#name').val("");
        
        $('#unityes').prop("checked", true);
        $('#name').focus();
    }
    if(MODALVIEW == 0){
        $('html, body').animate({scrollTop:0},'slow');  
    }
}
function checkvalidationunit(addtype=0) {
   
    var name = $('#name').val().trim();
    var isvalidname = 0;
   
    PNotify.removeAll();
    if(name=="") {
        $("#name_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter product unit name !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else if(name.length < 2){
        $("#name_div").addClass("has-error is-focused");
        new PNotify({title: 'Product unit name required minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
        $("#name_div").removeClass("has-error is-focused");
        isvalidname = 1;
    }
    
    if(isvalidname ==1){
        var formData = new FormData($('#product-unit-form')[0]);
        if(ACTION == 0){ // INSERT
            var baseurl = SITE_URL + 'product-unit/product-unit-add';
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
                        new PNotify({title: 'Product unit successfully added.',styling: 'fontawesome',delay: '3000',type: 'success'});
                        if(MODALVIEW == 1){
                            resetdata();
                            $("#addunitModal").modal("hide");
                            if(data['status']==1){
                                $("#unitid option").first().after("<option value='"+data['unitid']+"' selected>"+data['name']+"</option>");
                                $("#unitid").selectpicker('refresh');
                            }
                        }else{
                            if(addtype==1){
                                resetdata();
                            }else{
                                setTimeout(function() { window.location = SITE_URL + "product-unit";}, 500);
                            }
                        }
                    }else if(data['error']==2){
                        new PNotify({title: 'Product unit name already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        $("#name_div").addClass("has-error is-focused");
                    }else if(data['error']==3){
                        new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
                        $("#name_div").addClass("has-error is-focused");
                    }else{
                        new PNotify({title: 'Product unit not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
            var baseurl = SITE_URL + 'product-unit/update-product-unit';
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
                        new PNotify({title: 'Product unit successfully updated.',styling: 'fontawesome',delay: '3000',type: 'success'});
                    
                        setTimeout(function() { window.location = SITE_URL + "product-unit";}, 500);
                    }else if(data['error']==2){
                        new PNotify({title: 'Product unit name already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        $("#name_div").addClass("has-error is-focused");
                    }else if(data['error']==3){
                        new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
                        $("#name_div").addClass("has-error is-focused");
                    }else{
                        new PNotify({title: 'Product unit not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
