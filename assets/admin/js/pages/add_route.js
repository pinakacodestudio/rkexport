$(document).ready(function() {
    getprovince(DEFAULTCOUNTRYID);
    getcity(provinceid);

    $(".add_btn").hide();
    $(".add_btn:last").show();
    
    $('#provinceid').on('change', function (e) {
            
        $('#cityid')
            .find('option')
            .remove()
            .end()
            .append('<option value="">Select City</option>')
            .val('0')
        ;
        $('#cityid').selectpicker('refresh');
        getcity(this.value);
    });

    $('#totaltime').datetimepicker({
        pickDate: false,
        minuteStep: 5,
        pickerPosition: 'bottom-right',
        format: 'hh:ii:ss',
        autoclose: true,
        showMeridian: false,
        startView: 1,
        maxView: 1,
    });

    $('.yesno input[type="checkbox"]').bootstrapToggle({
        on: 'Yes',
        off: 'No',
        onstyle: 'primary',
        offstyle: 'danger'
    });
});
/****CHANNEL CHANGE EVENT****/
$(document).on('change', 'select.channelid', function() { 
    var divid = $(this).attr("div-id");
   
    $("#uniquemember"+divid).val(this.value+"_0");
    getmember(divid);
});
/****MEMBER CHANGE EVENT****/
$(document).on('change', 'select.memberid', function() { 
    var divid = $(this).attr("div-id");
   
    var channelid = $("#channelid"+divid).val();
    $("#uniquemember"+divid).val(channelid+"_"+this.value);
});

