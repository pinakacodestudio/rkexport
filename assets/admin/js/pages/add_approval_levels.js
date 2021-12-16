$(document).ready(function(){
    $('.yesno input[type="checkbox"]').bootstrapToggle({
      on: 'Yes',
      off: 'No',
      onstyle: 'primary',
      offstyle: 'danger'
    });
    $(".add_level_btn").hide();
    $(".add_level_btn:last").show();

    $('.sortablepanel').sortable({
        handle: ".panel-heading",
        cursor: "move",
        opacity: 0.5,
        stop : function(event, ui){
            regeneratelevel();
        }
    });
    regeneratelevel();
});

function addNewLevel(){

    var rowcount = parseInt($(".countlevel:last").attr("id").match(/(\d+)/g))+1;
    var level = parseInt($(".countlevel").length)+1;
    var datahtml = '<div class="panel panel-default countlevel approvallevelbox" id="countlevel'+rowcount+'" style="transform:none;">\
                        <div class="panel-heading collapse-approval-panel border-filter-heading">\
                            <h2 style="font-weight:600;">Approval <span id="spanlevel'+rowcount+'">'+level+'</span></h2>\
                            <input type="hidden" name="generatedlevel[]" id="generatedlevel'+rowcount+'" value="'+rowcount+'">\
                            <input type="hidden" name="sortablelevel[]" id="sortablelevel'+rowcount+'" value="0">\
                            <div class="pull-right">\
                                <button type="button" class="btn btn-default btn-raised remove_level_btn" onclick="removeLevel('+rowcount+')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
                                <button type="button" class="btn btn-default btn-raised add_level_btn" onclick="addNewLevel()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>\
                            </div>\
                        </div>\
                        <div class="panel-body no-padding">\
                            <div class="col-sm-6">\
                                <div class="form-group" id="designation'+rowcount+'_div">\
                                    <label for="designationid'+rowcount+'" class="col-sm-4 control-label">Select Designation <span class="mandatoryfield">*</span></label>\
                                    <div class="col-sm-8">\
                                        <select id="designationid'+rowcount+'" name="designationid'+rowcount+'" class="selectpicker form-control designationid" data-live-search="true" data-size="8">\
                                            <option value="0">Select Designation</option>\
                                            '+DESIGNATION_DATA+'\
                                        </select>\
                                    </div>\
                                </div>\
                            </div>\
                            <div class="col-sm-2">\
                                <div class="form-group">\
                                    <label for="isenable'+rowcount+'" class="col-sm-5 control-label">Enable</label>\
                                    <div class="col-sm-7" style="margin-top: 5px;">\
                                        <div class="yesno">\
                                            <input type="checkbox" name="isenable'+rowcount+'" value="1">\
                                        </div>\
                                    </div>\
                                </div>\
                            </div>\
                            <div class="col-sm-2">\
                                <div class="form-group">\
                                    <label for="sendemail'+rowcount+'" class="col-sm-5 control-label">Email</label>\
                                    <div class="col-sm-7" style="margin-top: 5px;">\
                                        <div class="yesno">\
                                            <input type="checkbox" name="sendemail'+rowcount+'" value="1">\
                                        </div>\
                                    </div>\
                                </div>\
                            </div>\
                        </div>\
                    </div>';
    
    $(".remove_level_btn:first").show();
    $(".add_level_btn:last").hide();
    $("#countlevel"+(rowcount-1)).after(datahtml);
    regeneratelevel();
    $('.yesno input[type="checkbox"]').bootstrapToggle({
        on: 'Yes',
        off: 'No',
        onstyle: 'primary',
        offstyle: 'danger'
    });

    $('.sortablepanel').sortable({
        handle: ".panel-heading",
        cursor: "move",
        opacity: 0.5,
        stop : function(event, ui){
            regeneratelevel();
        }
    });

    $("#designationid"+rowcount).selectpicker("refresh");
}
function removeLevel(rowid){

    if($('select.designationid').length!=1 && ACTION==1 && $('#approvallevelsmappingid'+rowid).val()!=null){
        var removeapprovallevelsmappingid = $('#removeapprovallevelsmappingid').val();
        $('#removeapprovallevelsmappingid').val(removeapprovallevelsmappingid+','+$('#approvallevelsmappingid'+rowid).val());
    }
    $("#countlevel"+rowid).remove();
    $(".add_level_btn:last").show();
    if ($(".remove_level_btn:visible").length == 1) {
        $(".remove_level_btn:first").hide();
    }
    regeneratelevel();
}
function regeneratelevel(type=0){
   
    var jsonprocessids = [];
    $('.countlevel').each(function(index){
        var level = $(this).attr('id').match(/\d+/);
        $("#spanlevel"+level).html((index+1));
        $("#sortablelevel"+level).val((index+1));
        // jsonprocessids.push($("#processid"+level).val());
    });
    $(".add_level_btn").hide();
    $(".add_level_btn:last").show();
    /* if(type==1){
        $('#firstprocessids').html(JSON.stringify(jsonprocessids));
    } */
}
function resetdata() {
    $("#module_div").removeClass("has-error is-focused");
    $("#netprice_div").removeClass("has-error is-focused");
    
    if(ACTION==0){
        $('#module').val("0");
        $('#netprice').val("");
        
        $(".countlevel:not(:first)").remove();
        var divid = parseInt($(".countlevel:first").attr("id").match(/\d+/));

        $('#designationid'+divid).val("0");
        $('#designation'+divid+"_div").removeClass("has-error is-focused");
        $('.yesno input[type="checkbox"]').bootstrapToggle("off");

        $('.add_level_btn:first').show();
        $('.remove_level_btn').hide();
        $(".selectpicker").selectpicker("refresh");
        $('#yes').prop("checked", true);
    }
    $('html, body').animate({scrollTop:0},'slow');  
}
function checkvalidation(addtype=0) {
   
    var module = $('#module').val();
    
    var isvalidmodule = isvaliddesignation = isvaliduniquedesignation = 1;
   
    PNotify.removeAll();
    if(module==0) {
        $("#module_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select module !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmodule = 0;
    }else {
        $("#module_div").removeClass("has-error is-focused");
    }
    var c=1;
    $('.countlevel').each(function(){
        var id = $(this).attr('id').match(/\d+/);
        
        if($("#designationid"+id).val() == 0){
            $("#designation"+id+"_div").addClass("has-error is-focused");
            new PNotify({title: 'Please select '+(c)+' designation !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvaliddesignation = 0;
        }else {
            $("#designation"+id+"_div").removeClass("has-error is-focused");
        }
        c++;
    });

    var levels = $('select.designationid');
    var values = [];
    for(j=0;j<levels.length;j++) {
        var uniquelevels = levels[j];
        var id = uniquelevels.id.match(/\d+/);
        
        if(uniquelevels.value!="0"){
            if(values.indexOf(uniquelevels.value)>-1) {
                $("#designation"+id[0]+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(j+1)+' is different designation !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvaliduniquedesignation = 0;
            }
            else{ 
                values.push(uniquelevels.value);
            }
        }
    } 
    if(isvalidmodule == 1 && isvaliddesignation == 1 && isvaliduniquedesignation == 1){
        var formData = new FormData($('#approvallevelsform')[0]);
        if(ACTION == 0){ // INSERT
            var baseurl = SITE_URL + 'approval-levels/approval-levels-add';
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
                    $("#module_div").removeClass("has-error is-focused");
                    if(response==1){
                        new PNotify({title: 'Approval levels successfully added.',styling: 'fontawesome',delay: '3000',type: 'success'});
                        if(addtype==1){
                            resetdata();
                        }else{
                            setTimeout(function() { window.location = SITE_URL + "approval-levels";}, 500);
                        }
                    }else if(response==2){
                        new PNotify({title: 'Approval levels already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        $("#module_div").addClass("has-error is-focused");
                    }else{
                        new PNotify({title: 'Approval levels not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
            var baseurl = SITE_URL + 'approval-levels/update-approval-levels';
            $.ajax({
                url: baseurl,
                type: 'POST',
                data: formData,
                beforeSend: function(){
                    $('.mask').show();
                    $('#loader').show();
                },
                success: function(response){
                    $("#module_div").removeClass("has-error is-focused");
                    if(response==1){
                        new PNotify({title: 'Approval levels successfully updated.',styling: 'fontawesome',delay: '3000',type: 'success'});
                    
                        setTimeout(function() { window.location = SITE_URL + "approval-levels";}, 500);
                    }else if(response==2){
                        new PNotify({title: 'Approval levels already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        $("#module_div").addClass("has-error is-focused");
                    }else{
                        new PNotify({title: 'Approval levels not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
