$(document).ready(function(){
    if(ACTION==1){
        if(orderid!=0){
            getOrderDetails();
        }else{
            $(".add_btn").hide();
            $(".add_btn:last").show();

            $(".quantity").TouchSpin(touchspinoptions);
        }
        calculateorderquantity();
    }else{
        if(orderid!=0){
            getOrderDetails();
            calculateorderquantity();
        }
        // createproductsection("productwise");
    }

});
/****ORDER CHANGE EVENT****/
$(document).on('change', '#orderid', function (e) {
    if(this.value!=0){
        getOrderDetails();
    }else{
        createproductsection("productwise");
    }
});
/****ORDER CHANGE EVENT****/
$(document).on('change', 'select.productid', function (e) {
    var rowid = $(this).attr("id").match(/\d+/);
    getproductvariant(rowid);
});
function getOrderDetails(){
    var orderid = $("#orderid").val();
    var productionplanid = $("#productionplanid").val();
    $("#orderdetailpanel,#rawmaterialpanel").html("");
    var uurl = SITE_URL+"production-plan/getOrderProductDetails";
    $.ajax({
        url: uurl,
        type: 'POST',
        data: {orderid:String(orderid),productionplanid:String(productionplanid)},
        dataType: 'json',
        async: false,
        beforeSend: function(){
            $('.mask').show();
            $('#loader').show();
        },
        success: function(response){
            var HTML = '';
            var PRODUCT_HTML = '';
            if(response.length > 0){
                for(var i=0; i<response.length; i++){
                    PRODUCT_HTML += '<div class="col-md-12 p-n">\
                                        <input type="hidden" id="productionplandetailid'+(i+1)+'" name="productionplandetailid[]" value="'+response[i]['productionplandetailid']+'">\
                                        <input type="hidden" id="orderproductid'+(i+1)+'" name="orderproductid[]" value="'+response[i]['id']+'">\
                                        <input type="hidden" id="productid'+(i+1)+'" name="productid[]" value="'+response[i]['productid']+'">\
                                        <input type="hidden" id="priceid'+(i+1)+'" name="priceid[]" value="'+response[i]['priceid']+'">\
                                        \
                                        <div class="col-md-4 pl-xs pr-xs">\
                                            <div class="form-group m-n" style="padding: 5px 0px;">\
                                                <label class="control-label">'+response[i]['productname']+'</label>\
                                            </div>\
                                        </div>\
                                        <div class="col-md-3 pl-xs pr-xs">\
                                            <div class="form-group m-n" style="padding: 5px 0px;">\
                                                <label class="control-label">'+(response[i]['variantname']!=""?response[i]['variantname']:"---")+'</label>\
                                            </div>\
                                        </div>\
                                        <div class="col-md-2 pl-xs pr-xs">\
                                            <div class="form-group m-n p-n" id="quantity'+(i+1)+'_div">\
                                                <input type="text" id="quantity'+(i+1)+'" class="form-control quantity text-right" name="quantity[]" value="'+response[i]['quantity']+'" onkeypress="'+(MANAGE_DECIMAL_QTY==1?"return decimal_number_validation(event, this.value,8);":"return isNumber(event);")+'">\
                                            </div>\
                                        </div>\
                                    </div>';
                }
                /* HTML = '<div class="panel panel-transparent">\
                            <div class="panel-heading p-n">\
                                <h2 style="font-weight:600;">Order Details</h2>\
                            </div>\
                            <div class="panel-body productvariantdiv p-sm mb-n" id="orderdetaildata">\
                                <div class="col-md-9 p-n">\
                                    \
                                    <table class="table mb-n">\
                                        <thead>\
                                            <tr>\
                                                <th width="40%">Product Name</th>\
                                                <th width="40%">Variant</th>\
                                                <th class="text-right" width="20%">Quantity</th>\
                                            </tr>\
                                        </thead>\
                                        <tbody>\
                                            '+PRODUCT_HTML+'\
                                        </tbody>\
                                    </table>\
                                    \
                                </div>\
                                <div class="col-md-12 text-center">\
                                    <a href="javascript:void(0)" class="btn btn-primary btn-raised" title="Calculate" onclick="calculateorderquantity()">Calculate</a>\
                                </div>\
                            </div>\
                        </div>'; */
            }
            createproductsection("orderwise");
            $("#productdata").html(PRODUCT_HTML);
            $(".quantity").TouchSpin(touchspinoptions);
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
function createproductsection(type="orderwise"){
    
    $("#orderdetailpanel,#rawmaterialpanel").html("");
    var PRODUCT_HTML = "";
    if(type=="productwise"){

        PRODUCT_HTML = '<div class="col-md-12 p-n countproducts" id="countproducts1">\
                            <input type="hidden" id="productionplandetailid1" name="productionplandetailid[]" value="">\
                            <div class="col-md-4 pl-xs pr-xs">\
                                <div class="form-group m-n p-n" id="productid1_div">\
                                    <select id="productid1" name="productid[]" class="selectpicker form-control productid" data-live-search="true" data-select-on-tab="true" data-size="5" data-width = "100%">\
                                        <option value="0">Select Product</option>\
                                        '+PRODUCTDATA+'\
                                    </select>\
                                </div>\
                            </div>\
                            <div class="col-md-3 pl-xs pr-xs">\
                                <div class="form-group m-n p-n" id="priceid1_div">\
                                    <select id="priceid1" name="priceid[]" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                        <option value="0">Select Variant</option>\
                                    </select>\
                                </div>\
                            </div>\
                            <div class="col-md-2 pl-xs pr-xs">\
                                <div class="form-group m-n p-n" id="quantity1_div">\
                                    <input type="text" id="quantity1" class="form-control quantity text-right" name="quantity[]" value="1" onkeypress="'+(MANAGE_DECIMAL_QTY==1?"return decimal_number_validation(event, this.value,8);":"return isNumber(event);")+'">\
                                </div>\
                            </div>\
                            <div class="col-md-2 pl-xs pr-xs pt-sm">\
                                <div class="form-group m-n">\
                                    <button type="button" class="btn btn-default btn-raised remove_btn" onclick="removeproduct(1)" style="padding: 5px 10px;display:none;"> <i class = "fa fa-minus"></i></button>\
                                    <button type="button" class="btn btn-default btn-raised add_btn" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>\
                                </div>\
                            </div>\
                        </div>';
    }

    var HTML = '<div class="panel panel-transparent">\
                    <div class="panel-heading p-n">\
                        <h2 style="font-weight:600;">Product Details</h2>\
                    </div>\
                    <div class="panel-body productvariantdiv p-sm mb-n" id="orderdetaildata">\
                        <div class="col-md-12 p-n">\
                            \
                            <div class="col-md-4 pl-xs pr-xs">\
                                <div class="form-group m-n p-n">\
                                    <label class="control-label"><b>Product Name</b></label>\
                                </div>\
                            </div>\
                            <div class="col-md-3 pl-xs pr-xs">\
                                <div class="form-group m-n p-n">\
                                    <label class="control-label"><b>Variant</b></label>\
                                </div>\
                            </div>\
                            <div class="col-md-2 pl-xs pr-xs">\
                                <div class="form-group m-n p-n">\
                                    <label class="control-label"><b>Quantity</b></label>\
                                </div>\
                            </div>\
                            \
                        </div>\
                        <div class="" id="productdata">\
                            '+PRODUCT_HTML+'\
                        </div>\
                        <div class="col-md-12 text-center">\
                            <hr>\
                            <a href="javascript:void(0)" class="btn btn-primary btn-raised" title="Calculate" onclick="calculateorderquantity()">Calculate</a>\
                        </div>\
                    </div>\
                </div>';

    $("#orderdetailpanel").html(HTML);

    $(".add_btn").hide();
    $(".add_btn:last").show();
    
    $("#quantity1").TouchSpin(touchspinoptions);
    $(".selectpicker").selectpicker("refresh");
}
function getproductvariant(rowid){
   
    $("#priceid"+rowid).find('option')
                    .remove()
                    .end()
                    .append('<option value="">Select Variant</option>')
                    .val('0')
                ;
    $("#priceid"+rowid).selectpicker('refresh');
    var productid = $("#productid"+rowid).val();
    
    if(productid != '0'){
      var uurl = SITE_URL+"product/getVariantByProductIdForAdmin";
      
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {productid:String(productid)},
        dataType: 'json',
        async: false,
        success: function(response){
            
            for(var i = 0; i < response.length; i++) {
                $("#priceid"+rowid).append($('<option>', { 
                    value: response[i]['id'],
                    text : response[i]['variantname']
                }));
            }
            if(response.length == 1){
                $("#priceid"+rowid).val(response[0]['id']);
            }
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
      });
    }
    $("#priceid"+rowid).selectpicker('refresh');
}
function addnewproduct(){

    var prcount = parseInt($(".countproducts:last").attr("id").match(/\d+/))+1;
    
    producthtml = '<div class="col-md-12 p-n countproducts" id="countproducts'+prcount+'">\
                    <div class="col-md-4 pl-xs pr-xs">\
                        <input type="hidden" id="productionplandetailid'+prcount+'" name="productionplandetailid[]" value="">\
                        <div class="form-group m-n p-n" id="productid'+prcount+'_div">\
                            <select id="productid'+prcount+'" name="productid[]" class="selectpicker form-control productid" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                <option value="0">Select Product</option>\
                                '+PRODUCTDATA+'\
                            </select>\
                        </div>\
                    </div>\
                    <div class="col-md-3 pl-xs pr-xs">\
                        <div class="form-group m-n p-n" id="priceid'+prcount+'_div">\
                            <select id="priceid'+prcount+'" name="priceid[]" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                <option value="0">Select Variant</option>\
                            </select>\
                        </div>\
                    </div>\
                    <div class="col-md-2 pl-xs pr-xs">\
                        <div class="form-group m-n p-n" id="quantity'+prcount+'_div">\
                            <input type="text" id="quantity'+prcount+'" class="form-control quantity text-right" name="quantity[]" value="1" onkeypress="'+(MANAGE_DECIMAL_QTY==1?"return decimal_number_validation(event, this.value,8);":"return isNumber(event);")+'">\
                        </div>\
                    </div>\
                    <div class="col-md-2 pl-xs pr-xs pt-sm">\
                        <div class="form-group m-n">\
                            <button type="button" class="btn btn-default btn-raised remove_btn" onclick="removeproduct('+prcount+')" style="padding: 5px 10px;"> <i class = "fa fa-minus"></i></button>\
                            <button type="button" class="btn btn-default btn-raised add_btn" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>\
                        </div>\
                    </div>\
                </div>';

    $(".remove_btn:first").show();
    $(".add_btn:last").hide();
    $("#countproducts"+(prcount-1)).after(producthtml);
    
    $("#quantity"+prcount).TouchSpin(touchspinoptions);
    $(".selectpicker").selectpicker("refresh");
}
function removeproduct(divid){

    $("#countproducts"+divid).remove();

    $(".add_btn:last").show();
    if ($(".remove_btn:visible").length == 1) {
        $(".remove_btn:first").hide();
    }
}
function calculateorderquantity(){
    
    if($("#orderid").val()==0 || $("#orderid").val()==null){
        var productid = $("select[name='productid[]']").map(function(){return $(this).val();}).get();
        var elementid = $("select[name='productid[]']").map(function(){return $(this).attr("id");}).get();
        var priceid = $("select[name='priceid[]']").map(function(){return $(this).val();}).get();
    }
    var quantity = $("input[name='quantity[]']").map(function(){return $(this).val();}).get();
    var productionplanid = $("#productionplanid").val();
    
    var isvalidquantity = isvalidproducts = 1; 
    PNotify.removeAll();
    if(quantity.length > 0){
        var countqty = 0;
        for(var i=0; i<quantity.length; i++){

            if($("#orderid").val()==0 || $("#orderid").val()==null){
                
                var Rowid = elementid[i].match(/\d+/);
                if(productid[i] > 0 || priceid[i] > 0 || i==0){
                    if(productid[i] == 0){
                        $("#productid"+Rowid+"_div").addClass("has-error is-focused");
                        new PNotify({title: 'Please select '+(i+1)+' product !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        isvalidproducts = 0;
                    }else {
                        $("#productid"+Rowid+"_div").removeClass("has-error is-focused");
                    }
                    if(priceid[i] == 0){
                        $("#priceid"+Rowid+"_div").addClass("has-error is-focused");
                        new PNotify({title: 'Please select '+(i+1)+' price !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        isvalidproducts = 0;
                    }else {
                        $("#priceid"+Rowid+"_div").removeClass("has-error is-focused");
                    }
                } else{
                    $("#productid"+Rowid+"_div").removeClass("has-error is-focused");
                    $("#priceid"+Rowid+"_div").removeClass("has-error is-focused");
                }
            }
            if(quantity[i]=="" || parseFloat(quantity[i]) == "0"){
                countqty++;
            }
        }
        if(countqty == quantity.length){
            isvalidquantity = 0;
            new PNotify({title: 'Please enter atleast one product quantity !',styling: 'fontawesome',delay: '3000',type: 'error'});
        }
        if(isvalidquantity == 1 && isvalidproducts==1){
            var formData = new FormData($('#production-plan-form')[0]);
            var baseurl = SITE_URL + 'production-plan/get-production-plan-raw-material';
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
                    var HTML = '';

                    var obj = JSON.parse(response);
                    var PRODUCT_HTML = '';

                    if(obj.length > 0){
                        var isrequest = 0;
                        for(var i=0; i<obj.length; i++){
                            var requiredtostartproduction = remainingstock = "";
                            if(obj[i]['requiredtostartproduction'] != ""){
                                requiredtostartproduction = parseFloat(parseFloat(obj[i]['requiredtostartproduction'])).toFixed(2)+" "+obj[i]['unit'];
                            }
                            remainingstock = parseFloat(obj[i]['remainingstock']).toFixed(2)+" "+obj[i]['unit'];

                            
                            if(ACTION==1 && requiredtostartproduction!=""){
                                isrequest = 1;
                            }
                            PRODUCT_HTML += '<tr>\
                                                    <td>'+obj[i]['productname']+'</td>\
                                                    <td>'+parseFloat(obj[i]['stock']).toFixed(2)+" "+obj[i]['unit']+'</td>\
                                                    <td>'+parseFloat(obj[i]['requiredstock']).toFixed(2)+" "+obj[i]['unit']+'</td>\
                                                    <td>'+requiredtostartproduction+'</td>\
                                                    <td>'+remainingstock+'</td>\
                                                </tr>';
                        }
                    }else{
                        PRODUCT_HTML += '<tr>\
                                            <td colspan="5" class="text-center">No data available in table.</td>\
                                        </tr>';
                    }
                    var requestBtn = "";
                    if(ACTION==1 && isrequest == 1){
                        requestBtn = '<a href="'+SITE_URL+'raw-material-request/add-raw-material-request/'+productionplanid+'" target="_blank" class="btn btn-primary btn-raised pull-right">Request</a>';
                    }
                    HTML = '<div class="panel panel-transparent">\
                                <div class="panel-heading p-n">\
                                    <h2 style="font-weight:600;">Raw Material Details</h2>'+requestBtn+'\
                                </div>\
                                <div class="panel-body productvariantdiv p-sm mb-n" id="rawmaterialdata">\
                                    <div class="col-md-12 p-n">\
                                        \
                                        <table class="table mb-n">\
                                            <thead>\
                                                <tr>\
                                                    <th width="25%">Raw Material</th>\
                                                    <th>Stock</th>\
                                                    <th>Required Stock</th>\
                                                    <th>Required to Start Production</th>\
                                                    <th>Remaining Stock</th>\
                                                </tr>\
                                            </thead>\
                                            <tbody>\
                                                '+PRODUCT_HTML+'\
                                            </tbody>\
                                        </table>\
                                        \
                                    </div>\
                                </div>\
                            </div>';
                    
                    $("#rawmaterialpanel").html(HTML);
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
function resetdata() {
    $("#orderid_div").removeClass("has-error is-focused");
    
    if(ACTION==0){
        $('#orderid').val("");
        $("#orderdetailpanel,#rawmaterialpanel").html("");

        createproductsection("productwise");
        $(".selectpicker").selectpicker("refresh");
    }
    $('html, body').animate({scrollTop:0},'slow');  
}
function checkvalidation(addtype=0) {
   
    var orderid = $('#orderid').val();
    
    var isvalidorderid = isvalidproducts = 1;
    PNotify.removeAll();
    
    var c=1;
    if($('.countproducts').length > 0 && orderid == null){
        var firstproductid = $('.countproducts:first').attr('id').match(/\d+/);
        $('.countproducts').each(function(){
            var id = $(this).attr('id').match(/\d+/);
       
            if($("#productid"+id).val() > 0 || $("#priceid"+id).val() > 0 || parseInt(id)==parseInt(firstproductid)){
                if($("#productid"+id).val() == 0){
                    $("#productid"+id+"_div").addClass("has-error is-focused");
                    new PNotify({title: 'Please select '+(c)+' product !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    isvalidproducts = 0;
                }else {
                    $("#productid"+id+"_div").removeClass("has-error is-focused");
                }
                if($("#priceid"+id).val() == 0){
                    $("#priceid"+id+"_div").addClass("has-error is-focused");
                    new PNotify({title: 'Please select '+(c)+' price !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    isvalidproducts = 0;
                }else {
                    $("#priceid"+id+"_div").removeClass("has-error is-focused");
                }
                if($("#quantity"+id).val() == 0){
                    $("#quantity"+id+"_div").addClass("has-error is-focused");
                    new PNotify({title: 'Please enter '+(c)+' quantity !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    isvalidproducts = 0;
                }else {
                    $("#quantity"+id+"_div").removeClass("has-error is-focused");
                }
            } else{
                $("#productid"+id+"_div").removeClass("has-error is-focused");
                $("#priceid"+id+"_div").removeClass("has-error is-focused");
                $("#quantity"+id+"_div").removeClass("has-error is-focused");
            }
            c++;
        });
    }else{
        if(orderid == null) {
            $("#orderid_div").addClass("has-error is-focused");
            new PNotify({title: 'Please select order !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidorderid = 0;
        } else if(orderid.length > 1){
            $("#orderid_div").addClass("has-error is-focused");
            new PNotify({title: 'Please select only one order in add time !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidorderid = 0;
        }else{
            $("#orderid_div").removeClass("has-error is-focused");
        }
    }
    if(isvalidorderid == 1 && isvalidproducts == 1){
        
        var formData = new FormData($('#production-plan-form')[0]);
        if(ACTION == 0){ // INSERT
            var baseurl = SITE_URL + 'production-plan/production-plan-add';
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
                    $("#orderid_div").removeClass("has-error is-focused");
                    if(data['error']==1){
                        new PNotify({title: 'Production plan successfully added.',styling: 'fontawesome',delay: '3000',type: 'success'});
                        if(addtype==1){
                            resetdata();
                        }else{
                            setTimeout(function() { window.location = SITE_URL + "production-plan";}, 500);
                        }
                    }else if(data['error']==2){
                        new PNotify({title: 'Order already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        $("#orderid_div").addClass("has-error is-focused");
                    }else{
                        new PNotify({title: 'Production plan not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
            var baseurl = SITE_URL + 'production-plan/update-production-plan';
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
                    $("#orderid_div").removeClass("has-error is-focused");
                    if(data['error']==1){
                        new PNotify({title: 'Production plan successfully updated.',styling: 'fontawesome',delay: '3000',type: 'success'});
                    
                        setTimeout(function() { window.location = SITE_URL + "production-plan";}, 500);
                    }else if(data['error']==2){
                        new PNotify({title: 'Order already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        $("#orderid_div").addClass("has-error is-focused");
                    }else{
                        new PNotify({title: 'Production plan not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
