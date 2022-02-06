
var latestvoucherno = "";
$(document).ready(function() { 
    $('.yesno input[type="checkbox"]').bootstrapToggle({
        on: 'Incr.',
        off: 'Decr.',
        onstyle: 'primary',
        offstyle: 'danger'
    });  
    $('#voucherdate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        todayBtn:"linked",
    });
    $(".add_btn").hide();
    $(".add_btn:last").show();

    $("#qty1").TouchSpin(touchspinoptions);
});
 /****PRODUCT CHANGE EVENT****/
 $(document).on('change', 'select.productid', function() { 
    var divid = $(this).attr("div-id");
    $('#price'+divid).val("");
    var productid = (this.value!=0)?this.value:0;
    var uniqueproduct = (productid!="" && productid!=0)?productid+"_0_0.00":"";
    $("#uniqueproduct"+divid).val(uniqueproduct);
    
    getproductprice(divid);
    getveriyant(productid);
    $("#qty"+divid).val(1);
    calculatetotalprice(divid);
});
/****PRODUCT VARIANT CHANGE EVENT****/
$(document).on('change', 'select.priceid', function() { 
    var divid = $(this).attr("div-id");
    $("#qty"+divid).val('1');
    var productid = $("#productid"+divid).val();
    var priceid = (this.value!="")?this.value:0;

    if(this.value!=""){
        var price = parseFloat($("#priceid"+divid+" option:selected").attr("data-price"));
        $('#price'+divid).val(parseFloat(price).toFixed(2));
        $("#uniqueproduct"+divid).val(productid+"_"+priceid+"_"+parseFloat(price).toFixed(2));
    }else{
        $('#price'+divid).val("");
        $("#uniqueproduct"+divid).val(productid+"_"+priceid+"_0.00");
    }
    calculatetotalprice(divid);
});
$(document).on('keyup', '.price', function() { 
    var divid = $(this).attr("div-id");
   
    calculatetotalprice(divid);
    
    var productid = $("#productid"+divid).val();
    var priceid = $("#priceid"+divid).val();
    var price = (this.value!="")?this.value:'0.00';
    if(this.value!=""){
        $("#uniqueproduct"+divid).val(productid+"_"+priceid+"_"+parseFloat(price).toFixed(2));
    }else{
        $("#uniqueproduct"+divid).val(productid+"_"+priceid+"_0.00");
    }
});
$(document).on('change', '.qty', function() { 
    var divid = $(this).attr("div-id");
   
    calculatetotalprice(divid);
});
function calculatetotalprice(divid){
    var qty = $("#qty"+divid).val();
    var price = $('#price'+divid).val();
    var totalprice = 0;

    if(qty!="" && price!=""){
        totalprice = parseFloat(price) * parseFloat(qty);
    }
    $("#totalprice"+divid).val(parseFloat(totalprice).toFixed(2));

}
function getveriyant(id){
  
}
function getproductprice(divid){
    
    $('#priceid'+divid)
        .find('option')
        .remove()
        .end()
        .append('<option value="">Select Variant</option>')
        .val('0')
    ;
    $('#priceid'+divid).selectpicker('refresh');
  
    var productid = $("#productid"+divid).val();
    
    if(productid!=0){
      var uurl = SITE_URL+"product/getVariantByProductIdForAdmin";
      
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {productid:String(productid)},
        dataType: 'json',
        async: false,
        success: function(response){
  
            for(var i = 0; i < response.length; i++) {
                $('#priceid'+divid).append($('<option>', { 
                    value: response[i]['id'],
                    text : response[i]['variantname'],
                    "data-price" : response[i]['price']
                }));
            }
            if(response.length == 1){
                $('#priceid'+divid).val(response[0]['id']).selectpicker('refresh').change();
            }
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
      });
    }
    $('#priceid'+divid).selectpicker('refresh');
}
function addnewproduct(){

    var divcount = parseInt($(".countproducts:last").attr("id").match(/\d+/))+1;

    producthtml = '<div class="row countproducts" id="countproducts'+divcount+'">\
        <input type="hidden" name="uniqueproduct[]" id="uniqueproduct'+divcount+'">\
        <input type="hidden" name="productrow[]" value="'+divcount+'">\
        <div class="col-sm-3 pl-xs pr-xs">\
            <div class="form-group" id="product'+divcount+'_div">\
                <div class="col-sm-12">\
                    <select id="productid'+divcount+'" name="productid[]" class="selectpicker form-control productid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="'+divcount+'">\
                        <option value="0">Select Product</option>\
                        '+PRODUCT_DATA+'\
                    </select>\
                </div>\
            </div>\
        </div>\
        <div class="col-sm-2 pl-xs pr-xs">\
            <div class="form-group" id="price'+divcount+'_div">\
                <div class="col-md-12">\
                    <select id="priceid'+divcount+'" name="priceid[]" class="selectpicker form-control priceid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="'+divcount+'">\
                        <option value="">Select Variant</option>\
                    </select>\
                </div>\
            </div>\
        </div>\
        <div class="col-sm-1 pl-xs pr-xs">\
            <div class="form-group" id="qty'+divcount+'_div">\
                <div class="col-md-12">\
                    <input type="text" class="form-control qty" id="qty'+divcount+'" name="qty[]" value="" onkeypress="'+(MANAGE_DECIMAL_QTY==1?"return decimal_number_validation(event, this.value,8);":"return isNumber(event);")+'" style="display: block;" div-id="'+divcount+'">\
                </div>\
            </div>\
        </div>\
        <div class="col-sm-1 pl-xs pr-xs">\
            <div class="form-group" id="productprice'+divcount+'_div">\
                <div class="col-md-12">\
                    <input id="price'+divcount+'" name="price[]" class="form-control price text-right" onkeypress="return decimal_number_validation(event, this.value, 8)" div-id="'+divcount+'">\
                </div>\
            </div>\
        </div>\
        <div class="col-sm-1 pl-xs pr-xs">\
            <div class="form-group">\
                <div class="col-md-12">\
                    <input id="totalprice'+divcount+'" name="totalprice[]" class="form-control totalprice text-right" onkeypress="return decimal_number_validation(event, this.value, 8)" div-id="'+divcount+'" readonly>\
                </div>\
            </div>\
        </div>\
        <div class="col-sm-1 pl-xs pr-xs">\
            <div class="form-group" id="type'+divcount+'_div">\
                <div class="col-md-12">\
                    <div class="yesno mt-xs">\
                        <input type="checkbox" name="type'+divcount+'" value="1" checked>\
                    </div>\
                </div>\
            </div>\
        </div>\
        <div class="col-sm-2 pl-xs pr-xs">\
            <div class="form-group" id="narration'+divcount+'_div">\
                <div class="col-md-12">\
                    <select id="narrationid'+divcount+'" name="narrationid[]" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="'+divcount+'">\
                        <option value="0">Select Narration</option>\
                        '+NARRATION_DATA+'\
                    </select>\
                </div>\
            </div>\
        </div>\
        <div class="col-md-1 form-group m-n p-xs mt-xs">\
            <button type = "button" class = "btn btn-default btn-raised remove_btn" onclick="removeproduct('+divcount+')" style="padding: 5px 10px;"> <i class = "fa fa-minus"></i></button> \
            <button type="button" class="btn btn-default btn-raised add_btn" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button> \
        </div>\
    </div>';

    $(".remove_btn:first").show();
    $(".add_btn:last").hide();
    $("#countproducts"+(divcount-1)).after(producthtml);
    
    $("#qty"+divcount).TouchSpin(touchspinoptions);
    $('.yesno input[type="checkbox"]').bootstrapToggle({
        on: 'Incr.',
        off: 'Decr.',
        onstyle: 'primary',
        offstyle: 'danger'
    });
    $(".selectpicker").selectpicker("refresh");
}
function removeproduct(divid){

    $("#countproducts"+divid).remove();

    $(".add_btn:last").show();
    if ($(".remove_btn:visible").length == 1) {
        $(".remove_btn:first").hide();
    }
}
function resetdata() {
    $("#voucherno_div").removeClass("has-error is-focused");
    $("#voucherdate_div").removeClass("has-error is-focused");
    
    if(ACTION==0){
        if(latestvoucherno!=""){
            $("#voucherno").val(latestvoucherno);
        }
        $('#narrationid').val('0');
        $(".countproducts:not(:first)").remove();
        var divid = parseInt($(".countproducts:first").attr("id").match(/\d+/));

        $('#productid'+divid+',#priceid'+divid).val("0");
        $('#qty'+divid).val("1");
        $('#price'+divid+',#totalprice'+divid).val("");
        getproductprice(divid);

        $('.add_btn:first').show();
        $('.remove_btn').hide();

        $(".selectpicker").selectpicker('refresh');
    }
    $('html, body').animate({scrollTop:0},'slow');  
}
function checkvalidation(addtype=0) {
   
    var voucherno = $('#voucherno').val();
    var voucherdate = $('#voucherdate').val();
    
    var isvalidvoucherno = isvalidvoucherdate = isvalidproductid = isvalidpriceid = isvalidprice = isvalidqty = isvaliduniqueproducts = 1;
   
    PNotify.removeAll();
    if(voucherno=="") {
        $("#voucherno_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter request number !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidvoucherno = 0;
    }else {
        $("#voucherno_div").removeClass("has-error is-focused");
    }
    if(voucherdate=="") {
        $("#voucherdate_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select request date !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidvoucherdate = 0;
    } else {
        $("#voucherdate_div").removeClass("has-error is-focused");
    }   
    var c=1;
    var firstproductid = $('.countproducts:first').attr('id').match(/\d+/);
    $('.countproducts').each(function(){
        var id = $(this).attr('id').match(/\d+/);
       
        if($("#productid"+id).val() > 0 || $("#priceid"+id).val() > 0 || $("#price"+id).val() != "" || $("#qty"+id).val() == 0 || parseInt(id)==parseInt(firstproductid)){
            if($("#productid"+id).val() == 0){
                $("#product"+id+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(c)+' product !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidproductid = 0;
            }else {
                $("#product"+id+"_div").removeClass("has-error is-focused");
            }

            // if($("#priceid"+id).val() == "" || $("#priceid"+id+" option:selected").text() == "Select Variant"){
            //     $("#price"+id+"_div").addClass("has-error is-focused");
            //     new PNotify({title: 'Please select '+(c)+' price !',styling: 'fontawesome',delay: '3000',type: 'error'});
            //     isvalidpriceid = 0;
            // }else {
            //     $("#price"+id+"_div").removeClass("has-error is-focused");
            // }

            if($("#qty"+id).val() == 0){
                $("#qty"+id+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(c)+' quantity !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidqty = 0;
            }else {
                $("#qty"+id+"_div").removeClass("has-error is-focused");
            }
            
            if($("#price"+id).val() == ""){
                $("#productprice"+id+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please enter '+(c)+' price !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidprice = 0;
            }else {
                $("#productprice"+id+"_div").removeClass("has-error is-focused");
            }

          
        } else{
            $("#product"+id+"_div").removeClass("has-error is-focused");
            $("#price"+id+"_div").removeClass("has-error is-focused");
            $("#productprice"+id+"_div").removeClass("has-error is-focused");
            $("#qty"+id+"_div").removeClass("has-error is-focused");
        }
        c++;
    });

    var products = $('input[name="uniqueproduct[]"]');
    var values = [];
    for(j=0;j<products.length;j++) {
        var uniqueproducts = products[j];
        var id = uniqueproducts.id.match(/\d+/);
        
        if(uniqueproducts.value!="" && ($("#priceid"+id[0]).val()!="" && $("#priceid"+id[0]+" option:selected").text()!="Select Variant" && $("#price"+id[0]).val()!="")){
            if(values.indexOf(uniqueproducts.value)>-1) {
                $("#price"+id[0]+"_div,#product"+id[0]+"_div,#productprice"+id[0]+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(j+1)+' is different product variant & price !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvaliduniqueproducts = 0;
            }
            else{ 
                values.push(uniqueproducts.value);
                if(($("#priceid"+id[0]).val()!="" && $("#priceid"+id[0]+" option:selected").text()!="Select Variant" && $("#price"+id[0]).val()!="")){
                    $("#price"+id[0]+"_div,#product"+id[0]+"_div,#productprice"+id[0]+"_div").removeClass("has-error is-focused");
                }
            }
        }
    } 
    if(isvalidvoucherno == 1 && isvalidvoucherdate == 1 && isvalidproductid == 1 && isvalidpriceid == 1 && isvalidprice == 1 && isvalidqty == 1 && isvaliduniqueproducts == 1){
        var formData = new FormData($('#stockgeneralvoucherform')[0]);
        if(ACTION == 0){ // INSERT
            var baseurl = SITE_URL + 'stock-general-voucher/stock-general-voucher-add';
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
                        new PNotify({title: 'Stock general voucher successfully added.',styling: 'fontawesome',delay: '3000',type: 'success'});
                        if(addtype==1){
                            resetdata();
                            latestvoucherno = obj['voucherno'];
                            $("#voucherno").val(obj['voucherno']);
                        }else{
                            setTimeout(function() { window.location = SITE_URL + "stock-general-voucher";}, 500);
                        }
                    }else if(obj['error']==2){
                        new PNotify({title: 'Voucher number already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else{
                        new PNotify({title: 'Stock general voucher not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
            var baseurl = SITE_URL + 'stock-general-voucher/update-stock-general-voucher';
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
                        new PNotify({title: 'Stock general voucher successfully updated.',styling: 'fontawesome',delay: '3000',type: 'success'});
                    
                        setTimeout(function() { window.location = SITE_URL + "stock-general-voucher";}, 500);
                    }else if(response==2){
                        new PNotify({title: 'Voucher number already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else{
                        new PNotify({title: 'Stock general voucher not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
