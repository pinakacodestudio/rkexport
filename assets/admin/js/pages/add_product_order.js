var VENDOR_DATA = "";
$(document).ready(function() {   
    $('#orderdate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        todayBtn:"linked",
    });

    $(".qty").TouchSpin({
        initval: 0,
        min: 1,
        max: 9999,
        // decimals: 3,
        verticalbuttons: true,
        verticalupclass: 'glyphicon glyphicon-plus',
        verticaldownclass: 'glyphicon glyphicon-minus'
    });
});

/****VENDOR CHANGE EVENT****/
$(document).on('change','#productid', function (e) {
    getvendor();
    var discount = $("#productid option:selected").attr("data-discount");
    if(discount > 0){
        $(".discount").val(discount);
    }
});

/****VENDOR CHANGE EVENT****/
$(document).on('change','select.vendorid', function (e) {
    var divid = $(this).attr("div-id");
    getbillingaddress(divid);
    getChannelSettingByVendor(divid);
    getproductprice(divid);
    
    var vendorid = $("#vendorid"+divid).val();
    var uniqueproduct = (vendorid!="" && vendorid!=0)?vendorid+"_0_0":"";
    $("#uniqueproduct"+divid).val(uniqueproduct);
    $("select.priceid").change();
});
/****ADDRESS CHANGE EVENT****/
$(document).on('change','select.billingaddressid', function (e) {
    var divid = $(this).attr("div-id");
   
    var vendorid = $("#vendorid"+divid).val();
    var billingaddressid = $("#billingaddressid"+divid).val();
    var priceid = ($("#priceid"+divid).val()!="" && $("#priceid"+divid+" option:selected").text()!="Select Variant")?$("#priceid"+divid).val():"0";
    var uniqueproduct = vendorid+"_"+billingaddressid+"_"+priceid;
    $("#uniqueproduct"+divid).val(uniqueproduct);
});
/****PRODUCT PRICE CHANGE EVENT****/
$(document).on('change', 'select.priceid', function() { 
    var divid = $(this).attr("div-id");
    $("#producttaxamount"+divid).val('');
    
    $("#qty"+divid).val('1');
    $("#amount"+divid+",#discountinrs"+divid).val('');

    var tax = parseFloat($("#producttax"+divid).val());
    
    var actualprice = parseFloat($("#priceid"+divid+" option:selected").text().trim());
    var productrate = parseFloat(actualprice - ((actualprice * tax /(100+parseFloat(tax))))).toFixed(2);
    $('#productrate'+divid).val(productrate);
    if(this.value!=""){
        $('#actualprice'+divid).val(parseFloat(actualprice).toFixed(2));
    }else{
        $('#actualprice'+divid).val("");
    }

    if(this.value!=""){
        var referencetype = parseFloat($("#priceid"+divid+" option:selected").attr("data-referencetype"));
        $('#referencetype'+divid).val(parseInt(referencetype));

        var multipleprices = JSON.parse($("#priceid"+divid+" option:selected").attr("data-multipleprices"));
        // console.log(multipleprices)
        var length = multipleprices.length;
        for(var i = 0; i < multipleprices.length; i++) {
            
            var txt = "";

            // if(parseInt(pricetype)==1){
            //     txt = CURRENCY_CODE+multipleprices[i]['price']+" "+multipleprices[i]['quantity']+(parseInt(quantitytype)==0?"+":"")+" Qty"
            // }else{
                txt = multipleprices[i]['price'];
            // }

            $('#combopriceid'+divid)
                .find('option')
                .remove()
                .end()
                .append('<option value="">Price</option>')
                .val('')
            ;
            
            $('#combopriceid'+divid).append($('<option>', { 
                value: multipleprices[i]['id'],
                text : txt,
                "data-price" : multipleprices[i]['price'],
                "data-quantity" : multipleprices[i]['quantity'],
                "data-discount" : multipleprices[i]['discount']
            }));

        }
        if(length==1){
            $('#combopriceid'+divid).val(multipleprices[0]['id']).selectpicker('refresh');
            $('#combopriceid'+divid).change();
        }
    }

    $('#originalprice'+divid).val(parseFloat(actualprice).toFixed(2));

    calculatediscount(divid);
    changeproductamount(divid);
    
    var uniqueproduct = $("#uniqueproduct"+divid).val();
    if(uniqueproduct!=""){
        elementarr = uniqueproduct.split("_");
        var element1 = (this.value!="")?this.value:0;
        $("#uniqueproduct"+divid).val(elementarr[0]+"_"+elementarr[1]+"_"+element1);
    }
    $('.selectpicker').selectpicker('refresh');
    // alert();
});
/****ACTUAL PRICE CHANGE EVENT****/
$(document).on('keyup', '.actualprice', function() {
    var divid = $(this).attr("div-id");
    
    var actualprice = (this.value!="")?parseFloat(this.value):0;

    var tax = parseFloat($("#producttax"+divid).val());
    var productrate = parseFloat(actualprice - ((actualprice * tax /(100+parseFloat(tax))))).toFixed(2);
    $('#productrate'+divid).val(parseFloat(productrate).toFixed(2));
    $('#originalprice'+divid).val(parseFloat(actualprice).toFixed(2));
    calculatediscount(divid);
    changeproductamount(divid);
});
/****PRODUCT QUANTITY CHANGE EVENT****/
$(document).on('change', '.qty', function() {
    var divid = $(this).attr("div-id");
    
    // START - Minimum or Maximum Order Quantity Settings
    var qty = parseInt($(this).val());
    var minimumorderqty = $("#priceid"+divid+" option:selected").attr('data-minimumorderqty');
    var maximumorderqty = $("#priceid"+divid+" option:selected").attr('data-maximumorderqty');
    PNotify.removeAll();
    if(parseInt(minimumorderqty) > 0 && parseInt(qty) < parseInt(minimumorderqty)){
        new PNotify({title: 'Minimum '+parseInt(minimumorderqty)+' quantity required for this product !',styling: 'fontawesome',delay: '3000',type: 'error'});
        $(this).val(parseInt(minimumorderqty));
    }
    if(parseInt(maximumorderqty) > 0 && parseInt(qty) > parseInt(maximumorderqty)){
        new PNotify({title: 'Maximum '+parseInt(maximumorderqty)+' quantity allow for this product !',styling: 'fontawesome',delay: '3000',type: 'error'});
        $(this).val(parseInt(maximumorderqty));
    }
    // END - Minimum or Maximum Order Quantity Settings
    calculatediscount(divid);
    changeproductamount(divid);
});

