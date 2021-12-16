$(document).ready(function() {
    
    $("[data-provide='city']").each(function () {
        var $element = $(this);
       
        $element.select2({
            allowClear: true,
            minimumInputLength: 3,    
            placeholder: $element.attr("placeholder"),            
            
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
    
                if (id !== "" && id!=='0') {
                    $.ajax($element.data("url"), {
                        data: {
                            ids: id,
                        },
                        type: "POST",
                        dataType: "json",
                    }).done(function (data) {
                        callback(data);    
                    });
                }else{
                $("#cityid").select2("data", { id: 0, text: "Select City" });
                }
            }
        });
    });

    $("#channelid").change(function(){
        getmembers();
    });
    if(ACTION==1 && CHANNELID!=0){
        getmembers();
    }
});
function getmembers(){
    
    var channelid = $("#channelid").val();
  
    $('#memberid')
        .find('option')
        .remove()
        .end()
        .append('<option value="0">Select '+Member_label+'</option>')
        .val('whatever')
        ;
    if(channelid!=""){
        var uurl = SITE_URL+"member/getmembers";
        $.ajax({
            url: uurl,
            type: 'POST',
            data: {channelid:channelid},
            dataType: 'json',
            async: false,
            success: function(response){
        
                for(var i = 0; i < response.length; i++) {
            
                    $('#memberid').append($('<option>', { 
                    value: response[i]['id'],
                    text : ucwords(response[i]['name'])
                    }));
            
                }
                if(MEMBERID!=0){
                    $('#memberid').val(MEMBERID);
                }
            },
            error: function(xhr) {
            //alert(xhr.responseText);
            },
        });
    }
    $('#memberid').selectpicker('refresh');
}

function resetdata(){

  $("#companyname_div").removeClass("has-error is-focused");
  $("#mobileno_div").removeClass("has-error is-focused");
  $("#member_div").removeClass("has-error is-focused");
  $("#email_div").removeClass("has-error is-focused");
  $("#website_div").removeClass("has-error is-focused");
  $("#address_div").removeClass("has-error is-focused");
  $("#trackingurl_div").removeClass("has-error is-focused");
  $('#s2id_cityid > a').css({"background-color":"#FFF","border":"#D2D2D2"});

  if(ACTION==0){
      
    $('#companyname').val('');
    $('#mobileno').val('');
    $('#contactperson').val('');
    $('#channelid').val('0');
    $('#memberid').val('0');
    $('#email').val('');
    $('#website').val('');
    $('#address').val('');
    $('#trackingurl').val('');
    $("#cityid").select2("val", "0");
    $('#yes').prop("checked", true);
    $('#companycompanyname').focus();
    
    $('.selectpicker').selectpicker('refresh');
  }else{
    $("#cityid").select2("val", [$('#cityid').val()]);
  }
  $('html, body').animate({scrollTop:0},'slow');
}
function checkvalidation(){
  
    var companyname = $("#companyname").val().trim();
    var mobileno = $("#mobileno").val();
    var channelid = $("#channelid").val();
    var memberid = $("#memberid").val();
    var email = $("#email").val();
    var address = $("#address").val().trim();
    var website = $("#website").val().trim();
    var trackurl = $("#trackingurl").val().trim();

    var isvalidcompanyname = isvalidmobileno = isvalidmemberid = isvalidemail = isvalidaddress = isvalidtrackurl = isvalidwebsite = 1;
    
    PNotify.removeAll();
    if(companyname == ''){
        $("#companyname_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter company name !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidcompanyname = 0;
    }else { 
        if(companyname.length<2){
        $("#companyname_div").addClass("has-error is-focused");
        new PNotify({title: 'Company name require minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidcompanyname = 0;
        }else{
            $("#companyname_div").removeClass("has-error is-focused");
        }
    }
    if(mobileno == ''){
        $("#mobileno_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter mobile number !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmobileno = 0;
    }else{
        if(mobileno.length != 10){
            $("#mobileno_div").addClass("has-error is-focused");
            new PNotify({title: 'Please enter 10 digit mobile number !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidmobileno = 0;
        }else{
            $("#mobileno_div").removeClass("has-error is-focused");
        }
    }
    if(channelid!="0"){
        if(memberid == 0){
        $("#member_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select '+member_label+' !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmemberid = 0;
        }else{
        $("#member_div").removeClass("has-error is-focused");
        }
    }else{
        $("#member_div").removeClass("has-error is-focused");
    }
    if(email != '' && !ValidateEmail(email)){
        $("#email_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter valid Email address !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidemail = 0;
    }else{
        $("#email_div").removeClass("has-error is-focused");
    }
    if(address != "" && address.length<3){
        $("#address_div").addClass("has-error is-focused");
        new PNotify({title: 'Address have must be 3 characters!',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidaddress = 0;
    }else{
        $("#address_div").removeClass("has-error is-focused");
    }
    if(website != ''){
        if(!isUrl(website)){
            $("#website_div").addClass("has-error is-focused");
            new PNotify({title: 'Please enter valid website url !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidwebsite = 0;
        }else{
            $("#website_div").removeClass("has-error is-focused");
        }
    }
    if(trackurl != ''){
        if(!isUrl(trackurl)){
            $("#trackingurl_div").addClass("has-error is-focused");
            new PNotify({title: 'Please enter valid tracking url !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidtrackurl = 0;
        }else{
            $("#trackingurl_div").removeClass("has-error is-focused");
        }
    }

    if(isvalidcompanyname == 1 && isvalidmobileno == 1 && isvalidmemberid == 1 && isvalidemail == 1 && isvalidaddress == 1 && isvalidwebsite == 1 && isvalidtrackurl == 1){

        var formData = new FormData($('#transporterform')[0]);
        if(ACTION==0){
        var uurl = SITE_URL+"transporter/add-transporter";
        
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
                var data = JSON.parse(response);
                if(data['error']==1){
                    new PNotify({title: "Transporter successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
                    resetdata();
                }else if(data['error']==2){
                    new PNotify({title: 'Transporter already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(data['error']==3){
                    new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
                }else{
                    new PNotify({title: 'Transporter not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
        var uurl = SITE_URL+"transporter/update-transporter";
        
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
                var data = JSON.parse(response);
                if(data['error']==1){
                new PNotify({title: "Transporter successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
                setTimeout(function() { window.location=SITE_URL+"transporter"; }, 1500);
                }else if(data['error']==2){
                    new PNotify({title: 'Transporter already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(data['error']==3){
                    new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
                }else{
                    new PNotify({title: 'Transporter not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

