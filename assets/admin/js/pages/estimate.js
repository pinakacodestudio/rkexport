$(document).ready(function() {
   
    oTable = $('#estimatetable').DataTable({
        "language": {
            "lengthMenu": "_MENU_"
        },
        "pageLength": 10,
        "columnDefs": [{
            'orderable': false,
            'targets': [0,-1,-2]
        }],
        "order": [], //Initial no order.
        'serverSide': true,//Feature control DataTables' server-side processing mode.
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": SITE_URL+"estimate/listing",
            "type": "POST",
            "data": function ( data ) {
                
            },
            beforeSend: function(){
                $('.mask').show();
                $('#loader').show();
            },
            error: function(xhr) {
            //alert(xhr.responseText);
            },
            complete: function(e){
                $('.mask').hide();
                $('#loader').hide();
            },
        },
    });

    $('.dataTables_filter input').attr('placeholder','Search...');


    //DOM Manipulation to move datatable elements integrate to panel
    $('.panel-ctrls.panel-tbl').append($('.dataTables_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls.panel-tbl').append("<i class='separator'></i>");
    $('.panel-ctrls.panel-tbl').append($('.dataTables_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('.panel-footer').append($(".dataTable+.row"));
    $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-md");

    /* $('#productid').val(1722);
    getproductprice();
    getprocessgroup(); */

    $("#qty").TouchSpin(touchspinoptions);
    /****PRODUCT CHANGE EVENT****/
    $('#productid').change(function(){
        getproductprice();
    });
    /****PRICE CHANGE EVENT****/
    $('#priceid').change(function(){
        $("#qty").val("1");
        getprocessgroup();
    });
    /****PRIOCESS GROUP CHANGE EVENT****/
    $('#processgroupid').change(function(){
        getprocessbyprocessgroup();
    });
    $("#qty").change(function(){
        getprocessbyprocessgroup();
    });
    $("#pricetype").change(function(){
        getprocessbyprocessgroup();
    });
});
/****OUT PRODUCT CHANGE EVENT****/
$(document).on('change', 'select.outproductid', function(){
    var elementid = $(this).attr("id").split('_');
    var sequenceno = elementid[0].match(/\d+/);
    var divid = elementid[1];
    var productid = $(this).val();
    getproductvariant(sequenceno,divid,1,productid);
    $("#outprice"+sequenceno+"_"+divid).val($("#outproductvariantid"+sequenceno+"_"+divid+" option:selected").attr("data-price"));
});
$(document).on('change','select.outproductvariantid', function (e) {
    var elementid = $(this).attr("id").split('_');
    var sequenceno = elementid[0].match(/\d+/);
    var divid = elementid[1];
    var price = 0;
    if(this.value > 0){
        var price = $("#outproductvariantid"+sequenceno+"_"+divid+" option:selected").attr("data-price");
        var fifo_price = $("#outproductvariantid"+sequenceno+"_"+divid+" option:selected").attr("data-fifo-price");
        var average_price = $("#outproductvariantid"+sequenceno+"_"+divid+" option:selected").attr("data-average-price");
        var latest_price = $("#outproductvariantid"+sequenceno+"_"+divid+" option:selected").attr("data-latest-price");
        var low_price = $("#outproductvariantid"+sequenceno+"_"+divid+" option:selected").attr("data-low-price");
        var outpricetype = $("#outpricetype"+sequenceno+"_"+divid).val();

        if(outpricetype==0){
            price = fifo_price;
        }else if(outpricetype==1){
            price = average_price;
        }else if(outpricetype==2){
            price = latest_price;
        }else if(outpricetype==3){
            price = low_price;
        }
        $("#outprice"+sequenceno+"_"+divid).val(parseFloat(price).toFixed(2));
        
        if($("#outqty"+sequenceno+"_"+divid).val()!=""){
            price = parseFloat(price) * parseFloat($("#outqty"+sequenceno+"_"+divid).val());
        }
        $("#outproductamount"+sequenceno+"_"+divid).val(parseFloat(price).toFixed(2));
    }
    
});
/****IN PRODUCT CHANGE EVENT****/
$(document).on('change', 'select.inproductid', function(){
    var elementid = $(this).attr("id").split('_');
    var sequenceno = elementid[0].match(/\d+/);
    var divid = elementid[1];
    var productid = $(this).val();
    getproductvariant(sequenceno,divid,0,productid);
    $("#inprice"+sequenceno+"_"+divid).val($("#inproductvariantid"+sequenceno+"_"+divid+" option:selected").attr("data-price"));
});
$(document).on('change','select.inproductvariantid', function (e) {
    var elementid = $(this).attr("id").split('_');
    var sequenceno = elementid[0].match(/\d+/);
    var divid = elementid[1];
    $("#inprice"+sequenceno+"_"+divid).val($("#inproductvariantid"+sequenceno+"_"+divid+" option:selected").attr("data-price"));
    var price = 0;
    if($("#inqty"+sequenceno+"_"+divid).val()!=""){
        price = parseFloat($("#inproductvariantid"+sequenceno+"_"+divid+" option:selected").attr("data-price")) * parseFloat($("#inqty"+sequenceno+"_"+divid).val());
    }
    $("#inproductamount"+sequenceno+"_"+divid).val(parseFloat(price).toFixed(2));
});
$(document).on('change', '.outqty', function() { 
    var elementid = $(this).attr("id").split('_');
    var sequenceno = elementid[0].match(/\d+/);
    var divid = elementid[1];
    var price = 0;
    if(this.value!=""){
        price = parseFloat($("#outproductvariantid"+sequenceno+"_"+divid+" option:selected").attr("data-price")) * parseFloat(this.value);
    }
    $("#outproductamount"+sequenceno+"_"+divid).val(parseFloat(price).toFixed(2));
});
$(document).on('change', '.inqty', function() { 
    var elementid = $(this).attr("id").split('_');
    var sequenceno = elementid[0].match(/\d+/);
    var divid = elementid[1];
    var price = 0;
    if(this.value!=""){
        price = parseFloat($("#inproductvariantid"+sequenceno+"_"+divid+" option:selected").attr("data-price")) * parseFloat(this.value);
    }
    $("#inproductamount"+sequenceno+"_"+divid).val(parseFloat(price).toFixed(2));
});
$(document).on('change', '.outpricetype', function() { 
    var elementid = $(this).attr("id").split('_');
    var sequenceno = elementid[0].match(/\d+/);
    var divid = elementid[1];
    var fifo_price = $("#outproductvariantid"+sequenceno+"_"+divid+" option:selected").attr("data-fifo-price");
    var average_price = $("#outproductvariantid"+sequenceno+"_"+divid+" option:selected").attr("data-average-price");
    var latest_price = $("#outproductvariantid"+sequenceno+"_"+divid+" option:selected").attr("data-latest-price");
    var low_price = $("#outproductvariantid"+sequenceno+"_"+divid+" option:selected").attr("data-low-price");
    var outpricetype = this.value;

    if(outpricetype==0){
        price = fifo_price;
    }else if(outpricetype==1){
        price = average_price;
    }else if(outpricetype==2){
        price = latest_price;
    }else if(outpricetype==3){
        price = low_price;
    }
    $("#outprice"+sequenceno+"_"+divid).val(parseFloat(price).toFixed(2));
    
    if($("#outqty"+sequenceno+"_"+divid).val()!=""){
        price = parseFloat(price) * parseFloat($("#outqty"+sequenceno+"_"+divid).val());
    }
    $("#outproductamount"+sequenceno+"_"+divid).val(parseFloat(price).toFixed(2));
}); 
function getproductprice(){
   
    $('#priceid').find('option')
                    .remove()
                    .end()
                    .append('<option value="0">Select Variant</option>')
                    .val('0')
                ;
    $('#priceid').selectpicker('refresh');
    $('#processgroupid').find('option')
        .remove()
        .end()
        .append('<option value="0">Select Process Group</option>')
        .val('0')
    ;
    $('#processgroupid').selectpicker('refresh');

    $('#btnpanel,#inproducttable,#outproducttable,#saveestimatebtn').hide();
    $("#processgroup_maindiv").html('');

    var productid = $('#productid').val();
    
    if(productid != '0'){
      var uurl = SITE_URL+"product/getVariantByProductIdForAdmin";
      
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {productid:String(productid)},
        dataType: 'json',
        //async: false,
        beforeSend: function(){
            $('.mask').show();
            $('#loader').show();
        },
        success: function(response){
  
            for(var i = 0; i < response.length; i++) {
                
                $('#priceid').append($('<option>', { 
                    value: response[i]['id'],
                    text : response[i]['variantname']
                }));
            }
            if(response.length == 1){
                $('#priceid').val(response[0]['id']).selectpicker("refresh").change();
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
    $('#priceid').selectpicker('refresh');
}

function getprocessgroup(){
    $('#processgroupid').find('option')
        .remove()
        .end()
        .append('<option value="0">Select Process Group</option>')
        .val('0')
    ;
    $('#processgroupid').selectpicker('refresh');
    $('#btnpanel,#inproducttable,#outproducttable,#saveestimatebtn').hide();
    $("#processgroup_maindiv").html('');

    var productid = $('#productid').val();
    var priceid = $('#priceid').val();

    if(productid != '0' && priceid != '0'){
        var uurl = SITE_URL+"estimate/getProcessGroupByProduct";

        $.ajax({
            url: uurl,
            type: 'POST',
            data: {priceid:String(priceid)},
            dataType: 'json',
            //async: false,
            beforeSend: function(){
                $('.mask').show();
                $('#loader').show();
            },
            success: function(response){

                for(var i = 0; i < response.length; i++) {

                    $('#processgroupid').append($('<option>', { 
                        value: response[i]['id'],
                        text : response[i]['name']
                    }));
                }
                if(response.length == 1){
                    $('#processgroupid').val(response[0]['id']).selectpicker("refresh").change();
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
    $('#processgroupid').selectpicker('refresh');
}

function getprocessbyprocessgroup(){
    
    var processgroupid = $("#processgroupid").val();
    var productid = $("#productid").val();
    var priceid = $("#priceid").val();
    var qty = $("#qty").val();
    var pricetype = $("#pricetype").val();

    $('#btnpanel,#inproducttable,#outproducttable,#saveestimatebtn').hide();
    $("#processgroup_maindiv").html('');

    if(processgroupid != 0){
        var uurl = SITE_URL+"process-group/getProcessByProcessGroupID";

        $.ajax({
            url: uurl,
            type: 'POST',
            data: {processgroupid:String(processgroupid),productid:String(productid),priceid:String(priceid),qty:String(qty),pricetype:pricetype},
            dataType: 'json',
            //async: false,
            beforeSend: function(){
                $('.mask').show();
                $('#loader').show();
            },
            success: function(response){
                if(response.length > 0){

                    var processHTML = ""; 
                    
                    for (var i = 0; i < response.length; i++) {
                        var sequenceno = i+1;
                        
                        var outproductdata = response[i]['outproductdata'];
                        var inproductdata = response[i]['inproductdata'];
                        
                        var out_producthtml = outproducthtml(sequenceno,outproductdata,qty);
                        var in_producthtml = inproducthtml(sequenceno,inproductdata,qty);
                        
                        var OUTPRODUCTDETAILHTML = '<div class="row m-n">\
                                                        <div class="panel-heading"><h2>OUT Product Material Details</h2></div>\
                                                        <div class="row m-n">\
                                                            <div class="col-md-3">\
                                                                <div class="form-group p-n">\
                                                                    <div class="col-sm-12 pr-sm">\
                                                                        <label class="control-label">Product</label>\
                                                                    </div>\
                                                                </div>\
                                                            </div>\
                                                            <div class="col-md-2">\
                                                                <div class="form-group p-n">\
                                                                    <div class="col-sm-12 pl-sm pr-sm">\
                                                                        <label class="control-label">Variant</label>\
                                                                    </div>\
                                                                </div>\
                                                            </div>\
                                                            <div class="col-md-2">\
                                                                <div class="form-group p-n">\
                                                                    <div class="col-sm-12 pl-sm pr-sm">\
                                                                        <label class="control-label">Price Type</label>\
                                                                    </div>\
                                                                </div>\
                                                            </div>\
                                                            <div class="col-md-1">\
                                                                <div class="form-group text-right">\
                                                                    <div class="col-sm-12 pl-sm">\
                                                                        <label class="control-label">Quantity</label>\
                                                                    </div>\
                                                                </div>\
                                                            </div>\
                                                            <div class="col-md-2">\
                                                                <div class="form-group text-right">\
                                                                    <div class="col-sm-12 pr-sm pl-sm">\
                                                                        <label class="control-label">Price ('+CURRENCY_CODE+')</label>\
                                                                    </div>\
                                                                </div>\
                                                            </div>\
                                                            <div class="col-md-1">\
                                                                <div class="form-group p-n">\
                                                                    <div class="col-sm-12 pl-sm pr-sm">\
                                                                        <label class="control-label">Unit</label>\
                                                                    </div>\
                                                                </div>\
                                                            </div>\
                                                        </div>\
                                                        '+out_producthtml+'\
                                                    </div>';
            
            
                        var INPRODUCTDETAILHTML = '<div class="row m-n">\
                                                    <div class="col-md-12"><hr></div>\
                                                    <div class="panel-heading"><h2>IN Details</h2></div>\
                                                    \
                                                    <div class="col-md-12"><hr></div>\
                                                    <div class="col-md-12">\
                                                        <div class="col-md-6 p-n" id="inproductlabel1_'+sequenceno+'">\
                                                            <div class="col-md-4 pr-xs pl-xs">\
                                                                <div class="form-group p-n">\
                                                                    <div class="col-sm-12">\
                                                                        <label class="control-label">Product</label>\
                                                                    </div>\
                                                                </div>\
                                                            </div>\
                                                            <div class="col-md-4 pr-xs pl-xs">\
                                                                <div class="form-group p-n">\
                                                                    <div class="col-sm-12">\
                                                                        <label class="control-label">Product Variant</label>\
                                                                    </div>\
                                                                </div>\
                                                            </div>\
                                                            <div class="col-md-2 pr-xs pl-xs">\
                                                                <div class="form-group p-n text-right">\
                                                                    <div class="col-sm-12">\
                                                                        <label class="control-label" style="padding-right: 22px !important;">Quantity</label>\
                                                                    </div>\
                                                                </div>\
                                                            </div>\
                                                        </div>\
                                                        <div class="col-md-6 p-n" id="inproductlabel2_'+sequenceno+'" style="display:none;">\
                                                            <div class="col-md-4 pr-xs pl-xs">\
                                                                <div class="form-group p-n">\
                                                                    <div class="col-sm-12">\
                                                                        <label class="control-label">Product</label>\
                                                                    </div>\
                                                                </div>\
                                                            </div>\
                                                            <div class="col-md-4 pr-xs pl-xs">\
                                                                <div class="form-group p-n">\
                                                                    <div class="col-sm-12">\
                                                                        <label class="control-label">Product Variant</label>\
                                                                    </div>\
                                                                </div>\
                                                            </div>\
                                                            <div class="col-md-2 pl-xs">\
                                                                <div class="form-group p-n text-right">\
                                                                    <div class="col-sm-12">\
                                                                        <label class="control-label">Quantity</label>\
                                                                    </div>\
                                                                </div>\
                                                            </div>\
                                                        </div>\
                                                    </div>\
                                                    \
                                                    <div class="col-md-12">\
                                                        '+in_producthtml+'\
                                                    </div>\
                                                </div>';
            
            
                            processHTML = '<div class="panel panel-default border-panel processsdetailsequence" id="processsdetailsequence'+sequenceno+'" style="transform:none;">\
                                                <div class="panel-heading collapse-process-panel border-filter-heading">\
                                                    <div class="col-md-8 pl-n">\
                                                        <h2>Process : '+response[i]['name']+'</h2>\
                                                        <input type="hidden" name="seqno[]" value="'+sequenceno+'">\
                                                    </div>\
                                                </div>\
                                                <div class="panel-body p-n pb-md">\
                                                    '+OUTPRODUCTDETAILHTML+' \
                                                    '+INPRODUCTDETAILHTML+'\
                                                </div>\
                                            </div>';

                        $("#processgroup_maindiv").append(processHTML);
                    }
                    
                    // $("#processgroup_maindiv").html(processHTML);
                    $('.processsdetailsequence').each(function(){
                        var seqno = $(this).attr('id').match(/\d+/);
                       
                        $('.countoutproducts'+seqno).each(function(){
                            var elementid = $(this).attr("id").split('_');
                            var rowid = elementid[1];
                            var productid = $("#outproductid"+seqno+"_"+rowid).attr("data-val");
                            var priceid = $("#outproductvariantid"+seqno+"_"+rowid).attr("data-val");
                            var unitid = $("#unitid"+seqno+"_"+rowid).attr("data-val");

                            $("#outproductid"+seqno+"_"+rowid).val(productid);
                            $("#outproductid"+seqno+"_"+rowid).find('option').not(':selected').remove();
                            
                            getproductvariant(seqno,rowid,1,productid);

                            $("#outproductvariantid"+seqno+"_"+rowid).val(priceid);
                            $("#outproductvariantid"+seqno+"_"+rowid).find('option').not(':selected').remove();

                            $("#unitid"+seqno+"_"+rowid).val(unitid);
                            $("#unitid"+seqno+"_"+rowid).find('option').not(':selected').remove();
                        });  

                        $('.countinproducts'+seqno).each(function(){
                            var elementid = $(this).attr("id").split('_');
                            var rowid = elementid[1];
                            var productid = $("#inproductid"+seqno+"_"+rowid).attr("data-val");
                            var priceid = $("#inproductvariantid"+seqno+"_"+rowid).attr("data-val");

                            $("#inproductid"+seqno+"_"+rowid).val(productid);
                            $("#inproductid"+seqno+"_"+rowid).find('option').not(':selected').remove();

                            getproductvariant(seqno,rowid,0,productid);

                            $("#inproductvariantid"+seqno+"_"+rowid).val(priceid);
                            $("#inproductvariantid"+seqno+"_"+rowid).find('option').not(':selected').remove();
                        });    

                        if($('.countinproducts'+seqno).length > 1){
                            $("#inproductlabel2_"+seqno).show();
                        }else{
                            $("#inproductlabel2_"+seqno).hide();
                        }

                        $(".add_outproduct_btn"+seqno).hide();
                        $(".add_outproduct_btn"+seqno+":last").show();
                        if ($(".remove_outproduct_btn"+seqno+":visible").length == 1) {
                            $(".remove_outproduct_btn"+seqno+":first").hide();
                        }

                        $(".add_inproduct_btn"+seqno).hide();
                        $(".add_inproduct_btn"+seqno+":last").show();
                        if ($(".remove_inproduct_btn"+seqno+":visible").length == 1) {
                            $(".remove_inproduct_btn"+seqno+":first").hide();
                        }
                        
                    });
                    $(".spin").TouchSpin(touchspinoptions);
                    //$('.outproductid').find('option').not(':selected').remove();
                    $('.selectpicker').selectpicker('refresh');
                    //$('.outproductid').selectpicker('refresh');
                    $('#btnpanel').show();
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
    }else{
        $("#processgroup_maindiv").html('');
    }
}

function addnewoutproduct(sequenceno){
    
    // var rowcount = parseInt($(".countinproducts"+sequenceno+":last").attr("id").match(/\d+/))+1;
    var elementid = $(".countoutproducts"+sequenceno+":last").attr("id").split('_');
    var rowcount = parseInt(elementid[1])+1;
    var pricetype = $("#pricetype").val();

    var datahtml = '<div class="countoutproducts'+sequenceno+' col-md-12 p-n" id="countoutproducts'+sequenceno+'_'+rowcount+'">\
                        <div class="col-md-3">\
                            <div class="form-group p-n" id="outproduct'+sequenceno+'_'+rowcount+'_div">\
                            <div class="col-sm-12 pr-xs">\
                                <select id="outproductid'+sequenceno+'_'+rowcount+'" name="outproductid[]" class="selectpicker form-control outproductid" data-live-search="true" data-select-on-tab="true" data-size="8">\
                                    <option value="0">Select Product</option>\
                                    '+PRODUCT_DATA+'\
                                </select>\
                            </div>\
                            </div>\
                        </div>\
                        <div class="col-md-2">\
                            <div class="form-group p-n" id="outproductvariant'+sequenceno+'_'+rowcount+'_div">\
                                <div class="col-sm-12 pl-xs pr-xs">\
                                    <select id="outproductvariantid'+sequenceno+'_'+rowcount+'" name="outproductvariantid[]" class="selectpicker form-control outproductvariantid" data-live-search="true" data-select-on-tab="true" data-size="8">\
                                        <option value="0">Select Variant</option>\
                                    </select>\
                                    <input type="hidden" id="outprice_'+sequenceno+'_'+rowcount+'" value="0">\
                                    <input type="hidden" id="outproductamount'+sequenceno+'_'+rowcount+'" class="outproductamount" value="0">\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-2">\
                            <div class="form-group p-n" id="outpricetype'+sequenceno+'_'+rowcount+'_div">\
                                <div class="col-sm-12 pr-xs pl-sm">\
                                    <select id="outpricetype'+sequenceno+'_'+rowcount+'" name="outpricetype'+sequenceno+'[]" class="selectpicker form-control outpricetype" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                        <option value="0" '+(pricetype==0?"selected":"")+'>FIFO Base</option>\
                                        <option value="1" '+(pricetype==1?"selected":"")+'>Avg. Price</option>\
                                        <option value="2" '+(pricetype==2?"selected":"")+'>Latest Price</option>\
                                        <option value="3" '+(pricetype==3?"selected":"")+'>Low Price</option>\
                                    </select>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-1">\
                            <div class="form-group" id="outqty'+sequenceno+'_'+rowcount+'_div">\
                                <div class="col-sm-12 pr-xs pl-xs">\
                                    <input type="text" class="form-control spin outqty text-right" id="outqty'+sequenceno+'_'+rowcount+'" name="outqty[]" value="" onkeypress="'+(MANAGE_DECIMAL_QTY==1?"return decimal_number_validation(event, this.value,8);":"return isNumber(event);")+'" style="display: block;">\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-2">\
                            <div class="form-group" id="outprice'+sequenceno+'_'+rowcount+'_div">\
                                <div class="col-sm-12 pr-xs pl-xs">\
                                    <input type="text" class="form-control outprice text-right" id="outprice'+sequenceno+'_'+rowcount+'" name="outprice[]" value="" onkeypress="return decimal_number_validation(event, this.value, 8);">\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-1">\
                            <div class="form-group p-n" id="unit'+sequenceno+'_'+rowcount+'_div">\
                            <div class="col-sm-12 pl-xs pr-xs">\
                                <select id="unitid'+sequenceno+'_'+rowcount+'" name="unitid[]" class="selectpicker form-control unitid" data-live-search="true" data-select-on-tab="true" data-size="8">\
                                    <option value="0">Select Unit</option>\
                                    '+UNIT_DATA+'\
                                </select>\
                            </div>\
                            </div>\
                        </div>\
                        <div class="col-md-1 pt-md pl-xs">\
                            <button type="button" class="btn btn-default btn-raised remove_outproduct_btn'+sequenceno+'" onclick="removeoutproduct('+sequenceno+','+rowcount+')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
                            <button type="button" class="btn btn-default btn-raised add_outproduct_btn'+sequenceno+'" onclick="addnewoutproduct('+sequenceno+')" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>\
                        </div>\
                    </div>';
    
    $(".remove_outproduct_btn"+sequenceno+":first").show();
    $(".add_outproduct_btn"+sequenceno+":last").hide();
    $("#countoutproducts"+sequenceno+"_"+(rowcount-1)).after(datahtml);
    
    $(".selectpicker").selectpicker("refresh");

    $(".spin").TouchSpin(touchspinoptions);
}
function removeoutproduct(sequenceno,rowid){

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
                        <div class="col-md-4 pr-xs pl-xs">\
                            <div class="form-group p-n" id="inproduct'+sequenceno+'_'+rowcount+'_div">\
                                <div class="col-sm-12">\
                                    <select id="inproductid'+sequenceno+'_'+rowcount+'" name="inproductid'+sequenceno+'[]" class="selectpicker form-control inproductid" data-live-search="true" data-select-on-tab="true" data-size="8">\
                                        <option value="0">Select Product</option>\
                                        '+PRODUCT_DATA+'\
                                    </select>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-4 pr-xs pl-xs">\
                            <div class="form-group p-n" id="inproductvariant'+sequenceno+'_'+rowcount+'_div">\
                            <div class="col-sm-12">\
                                <select id="inproductvariantid'+sequenceno+'_'+rowcount+'" name="inproductvariantid'+sequenceno+'[]" class="selectpicker form-control inproductvariantid" data-live-search="true" data-select-on-tab="true" data-size="8">\
                                    <option value="0">Select Variant</option>\
                                </select>\
                                <input type="hidden" id="inprice'+sequenceno+'_'+rowcount+'" value="0">\
                                <input type="hidden" id="inproductamount'+sequenceno+'_'+rowcount+'" class="inproductamount" value="0">\
                            </div>\
                            </div>\
                        </div>\
                        <div class="col-md-2 pr-xs pl-xs">\
                            <div class="form-group p-n" id="inqty'+sequenceno+'_'+rowcount+'_div">\
                                <div class="col-sm-12">\
                                    <input type="text" class="form-control spin inqty text-right" id="inqty'+sequenceno+'_'+rowcount+'" name="inqty'+sequenceno+'[]" value="" onkeypress="'+(MANAGE_DECIMAL_QTY==1?"return decimal_number_validation(event, this.value,8);":"return isNumber(event);")+'" style="display: block;">\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-2 pt-md pl-xs">\
                            <button type="button" class="btn btn-default btn-raised remove_inproduct_btn'+sequenceno+'" onclick="removeinproduct('+sequenceno+','+rowcount+')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
                            <button type="button" class="btn btn-default btn-raised add_inproduct_btn'+sequenceno+'" onclick="addnewinproduct('+sequenceno+')" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>\
                        </div>\
                    </div>';
    
    $(".remove_inproduct_btn"+sequenceno+":first").show();
    $(".add_inproduct_btn"+sequenceno+":last").hide();
    $("#countinproducts"+sequenceno+"_"+(rowcount-1)).after(datahtml);
    
    $("#inproductid"+sequenceno+"_"+rowcount+",#inproductvariantid"+sequenceno+"_"+rowcount).selectpicker("refresh");
    $(".spin").TouchSpin(touchspinoptions);

    if($(".countinproducts"+sequenceno).length == 1){
        $("#inproductlabel2_"+sequenceno).hide();
    }else{
        $("#inproductlabel2_"+sequenceno).show();
    }  
}
function removeinproduct(sequenceno,rowid){

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
function getproductvariant(sequenceno,rowid,processtype,productid){
   
    var productelement = $('#inproductid'+sequenceno+'_'+rowid);
    var productvariantelement = $('#inproductvariantid'+sequenceno+'_'+rowid);
    if(processtype == 1){
        productelement = $('#outproductid'+sequenceno+'_'+rowid);
        productvariantelement = $('#outproductvariantid'+sequenceno+'_'+rowid);
    }
    productvariantelement.find('option')
                    .remove()
                    .end()
                    .append('<option value="">Select Variant</option>')
                    .val('0')
                ;
    productvariantelement.selectpicker('refresh');
   
    if(productid != '0'){
        var productvariant = [];
        if(processtype == 1){
            productvariant = JSON.parse($("#outproductid"+sequenceno+"_"+rowid+" option:selected").attr("data-variants"));
        }else{
            productvariant = JSON.parse($("#inproductid"+sequenceno+"_"+rowid+" option:selected").attr("data-variants"));
        }
        // console.log(productvariant);
        for(var i = 0; i < productvariant.length; i++) {

            if(processtype == 1){
                productvariantelement.append($('<option>', { 
                    value: productvariant[i]['id'],
                    text : productvariant[i]['variantname'],
                    "data-price": productvariant[i]['price'],
                    "data-fifo-price": productvariant[i]['fifo_price'],
                    "data-average-price": productvariant[i]['average_price'],
                    "data-latest-price": productvariant[i]['latest_price'],
                    "data-low-price": productvariant[i]['low_price'],
                }));
            }else{
                productvariantelement.append($('<option>', { 
                    value: productvariant[i]['id'],
                    text : productvariant[i]['variantname'],
                    "data-price": productvariant[i]['price'],
                }));
            }

        }
        if(productvariant.length == 1){
            productvariantelement.val(productvariant[0]['id']);
        }
    }
    productvariantelement.selectpicker('refresh');
}
/* function getproductvariant(sequenceno,rowid,processtype,productid){
   
    var productelement = $('#inproductid'+sequenceno+'_'+rowid);
    var productvariantelement = $('#inproductvariantid'+sequenceno+'_'+rowid);
    if(processtype == 1){
        productelement = $('#outproductid'+sequenceno+'_'+rowid);
        productvariantelement = $('#outproductvariantid'+sequenceno+'_'+rowid);
    }
    productvariantelement.find('option')
                    .remove()
                    .end()
                    .append('<option value="">Select Variant</option>')
                    .val('0')
                ;
    productvariantelement.selectpicker('refresh');
   
    if(productid != '0'){
      var uurl = SITE_URL+"process-group/getProductVariantByProductId";
      
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {productid:String(productid),type:2},
        dataType: 'json',
        async: false,
        beforeSend: function(){
            $('.mask').show();
            $('#loader').show();
        },
        success: function(response){
  
            for(var i = 0; i < response.length; i++) {
                productvariantelement.append($('<option>', { 
                    value: response[i]['id'],
                    text : response[i]['variantname'],
                    "data-price": response[i]['price'],
                }));
            }
            if(response.length == 1){
                productvariantelement.val(response[0]['id']);
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
    productvariantelement.selectpicker('refresh');
} */
function outproducthtml(sequenceno,productdata,qty){
    console.log(productdata)
    var html = "";
    var length = (productdata.length>0)?productdata.length:1;
    var pricetype = $("#pricetype").val();

    if(length > 0){
        for(var i=0;i<length;i++){

            var productid = priceid = unitid = price = amount = "0";
            // var qty = "1";
            if(!$.isEmptyObject(productdata)){
                productid = productdata[i]['productid'];
                priceid = productdata[i]['productpriceid'];
                unitid = productdata[i]['unitid'];
                price = productdata[i]['price'];
                amount = parseFloat(price)*parseFloat(qty);
            }
            html += '<div class="countoutproducts'+sequenceno+' col-md-12 p-n" id="countoutproducts'+sequenceno+'_'+(i+1)+'">\
                        <div class="col-md-3">\
                            <div class="form-group p-n" id="outproduct'+sequenceno+'_'+(i+1)+'_div">\
                                <div class="col-sm-12 pr-xs">\
                                    <select id="outproductid'+sequenceno+'_'+(i+1)+'" name="outproductid'+sequenceno+'[]" class="selectpicker form-control outproductid" data-live-search="true" data-select-on-tab="true" data-size="5" data-val="'+productid+'">\
                                        <option value="0">Select Product</option>\
                                        '+PRODUCT_DATA+'\
                                    </select>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-2">\
                            <div class="form-group p-n" id="outproductvariant'+sequenceno+'_'+(i+1)+'_div">\
                                <div class="col-sm-12 pr-xs pl-xs">\
                                    <select id="outproductvariantid'+sequenceno+'_'+(i+1)+'" name="outproductvariantid[]" class="selectpicker form-control outproductvariantid" data-live-search="true" data-select-on-tab="true" data-size="5" data-val="'+priceid+'">\
                                        <option value="0">Select Variant</option>\
                                    </select>\
                                    <input type="hidden" id="outprice_'+sequenceno+'_'+(i+1)+'" value="'+parseFloat(price)+'">\
                                    <input type="hidden" id="outproductamount'+sequenceno+'_'+(i+1)+'" class="outproductamount" value="'+parseFloat(amount)+'">\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-2">\
                            <div class="form-group p-n" id="outpricetype'+sequenceno+'_'+(i+1)+'_div">\
                                <div class="col-sm-12 pr-xs pl-xs">\
                                    <select id="outpricetype'+sequenceno+'_'+(i+1)+'" name="outpricetype'+sequenceno+'[]" class="selectpicker form-control outpricetype" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                        <option value="0" '+(pricetype==0?"selected":"")+'>FIFO Base</option>\
                                        <option value="1" '+(pricetype==1?"selected":"")+'>Avg. Price</option>\
                                        <option value="2" '+(pricetype==2?"selected":"")+'>Latest Price</option>\
                                        <option value="3" '+(pricetype==3?"selected":"")+'>Low Price</option>\
                                    </select>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-1">\
                            <div class="form-group" id="outqty'+sequenceno+'_'+(i+1)+'_div">\
                                <div class="col-sm-12 pr-xs pl-xs">\
                                    <input type="text" class="form-control spin outqty text-right" id="outqty'+sequenceno+'_'+(i+1)+'" name="outqty[]" value="'+parseFloat(qty).toFixed(2)+'" onkeypress="'+(MANAGE_DECIMAL_QTY==1?"return decimal_number_validation(event, this.value,8);":"return isNumber(event);")+'" style="display: block;">\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-2">\
                            <div class="form-group" id="outprice'+sequenceno+'_'+(i+1)+'_div">\
                                <div class="col-sm-12 pr-xs pl-xs">\
                                    <input type="text" class="form-control outprice text-right" id="outprice'+sequenceno+'_'+(i+1)+'" name="outprice[]" value="'+parseFloat(price).toFixed(2)+'" onkeypress="return decimal_number_validation(event, this.value, 8);">\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-1">\
                            <div class="form-group p-n" id="unit'+sequenceno+'_'+(i+1)+'_div">\
                                <div class="col-sm-12 pr-xs pl-xs">\
                                    <select id="unitid'+sequenceno+'_'+(i+1)+'" name="unitid[]" class="selectpicker form-control unitid" data-live-search="true" data-select-on-tab="true" data-size="5" data-val="'+unitid+'">\
                                        <option value="0">Select Unit</option>\
                                        '+UNIT_DATA+'\
                                    </select>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-1 pt-md pl-xs">\
                            <button type="button" class="btn btn-default btn-raised remove_outproduct_btn'+sequenceno+'" onclick="removeoutproduct('+sequenceno+','+(i+1)+')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
                            <button type="button" class="btn btn-default btn-raised add_outproduct_btn'+sequenceno+'" onclick="addnewoutproduct('+sequenceno+')" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>\
                        </div>\
                    </div>';
        }
    }

    return html;
}
function inproducthtml(sequenceno,productdata,qty){
    
    var html = "";
    var length = (productdata.length>0)?productdata.length:1;

    if(length > 0){
        for(var i=0;i<length;i++){

            var productid = priceid = price = "0";
            // var qty = "1";
            if(!$.isEmptyObject(productdata)){
                productid = productdata[i]['productid'];
                priceid = productdata[i]['productpriceid'];
                price = productdata[i]['price'];
            }
            html += '<div class="countinproducts'+sequenceno+' col-md-6 p-n" id="countinproducts'+sequenceno+'_'+(i+1)+'">\
                        <div class="col-md-4 pr-xs pl-xs">\
                            <div class="form-group p-n" id="inproduct'+sequenceno+'_'+(i+1)+'_div">\
                                <div class="col-sm-12">\
                                    <select id="inproductid'+sequenceno+'_'+(i+1)+'" name="inproductid'+sequenceno+'[]" class="selectpicker form-control inproductid" data-live-search="true" data-select-on-tab="true" data-size="5" data-val="'+productid+'">\
                                        <option value="0">Select Product</option>\
                                        '+PRODUCT_DATA+'\
                                    </select>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-4 pr-xs pl-xs">\
                            <div class="form-group p-n" id="inproductvariant'+sequenceno+'_'+(i+1)+'_div">\
                                <div class="col-sm-12">\
                                    <select id="inproductvariantid'+sequenceno+'_'+(i+1)+'" name="inproductvariantid[]" class="selectpicker form-control inproductvariantid" data-live-search="true" data-select-on-tab="true" data-size="5" data-val="'+priceid+'">\
                                        <option value="0">Select Variant</option>\
                                    </select>\
                                    <input type="hidden" id="inprice'+sequenceno+'_'+(i+1)+'" value="'+parseFloat(price)+'">\
                                    <input type="hidden" id="inproductamount'+sequenceno+'_'+(i+1)+'" class="inproductamount" value="'+parseFloat(price)+'">\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-2 pr-xs pl-xs">\
                            <div class="form-group p-n" id="inqty'+sequenceno+'_'+(i+1)+'_div">\
                                <div class="col-sm-12">\
                                    <input type="text" class="form-control spin inqty text-right" id="inqty'+sequenceno+'_'+(i+1)+'" name="inqty[]" value="'+parseFloat(qty).toFixed(2)+'" onkeypress="'+(MANAGE_DECIMAL_QTY==1?"return decimal_number_validation(event, this.value,8);":"return isNumber(event);")+'" style="display: block;">\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-2 pt-md pl-xs">\
                            <button type="button" class="btn btn-default btn-raised remove_inproduct_btn'+sequenceno+'" onclick="removeinproduct('+sequenceno+','+(i+1)+')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
                            <button type="button" class="btn btn-default btn-raised add_inproduct_btn'+sequenceno+'" onclick="addnewinproduct('+sequenceno+')" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>\
                        </div>\
                    </div>';
        }
    }

    return html;
}
function generateestimate(){

    var totaloutproducts = [];
    var totalinproducts = [];
    var totalinproductscal = [];
    var productsdata = [];

    var isduplicateoutproduct = [];
    var isduplicateinproduct = [];
    var isduplicateinproductcal = [];

    var arrkey = inarrkey = inarrkeycal = 0;
    $("select.outproductid").each(function(index){
        var elementid = $(this).attr("id").split('_');
        var sequenceno = elementid[0].match(/\d+/);
        var divid = elementid[1];
        var eleid = sequenceno+"_"+divid;

        var outproductid = this.value;
        var outproductvariantid = $("#outproductvariantid"+eleid).val();
        var unitid = $("#unitid"+eleid).val();
        var outqty = $("#outqty"+eleid).val();
        var outprice = $("#outprice"+eleid).val();
        var outproductamount = $("#outproductamount"+eleid).val();
        var text = outproductid+"_"+outproductvariantid+"_"+unitid+"_"+parseFloat(outprice).toFixed(2);

        if(outproductid > 0 && outproductvariantid > 0 && unitid > 0 && outqty > 0){
            
            var productname = $("#outproductid"+eleid+" option:selected").text();
            var variantname = $("#outproductvariantid"+eleid+" option:selected").text();
            var unit = $("#unitid"+eleid+" option:selected").text();
            
            if(isduplicateoutproduct.includes(text) == false){
                isduplicateoutproduct.push(text);
                
                totaloutproducts[arrkey] = {"productname":productname,
                                            "variantname":variantname,
                                            "unit":unit,
                                            "price":parseFloat(outprice),
                                            "qty":outqty,
                                            "amount":parseFloat(parseFloat(outprice)*parseFloat(outqty)),
                                            "text":text
                                        }; 

                arrkey++;
            }else{

                var iskey = Object.keys(isduplicateoutproduct).find(key => isduplicateoutproduct[key] === text);
                totaloutproducts[iskey]['qty'] = parseFloat(totaloutproducts[iskey]['qty']) + parseFloat(outqty);
                totaloutproducts[iskey]['amount'] = parseFloat(parseFloat(outprice)*parseFloat(totaloutproducts[iskey]['qty']));
            }


        }
    });
    
    var outproducttable = "";
    if(totaloutproducts.length > 0){
        var totaloutproductamount = totaloutqty = 0;
        for(var j = 0; j < totaloutproducts.length; j++) {

            totaloutproductamount = parseFloat(totaloutproductamount) + parseFloat(totaloutproducts[j]['amount']);
            totaloutqty = parseFloat(totaloutqty) + parseFloat(totaloutproducts[j]['qty']);

            outproducttable += '<tr class="cnttblrow">';
            outproducttable += '<td class="text-center">'+(j+1)+'</td>';
            outproducttable += '<td>'+totaloutproducts[j]['productname']+'</td>';
            outproducttable += '<td>'+totaloutproducts[j]['variantname']+'</td>';
            outproducttable += '<td>'+totaloutproducts[j]['unit']+'</td>';
            outproducttable += '<td class="text-right">'+format.format(totaloutproducts[j]['price'])+'</td>';
            outproducttable += '<td class="text-right">'+format.format(totaloutproducts[j]['qty'])+'</td>';
            outproducttable += '<td class="text-right">'+format.format(totaloutproducts[j]['amount'])+'</td>';
            outproducttable += '</tr>';
        }
        outproducttable += '<tr>\
                                <th colspan="5" class="text-right">Total</th>\
                                <th class="text-right">'+format.format(totaloutqty)+'</th>\
                                <th class="text-right">'+format.format(totaloutproductamount)+'</th>\
                            </tr>';
    }else{
        outproducttable += '<tr>\
                                <th colspan="7" class="text-center">No data available.</th>\
                            </tr>';
    }
    $("#outproducttable tbody").html(outproducttable);

    $("select.inproductid").each(function(index){
        var elementid = $(this).attr("id").split('_');
        var sequenceno = elementid[0].match(/\d+/);
        var divid = elementid[1];
        var eleid = sequenceno+"_"+divid;

        var inproductid = this.value;
        var inproductvariantid = $("#inproductvariantid"+eleid).val();
        var inqty = $("#inqty"+eleid).val();
        // var inprice = $("#inprice"+eleid).val();
        
        var inprice = totaloutproductamount*inqty;
        
        var inproductamount = $("#inproductamount"+eleid).val();
        var text = inproductid+"_"+inproductvariantid+"_"+parseFloat(inprice).toFixed(2);

        if(inproductid > 0 && inproductvariantid > 0 && inqty > 0){
            
            var productname = $("#inproductid"+eleid+" option:selected").text();
            var variantname = $("#inproductvariantid"+eleid+" option:selected").text();
            
            if(isduplicateinproductcal.includes(text) == false){
                isduplicateinproductcal.push(text);
                
                totalinproductscal[inarrkeycal] = {"productname":productname,
                                            "variantname":variantname,
                                            "price":parseFloat(inprice),
                                            "qty":inqty,
                                            "amount":parseFloat(parseFloat(inprice)*parseFloat(inqty)),
                                        }; 

                                        inarrkeycal++;
            }else{

                var iskey = Object.keys(isduplicateinproductcal).find(key => isduplicateinproductcal[key] === text);
                totalinproductscal[iskey]['qty'] = parseFloat(totalinproductscal[iskey]['qty']) + parseFloat(inqty);
                totalinproductscal[iskey]['amount'] = parseFloat(parseFloat(inprice)*parseFloat(totalinproductscal[iskey]['qty']));
            }
        }
    });

    var totalinqtyforcal = 0;
    if(totalinproductscal.length > 0){
        var totalinproductamount = totalinqty = 0;
        for(var i = 0; i < totalinproductscal.length; i++) {

            totalinproductamount = parseFloat(totalinproductamount) + parseFloat(totalinproductscal[i]['amount']);
            totalinqty = parseFloat(totalinqty) + parseFloat(totalinproductscal[i]['qty']);
        }
        var totalinqtyforcal = totalinqty;
    }

    $("select.inproductid").each(function(index){
        var elementid = $(this).attr("id").split('_');
        var sequenceno = elementid[0].match(/\d+/);
        var divid = elementid[1];
        var eleid = sequenceno+"_"+divid;

        var inproductid = this.value;
        var inproductvariantid = $("#inproductvariantid"+eleid).val();
        var inqty = $("#inqty"+eleid).val();
        // var inprice = $("#inprice"+eleid).val();
        
        var inprice = (parseFloat(totaloutproductamount)*parseFloat(inqty))/parseFloat(totalinqtyforcal);
        
        var inproductamount = $("#inproductamount"+eleid).val();
        var text = inproductid+"_"+inproductvariantid+"_"+parseFloat(inprice).toFixed(2);

        if(inproductid > 0 && inproductvariantid > 0 && inqty > 0){
            
            var productname = $("#inproductid"+eleid+" option:selected").text();
            var variantname = $("#inproductvariantid"+eleid+" option:selected").text();
            
            if(isduplicateinproduct.includes(text) == false){
                isduplicateinproduct.push(text);
                
                totalinproducts[inarrkey] = {"productname":productname,
                                            "variantname":variantname,
                                            "price":parseFloat(inprice),
                                            "qty":inqty,
                                            "amount":parseFloat(parseFloat(inprice)*parseFloat(inqty)),
                                        }; 

                inarrkey++;
            }else{

                var iskey = Object.keys(isduplicateinproduct).find(key => isduplicateinproduct[key] === text);
                totalinproducts[iskey]['qty'] = parseFloat(totalinproducts[iskey]['qty']) + parseFloat(inqty);
                totalinproducts[iskey]['amount'] = parseFloat(parseFloat(inprice)*parseFloat(totalinproducts[iskey]['qty']));
            }
        }
    });

    var inproducttable = "";
    if(totalinproducts.length > 0){
        var totalinproductamount = totalinqty = 0;
        for(var i = 0; i < totalinproducts.length; i++) {

            totalinproductamount = parseFloat(totalinproductamount) + parseFloat(totalinproducts[i]['amount']);
            totalinqty = parseFloat(totalinqty) + parseFloat(totalinproducts[i]['qty']);
            
            inproducttable += '<tr class="cnttblrow">';
            inproducttable += '<td class="text-center">'+(i+1)+'</td>';
            inproducttable += '<td>'+totalinproducts[i]['productname']+'</td>';
            inproducttable += '<td>'+totalinproducts[i]['variantname']+'</td>';
            inproducttable += '<td class="text-right">'+format.format(totalinproducts[i]['price'])+'</td>';
            inproducttable += '<td class="text-right">'+format.format(totalinproducts[i]['qty'])+'</td>';
            inproducttable += '<td class="text-right">'+format.format(totalinproducts[i]['amount'])+'</td>';
            inproducttable += '</tr>';
        }
        inproducttable += '<tr>\
                                <th colspan="4" class="text-right">Total</th>\
                                <th class="text-right">'+format.format(totalinqty)+'</th>\
                                <th class="text-right">'+format.format(totalinproductamount)+'</th>\
                            </tr>';
    }else{
        inproducttable += '<tr>\
                                <th colspan="6" class="text-center">No data available.</th>\
                            </tr>';
    }
    $("#inproducttable tbody").html(inproducttable);

    $("#inproducttable,#outproducttable,#exportbtn,#saveestimatebtn").show();

    productsdata.push(totaloutproducts);
    productsdata.push(totalinproducts);

    $("#productjsondata").val(JSON.stringify(productsdata));
}

function exporttopdfestimate(){
    
    var html = $("#productjsondata").val();
    var totalRecords = $(".cnttblrow").length; 

    $.skylo('end');
    if(totalRecords > 0){ 
        window.location= SITE_URL+"estimate/exporttopdfestimate?content="+html;
    }else{
        new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }
}
function printestimate(){
    
    var html = $("#totalestimate").html();
    var totalRecords = $(".cnttblrow").length; 

    if(totalRecords > 0){ 
        
      var uurl = SITE_URL + "estimate/printestimate";
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {content: html},
        //dataType: 'json',
        //async: false,
        beforeSend: function() {
            $('.mask').show();
            $('#loader').show();
        },
        success: function(response) {
            var data = JSON.parse(response);
            var html = data['content'];

            var frame1 = document.createElement("iframe");
            frame1.name = "frame1";
            frame1.style.position = "absolute";
            frame1.style.top = "-1000000px";
            document.body.appendChild(frame1);
            var frameDoc = frame1.contentWindow ? frame1.contentWindow : frame1.contentDocument.document ? frame1.contentDocument.document : frame1.contentDocument;
            frameDoc.document.open();
            frameDoc.document.write(html);
            frameDoc.document.close();
            setTimeout(function () {
            window.frames["frame1"].focus();
            window.frames["frame1"].print();
            document.body.removeChild(frame1);
            }, 500);
        },
        error: function(xhr) {
            //alert(xhr.responseText);
        },
        complete: function() {
            $('.mask').hide();
            $('#loader').hide();
        },
      });
    }else{
        new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }
}

function openestimatepopup(){
    
    var totalRecords = $(".cnttblrow").length; 

    if(totalRecords > 0){ 
        $("#saveModal").modal("show");
        $("#estimatename").val("");
        $("#estimatename_div").removeClass("has-error is-focused");
        $("#namealert").html("");
    }else{
        new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }
}

function checkvalidationforsaveestimate(){
    var estimatename = $("#estimatename").val().trim();
    var isvalidestimatename = 0;

    $("#namealert").html('');
    if(estimatename == ''){
        $("#estimatename_div").addClass("has-error is-focused");
        $("#namealert").html('<i class="fa fa-exclamation-triangle"></i> Please enter estimate name !');
    }else { 
        if(estimatename.length<2){
            $("#estimatename_div").addClass("has-error is-focused");
            $("#namealert").html('<i class="fa fa-exclamation-triangle"></i> Estimate name require minimum 2 characters !');
        }else{
            $("#estimatename_div").removeClass("has-error is-focused");
            isvalidestimatename = 1;
        }
    }

    if(isvalidestimatename==1){
        var uurl = SITE_URL+"estimate/save-estimate";
        var html = $("#productjsondata").val();
        $.ajax({
            url: uurl,
            type: 'POST',
            data: {estimatename:estimatename,content:html},
            beforeSend: function(){
                $('.mask').show();
                $('#loader').show();
            },
            success: function(response){
                if(response==1){
                    $("#saveModal").modal("hide");
                    new PNotify({title: "Estimate saved successfully.",styling: 'fontawesome',delay: '3000',type: 'success'});
                }else if(response==2){
                    $("#namealert").html('<i class="fa fa-exclamation-triangle"></i> Estimate name already exists !');
                }else{
                    $("#namealert").html('<i class="fa fa-exclamation-triangle"></i> Estimate not saved !');
                }
            },
            complete: function(){
                $('.mask').hide();
                $('#loader').hide();
            },
            error: function(xhr) {
            //alert(xhr.responseText);
            }
        }); 
    }
}
