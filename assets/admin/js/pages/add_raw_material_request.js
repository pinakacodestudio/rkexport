
$(document).ready(function() {   
    $('#requestdate,#estimatedate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        todayBtn:"linked",
    });
    $(".add_btn").hide();
    $(".add_btn:last").show();

    $(".qty").TouchSpin(touchspinoptions);
});
 /****PRODUCT CHANGE EVENT****/
 $(document).on('change', 'select.productid', function() { 
    var divid = $(this).attr("div-id");
   
    var unitid = $("#unitid"+divid).val();
    $("#uniqueproduct"+divid).val("0_"+unitid);
    
    getproductprice(divid);
    $("#qty"+divid).val(1);
});
/****PRODUCT VARIANT CHANGE EVENT****/
$(document).on('change', 'select.priceid', function() { 
    var divid = $(this).attr("div-id");
   
    var priceid = (this.value!="")?this.value:0;
    var unitid = $("#unitid"+divid).val();
    $("#uniqueproduct"+divid).val(priceid+"_"+unitid);
    $("#qty"+divid).val('1');
});
/****PRODUCT VARIANT CHANGE EVENT****/
$(document).on('change', 'select.unitid', function() { 
    var divid = $(this).attr("div-id");
   
    var priceid = $("#priceid"+divid).val();
    var unitid = (this.value!="")?this.value:0;
    $("#uniqueproduct"+divid).val(priceid+"_"+unitid);
});
function checkBarcode(){
  
    var barcode = $.trim($("#productbarcode").val());
    var isvalidbarcode=0;
    
    PNotify.removeAll();
    if(barcode==''){
        $("#productbarcode_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter barcode or QR code number !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidbarcode=0;
        $("#productbarcode").focus();
    }else{
        $("#productbarcode_div").removeClass("has-error is-focused");
        isvalidbarcode=1;
    }
   
    if(isvalidbarcode==1){
        var datastr = 'barcode='+barcode;
        var baseurl = SITE_URL+'raw-material-request/getproductdetailsByBarcode';
        $.ajax({
            url: baseurl,
            type: 'POST',
            data: datastr,
            datatype:'json',
            beforeSend: function(){
                $('.mask').show();
                $('#loader').show();
            },
            success: function(response){
                var obj = JSON.parse(response);
                
                if(!$.isEmptyObject(obj)){
                    
                    var productsarray = $("input[name='uniqueproduct[]']").map(function(){return $(this).val();}).get();
                    
                    var process = 0;
                    var priceid = obj['priceid'];
                    var unitid = obj['unitid'];
                    var uniqueid = priceid+'_'+unitid;
                    
                    $('select.productid').each(function() {
                        var divid = $(this).attr("div-id");
                        
                        if((this.value==0 || this.value!=0 && $('#priceid'+divid).val()=='') && process==0){
                            if(!productsarray.includes(uniqueid)){
                                $(this).val(obj['id']).selectpicker('refresh').change();
                                $('#priceid'+divid).val(priceid).selectpicker('refresh').change();
                                $('#unitid'+divid).val(unitid).selectpicker('refresh').change();
                                $('#qty'+divid).val('1');
                            }
                            process = 1;
                        }
                        if(productsarray.includes(uniqueid) && $("#uniqueproduct"+divid).val()==uniqueid){
                            var qty = ($('#qty'+divid).val()==""?1:$('#qty'+divid).val());
                            $('#qty'+divid).val(parseFloat(qty).toFixed(2)+1).change();
                        }
                    });
                    
                    if(process==0 && !productsarray.includes(uniqueid)){
                        addnewproduct();
                        var divid = parseInt($(".countproducts:last").attr("id").match(/\d+/));
                        $('#productid'+divid).val(obj['id']).selectpicker('refresh').change();
                        $('#priceid'+divid).val(priceid).selectpicker('refresh').change();
                        $('#unitid'+divid).val(unitid).selectpicker('refresh').change();
                        process = 1;
                    }
                    var productid = $("select[name='productid[]']").map(function(){return $(this).val();}).get();

                    if(productid[productid.length-1]!=0){
                        addnewproduct();
                    }

                    $("#productbarcode").val('');
                }else{
                    // $("#productbarcode").val('');
                    // $("#productbarcode").focus();
                    $("#productbarcode_div").addClass("has-error is-focused");
                    new PNotify({title: 'Barcode or QR code not match with any product !',styling: 'fontawesome',delay: '3000',type: 'error'});
                }
            },
            complete: function(){
                $('.mask').hide();
                $('#loader').hide();
            },
        });
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
  
    var productid = $("#productid"+divid).val();
    
    if(productid!=0){

        var productvariant = JSON.parse($("#productid"+divid+" option:selected").attr("data-variants"));
        for(var i = 0; i < productvariant.length; i++) {
            $('#priceid'+divid).append($('<option>', { 
                value: productvariant[i]['id'],
                text : productvariant[i]['variantname']
            }));
        }
        if(productvariant.length == 1){
            $('#priceid'+divid).val(productvariant[0]['id']);
        }  
    }
    $('#priceid'+divid).selectpicker('refresh');
}
function addnewproduct(){

    var divcount = parseInt($(".countproducts:last").attr("id").match(/\d+/))+1;
    
    producthtml = '<div class="row countproducts" id="countproducts'+divcount+'">\
        <input type="hidden" name="uniqueproduct[]" id="uniqueproduct'+divcount+'">\
        <div class="col-sm-4 pl-sm pr-sm">\
            <div class="form-group" id="product'+divcount+'_div">\
                <div class="col-sm-12">\
                    <select id="productid'+divcount+'" name="productid[]" class="selectpicker form-control productid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="'+divcount+'">\
                        <option value="0">Select Product</option>\
                        '+PRODUCT_DATA+'\
                    </select>\
                </div>\
            </div>\
        </div>\
        <div class="col-sm-3 pl-sm pr-sm">\
            <div class="form-group" id="price'+divcount+'_div">\
                <div class="col-md-12">\
                    <select id="priceid'+divcount+'" name="priceid[]" class="selectpicker form-control priceid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="'+divcount+'">\
                        <option value="">Select Variant</option>\
                    </select>\
                </div>\
            </div>\
        </div>\
        <div class="col-sm-2 pl-sm pr-sm">\
            <div class="form-group" id="unit'+divcount+'_div">\
                <div class="col-md-12">\
                    <select id="unitid'+divcount+'" name="unitid[]" class="selectpicker form-control unitid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="'+divcount+'">\
                        <option value="0">Select Unit</option>\
                        '+UNIT_DATA+'\
                    </select>\
                </div>\
            </div>\
        </div>\
        <div class="col-sm-1 pl-sm pr-sm">\
            <div class="form-group" id="qty'+divcount+'_div">\
                <div class="col-md-12">\
                    <input type="text" class="form-control qty" id="qty'+divcount+'" name="qty[]" value="" onkeypress="'+(MANAGE_DECIMAL_QTY==1?"return decimal_number_validation(event, this.value,8);":"return isNumber(event);")+'" style="display: block;" div-id="'+divcount+'">\
                </div>\
            </div>\
        </div>\
        <div class="col-md-2 form-group m-n p-sm pt-md">\
            <button type = "button" class = "btn btn-default btn-raised remove_btn" onclick="removeproduct('+divcount+')" style="padding: 5px 10px;"> <i class = "fa fa-minus"></i></button> \
            <button type="button" class="btn btn-default btn-raised add_btn" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button> \
        </div>\
    </div>';

    $(".remove_btn:first").show();
    $(".add_btn:last").hide();
    $("#countproducts"+(divcount-1)).after(producthtml);
    
    $("#qty"+divcount).TouchSpin(touchspinoptions);
    $(".selectpicker").selectpicker("refresh");
}
function removeproduct(divid){

    if($('select[name="productid[]"]').length!=1 && ACTION==1 && $('#rawmaterialrequestproductid'+divid).val()!=null){
        var removerawmaterialrequestproductid = $('#removerawmaterialrequestproductid').val();
        $('#removerawmaterialrequestproductid').val(removerawmaterialrequestproductid+','+$('#rawmaterialrequestproductid'+divid).val());
    }
    $("#countproducts"+divid).remove();

    $(".add_btn:last").show();
    if ($(".remove_btn:visible").length == 1) {
        $(".remove_btn:first").hide();
    }
}
function resetdata() {
    $("#requestno_div").removeClass("has-error is-focused");
    $("#requestdate_div").removeClass("has-error is-focused");
    
    if(ACTION==0){
        $('#remarks').val("");
        $("#estimatedate").val("");
        $(".countproducts:not(:first)").remove();
        var divid = parseInt($(".countproducts:first").attr("id").match(/\d+/));

        $('#productid'+divid+',#priceid'+divid).val("0");
        $('#qty'+divid).val("1");
        getproductprice(divid);

        $('.add_btn:first').show();
        $('.remove_btn').hide();

        $(".selectpicker").selectpicker('refresh');
    }
    $('html, body').animate({scrollTop:0},'slow');  
}
function checkvalidation(addtype=0) {
   
    var requestno = $('#requestno').val();
    var requestdate = $('#requestdate').val();
    
    var isvalidrequestno = isvalidrequestdate = isvalidproductid = isvalidpriceid = isvalidqty = isvaliduniqueproducts = 1;
   
    PNotify.removeAll();
    if(requestno=="") {
        $("#requestno_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter request number !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidrequestno = 0;
    }else {
        $("#requestno_div").removeClass("has-error is-focused");
    }
    if(requestdate=="") {
        $("#requestdate_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select request date !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidrequestdate = 0;
    } else {
        $("#requestdate_div").removeClass("has-error is-focused");
    }   
    var c=1;
    // var firstproductid = $('.countdocuments:first').attr('id').match(/\d+/);
    $('.countdocuments').each(function(){
        var id = $(this).attr('id').match(/\d+/);
       
        if($("#productid"+id).val() > 0 || $("#priceid"+id).val() > 0 || $("#qty"+id).val() == 0 || parseInt(id)==parseInt(firstproductid)){
            if($("#productid"+id).val() == 0){
                $("#product"+id+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(c)+' product !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidproductid = 0;
            }else {
                $("#product"+id+"_div").removeClass("has-error is-focused");
            }
            if($("#priceid"+id).val() == "" || $("#priceid"+id+" option:selected").text() == "Select Variant"){
                $("#price"+id+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(c)+' price !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidpriceid = 0;
            }else {
                $("#price"+id+"_div").removeClass("has-error is-focused");
            }
            if($("#qty"+id).val() == 0){
                $("#qty"+id+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(c)+' quantity !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidqty = 0;
            }else {
                $("#qty"+id+"_div").removeClass("has-error is-focused");
            }
        } else{
            $("#product"+id+"_div").removeClass("has-error is-focused");
            $("#price"+id+"_div").removeClass("has-error is-focused");
            $("#qty"+id+"_div").removeClass("has-error is-focused");
        }
        c++;
    });

    var products = $('input[name="uniqueproduct[]"]');
    var values = [];
    for(j=0;j<products.length;j++) {
        var uniqueproducts = products[j];
        var id = uniqueproducts.id.match(/\d+/);
        
        if(uniqueproducts.value!="" && ($("#priceid"+id[0]).val()!="" && $("#priceid"+id[0]+" option:selected").text()!="Select Variant")){
            if(values.indexOf(uniqueproducts.value)>-1) {
                $("#price"+id[0]+"_div,#unit"+id[0]+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(j+1)+' is different product variant & unit !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvaliduniqueproducts = 0;
            }
            else{ 
                values.push(uniqueproducts.value);
                if(($("#priceid"+id[0]).val()!="" && $("#priceid"+id[0]+" option:selected").text()!="Select Variant")){
                    $("#price"+id[0]+"_div,#unit"+id[0]+"_div").removeClass("has-error is-focused");
                }
            }
        }
    } 
    if(isvalidrequestno == 1 && isvalidrequestdate == 1 && isvalidproductid == 1 && isvalidpriceid == 1 && isvalidqty == 1 && isvaliduniqueproducts == 1){
        var formData = new FormData($('#rawmaterialrequestform')[0]);
        if(ACTION == 0){ // INSERT
            var baseurl = SITE_URL + 'raw-material-request/raw-material-request-add';
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
                        new PNotify({title: 'Raw material request successfully added.',styling: 'fontawesome',delay: '3000',type: 'success'});
                        if(addtype==1){
                            resetdata();
                        }else{
                            setTimeout(function() { window.location = SITE_URL + "raw-material-request";}, 500);
                        }
                    }else if(response==2){
                        new PNotify({title: 'Raw material request number already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else{
                        new PNotify({title: 'Raw material request not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
            var baseurl = SITE_URL + 'raw-material-request/update-raw-material-request';
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
                        new PNotify({title: 'Raw material request successfully updated.',styling: 'fontawesome',delay: '3000',type: 'success'});
                    
                        setTimeout(function() { window.location = SITE_URL + "raw-material-request";}, 500);
                    }else if(response==2){
                        new PNotify({title: 'Raw material request number already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else{
                        new PNotify({title: 'Raw material request not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
