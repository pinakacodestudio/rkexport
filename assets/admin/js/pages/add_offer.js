$(document).ready(function() {

    if(ACTION==1){
        var channelid = $("#channelid").val();
        getmembers(channelid);
    }
    $("#channelid").change(function(){
        var channelid = $(this).val();
        /* var index = -1;
        if(channelid!=null){
            index = channelid.indexOf('0');
        }
        if(index == 0){
            $("#channelid option[value!=0]").prop('disabled', true);
            $("#channelid option[value!=0]").prop('selected', false);
            $("#memberid").prop('disabled', true);
        }else{
            $("#channelid option").removeAttr('disabled');
            $("#memberid").prop('disabled', false);
        }
        $("#channelid").selectpicker('refresh');
        $("#memberid").selectpicker('refresh'); */
        getmembers(channelid);
    });
    $('#datepicker-range').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayBtn:"linked"
    });
    $('#cleardatebtn').click(function(){
        $("#startdate").val("");
        $("#enddate").val("");
    });

    $('.offerimage').change(function(){
        validfile($(this),this);
    });

    $('select.purchaseproductid').change(function(){
      combination = $(this).attr('id').split("_");
      getpricebyproductid(combination[1],combination[2],'purchasepriceid',$(this).val());
    });

    $('select.offerproductid').change(function(){
      combination = $(this).attr('id').split("_");
      getpricebyproductid(combination[1],combination[2],'offerpriceid',$(this).val());
    });

    $(".add_image_btn").hide();
    $(".add_image_btn:last").show();

    if(ACTION==1){

    }
    $('input[name="type"]').click(function(){
        var type = this.value;
        var offertype = $("input[name='offertype']:checked").val();  
        if(type == 1 || type == 4){
            $("#useractivationrequired_div").show();
            $(".targetdiv").hide();
            if(type==4){
                $(".targetdiv").show();
                $(".add_combination_btn").hide();
                $(".remove_combination_btn").hide();
                $(".combination:not(:first)").remove();
            }
        }else{
            $("#useractivationrequired_div").hide();
            $("#useractivationno").prop("checked",true);
            $(".targetdiv").hide();
            if(offertype==1){
                $(".add_combination_btn:last").show();
                if ($(".remove_combination_btn:visible").length == 1) {
                    $(".remove_combination_btn:first").hide();
                }
            }
        }
        
        $('input[name="combinationid[]"]').each(function(){
            if(type==1){
                
                $('#combination_div').html('');
                if(offertype==1){
                    $(".add_combination_btn:last").show();
                    if ($(".remove_combination_btn:visible").length == 1) {
                        $(".remove_combination_btn:first").hide();
                    }
                }
                $('.combination').hide();
                $('.gifthead'+this.value).hide();
            }else if(type==3){
                $('.combination').show();
                $('#offerproductid_'+this.value+'_1').val(0);
                $('#offerpriceid_'+this.value+'_1').val(0);
                $('#offerqty_'+this.value+'_1').val('');
                $('#percentage_'+this.value+'_1').prop("checked", true);
                $('#discountvalue_'+this.value+'_1').val('');
                $('#countofferproduct_'+this.value+'_1').hide();
                $('#countofferproduct_'+this.value+'_1').hide();
                $('.gifthead'+this.value).hide();
                
            }else{
                $('.combination').show();
                $('#countofferproduct_'+this.value+'_1').show();
                $('.gifthead'+this.value).show();
            }
            $('#offerproduct_'+this.value+'_div').html('');
            //$('.offerproduct').not('.offerproduct:first').html('');
        });
        $(".selectpicker").selectpicker('refresh');
        getpricebyproductid(1,1,'offerpriceid',0);
    });

    $('input[name="offertype"]').click(function(){
      var type = this.value;
      var otype = $("input[name='type']:checked").val(); 
      if(type == 1){
          if(otype==2){
              $(".add_combination_btn:last").show();
              if ($(".remove_combination_btn:visible").length == 1) {
                  $(".remove_combination_btn:first").hide();
              }
          }
          $(".brandoption").hide();
          $(".purchaseqty").prop('readonly',false);
          getproductbybrandid(0);
          $('select.purchaseproductid')
              .append(PURCHASE_PRODUCT_DATA)
              .val('0')
              .selectpicker('refresh');
      }else{
          $(".add_combination_btn").hide();
          $(".remove_combination_btn").hide();
          $(".brandoption").show();
          $(".purchaseqty").prop('readonly',true);
          $(".combination:not(:first)").remove();
          getproductbybrandid(0);
          $('select.brandid').val(0).selectpicker('refresh');
          $('input.minpurchaseamount').val('');
      }
    });
   
    $(document).on('change','select.brandid',function(){
        var id = $(this).attr('id').match(/\d+/);
        getproductbybrandid($(this).val());
    });
    
    if(ACTION==1 && brandid!=""){
        // getproductbybrandid(brandid);
    }
});
/* $(document).on('change','select.giftproductid',function(){
    var id = $(this).attr('id').match(/\d+/);
    getgiftpricebyproductid(parseInt(id),$(this).val());
}); */
function getproductbybrandid(brandid,combinationid='',rowid=''){
    var purchaseproductid = $('select.purchaseproductid');
    var purchasepriceid = $('select.purchasepriceid');
    var purchaseqty = $('input.purchaseqty');
    PURCHASE_BRAND_PRODUCT_DATA = "";
    if(combinationid!="" && rowid!=""){
        var purchaseproductid = $('#purchaseproductid_'+combinationid+'_'+rowid);
        var purchasepriceid = $('#purchasepriceid_'+combinationid+'_'+rowid);
        var purchaseqty = $('#purchaseqty_'+combinationid+'_'+rowid);
    }
    purchaseproductid
            .find('option')
            .remove()
            .end()
            .append('<option value="0">Select Product</option>')
            .val('0');
    purchasepriceid
            .find('option')
            .remove()
            .val('');
    purchaseqty.val('');
    
    if(brandid!=0){
        var uurl = SITE_URL+"product/getProductByBrandid";
        $.ajax({
            url: uurl,
            type: 'POST',
            data: {brandid:String(brandid)},
            dataType: 'json',
            async: false,
            success: function(response){
    
                for(var i = 0; i < response.length; i++) {
                    
                    var productname = response[i]['name'].replace("'","&apos;");
                    if(DROPDOWN_PRODUCT_LIST==0){
                        
                        purchaseproductid.append($('<option>', { 
                            value: response[i]['id'],
                            text : productname
                        }));
        
                        PURCHASE_BRAND_PRODUCT_DATA += '<option value="'+response[i]['id']+'">'+productname+'</option>';
                    }else{
                        
                        purchaseproductid.append($('<option>', { 
                            value: response[i]['id'],
                            // text : productname,
                            "data-content" :'<img src="'+PRODUCT_PATH+response[i]['image']+'" style="width:40px">  ' + productname
                        }));

                        PURCHASE_BRAND_PRODUCT_DATA += '<option data-content="<img src=&apos;'+PRODUCT_PATH+response[i]['image']+'&apos; style=&apos;width:40px&apos;>  '+productname+'" value="'+response[i]['id']+'">'+productname+'</option>';
                    }
                }  
            //$('#priceid'+divid).val(priceid);
            },
            error: function(xhr) {
            //alert(xhr.responseText);
            },
        });
    }
    purchaseproductid.selectpicker('refresh');
    purchasepriceid.selectpicker('refresh');

}
function validfile(obj,thisobj){
    var val = obj.val();
    var id = obj.attr('id').match(/\d+/);
    var filename = obj.val().replace(/C:\\fakepath\\/i, '');
    switch(val.substring(val.lastIndexOf('.') + 1).toLowerCase()){
      case 'gif': case 'bmp': case 'jpg': case 'jpeg': case 'png':
        
        $("#Filetext"+id).val(filename);
        readURL(thisobj,"imagepreview"+id);
        isvalidfiletext = 1;
        $("#image"+id+"_div").removeClass("has-error is-focused");
        break;
      default:
        $("#offerimage"+id).val("");
        $("#Filetext"+id).val("");
        isvalidfiletext = 0;
        $("#image"+id+"_div").addClass("has-error is-focused");
        new PNotify({title: 'Please upload valid file !',styling: 'fontawesome',delay: '3000',type: 'error'});
        break;
    }
}
function addnewimage(){

    var rowcount = parseInt($(".countimages:last").attr("id").match(/\d+/))+1;
    var datahtml = '<div class="col-md-6 p-n countimages" id="countimages'+rowcount+'">\
                        <div class="col-md-9">\
                            <div class="form-group" id="image'+rowcount+'_div">\
                                <div class="col-md-3 text-center">\
                                    <img src="'+DEFAULT_IMAGE_PREVIEW+'" id="imagepreview'+rowcount+'"                                     class="thumbwidth">\
                                </div>\
                                <div class="col-md-9 pl-n" style="padding-top: 23px;">\
                                    <div class="input-group" id="fileupload'+rowcount+'">\
                                        <span class="input-group-btn" style="padding: 0 0px 0px 0px;">\
                                            <span class="btn btn-primary btn-raised btn-file"><i class="fa fa-upload"></i>\
                                                <input type="file" name="offerimage'+rowcount+'" class="offerimage" id="offerimage'+rowcount+'" accept=".bmp,.bm,.gif,.ico,.jfif,.jfif-tbnl,jpe,.jpeg,.jpg,.pbm,.png,.svf,.tif,.tiff,wbmp,.x-png">\
                                            </span>\
                                        </span>\
                                        <input type="text" readonly="" id="Filetext'+rowcount+'" class="form-control" name="Filetext[]" value="">\
                                    </div>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-1">\
                            <div class="form-group" id="priority'+rowcount+'_div">\
                                <label class="control-label">Priority</label>\
                                <input type="text" class="form-control" name="priority[]" id="priority'+rowcount+'" value="" onkeypress="return isNumber(event)" maxlength="4">\
                            </div>\
                        </div>\
                        <div class="col-md-2 pl-sm mt-xxl">\
                            <button type="button" class="btn btn-default btn-raised remove_image_btn m-n" onclick="removeimage('+rowcount+')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
                            <button type="button" class="btn btn-default btn-raised add_image_btn m-n" onclick="addnewimage()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>\
                        </div>\
                    </div>';
    
    $(".remove_image_btn:first").show();
    $(".add_image_btn:last").hide();
    $("#countimages"+(rowcount-1)).after(datahtml);
    
    $('#offerimage'+rowcount).change(function(){
        validfile($(this),this);
    });
}
function removeimage(rowid){

    if($('.offerimage').length!=1 && ACTION==1 && $('#offerimageid'+rowid).val()!=null){
        var removeofferimageid = $('#removeofferimageid').val();
        $('#removeofferimageid').val(removeofferimageid+','+$('#offerimageid'+rowid).val());
    }
    $("#countimages"+rowid).remove();

    $(".add_image_btn:last").show();
    if ($(".remove_image_btn:visible").length == 1) {
        $(".remove_image_btn:first").hide();
    }
}
function addnewpurchase(combinationid){

    var offertype = $("input[name='offertype']:checked").val();  
    var readonly = (offertype==0)?"readonly":"";    
    var productdataoptions = (offertype==0)?PURCHASE_BRAND_PRODUCT_DATA:PURCHASE_PRODUCT_DATA;                    
  combination = $(".purchaseproduct:last").attr("id").split("_");
  var rowcount = parseInt(combination[2])+1;
  var datahtml = '<div class="col-md-12 p-n purchaseproduct" id="countpurchaseproduct_'+combinationid+'_'+rowcount+'"> \
                    <div class="col-md-4 pr-sm pl-sm"> \
                      <div class="form-group ml-n mr-n" id="purchaseproductid_'+combinationid+'_'+rowcount+'_div"> \
                          <select id="purchaseproductid_'+combinationid+'_'+rowcount+'" name="purchaseproductid['+combinationid+'][]" class="selectpicker form-control purchaseproductid" data-live-search="true" data-select-on-tab="true" data-size="5" data-actions-box="true"> \
                            <option value="0">Select Product</option> \
                            '+productdataoptions+' \
                          </select> \
                      </div> \
                    </div> \
                    <div class="col-md-4 pr-sm pl-sm"> \
                      <div class="form-group ml-n mr-n" id="purchasepriceid_'+combinationid+'_'+rowcount+'_div"> \
                          <select id="purchasepriceid_'+combinationid+'_'+rowcount+'" name="purchasepriceid['+combinationid+']['+(rowcount-1)+'][]" class="selectpicker form-control purchasepriceid" multiple data-live-search="true" data-select-on-tab="true" data-size="5" data-actions-box="true" title="Select Variant"> \
                          </select> \
                      </div> \
                    </div> \
                    <div class="col-md-2 pr-sm pl-sm"> \
                      <div class="form-group ml-n mr-n" id="purchaseqty_'+combinationid+'_'+rowcount+'_div"> \
                          <input type="text" class="form-control purchaseqty" name="purchaseqty['+combinationid+'][]" id="purchaseqty_'+combinationid+'_'+rowcount+'" onkeypress="'+(MANAGE_DECIMAL_QTY==1?"return decimal_number_validation(event, this.value,8);":"return isNumber(event);")+'" '+readonly+'> \
                      </div> \
                    </div> \
                    <div class="col-md-2 pl-sm" style="margin-top: 20px !important;"> \
                        <button type="button" class="btn btn-default btn-raised remove_purchase_btn_'+combinationid+' m-n" onclick="removepurchase('+combinationid+','+rowcount+')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button> \
                        <button type="button" class="btn btn-default btn-raised add_purchase_btn_'+combinationid+' m-n" onclick="addnewpurchase('+combinationid+')" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button> \
                    </div> \
                </div>';
  
  $(".remove_purchase_btn_"+combinationid+":first").show();
  $(".add_purchase_btn_"+combinationid+":last").hide();
  $("#purchaseproduct_"+combinationid+"_div").append(datahtml);
  $(".selectpicker").selectpicker('refresh');
  $('select.purchaseproductid').change(function(){
    combination = $(this).attr('id').split("_");
    getpricebyproductid(combination[1],combination[2],'purchasepriceid',$(this).val());
    
  });
}
function removepurchase(combinationid,rowid){

  if($('.purchaseproduct').length!=1 && ACTION==1 && $('#purchaseofferproductid_'+combinationid+'_'+rowid).val()!=null){
      var removepurchaseofferproductid = $('#removepurchaseofferproductid'+combinationid).val();
      $('#removepurchaseofferproductid'+combinationid).val(removepurchaseofferproductid+','+$('#purchaseofferproductid_'+combinationid+'_'+rowid).val());
  }
  $('#countpurchaseproduct_'+combinationid+'_'+rowid).remove();

  $(".add_purchase_btn_"+combinationid+":last").show();
  if ($(".remove_purchase_btn_"+combinationid+":visible").length == 1) {
      $(".remove_purchase_btn_"+combinationid+":first").hide();
  }
}

