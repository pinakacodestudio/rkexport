$(document).ready(function(){
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
            regeneratesequenceno();
        }
    });

    $('.processsdetailsequence').each(function(){
        var seqno = $(this).attr('id').match(/\d+/);

        $(".add_outproduct_btn"+seqno).hide();
        $(".add_outproduct_btn"+seqno+":last").show();

        $(".add_inproduct_btn"+seqno).hide();
        $(".add_inproduct_btn"+seqno+":last").show();
    });

    if(ACTION==1 || ISDUPLICATE==1){
        //getVendorOrMachineDataByProcessIds(JSON.parse($('#firstprocessids').html()));
    }
});
/****OUT PRODUCT CHANGE EVENT****/
$(document).on('change', 'select.outproductid', function(){
    var elementid = $(this).attr("id").split('_');
    var sequenceno = elementid[0].match(/\d+/);
    var rowid = elementid[1];
    var productid = $(this).val();
    getproductvariant(sequenceno,rowid,1,productid);
});
/****IN PRODUCT CHANGE EVENT****/
$(document).on('change', 'select.inproductid', function(){
    var elementid = $(this).attr("id").split('_');
    var sequenceno = elementid[0].match(/\d+/);
    var rowid = elementid[1];
    var productid = $(this).val();
    getproductvariant(sequenceno,rowid,0,productid);
});
$(document).on('change', 'input[class="processoption"]', function(){
    var elementid = $(this).attr("id").split('_');
    var sequenceno = elementid[0].match(/\d+/);
    var rowid = elementid[1];
    if($(this).prop("checked") == false){
        $("#optionvalue"+sequenceno+"_"+rowid).val(0);
    }else{
        $("#optionvalue"+sequenceno+"_"+rowid).val(1);
    }
});
$(document).on('change', 'input[class="processedby"]', function(){
    var sequenceno = $(this).attr("id").match(/\d+/);
    if($("#inhouse"+sequenceno).prop("checked") == true){
        $("#vendor"+sequenceno+"_div").hide();
        $("#machine"+sequenceno+"_div").show();
    }else{
        $("#vendor"+sequenceno+"_div").show();
        $("#machine"+sequenceno+"_div").hide();
    }
});
$(function () {
    $(document).on('click','.panel-heading.collapse-process-panel', function() {
        $(this).find('.button-icon span').html($(this).find('.button-icon span').text() == 'keyboard_arrow_down' ? 'keyboard_arrow_up' : 'keyboard_arrow_down');
        //$(this).children().toggleClass(" ");
        $(this).next().slideToggle({duration: 200});
        $(this).toggleClass('panel-collapsed');
       
        return false;
    });
});
function generateprocess(){
    
    /******
     * when click on generate button then call this function.
     * Functionality
     * - check process validation.
     * - Create group HTML of number of process selected   
     */
    var processid = $("#processid").val();
    var processidarray = $("select[name='processid[]']").map(function(){return $(this).val();}).get();

    var isvalidprocessid = 0;
    PNotify.removeAll();
    
    if(processid==null){
        $("#process_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select process !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }else{
        $("#process_div").removeClass("has-error is-focused");
        isvalidprocessid = 1;
    }
    if(isvalidprocessid == 1){
        
        if(processidarray.length > 0){
            var JSONPROCESSIDSARRAY = $('#firstprocessids').html();
            if(JSONPROCESSIDSARRAY==""){
                $('#firstprocessids').html(JSON.stringify(processidarray));
            }
            
            var uiPROCESSIDS = JSON.parse($('#firstprocessids').html());
            var processHTML = ""; var jsonprocessids = [];
            var newseqno = 1;
            for (var i = 0; i < processidarray.length; i++) {
                var sequenceno = i+1;
                jsonprocessids.push(processidarray[i]);
                
                var PROCESS_OPTION_HTML = "";
                var prodessdata = JSON.parse(PROCESS_OPTION_DATA);
                
                var count = $(".clsgeneratedsequenceno").length;
                var countseqno = [];
                if(count > 0){
                    $(".clsgeneratedsequenceno").each(function(){
                        countseqno.push(parseInt($(this).val()));
                    });
                    sequenceno = Math.max.apply(Math,countseqno);
                }
                
                if(!uiPROCESSIDS.includes(processidarray[i]) || JSONPROCESSIDSARRAY==""){
                    sequenceno = (JSONPROCESSIDSARRAY!=""?(sequenceno + newseqno):sequenceno);
                    newseqno++;
                      
                    if(prodessdata.length > 0){
                        for (var p = 0; p < prodessdata.length; p++) {
                            if(prodessdata[p]['datatype'] == 0){
                                
                                var checked = (prodessdata[p]['optionvalue']!="" && prodessdata[p]['optionvalue']==1 ? "checked" : "");
                                var value = (prodessdata[p]['optionvalue']!="" && prodessdata[p]['optionvalue']==1 ? "1" : "0");
                                PROCESS_OPTION_HTML += '<div class="col-md-2">\
                                                <div class="form-group">\
                                                <label for="sms" class="col-sm-6 control-label">'+ucwords(prodessdata[p]['name'])+'</label>\
                                                <div class="col-sm-3">\
                                                    <div class="yesno">\
                                                    <input type="checkbox" class="processoption" id="processoption'+sequenceno+'_'+(p+1)+'" value="0" '+checked+'>\
                                                    </div>\
                                                    <input type="hidden" id="optionvalue'+sequenceno+'_'+(p+1)+'" name="optionvalue'+sequenceno+'[]" value="'+value+'">\
                                                    <input type="hidden" id="optionid'+sequenceno+'_'+(p+1)+'" name="optionid'+sequenceno+'[]" value="'+prodessdata[p]['id']+'">\
                                                </div>\
                                                </div>\
                                            </div>';
                            }else if(prodessdata[p]['datatype'] == 3){
                                
                                var name = (prodessdata[p]['name']=='certificate'?'mincertcountrequired'+sequenceno:prodessdata[p]['name']+sequenceno);
                                PROCESS_OPTION_HTML += '<div class="col-md-5">\
                                                            <div class="form-group">\
                                                            <label for="sms" class="col-sm-6 control-label">'+ucwords(prodessdata[p]['name'])+'</label>\
                                                            <div class="col-sm-5">\
                                                                <input type="text" id="optionvalue'+sequenceno+'" class="form-control" name="optionvalue'+sequenceno+'[]" value="'+prodessdata[p]['optionvalue']+'">\
                                                                <input type="hidden" name="optionid'+sequenceno+'[]" value="'+prodessdata[p]['id']+'">\
                                                            </div>\
                                                            </div>\
                                                        </div>';
                            }           
                        }
                    }
                    var OUTPRODUCTDETAILHTML = '<div class="row m-n">\
                                                    <div class="panel-heading"><h2>OUT Product Material Details</h2></div>\
                                                    <div class="row m-n">\
                                                        <div class="col-md-3">\
                                                            <div class="form-group p-n">\
                                                                <div class="col-sm-12">\
                                                                    <label class="control-label">Product</label>\
                                                                </div>\
                                                            </div>\
                                                        </div>\
                                                        <div class="col-md-3">\
                                                            <div class="form-group p-n">\
                                                                <div class="col-sm-12">\
                                                                    <label class="control-label">Variant</label>\
                                                                </div>\
                                                            </div>\
                                                        </div>\
                                                        <div class="col-md-2">\
                                                            <div class="form-group p-n">\
                                                                <div class="col-sm-12">\
                                                                    <label class="control-label">Unit</label>\
                                                                </div>\
                                                            </div>\
                                                        </div>\
                                                        <div class="col-md-1">\
                                                            <div class="form-group">\
                                                                <div class="col-sm-6">\
                                                                    <label class="control-label">Optional</label>\
                                                                </div>\
                                                            </div>\
                                                        </div>\
                                                    </div>\
                                                    <div class="countoutproducts'+sequenceno+' col-md-12 p-n" id="countoutproducts'+sequenceno+'_1">\
                                                        <div class="col-md-3">\
                                                            <div class="form-group p-n" id="outproduct'+sequenceno+'_1_div">\
                                                                <div class="col-sm-12">\
                                                                    <select id="outproductid'+sequenceno+'_1" name="outproductid'+sequenceno+'[]" class="selectpicker form-control outproductid" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                                                        <option value="0">Select Product</option>\
                                                                        '+PRODUCT_DATA+'\
                                                                    </select>\
                                                                </div>\
                                                            </div>\
                                                        </div>\
                                                        <div class="col-md-3">\
                                                            <div class="form-group p-n" id="outproductvariant'+sequenceno+'_1_div">\
                                                                <div class="col-sm-12">\
                                                                    <select id="outproductvariantid'+sequenceno+'_1" name="outproductvariantid'+sequenceno+'[]" class="selectpicker form-control outproductvariantid'+sequenceno+'" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                                                        <option value="0">Select Variant</option>\
                                                                    </select>\
                                                                </div>\
                                                            </div>\
                                                        </div>\
                                                        <div class="col-md-2">\
                                                            <div class="form-group p-n" id="unit'+sequenceno+'_1_div">\
                                                                <div class="col-sm-12">\
                                                                    <select id="unitid'+sequenceno+'_1" name="unitid'+sequenceno+'[]" class="selectpicker form-control unitid" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                                                        <option value="0">Select Unit</option>\
                                                                        '+UNIT_DATA+'\
                                                                    </select>\
                                                                </div>\
                                                            </div>\
                                                        </div>\
                                                        <div class="col-md-1">\
                                                            <div class="form-group">\
                                                                <div class="col-sm-6">\
                                                                    <div class="yesno">\
                                                                    <input type="checkbox" name="outproductisoptional'+sequenceno+'_1" value="0">\
                                                                    </div>\
                                                                </div>\
                                                            </div>\
                                                        </div>\
                                                        <div class="col-md-1 text-right pt-md pl-xs">\
                                                            <button type="button" class="btn btn-default btn-raised remove_outproduct_btn'+sequenceno+'" onclick="removeoutproduct('+sequenceno+',1)" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>\
                                                            <button type="button" class="btn btn-default btn-raised add_outproduct_btn'+sequenceno+'" onclick="addnewoutproduct('+sequenceno+')" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>\
                                                        </div>\
                                                    </div>\
                                                </div>';


                var INPRODUCTDETAILHTML = '<div class="row m-n">\
                                            <div class="col-md-12"><hr></div>\
                                            <div class="panel-heading"><h2>IN Details</h2><div class="col-md-6 pull-right text-right">Note : Certificate is -1 unlimited, 0 - Not require and Otherwise limited.</div></div>\
                                            '+PROCESS_OPTION_HTML+'\
                                            \
                                            <div class="col-md-12"><hr></div>\
                                            <div class="col-md-12">\
                                                <div class="col-md-6 p-n" id="inproductlabel1_'+sequenceno+'">\
                                                    <div class="col-md-5 pr-xs pl-xs">\
                                                        <div class="form-group p-n">\
                                                            <div class="col-sm-12">\
                                                                <label class="control-label">Product</label>\
                                                            </div>\
                                                        </div>\
                                                    </div>\
                                                    <div class="col-md-5 pr-xs pl-xs">\
                                                        <div class="form-group p-n">\
                                                            <div class="col-sm-12">\
                                                                <label class="control-label">Product Variant</label>\
                                                            </div>\
                                                        </div>\
                                                    </div>\
                                                </div>\
                                                <div class="col-md-6 p-n" id="inproductlabel2_'+sequenceno+'" style="display:none;">\
                                                    <div class="col-md-5 pr-xs pl-xs">\
                                                        <div class="form-group p-n">\
                                                            <div class="col-sm-12">\
                                                                <label class="control-label">Product</label>\
                                                            </div>\
                                                        </div>\
                                                    </div>\
                                                    <div class="col-md-5 pr-xs pl-xs">\
                                                        <div class="form-group p-n">\
                                                            <div class="col-sm-12">\
                                                                <label class="control-label">Product Variant</label>\
                                                            </div>\
                                                        </div>\
                                                    </div>\
                                                </div>\
                                            </div>\
                                            \
                                            <div class="col-md-12">\
                                                <div class="countinproducts'+sequenceno+' col-md-6 p-n" id="countinproducts'+sequenceno+'_1">\
                                                    <div class="col-md-5 pr-xs pl-xs">\
                                                        <div class="form-group p-n" id="inproduct'+sequenceno+'_1_div">\
                                                            <div class="col-sm-12">\
                                                                <select id="inproductid'+sequenceno+'_1" name="inproductid'+sequenceno+'[]" class="selectpicker form-control inproductid" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                                                    <option value="0">Select Product</option>\
                                                                    '+PRODUCT_DATA+'\
                                                                </select>\
                                                            </div>\
                                                        </div>\
                                                    </div>\
                                                    <div class="col-md-5 pr-xs pl-xs">\
                                                        <div class="form-group p-n" id="inproductvariant'+sequenceno+'_1_div">\
                                                            <div class="col-sm-12">\
                                                                <select id="inproductvariantid'+sequenceno+'_1" name="inproductvariantid'+sequenceno+'[]" class="selectpicker form-control inproductvariantid'+sequenceno+'" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                                                    <option value="0">Select Variant</option>\
                                                                </select>\
                                                            </div>\
                                                        </div>\
                                                    </div>\
                                                    <div class="col-md-2 text-right pt-md pl-xs">\
                                                        <button type="button" class="btn btn-default btn-raised remove_inproduct_btn'+sequenceno+'" onclick="removeinproduct('+sequenceno+',1)" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>\
                                                        <button type="button" class="btn btn-default btn-raised add_inproduct_btn'+sequenceno+'" onclick="addnewinproduct('+sequenceno+')" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>\
                                                    </div>\
                                                </div>\
                                            </div>\
                                        </div>';


                    processHTML += '<div class="panel panel-default border-panel processsdetailsequence" id="processsdetailsequence'+sequenceno+'" style="transform:none;">\
                                        <div class="panel-heading collapse-process-panel border-filter-heading">\
                                            <div class="col-md-8 pl-n">\
                                                <h2>Process Details - Sequence No - <span id="spanseqno'+sequenceno+'">0</span></h2>\
                                                <input type="hidden" class="clsgeneratedsequenceno" name="generatedsequenceno[]" value="'+sequenceno+'">\
                                                <input type="hidden" name="sortablesequenceno[]" id="sortablesequenceno'+sequenceno+'" value="0">\
                                                <input type="hidden" name="postprocessid[]" class="processidselection" id="processidselection'+sequenceno+'" value="'+processidarray[i]+'">\
                                            </div>\
                                            <div class="col-md-4 text-right pr-n">\
                                                <button type="button" class="btn btn-danger btn-raised mr-md" onclick="removeprocess('+sequenceno+')"><i class="fa fa-times"></i> Remove</button>\
                                                <div class="panel-ctrls" data-actions-container data-action-collapse="{&quot;target&quot;: &quot;.panelcollapse&quot;}" style="float:right;padding-top: 8px;"><span class="button-icon has-bg"><span class="material-icons">keyboard_arrow_down</span></span></div>\
                                            </div>\
                                        </div>\
                                        <div class="panel-body p-n pb-md">\
                                            <div class="row m-n">\
                                                <div class="col-sm-5 pr-sm pl-sm">\
                                                    <div class="form-group" id="process'+sequenceno+'_div">\
                                                        <label for="processid'+sequenceno+'" class="col-sm-4 control-label">Select Process </label>\
                                                        <div class="col-sm-8">\
                                                            <select id="processid'+sequenceno+'" name="processid'+sequenceno+'" class="selectpicker form-control" title="Select Process" data-select-on-tab="true" data-size="5" disabled>\
                                                                <option value="0">Select Process</option>\
                                                                '+PROCESS_DATA+'\
                                                            </select>\
                                                        </div>\
                                                    </div>\
                                                </div>\
                                                <div class="col-md-4 pr-sm pl-sm">\
                                                    <div class="form-group" id="priority'+sequenceno+'_div">\
                                                        <label class="col-md-4 control-label pr-n" for="priority'+sequenceno+'">Priority <span class="mandatoryfield">*</span></label>\
                                                        <div class="col-md-8">\
                                                            <input type="text" id="priority'+sequenceno+'" class="form-control" name="priority[]" value="'+sequenceno+'">\
                                                        </div>\
                                                    </div>\
                                                </div>\
                                                <div class="col-md-3 pr-sm pl-sm">\
                                                    <div class="form-group">\
                                                        <label for="sms" class="col-sm-6 control-label">Is Optional</label>\
                                                        <div class="col-sm-6">\
                                                            <div class="yesno">\
                                                            <input type="checkbox" name="processisoptional'+sequenceno+'" value="0">\
                                                            </div>\
                                                        </div>\
                                                    </div>\
                                                </div>\
                                            </div>\
                                            <div class="row m-n">\
                                                <div class="col-md-5 pr-sm pl-sm">\
                                                    <div class="form-group">\
                                                        <label for="focusedinput" class="col-md-4 control-label">Processed By</label>\
                                                        <div class="col-md-8 mt-xs">\
                                                            <div class="col-md-7 col-xs-4 pr-n">\
                                                                <div class="radio">\
                                                                    <input class="processedby" type="radio" name="processedby'+sequenceno+'" id="inhouse'+sequenceno+'" value="1" checked>\
                                                                    <label for="inhouse'+sequenceno+'">In-House Emp</label>\
                                                                </div>\
                                                            </div>\
                                                            <div class="col-md-5 col-xs-4 p-n">\
                                                                <div class="radio">\
                                                                    <input class="processedby" type="radio" name="processedby'+sequenceno+'" id="otherparty'+sequenceno+'" value="0">\
                                                                    <label for="otherparty'+sequenceno+'">Other Party</label>\
                                                                </div>\
                                                            </div>\
                                                        </div>\
                                                    </div>\
                                                </div>\
                                                <div class="col-md-4 pr-sm pl-sm">\
                                                    <div class="form-group" id="vendor'+sequenceno+'_div" style="display:none;">\
                                                        <label class="col-md-4 control-label pr-n" for="vendorid'+sequenceno+'">Vendor</label>\
                                                        <div class="col-md-8">\
                                                            <select id="vendorid'+sequenceno+'" name="vendorid['+sequenceno+'][]" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8" data-actions-box="true" title="Select Vendor" multiple>\
                                                            </select>\
                                                        </div>\
                                                    </div>\
                                                    <div class="form-group" id="machine'+sequenceno+'_div">\
                                                        <label class="col-md-4 control-label pr-n" for="machineid'+sequenceno+'">Machine</label>\
                                                        <div class="col-md-8">\
                                                            <select id="machineid'+sequenceno+'" name="machineid['+sequenceno+'][]" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8" data-actions-box="true" title="Select Machine" multiple>\
                                                            </select>\
                                                        </div>\
                                                    </div>\
                                                </div>\
                                                <div class="col-md-3 pr-sm pl-sm">\
                                                    <div class="form-group">\
                                                        <label for="sms" class="col-sm-6 control-label">QC Require</label>\
                                                        <div class="col-sm-6">\
                                                            <div class="yesno">\
                                                            <input type="checkbox" name="processqcrequire'+sequenceno+'" value="0">\
                                                            </div>\
                                                        </div>\
                                                    </div>\
                                                </div>\
                                            </div>\
                                            <div class="col-md-12"><hr></div>\
                                            '+OUTPRODUCTDETAILHTML+' \
                                            '+INPRODUCTDETAILHTML+'\
                                        </div>\
                                    </div>';
                }
            }
            
            var difference = uiPROCESSIDS.filter(x => jsonprocessids.indexOf(x) === -1);
            if(difference.length > 0){
                for (var i = 0; i < difference.length; i++) {
                    $('.processsdetailsequence').each(function(){
                        var seqno = $(this).attr('id').match(/\d+/);
                       
                        if($("#processid"+seqno).val() == difference[i]){
                            if(ACTION==1 && $('#processgroupmappingid'+seqno).val()!=null){
                                var removeprocessgroupmappingid = $('#removeprocessgroupmappingid').val();
                                $('#removeprocessgroupmappingid').val(removeprocessgroupmappingid+','+$('#processgroupmappingid'+seqno).val());
                            }
                            $('#processsdetailsequence'+seqno).remove();
                        }
                    });
                }
            }
            
            if(JSONPROCESSIDSARRAY==""){
                $("#processgroup_maindiv").html(processHTML);
                
                /* $('.processsdetailsequence').each(function(index){
                    var seqno = $(this).attr('id').match(/\d+/);
                    $('#processid'+seqno).val(jsonprocessids[seqno-1]);
                }); */
            }else{
                $("#processgroup_maindiv").append(processHTML);
            }
            regeneratesequenceno();
            $('.selectpicker').selectpicker('refresh');
            $('.yesno input[type="checkbox"]').bootstrapToggle({
                on: 'Yes',
                off: 'No',
                onstyle: 'primary',
                offstyle: 'danger'
            });
            $(".processidselection").each(function(){
                var seqno = $(this).attr('id').match(/\d+/);
                // alert($('#processid'+seqno).val());
                if($('#processid'+seqno).val() == 0){
                    $('#processid'+seqno).val($(this).val());
                    $('#processid'+seqno).selectpicker('refresh');
                    // getmachine(seqno,$(this).val());
                    // getvendor(seqno,$(this).val());
                }
            });
            getVendorOrMachineDataByProcessIds(jsonprocessids);
            $('.sortablepanel').sortable({
                handle: ".panel-heading",
                cursor: "move",
                opacity: 0.5,
                stop : function(event, ui){
                    regeneratesequenceno();
                }
            });
            if(JSONPROCESSIDSARRAY!=""){
                $('#firstprocessids').html(JSON.stringify(jsonprocessids));
            }
        }
    }else{
        $("#processgroup_maindiv,#firstprocessids").html('');
    }
}
function regeneratesequenceno(type=0){
   
    var jsonprocessids = [];
    $('.processsdetailsequence').each(function(index){
        var seqno = $(this).attr('id').match(/\d+/);
        $("#spanseqno"+seqno).html((index+1));
        $("#sortablesequenceno"+seqno).val((index+1));
        $("#priority"+seqno).val((index+1));
        jsonprocessids.push($("#processid"+seqno).val());
    });
    if(type==1){
        $('#firstprocessids').html(JSON.stringify(jsonprocessids));
    }
}
function removeprocess(sequenceno){
    if(ACTION==1 && $('#processgroupmappingid'+sequenceno).val()!=null){
        var removeprocessgroupmappingid = $('#removeprocessgroupmappingid').val();
        $('#removeprocessgroupmappingid').val(removeprocessgroupmappingid+','+$('#processgroupmappingid'+sequenceno).val());
    }
    $('#processsdetailsequence'+sequenceno).remove();
    regeneratesequenceno(1);
}
function addnewoutproduct(sequenceno){
    
    // var rowcount = parseInt($(".countinproducts"+sequenceno+":last").attr("id").match(/\d+/))+1;
    var elementid = $(".countoutproducts"+sequenceno+":last").attr("id").split('_');
    var rowcount = parseInt(elementid[1])+1;

    var datahtml = '<div class="countoutproducts'+sequenceno+' col-md-12 p-n" id="countoutproducts'+sequenceno+'_'+rowcount+'">\
                        <div class="col-md-3">\
                            <div class="form-group p-n" id="outproduct'+sequenceno+'_'+rowcount+'_div">\
                            <div class="col-sm-12">\
                                <select id="outproductid'+sequenceno+'_'+rowcount+'" name="outproductid'+sequenceno+'[]" class="selectpicker form-control outproductid" data-live-search="true" data-select-on-tab="true" data-size="8">\
                                    <option value="0">Select Product</option>\
                                    '+PRODUCT_DATA+'\
                                </select>\
                            </div>\
                            </div>\
                        </div>\
                        <div class="col-md-3">\
                            <div class="form-group p-n" id="outproductvariant'+sequenceno+'_'+rowcount+'_div">\
                                <div class="col-sm-12">\
                                    <select id="outproductvariantid'+sequenceno+'_'+rowcount+'" name="outproductvariantid'+sequenceno+'[]" class="selectpicker form-control outproductvariantid" data-live-search="true" data-select-on-tab="true" data-size="8">\
                                        <option value="0">Select Variant</option>\
                                    </select>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-2">\
                            <div class="form-group p-n" id="unit'+sequenceno+'_'+rowcount+'_div">\
                            <div class="col-sm-12">\
                                <select id="unitid'+sequenceno+'_'+rowcount+'" name="unitid'+sequenceno+'[]" class="selectpicker form-control unitid" data-live-search="true" data-select-on-tab="true" data-size="8">\
                                    <option value="0">Select Unit</option>\
                                    '+UNIT_DATA+'\
                                </select>\
                            </div>\
                            </div>\
                        </div>\
                        <div class="col-md-1">\
                            <div class="form-group">\
                            <div class="col-sm-6">\
                                <div class="yesno">\
                                <input type="checkbox" name="outproductisoptional'+rowcount+'" value="0">\
                                </div>\
                            </div>\
                            </div>\
                        </div>\
                        <div class="col-md-1 text-right pt-md pl-xs">\
                            <button type="button" class="btn btn-default btn-raised remove_outproduct_btn'+sequenceno+'" onclick="removeoutproduct('+sequenceno+','+rowcount+')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
                            <button type="button" class="btn btn-default btn-raised add_outproduct_btn'+sequenceno+'" onclick="addnewoutproduct('+sequenceno+')" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>\
                        </div>\
                    </div>';
    
    $(".remove_outproduct_btn"+sequenceno+":first").show();
    $(".add_outproduct_btn"+sequenceno+":last").hide();
    $("#countoutproducts"+sequenceno+"_"+(rowcount-1)).after(datahtml);
    
    $("#outproductid"+sequenceno+"_"+rowcount+",#outproductvariantid"+sequenceno+"_"+rowcount+",#unitid"+sequenceno+"_"+rowcount).selectpicker("refresh");
    $('.yesno input[type="checkbox"]').bootstrapToggle({
        on: 'Yes',
        off: 'No',
        onstyle: 'primary',
        offstyle: 'danger'
    });
}
function removeoutproduct(sequenceno,rowid){

    if($('select[name="outproductid'+sequenceno+'[]"]').length!=1 && ACTION==1 && $('#processgroupoutproductid'+sequenceno+'_'+rowid).val()!=null){
        var removeprocessgroupproductid = $('#removeprocessgroupproductid').val();
        $('#removeprocessgroupproductid').val(removeprocessgroupproductid+','+$('#processgroupoutproductid'+sequenceno+'_'+rowid).val());
    }
    $("#countoutproducts"+sequenceno+"_"+rowid).remove();

    $(".add_outproduct_btn"+sequenceno+":last").show();
    if ($(".remove_outproduct_btn"+sequenceno+":visible").length == 1) {
        $(".remove_outproduct_btn"+sequenceno+":first").hide();
    }
}
function addnewinproduct(sequenceno){
    
    // var rowcount = parseInt($(".countinproducts"+sequenceno+":last").attr("id").match(/\d+/))+1;
    var elementid = $(".countinproducts"+sequenceno+":last").attr("id").split('_');
    var rowcount = parseInt(elementid[1])+1;

    var datahtml = '<div class="countinproducts'+sequenceno+' col-md-6 p-n" id="countinproducts'+sequenceno+'_'+rowcount+'">\
                        <div class="col-md-5 pr-xs pl-xs">\
                            <div class="form-group p-n" id="inproduct'+sequenceno+'_'+rowcount+'_div">\
                                <div class="col-sm-12">\
                                    <select id="inproductid'+sequenceno+'_'+rowcount+'" name="inproductid'+sequenceno+'[]" class="selectpicker form-control inproductid" data-live-search="true" data-select-on-tab="true" data-size="8">\
                                        <option value="0">Select Product</option>\
                                        '+PRODUCT_DATA+'\
                                    </select>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-5 pr-xs pl-xs">\
                            <div class="form-group p-n" id="inproductvariant'+sequenceno+'_'+rowcount+'_div">\
                            <div class="col-sm-12">\
                                <select id="inproductvariantid'+sequenceno+'_'+rowcount+'" name="inproductvariantid'+sequenceno+'[]" class="selectpicker form-control inproductvariantid" data-live-search="true" data-select-on-tab="true" data-size="8">\
                                    <option value="0">Select Variant</option>\
                                </select>\
                            </div>\
                            </div>\
                        </div>\
                        <div class="col-md-2 text-right pt-md pl-xs">\
                            <button type="button" class="btn btn-default btn-raised remove_inproduct_btn'+sequenceno+'" onclick="removeinproduct('+sequenceno+','+rowcount+')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
                            <button type="button" class="btn btn-default btn-raised add_inproduct_btn'+sequenceno+'" onclick="addnewinproduct('+sequenceno+')" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>\
                        </div>\
                    </div>';
    
    $(".remove_inproduct_btn"+sequenceno+":first").show();
    $(".add_inproduct_btn"+sequenceno+":last").hide();
    $("#countinproducts"+sequenceno+"_"+(rowcount-1)).after(datahtml);
    
    $("#inproductid"+sequenceno+"_"+rowcount+",#inproductvariantid"+sequenceno+"_"+rowcount).selectpicker("refresh");
    $('.yesno input[type="checkbox"]').bootstrapToggle({
        on: 'Yes',
        off: 'No',
        onstyle: 'primary',
        offstyle: 'danger'
    });

    if($(".countinproducts"+sequenceno).length == 1){
        $("#inproductlabel2_"+sequenceno).hide();
    }else{
        $("#inproductlabel2_"+sequenceno).show();
    }  
}
function removeinproduct(sequenceno,rowid){

    if($('select[name="inproductid'+sequenceno+'[]"]').length!=1 && ACTION==1 && $('#processgroupinproductid'+sequenceno+'_'+rowid).val()!=null){
        var removeprocessgroupproductid = $('#removeprocessgroupproductid').val();
        $('#removeprocessgroupproductid').val(removeprocessgroupproductid+','+$('#processgroupinproductid'+sequenceno+'_'+rowid).val());
    }
    $("#countinproducts"+sequenceno+"_"+rowid).remove();

    $(".add_inproduct_btn"+sequenceno+":last").show();
    if ($(".remove_inproduct_btn"+sequenceno+":visible").length == 1) {
        $(".remove_inproduct_btn"+sequenceno+":first").hide();
    }
    if($(".countinproducts"+sequenceno).length == 1){
        $("#inproductlabel2_"+sequenceno).hide();
    }else{
        $("#inproductlabel2_"+sequenceno).show();
    }  
}
function getproductvariant(sequenceno,rowid,processtype,productid,productvariantid=""){
   
    
    if(processtype == 1){
        var productvariant = JSON.parse($('#outproductid'+sequenceno+'_'+rowid+' option:selected').attr("data-variants"));
        var productvariantelement = $('#outproductvariantid'+sequenceno+'_'+rowid);
    }else{
        var productvariant = JSON.parse($('#inproductid'+sequenceno+'_'+rowid+' option:selected').attr("data-variants"));
        var productvariantelement = $('#inproductvariantid'+sequenceno+'_'+rowid);
    }
    productvariantelement.find('option')
                    .remove()
                    .end()
                    .append('<option value="">Select Variant</option>')
                    .val('0')
                ;
    productvariantelement.selectpicker('refresh');
    
    if(productid != '0'){
        for(var i = 0; i < productvariant.length; i++) {
        
            productvariantelement.append($('<option>', { 
                value: productvariant[i]['id'],
                text : productvariant[i]['variantname'],
            }));
        }
        if(productvariant.length == 1){
            productvariantelement.val(productvariant[0]['id']);
        }
        if((ACTION==1 || ISDUPLICATE==1) && productvariantid != ""){
            productvariantelement.val(productvariantid);
        }
    }
    /*if(productid != '0'){
      var uurl = SITE_URL+"process-group/getProductVariantByProductId";
      
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {productid:String(productid),type:2},
        dataType: 'json',
        async: false,
        success: function(response){
  
            for(var i = 0; i < response.length; i++) {
                
                productvariantelement.append($('<option>', { 
                    value: response[i]['id'],
                    text : response[i]['variantname']
                }));
            }
            if(response.length == 1){
                productvariantelement.val(response[0]['id']);
            }
            if((ACTION==1 || ISDUPLICATE==1) && productvariantid != ""){
                productvariantelement.val(productvariantid);
            }
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
      });
    }*/
    productvariantelement.selectpicker('refresh');
}
function getVendorOrMachineDataByProcessIds(jsonprocessids){
    
    if(jsonprocessids != ""){

      var uurl = SITE_URL+"manufacturing-process/getVendorOrMachineDataByProcessIds";
      
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {processid:String(jsonprocessids)},
        dataType: 'json',
        async: false,
        success: function(response){

            var data = response['data'];
          
            $.each( data, function( key, value ) {
                $('.processsdetailsequence').each(function(index){
                    var sequenceno = $(this).attr('id').match(/\d+/);
                    if($('#processid'+sequenceno).val()!=0 && $('#processid'+sequenceno).val()==key){

                        var machinedata = data[key]['machinedata'];
                        var vendordata = data[key]['vendordata'];
                            
                        if($("#vendorid"+sequenceno).val()==null || $("#vendorid"+sequenceno).val()==""){
                            var vendorid = [];
                            var prevendorid = $("#prevendorid"+sequenceno).val();
                            if(prevendorid!=0 && prevendorid!=undefined){
                                if (prevendorid.indexOf(',') > -1) { 
                                    vendorid = prevendorid.split(',');
                                }else{
                                    vendorid.push(prevendorid);
                                }
                            }
                            
                            $("#vendorid"+sequenceno).find('option')
                                            .remove()
                                            .end()
                                            .append('')
                                            .val('whatever')
                                        ;

                            if(vendordata.length > 0){
                                for(var i = 0; i < vendordata.length; i++) {
                                    if(vendorid.includes(vendordata[i]['id'])){
                                        $("#vendorid"+sequenceno).append($('<option>', { 
                                            value: vendordata[i]['id'],
                                            text : vendordata[i]['name'],
                                            "selected" : "selected"
                                        }));
                                    }else{
                                        $("#vendorid"+sequenceno).append($('<option>', { 
                                            value: vendordata[i]['id'],
                                            text : vendordata[i]['name']
                                        }));
                                    }
                                }
                            }
                            $("#vendorid"+sequenceno).selectpicker('refresh');
                        }
                        if($("#machineid"+sequenceno).val()==null || $("#machineid"+sequenceno).val()==""){
                            var machineid = [];
                            var premachineid = $("#premachineid"+sequenceno).val();
                            if(premachineid!=0 && premachineid!=undefined){
                                if (premachineid.indexOf(',') > -1) { 
                                    machineid = premachineid.split(',');
                                }else{
                                    machineid.push(premachineid);
                                }
                            }
                            $("#machineid"+sequenceno).find('option')
                                            .remove()
                                            .end()
                                            .append('')
                                            .val('whatever')
                                        ;

                            if(machinedata.length > 0){
                                for(var i = 0; i < machinedata.length; i++) {
                                    if(machineid.includes(machinedata[i]['id'])){
                                        $("#machineid"+sequenceno).append($('<option>', { 
                                            value: machinedata[i]['id'],
                                            text : machinedata[i]['name'],
                                            "selected" : "selected"
                                        }));
                                    }else{
                                        $("#machineid"+sequenceno).append($('<option>', { 
                                            value: machinedata[i]['id'],
                                            text : machinedata[i]['name']
                                        }));
                                    }
                                }
                            }
                            $("#machineid"+sequenceno).selectpicker('refresh');
                        }
                    }
                });
            });
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
      });
    }
}
function getvendor(sequenceno,processid){
   
    $("#vendorid"+sequenceno).find('option')
                    .remove()
                    .end()
                    .append('')
                    .val('whatever')
                ;
    $("#vendorid"+sequenceno).selectpicker('refresh');
    
    if(processid != '0'){

      var uurl = SITE_URL+"manufacturing-process/getVendorByProcessId";
      
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {processid:String(processid)},
        dataType: 'json',
        async: false,
        success: function(response){
  
            for(var i = 0; i < response.length; i++) {
                $("#vendorid"+sequenceno).append($('<option>', { 
                    value: response[i]['id'],
                    text : response[i]['name']
                }));
            }
            /* if(ACTION==1 && productvariantid != ""){
                $("#machineid"+sequenceno).val(productvariantid);
            } */
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
      });
    }
    $("#vendorid"+sequenceno).selectpicker('refresh');
}
function resetdata() {
    $("#groupname_div").removeClass("has-error is-focused");
    $("#description_div").removeClass("has-error is-focused");
    $("#process_div").removeClass("has-error is-focused");

    if(ACTION==0){
        $('#groupname,#description').val("");
        $('#processid').val("0");
        $('#processgroup_maindiv,#firstprocessids,#generatedsequence').html("");
        
        $('.selectpicker').selectpicker('refresh');
        $('#yes').prop("checked", true);
        $('#groupname').focus();
    }
    $('html, body').animate({scrollTop:0},'slow');  
}
function checkvalidation(addtype=0) {
   
    var groupname = $('#groupname').val().trim();
    var description = $('#description').val().trim();
    var processid =  $('#processid').val();
    var countprocess = $('.processsdetailsequence').length;

    var isvalidgroupname = isvaliddescription = isvalidprocessid = 0;
    var isvalidprocesspriority = isvalidmachineid = isvalidoutproductid = isvalidoutvariantid = isvalidunitid = isvalidinproductid = isvalidinvariantid = isvaliduniqueoutproducts = isvaliduniqueinproducts = isvaliduniquepriority = 1;

    PNotify.removeAll();
    if(groupname=="") {
        $("#groupname_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter process group name !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else if(groupname.length < 2){
        $("#groupname_div").addClass("has-error is-focused");
        new PNotify({title: 'Process group name required minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
        $("#groupname_div").removeClass("has-error is-focused");
        isvalidgroupname = 1;
    }
    if(description!="" && description.length < 3) {
        $("#description_div").addClass("has-error is-focused");
        new PNotify({title: 'Description required minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
        $("#description_div").removeClass("has-error is-focused");
        isvaliddescription = 1;
    }
    if(processid == null) {
        $("#process_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select process !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
        $("#process_div").removeClass("has-error is-focused");
        isvalidprocessid = 1;
    }
    if(countprocess == 0 && isvalidprocessid == 1){
        new PNotify({title: 'Please generate process group !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidprocessid = 0;
    }
    if(countprocess > 0){
        
        $('.processsdetailsequence').each(function(){
            var seqno = $(this).attr('id').match(/\d+/);
            var sortseqno = $("#processsdetailsequence"+seqno+" input[type=hidden]:nth-child(3)").val();
            if($("#priority"+seqno).val() == "" || $("#priority"+seqno).val() == "0"){
                $("#priority"+seqno+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please enter '+(sortseqno)+' process priority !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidprocesspriority = 0;
            }else {
                $("#priority"+seqno+"_div").removeClass("has-error is-focused");
            }
            if($("input[name='processedby"+seqno+"']:checked").val() == 1 && $("#machineid"+seqno+" option").length > 0 && $("#machineid"+seqno).val() == null){
                $("#machine"+seqno+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(sortseqno)+' machine !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidmachineid = 0;
            }else {
                $("#machine"+seqno+"_div").removeClass("has-error is-focused");
            }
            
            var c=1;
            var firstoutproductid = $('.countoutproducts'+seqno+':first').attr('id').split('_');
            firstoutproductid = firstoutproductid[1];
            $('.countoutproducts'+seqno).each(function(){
                var elementid = $(this).attr("id").split('_');
                var rowid = elementid[1];
                var elID = seqno+"_"+rowid;

                if($("#outproductid"+elID).val() > 0 || $("#outproductvariantid"+elID).val() > 0 || $("#unitid"+elID).val() > 0){
                    if($("#outproductid"+elID).val() == 0){
                        $("#outproduct"+elID+"_div").addClass("has-error is-focused");
                        new PNotify({title: 'Please select '+(sortseqno)+' sequence '+(c)+' OUT product !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        isvalidoutproductid = 0;
                    }else {
                        $("#outproduct"+elID+"_div").removeClass("has-error is-focused");
                    }
                    if($("#outproductvariantid"+elID).val() == 0){
                        $("#outproductvariant"+elID+"_div").addClass("has-error is-focused");
                        new PNotify({title: 'Please select '+(sortseqno)+' sequence '+(c)+' OUT product variant !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        isvalidoutvariantid = 0;
                    }else {
                        $("#outproductvariant"+elID+"_div").removeClass("has-error is-focused");
                    }
                    if($("#unitid"+elID).val() == 0){
                        $("#unit"+elID+"_div").addClass("has-error is-focused");
                        new PNotify({title: 'Please select '+(sortseqno)+' sequence '+(c)+' OUT product unit !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        isvalidunitid = 0;
                    }else {
                        $("#unit"+elID+"_div").removeClass("has-error is-focused");
                    }
                } else{
                    $("#outproduct"+elID+"_div").removeClass("has-error is-focused");
                    $("#outproductvariant"+elID+"_div").removeClass("has-error is-focused");
                    $("#unit"+elID+"_div").removeClass("has-error is-focused");
                }
                c++;
            });

            var i=1;
            var firstinproductid = $('.countinproducts'+seqno+':first').attr('id').split('_');
            firstinproductid = firstinproductid[1];
            $('.countinproducts'+seqno).each(function(){
                var elementid = $(this).attr("id").split('_');
                var rowid = elementid[1];
                var elID = seqno+"_"+rowid;

                if($("#inproductid"+elID).val() > 0 || $("#inproductvariantid"+elID).val() > 0){
                    if($("#inproductid"+elID).val() == 0){
                        $("#inproduct"+elID+"_div").addClass("has-error is-focused");
                        new PNotify({title: 'Please select '+(sortseqno)+' sequence '+(i)+' IN product !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        isvalidinproductid = 0;
                    }else {
                        $("#inproduct"+elID+"_div").removeClass("has-error is-focused");
                    }
                    if($("#inproductvariantid"+elID).val() == 0){
                        $("#inproductvariant"+elID+"_div").addClass("has-error is-focused");
                        new PNotify({title: 'Please select '+(sortseqno)+' sequence '+(i)+' IN product variant !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        isvalidinvariantid = 0;
                    }else {
                        $("#inproductvariant"+elID+"_div").removeClass("has-error is-focused");
                    }
                } else{
                    $("#inproduct"+elID+"_div").removeClass("has-error is-focused");
                    $("#inproductvariant"+elID+"_div").removeClass("has-error is-focused");
                }
                i++;
            });

            var outproducts = $('select[name="outproductvariantid'+seqno+'[]"]');
            var outvalues = [];
            for(j=0;j<outproducts.length;j++) {
                
                var uniqueoutproducts = outproducts[j];
                var elementid = uniqueoutproducts.id.split('_');
                var rowid = elementid[1];
                var elID = seqno+"_"+rowid;
                
                if(uniqueoutproducts.value!="" && $("#outproductvariantid"+elID+" option:selected").text()!="Select Variant"){
                    if(outvalues.indexOf(uniqueoutproducts.value)>-1) {
                        $("#outproductvariant"+elID+"_div").addClass("has-error is-focused");
                        new PNotify({title: 'Please select '+(sortseqno)+' sequence '+(j+1)+' is different OUT variant !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        isvaliduniqueoutproducts = 0;
                    }
                    else{ 
                        outvalues.push(uniqueoutproducts.value);
                        if(($("#outproductvariantid"+elID).val()!="" && $("#outproductvariantid"+elID+" option:selected").text()!="Select Variant")){
                            $("#outproductvariantid"+elID+"_div").removeClass("has-error is-focused");
                        }
                    }
                }
            }

            var inproducts = $('select[name="inproductvariantid'+seqno+'[]"]');
            var invalues = [];
            for(j=0;j<inproducts.length;j++) {
                
                var uniqueinproducts = inproducts[j];
                var elementid = uniqueinproducts.id.split('_');
                var rowid = elementid[1];
                var elID = seqno+"_"+rowid;
                
                if(uniqueinproducts.value!="" && $("#inproductvariantid"+elID+" option:selected").text()!="Select Variant"){
                    if(invalues.indexOf(uniqueinproducts.value)>-1) {
                        $("#inproductvariant"+elID+"_div").addClass("has-error is-focused");
                        new PNotify({title: 'Please select '+(sortseqno)+' sequence '+(j+1)+' is different IN variant !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        isvaliduniqueinproducts = 0;
                    }
                    else{ 
                        invalues.push(uniqueinproducts.value);
                        if(($("#inproductvariantid"+elID).val()!="" && $("#inproductvariantid"+elID+" option:selected").text()!="Select Variant")){
                            $("#inproductvariant"+elID+"_div").removeClass("has-error is-focused");
                        }
                    }
                }
            }
        });
        var input_priority = $('input[name="priority[]"]');
        var input_values = [];
        for(j=0;j<input_priority.length;j++) {
            
            var uniquepriority = input_priority[j];
            var rowid = uniquepriority.id.match(/\d+/);
            var seq = $("#processsdetailsequence"+rowid+" input[type=hidden]:nth-child(3)").val();
            
            if(uniquepriority.value!=""){
                if(input_values.indexOf(uniquepriority.value)>-1) {
                    $("#priority"+rowid+"_div").addClass("has-error is-focused");
                    new PNotify({title: 'Please enter '+(seq)+' sequence is different priority !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    isvaliduniquepriority = 0;
                }
                else{ 
                    input_values.push(uniquepriority.value);
                }
            }
        }
        
    }
    
    if(isvalidgroupname == 1 && isvaliddescription == 1 && isvalidprocessid == 1 && isvalidprocesspriority == 1 && isvalidmachineid == 1 && isvalidoutproductid == 1 && isvalidoutvariantid == 1 && isvalidunitid == 1 && isvalidinproductid == 1 && isvalidinvariantid == 1 && isvaliduniqueoutproducts == 1 && isvaliduniqueinproducts == 1 && isvaliduniquepriority == 1){

        var formData = new FormData($('#processgroupform')[0]);
        if(ACTION == 0){ // INSERT
            var baseurl = SITE_URL + 'process-group/process-group-add';
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
                    $("#groupname_div").removeClass("has-error is-focused");
                    if(data['error']==1){
                        new PNotify({title: 'Process group successfully added.',styling: 'fontawesome',delay: '3000',type: 'success'});
                        if(addtype==1){
                            resetdata();
                        }else{
                            setTimeout(function() { window.location = SITE_URL + "process-group";}, 500);
                        }
                    }else if(data['error']==2){
                        new PNotify({title: 'Process group name already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        $("#groupname_div").addClass("has-error is-focused");
                        $('html, body').animate({scrollTop:0},'slow');  
                    }else{
                        new PNotify({title: 'Process group not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
            var baseurl = SITE_URL + 'process-group/update-process-group';
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
                    $("#groupname_div").removeClass("has-error is-focused");
                    if(data['error']==1){
                        new PNotify({title: 'Process group successfully updated.',styling: 'fontawesome',delay: '3000',type: 'success'});
                    
                        setTimeout(function() { window.location = SITE_URL + "process-group";}, 500);
                    }else if(data['error']==2){
                        new PNotify({title: 'Process group name already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        $("#groupname_div").addClass("has-error is-focused");
                        $('html, body').animate({scrollTop:0},'slow');  
                    }else if(data['error']==3){
                        new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
                        $("#name_div").addClass("has-error is-focused");
                    }else{
                        new PNotify({title: 'Process group not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