$(document).on('keyup', '.discount', function() { 
    var divid = $(this).attr("div-id");
    var price = $("#actualprice"+divid).val();
    var qty = $("#qty"+divid).val();
    if(divid!=undefined){
        dicountvalue = $("#discount"+divid).val();
        if(parseFloat(dicountvalue)>=100){
            $("#discount"+divid).val("100");
        }
        calculatediscount(divid);
        changeproductamount(divid);
    }
});
$(document).on('keyup', '.discountinrs', function(e) { 
    
    var divid = $(this).attr("div-id");
    if(divid!=undefined){
        calculatediscountamount(divid,$(this).val());
        changeproductamount(divid);
    }
});
$(document).on('keyup', '.tax', function() { 
    var divid = $(this).attr("div-id");
    if(divid!=undefined){
        taxvalue = $("#tax"+divid).val();
        if(parseFloat(taxvalue)>=100){
            $("#tax"+divid).val("100");
        }
        changeproductamount(divid);
    }
});

function calculatediscount(elementid){
    var discountpercentage = $("#discount"+elementid).val(); 
    discountpercentage = (discountpercentage!='' && discountpercentage!=0)?discountpercentage:0;
    var priceid = $("#priceid"+elementid).val();
    var price = $("#actualprice"+elementid).val();
    price = (price!='' && price!=0)?price:0;
    var qty = $("#qty"+elementid).val();
    qty = (qty!='' && qty!=0)?qty:0;
    
    if(price!=0 && qty!=0 && priceid!="" && discountpercentage!=0){
        var discountamount = (parseFloat(price)*parseFloat(discountpercentage)/100) * parseInt(qty);
        
        $("#discountinrs"+elementid).val(parseFloat(discountamount).toFixed(2));
    }else{
        $("#discountinrs"+elementid).val('');
    }
}
function calculatediscountamount(elementid,discountamount){
    var discountpercentage = 0;
    var price = $("#actualprice"+elementid).val();
    price = (price!=0)?price:0;
    var qty = $("#qty"+elementid).val();
    qty = (qty!=0)?qty:0;
    
    if(discountamount!=undefined && discountamount!=''){
        grossamount = parseFloat(price)*parseFloat(qty);
        if(parseFloat(discountamount)>parseFloat(grossamount)){
            discountamount = parseFloat(grossamount);
            $("#discountinrs"+elementid).val(parseFloat(discountamount).toFixed(2));
        }
        
        if(parseFloat(grossamount)!=0){
            var discountpercentage = ((parseFloat(discountamount)*100) / parseFloat(grossamount));
        }
        
        $("#discount"+elementid).val(parseFloat(discountpercentage).toFixed(2)); 
    }else{
        $("#discountinrs"+elementid).val('');
        $("#discount"+elementid).val(""); 
    }
}
function changeproductamount(divid){
   
    if(divid!=undefined){
        var price = $("#priceid"+divid+" option:selected").text().trim();
        var actualprice = $("#actualprice"+divid).val();
        var qty = $("#qty"+divid).val();
        var discount = $("#discount"+divid).val();
        var tax = parseFloat($("#producttax"+divid).val()).toFixed(2);
        var edittax = $("#tax"+divid).val();
        edittax = (edittax!="")?parseFloat(edittax).toFixed(2):0;
        actualprice = (actualprice!="")?parseFloat(actualprice).toFixed(2):0;
        
        if(actualprice!=0 && price!="0" && qty!="0" && price!="" && qty!="" && price!="Select Variant"){
            
            totalamount = productamount = discountamount = 0;
            if(PRODUCT_DISCOUNT == 1 && discount!='0' && discount!=""){
                discountamount = (parseFloat(actualprice)*(parseFloat(discount)/100));
            }
            price = parseFloat(parseFloat(actualprice) - parseFloat(discountamount)).toFixed(2);
            
            var productrate = parseFloat(price);
            if(GST_PRICE == 1){
                var taxAmount = (parseFloat(price) * parseFloat(edittax) / 100);
                price = parseFloat(parseFloat(price) + (parseFloat(price) * parseFloat(edittax) / 100)).toFixed(2);
            }else{
                var taxAmount = (parseFloat(price) * parseFloat(edittax) / (100+parseFloat(edittax)));
                productrate = parseFloat(productrate) - parseFloat(taxAmount);
            }
            productamount = parseFloat(price);
            totalamount = parseFloat(productamount) * parseFloat(qty);
            producttaxamount = parseFloat(taxAmount) * parseFloat(qty);
            
            $("#productrate"+divid).val(parseFloat(productrate).toFixed(2));
            $("#amount"+divid).val(parseFloat(totalamount).toFixed(2));
            $('#producttaxamount'+divid).val(parseFloat(producttaxamount).toFixed(2));

            var grossamount = 0;
            $(".amounttprice").each(function( index ) {
                if($(this).val()!=""){
                    grossamount += parseFloat($(this).val());
                }
            });
        }else{
            $("#amount"+divid).val(0);
            $("#discountpercentage").html('0'); 
            $("#discountamount").html('0.00'); 
        }
    }
}
function getbillingaddress(divid){
    $('#billingaddressid'+divid)
        .find('option')
        .remove()
        .end()
        .append('<option value="0">Select Address</option>')
        .val('0')
    ;
    
    var vendorid = $("#vendorid"+divid).val();
    var BillingAddressID = $("#vendorid"+divid+" option:selected").attr("data-billingid");
    
    if(vendorid!=0){
      var uurl = SITE_URL+"purchase-order/getBillingAddressByVendorId";
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {vendorid:String(vendorid)},
        //dataType: 'json',
        async: false,
        success: function(response){
            var obj = JSON.parse(response);
            if (!jQuery.isEmptyObject(obj['billingaddress'])) {
                for(var i = 0; i < obj['billingaddress'].length; i++) {
  
                    $('#billingaddressid'+divid).append($('<option>', { 
                        value: obj['billingaddress'][i]['id'],
                        text : ucwords(obj['billingaddress'][i]['address'])
                    }));
                }
                $('#billingaddressid'+divid).val(BillingAddressID);
            }
            
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
      });
    }
    $('#billingaddressid'+divid).selectpicker('refresh');
}
function getvendor(divid=''){

    if(divid==''){
        UIVENDOR = [];
        UIPRICE = [];
        $('select.vendorid').each(function() {
            var divid = $(this).attr("div-id");
            UIVENDOR.push($('#vendorid'+divid).val());
            UIPRICE.push($('#priceid'+divid).val());
        });
        
        $('select.vendorid')
            .find('option')
            .remove()
            .end()
            .append('<option value="">Select Vendor</option>')
            .val('0')
        ;
        $('select.vendorid').selectpicker('refresh');
        $('select.billingaddressid')
            .find('option')
            .remove()
            .end()
            .append('<option value="0">Select Address</option>')
            .val('0')
        ;
        $('select.billingaddressid').selectpicker('refresh');
        var element = $('select.vendorid');
    }else{
        $('#vendorid'+divid)
            .find('option')
            .remove()
            .end()
            .append('<option value="">Select Vendor</option>')
            .val('0')
        ;
        $('#billingaddressid'+divid)
            .find('option')
            .remove()
            .end()
            .append('<option value="0">Select Address</option>')
            .val('0')
        ;
        $('#priceid'+divid)
            .find('option')
            .remove()
            .end()
            .append('<option value="">Select Variant</option>')
            .val('0')
        ;
        
        $('#vendorid'+divid).selectpicker('refresh');
        $('#billingaddressid'+divid).selectpicker('refresh');
        $('#priceid'+divid).selectpicker('refresh');

        var element = $('#vendorid'+divid);
    }
    var productid = $("#productid").val();
    
    if(productid!='' && productid!=0){
      var uurl = SITE_URL+"vendor/getVendorByProductId";
      VENDOR_DATA = "";
      
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {productid:productid},
        dataType: 'json',
        async: false,
        success: function(response){
  
            var NewVendor = [];
            for(var i = 0; i < response.length; i++) {
  
                element.append($('<option>', { 
                    value: response[i]['id'],
                    text : response[i]['name']
                }));
  
                VENDOR_DATA += "<option value="+response[i]['id']+">"+response[i]['name']+"</option>";

                NewVendor.push(response[i]['id']);
            }
            var VENDOR_ARR = [];
            if(NewVendor.length > 0 && ACTION==0 && divid==''){

                $('select.vendorid').each(function(index) {
                    var divid = $(this).attr("div-id");
                   
                    if(NewVendor.includes(UIVENDOR[index])){
                        // If product id is match then execute
                        $('#vendorid'+divid).val(UIVENDOR[index]);
                        $('#vendorid'+divid).selectpicker('refresh');
                        
                        if(!VENDOR_ARR.includes(UIVENDOR[index])){
                            VENDOR_ARR.push(UIVENDOR[index]);
                        }
                    }else{
                        // If product id is not match then reset all product data
                        $('#priceid'+divid)
                            .find('option')
                            .remove()
                            .end()
                            .append('<option value="">Select Variant</option>')
                            .val('0')
                        ;
                        $('#priceid'+divid).selectpicker('refresh');
                        $("#actualprice"+divid).val('');
                        $("#billingaddressid"+divid).val('0').selectpicker('refresh');
                        $("#qty"+divid).val('1');
                        $("#discount"+divid+",#discountinrs"+divid+",#amount"+divid+",#tax"+divid+",#ordertax"+divid+",#uniqueproduct"+divid).val('');
                    }
                    changeproductamount(divid);
                    $("#discount"+divid+",#discountinrs"+divid).val('');
                });
            }
            if(VENDOR_ARR.length > 0){
                getpricebyvendorid(VENDOR_ARR); 
            }
            /* if(oldvendorid[divid-1]!=0){
                $('#vendorid'+divid).val(oldvendorid[divid-1]);
            } */
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
      });
    }else{
        $('select.priceid')
            .find('option')
            .remove()
            .end()
            .append('<option value="">Select Variant</option>')
            .val('0')
        ;
        $('select.priceid').selectpicker('refresh');
        $(".qty").val('1');
        $(".discount,.discountinrs,.amounttprice,.actualprice").val('');
    }
    if(divid==''){
        $('select.vendorid').selectpicker('refresh');
    }else{
        $('#vendorid'+divid).selectpicker('refresh');
        $('#priceid'+divid).selectpicker('refresh');
    }
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
  
    var vendorid = $("#vendorid"+divid).val();
    var productid = $('#productid').val();
    
    if(vendorid!=0 && productid != 0){
      var uurl = SITE_URL+"purchase-order/getVariantByProductId";
      
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {productid:String(productid),vendorid:vendorid},
        dataType: 'json',
        async: false,
        success: function(response){
            
            if(response.length > 0){

                console.log(response)
                for(var i = 0; i < response.length; i++) {

                        var multiplepricedata = [];
                        if (!$.isEmptyObject(response[i]['multipleprices'])) {
                            multiplepricedata = response[i]['multipleprices'];
                        }

                    $('#priceid'+divid).append($('<option>', { 
                        value: response[i]['id'],
                        text : response[i]['memberprice'],
                        "data-id" : response[i]['priceid'],
                        "data-multipleprices" : JSON.stringify(multiplepricedata),
                        "data-minimumorderqty" : response[i]['minimumorderqty'],
                        "data-maximumorderqty" : response[i]['maximumorderqty'],
                        "data-referencetype" : response[i]['referencetype'],
                    }));
                    /* if(response[i]['universal']!='undefined' && response[i]['universal']==1){
                        $('#priceid'+divid).val(response[i]['id']);
                    } */
                    $('#producttax'+divid).val(response[i]['tax']);
                }
            
                $('#tax'+divid).val(response[0]['tax']);
            }
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
      });
    }
    $('#priceid'+divid).selectpicker('refresh');
}
function getpricebyvendorid(vendorids){
   
    if(vendorids.length > 0){
        for(var i = 0; i < vendorids.length; i++) {

            var vendorid = vendorids[i];
            if(productid!=''){
                var uurl = SITE_URL+"purchase-order/getVariantByProductId";
                var productid = $("#productid").val();
                
                $.ajax({
                    url: uurl,
                    type: 'POST',
                    data: {productid:String(productid),vendorid:vendorid},
                    dataType: 'json',
                    async: false,
                    success: function(response){
                
                        $('select.vendorid').each(function(index) {
                            var divid = $(this).attr("div-id");
                            var vid = $('#vendorid'+divid).val();
                            var priceid = $('#priceid'+divid).val();

                            if(vid == vendorid){

                                $('#priceid'+divid)
                                    .find('option')
                                    .remove()
                                    .end()
                                    .append('<option value="">Select Variant</option>')
                                    .val('0')
                                ;
                                $('#priceid'+divid).selectpicker('refresh');
                                
                                for(var i = 0; i < response.length; i++) {

                                    var multiplepricedata = [];
                                    if (!$.isEmptyObject(response[i]['variantdata'])) {
                                        multiplepricedata = response[i]['variantdata'];
                                    }
                                    console.log(multiplepricedata)

                                    $('#priceid'+divid).append($('<option>', { 
                                        value: response[i]['id'],
                                        text : response[i]['memberprice'],
                                        "data-id" : response[i]['priceid'],
                                        "data-multipleprices" : JSON.stringify(multiplepricedata),
                                        "data-minimumorderqty" : response[i]['minimumorderqty'],
                                        "data-maximumorderqty" : response[i]['maximumorderqty']
                                    }));
                                    $('#producttax'+divid).val(response[i]['tax']);
                                }  
                                $('#priceid'+divid).val(priceid);
                                $('#priceid'+divid).selectpicker('refresh');
                                var actualprice = parseFloat($("#priceid"+divid+" option:selected").text().trim());
                                var discount = parseFloat($("#productid option:selected").attr("data-discount"));
                                if(this.value!=""){
                                    $('#actualprice'+divid).val(parseFloat(actualprice).toFixed(2));
                                }else{
                                    $('#actualprice'+divid).val("");
                                }
                                if(parseFloat(discount) > 0){
                                    $("#discount"+divid).val(parseFloat(discount).toFixed(2));
                                    calculatediscount(divid);
                                }else{
                                    $("#discount"+divid+",#discountinrs"+divid).val('');
                                }
                                $("#tax"+divid).val($("#ordertax"+divid).val());
                                changeproductamount(divid);
                            }else{
                                // $('#applyoldprice'+divid+'_div').remove();
                            }
                        });
                    },
                    error: function(xhr) {
                    //alert(xhr.responseText);
                    },
                });
            }
        }     
    }
}
function getChannelSettingByVendor(divid){

    var vendorid = $('#vendorid'+divid).val();

    if(vendorid!='' && vendorid!=0){
        var uurl = SITE_URL+"vendor/getChannelSettingsByVendor";
        $.ajax({
          url: uurl,
          type: 'POST',
          data: {vendorid:String(vendorid)},
          dataType: 'json',
          async: false,
          success: function(response){
            if(response.edittaxrate==1 && EDITTAXRATE_SYSTEM==1){
                EDITTAXRATE_CHANNEL = response.edittaxrate;
                $("#tax"+divid).prop("readonly",false);
            }else{
                EDITTAXRATE_CHANNEL = 0;
                $("#tax"+divid).val('').prop("readonly",true);
            }
          },
          error: function(xhr) {
          //alert(xhr.responseText);
          },
        });
    }else{
        $("#tax"+divid).val('').prop("readonly",true);
    }
}
function addnewproduct(){

    if(PRODUCT_DISCOUNT==0){
        discount = "display:none;";
    }else{ 
        discount = "display:block;"; 
    }
    var readonly = "readonly";
    if(EDITTAXRATE_CHANNEL==1 && EDITTAXRATE_SYSTEM==1){
        readonly = "";
    }

    divcount = parseInt($(".amounttprice:last").attr("div-id"))+1;
    
    producthtml = '<tr class="countproducts" id="orderproductdiv'+divcount+'">\
        <td>\
            <input type="hidden" name="producttax[]" id="producttax'+divcount+'">\
            <input type="hidden" name="productrate[]" id="productrate'+divcount+'">\
            <input type="hidden" name="originalprice[]" id="originalprice'+divcount+'">\
            <input type="hidden" name="referencetype[]" id="referencetype'+divcount+'">\
            <input type="hidden" name="uniqueproduct[]" id="uniqueproduct'+divcount+'">\
            <div class="form-group" id="vendor'+divcount+'_div">\
                <div class="col-sm-12">\
                    <select id="vendorid'+divcount+'" name="vendorid[]" data-width="150px" class="selectpicker form-control vendorid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="'+divcount+'">\
                        <option value="0">Select Vendor</option>\
                        '+VENDOR_DATA+'\
                    </select>\
                </div>\
            </div>\
        </td>\
        <td>\
            <div class="form-group" id="billingaddress'+divcount+'_div">\
                <div class="col-sm-12">\
                    <select id="billingaddressid'+divcount+'" data-width="150px" name="billingaddressid[]" class="selectpicker form-control billingaddressid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="'+divcount+'">\
                        <option value="0">Select Address</option>\
                    </select>\
                </div>\
            </div>\
        </td>\
        <td>\
            <div class="form-group" id="price'+divcount+'_div">\
                <div class="col-md-12">\
                    <select id="priceid'+divcount+'" name="priceid[]" data-width="150px" class="selectpicker form-control priceid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="'+divcount+'">\
                        <option value="">Select Variant</option>\
                    </select>\
                </div>\
            </div>\
        </td>\
        <td>\
            <div class="form-group" id="comboprice'+divcount+'_div">\
                <div class="col-sm-12">\
                    <select id="combopriceid'+divcount+'" name="combopriceid[]" class="selectpicker form-control combopriceid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="'+divcount+'">\
                        <option value="">Price</option>\
                    </select>\
                </div>\
            </div>\
            <div class="form-group" id="actualprice'+divcount+'_div">\
                <div class="col-sm-12">\
                    <input type="text" class="form-control actualprice text-right" id="actualprice'+divcount+'" name="actualprice[]" value="" onkeypress="return decimal_number_validation(event, this.value, 8)" style="display: block;" div-id="'+divcount+'">\
                </div>\
            </div>\
        </td>\
        <td>\
            <div class="form-group" id="qty'+divcount+'_div">\
                <div class="col-md-12">\
                    <input type="text" class="form-control qty" id="qty'+divcount+'" name="qty[]" value="" maxlength="4" onkeypress="return isNumber(event);" style="display: block;" div-id="'+divcount+'">\
                </div>\
            </div>\
        </td>\
        <td style="'+discount+'">\
            <div class="form-group" id="discount'+divcount+'_div">\
                <div class="col-md-12">\
                    <label for="discount'+divcount+'" class="control-label">Dis. (%)</label>\
                    <input type="text" class="form-control discount" id="discount'+divcount+'" name="discount[]" value="" div-id="'+divcount+'" onkeypress="return decimal_number_validation(event, this.value)">\
                    <input type="hidden" value="" id="orderdiscount'+divcount+'">\
                </div>\
            </div>\
            <div class="form-group" id="discountinrs'+divcount+'_div">\
                <div class="col-md-12">\
                    <label for="discountinrs'+divcount+'" class="control-label">Dis. ('+CURRENCY_CODE+')</label>\
                    <input type="text" class="form-control discountinrs" id="discountinrs'+divcount+'" name="discountinrs[]" value="" div-id="'+divcount+'" onkeypress="return decimal_number_validation(event, this.value)">\
                </div>\
            </div>\
        </td>\
        <td>\
            <div class="form-group" id="tax'+divcount+'_div">\
                <div class="col-md-12">\
                    <input type="text" class="form-control text-right tax" id="tax'+divcount+'" name="tax[]" value="" div-id="'+divcount+'" '+readonly+'>	\
                    <input type="hidden" value="" id="ordertax'+divcount+'">\
                </div>\
            </div>\
        </td>\
        <td>\
            <div class="form-group" id="amount'+divcount+'_div">\
                <div class="col-md-12">\
                    <input type="text" class="form-control amounttprice" id="amount'+divcount+'" name="amount[]" value="" div-id="'+divcount+'" readonly>\
                    <input type="hidden" class="producttaxamount" id="producttaxamount'+divcount+'" name="producttaxamount[]" value="" div-id="'+divcount+'">\
                </div>\
            </div>\
        </td>\
        <td>\
            <div class="form-group pt-sm">\
                <div class="col-md-12 pr-n">\
                    <button type = "button" class = "btn btn-default btn-raised add_remove_btn_product" onclick = "removeproduct('+divcount+')" style="padding: 5px 10px;"> <i class = "fa fa-minus"></i></button> \
                    <button type="button" class="btn btn-default btn-raised add_remove_btn" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button> \
                </div>\
            </div>\
        </td>\
    </div>';

    $(".add_remove_btn_product:first").show();
    $(".add_remove_btn:last").hide();
    $("#orderproducttable tbody").append(producthtml);

    $("#qty"+divcount).TouchSpin({
        initval: 0,
        min: 1,
        max: 9999,
        // decimals: 3,
        verticalbuttons: true,
        verticalupclass: 'glyphicon glyphicon-plus',
        verticaldownclass: 'glyphicon glyphicon-minus'
    });
    $(".selectpicker").selectpicker("refresh");
}
function removeproduct(divid){

    $("#orderproductdiv"+divid).remove();

    $(".add_remove_btn:last").show();
    if ($(".add_remove_btn_product:visible").length == 1) {
        $(".add_remove_btn_product:first").hide();
    }
    changeproductamount(divid);
    
}
function resetdata(){  
  
    $("#product_div").removeClass("has-error is-focused");
    $("#orderdate_div").removeClass("has-error is-focused");
  
    if(ACTION==0){
        $('#productid').val('0');
        $('#remarks').val('');
        
        $(".countproducts:not(:first)").remove();
        var divid = parseInt($(".countproducts:first").attr("id").match(/\d+/));
        
        $('#vendorid'+divid+',#billingaddressid'+divid+',#priceid'+divid).val("0");
        $('#qty'+divid).val("1");
        $("#actualprice"+divid+"#discount"+divid+"#discountinrs"+divid+"#amount"+divid).val("");

        $("#vendor"+divid+"_div,#price"+divid+"_div,#actualprice"+divid+"_div,#billingaddress"+divid+"_div,#qty"+divid+"_div,#amount"+divid+"_div").removeClass("has-error is-focused");
        $('#vendorid'+divid+',#productid').change();
        
        $('.add_remove_btn:first').show();
        $('.add_remove_btn_product').hide();
       
        $('.selectpicker').selectpicker('refresh');
    }

    $('html, body').animate({scrollTop:0},'slow');
}