function addnewoffer(combinationid){
  combination = $(".offerproduct:last").attr("id").split("_");
  var rowcount = parseInt(combination[2])+1;
  var datahtml = '<div class="col-md-12 p-n offerproduct" id="countofferproduct_'+combinationid+'_'+rowcount+'"> \
                    <div class="col-md-4 pr-sm pl-sm"> \
                      <div class="form-group ml-n mr-n" id="offerproductid_'+combinationid+'_'+rowcount+'_div"> \
                          <select id="offerproductid_'+combinationid+'_'+rowcount+'" name="offerproductid['+combinationid+'][]" class="selectpicker form-control offerproductid" data-live-search="true" data-select-on-tab="true" data-size="5" data-actions-box="true"> \
                            <option value="0">Select Product</option> \
                            '+OFFER_PRODUCT_DATA+' \
                          </select> \
                      </div> \
                    </div> \
                    <div class="col-md-4 pr-sm pl-sm"> \
                      <div class="form-group ml-n mr-n" id="offerpriceid_'+combinationid+'_'+rowcount+'_div"> \
                          <select id="offerpriceid_'+combinationid+'_'+rowcount+'" name="offerpriceid['+combinationid+'][]" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5" data-actions-box="true"> \
                            <option value="0">Select Variant</option> \
                          </select> \
                      </div> \
                    </div> \
                    <div class="col-md-2 pr-sm pl-sm"> \
                      <div class="form-group ml-n mr-n" id="offerqty_'+combinationid+'_'+rowcount+'_div"> \
                          <input type="text" class="form-control" name="offerqty['+combinationid+'][]" id="offerqty_'+combinationid+'_'+rowcount+'" value="" onkeypress="'+(MANAGE_DECIMAL_QTY==1?"return decimal_number_validation(event, this.value,8);":"return isNumber(event);")+'"> \
                      </div> \
                    </div> \
                    <div class="col-md-2 pl-sm" style="margin-top: 20px !important;"> \
                        <button type="button" class="btn btn-default btn-raised remove_offer_btn_'+combinationid+' m-n" onclick="removeoffer('+combinationid+','+rowcount+')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button> \
                        <button type="button" class="btn btn-default btn-raised add_offer_btn_'+combinationid+' m-n" onclick="addnewoffer('+combinationid+')" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button> \
                    </div> \
                    <div class="col-md-6 pr-sm pl-sm"> \
                        <div class="form-group ml-n mr-n"> \
                            <label class="control-label">Discount Type</label> \
                            <div class="col-md-12 p-n"> \
                                <div class="col-sm-6 col-xs-6" style="padding-left: 0px;"> \
                                    <div class="radio"> \
                                    <input type="radio" name="offerdiscounttype_'+combinationid+'_'+rowcount+'" id="percentage_'+combinationid+'_'+rowcount+'" value="1" checked onclick="validdiscount('+combinationid+','+rowcount+')"> \
                                    <label for="percentage_'+combinationid+'_'+rowcount+'">Percentage</label> \
                                    </div> \
                                </div> \
                                <div class="col-sm-6 col-xs-6"> \
                                    <div class="radio"> \
                                    <input type="radio" name="offerdiscounttype_'+combinationid+'_'+rowcount+'" id="amounttype_'+combinationid+'_'+rowcount+'" value="0" onclick="validdiscount('+combinationid+','+rowcount+')"> \
                                    <label for="amounttype_'+combinationid+'_'+rowcount+'">Amount</label> \
                                    </div> \
                                </div> \
                            </div> \
                        </div> \
                    </div> \
                    <div class="col-md-4 pr-sm pl-sm"> \
                        <div class="form-group ml-n mr-n " id="discountvalue_'+combinationid+'_'+rowcount+'_div"> \
                            <label class="control-label">Discount</label> \
                            <div class="col-md-12 p-n"> \
                                <input type="text" class="form-control" name="discountvalue['+combinationid+'][]" id="discountvalue_'+combinationid+'_'+rowcount+'" onkeypress="return decimal_number_validation(event,this.value)" onkeyup="validdiscount('+combinationid+','+rowcount+')" maxlength="8"> \
                            </div> \
                        </div> \
                    </div> \
                </div>';
  
  $('.remove_offer_btn_'+combinationid+':first').show();
  $('.add_offer_btn_'+combinationid+':last').hide();
  $("#offerproduct_"+combinationid+"_div").append(datahtml);
  $(".selectpicker").selectpicker('refresh');
  $('select.offerproductid').change(function(){
    combination = $(this).attr('id').split("_");
    getpricebyproductid(combination[1],combination[2],'offerpriceid',$(this).val());
  });
}
function removeoffer(combinationid,rowid){

  if($('.offerproduct').length!=1 && ACTION==1 && $('#offerproducttableid_'+combinationid+'_'+rowid).val()!=null){
    var removeofferproductid = $('#removeofferproductid'+combinationid).val();
    $('#removeofferproductid'+combinationid).val(removeofferproductid+','+$('#offerproducttableid_'+combinationid+'_'+rowid).val());
  }
  $('#countofferproduct_'+combinationid+'_'+rowid).remove();

  $('.add_offer_btn_'+combinationid+':last').show();
  if ($('.remove_offer_btn_'+combinationid+':visible').length == 1) {
      $('.remove_offer_btn_'+combinationid+':first').hide();
  }
}

