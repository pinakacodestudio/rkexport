$(document).ready(function(){
    $(".add_crp_btn").hide();
    $(".add_crp_btn:last").show();

    if(ACTION==1 || ISDUPLICATE==1){
        var isuniversal = (ISDUPLICATE==1)?DUPIsUniversalProduct:$("#productid option:selected").attr("data-isuniversal");
        if(isuniversal==0){
            $("#variantwisepanel").show(); 
            getVariantProductCombination();
        }else{
            $("#variantwisepanel").hide(); 
        }
    }
});
/****MAIN PRODUCT CHANGE EVENT****/
$(document).on('change', '#productid', function (e) {
    if(this.value != 0){
        $("#commonrawpanel").show(); 
        
        var isuniversal = (ISDUPLICATE==1)?DUPIsUniversalProduct:$("#productid option:selected").attr("data-isuniversal");
        if(isuniversal==0){
            $("#variantwisepanel").show(); 
            getVariantProductCombination();
        }else{
            $("#variantwisepanel").hide(); 
        }
    }else{
        $("#commonrawpanel").hide();       
        $("#variantwisepanel").hide();  
    }
});

/****COMMON RAW PRODUCT CHANGE EVENT****/
$(document).on('change', '.commonproductid', function (e) {

    var ID = this.id.match(/(\d+)/g);
    var unit = $("#commonunitid"+ID).val();
    
    $("#uniquerawproduct"+ID).val("0_"+unit);
    getproductprice(ID,'common');
});
/****COMMON RAW PRODUCT CHANGE EVENT****/
$(document).on('change', '.commonpriceid', function (e) {

    var ID = this.id.match(/(\d+)/g);
    var unit = $("#commonunitid"+ID).val();
    $("#uniquerawproduct"+ID).val(this.value+"_"+unit);
});
/****COMMON RAW UNIT CHANGE EVENT****/
$(document).on('change', '.commonunitid', function (e) {

    var ID = this.id.match(/(\d+)/g);
    var priceid = $("#commonpriceid"+ID).val();
    $("#uniquerawproduct"+ID).val(priceid+"_"+this.value);
});
/****COMMON RAW PRODUCT CHANGE EVENT****/
$(document).on('change', '.variantproductid', function (e) {

    var ID = this.id.split("_");
    var MainID = parseInt(ID[1]);
    var RowID = parseInt(ID[2]);
    var unit = $("#variantunitid_"+MainID+"_"+RowID).val();

    $("#uniquevariantproduct_"+MainID+"_"+RowID).val("0_"+unit);
    getproductprice(MainID+"_"+RowID,'variant');
});
/****COMMON RAW PRODUCT PRICE CHANGE EVENT****/
$(document).on('change', '.variantproductpriceid', function (e) {

    var ID = this.id.split("_");
    var MainID = parseInt(ID[1]);
    var RowID = parseInt(ID[2]);
    var unit = $("#variantunitid_"+MainID+"_"+RowID).val();

    $("#uniquevariantproduct_"+MainID+"_"+RowID).val(this.value+"_"+unit);
});
/****COMMON RAW UNIT CHANGE EVENT****/
$(document).on('change', '.variantunitid', function (e) {

    var ID = this.id.split("_");
    var MainID = parseInt(ID[1]);
    var RowID = parseInt(ID[2]);
    var priceid = $("#variantproductpriceid_"+MainID+"_"+RowID).val();
    $("#uniquevariantproduct_"+MainID+"_"+RowID).val(priceid+"_"+this.value);
});
function getVariantProductCombination(){
    var productid = (ISDUPLICATE==1)?DUPPRODUCTID:$("#productid").val();
    var productrecepieid = $("#productrecepieid").val();
    var uurl = SITE_URL+"product-recepie/getVariantProductCombination";
    $.ajax({
        url: uurl,
        type: 'POST',
        data: {productid:String(productid),productrecepieid:String(productrecepieid)},
        dataType: 'json',
        async: false,
        beforeSend: function(){
            $('.mask').show();
            $('#loader').show();
        },
        success: function(response){

            var prices = response['prices'];
            var combination = response['combination'];
            if(prices.length > 0){

                var VARIANT_HTML = '';
                for(var i=0; i<prices.length; i++){

                    var variant = '';
                    if(!$.isEmptyObject(combination)){
                        var priceid = combination[prices[i]['id']];
                        if(priceid.length > 0){
                            variant = '<table class="table table-striped table-bordered" style="box-shadow: 0 1px 6px 0 rgba(0, 0, 0, 0.12), 0px 1px 1px 0 rgba(0, 0, 0, 0.12) !important;">\
                                            <tr>\
                                                <th width="40%">Attribute</th>\
                                                <th width="60%">Variant</th>\
                                            </tr>';
                            for(var j=0; j<priceid.length; j++){

                                variant += '<tr>\
                                                <th>'+priceid[j]['attribute']+'</th>\
                                                <td>'+priceid[j]['variantvalue']+'</td>\
                                            </tr>';
                            }
                            variant += '</table>';
                        }
                    }
                    var productdata = "";
                    if(productrecepieid!="" && !$.isEmptyObject(prices[i]['recepievariantdata']) && prices[i]['recepievariantdata'].length > 0){
                        var recepievariantdata = prices[i]['recepievariantdata'];
                        for(var v=0; v<recepievariantdata.length; v++){
                            var button = "";
                            if(v==0){
                                if(recepievariantdata.length > 1){
                                    button += '<button type="button" class="btn btn-default btn-raised remove_vrp_btn'+(i+1)+'" onclick="removeVariantRawProduct('+(i+1)+',1)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>';
                                }else {
                                    button += '<button type="button" class="btn btn-default btn-raised add_vrp_btn'+(i+1)+'" onclick="addNewVariantRawProduct('+(i+1)+')" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>';
                                }
                            }else if(v!=0) {
                                button += '<button type="button" class="btn btn-default btn-raised remove_vrp_btn'+(i+1)+'" onclick="removeVariantRawProduct('+(i+1)+','+(v+1)+')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>';
                            }
                            button += '<button type="button" class="btn btn-default btn-raised btn-sm remove_vrp_btn'+(i+1)+'" onclick="removeVariantRawProduct('+(i+1)+','+(v+1)+')"  style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>';
                            button += '<button type="button" class="btn btn-default btn-raised add_vrp_btn'+(i+1)+'" onclick="addNewVariantRawProduct('+(i+1)+')" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>';


                            productdata += '<div class="col-md-12 p-n count_variant_material_product'+(i+1)+'" id="countvariantmaterialproduct_'+(i+1)+'_'+(v+1)+'">\
                                                <input type="hidden" name="recepievariantwisematerialid['+prices[i]['id']+'][]" id="recepievariantwisematerialid'+(i+1)+'_'+(v+1)+'" value="'+recepievariantdata[v]['id']+'">\
                                                <div class="col-sm-3">\
                                                    <div class="form-group" id="variantproductid'+(i+1)+'_'+(v+1)+'_div">\
                                                        <div class="col-sm-12 pr-xs pl-xs">\
                                                            <select id="variantproductid_'+(i+1)+'_'+(v+1)+'" name="variantproductid['+prices[i]['id']+'][]" class="selectpicker form-control variantproductid" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                                                <option value="0">Select Product</option>\
                                                                '+PRODUCTDATA+'\
                                                            </select>\
                                                            <input type="hidden" id="prevariantproductid_'+(i+1)+'_'+(v+1)+'" class="prevariantproductid" value="'+recepievariantdata[v]['productid']+'">\
                                                        </div>\
                                                    </div>\
                                                </div>\
                                                <div class="col-sm-3">\
                                                    <div class="form-group" id="variantproductpriceid'+(i+1)+'_'+(v+1)+'_div">\
                                                        <div class="col-sm-12 pr-xs pl-xs">\
                                                            <select id="variantproductpriceid_'+(i+1)+'_'+(v+1)+'" name="variantproductpriceid['+prices[i]['id']+'][]" class="selectpicker form-control variantproductpriceid" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                                                <option value="0">Select Variant</option>\
                                                            </select>\
                                                            <input type="hidden" id="uniquevariantproduct_'+(i+1)+'_'+(v+1)+'" name="uniquevariantproduct[]" value="'+recepievariantdata[v]['rawpriceid']+'_'+recepievariantdata[v]['unitid']+'">\
                                                            <input type="hidden" id="prevariantproductpriceid_'+(i+1)+'_'+(v+1)+'" class="prevariantproductpriceid" value="'+recepievariantdata[v]['rawpriceid']+'">\
                                                        </div>\
                                                    </div>\
                                                </div>\
                                                <div class="col-sm-2">\
                                                    <div class="form-group" id="variantunitid'+(i+1)+'_'+(v+1)+'_div">\
                                                        <div class="col-sm-12 pr-xs pl-xs">\
                                                            <select id="variantunitid_'+(i+1)+'_'+(v+1)+'" name="variantunitid['+prices[i]['id']+'][]" class="selectpicker form-control variantunitid" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                                                <option value="0">Unit</option>\
                                                                '+UNITDATA+'\
                                                            </select>\
                                                            <input type="hidden" id="prevariantunitid_'+(i+1)+'_'+(v+1)+'" class="prevariantunitid" value="'+recepievariantdata[v]['unitid']+'">\
                                                        </div>\
                                                    </div>\
                                                </div>\
                                                <div class="col-sm-2">\
                                                    <div class="form-group" id="variantvalue'+(i+1)+'_'+(v+1)+'_div">\
                                                        <div class="col-sm-12 pr-xs pl-xs">\
                                                            <input type="text" id="variantvalue_'+(i+1)+'_'+(v+1)+'" class="form-control variantvalue text-right" name="variantvalue['+prices[i]['id']+'][]" value="'+parseFloat(recepievariantdata[v]['value']).toFixed(2)+'" onkeypress="return decimal_number_validation(event,this.value,8)">\
                                                        </div>\
                                                    </div>\
                                                </div>\
                                                <div class="col-md-2 pt-md">\
                                                    '+button+'\
                                                </div>\
                                            </div>';
                        }
                    }else{
                        productdata = '<div class="col-md-12 p-n count_variant_material_product'+(i+1)+'" id="countvariantmaterialproduct_'+(i+1)+'_1">\
                                            <div class="col-sm-3">\
                                                <div class="form-group" id="variantproductid'+(i+1)+'_1_div">\
                                                    <div class="col-sm-12 pr-xs pl-xs">\
                                                        <select id="variantproductid_'+(i+1)+'_1" name="variantproductid['+prices[i]['id']+'][]" class="selectpicker form-control variantproductid" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                                            <option value="0">Select Product</option>\
                                                            '+PRODUCTDATA+'\
                                                        </select>\
                                                    </div>\
                                                </div>\
                                            </div>\
                                            <div class="col-sm-3">\
                                                <div class="form-group" id="variantproductpriceid'+(i+1)+'_1_div">\
                                                    <div class="col-sm-12 pr-xs pl-xs">\
                                                        <select id="variantproductpriceid_'+(i+1)+'_1" name="variantproductpriceid['+prices[i]['id']+'][]" class="selectpicker form-control variantproductpriceid" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                                            <option value="0">Select Variant</option>\
                                                        </select>\
                                                        <input type="hidden" id="uniquevariantproduct_'+(i+1)+'_1" name="uniquevariantproduct[]" value="0_0">\
                                                    </div>\
                                                </div>\
                                            </div>\
                                            <div class="col-sm-2">\
                                                <div class="form-group" id="variantunitid'+(i+1)+'_1_div">\
                                                    <div class="col-sm-12 pr-xs pl-xs">\
                                                        <select id="variantunitid_'+(i+1)+'_1" name="variantunitid['+prices[i]['id']+'][]" class="selectpicker form-control variantunitid" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                                            <option value="0">Unit</option>\
                                                            '+UNITDATA+'\
                                                        </select>\
                                                    </div>\
                                                </div>\
                                            </div>\
                                            <div class="col-sm-2">\
                                                <div class="form-group" id="variantvalue'+(i+1)+'_1_div">\
                                                    <div class="col-sm-12 pr-xs pl-xs">\
                                                        <input type="text" id="variantvalue_'+(i+1)+'_1" class="form-control variantvalue text-right" name="variantvalue['+prices[i]['id']+'][]" value="" onkeypress="return decimal_number_validation(event,this.value,8)">\
                                                    </div>\
                                                </div>\
                                            </div>\
                                            <div class="col-md-2 pt-md">\
                                                <button type="button" class="btn btn-default btn-raised remove_vrp_btn'+(i+1)+' m-n" onclick="removeVariantRawProduct('+(i+1)+',1)" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>\
                                                <button type="button" class="btn btn-default btn-raised add_vrp_btn'+(i+1)+' m-n" onclick="addNewVariantRawProduct('+(i+1)+')" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>\
                                            </div>\
                                        </div>';
                    }

                    var checkedrecepievariant = "";
                    if(productrecepieid!=""){
                        if(prices[i]['isrecepievariant']==1){
                            checkedrecepievariant = "checked";
                        }
                    }else{
                        checkedrecepievariant = "checked";
                    }
                    
                    VARIANT_HTML += '<div class="col-md-12 countvariants" id="countvariants'+(i+1)+'">\
                                        <div class="panel panel-dafault productvariantdiv">\
                                            <div class="panel-body" id="">\
                                                <div class="col-md-7 p-n">\
                                                    \
                                                    <div class="col-md-12 p-n">\
                                                        <div class="col-sm-3">\
                                                            <div class="form-group">\
                                                                <div class="col-sm-12 pr-xs pl-xs">\
                                                                    <label class="control-label">Product Name <span class="mandatoryfield">*</span></label>\
                                                                </div>\
                                                            </div>\
                                                        </div>\
                                                        <div class="col-sm-3">\
                                                            <div class="form-group">\
                                                                <div class="col-sm-12 pr-xs pl-xs">\
                                                                    <label class="control-label">Variant <span class="mandatoryfield">*</span></label>\
                                                                </div>\
                                                            </div>\
                                                        </div>\
                                                        <div class="col-sm-2">\
                                                            <div class="form-group">\
                                                                <div class="col-sm-12 pr-xs pl-xs">\
                                                                    <label class="control-label">Unit <span class="mandatoryfield">*</span></label>\
                                                                </div>\
                                                            </div>\
                                                        </div>\
                                                        <div class="col-sm-2">\
                                                            <div class="form-group text-right">\
                                                                <div class="col-sm-12 pr-xs pl-xs">\
                                                                    <label class="control-label">Value <span class="mandatoryfield">*</span></label>\
                                                                </div>\
                                                            </div>\
                                                        </div>\
                                                    </div>\
                                                    \
                                                    <input type="hidden" name="priceid[]" id="priceid'+(i+1)+'" value="'+prices[i]['id']+'">\
                                                    '+productdata+'\
                                                    \
                                                </div>\
                                                <div class="col-md-5 pr-n">\
                                                    <div class="col-md-12">\
                                                        <div class="form-group text-right">\
                                                            <div class="yesno">\
                                                                <input type="checkbox" name="isrecepievariant'+(i+1)+'" id="isrecepievariant'+(i+1)+'" value="'+prices[i]['isrecepievariant']+'" '+checkedrecepievariant+'>\
                                                            </div>\
                                                        </div>\
                                                    </div>\
                                                    '+variant+'\
                                                </div>\
                                            </div>\
                                        </div>\
                                    </div>';
                }
            }
            
            $("#variantmaterialdata").html(VARIANT_HTML);

            $(".prevariantproductid").each(function(){
                var elementid = this.id.split("_");
                var mainid = elementid[1];
                var RowId = elementid[2];
               
                if(this.value != 0){
                    $("#variantproductid_"+mainid+"_"+RowId).val(this.value).selectpicker('refresh');
                    getproductprice(mainid+"_"+RowId,'variant');
                    $("#variantproductpriceid_"+mainid+"_"+RowId).val($("#prevariantproductpriceid_"+mainid+"_"+RowId).val()).selectpicker('refresh');
                }
            });
            $(".prevariantunitid").each(function(){
                var elementid = this.id.split("_");
                var mainid = elementid[1];
                var RowId = elementid[2];
               
                if(this.value != 0){
                    $("#variantunitid_"+mainid+"_"+RowId).val(this.value).selectpicker('refresh');
                }
            });
            $('.countvariants').each(function(){
                var mainid = $(this).attr('id').match(/(\d+)/g);
        
                $(".add_vrp_btn"+mainid).hide();
                $(".add_vrp_btn"+mainid+":last").show();
            });
            $('.yesno input[type="checkbox"]').bootstrapToggle({
                on: 'On',
                off: 'Off',
                onstyle: 'primary',
                offstyle: 'danger',
                style: 'ios',
                size: "small"
            });
            $('.selectpicker').selectpicker('refresh');
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
        complete: function(){
            $('.mask').hide();
            $('#loader').hide();
        },
    });
}

function addNewCommonRawProduct(){

    var rowcount = parseInt($(".count_common_raw_product:last").attr("id").match(/(\d+)/g))+1;
    var datahtml = ' <div class="col-md-6 count_common_raw_product pl-sm pr-sm" id="countcommonrawproduct'+rowcount+'">\
                            <input type="hidden" name="recepiecommonmaterialid[]" value="" id="recepiecommonmaterialid'+rowcount+'">\
                        \
                        <div class="col-sm-3">\
                            <div class="form-group" id="commonproductid'+rowcount+'_div">\
                                <div class="col-sm-12 pr-xs pl-xs">\
                                    <select id="commonproductid'+rowcount+'" name="commonproductid[]" class="selectpicker form-control commonproductid" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                        <option value="0">Select Product</option>\
                                        '+PRODUCTDATA+'\
                                    </select>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-sm-3">\
                            <div class="form-group" id="commonpriceid'+rowcount+'_div">\
                                <div class="col-sm-12 pr-xs pl-xs">\
                                    <select id="commonpriceid'+rowcount+'" name="commonpriceid[]" class="selectpicker form-control commonpriceid" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                        <option value="0">Select Variant</option>\
                                    </select>\
                                    <input type="hidden" id="uniquerawproduct'+rowcount+'" name="uniquerawproduct[]" value="0_0">\
                                </div>\
                            </div>\
                        </div>\
                        \
                        <div class="col-sm-2">\
                          <div class="form-group" id="commonunitid'+rowcount+'_div">\
                              <div class="col-sm-12 pr-xs pl-xs">\
                                <select id="commonunitid'+rowcount+'" name="commonunitid[]" class="selectpicker form-control commonunitid" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                    <option value="0">Unit</option>\
                                    '+UNITDATA+'\
                                </select>\
                              </div>\
                          </div>\
                        </div>\
                        \
                        <div class="col-sm-2">\
                          <div class="form-group" id="commonvalue'+rowcount+'_div">\
                              <div class="col-sm-12 pr-xs pl-xs">\
                                <input type="text" id="commonvalue'+rowcount+'" class="form-control commonvalue text-right" name="commonvalue[]" value="" onkeypress="return decimal_number_validation(event,this.value,8)">\
                              </div>\
                          </div>\
                        </div>\
                        \
                        <div class="col-md-2 pt-md pr-xs">\
                            <button type="button" class="btn btn-default btn-raised remove_crp_btn m-n" onclick="removeCommonRawProduct('+rowcount+')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
                            <button type="button" class="btn btn-default btn-raised add_crp_btn m-n" onclick="addNewCommonRawProduct()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>\
                        </div>\
                      </div>';
    
    $(".remove_crp_btn:first").show();
    $(".add_crp_btn:last").hide();
    $("#countcommonrawproduct"+(rowcount-1)).after(datahtml);
    
    if($(".count_common_raw_product").length > 1){
        $("#commonrawrightlabel").show();
    }else{
        $("#commonrawrightlabel").hide();
    }
    $("#commonproductid"+rowcount+",#commonpriceid"+rowcount+",#commonunitid"+rowcount).selectpicker("refresh");
}
function removeCommonRawProduct(rowid){

    if($('select[name="commonproductid[]"]').length!=1 && ACTION==1 && $('#recepiecommonmaterialid'+rowid).val()!=null){
        var removerecepiecommonmaterialid = $('#removerecepiecommonmaterialid').val();
        $('#removerecepiecommonmaterialid').val(removerecepiecommonmaterialid+','+$('#recepiecommonmaterialid'+rowid).val());
    }
    $("#countcommonrawproduct"+rowid).remove();
    $(".add_crp_btn:last").show();
    if ($(".remove_crp_btn:visible").length == 1) {
        $(".remove_crp_btn:first").hide();
    }
    if($(".count_common_raw_product").length > 1){
        $("#commonrawrightlabel").show();
    }else{
        $("#commonrawrightlabel").hide();
    }
}
function addNewVariantRawProduct(mainid){

    var lastid = $(".count_variant_material_product"+mainid+":last").attr("id").split("_");
    var rowcount = parseInt(lastid[2])+1;
    var priceid = $("#priceid"+mainid).val();
    var datahtml = ' <div class="col-md-12 p-n count_variant_material_product'+mainid+'" id="countvariantmaterialproduct_'+mainid+'_'+rowcount+'">\
                        <input type="hidden" name="recepievariantwisematerialid['+priceid+'][]" value="" id="recepievariantwisematerialid'+mainid+'_'+rowcount+'">\
                        \
                        <div class="col-sm-3">\
                            <div class="form-group" id="variantproductid'+mainid+'_'+rowcount+'_div">\
                                <div class="col-sm-12 pr-xs pl-xs">\
                                    <select id="variantproductid_'+mainid+'_'+rowcount+'" name="variantproductid['+priceid+'][]" class="selectpicker form-control variantproductid" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                        <option value="0">Select Product</option>\
                                        '+PRODUCTDATA+'\
                                    </select>\
                                    <input type="hidden" id="uniquevariantproduct_'+mainid+'_'+rowcount+'" name="uniquevariantproduct[]" value="0_0">\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-sm-3">\
                            <div class="form-group" id="variantproductpriceid'+mainid+'_'+rowcount+'_div">\
                                <div class="col-sm-12 pr-xs pl-xs">\
                                    <select id="variantproductpriceid_'+mainid+'_'+rowcount+'" name="variantproductpriceid['+priceid+'][]" class="selectpicker form-control variantproductpriceid" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                        <option value="0">Select Variant</option>\
                                    </select>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-sm-2">\
                            <div class="form-group" id="variantunitid'+mainid+'_'+rowcount+'_div">\
                                <div class="col-sm-12 pr-xs pl-xs">\
                                    <select id="variantunitid_'+mainid+'_'+rowcount+'" name="variantunitid['+priceid+'][]" class="selectpicker form-control variantunitid" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                        <option value="0">Unit</option>\
                                        '+UNITDATA+'\
                                    </select>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-sm-2">\
                            <div class="form-group" id="variantvalue'+mainid+'_'+rowcount+'_div">\
                                <div class="col-sm-12 pr-xs pl-xs">\
                                    <input type="text" id="variantvalue_'+mainid+'_'+rowcount+'" class="form-control variantvalue text-right" name="variantvalue['+priceid+'][]" value="" onkeypress="return decimal_number_validation(event,this.value,8)">\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-2 pt-md">\
                            <button type="button" class="btn btn-default btn-raised remove_vrp_btn'+mainid+' m-n" onclick="removeVariantRawProduct('+mainid+','+rowcount+')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
                            <button type="button" class="btn btn-default btn-raised add_vrp_btn'+mainid+' m-n" onclick="addNewVariantRawProduct('+mainid+')" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>\
                        </div>\
                      </div>';
    
    $(".remove_vrp_btn"+mainid+":first").show();
    $(".add_vrp_btn"+mainid+":last").hide();
    $("#countvariantmaterialproduct_"+mainid+"_"+(rowcount-1)).after(datahtml);
    
    $("#countvariantmaterialproduct_"+mainid+"_"+rowcount+" .selectpicker").selectpicker("refresh");
}
function getproductprice(divid,type='common'){
    
    if(type == 'variant'){
        var productelement = $("#variantproductid_"+divid);
        var priceelement = $("#variantproductpriceid_"+divid);
    }else{
        var productelement = $("#commonproductid"+divid);
        var priceelement = $("#commonpriceid"+divid);
    }
    priceelement.find('option')
        .remove()
        .end()
        .append('<option value="0">Select Variant</option>')
        .val('0')
    ;
    priceelement.selectpicker('refresh');
    var productid = productelement.val();
    
    if(productid!=0){
      var uurl = SITE_URL+"product-recepie/getVariantByProductId";
      
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {productid:String(productid)},
        dataType: 'json',
        async: false,
        success: function(response){
  
            for(var i = 0; i < response.length; i++) {
                priceelement.append($('<option>', { 
                    value: response[i]['id'],
                    text : response[i]['variantname']
                }));

                /* if(ACTION==1){
                    if(typeof(response[i]['universal'])!='undefined'){
                        priceelement.append($('<option>', { 
                          value: response[i]['id'],
                          text : response[i]['variantname']
                        }));
                        priceelement.val(response[i]['id']).selectpicker("refresh");
                    }else{
                        priceelement.append($('<option>', { 
                            value: response[i]['id'],
                            text : response[i]['variantname']
                        }));
                    }
                }else{
                    priceelement.append($('<option>', { 
                        value: response[i]['id'],
                        text : response[i]['variantname']
                    }));
                    if(response[i]['universal']!='undefined'){
                        priceelement.val(response[i]['id']);
                    }
                } */
            }
            if(type == 'common'){
                if(oldcommonpriceid[divid-1]!="undefined"){
                    priceelement.val(oldcommonpriceid[divid-1]);
                }
            }
            if(response.length == 1){
                priceelement.val(response[0]['id']);
            }
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
      });
    }
    priceelement.selectpicker('refresh');
}
  