function getmember(divid){
  
    $("#memberid"+divid).find('option')
                .remove()
                .end()
                .val('0')
                .append('<option value="0">Select '+Member_label+'</option>')
              ;
    $("#memberid"+divid).selectpicker('refresh');
    var channelid = $("#channelid"+divid).val();
  
    if(channelid!=0){
        var uurl = SITE_URL+"member/getmembers";
        $.ajax({
            url: uurl,
            type: 'POST',
            data: {channelid:channelid},
            dataType: 'json',
            async: false,
            success: function(response){
    
                for(var i = 0; i < response.length; i++) {
        
                    $("#memberid"+divid).append($('<option>', { 
                        value: response[i]['id'],
                        text : ucwords(response[i]['namewithcodeormobile'])
                    }));
                }
            },
            error: function(xhr) {
                //alert(xhr.responseText);
            },
        });
        $("#memberid"+divid).selectpicker('refresh');
    }
}
function addNewMemberRaw(){

    var divcount = parseInt($(".countmembers:last").attr("id").match(/\d+/))+1;
    
    html = '<div class="col-md-12 p-n countmembers" id="countmembers'+divcount+'">\
        <input type="hidden" name="uniquemember[]" id="uniquemember'+divcount+'">\
        <div class="col-sm-3 pl-sm pr-sm">\
            <div class="form-group" id="channel'+divcount+'_div">\
                <div class="col-sm-12">\
                    <select id="channelid'+divcount+'" name="channelid[]" class="selectpicker form-control channelid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="'+divcount+'">\
                        <option value="0">Select Channel</option>\
                        '+CHANNEL_DATA+'\
                    </select>\
                </div>\
            </div>\
        </div>\
        <div class="col-sm-4 pl-sm pr-sm">\
            <div class="form-group" id="member'+divcount+'_div">\
                <div class="col-md-12">\
                    <select id="memberid'+divcount+'" name="memberid[]" class="selectpicker form-control memberid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="'+divcount+'">\
                        <option value="">Select '+Member_label+'</option>\
                    </select>\
                </div>\
            </div>\
        </div>\
        <div class="col-sm-1 pl-sm pr-sm">\
            <div class="form-group" id="priority'+divcount+'_div">\
                <div class="col-md-12">\
                <input type="text" id="priority'+divcount+'" name="priority[]" class="form-control priority" div-id="'+divcount+'">\
                </div>\
            </div>\
        </div>\
        <div class="col-sm-1 pl-sm pr-sm">\
            <div class="form-group" id="active'+divcount+'_div">\
                <div class="col-md-12">\
                    <div class="yesno mt-xs">\
                        <input type="checkbox" id="active'+divcount+'" name="active'+divcount+'" value="1" checked>\
                    </div>\
                </div>\
            </div>\
        </div>\
        <div class="col-md-2 form-group m-n p-sm pt-md">\
            <button type = "button" class = "btn btn-default btn-raised remove_btn" onclick="removeMemberRaw('+divcount+')" style="padding: 5px 10px;"> <i class = "fa fa-minus"></i></button> \
            <button type="button" class="btn btn-default btn-raised add_btn" onclick="addNewMemberRaw()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button> \
        </div>\
    </div>';

    $(".remove_btn:first").show();
    $(".add_btn:last").hide();
    $("#countmembers"+(divcount-1)).after(html);
    
    $('.yesno input[type="checkbox"]').bootstrapToggle({
        on: 'Yes',
        off: 'No',
        onstyle: 'primary',
        offstyle: 'danger'
    });
    $(".selectpicker").selectpicker("refresh");

    $("#priority"+divcount).val(parseInt($(".countmembers").length));
}
function removeMemberRaw(divid){

    if($('select[name="channelid[]"]').length!=1 && ACTION==1 && $('#routememberid'+divid).val()!=null){
        var removeroutememberid = $('#removeroutememberid').val();
        $('#removeroutememberid').val(removeroutememberid+','+$('#routememberid'+divid).val());
    }
    $("#countmembers"+divid).remove();

    $(".add_btn:last").show();
    if ($(".remove_btn:visible").length == 1) {
        $(".remove_btn:first").hide();
    }
}
function resetdata() {
    $("#route_div").removeClass("has-error is-focused");
    $("#province_div").removeClass("has-error is-focused");
    $("#city_div").removeClass("has-error is-focused");
    $("#totaltime_div").removeClass("has-error is-focused");
    $("#totalkm_div").removeClass("has-error is-focused");
    
    if(ACTION==0){
        $('#route,#totaltime,#totalkm').val("");
        $('#provinceid,#cityid').val("0");
        $('#cityid')
            .find('option')
            .remove()
            .end()
            .append('<option value="">Select City</option>')
            .val('0')
        ;
        $('#provinceid,#cityid').selectpicker('refresh');
        
        $(".countmembers:not(:first)").remove();
        var divid = parseInt($(".countmembers:first").attr("id").match(/\d+/));

        $('#channelid'+divid+',#memberid'+divid).val("0");
        $('#channel'+divid+'_div,#member'+divid+'_div').removeClass("has-error is-focused");
        $('#priority'+divid).val("1");
        $('#active'+divid).bootstrapToggle("on");
        getmember(divid);

        $('.add_btn:first').show();
        $('.remove_btn').hide();

        $(".selectpicker").selectpicker('refresh');
    }
    $('html, body').animate({scrollTop:0},'slow');  
}
function checkvalidation(addtype=0) {
   
    var route = $('#route').val().trim();
    var provinceid = $('#provinceid').val();
    var cityid = $('#cityid').val();

    var isvalidroute = isvalidprovinceid = isvalidcityid = isvalidchannelid = isvalidmemberid = isvalidpriority = isvaliduniquemember = 1;
   
    PNotify.removeAll();
    if(route=="") {
        $("#route_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter route name !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidroute = 0;
    }else if(route.length < 3) {
        $("#route_div").addClass("has-error is-focused");
        new PNotify({title: 'Route name require minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidroute = 0;
    }else {
        $("#route_div").removeClass("has-error is-focused");
    }
    if(provinceid==0) {
        $("#province_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select province !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidprovinceid = 0;
    } else {
        $("#province_div").removeClass("has-error is-focused");
    }   
    if(cityid==0) {
        $("#city_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select city !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidcityid = 0;
    } else {
        $("#city_div").removeClass("has-error is-focused");
    }   
    
    var c=1;
    $('.countmembers').each(function(){
        var id = $(this).attr('id').match(/\d+/);
       
        if($("#channelid"+id).val() == 0){
            $("#channel"+id+"_div").addClass("has-error is-focused");
            new PNotify({title: 'Please select '+(c)+' channel !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidchannelid = 0;
        }else {
            $("#channel"+id+"_div").removeClass("has-error is-focused");
        }
        if($("#memberid"+id).val() == 0){
            $("#member"+id+"_div").addClass("has-error is-focused");
            new PNotify({title: 'Please select '+(c)+' '+member_label+' !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidmemberid = 0;
        }else {
            $("#member"+id+"_div").removeClass("has-error is-focused");
        }
        if($("#priority"+id).val() == "" || $("#priority"+id).val() == "0"){
            $("#priority"+id+"_div").addClass("has-error is-focused");
            new PNotify({title: 'Please enter '+(c)+' priority !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidpriority = 0;
        }else {
            $("#priority"+id+"_div").removeClass("has-error is-focused");
        }
        
        c++;
    });

    var member = $('input[name="uniquemember[]"]');
    var values = [];
    for(j=0;j<member.length;j++) {
        var uniquemember = member[j];
        var id = uniquemember.id.match(/\d+/);
        
        if(uniquemember.value!="" && $("#memberid"+id[0]).val()!=0){
            if(values.indexOf(uniquemember.value)>-1) {
                $("#channel"+id[0]+"_div,#member"+id[0]+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(j+1)+' is different channel & '+member_label+' !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvaliduniquemember = 0;
            }
            else{ 
                values.push(uniquemember.value);
            }
        }
    } 
    if(isvalidroute == 1 && isvalidprovinceid == 1 && isvalidcityid == 1 && isvalidchannelid == 1 && isvalidmemberid == 1 && isvalidpriority == 1 && isvaliduniquemember == 1){
        var formData = new FormData($('#routeform')[0]);
        if(ACTION == 0){ // INSERT
            var baseurl = SITE_URL + 'route/add-route';
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
                    var obj = JSON.parse(response);
                    if(obj['error']==1){
                        new PNotify({title: 'Route successfully added.',styling: 'fontawesome',delay: '3000',type: 'success'});
                        if(addtype==1){
                            resetdata();
                        }else{
                            setTimeout(function() { window.location = SITE_URL + "route";}, 500);
                        }
                    }else if(obj['error']==2){
                        new PNotify({title: 'Route already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else{
                        new PNotify({title: 'Route not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
            var baseurl = SITE_URL + 'route/update-route';
            $.ajax({
                url: baseurl,
                type: 'POST',
                data: formData,
                beforeSend: function(){
                    $('.mask').show();
                    $('#loader').show();
                },
                success: function(response){
                    var obj = JSON.parse(response);
                    if(obj['error']==1){
                        new PNotify({title: 'Route successfully updated.',styling: 'fontawesome',delay: '3000',type: 'success'});
                    
                        setTimeout(function() { window.location = SITE_URL + "route";}, 500);
                    }else if(obj['error']==2){
                        new PNotify({title: 'Route already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else{
                        new PNotify({title: 'Route not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

  
  
  