function validdiscount(combinationid,rowid){
    var discounttype = $('input[name="offerdiscounttype_'+combinationid+'_'+rowid+'"]:checked').val();
    var discountvalue = parseFloat($('#discountvalue_'+combinationid+'_'+rowid).val());
    if(discounttype==1){
        if(discountvalue>100){
            $('#discountvalue_'+combinationid+'_'+rowid).val('100.00');
        }
    }
}
function addnewcombination(){

  combination = parseInt($('input[name="combinationid[]"]:last').attr("id").match(/(\d+)/g))+1;
  var type = $('input[name="type"]:checked').val();
  var display = (type==2 || type==4)?'block':'none';
  var datahtml = '<div class="col-sm-12 p-n combination" id="combination_'+combination+'_div"> \
                    <div class="well well-sm" style="float:left;"> \
                        <input type="hidden" id="combinationid'+combination+'" name="combinationid[]" value="'+combination+'"> \
                        <div class="col-sm-6 p-n"> \
                            <div class="panel-heading p-n"> \
                                <h2 class="p-n">Purchased Product</h2> \
                            </div> \
                            <div class="col-sm-12 p-n"> \
                                <div class="form-group ml-n mr-n"> \
                                    <label class="control-label col-md-3">Multiplication</label> \
                                    <div class="col-md-8 p-n"> \
                                        <div class="col-sm-3 col-xs-6" style="padding-left: 0px;"> \
                                            <div class="radio"> \
                                                <input type="radio" name="multiplication_'+combination+'" id="nomultiplication_'+combination+'" value="0" checked> \
                                                <label for="nomultiplication_'+combination+'">No</label> \
                                            </div> \
                                        </div> \
                                        <div class="col-sm-3 col-xs-6"> \
                                            <div class="radio"> \
                                                <input type="radio" name="multiplication_'+combination+'" id="yesmultiplication_'+combination+'" value="1"> \
                                                <label for="yesmultiplication_'+combination+'">Yes</label> \
                                            </div> \
                                        </div> \
                                    </div> \
                                </div> \
                            </div> \
                            <div class="col-sm-12 p-n brandoption" style="display:none;"> \
                                <div class="col-md-6 pr-sm pl-sm"> \
                                    <div class="form-group ml-n mr-n" id="brandid'+combination+'_div"> \
                                        <label class="control-label">Select Brand</label> \
                                        <select id="brandid'+combination+'" name="brandid[]" class="selectpicker form-control brandid" data-live-search="true" data-select-on-tab="true" data-size="5" data-actions-box="true"> \
                                            <option value="0">Select Brand</option> \
                                            '+BRAND_DATA+' \
                                        </select> \
                                    </div>\
                                </div> \
                                <div class="col-md-5 pr-sm pl-sm"> \
                                    <div class="form-group ml-n mr-n text-right" id="minpurchaseamount'+combination+'_div"> \
                                        <label class="control-label">Min. Purchase Amount (&#8377;) <span class="mandatoryfield">*</span></label> \
                                        <input type="text" id="minpurchaseamount'+combination+'" name="minpurchaseamount" class="form-control text-right minpurchaseamount" onkeypress="return decimal_number_validation(event, this.value)"> \
                                    </div> \
                                </div> \
                            </div> \
                            <div class="col-sm-12 p-n"> \
                                <div class="col-md-4 pr-sm pl-sm"> \
                                    <div class="form-group ml-n mr-n"> \
                                        <label class="control-label">Select Product</label> \
                                    </div> \
                                </div> \
                                <div class="col-md-4 pr-sm pl-sm"> \
                                    <div class="form-group ml-n mr-n"> \
                                        <label class="control-label">Select Variant</label> \
                                    </div> \
                                </div> \
                                <div class="col-md-4 pr-sm pl-sm"> \
                                    <div class="form-group ml-n mr-n"> \
                                        <label class="control-label">Qty.</label> \
                                    </div> \
                                </div> \
                            </div> \
                            <div class="col-sm-12 p-n purchaseproduct" id="countpurchaseproduct_'+combination+'_1"> \
                                <div class="col-md-4 pr-sm pl-sm"> \
                                    <div class="form-group ml-n mr-n" id="purchaseproductid_'+combination+'_1_div"> \
                                        <select id="purchaseproductid_'+combination+'_1" name="purchaseproductid['+combination+'][]" class="selectpicker form-control purchaseproductid" data-live-search="true" data-select-on-tab="true" data-size="5" data-actions-box="true"> \
                                            <option value="0">Select Product</option> \
                                            '+PURCHASE_PRODUCT_DATA+' \
                                        </select> \
                                    </div> \
                                </div> \
                                <div class="col-md-4 pr-sm pl-sm"> \
                                    <div class="form-group ml-n mr-n" id="purchasepriceid_'+combination+'_1_div"> \
                                        <select id="purchasepriceid_'+combination+'_1" name="purchasepriceid['+combination+'][0][]" class="selectpicker form-control" multiple data-live-search="true" data-select-on-tab="true" data-size="5" data-actions-box="true" title="Select Variant"> \
                                            <option value="0">Select Variant</option> \
                                        </select> \
                                    </div> \
                                </div> \
                                <div class="col-md-2 pr-sm pl-sm"> \
                                    <div class="form-group ml-n mr-n" id="purchaseqty_'+combination+'_1_div"> \
                                        <input type="text" class="form-control" name="purchaseqty['+combination+'][]" id="purchaseqty_'+combination+'_1" value="" onkeypress="'+(MANAGE_DECIMAL_QTY==1?"return decimal_number_validation(event, this.value,8);":"return isNumber(event);")+'"> \
                                    </div> \
                                </div> \
                                <div class="col-md-2 pl-sm" style="margin-top: 20px !important;"> \
                                    <button type="button" class="btn btn-default btn-raised remove_purchase_btn_'+combination+' m-n" onclick="removepurchase('+combination+',1)" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button> \
                                    <button type="button" class="btn btn-default btn-raised  add_purchase_btn_'+combination+' m-n" onclick="addnewpurchase('+combination+')" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button> \
                                </div> \
                            </div> \
                            <div id="purchaseproduct_'+combination+'_div"> \
                            </div> \
                        </div> \
                        <div class="col-sm-6 p-n"> \
                            <div class="panel-heading p-n"> \
                                <h2 class="p-n" style="width:100%"> \
                                    <div class="col-sm-9 p-n"><span class="gifthead'+combination+'" style="display:'+display+'">Gift Product</span></div> \
                                    <div class="col-sm-3 p-n text-right"> \
                                        <a href="javascript:void(0)" class="btn btn-danger btn-raised btn-sm remove_combination_btn" onclick="removecombination('+combination+')"><i class="fa fa-minus mr-n" aria-hidden="true"></i></a> \
                                        <a href="javascript:void(0)" class="btn btn-success btn-raised add_combination_btn" onclick="addnewcombination()"><i class="fa fa-plus mr-n" aria-hidden="true"></i></a> \
                                    </div> \
                                </h2> \
                            </div> \
                            <div class="col-sm-12 p-n gifthead'+combination+'" style="display:'+display+'"> \
                                <div class="col-md-4 pr-sm pl-sm"> \
                                    <div class="form-group ml-n mr-n"> \
                                        <label class="control-label">Select Product</label> \
                                    </div> \
                                </div> \
                                <div class="col-md-4 pr-sm pl-sm"> \
                                    <div class="form-group ml-n mr-n"> \
                                        <label class="control-label">Select Variant</label> \
                                    </div> \
                                </div> \
                                <div class="col-md-4 pr-sm pl-sm"> \
                                    <div class="form-group ml-n mr-n"> \
                                        <label class="control-label">Qty.</label> \
                                    </div> \
                                </div> \
                            </div> \
                            <div class="col-sm-12 p-n offerproduct" id="countofferproduct_'+combination+'_1" style="display:'+display+'"> \
                                <div class="col-md-4 pr-sm pl-sm"> \
                                    <div class="form-group ml-n mr-n" id="offerproductid_'+combination+'_1_div"> \
                                        <select id="offerproductid_'+combination+'_1" name="offerproductid['+combination+'][]" class="selectpicker form-control offerproductid" data-live-search="true" data-select-on-tab="true" data-size="5" data-actions-box="true"> \
                                            <option value="0">Select Product</option> \
                                            '+OFFER_PRODUCT_DATA+' \
                                        </select> \
                                    </div> \
                                </div> \
                                <div class="col-md-4 pr-sm pl-sm"> \
                                    <div class="form-group ml-n mr-n" id="offerpriceid_'+combination+'_1_div"> \
                                        <select id="offerpriceid_'+combination+'_1" name="offerpriceid['+combination+'][]" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5" data-actions-box="true"> \
                                            <option value="0">Select Variant</option> \
                                        </select> \
                                    </div> \
                                </div> \
                                <div class="col-md-2 pr-sm pl-sm"> \
                                    <div class="form-group ml-n mr-n" id="offerqty_'+combination+'_1_div"> \
                                        <input type="text" class="form-control" name="offerqty['+combination+'][]" id="offerqty_'+combination+'_1" onkeypress="'+(MANAGE_DECIMAL_QTY==1?"return decimal_number_validation(event, this.value,8);":"return isNumber(event);")+'"> \
                                    </div> \
                                </div> \
                                <div class="col-md-2 pl-sm" style="margin-top: 20px !important;"> \
                                    <button type="button" class="btn btn-default btn-raised remove_offer_btn m-n" onclick="removeoffer('+combination+',1)" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button> \
                                    <button type="button" class="btn btn-default btn-raised  add_offer_btn m-n" onclick="addnewoffer('+combination+')" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button> \
                                </div> \
                                <div class="col-md-6 pr-sm pl-sm"> \
                                    <div class="form-group ml-n mr-n"> \
                                        <label class="control-label">Discount Type</label> \
                                        <div class="col-md-12 p-n"> \
                                            <div class="col-sm-6 col-xs-6" style="padding-left: 0px;"> \
                                                <div class="radio"> \
                                                <input type="radio" name="offerdiscounttype_'+combination+'_1" id="percentage_'+combination+'_1" value="1" checked="" onclick="validdiscount('+combination+',1)"> \
                                                <label for="percentage_'+combination+'_1">Percentage</label> \
                                                </div> \
                                            </div> \
                                            <div class="col-sm-6 col-xs-6"> \
                                                <div class="radio"> \
                                                <input type="radio" name="offerdiscounttype_'+combination+'_1" id="amounttype_'+combination+'_1" value="0" onclick="validdiscount('+combination+',1)"> \
                                                <label for="amounttype_'+combination+'_1">Amount</label> \
                                                </div> \
                                            </div> \
                                        </div> \
                                    </div> \
                                </div> \
                                <div class="col-md-4 pr-sm pl-sm"> \
                                    <div class="form-group ml-n mr-n" id="discountvalue_'+combination+'_1_div"> \
                                        <label class="control-label">Discount</label> \
                                        <div class="col-md-12 p-n"> \
                                            <input type="text" class="form-control" name="discountvalue['+combination+'][]" id="discountvalue_'+combination+'_1" onkeypress="return decimal_number_validation(event,this.value)" onkeyup="validdiscount('+combination+',1)" maxlength="8"> \
                                        </div> \
                                    </div> \
                                </div> \
                            </div> \
                            <div id="offerproduct_'+combination+'_div"></div> \
                        </div> \
                    </div> \
                  </div>';
  $(".remove_combination_btn:first").show();
  $(".add_combination_btn:last").hide();
  $('#combination_div').append(datahtml);
  
  $('select.purchaseproductid').change(function(){
    combination = $(this).attr('id').split("_");
    getpricebyproductid(combination[1],combination[2],'purchasepriceid',$(this).val());
  });
  $('select.offerproductid').change(function(){
    combination = $(this).attr('id').split("_");
    getpricebyproductid(combination[1],combination[2],'offerpriceid',$(this).val());
  });
  $(".selectpicker").selectpicker('refresh');
}