function removeVariantRawProduct(mainid,rowid){
  
    var priceid = $("#priceid"+mainid).val();
    if($('select[name="variantproductid['+priceid+'][]"]').length!=1 && ACTION==1 && $('#recepievariantwisematerialid'+mainid+'_'+rowid).val()!=null){
        var removerecepievariantwisematerialid = $('#removerecepievariantwisematerialid').val();
        $('#removerecepievariantwisematerialid').val(removerecepievariantwisematerialid+','+$('#recepievariantwisematerialid'+mainid+'_'+rowid).val());
    }
    $("#countvariantmaterialproduct_"+mainid+"_"+rowid).remove();
    $(".add_vrp_btn"+mainid+":last").show();
    if ($(".remove_vrp_btn"+mainid+":visible").length == 1) {
        $(".remove_vrp_btn"+mainid+":first").hide();
    }
}
function resetdata() {
    $("#productid_div").removeClass("has-error is-focused");
    
    if(ACTION==0){
        $('#productid').val("0");
        
        $(".count_common_raw_product:not(:first)").remove();
        $('.add_crp_btn:first').show();
        $('.remove_crp_btn').hide();
        
        $('.countvariants').each(function(l){
            var mainid = $(this).attr('id').match(/(\d+)/g);
            $(".count_variant_material_product"+mainid+":not(:first)").remove();
            $('.add_vrp_btn'+mainid+':first').show();
            $('.remove_vrp_btn'+mainid).hide();
        });
        $('select.commonproductid').val("0");
        $('select.commonunitid').val("0");
        $('input.commonvalue').val("");
        $("#commonrawpanel").hide();

        $(".selectpicker").selectpicker("refresh");
    }
    $('html, body').animate({scrollTop:0},'slow');  
}
function checkvalidation(addtype=0) {
   
    var mainproductid = $('#productid').val();
    var isuniversal = $("#productid option:selected").attr("data-isuniversal");

    var isvalidmainproductid = isvalidcommonrawmaterial = isvaliduniquecommonproduct = isvalidvariantmaterial = isvaliduniquevariantproduct = 1;
    var countvalidrawproduct = countvalidvariantwiseproduct = 0;
    PNotify.removeAll();
    if(mainproductid == 0) {
        $("#productid_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select product !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmainproductid = 0;
    } else {
        $("#productid_div").removeClass("has-error is-focused");
    }
    
    if(isvalidmainproductid == 1){
        var firstID = parseInt($('.count_common_raw_product:first').attr('id').match(/(\d+)/g));
        $('.count_common_raw_product').each(function(index){
            var CntID = parseInt($(this).attr('id').match(/(\d+)/g));
            
            var isvalidcommonproductid = isvalidcommonpriceid = isvalidcommonunitid = isvalidcommonvalue = 1;

            if($("#commonproductid"+CntID).val() > 0 || $("#commonpriceid"+CntID).val() > 0 || $("#commonunitid"+CntID).val() > 0 || $("#commonvalue"+CntID).val() != "" || CntID == firstID){
      
                if($("#commonproductid"+CntID).val() == 0){
                    $("#commonproductid"+CntID+"_div").addClass("has-error is-focused");
                    new PNotify({title: 'Please select '+(index+1)+' product on common raw material !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    isvalidcommonproductid = 0;
                }else {
                    $("#commonproductid"+CntID+"_div").removeClass("has-error is-focused");
                }
                if($("#commonpriceid"+CntID).val() == 0){
                    $("#commonpriceid"+CntID+"_div").addClass("has-error is-focused");
                    new PNotify({title: 'Please select '+(index+1)+' product variant on common raw material !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    isvalidcommonpriceid = 0;
                }else {
                    $("#commonpriceid"+CntID+"_div").removeClass("has-error is-focused");
                }
                if($("#commonunitid"+CntID).val() == 0){
                    $("#commonunitid"+CntID+"_div").addClass("has-error is-focused");
                    new PNotify({title: 'Please select '+(index+1)+' unit on common raw material !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    isvalidcommonunitid = 0;
                }else {
                    $("#commonunitid"+CntID+"_div").removeClass("has-error is-focused");
                }
                if($("#commonvalue"+CntID).val() == "" || parseFloat($("#commonvalue"+CntID).val()) <= "0"){
                    $("#commonvalue"+CntID+"_div").addClass("has-error is-focused");
                    new PNotify({title: 'Please enter '+(index+1)+' value on common raw material !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    isvalidcommonvalue = 0;
                }else {
                    $("#commonvalue"+CntID+"_div").removeClass("has-error is-focused");
                }
            } else{
                $("#commonproductid"+CntID+"_div").removeClass("has-error is-focused");
                $("#commonpriceid"+CntID+"_div").removeClass("has-error is-focused");
                $("#commonunitid"+CntID+"_div").removeClass("has-error is-focused");
                $("#commonvalue"+CntID+"_div").removeClass("has-error is-focused");
            }
            
            if(isvalidcommonproductid == 1 && isvalidcommonpriceid == 1 && isvalidcommonunitid == 1 && isvalidcommonvalue == 1){
                countvalidrawproduct++;
            }

        }); 
        var inputRawProduct = $('input[name="uniquerawproduct[]"]');
        var values = [];
        for(j=0;j<inputRawProduct.length;j++) {
            var inputRaw = inputRawProduct[j];
            var divid = inputRaw.id.match(/(\d+)/g);
           
            if(inputRaw.value!="0_0"){
                if(values.indexOf(inputRaw.value)>-1) {
                    $("#commonpriceid"+divid+"_div").addClass("has-error is-focused");
                    $("#commonunitid"+divid+"_div").addClass("has-error is-focused");
                    new PNotify({title: 'Please select '+(j+1)+' is different product on common raw material !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    isvaliduniquecommonproduct = 0;
                }else{ 
                    values.push(inputRaw.value);
                    // $("#commonproductid"+divid+"_div").removeClass("has-error is-focused");
                    // $("#commonunitid"+divid+"_div").removeClass("has-error is-focused");
                }
            }
        }

        if(isuniversal==0){
            $('.countvariants').each(function(l){
                var mainid = $(this).attr('id').match(/(\d+)/g);
                var firstId = $('.count_variant_material_product'+mainid+':first').attr('id').split('_');
                firstId = firstId[2];

                if($('#isrecepievariant'+mainid).is(':checked')){
                    
                    var countvalidvariantproduct = 0;
                    $('.count_variant_material_product'+mainid).each(function(index){
                        var elementid = $(this).attr('id').split('_');
                        var CntId = elementid[2];
                        var ElID = mainid+"_"+CntId;
                        var isvalidvariantproductid = isvalidvariantproductpriceid = isvalidvariantunitid = isvalidvariantvalue = 1;

                        if($("#variantproductid_"+ElID).val() > 0 || $("#variantproductpriceid_"+ElID).val() > 0 || $("#variantunitid_"+ElID).val() > 0 || $("#variantvalue_"+ElID).val() != "" || CntId == firstId){
                
                            if($("#variantproductid_"+ElID).val() == 0){
                                $("#variantproductid"+ElID+"_div").addClass("has-error is-focused");
                                new PNotify({title: 'Please select '+(l+1)+' variant '+(index+1)+' product on variant wise material !',styling: 'fontawesome',delay: '3000',type: 'error'});
                                isvalidvariantproductid = 0;
                            }else {
                                $("#variantproductid"+ElID+"_div").removeClass("has-error is-focused");
                            }
                            if($("#variantproductpriceid_"+ElID).val() == 0){
                                $("#variantproductpriceid"+ElID+"_div").addClass("has-error is-focused");
                                new PNotify({title: 'Please select '+(l+1)+' variant '+(index+1)+' variant on variant wise material !',styling: 'fontawesome',delay: '3000',type: 'error'});
                                isvalidvariantproductpriceid = 0;
                            }else {
                                $("#variantproductpriceid"+ElID+"_div").removeClass("has-error is-focused");
                            }
                            if($("#variantunitid_"+ElID).val() == 0){
                                $("#variantunitid"+ElID+"_div").addClass("has-error is-focused");
                                new PNotify({title: 'Please select '+(l+1)+' variant '+(index+1)+' unit on variant wise material !',styling: 'fontawesome',delay: '3000',type: 'error'});
                                isvalidvariantunitid = 0;
                            }else {
                                $("#variantunitid"+ElID+"_div").removeClass("has-error is-focused");
                            }
                            if($("#variantvalue_"+ElID).val() == "" || parseFloat($("#variantvalue_"+ElID).val()) <= "0"){
                                $("#variantvalue"+ElID+"_div").addClass("has-error is-focused");
                                new PNotify({title: 'Please enter '+(l+1)+' variant '+(index+1)+' value on variant wise material !',styling: 'fontawesome',delay: '3000',type: 'error'});
                                isvalidvariantvalue = 0;
                            }else {
                                $("#variantvalue"+ElID+"_div").removeClass("has-error is-focused");
                            }
                        } else{
                            $("#variantproductid"+ElID+"_div").removeClass("has-error is-focused");
                            $("#variantproductpriceid"+CntID+"_div").removeClass("has-error is-focused");
                            $("#variantunitid"+ElID+"_div").removeClass("has-error is-focused");
                            $("#variantvalue"+ElID+"_div").removeClass("has-error is-focused");
                        }
                        
                        if(isvalidvariantproductid == 1 && isvalidvariantproductpriceid == 1 && isvalidvariantunitid == 1 && isvalidvariantvalue == 1){
                            countvalidvariantproduct++;
                        }
                    });
                    if($('.count_variant_material_product'+mainid).length == countvalidvariantproduct){
                        countvalidvariantwiseproduct++;
                    }
            
                    var inputVariantProduct = $('.count_variant_material_product'+mainid+' input[name="uniquevariantproduct[]"]');
                    var values = [];
                    for(j=0;j<inputVariantProduct.length;j++) {
                        var inputRaw = inputVariantProduct[j];
                        var divid = inputRaw.id.split('_');
                        divid = divid[2];
        
                        if(inputRaw.value!="0_0"){
                            if(values.indexOf(inputRaw.value)>-1) {
                                $("#variantproductpriceid"+mainid+"_"+divid+"_div").addClass("has-error is-focused");
                                $("#variantunitid"+mainid+"_"+divid+"_div").addClass("has-error is-focused");
                                new PNotify({title: 'Please select '+(l+1)+' variant '+(j+1)+' is different product variant & unit on variant wise  material !',styling: 'fontawesome',delay: '3000',type: 'error'});
                                isvaliduniquevariantproduct = 0;
                            }else{ 
                                values.push(inputRaw.value);
                                // $("#commonproductid"+divid+"_div").removeClass("has-error is-focused");
                                // $("#commonunitid"+divid+"_div").removeClass("has-error is-focused");
                            }
                        }
                    }
                }else{
                    countvalidvariantwiseproduct++;
                    $(".count_variant_material_product"+mainid+":not(:first)").remove();
                    $('.add_vrp_btn'+mainid+':first').show();
                    $('.remove_vrp_btn'+mainid).hide();

                    $("#variantproductid"+mainid+"_"+firstId+"_div").removeClass("has-error is-focused");
                    $("#variantunitid"+mainid+"_"+firstId+"_div").removeClass("has-error is-focused");
                    $("#variantvalue"+mainid+"_"+firstId+"_div").removeClass("has-error is-focused");
                    $("#variantproductid_"+mainid+"_"+firstId+",#variantunitid_"+mainid+"_"+firstId).val('0').selectpicker('refresh');
                    $("#variantvalue_"+mainid+"_"+firstId).val('');
                }
            });
            if($('.countvariants').length != countvalidvariantwiseproduct){
                isvalidvariantmaterial = 0;
            }
        }
    }
    if($('.count_common_raw_product').length != countvalidrawproduct){
        isvalidcommonrawmaterial = 0;
    }
    if(isvalidmainproductid == 1 && isvalidcommonrawmaterial == 1 && isvaliduniquecommonproduct == 1 && isvalidvariantmaterial == 1 && isvaliduniquevariantproduct == 1){
        
        var formData = new FormData($('#product-recepie-form')[0]);
        if(ACTION == 0){ // INSERT
            var baseurl = SITE_URL + 'product-recepie/product-recepie-add';
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
                    $("#productid_div").removeClass("has-error is-focused");
                    if(data['error']==1){
                        new PNotify({title: 'Product recepie successfully added.',styling: 'fontawesome',delay: '3000',type: 'success'});
                        if(addtype==1){
                            resetdata();
                        }else{
                            setTimeout(function() { window.location = SITE_URL + "product-recepie";}, 500);
                        }
                    }else if(data['error']==2){
                        new PNotify({title: 'Product name already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        $("#productid_div").addClass("has-error is-focused");
                    }else{
                        new PNotify({title: 'Product recepie not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
            var baseurl = SITE_URL + 'product-recepie/update-product-recepie';
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
                    $("#productid_div").removeClass("has-error is-focused");
                    if(data['error']==1){
                        new PNotify({title: 'Product recepie successfully updated.',styling: 'fontawesome',delay: '3000',type: 'success'});
                    
                        setTimeout(function() { window.location = SITE_URL + "product-recepie";}, 500);
                    }else if(data['error']==2){
                        new PNotify({title: 'Product name already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        $("#productid_div").addClass("has-error is-focused");
                    }else{
                        new PNotify({title: 'Product recepie not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
