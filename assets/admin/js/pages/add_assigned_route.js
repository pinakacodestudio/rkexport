$(document).ready(function() {

    //Call function on Load page 
    getprovince(DEFAULTCOUNTRYID);
    if(provinceid!=0){
        getcity(provinceid);
    }
    getroute();

    if(ACTION==1){
        // getvehicle($('#employeeid').val());
        getroutemember(routeid);
        getinvoice();
        getinvoiceproducts();
    }
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
        getroute();
    });
    $('#cityid').on('change', function (e) {
        getroute();
    });
    $('#startdate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        todayBtn:"linked",
        clearBtn: true
    });
    $('#time').datetimepicker({
        pickDate: false,
        minuteStep: 5,
        pickerPosition: 'bottom-right',
        format: 'hh:ii:ss',
        autoclose: true,
        showMeridian: false,
        startView: 1,
        maxView: 1,
    });

    /****EMPLOYEE CHANGE EVENT****/
    $('#employeeid').on('change', function() { 
        // getvehicle(this.value);
    });
    /****VEHICLE CHANGE EVENT****/
    $('#vehicleid').on('change', function() { 
        var capacity = $(this).find("option:selected").attr("data-capacity")
        $("#capacity").val(capacity);
    });
    /****ROUTE CHANGE EVENT****/
    $('#routeid').on('change', function() { 
        getroutemember(this.value);
        getinvoice();
    });
    /****MEMBER CHANGE EVENT****/
    $('#memberid').on('change', function() { 
        getinvoice();
    });
     /****INVOVICE CHANGE EVENT****/
     $('#invoiceid').on('change', function() { 
        getinvoiceproducts();
    });
    $(".qty").TouchSpin(touchspinoptions);
    $(".add_btn").hide();
    $(".add_btn:last").show();
});
/****EXTRA PRODUCT CHANGE EVENT****/
$(document).on('change', 'select.productid', function() { 
    var divid = $(this).attr("div-id");
    $('#tax'+divid+',#price'+divid).val("");
    $("#uniqueproducts"+divid).val(this.value+"_0");

    getproductprice(divid);
    changeproductamount(divid);
});
/****EXTRA VARIANT CHANGE EVENT****/
$(document).on('change', 'select.priceid', function() { 
    var divid = $(this).attr("div-id");
   
    if(this.value!="0"){
        var actualprice = parseFloat($("#priceid"+divid+" option:selected").text().trim());
        $('#price'+divid).val(parseFloat(actualprice).toFixed(2));
    }else{
        $('#price'+divid).val("");
    }
    var productid = $("#productid"+divid).val();
    $("#uniqueproducts"+divid).val(productid+"_"+this.value);
    changeproductamount(divid);
});
$(document).on('change', '.qty', function() {
    var divid = $(this).attr("div-id");
    changeproductamount(divid);
});
$(document).on('keyup', '.price', function() {
    var divid = $(this).attr("div-id");
    changeproductamount(divid);
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

function getvehicle(employeeid){
  
    $("#vehicleid").find('option')
                .remove()
                .end()
                .val('0')
                .append('<option value="0">Select Vehicle</option>')
              ;
    $("#vehicleid").selectpicker('refresh');
    $("#capacity").val("");
  
    if(employeeid!=0){
        var uurl = SITE_URL+"vehicle/getVehicleByEmployeeId";
        $.ajax({
            url: uurl,
            type: 'POST',
            data: {employeeid:employeeid},
            dataType: 'json',
            async: false,
            success: function(response){
    
                for(var i = 0; i < response.length; i++) {
        
                    $("#vehicleid").append($('<option>', { 
                        value: response[i]['id'],
                        text : response[i]['name'],
                        "data-capacity" : response[i]['capacity'],
                    }));
                }
                if(vehicleid!=0){
                    $("#vehicleid").val(vehicleid);
                    $("#capacity").val(capacity);
                }
            },
            error: function(xhr) {
                //alert(xhr.responseText);
            },
        });
        $("#vehicleid").selectpicker('refresh');
    }
}
function getroute(){
  
    $("#routeid").find('option')
                .remove()
                .end()
                .val('0')
                .append('<option value="0">Select Route</option>')
              ;
    $("#routeid").selectpicker('refresh');
    $("#memberid").find('option')
        .remove()
        .end()
        .val('')
        .append('')
    ;
    $("#memberid").selectpicker('refresh');
    $("#invoiceid").find('option')
        .remove()
        .end()
        .val('')
        .append('')
    ;
    $("#invoiceid").selectpicker('refresh');
    $("#invoiceproductsection").html("");

    var provinceid = $("#provinceid").val();
    var cityid = $("#cityid").val();
    
    var uurl = SITE_URL+"route/getRouteByProvinceOrCity";
    $.ajax({
        url: uurl,
        type: 'POST',
        data: {provinceid:provinceid,cityid:cityid},
        dataType: 'json',
        async: false,
        success: function(response){

            for(var i = 0; i < response.length; i++) {
    
                $("#routeid").append($('<option>', { 
                    value: response[i]['id'],
                    text : response[i]['route']
                }));
            }
            if(routeid!=0){
                $("#routeid").val(routeid);
            }
        },
        error: function(xhr) {
            //alert(xhr.responseText);
        },
    });
    $("#routeid").selectpicker('refresh');
}
function getroutemember(routeid){
  
    $("#memberid").find('option')
                .remove()
                .end()
                .val('')
                .append('')
              ;
    $("#memberid").selectpicker('refresh');
    $("#invoiceid").find('option')
                .remove()
                .end()
                .val('')
                .append('')
              ;
    $("#invoiceid").selectpicker('refresh');
    $("#invoiceproductsection").html("");

    if(routeid!=0){
        var uurl = SITE_URL+"route/getMembersInRoute";
        $.ajax({
            url: uurl,
            type: 'POST',
            data: {routeid:routeid},
            dataType: 'json',
            async: false,
            success: function(response){
    
                for(var i = 0; i < response.length; i++) {
        
                    if(ACTION==1){
                        if(memberids!=null || memberids!=''){
             
                            memberids = memberids.toString().split(',');
                           
                            if(memberids.includes(response[i]['id'])){
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
                        $("#memberid").append($('<option>', { 
                            value: response[i]['id'],
                            selected: "selected",
                            text : ucwords(response[i]['name'])
                        }));
                    }
                }
            },
            error: function(xhr) {
                //alert(xhr.responseText);
            },
        });
        $("#memberid").selectpicker('refresh');
    }
}
function getinvoice(){
  
    $("#invoiceid").find('option')
                .remove()
                .end()
                .val('')
                .append('')
              ;
    $("#invoiceid").selectpicker('refresh');
    $("#invoiceproductsection").html("");

    var memberid = ($("#memberid").val()!=null)?$("#memberid").val():"";
    if(memberid!=""){
        var uurl = SITE_URL+"invoice/getInvoiceByBuyer";
        $.ajax({
            url: uurl,
            type: 'POST',
            data: {memberid:memberid},
            dataType: 'json',
            async: false,
            success: function(response){
    
                for(var i = 0; i < response.length; i++) {
        
                    if(ACTION==1){
                        if(invoiceids!=null || invoiceids!=''){
                            invoiceids = invoiceids.toString().split(',');
                            if(invoiceids.includes(response[i]['id'])){
                                $('#invoiceid').append($('<option>', { 
                                    value: response[i]['memberid']+"_"+response[i]['id'],
                                    selected: "selected",
                                    text : response[i]['invoiceno'],
                                    "data-id": response[i]['id'],
                                }));
                            }else{
                                $("#invoiceid").append($('<option>', { 
                                    value: response[i]['memberid']+"_"+response[i]['id'],
                                    text : response[i]['invoiceno'],
                                    "data-id": response[i]['id'],
                                }));
                            }
                        }
                    }else{
                        $("#invoiceid").append($('<option>', { 
                            value: response[i]['memberid']+"_"+response[i]['id'],
                            text : response[i]['invoiceno'],
                            "data-id": response[i]['id'],
                        }));
                    }
                }

            },
            error: function(xhr) {
                //alert(xhr.responseText);
            },
        });
        $("#invoiceid").selectpicker('refresh');
    }
}
function getinvoiceproducts(){

    var invoiceid = [];
    if($("#invoiceid").val()!=null){
        
        if($("#invoiceid option:selected").length > 0){
            $("#invoiceid option:selected").each(function(){
                invoiceid.push($(this).attr("data-id"));
            });
        }
    }
    $("#invoiceproductsection").html("");
    if(invoiceid.length>0){
        var uurl = SITE_URL+"invoice/getInvoiceProducts";
        $.ajax({
            url: uurl,
            type: 'POST',
            data: {invoiceid:invoiceid},
            dataType: 'json',
            async: false,
            success: function(response){
    
                var HTML = PRODUCT_HTML = "";
                if(response.length > 0){
                    var totalprice = 0;
                    for(var i=0; i<response.length; i++){
                        
                        totalprice += parseFloat(response[i]['totalprice']);
                        PRODUCT_HTML += '<tr>\
                                            <td>'+(i+1)+'</td>\
                                            <td>'+response[i]['productname']+'</td>\
                                            <td>'+response[i]['variantname']+'</td>\
                                            <td class="text-right">'+format.format(parseFloat(response[i]['quantity']).toFixed(2))+'</td>\
                                            <td class="text-right">'+format.format(parseFloat(response[i]['price']).toFixed(2))+'</td>\
                                            <td class="text-right">'+format.format(parseFloat(response[i]['tax']).toFixed(2))+'</td>\
                                            <td class="text-right">'+format.format(parseFloat(response[i]['totalprice']).toFixed(2))+'</td>\
                                        </tr>';
                    }
                    PRODUCT_HTML += '<tr style="border-top: 2px solid #ddd;">\
                                        <th colspan="6" class="text-right">Total Price ('+CURRENCY_CODE+')</th>\
                                        <th class="text-right">'+format.format(parseFloat(totalprice).toFixed(2))+'</th>\
                                    </tr>';
                }else{
                    PRODUCT_HTML += '<tr>\
                                        <td colspan="7" class="text-center">No data available in table.</td>\
                                    </tr>';
                }
                HTML = '<div class="panel panel-transparent pt-md">\
                            <div class="panel-heading p-n">\
                                <h2 style="font-weight:600;">Total Product List</h2>\
                            </div>\
                            <div class="panel-body invoiceproductdiv p-sm mb-n">\
                                <div class="col-md-12 p-n">\
                                    \
                                    <table class="table mb-n">\
                                        <thead>\
                                            <tr>\
                                                <th width="8%">Sr. No.</th>\
                                                <th>Product Name</th>\
                                                <th>Variant Name</th>\
                                                <th class="text-right">Quantity</th>\
                                                <th class="text-right">Price ('+CURRENCY_CODE+')</th>\
                                                <th class="text-right">Tax (%)</th>\
                                                <th class="text-right">Total Price ('+CURRENCY_CODE+')</th>\
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
                
                $("#invoiceproductsection").html(HTML);
            },
            error: function(xhr) {
                //alert(xhr.responseText);
            },
        });
    }
}
function getproductprice(divid){
    
    $('#priceid'+divid)
        .find('option')
        .remove()
        .end()
        .append('<option value="0">Select Variant</option>')
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
        beforeSend: function(){
            $('.mask').show();
            $('#loader').show();
        },
        success: function(response){
            for(var i = 0; i < response.length; i++) {
               
                $('#priceid'+divid).append($('<option>', { 
                    value: response[i]['id'],
                    text : response[i]['variantname']
                }));
                
                $('#tax'+divid).val(response[i]['tax']);
                /* if(response[i]['isuniversal']==1 || response.length==1){
                    $('#priceid'+divid).val(response[i]['id']).selectpicker('refresh');
                    $('#priceid'+divid).change();
                    // $("#uniqueproducts"+divid).val(productid+"_"+response[i]['id']);
                } */
            }
            if(response.length == 1){
                $('#priceid'+divid).val(response[0]['id']).selectpicker('refresh');
                $('#priceid'+divid).change();
            }
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
    $('#priceid'+divid).selectpicker('refresh');
}
function addNewProductRaw(){

    var readonly = "readonly";
    if(EDITTAXRATE_SYSTEM==1){
        readonly = "";
    }

    var divcount = parseInt($(".countproducts:last").attr("id").match(/\d+/))+1;
    
    html = '<div class="col-md-12 p-n countproducts" id="countproducts'+divcount+'">\
        <input type="hidden" name="uniqueproducts[]" id="uniqueproducts'+divcount+'">\
        <div class="col-sm-3 pl-sm pr-sm">\
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
            <div class="form-group" id="priceid'+divcount+'_div">\
                <div class="col-md-12">\
                    <select id="priceid'+divcount+'" name="priceid[]" class="selectpicker form-control priceid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="'+divcount+'">\
                        <option value="0">Select Variant</option>\
                    </select>\
                </div>\
            </div>\
        </div>\
        <div class="col-sm-1 pl-sm pr-sm">\
            <div class="form-group" id="qty'+divcount+'_div">\
                <div class="col-md-12">\
                    <input type="text" id="qty'+divcount+'" name="qty[]" value="" class="form-control qty" div-id="'+divcount+'" onkeypress="'+(MANAGE_DECIMAL_QTY==1?"return decimal_number_validation(event, this.value,8);":"return isNumber(event);")+'">\
                </div>\
            </div>\
        </div>\
        <div class="col-sm-1 pl-sm pr-sm">\
            <div class="form-group" id="price'+divcount+'_div">\
                <div class="col-md-12">\
                    <input type="text" id="price'+divcount+'" name="price[]" value="" class="form-control price text-right" div-id="'+divcount+'" onkeypress="return decimal_number_validation(event, this.value, 8)">\
                </div>\
            </div>\
        </div>\
        <div class="col-sm-1 pl-sm pr-sm">\
            <div class="form-group" id="tax'+divcount+'_div">\
                <div class="col-md-12">\
                    <input type="text" id="tax'+divcount+'" name="tax[]" value="" class="form-control tax text-right" div-id="'+divcount+'" onkeypress="return decimal_number_validation(event, this.value, 8)" '+readonly+'>\
                </div>\
            </div>\
        </div>\
        <div class="col-sm-2 pl-sm pr-sm">\
            <div class="form-group" id="totalprice'+divcount+'_div">\
                <div class="col-md-12">\
                    <input type="text" id="totalprice'+divcount+'" name="totalprice[]" value="" class="form-control totalprice text-right" div-id="'+divcount+'" readonly>\
                </div>\
            </div>\
        </div>\
        <div class="col-md-1 form-group m-n p-sm pt-md">\
            <button type = "button" class = "btn btn-default btn-raised remove_btn" onclick="removeProductRaw('+divcount+')" style="padding: 5px 10px;"> <i class = "fa fa-minus"></i></button> \
            <button type="button" class="btn btn-default btn-raised add_btn" onclick="addNewProductRaw()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button> \
        </div>\
    </div>';

    $(".remove_btn:first").show();
    $(".add_btn:last").hide();
    $("#countproducts"+(divcount-1)).after(html);
    $(".selectpicker").selectpicker("refresh");
    $("#qty"+divcount).TouchSpin(touchspinoptions);
}
function removeProductRaw(divid){

    /* if($('select[name="channelid[]"]').length!=1 && ACTION==1 && $('#routememberid'+divid).val()!=null){
        var removeroutememberid = $('#removeroutememberid').val();
        $('#removeroutememberid').val(removeroutememberid+','+$('#routememberid'+divid).val());
    } */
    $("#countproducts"+divid).remove();

    $(".add_btn:last").show();
    if ($(".remove_btn:visible").length == 1) {
        $(".remove_btn:first").hide();
    }
    calculatetotalprice();
}
function changeproductamount(divid){
   
    if(divid!=undefined){
        var price = $("#priceid"+divid+" option:selected").text().trim();
        var actualprice = $("#price"+divid).val();
        var qty = $("#qty"+divid).val();
        var tax = $("#tax"+divid).val();
        tax = (tax!="")?parseFloat(tax).toFixed(2):0;
        actualprice = (actualprice!="")?parseFloat(actualprice).toFixed(2):0;

        if(price!="0" && qty!="0" && price!="" && qty!="" && price!="Select Variant"){
            totalamount = productamount = 0;

            price = parseFloat(actualprice).toFixed(2);
           
            if(GST_PRICE == 1){
                price = parseFloat(parseFloat(price) + (parseFloat(price) * parseFloat(tax) / 100)).toFixed(2);
            }
            productamount = parseFloat(price);
            totalamount = parseFloat(productamount) * parseFloat(qty);
            
            $("#totalprice"+divid).val(parseFloat(totalamount).toFixed(2));
        }else{
            $("#totalprice"+divid).val("");
        }
        calculatetotalprice();
    }
}
function calculatetotalprice(){
    var totalprice = 0;
    $(".totalprice").each(function( index ) {
        if($(this).val()!=""){
            totalprice += parseFloat($(this).val());
        }
    });
    $("#displaytotalprice").html(format.format(parseFloat(totalprice).toFixed(2)));
}
function resetdata() {
    $("#employee_div").removeClass("has-error is-focused");
    $("#vehicle_div").removeClass("has-error is-focused");
    $("#route_div").removeClass("has-error is-focused");
    $("#member_div").removeClass("has-error is-focused");
    $("#invoice_div").removeClass("has-error is-focused");
    
    if(ACTION==0){
        $('#employeeid,#provinceid,#cityid,#vehicleid,#routeid').val("0");
        $('#cityid')
            .find('option')
            .remove()
            .end()
            .append('<option value="0">Select City</option>')
            .val('0')
        ;
        $('#vehicleid')
            .find('option')
            .remove()
            .end()
            .append('<option value="0">Select Vehicle</option>')
            .val('0')
        ;
        $('#capacity,#time,#totalweight').val("");
        getroute();

        /**Reset form of extra product section ********/
        $(".countproducts:not(:first)").remove();
        var divid = parseInt($(".countproducts:first").attr("id").match(/\d+/));

        $('#productid'+divid+',#priceid'+divid).val("0");
        $('#product'+divid+'_div,#priceid'+divid+'_div,#qty'+divid+'_div,#price'+divid+'_div,#price'+divid+'_div').removeClass("has-error is-focused");
        $('#qty'+divid).val("1");
        $('#price'+divid+',#tax'+divid+',#totalprice'+divid).val("");
        getproductprice(divid);
        calculatetotalprice();

        $('.add_btn:first').show();
        $('.remove_btn').hide();
        /*******/

        $(".selectpicker").selectpicker('refresh');
    }
    $('html, body').animate({scrollTop:0},'slow');  
}
function checkvalidation(addtype=0) {
   
    var employeeid = $('#employeeid').val().trim();
    var vehicleid = $('#vehicleid').val();
    var routeid = $('#routeid').val();
    var memberid = $('#memberid').val();
    var invoiceid = $('#invoiceid').val();
    
    var isvalidemployeeid = isvalidvehicleid = isvalidrouteid = isvalidmemberid = isvaliduniqueproduct = isvalidproductid = isvalidpriceid = isvalidqty = isvalidprice = 1;
   
    PNotify.removeAll();
    if(employeeid==0) {
        $("#employee_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select employee !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidemployeeid = 0;
    }else {
        $("#employee_div").removeClass("has-error is-focused");
    }
    /* if(vehicleid==0) {
        $("#vehicle_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select vehicle !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidvehicleid = 0;
    } else {
        $("#vehicle_div").removeClass("has-error is-focused");
    }  */  
    if(routeid==0) {
        $("#route_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select route !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidrouteid = 0;
    } else {
        $("#route_div").removeClass("has-error is-focused");
    }   
    if(memberid==null) {
        $("#member_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select '+member_label+' !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmemberid = 0;
    } else {
        $("#member_div").removeClass("has-error is-focused");
    } 
    
    /* var c=1;
    $('.countproducts').each(function(){
        var id = $(this).attr('id').match(/\d+/);
        
        var productid = $("#productid"+id).val();
        var priceid = $('#priceid'+id).val();
        var qty = $('#qty'+id).val();
        var price = $('#price'+id).val();
       
        if(productid > 0 || priceid > 0 || price != "" || invoiceid==null){
            if(productid == 0){
                $("#product"+id+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(c)+' product !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidproductid = 0;
            }else {
                $("#product"+id+"_div").removeClass("has-error is-focused");
            }
            if(priceid == 0){
                $("#priceid"+id+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(c)+' variant !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidpriceid = 0;
            }else {
                $("#priceid"+id+"_div").removeClass("has-error is-focused");
            }
            if(qty == "" || qty == "0"){
                $("#qty"+id+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please enter '+(c)+' quantity !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidqty = 0;
            }else {
                $("#qty"+id+"_div").removeClass("has-error is-focused");
            }
            if(price == "" || price == 0){
                $("#price"+id+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please enter '+(c)+' price !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidprice = 0;
            }else {
                $("#price"+id+"_div").removeClass("has-error is-focused");
            }
        } else{
            $("#product"+id+"_div").removeClass("has-error is-focused");
            $("#priceid"+id+"_div").removeClass("has-error is-focused");
            $("#qty"+id+"_div").removeClass("has-error is-focused");
            $("#price"+id+"_div").removeClass("has-error is-focused");
        }
        c++;
    }); */

    var products = $('input[name="uniqueproducts[]"]');
    var values = [];
    for(j=0;j<products.length;j++) {
        var uniqueproducts = products[j];
        var id = uniqueproducts.id.match(/\d+/);
        
        if(uniqueproducts.value!="" && $("#productid"+id[0]).val()!=0 && $("#priceid"+id[0]).val()!=0){
            if(values.indexOf(uniqueproducts.value)>-1) {
                $("#product"+id[0]+"_div,#priceid"+id[0]+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(j+1)+' is different product & variant !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvaliduniqueproduct = 0;
            }
            else{ 
                values.push(uniqueproducts.value);
                if($("#productid"+id[0]).val()!=0 && $("#priceid"+id[0]).val()!=0){
                    $("#product"+id[0]+"_div,#priceid"+id[0]+"_div").removeClass("has-error is-focused");
                }
            }
        }
    } 

    if(isvalidemployeeid == 1 && isvalidvehicleid == 1 && isvalidrouteid == 1 && isvalidmemberid == 1 && isvaliduniqueproduct == 1 && isvalidproductid == 1 && isvalidpriceid == 1 && isvalidqty == 1 && isvalidprice == 1){
        var formData = new FormData($('#assignedrouteform')[0]);
        if(ACTION == 0){ // INSERT
            var baseurl = SITE_URL + 'assigned-route/add-assigned-route';
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
                        new PNotify({title: 'Assigned route successfully added.',styling: 'fontawesome',delay: '3000',type: 'success'});
                        if(addtype==1){
                            resetdata();
                        }else{
                            setTimeout(function() { window.location = SITE_URL + "assigned-route";}, 500);
                        }
                    }else{
                        new PNotify({title: 'Assigned route not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
            var baseurl = SITE_URL + 'assigned-route/update-assigned-route';
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
                        new PNotify({title: 'Assigned route successfully updated.',styling: 'fontawesome',delay: '3000',type: 'success'});
                        setTimeout(function() { window.location = SITE_URL + "assigned-route";}, 500);
                    }else{
                        new PNotify({title: 'Assigned route not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

  
  
  