function removecombination(combinationid){

  if($('.combination').length!=1 && ACTION==1 && $('#combinationtableid'+combinationid).val()!=null){
      var removecombinationtableid = $('#removecombinationtableid').val();
      $('#removecombinationtableid').val(removecombinationtableid+','+$('#combinationtableid'+combinationid).val());
  }
  $('#combination_'+combinationid+'_div').remove();

  $(".add_combination_btn:last").show();
  if ($(".remove_combination_btn:visible").length == 1) {
      $(".remove_combination_btn:first").hide();
  }
}

function resetdata(){

    $("#offername_div").removeClass("has-error is-focused");
    $("#channel_div").removeClass("has-error is-focused");
    $("#member_div").removeClass("has-error is-focused");
  
    if(ACTION==0){
        $("#offername").val("");
        $("#startdate").val("");
        $("#enddate").val("");
        $('.cke_inner').css({"background-color":"#FFF","border":"1px solid #D2D2D2"});
        CKEDITOR.instances['description'].setData("");
        $("#channelid").val('');
        getmembers(0);
        
        $('.countimages').not(':first').remove();
        imagecount = $('.countimages:first').attr('id').match(/(\d+)/g);
        $('#Filetext'+imagecount+',#priority'+imagecount).val("");
        $('#image'+imagecount+'_div,#priority'+imagecount+'_div').removeClass("has-error is-focused");
        $('#imagepreview'+imagecount).attr('src',DEFAULT_IMAGE_PREVIEW);
        $("#channelid option").removeAttr('disabled');
        $("#memberid").prop('disabled', false);

        $(".add_image_btn:last").show();
        if ($(".remove_image_btn:visible").length == 1) {
            $(".remove_image_btn:first").hide();
        }
        
        $(".selectpicker").selectpicker('refresh');
        $('#yes').prop("checked", true);
    }
    $('html, body').animate({scrollTop:0},'slow');
}