function checkvalidation(addtype="0"){
    
    var productid = $("#productid").val();
    var orderdate = $("#orderdate").val();

    var isvalidproductid = isvalidorderdate = isvalidvendorid = isvalidbillingaddressid = isvalidpriceid = isvalidqty = isvalidamount = isvaliduniqueproducts = isvalidactualprice = 1;
    PNotify.removeAll();
    
    if(productid == 0){
        $("#product_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select product !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidproductid = 0;
    }else{
        $("#product_div").removeClass("has-error is-focused");
    }
    if(orderdate == ''){
        $("#orderdate_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select order date !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidorderdate = 0;
    }else {
        $("#orderdate_div").removeClass("has-error is-focused");
    }

    var c=1;
    var firstrowid = $('.countproducts:first').attr('id').match(/\d+/);
    $('.countproducts').each(function(){
        var id = $(this).attr('id').match(/\d+/);
       
        if($("#vendorid"+id).val() > 0 || $("#billingaddressid"+id).val() > 0 || $("#priceid"+id).val() > 0 || $("#actualprice"+id).val() != "" || $("#qty"+id).val() == 0 || $("#amount"+id).val() > 0 || parseInt(id)==parseInt(firstrowid)){
            if($("#vendorid"+id).val() == 0){
                $("#vendor"+id+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(c)+' vendor !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidvendorid = 0;
            }else {
                $("#vendor"+id+"_div").removeClass("has-error is-focused");
            }
            if($("#billingaddressid"+id).val() == 0){
                $("#billingaddress"+id+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(c)+' billing address !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidbillingaddressid = 0;
            }else {
                $("#billingaddress"+id+"_div").removeClass("has-error is-focused");
            }
            if($("#priceid"+id).val() == "" || $("#priceid"+id+" option:selected").text() == "Select Variant"){
                $("#price"+id+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(c)+' price !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidpriceid = 0;
            }else {
                $("#price"+id+"_div").removeClass("has-error is-focused");
            }
            if($("#actualprice"+id).val() == ""){
                $("#actualprice"+id+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please enter '+(c)+' actual price !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidactualprice = 0;
            }else {
                $("#actualprice"+id+"_div").removeClass("has-error is-focused");
            }
            if($("#qty"+id).val() == 0){
                $("#qty"+id+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(c)+' quantity !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidqty = 0;
            }else if(parseInt($("#qty"+id).val()) > 0 && $("#priceid"+id).val() != ""){
                
                var minimumorderqty = $("#priceid"+id+" option:selected").attr('data-minimumorderqty');
                var maximumorderqty = $("#priceid"+id+" option:selected").attr('data-maximumorderqty');
                
                if(parseInt(minimumorderqty) > 0 && parseInt($("#qty"+id).val()) < parseInt(minimumorderqty)){
                    new PNotify({title: 'Minimum '+parseInt(minimumorderqty)+' quantity required for '+(c)+' product !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    $("#qty"+id+"_div").addClass("has-error is-focused");
                    isvalidqty = 0;
                }
                if(parseInt(maximumorderqty) > 0 && parseInt($("#qty"+id).val()) > parseInt(maximumorderqty)){
                    new PNotify({title: 'Maximum '+parseInt(maximumorderqty)+' quantity allow for '+(c)+' product !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    $("#qty"+id+"_div").addClass("has-error is-focused");
                    isvalidqty = 0;
                }
            }else {
                $("#qty"+id+"_div").removeClass("has-error is-focused");
            }
            if($("#amount"+id).val() == 0){
                $("#amount"+id+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please enter '+(c)+' amount !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidamount = 0;
            }else {
                $("#amount"+id+"_div").removeClass("has-error is-focused");
            }
        } else{
            $("#vendor"+id+"_div").removeClass("has-error is-focused");
            $("#billingaddress"+id+"_div").removeClass("has-error is-focused");
            $("#price"+id+"_div").removeClass("has-error is-focused");
            $("#actualprice"+id+"_div").removeClass("has-error is-focused");
            $("#qty"+id+"_div").removeClass("has-error is-focused");
            $("#amount"+id+"_div").removeClass("has-error is-focused");
        }
        c++;
    });

    var products = $('input[name="uniqueproduct[]"]');
    var values = [];
    for(j=0;j<products.length;j++) {
        var uniqueproducts = products[j];
        var id = uniqueproducts.id.match(/\d+/);
        
        if(uniqueproducts.value!="" && $("#vendorid"+id[0]).val()!=0 && $("#billingaddressid"+id[0]).val()!=0 && ($("#priceid"+id[0]).val()!="" && $("#priceid"+id[0]+" option:selected").text()!="Select Variant")){
            if(values.indexOf(uniqueproducts.value)>-1) {
                $("#vendor"+id[0]+"_div,#billingaddress"+id[0]+"_div,#price"+id[0]+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(j+1)+' is different vendor, address & price !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvaliduniqueproducts = 0;
            }
            else{ 
                values.push(uniqueproducts.value);
                if($("#vendorid"+id[0]).val()!=0 && $("#billingaddressid"+id[0]).val()!=0 && ($("#priceid"+id[0]).val()!="" && $("#priceid"+id[0]+" option:selected").text()!="Select Variant")){
                    $("#vendor"+id[0]+"_div,#billingaddress"+id[0]+"_div,#price"+id[0]+"_div").removeClass("has-error is-focused");
                }
            }
        }
    }

    if(isvalidproductid ==1 && isvalidorderdate == 1 && isvalidvendorid == 1 && isvalidbillingaddressid ==1 && isvalidpriceid == 1 && isvalidactualprice==1 && isvalidqty == 1 && isvalidamount == 1 && isvaliduniqueproducts == 1){
        
        var formData = new FormData($('#productorderform')[0]);
        if(ACTION==0){
            var uurl = SITE_URL+"purchase-order/add-product-order";
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
                        new PNotify({title: "Product order successfully added !",styling: 'fontawesome',delay: '3000',type: 'success'});
                        if(addtype==1){
                            resetdata();
                        }else{
                            setTimeout(function() { window.location=SITE_URL+"purchase-order"; }, 1500);
                        }
                    }else if(data['error']==2){
                        new PNotify({title: "Product order already exist !",styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else if(data['error']==-1){
                        new PNotify({title: "Quantity greater than stock quantity!",styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else if(data['error']==0){
                        new PNotify({title: 'Product order not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else if(data['error']==-2){
                        new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
                        $("#file"+data['id']+"_div").addClass("has-error is-focused");
                    }else if(data['error']==-4){
                        new PNotify({title:data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
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
            var uurl = SITE_URL+"purchase-order/update-product-order";
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
                    new PNotify({title: "Product order successfully updated.",styling: 'fontawesome',delay: '3000',type: 'success'});
                    setTimeout(function() { window.location=SITE_URL+"purchase-order"; }, 1500);
                }else if(data['error']==2){
                    new PNotify({title: "Product order already exist !",styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(data['error']==-1){
                    new PNotify({title: "Quantity greater than stock quantity !",styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(data['error']==0){
                    new PNotify({title: 'Product order not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(data['error']==-2){
                    new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
                    $("#file"+data['id']+"_div").addClass("has-error is-focused");
                }else if(data['error']==-4){
                    new PNotify({title:data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
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