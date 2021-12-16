
$(document).ready(function() {   
    if(ACTION==1){
        getSalesPersonChannel($('#salespersonid').val());
        getmember(channelid)
    }
});
/****EMPLOYEE CHANGE EVENT****/
$(document).on('change', '#salespersonid', function() { 
    getSalesPersonChannel(this.value);
});
/****EMPLOYEE CHANGE EVENT****/
$(document).on('change', '#workforchannelid', function() { 
    getmember(this.value);
});
function getSalesPersonChannel(salespersonid){
    
    $('#workforchannelid')
        .find('option')
        .remove()
        .end()
        .append('<option value="">Select Channel</option>')
        .val('0')
    ;
    $('#workforchannelid').selectpicker('refresh');

    if(ACTION==1){
        $('#memberid')
            .find('option')
            .remove()
            .end()
            .append('<option value="0">Select '+Member_label+'</option>')
            .val('0')
        ;
    }else{
        $('#memberid')
            .find('option')
            .remove()
            .end()
            .append('')
            .val('')
        ;
    }
    $('#memberid').selectpicker('refresh');
  
    if(salespersonid!=0){
      var uurl = SITE_URL+"sales-person-members/getSalesPersonChannel";
      
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {salespersonid:String(salespersonid)},
        dataType: 'json',
        async: false,
        success: function(response){
  
            for(var i = 0; i < response.length; i++) {
                $('#workforchannelid').append($('<option>', { 
                    value: response[i]['id'],
                    text : response[i]['name']
                }));
            }
            if(channelid!=0){
                $('#workforchannelid').val(channelid);
            }
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
      });
    }
    $('#workforchannelid').selectpicker('refresh');
}
function getmember(channelid){
    
    if(ACTION==1){
        $('#memberid')
            .find('option')
            .remove()
            .end()
            .append('<option value="0">Select '+Member_label+'</option>')
            .val('0')
        ;
    }else{
        $('#memberid')
            .find('option')
            .remove()
            .end()
            .append('')
            .val('')
        ;
    }
    
    $('#memberid').selectpicker('refresh');
  
    if(channelid!=0){
      var uurl = SITE_URL+"member/getmembers";
      
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {channelid:String(channelid)},
        dataType: 'json',
        async: false,
        success: function(response){
  
            for(var i = 0; i < response.length; i++) {
                $('#memberid').append($('<option>', { 
                    value: response[i]['id'],
                    text : response[i]['name']
                }));
            }
            if(memberid!=0){
                $('#memberid').val(memberid);
            }
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
      });
    }
    $('#memberid').selectpicker('refresh');
}

function resetdata() {
    $("#salesperson_div").removeClass("has-error is-focused");
    $("#workforchannel_div").removeClass("has-error is-focused");
    $("#member_div").removeClass("has-error is-focused");
    
    if(ACTION==0){
        $('#salespersonid,#workforchannelid').val("0");
        $('#memberid').val("");
        
        $(".selectpicker").selectpicker('refresh');
    }
    $('html, body').animate({scrollTop:0},'slow');  
}
function checkvalidation(addtype=0) {
   
    var salespersonid = $('#salespersonid').val();
    var workforchannelid = $('#workforchannelid').val();
    var memberid = $('#memberid').val();
    
    var isvalidsalespersonid = isvalidworkforchannelid = isvalidmemberid = 1;
   
    PNotify.removeAll();
    if(salespersonid==0) {
        $("#salesperson_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select sales person !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidsalespersonid = 0;
    }else {
        $("#salesperson_div").removeClass("has-error is-focused");
    }
    if(workforchannelid==0) {
        $("#workforchannel_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select channel !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidworkforchannelid = 0;
    } else {
        $("#workforchannel_div").removeClass("has-error is-focused");
    }   
   
    if(memberid==null || memberid==0) {
        $("#member_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select '+member_label+' !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmemberid = 0;
    } else {
        $("#member_div").removeClass("has-error is-focused");
    }  
    
    if(isvalidsalespersonid == 1 && isvalidworkforchannelid == 1 && isvalidmemberid == 1){
        var formData = new FormData($('#salespersonmemberform')[0]);
        if(ACTION == 0){ // INSERT
            var baseurl = SITE_URL + 'sales-person-members/sales-person-member-add';
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
                    if(response==1){
                        new PNotify({title: 'Sales person '+member_label+' successfully added.',styling: 'fontawesome',delay: '3000',type: 'success'});
                        if(addtype==1){
                            resetdata();
                        }else{
                            setTimeout(function() { window.location = SITE_URL + "sales-person-members";}, 500);
                        }
                    }else{
                        new PNotify({title: 'Sales person '+member_label+' not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
            var baseurl = SITE_URL + 'sales-person-members/update-sales-person-member';
            $.ajax({
                url: baseurl,
                type: 'POST',
                data: formData,
                beforeSend: function(){
                    $('.mask').show();
                    $('#loader').show();
                },
                success: function(response){
                    if(response==1){
                        new PNotify({title: 'Sales person '+member_label+' successfully updated.',styling: 'fontawesome',delay: '3000',type: 'success'});
                    
                        setTimeout(function() { window.location = SITE_URL + "sales-person-members";}, 500);
                    }else if(response==2){
                        new PNotify({title: 'Sales person '+member_label+' already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else{
                        new PNotify({title: 'Sales person '+member_label+' not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