function getmembers(channelid){
  $('#memberid')
      .find('option')
      .remove()
      .end()
      .append('')
      .val('whatever')
  ;
  $('#memberid').selectpicker('refresh');

  if(channelid!=0 && channelid!=null){
    var uurl = SITE_URL+"member/get-multiple-channel-members";
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {channelid:channelid},
      dataType: 'json',
      async: false,
      success: function(response){

        for(var i = 0; i < response.length; i++) {
          
          if(ACTION==1){
            if(memberidarr!=null || memberidarr!=''){
             
              memberidarr = memberidarr.toString().split(',');
             
              if(memberidarr.includes(response[i]['id'])){
                $('#memberid').append($('<option>', { 
                  value: response[i]['id'],
                  selected: "selected",
                  text : ucwords(response[i]['name'])
                }));
              }else{
                $('#memberid').append($('<option>', { 
                  value: response[i]['id'],
                  text : ucwords(response[i]['name'])
                }));
              }
            }
          }else{
            $('#memberid').append($('<option>', { 
              value: response[i]['id'],
              text : ucwords(response[i]['name'])
            }));
          }
        }
      },
      error: function(xhr) {
        //alert(xhr.responseText);
      },
    });
    $('#memberid').selectpicker('refresh');
  }
}

function getpricebyproductid(combinationid,rowid,element,productid){
  var uurl = SITE_URL+"product/getVariantByProductIdForAdmin";
  //var productid = $('#purchaseproductid_'+combinationid+'_'+rowid).val();
  
  if(element=='offerpriceid'){
    $('#'+element+'_'+combinationid+'_'+rowid)
            .find('option')
            .remove()
            .end()
            .append('<option value="0">Select Variant</option>')
            .val('0');
  }else{
    $('#'+element+'_'+combinationid+'_'+rowid)
            .find('option')
            .remove()
            .val('');
  }
  

  $('#'+element+'_'+combinationid+'_'+rowid).selectpicker('refresh');
  if(productid!=0){
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {productid:String(productid)},
      dataType: 'json',
      async: false,
      success: function(response){

        for(var i = 0; i < response.length; i++) {
            $('#'+element+'_'+combinationid+'_'+rowid).append($('<option>', { 
                value: response[i]['id'],
                text : response[i]['variantname']
            }));
        }  
        //$('#priceid'+divid).val(priceid);
        if(response.length == 1){
            $('#'+element+'_'+combinationid+'_'+rowid).val(response[0]['id']);
        }
        $('#'+element+'_'+combinationid+'_'+rowid).selectpicker('refresh');

      },
      error: function(xhr) {
      //alert(xhr.responseText);
      },
    });
  }
        
}


function checkvalidation(){

    var offername = $("#offername").val().trim();
    var channelid = $("#channelid").val();
    var memberid = $("#memberid").val();
    var description = CKEDITOR.instances['description'].getData();
    description = encodeURIComponent(description);
    CKEDITOR.instances['description'].updateElement();
    var offertype = $("input[name='offertype']:checked").val();  
    var type = $("input[name='type']:checked").val();
    var targetvalue = rewardvalue = rewardtype = "";
    
    var isvalidoffername = isvalidmemberid = isvalidimage = isvalidpriority = isvalidcombination = isvalidminpurchaseamount = isvalidgiftproducts = 1;
    
    PNotify.removeAll();
    if(offername=='') {
        $("#offername_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter offer name !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidoffername = 0;
    } else {
      if(offername.length <= 3){
        $("#offername_div").addClass("has-error is-focused");
        new PNotify({title: 'Offer name require minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidoffername = 0;
      }else{
        $("#offername_div").removeClass("has-error is-focused");
      }
      
    }
    if(channelid!=null && channelid!=0 && memberid==null) {
        $("#member_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select members !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmemberid = 0;
    } else {
        $("#member_div").removeClass("has-error is-focused");
    }
    var c=1;
    var firstid = $('.countimages:first').attr('id').match(/\d+/);
    $('.countimages').each(function(){
        var id = $(this).attr('id').match(/\d+/);
        
        if($("#Filetext"+id).val() != "" || $("#priority"+id).val() != "" || parseInt(id)==parseInt(firstid)){

            if($("#Filetext"+id).val() == ""){
                $("#image"+id+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(c)+' image !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidimage = 0;
            }else {
                $("#image"+id+"_div").removeClass("has-error is-focused");
            }
            if($("#priority"+id).val() == ''){
                $("#priority"+id+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please enter '+(c)+' priority !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidpriority = 0;
            }else {
                $("#priority"+id+"_div").removeClass("has-error is-focused");
            }
        } else{
            $("#image"+id+"_div").removeClass("has-error is-focused");
            $("#priority"+id+"_div").removeClass("has-error is-focused");
        }
        c++;
    });

    var isvalidtargetvalue = isvalidrewardvalue = isvalidtargetvalidation = 1;
    if(type==4){
        targetvalue = $("#targetvalue").val(); 
        rewardvalue = $("#rewardvalue").val(); 
        rewardtype = $("input[name='rewardtype']:checked").val();  
        var isvalidpffertarget = 1;
        
        $('input[name="combinationid[]"]').each(function(){
            combinationid = this.value;
            var offerproductid = $("select[name='offerproductid["+combinationid+"][]']").map(function(){return $(this).val();}).get();
            var offerpriceid = $("select[name='offerpriceid["+combinationid+"][]']").map(function(){return $(this).val();}).get();
            var offerqty = $("input[name='offerqty["+combinationid+"][]']").map(function(){return $(this).val();}).get();
            var discountvalue = $("input[name='discountvalue["+combinationid+"][]']").map(function(){return $(this).val();}).get();
            
            for (var i = 0; i < offerproductid.length; i++) {
                if(offerproductid[i] > 0 && offerpriceid[i] > 0 && offerqty[i] > 0 && discountvalue[i] > 0){
                    isvalidpffertarget = 0;
                }
            }
        });

        if(targetvalue=="" || targetvalue==0) {
            $("#targetvalue_div").addClass("has-error is-focused");
            new PNotify({title: 'Please enter target value !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidtargetvalue = 0;
        } else {
            $("#targetvalue_div").removeClass("has-error is-focused");
        }
        if(isvalidpffertarget==1 && (rewardvalue=="" || rewardvalue==0)) {
            $("#rewardvalue_div").addClass("has-error is-focused");
            new PNotify({title: 'Please enter reward value !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidrewardvalue = 0;
        } else {
            $("#rewardvalue_div").removeClass("has-error is-focused");
        }
    }
    
    $('input[name="combinationid[]"]').each(function(){
        combinationid = this.value;
        var purchaseproductid = $("select[name='purchaseproductid["+combinationid+"][]']").map(function(){return $(this).val();}).get();
        var purchaseproductidarray = $("select[name='purchaseproductid["+combinationid+"][]']").map(function(){return $(this).attr('id');}).get();
        var purchaseqty = $("input[name='purchaseqty["+combinationid+"][]']").map(function(){return $(this).val();}).get();
        var purchaseqtyarray = $("input[name='purchaseqty["+combinationid+"][]']").map(function(){return $(this).attr('id');}).get();
        
        var offerproductid = $("select[name='offerproductid["+combinationid+"][]']").map(function(){return $(this).val();}).get();
        var offerproductidarray = $("select[name='offerproductid["+combinationid+"][]']").map(function(){return $(this).attr('id');}).get();
        var offerpriceid = $("select[name='offerpriceid["+combinationid+"][]']").map(function(){return $(this).val();}).get();
        var offerpriceidarray = $("select[name='offerpriceid["+combinationid+"][]']").map(function(){return $(this).attr('id');}).get();
        var offerqty = $("input[name='offerqty["+combinationid+"][]']").map(function(){return $(this).val();}).get();
        var offerqtyarray = $("input[name='offerqty["+combinationid+"][]']").map(function(){return $(this).attr('id');}).get();
        var discountvalue = $("input[name='discountvalue["+combinationid+"][]']").map(function(){return $(this).val();}).get();
        var discountvaluearray = $("input[name='discountvalue["+combinationid+"][]']").map(function(){return $(this).attr('id');}).get();
        if(offertype==0){
            
            var minpurchaseamount = $("#minpurchaseamount"+combinationid).val();
            if(minpurchaseamount=='' || minpurchaseamount==0) {
                $("#minpurchaseamount"+combinationid+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please enter minimum purchase amount !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidminpurchaseamount = 0;
            } else {
                $("#minpurchaseamount"+combinationid+"_div").removeClass("has-error is-focused");
            }
        }
        if(type!=1 && type!=4){
            for (var i = 0; i < purchaseproductid.length; i++) {
                var purchasepriceid = $("select[name='purchasepriceid["+combinationid+"]["+i+"][]']").map(function(){return $(this).val();}).get();
                var purchasepriceidarray = $("select[name='purchasepriceid["+combinationid+"]["+i+"][]']").map(function(){return $(this).attr('id');}).get();
                
                if(purchaseproductid[i]==0) {
                    $("#"+purchaseproductidarray[i]+"_div").addClass("has-error is-focused");
                    new PNotify({title: 'Please select '+(i+1)+' purchase product on '+combinationid+' combination !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    isvalidcombination = 0;
                } else {
                    $("#"+purchaseproductidarray[i]+"_div").removeClass("has-error is-focused");
                }
                if(purchasepriceid[0]==null) {
                    $("#"+purchasepriceidarray[0]+"_div").addClass("has-error is-focused");
                    new PNotify({title: 'Please select '+(i+1)+' purchase variant on '+combinationid+' combination !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    isvalidcombination = 0;
                } else {
                    $("#"+purchasepriceidarray[0]+"_div").removeClass("has-error is-focused");
                }
                if((purchaseqty[i]==0 || purchaseqty[i]=='') && offertype==1) {
                    $("#"+purchaseqtyarray[i]+"_div").addClass("has-error is-focused");
                    new PNotify({title: 'Please select '+(i+1)+' purchase qty on '+combinationid+' combination !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    isvalidcombination = 0;
                } else {
                    $("#"+purchaseqtyarray[i]+"_div").removeClass("has-error is-focused");
                }
            }
        }
        
        if(type==2){
            for (var i = 0; i < offerproductid.length; i++) {
                if(offerproductid[i]==0) {
                    $("#"+offerproductidarray[i]+"_div").addClass("has-error is-focused");
                    new PNotify({title: 'Please select '+(i+1)+' gift product on '+combinationid+' combination !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    isvalidcombination = 0;
                    isvalidtargetvalidation = 0;
                } else {
                    $("#"+offerproductidarray[i]+"_div").removeClass("has-error is-focused");
                }
                if(offerpriceid[i]==0) {
                    $("#"+offerpriceidarray[i]+"_div").addClass("has-error is-focused");
                    new PNotify({title: 'Please select '+(i+1)+' gift product variant on '+combinationid+' combination !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    isvalidcombination = 0;
                } else {
                    $("#"+offerpriceidarray[i]+"_div").removeClass("has-error is-focused");
                }
                if(offerqty[i]==0 || offerqty[i]=='') {
                    $("#"+offerqtyarray[i]+"_div").addClass("has-error is-focused");
                    new PNotify({title: 'Please select '+(i+1)+' gift qty on '+combinationid+' combination !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    isvalidcombination = 0;
                } else {
                    $("#"+offerqtyarray[i]+"_div").removeClass("has-error is-focused");
                }
                if(discountvalue[i]==0 || discountvalue[i]=='') {
                    $("#"+discountvaluearray[i]+"_div").addClass("has-error is-focused");
                    new PNotify({title: 'Please select '+(i+1)+' gift discount on '+combinationid+' combination !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    isvalidcombination = 0;
                } else {
                    $("#"+discountvaluearray[i]+"_div").removeClass("has-error is-focused");
                }
            }
        }
        if(type==4){
            for (var i = 0; i < purchaseproductid.length; i++) {
                var purchasepriceid = $("select[name='purchasepriceid["+combinationid+"]["+i+"][]']").map(function(){return $(this).val();}).get();
                var purchasepriceidarray = $("select[name='purchasepriceid["+combinationid+"]["+i+"][]']").map(function(){return $(this).attr('id');}).get();
                if(purchaseproductid[i] > 0 || purchasepriceid[0] != null){
                    
                    if(purchaseproductid[i]==0) {
                        $("#"+purchaseproductidarray[i]+"_div").addClass("has-error is-focused");
                        new PNotify({title: 'Please select '+(i+1)+' purchase product on '+combinationid+' combination !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        isvalidcombination = 0;
                    } else {
                        $("#"+purchaseproductidarray[i]+"_div").removeClass("has-error is-focused");
                    }
                    if(purchasepriceid[0]==null) {
                        $("#"+purchasepriceidarray[0]+"_div").addClass("has-error is-focused");
                        new PNotify({title: 'Please select '+(i+1)+' purchase variant on '+combinationid+' combination !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        isvalidcombination = 0;
                    } else {
                        $("#"+purchasepriceidarray[0]+"_div").removeClass("has-error is-focused");
                    }
                    if((purchaseqty[i]==0 || purchaseqty[i]=='') && offertype==1) {
                        $("#"+purchaseqtyarray[i]+"_div").addClass("has-error is-focused");
                        new PNotify({title: 'Please select '+(i+1)+' purchase qty on '+combinationid+' combination !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        isvalidcombination = 0;
                    } else {
                        $("#"+purchaseqtyarray[i]+"_div").removeClass("has-error is-focused");
                    }
                }else{
                    $("#"+purchaseproductidarray[i]+"_div").removeClass("has-error is-focused");
                    $("#"+purchasepriceidarray[0]+"_div").removeClass("has-error is-focused");
                    $("#"+purchaseqtyarray[i]+"_div").removeClass("has-error is-focused");
                }
            }
            for (var i = 0; i < offerproductid.length; i++) {
               
                if((offerproductid[i] > 0 || offerpriceid[i] > 0 || offerqty[i] > 0 || discountvalue[i] > 0)){
                    if(offerproductid[i]==0) {
                        $("#"+offerproductidarray[i]+"_div").addClass("has-error is-focused");
                        new PNotify({title: 'Please select '+(i+1)+' gift product on '+combinationid+' combination !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        isvalidcombination = 0;
                    } else {
                        $("#"+offerproductidarray[i]+"_div").removeClass("has-error is-focused");
                    }
                    if(offerpriceid[i]==0) {
                        $("#"+offerpriceidarray[i]+"_div").addClass("has-error is-focused");
                        new PNotify({title: 'Please select '+(i+1)+' gift product variant on '+combinationid+' combination !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        isvalidcombination = 0;
                    } else {
                        $("#"+offerpriceidarray[i]+"_div").removeClass("has-error is-focused");
                    }
                    if(offerqty[i]==0 || offerqty[i]=='') {
                        $("#"+offerqtyarray[i]+"_div").addClass("has-error is-focused");
                        new PNotify({title: 'Please select '+(i+1)+' gift qty on '+combinationid+' combination !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        isvalidcombination = 0;
                    } else {
                        $("#"+offerqtyarray[i]+"_div").removeClass("has-error is-focused");
                    }
                    if(discountvalue[i]==0 || discountvalue[i]=='') {
                        $("#"+discountvaluearray[i]+"_div").addClass("has-error is-focused");
                        new PNotify({title: 'Please select '+(i+1)+' gift discount on '+combinationid+' combination !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        isvalidcombination = 0;
                    } else {
                        $("#"+discountvaluearray[i]+"_div").removeClass("has-error is-focused");
                    }
                }else{
                    $("#"+offerproductidarray[i]+"_div").removeClass("has-error is-focused");
                    $("#"+offerpriceidarray[i]+"_div").removeClass("has-error is-focused");
                    $("#"+offerqtyarray[i]+"_div").removeClass("has-error is-focused");
                    $("#"+discountvaluearray[i]+"_div").removeClass("has-error is-focused");
                }
            }
        }
    });
    
    //var isvalidoffername = isvalidmemberid = isvalidimage = isvalidpriority = 1;
    if(isvalidoffername == 1 && isvalidmemberid == 1 && isvalidimage == 1 && isvalidpriority == 1 && isvalidcombination == 1 && isvalidminpurchaseamount == 1 && isvalidgiftproducts == 1 && isvalidtargetvalue == 1 && isvalidrewardvalue == 1){
                            
        var formData = new FormData($('#offerform')[0]);
        if(ACTION == 0){    
          var uurl = SITE_URL+"offer/add-offer";
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
                var obj = JSON.parse(response);
                if(obj['error']==1){
                    new PNotify({title: 'Offer successfully added !',styling: 'fontawesome',delay: '3000',type: 'success'});
                    setTimeout(function() { location.reload(); }, 1500);
                }else if(obj['error'] == 2){
                    new PNotify({title: 'Offer already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(obj['error'] == 3){
                    new PNotify({title: obj['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
                }else{
                    new PNotify({title: 'Offer not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
            var uurl = SITE_URL+"offer/update-offer";
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
                var obj = JSON.parse(response);
                if(obj['error']==1){
                    new PNotify({title: 'Offer successfully Updated !',styling: 'fontawesome',delay: '3000',type: 'success'});
                    setTimeout(function() { window.location=SITE_URL+"offer"; }, 1500);
                }else if(obj['error'] == 2){
                    new PNotify({title: 'Offer already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(obj['error'] == 3){
                    new PNotify({title: obj['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
                }else{
                    new PNotify({title: 'Offer not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
/* 
function getgiftpricebyproductid(rowid,productid){
    var uurl = SITE_URL+"product/getVariantByProductIdForAdmin";
    //var productid = $('#purchaseproductid_'+combinationid+'_'+rowid).val();
    
    var element = $('#giftpriceid'+rowid);
    element.find('option').remove().val('');
    element.selectpicker('refresh');
    if(productid!=0){
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {productid:String(productid)},
        dataType: 'json',
        async: false,
        success: function(response){
  
          for(var i = 0; i < response.length; i++) {
            element.append($('<option>', { 
                  value: response[i]['id'],
                  text : response[i]['memberprice']
              }));
          }  
          //$('#priceid'+divid).val(priceid);
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
      });
    }
    element.selectpicker('refresh');  
}
function addnewgiftproduct(){

    var offertype = $("input[name='offertype']:checked").val();  
    var rowcount = parseInt($(".countgiftproduct:last").attr("id").match(/\d+/))+1;
  
    var datahtml = '<div class="col-md-6 p-n countgiftproduct" id="countgiftproduct'+rowcount+'"> \
                    <div class="col-md-4 pr-sm pl-sm"> \
                      <div class="form-group ml-n mr-n" id="giftproductid'+rowcount+'_div"> \
                          <select id="giftproductid'+rowcount+'" name="giftproductid[]" class="selectpicker form-control giftproductid" data-live-search="true" data-select-on-tab="true" data-size="8" data-actions-box="true"> \
                            <option value="0">Select Product</option> \
                            '+OFFER_PRODUCT_DATA+' \
                          </select> \
                      </div> \
                    </div> \
                    <div class="col-md-4 pr-sm pl-sm"> \
                      <div class="form-group ml-n mr-n" id="giftpriceid'+rowcount+'_div"> \
                          <select id="giftpriceid'+rowcount+'" name="giftpriceid[]" class="selectpicker form-control giftpriceid" multiple data-live-search="true" data-select-on-tab="true" data-size="8" data-actions-box="true" title="Select Variant"> \
                          </select> \
                      </div> \
                    </div> \
                    <div class="col-md-2 pr-sm pl-sm"> \
                      <div class="form-group ml-n mr-n" id="giftqty'+rowcount+'_div"> \
                          <input type="text" class="form-control giftqty" name="giftqty[]" id="giftqty'+rowcount+'" onkeypress="return isNumber(event)" maxlength="4"> \
                      </div> \
                    </div> \
                    <div class="col-md-2 pl-sm" style="margin-top: 20px !important;"> \
                        <button type="button" class="btn btn-default btn-raised remove_gift_btn m-n" onclick="removegiftproduct('+rowcount+')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button> \
                        <button type="button" class="btn btn-default btn-raised add_gift_btn m-n" onclick="addnewgiftproduct()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button> \
                    </div> \
                </div>';
  
  $(".remove_gift_btn:first").show();
  $(".add_gift_btn:last").hide();
  $("#countgiftproduct"+(rowcount-1)).after(datahtml);
  $(".selectpicker").selectpicker('refresh');

  if($('.countgiftproduct').length > 1){
    $("#giftheader2").show(); 
  }else{
    $("#giftheader2").hide(); 
  }
}
function removegiftproduct(rowid){

  if($('.countgiftproduct').length!=1 && ACTION==1 && $('#offerproducttableid'+rowid).val()!=null){
      var removegiftproductid = $('#removegiftproductid').val();
      $('#removegiftproductid').val(removegiftproductid+','+$('#offerproducttableid'+rowid).val());
  }
  $('#countgiftproduct'+rowid).remove();

  $(".add_gift_btn:last").show();
  if ($(".remove_gift_btn:visible").length == 1) {
      $(".remove_gift_btn:first").hide();
  }
  if($('.countgiftproduct').length > 1){
    $("#giftheader2").show(); 
  }else{
    $("#giftheader2").hide(); 
  }
} */