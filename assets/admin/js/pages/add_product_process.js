var PROCESS_GROUP_IDS_ARRAY = [];
$(document).ready(function() {
    $('.yesno input[type="checkbox"]').bootstrapToggle({
        on: 'Yes',
        off: 'No',
        onstyle: 'primary',
        offstyle: 'danger'
    });
    /*$('#transactiondate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        todayBtn:"linked",
    });
    $('#estimatedate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        todayBtn:"linked",
        startDate: new Date()
    }); */
    $('.docdate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        todayBtn:"linked",
    });
    
    $('#processgroupid').on('change', function (e) {
        generateproductprocess();
        PROCESS_GROUP_IDS_ARRAY = JSON.parse("[" + $(this).val() + "]");;
        // getprocess();
        
        $(".pg").each(function(){
            var pgid = parseInt($(this).attr("id").match(/\d+/));
            
            var isdelete = 1;
            for(var l=0;l<PROCESS_GROUP_IDS_ARRAY.length;l++){
                if(PROCESS_GROUP_IDS_ARRAY[l] == pgid){
                    isdelete = 0;
                }
            }
            if(isdelete == 1){
                $("#pg"+pgid).remove();
            }
        });
    });
    
    if(PROCESSTYPE == "IN"){
        PRODUCT_OPTION_DATA_ARR=[];
        var ppgid = $("#postprocessgroupid").val();
        getprocess(ppgid);
        getproduct(ppgid);
        getmachine(ppgid);
        // getproductunit();

        $(".add_inproduct_btn"+ppgid).hide();
        $(".add_inproduct_btn"+ppgid+":last").show();
 
        getinproduct(ppgid);
        
        $(".add_incharges_btn"+ppgid).hide();
        $(".add_incharges_btn"+ppgid+":last").show();
    }
    if(PROCESSTYPE == "REPROCESS"){
        generateproductprocess();
    }
    if(PROCESSGROUPID != ""){
        generateproductprocess();
    }
    if(ACTION==1 && PROCESSTYPE != "IN"){
        // getprocess($("#processgroupid").val()); 
        generateproductprocess();
    }

    if(PROCESSTYPE=="IN"){
        var ppgid = $("#postprocessgroupid").val();

        $('#transactiondate'+ppgid).datepicker({
            todayHighlight: true,
            format: 'dd/mm/yyyy',
            todayBtn:"linked",
        });
        $('#estimatedate'+ppgid).datepicker({
            todayHighlight: true,
            format: 'dd/mm/yyyy',
            todayBtn:"linked",
            startDate: new Date()
        });
        $(document).on('change','select.inproductid'+ppgid, function (e) {
            // var divid = $(this).attr('id').match(/\d+/);
            var element = $(this).attr('id').split('_');
            var divid = element[2];
            getinproductvariant(divid,ppgid);
            $("#inprice_"+ppgid+"_"+divid).val($("#inproductvariantid_"+ppgid+"_"+divid+" option:selected").attr("data-price"));
        });
        $(document).on('change','select.inproductvariantid'+ppgid, function (e) {
            // var divid = $(this).attr('id').match(/\d+/);
            var element = $(this).attr('id').split('_');
            var divid = element[2];
            $("#inprice_"+ppgid+"_"+divid).val($("#inproductvariantid_"+ppgid+"_"+divid+" option:selected").attr("data-price"));
            var price = 0;
            if($("#inquantity_"+ppgid+"_"+divid).val()!=""){
                price = parseFloat($("#inproductvariantid_"+ppgid+"_"+divid+" option:selected").attr("data-price")) * parseFloat($("#inquantity_"+ppgid+"_"+divid).val());
            }
            $("#inproductamount_"+ppgid+"_"+divid).val(parseFloat(price).toFixed(2));
        });
        $(document).on('keyup','.stockqty'+ppgid, function (e) {
            var element = $(this).attr('id').split("_");
            var divid = "_"+element[1]+"_"+element[2]+"_"+element[3];
            
            var outqty = $("#outqty"+divid).val();
            
            if(this.value!="" && parseFloat(this.value) > parseFloat(outqty)){
                if(MANAGE_DECIMAL_QTY==1){
                    $(this).val(parseFloat(outqty).toFixed(2));
                }else{
                    $(this).val(parseInt(outqty));
                }
            }
        });
        $(document).on('keyup','.inpendingquantity'+ppgid, function (e) {
            // var divid = $(this).attr('id').match(/\d+/);
            var element = $(this).attr('id').split('_');
            var divid = element[2];

            var pendingqty = $("#inpendingquantity_"+ppgid+"_"+divid).val();
            var inqty = $("#inquantity_"+ppgid+"_"+divid).val();
            var maxqty = $("#inquantity_"+ppgid+"_"+divid).attr("data-maxqty");
            // var maxpendingqty = parseFloat(maxqty)-parseFloat(qty);
        
            var isRejection = $("#isRejection").val();
            var isWastage = $("#isWastage").val();
            var isLost = $("#isLost").val();
            var inproductvariantid = $("#inproductvariantid_"+ppgid+"_"+divid).val();
            
            var qty = /* maxqty = */ 0;
            if($('#otherparty'+ppgid).prop("checked")==true){
                $(".outproductpricesid"+ppgid).each(function(index){
                    var outproductpricesid = $("#outproductpricesid_"+ppgid+"_"+(index+1)).val();
                    if($("input[name=finalproduct_"+ppgid+"_"+divid+"]").prop("checked")==true || inproductvariantid == outproductpricesid){
                        // maxqty += parseFloat($("#tpqty_"+ppgid+"_"+(index+1)).val());
                        if(parseInt(isRejection)==1){
                            if($("#rejection_"+ppgid+"_"+(index+1)).val() != ""){
                                qty += parseFloat($("#rejection_"+ppgid+"_"+(index+1)).val());
                            }
                        }
                        if(parseInt(isWastage)==1){
                            if($("#wastage_"+ppgid+"_"+(index+1)).val() != ""){
                                qty += parseFloat($("#wastage_"+ppgid+"_"+(index+1)).val());
                            }
                        }
                        if(parseInt(isLost)==1){
                            if($("#lost_"+ppgid+"_"+(index+1)).val() != ""){
                                qty += parseFloat($("#lost_"+ppgid+"_"+(index+1)).val());
                            }
                        }
                    }
                });
            }
            
            var maxpendingqty = parseFloat(maxqty)-parseFloat(inqty)-parseFloat(qty);

            if(pendingqty!="" && parseFloat(pendingqty) > parseFloat(maxpendingqty)){
                if(MANAGE_DECIMAL_QTY==1){
                    $("#inpendingquantity_"+ppgid+"_"+divid).val(parseFloat(maxpendingqty).toFixed(2));
                }else{
                    $("#inpendingquantity_"+ppgid+"_"+divid).val(parseInt(maxpendingqty));
                }
            }
        
        });
        $(document).on('keyup','.laborcost'+ppgid, function (e) {
            // var divid = $(this).attr('id').match(/\d+/);
            var element = $(this).attr('id').split('_');
            var divid = element[2];

            totallaborcost(divid,ppgid);
        });
        $(document).on('change', 'select.inextrachargesid'+ppgid, function() { 
            var element = $(this).attr('id').split('_');
            var rowid = element[2];
            calculateextracharges(rowid,1,ppgid);
        });
        $(document).on('keyup', '.inextrachargeamount'+ppgid, function() { 
            var element = $(this).attr('id').split('_');
            var rowid = element[2];
            // calculateextracharges(rowid,1);

            var productamount = chargestaxamount = chargespercent = 0;
            $(".inproductamount"+ppgid).each(function( index ) {
                var divid = $(this).attr("div-id");
                if($(this).val()!=""){
                    productamount += parseFloat($(this).val());
                }
            });
            var extrachargesid = $("#inextrachargesid_"+ppgid+"_"+rowid).val();
            var tax = $("#inextrachargesid_"+ppgid+"_"+rowid+" option:selected").attr("data-tax");
            var chargestype = $("#inextrachargesid_"+ppgid+"_"+rowid+" option:selected").attr("data-type");
            var optiontext = $("#inextrachargesid_"+ppgid+"_"+rowid+" option:selected").text();
        
            if(this.value!=''){
                if(chargestype==0){
                    if(parseFloat(this.value) > parseFloat(productamount)){
                        $(this).val(parseFloat(productamount).toFixed(2));
                    }
                }
                if(tax>0){
                    chargestaxamount = parseFloat(this.value) * parseFloat(tax) / (100+parseFloat(tax));
                }
                if(chargestype==0){
                    chargespercent = parseFloat(this.value) * 100 / parseFloat(productamount);
                    
                }
            }
            $("#inextrachargestax_"+ppgid+"_"+rowid).val(parseFloat(chargestaxamount).toFixed(2));
            $("#inextrachargepercentage_"+ppgid+"_"+rowid).val(parseFloat(chargespercent).toFixed(2));
            if(chargestype==0){
                optiontext = optiontext.split("(");
                $("#inextrachargesid_"+ppgid+"_"+rowid+" option:selected").text(optiontext[0]+" ("+parseFloat(chargespercent).toFixed(2)+"%)");
                $("#inextrachargesid_"+ppgid+"_"+rowid).selectpicker("refresh");
                $("#inextrachargesname_"+ppgid+"_"+rowid).val(optiontext[0]+" ("+parseFloat(chargespercent).toFixed(2)+"%)");
            }

        });
        $(document).on('keyup', '.inquantity'+ppgid, function() { 
            // var rowid = $(this).attr("id").match(/\d+/);
            var element = $(this).attr('id').split('_');
            var rowid = element[2];

            var qty = $("#inquantity_"+ppgid+"_"+rowid).val();
            /* var maxqty = $("#inquantity_"+ppgid+"_"+rowid).attr("data-maxqty");
            
            if(qty!="" && maxqty!=""){
                if(parseFloat(qty) > parseFloat(maxqty) && parseFloat(maxqty) > 0){
                    $("#inquantity_"+ppgid+"_"+rowid).val(parseFloat(maxqty));
                }
            } */
            var price = 0;
            if(this.value!=""){
                price = parseFloat($("#inproductvariantid_"+ppgid+"_"+rowid+" option:selected").attr("data-price")) * parseFloat(this.value);
            }
            $("#inproductamount_"+ppgid+"_"+rowid).val(parseFloat(price).toFixed(2));
            
            calculatependingqty(rowid,ppgid);
            totallaborcost(rowid,ppgid);
            changeextrachargesamount(1,ppgid);
            calculateinstockqty(ppgid,rowid);
        });
        if(ACTION==1){
            $(".add_certificate_btn").hide();
            $(".add_certificate_btn:last").show();
        }
        $(document).on('keyup', '.processoptionclass'+ppgid, function() { 
            var element = $(this).attr('id').split('_');
            var rowid = element[2];
            
            $(".countinproducts"+ppgid).each(function(){
                
                var element = $(this).attr('id').split('_');
                var divid = element[2];
                
                calculatependingqty(divid,ppgid);
            });
        });
    }

});
function calculateinstockqty(ppgid,rowid){
    var inqty = $("#inquantity_"+ppgid+"_"+rowid).val();

    var qty = 0 ;
    $(".stockqty_"+ppgid+"_"+rowid).each(function(){
        var element = $(this).attr('id').split('_');
        var cnt = element[3];
        $("#stockqty_"+ppgid+"_"+rowid+"_"+cnt).val("");
        var maxqty = $("#outqty_"+ppgid+"_"+rowid+"_"+cnt).val();

        if(parseFloat(inqty) > 0){
            if(parseFloat(inqty) > parseFloat(maxqty)){
    
                if(MANAGE_DECIMAL_QTY==1){
                    $("#stockqty_"+ppgid+"_"+rowid+"_"+cnt).val(parseFloat(maxqty).toFixed(2));
                }else{
                    $("#stockqty_"+ppgid+"_"+rowid+"_"+cnt).val(parseInt(maxqty));
                }
                inqty -= parseFloat(maxqty);
            }else if(parseFloat(inqty) <= parseFloat(maxqty)){
                if(MANAGE_DECIMAL_QTY==1){
                    $("#stockqty_"+ppgid+"_"+rowid+"_"+cnt).val(parseFloat(inqty).toFixed(2));
                }else{
                    $("#stockqty_"+ppgid+"_"+rowid+"_"+cnt).val(parseInt(inqty));
                }
                inqty = 0;
            }
        }

    });
}
function totallaborcost(divid,pgid){
    var qty = $("#inquantity_"+pgid+"_"+divid).val();
    var laborcost = $("#inlaborcost_"+pgid+"_"+divid).val();
    
    if(qty!="" && laborcost!=""){
        var totalcost = parseFloat(qty) * parseFloat(laborcost);
        $('#intotalcost_'+pgid+'_'+divid).val(parseFloat(totalcost).toFixed(2));
    }else{
        $('#intotalcost_'+pgid+'_'+divid).val('0');
    }
}
function calculatependingqty(divid,pgid){
    var inqty = $("#inquantity_"+pgid+"_"+divid).val();
    var maxqty = $("#inquantity_"+pgid+"_"+divid).attr("data-maxqty");
    var isRejection = $("#isRejection").val();
    var isWastage = $("#isWastage").val();
    var isLost = $("#isLost").val();
    var inproductvariantid = $("#inproductvariantid_"+pgid+"_"+divid).val();
    
    maxqty = (maxqty!="")?parseFloat(maxqty):0; 
    var qty = /* maxqty = */ 0;

    if($('#otherparty'+pgid).prop("checked")==true){

        $(".outproductpricesid"+pgid).each(function(index){
            var outproductpricesid = $("#outproductpricesid_"+pgid+"_"+(index+1)).val();
            if($("input[name=finalproduct_"+pgid+"_"+divid+"]").prop("checked")==true || inproductvariantid == outproductpricesid){
                // maxqty += parseFloat($("#tpqty_"+pgid+"_"+(index+1)).val());
                if(parseInt(isRejection)==1){
                    if($("#rejection_"+pgid+"_"+(index+1)).val() != ""){
                        qty += parseFloat($("#rejection_"+pgid+"_"+(index+1)).val());
                    }
                }
                if(parseInt(isWastage)==1){
                    if($("#wastage_"+pgid+"_"+(index+1)).val() != ""){
                        qty += parseFloat($("#wastage_"+pgid+"_"+(index+1)).val());
                    }
                }
                if(parseInt(isLost)==1){
                    if($("#lost_"+pgid+"_"+(index+1)).val() != ""){
                        qty += parseFloat($("#lost_"+pgid+"_"+(index+1)).val());
                    }
                }
            }
        });
    }
       
    
    maxqty = parseFloat(maxqty) - parseFloat(qty);
    
    if(inqty!="" && maxqty!=""){
        if(parseFloat(inqty) > parseFloat(maxqty) && parseFloat(maxqty) > 0){
            $("#inquantity_"+pgid+"_"+divid).val(parseFloat(maxqty));
        }
    }

    if(inqty!="" && parseFloat(inqty) < parseFloat(maxqty)){
        if(MANAGE_DECIMAL_QTY==1){
            $("#inpendingquantity_"+pgid+"_"+divid).val(parseFloat(parseFloat(maxqty)-parseFloat(inqty)).toFixed(2));
        }else{
            $("#inpendingquantity_"+pgid+"_"+divid).val(parseInt(parseFloat(maxqty)-parseFloat(inqty)));
        }
    }else{
        $("#inpendingquantity_"+pgid+"_"+divid).val("");
    }
}
function displayStockMessage(divid,pgid){
    var productstock = $("#outproductvariantid_"+pgid+"_"+divid+" option:selected").data("orderproductsforfifo");
    var stockqtyarr = (productstock!=undefined)?productstock.map(function(value,index){ return value['qty']}):[];
    var stock = 0;
    for (var i = 0; i < stockqtyarr.length; i++) {
        stock += parseFloat(stockqtyarr[i]);
    }
    
    if(productstock==undefined){
        //$('#ordproductstock'+divid).val('0');
        $('#displaystockmessage_'+pgid+'_'+divid).html('');
    }else{
        if(parseFloat(stock) > 0){
            //$('#ordproductstock'+divid).val(parseFloat(productstock));
            $('#displaystockmessage_'+pgid+'_'+divid).html('<span class="text-primary" style="font-weight: 600;">Stock : '+parseFloat(stock).toFixed(2)+'</span>');
        }else{
            //$('#ordproductstock'+divid).val(0);
            $('#displaystockmessage_'+pgid+'_'+divid).html('<span class="text-danger" style="font-weight: 600;">Out of Stock</span>');
        }
    }
}
function getorderproductqty(divid,pgid){
    var qty = $("#quantity_"+pgid+"_"+divid).val();
    var stockqtyarray = [];
    
    var orderproducts = $("#orderproducts_"+pgid+"_"+divid).val();
    
    if(orderproducts!=""){
        var productarr = JSON.parse(orderproducts); 
        var key = 0;
        for(var i = 0; i < productarr.length; i++) {
            var mappingid = productarr[i]['transactionproductstockmappingid'];
            var stocktype = productarr[i]['stocktype'];
            var stocktypeid = productarr[i]['stocktypeid']; 
            var orderqty = productarr[i]['qty'];
            orderqty = (MANAGE_DECIMAL_QTY==1)?parseFloat(orderqty):parseInt(orderqty);

            if(parseFloat(qty) > 0){
                
                if(parseFloat(orderqty) < parseFloat(qty)){
                    qty = parseFloat(qty) - parseFloat(orderqty);
                    stockqtyarray[key] = {'mappingid':mappingid,'qty':parseFloat(orderqty).toFixed(2),'stocktype':stocktype,'stocktypeid':stocktypeid};
                }else if(parseFloat(orderqty) >= parseFloat(qty)){
                    stockqtyarray[key] = {'mappingid':mappingid,'qty':parseFloat(qty).toFixed(2),'stocktype':stocktype,'stocktypeid':stocktypeid};
                    qty = 0;
                }
                key++;
            }
        }
        if(parseFloat(qty) > 0){
            stockqtyarray[key] = {'qty':parseInt(qty),'stocktype':1,'stocktypeid':0};
        }
    }
    if(stockqtyarray.length > 0){
        $("#finalorderproducts_"+pgid+"_"+divid).val(JSON.stringify(stockqtyarray));
    }
    
}
function changeextrachargesamount(type=0,pgid){
    var prtype = (type==1?"in":"out");
    $("select."+prtype+"extrachargesid"+pgid).each(function( index ) {
        // var rowid = $(this).attr("id").match(/\d+/);
        var element = $(this).attr('id').split('_');
        var rowid = element[2];
        calculateextracharges(rowid,type,pgid);
    });
}
function calculateextracharges(rowid,type=0,pgid){

    var prtype = (type==1?"in":"out");
    
    var extracharges = $("#"+prtype+"extrachargesid_"+pgid+"_"+rowid).val();
    var chargestype = $("#"+prtype+"extrachargesid_"+pgid+"_"+rowid+" option:selected").attr("data-type");
    var amount = $("#"+prtype+"extrachargesid_"+pgid+"_"+rowid+" option:selected").attr("data-amount");
    var tax = $("#"+prtype+"extrachargesid_"+pgid+"_"+rowid+" option:selected").attr("data-tax");
    
    var productamount = 0;
    if(type==1){
        $(".inproductamount"+pgid).each(function( index ) {
            if($(this).val()!=""){
                productamount += parseFloat($(this).val());
            }
        });
    }else{
        $(".productamount"+pgid).each(function( index ) {
            if($(this).val()!=""){
                productamount += parseFloat($(this).val());
            }
        });
    }
    
    var chargesamount = chargestaxamount = 0;
    if(parseFloat(productamount)>0 && parseFloat(extracharges) > 0){

        if(chargestype==0){
            chargesamount = parseFloat(productamount) * parseFloat(amount) / 100;
        }else{
            chargesamount = parseFloat(amount);
        }

        chargestaxamount = parseFloat(chargesamount) * parseFloat(tax) / (100+parseFloat(tax));
        
        $("#"+prtype+"extrachargestax_"+pgid+"_"+rowid).val(parseFloat(chargestaxamount).toFixed(2));
        $("#"+prtype+"extrachargeamount_"+pgid+"_"+rowid).val(parseFloat(chargesamount).toFixed(2));
    }else{
        $("#"+prtype+"extrachargestax_"+pgid+"_"+rowid).val(parseFloat(0).toFixed(2));
        $("#"+prtype+"extrachargeamount_"+pgid+"_"+rowid).val(parseFloat(0).toFixed(2));
    }
    
    var chargesname = $("#"+prtype+"extrachargesid_"+pgid+"_"+rowid+" option:selected").text();
    $("#"+prtype+"extrachargesname_"+pgid+"_"+rowid).val(chargesname.trim());
    var chargespercent = 0;
    if(chargestype==0){
        chargespercent = parseFloat(amount);
    }
    $("#"+prtype+"extrachargepercentage_"+pgid+"_"+rowid).val(parseFloat(chargespercent).toFixed(2));
    
    /* if(chargestype==0){
        optiontext = chargesname.split("(");
        $("#"+prtype+"extrachargesid"+rowid+" option:selected").text(optiontext[0]+" ("+parseFloat(chargespercent).toFixed(2)+"%)");
        $("#"+prtype+"extrachargesid"+rowid).selectpicker("refresh");
        $("#"+prtype+"extrachargesname"+rowid).val(optiontext[0]+" ("+parseFloat(chargespercent).toFixed(2)+"%)");
    } */
}
function validcertificatefile(obj,element){
    var val = obj.val();
    var id = obj.attr('id').match(/\d+/);
    var filename = obj.val().replace(/C:\\fakepath\\/i, '');
  
    switch(val.substring(val.lastIndexOf('.') + 1).toLowerCase()){
      case 'pdf': case 'gif': case 'bmp': case 'jpg': case 'jpeg': case 'png':
        
        isvalidimageorpdffile = 1;
        $("#isvalid"+element).val("1");
        $("#Filetext"+element).val(filename);
        $("#"+element+"_div").removeClass("has-error is-focused");
        break;
      default:
        $("#isvalid"+element).val("0");
        $("#Filetext"+element).val("");
        $("#"+element+"_div").addClass("has-error is-focused");
        new PNotify({title: 'Please upload valid file !',styling: 'fontawesome',delay: '3000',type: 'error'});
        break;
    }
}
function addnewoutproduct(pgid){
    
    var rowcount = parseInt($(".countoutproducts"+pgid+":last").attr("id").split("_")[2])+1;
    
    var datahtml = '<div class="countoutproducs'+pgid+'" id="countoutproducts_'+pgid+'_'+rowcount+'" style="width: 100%;float: left;">\
                        <textarea style="display:none;" id="orderproducts_'+pgid+'_'+rowcount+'"></textarea>\
                        <textarea style="display:none;" name="finalorderproducts['+pgid+'][]" id="finalorderproducts_'+pgid+'_'+rowcount+'"></textarea>\
                        <div class="col-md-3">\
                            <div class="form-group p-n" id="outproduct_'+pgid+'_'+rowcount+'_div">\
                            <div class="col-sm-12">\
                                <input type="hidden" id="preoutproductid_'+pgid+'_'+rowcount+'" value="">\
                                <select id="outproductid_'+pgid+'_'+rowcount+'" name="outproductid['+pgid+'][]" class="selectpicker form-control outproductid'+pgid+'" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                    <option value="0">Select Product</option>\
                                    '+PRODUCT_OPTION_DATA_ARR[pgid]+'\
                                </select>\
                            </div>\
                            </div>\
                        </div>\
                        <div class="col-md-3">\
                            <div class="form-group p-n" id="outproductvariant_'+pgid+'_'+rowcount+'_div">\
                                <div class="col-sm-12">\
                                    <input type="hidden" id="preoutproductvariantid_'+pgid+'_'+rowcount+'" value="">\
                                    <select id="outproductvariantid_'+pgid+'_'+rowcount+'" name="outproductvariantid['+pgid+'][]" class="selectpicker form-control outproductvariantid'+pgid+'" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                        <option value="0" data-price="0">Select Variant</option>\
                                    </select>\
                                    <input type="hidden" name="outprice['+pgid+'][]" id="outprice_'+pgid+'_'+rowcount+'" value="">\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-2">\
                            <div class="form-group p-n" id="unit_'+pgid+'_'+rowcount+'_div">\
                            <div class="col-sm-12">\
                                <input type="text" id="unitid_'+pgid+'_'+rowcount+'" name="unitid['+pgid+'][]" class="form-control unitid'+pgid+'" value="" readonly>\
                            </div>\
                            </div>\
                        </div>\
                        <div class="col-md-2">\
                            <div class="form-group" id="quantity_'+pgid+'_'+rowcount+'_div">\
                                <div class="col-sm-12">\
                                    <input id="quantity_'+pgid+'_'+rowcount+'" name="quantity['+pgid+'][]" class="form-control quantity'+pgid+' text-right" onkeypress="'+(MANAGE_DECIMAL_QTY==1?"return decimal_number_validation(event, this.value,8);":"return isNumber(event);")+'">\
                                    <input type="hidden" id="productamount_'+pgid+'_'+rowcount+'" class="productamount'+pgid+'" value="">\
                                </div>\
                                <div class="col-sm-12 text-center p-n displaystockmessage" id="displaystockmessage_'+pgid+'_'+rowcount+'"></div> \
                            </div>\
                        </div>\
                        <div class="col-md-1 text-right pt-md">\
                            <button type="button" class="btn btn-default btn-raised remove_outproduct_btn'+pgid+' m-n" onclick="removeoutproduct('+rowcount+','+pgid+')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
                            <button type="button" class="btn btn-default btn-raised add_outproduct_btn'+pgid+' m-n" onclick="addnewoutproduct('+pgid+')" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>\
                        </div>\
                    </div>';
    
    $(".remove_outproduct_btn"+pgid+":first").show();
    $(".add_outproduct_btn"+pgid+":last").hide();
    $("#countoutproducts_"+pgid+"_"+(rowcount-1)).after(datahtml);
    
    $("#outproductid_"+pgid+"_"+rowcount+",#outproductvariantid_"+pgid+"_"+rowcount+",#unitid_"+pgid+"_"+rowcount).selectpicker("refresh");
    $('.yesno input[type="checkbox"]').bootstrapToggle({
        on: 'Yes',
        off: 'No',
        onstyle: 'primary',
        offstyle: 'danger'
    });
}
function removeoutproduct(rowid,pgid){

    if($('select[name="outproductid['+pgid+'][]"]').length!=1 && ACTION==1 && $('.countoutproducts'+pgid+' #productprocessdetailid_'+pgid+'_'+rowid).val()!=null){
        var removeproductprocessdetailid = $('#removeproductprocessdetailid').val();
        $('#removeproductprocessdetailid').val(removeproductprocessdetailid+','+$('.countoutproducts'+pgid+' #productprocessdetailid_'+pgid+'_'+rowid).val());
    }
    $("#countoutproducts_"+pgid+"_"+rowid).remove();

    $(".add_outproduct_btn"+pgid+":last").show();
    if ($(".remove_outproduct_btn"+pgid+":visible").length == 1) {
        $(".remove_outproduct_btn"+pgid+":first").hide();
    }
    changeextrachargesamount(0,pgid);
}
function addnewinproduct(pgid){
    
    var rowcount = parseInt($(".countinproducts"+pgid+":last").attr("id").split("_")[2])+1;
    var datahtml = '<div class="countinproducts'+pgid+'" id="countinproducts_'+pgid+'_'+rowcount+'" style="width: 100%;float: left;">\
                        <div class="col-md-2">\
                            <div class="form-group">\
                                <div class="col-sm-12 pl-xl pr-xs">\
                                    <div class="yesno">\
                                    <input type="checkbox" name="finalproduct_'+pgid+'_'+rowcount+'" value="0">\
                                    </div>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-3">\
                            <div class="form-group p-n mt-n" id="inproduct_'+pgid+'_'+rowcount+'_div">\
                            <div class="col-sm-12 pl-xs pr-xs">\
                                <input type="hidden" id="preinproductid_'+pgid+'_'+rowcount+'" value="">\
                                <select id="inproductid_'+pgid+'_'+rowcount+'" name="inproductid['+pgid+'][]" class="selectpicker form-control inproductid'+pgid+'" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                    <option value="0">Select Product</option>\
                                    '+INPRODUCT_OPTION_DATA+'\
                                </select>\
                            </div>\
                            </div>\
                        </div>\
                        <div class="col-md-2">\
                            <div class="form-group p-n mt-n" id="inproductvariant_'+pgid+'_'+rowcount+'_div">\
                                <div class="col-sm-12 pl-xs pr-xs">\
                                    <input type="hidden" id="preinproductvariantid_'+pgid+'_'+rowcount+'" value="">\
                                    <select id="inproductvariantid_'+pgid+'_'+rowcount+'" name="inproductvariantid['+pgid+'][]" class="selectpicker form-control inproductvariantid'+pgid+'" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                        <option value="0" data-price="0">Select Variant</option>\
                                    </select>\
                                    <input type="hidden" name="inprice['+pgid+'][]" id="inprice_'+pgid+'_'+rowcount+'" value="">\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-1">\
                            <div class="form-group mt-n" id="inquantity_'+pgid+'_'+rowcount+'_div">\
                                <div class="col-sm-12 pl-xs pr-xs">\
                                    <input type="text" id="inquantity_'+pgid+'_'+rowcount+'" name="inquantity['+pgid+'][]" class="form-control inquantity'+pgid+' text-right" onkeypress="'+(MANAGE_DECIMAL_QTY==1?"return decimal_number_validation(event, this.value,8);":"return isNumber(event);")+'">\
                                    <input type="hidden" id="inproductamount_'+pgid+'_'+rowcount+'" class="inproductamount'+pgid+'" value="">\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-1">\
                            <div class="form-group mt-n" id="inpendingquantity_'+pgid+'_'+rowcount+'_div">\
                                <div class="col-sm-12 pl-xs pr-xs">\
                                    <input type="text" id="inpendingquantity_'+pgid+'_'+rowcount+'" name="inpendingquantity['+pgid+'][]" class="form-control inpendingquantity'+pgid+' text-right" onkeypress="'+(MANAGE_DECIMAL_QTY==1?"return decimal_number_validation(event, this.value,8);":"return isNumber(event);")+'">\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-1">\
                            <div class="form-group mt-n" id="inlaborcost_'+pgid+'_'+rowcount+'_div">\
                                <div class="col-sm-12 pl-xs pr-xs">\
                                    <input type="text" id="inlaborcost_'+pgid+'_'+rowcount+'" name="inlaborcost['+pgid+'][]" class="form-control laborcost'+pgid+' text-right" onkeypress="return decimal_number_validation(event, this.value,8)">\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-1">\
                            <div class="form-group mt-n" id="intotalcost_'+pgid+'_'+rowcount+'_div">\
                                <div class="col-sm-12 pl-xs pr-xs">\
                                    <input type="text" id="intotalcost_'+pgid+'_'+rowcount+'" name="intotalcost['+pgid+'][]" class="form-control intotalcost'+pgid+' text-right" onkeypress="return decimal_number_validation(event, this.value,8)" readonly>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-1 text-right pt-md">\
                            <button type="button" class="btn btn-default btn-raised remove_inproduct_btn'+pgid+' m-n" onclick="removeinproduct('+rowcount+','+pgid+')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
                            <button type="button" class="btn btn-default btn-raised add_inproduct_btn'+pgid+' m-n" onclick="addnewinproduct('+pgid+')" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>\
                        </div>\
                    </div>';
    
    $(".remove_inproduct_btn"+pgid+":first").show();
    $(".add_inproduct_btn"+pgid+":last").hide();
    $("#countinproducts_"+pgid+"_"+(rowcount-1)).after(datahtml);
    
    $("#inproductid_"+pgid+"_"+rowcount+",#inproductvariantid_"+pgid+"_"+rowcount).selectpicker("refresh");
    $('.yesno input[type="checkbox"]').bootstrapToggle({
        on: 'Yes',
        off: 'No',
        onstyle: 'primary',
        offstyle: 'danger'
    });
}
function removeinproduct(rowid,pgid){

    if($('select[name="inproductid['+pgid+'][]"]').length!=1 && ACTION==1 && $('.countinproducts'+pgid+' #productprocessdetailid_'+pgid+'_'+rowid).val()!=null){
        var removeproductprocessdetailid = $('#removeproductprocessdetailid').val();
        $('#removeproductprocessdetailid').val(removeproductprocessdetailid+','+$('.countinproducts'+pgid+' #productprocessdetailid_'+pgid+'_'+rowid).val());
    }
    $("#countinproducts_"+pgid+"_"+rowid).remove();

    $(".add_inproduct_btn"+pgid+":last").show();
    if ($(".remove_inproduct_btn"+pgid+":visible").length == 1) {
        $(".remove_inproduct_btn"+pgid+":first").hide();
    }
    changeextrachargesamount(1,pgid);
}
function addnewcertificate(){
    
    var date = new Date();
    var dd = date.getDate(); //yields day
    var MM = date.getMonth(); //yields month
    var yyyy = date.getFullYear(); //yields year
    var MM = (((MM+1)<10)?"0":"")+(MM+1);
    var currentDate= dd + "/" +MM + "/" + yyyy; 
    
    var rowcount = parseInt($(".countcertificates:last").attr("id").match(/\d+/))+1;
    var datahtml = '<div class="countcertificates" id="countcertificates'+rowcount+'" style="width: 100%;float: left;">\
                        <input type="hidden" name="productprocesscertificatesid['+rowcount+']" value="" id="productprocesscertificatesid'+rowcount+'">\
                        <div class="col-md-2">\
                            <div class="form-group p-n" id="docno'+rowcount+'_div">\
                            <div class="col-sm-12">\
                                <input type="text" id="docno'+rowcount+'" name="docno['+rowcount+']" class="form-control docno">\
                            </div>\
                            </div>\
                        </div>\
                        <div class="col-md-2">\
                            <div class="form-group p-n" id="doctitle'+rowcount+'_div">\
                                <div class="col-sm-12">\
                                    <input type="text" id="doctitle'+rowcount+'" name="doctitle['+rowcount+']" class="form-control doctitle">\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-2">\
                            <div class="form-group p-n" id="docdescription'+rowcount+'_div">\
                                <div class="col-sm-12">\
                                    <input type="text" id="docdescription'+rowcount+'" name="docdescription['+rowcount+']" class="form-control docdescription">\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-3">\
                            <div class="form-group" id="docfile'+rowcount+'_div">\
                                <div class="col-md-12">\
                                    <input type="hidden" id="isvaliddocfile'+rowcount+'" value="0">\
                                    <input type="hidden" name="olddocfile['+rowcount+']" id="olddocfile'+rowcount+'" value="">\
                                    <div class="input-group" id="fileupload'+rowcount+'">\
                                        <span class="input-group-btn" style="padding: 0 0px 0px 0px;">\
                                            <span class="btn btn-primary btn-raised btn-file">\
                                            <i class="fa fa-upload"></i>\
                                                <input type="file" name="docfile'+rowcount+'" class="docfile" id="docfile'+rowcount+'" accept=".bmp,.bm,.gif,.ico,.jfif,.jfif-tbnl,.jpe,.jpeg,.jpg,.pbm,.png,.svf,.tif,.tiff,.wbmp,.x-png,.pdf" onchange="validcertificatefile($(this),&apos;docfile'+rowcount+'&apos;)">\
                                            </span>\
                                        </span>\
                                        <input type="text" readonly="" id="Filetextdocfile'+rowcount+'" class="form-control" name="Filetextdocfile['+rowcount+']" value="">\
                                    </div>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-2">\
                            <div class="form-group" id="docdate'+rowcount+'_div">\
                                <div class="col-sm-12">\
                                    <input id="docdate'+rowcount+'" type="text" name="docdate['+rowcount+']" value="'+currentDate+'" class="form-control text-center docdate" readonly>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-1 text-right pt-md">\
                            <button type="button" class="btn btn-default btn-raised remove_certificate_btn m-n" onclick="removecertificate('+rowcount+')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
                            <button type="button" class="btn btn-default btn-raised add_certificate_btn m-n" onclick="addnewcertificate()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>\
                        </div>\
                    </div>';
    
    $(".remove_certificate_btn:first").show();
    $(".add_certificate_btn:last").hide();
    $("#countcertificates"+(rowcount-1)).after(datahtml);

    $('.docdate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        todayBtn:"linked",
    });
    
}
function removecertificate(rowid){

    if(ACTION==1 && $('#productprocesscertificatesid'+rowid).val()!=null){
        var removeproductprocesscertificatesid = $('#removeproductprocesscertificatesid').val();
        $('#removeproductprocesscertificatesid').val(removeproductprocesscertificatesid+','+$('#productprocesscertificatesid'+rowid).val());
    }
    $("#countcertificates"+rowid).remove();

    $(".add_certificate_btn:last").show();
    if ($(".remove_certificate_btn:visible").length == 1) {
        $(".remove_certificate_btn:first").hide();
    }
}
function getprocess(mid=""){
    
    $("#processid"+mid).find('option')
        .remove()
        .end()
        .append("<option value='0'>Select Process</option>")
        .val('0')
    ;
    $("#processid"+mid).selectpicker('refresh');
    if(PROCESSTYPE=="OUT"){

        $("#processgroupmappingid"+mid).val('');
        $("#outproductsdiv"+mid).hide();
        $(".countoutproducts"+mid).remove();
        $("#noofsequence"+mid).html('');
        if(ACTION==0){
            $(".outextrachargesid"+mid).val('0').selectpicker("refresh");
            $(".outextrachargeamount"+mid).val('');
            if($(".countoutcharges"+mid).length > 1){
                $(".countoutcharges"+mid+":not(:first)").remove();
                $(".add_outcharges_btn"+mid).hide();
                $(".add_outcharges_btn"+mid+":last").show();
                
                $(".add_outcharges_btn"+mid+":last").show();
                if ($(".remove_outcharges_btn"+mid+":visible").length == 1) {
                    $(".remove_outcharges_btn"+mid+":first").hide();
                }
            }
            $("#outextrachargesdiv"+mid).hide();
        }
    }
    var processgroupid = $('#processgroupid').val();
    PRODUCT_OPTION_DATA_ARR[mid] = [];

    if(processgroupid != '0'){
        var uurl = SITE_URL+"manufacturing-process/getProcessByProcessGroupId";
        var processgroupmappingid = (PROCESSTYPE=="OUT" && ACTION==0?"":PROCESSGROUPMAPPINGID);
        
        // var processdata = [];
        processdata = JSON.parse($("#jsonprocessid"+mid).val().replace(/'/g, '"'));
        
        var noofsequence = "";
        if(processdata.length > 0){
            for(var i = 0; i < processdata.length; i++) {
                $("#processid"+mid).append($('<option>', { 
                    value: processdata[i]['id'],
                    text : processdata[i]['name'],
                    "data-processgroupmappingid" : processdata[i]['processgroupmappingid'],
                    "data-sequenceno" : processdata[i]['sequenceno'],
                    "data-maxsequenceno" : processdata[i]['maxsequenceno'],
                    "data-isoptional" : processdata[i]['isoptional']
                }));
            }
            $("#processid"+mid).val(processdata[0]['id']).selectpicker('refresh');
            
            noofsequence = "<label class='control-label'>Sequence "+processdata[0]['sequenceno']+" of "+processdata[0]['maxsequenceno']+"</label>";
            
            if(PROCESSTYPE!="IN"){
                getmachine(mid);
                getoutproductsdata(mid);
                getproduct(mid);
                // getproductunit();
            }
            $("#processgroupmappingid"+mid).val(processdata[0]['processgroupmappingid']);
        }
        $("#noofsequence"+mid).html(noofsequence);
      /* $.ajax({
        url: uurl,
        type: 'POST',
        data: {processgroupid:String(processgroupid),processgroupmappingid:String(processgroupmappingid),type:PROCESSTYPE},
        dataType: 'json',
        async: false,
        success: function(response){
            var noofsequence = "";
            if(response.length > 0){
                for(var i = 0; i < response.length; i++) {
                    $("#processid").append($('<option>', { 
                        value: response[i]['id'],
                        text : response[i]['name'],
                        "data-processgroupmappingid" : response[i]['processgroupmappingid'],
                        "data-sequenceno" : response[i]['sequenceno'],
                        "data-maxsequenceno" : response[i]['maxsequenceno']
                    }));
                }
                $("#processid").val(response[0]['id']).selectpicker('refresh');
                noofsequence = "<label class='control-label'>Sequence "+response[0]['sequenceno']+" of "+response[0]['maxsequenceno']+"</label>";
                
                if(PROCESSTYPE!="IN"){
                    getmachine();
                    getoutproductsdata();
                    getproduct();
                    // getproductunit();
                }
                $("#processgroupmappingid").val(response[0]['processgroupmappingid']);
            }
            $("#noofsequence").html(noofsequence);
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
      }); */
    }
    if($("#processid"+mid+" option:selected").attr("data-isoptional")==1){
        $('#processid'+mid).attr("disabled", false); 
    }
    $("#processid"+mid).selectpicker('refresh');
}
function getoutproductsdata(pgid){
    
    var processgroupid = pgid; //(PROCESSTYPE=="OUT" && ACTION==0)?pgid:$('#processgroupid').val();
    var processid = $('#processid'+pgid).val();
    var productionplanid = $('#postproductionplanid').val();
    var productionplanqtydetail = $('#productionplanqtydetail'+pgid).val();

    var PRODUCT_OPTION_DATA = PRODUCTUNIT_OPTION_DATA = "";
    if(processgroupid != '0' && processid != '0'){
      var uurl = SITE_URL+"process-group/getOutProductByProcessGroupIdOrProcessId";
      var productprocessid = (PROCESSTYPE=="OUT" && ACTION==0?"":$("#productprocessid"+pgid).val());
      var parentproductprocessid = (PROCESSTYPE!="OUT" && ACTION==0?PARENTPRODUCTPROCESSID:"");
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {processgroupid:String(processgroupid),processid:String(processid),productprocessid:String(productprocessid),parentproductprocessid:String(parentproductprocessid),productionplanid:String(productionplanid),productionplanqtydetail:String(productionplanqtydetail)},
        dataType: 'json',
        async: false,
        success: function(response){
  
            if(response.length > 0){
                var HTML = "";
                
                for(var i = 0; i < response.length; i++) {
                    var issupportingproduct = (response[i]['issupportingproduct']==1?"checked":"");
                    var productprocessdetailid = (productprocessid!=""?response[i]['id']:"");
                    var btnHTML = "";
                    var qty = ((productprocessid!="" || PROCESSTYPE == "NEXTPROCESS" || PROCESSTYPE == "REPROCESS" || productionplanid!="")?response[i]['quantity']:1);
                    qty = (MANAGE_DECIMAL_QTY==1)?parseFloat(qty):parseInt(qty);

                    var finalorderproducts = "";
                    if(/* STOCK_MANAGE_BY==1 &&  */PROCESSTYPE=="OUT" && ACTION==1){
                        finalorderproducts = response[i]['productstockdata'];
                    }
                        if(response.length > 1){
                            if(i==0){
                                if(response.length>1){
                                    btnHTML += '<button type="button" class="btn btn-default btn-raised remove_outproduct_btn'+pgid+' m-n" onclick="removeoutproduct(1,'+pgid+')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>';
                                }else {
                                    btnHTML += '<button type="button" class="btn btn-default btn-raised add_outproduct_btn'+pgid+' m-n" onclick="addnewoutproduct('+pgid+')" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>';
                                }
                            }else if(i!=0) {
                                btnHTML += '<button type="button" class="btn btn-default btn-raised remove_outproduct_btn'+pgid+' m-n" onclick="removeoutproduct('+(i+1)+','+pgid+')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>';
                            }
                            btnHTML += '<button type="button" class="btn btn-default btn-raised btn-sm remove_outproduct_btn'+pgid+' m-n" onclick="removeoutproduct('+(i+1)+','+pgid+')" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>';
                        
                            btnHTML += '<button type="button" class="btn btn-default btn-raised add_outproduct_btn'+pgid+' m-n" onclick="addnewoutproduct('+pgid+')" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>';
                        }
                        HTML += '<div class="countoutproducts'+pgid+'" id="countoutproducts_'+pgid+'_'+(i+1)+'" style="width: 100%;float: left;">\
                                    <textarea style="display:none;" id="orderproducts_'+pgid+'_'+(i+1)+'"></textarea>\
                                    <textarea style="display:none;" name="finalorderproducts['+pgid+'][]" id="finalorderproducts_'+pgid+'_'+(i+1)+'">'+finalorderproducts+'</textarea>\
                                    <div class="col-md-3">\
                                        <div class="form-group p-n" id="outproduct_'+pgid+'_'+(i+1)+'_div">\
                                            <div class="col-sm-12">\
                                                <input type="hidden" name="productprocessdetailid['+pgid+'][]" id="productprocessdetailid_'+pgid+'_'+(i+1)+'" value="'+productprocessdetailid+'">\
                                                <input type="hidden" id="preoutproductid_'+pgid+'_'+(i+1)+'" value="'+response[i]['productid']+'">\
                                                <select id="outproductid_'+pgid+'_'+(i+1)+'" name="outproductid['+pgid+'][]" class="selectpicker form-control outproductid'+pgid+'" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                                    <option value="0">Select Product</option>\
                                                </select>\
                                            </div>\
                                        </div>\
                                    </div>\
                                    <div class="col-md-3">\
                                        <div class="form-group p-n" id="outproductvariant_'+pgid+'_'+(i+1)+'_div">\
                                            <div class="col-sm-12">\
                                                <input type="hidden" id="preoutproductvariantid_'+pgid+'_'+(i+1)+'" value="'+response[i]['productpriceid']+'">\
                                                <select id="outproductvariantid_'+pgid+'_'+(i+1)+'" name="outproductvariantid['+pgid+'][]" class="selectpicker form-control outproductvariantid'+pgid+'" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                                    <option value="0" data-price="0">Select Variant</option>\
                                                </select>\
                                                <input type="hidden" name="outprice['+pgid+'][]" id="outprice_'+pgid+'_'+(i+1)+'" value="">\
                                            </div>\
                                        </div>\
                                    </div>\
                                    <div class="col-md-2">\
                                        <div class="form-group p-n" id="unit_'+pgid+'_'+(i+1)+'_div">\
                                            <div class="col-sm-12">\
                                                <input type="text" id="unitid_'+pgid+'_'+(i+1)+'" name="unitid['+pgid+'][]" class="form-control unitid'+pgid+'" value="'+response[i]['unitname']+'" readonly>\
                                            </div>\
                                        </div>\
                                    </div>\
                                    <div class="col-md-2">\
                                        <div class="form-group" id="quantity_'+pgid+'_'+(i+1)+'_div">\
                                            <div class="col-sm-12">\
                                                <input type="text" id="quantity_'+pgid+'_'+(i+1)+'" name="quantity['+pgid+'][]" class="form-control quantity'+pgid+' text-right" value="'+qty+'" onkeypress="'+(MANAGE_DECIMAL_QTY==1?"return decimal_number_validation(event, this.value,8);":"return isNumber(event);")+'">\
                                                <input type="hidden" id="productamount_'+pgid+'_'+(i+1)+'" class="productamount'+pgid+'" value="">\
                                            </div>\
                                            <div class="col-sm-12 text-center p-n displaystockmessage" id="displaystockmessage_'+pgid+'_'+(i+1)+'"></div> \
                                        </div>\
                                    </div>\
                                    <div class="col-md-1 text-right pt-md">\
                                        '+btnHTML+'\
                                    </div>\
                                </div>';
                }
                $("#productdata"+pgid).html(HTML);
                $("#outproductsdiv"+pgid).show();
                if(PROCESSTYPE != "IN"){
                    $("#outextrachargesdiv"+pgid).show();
                }
                $(".selectpicker").selectpicker("refresh");
                $('.yesno input[type="checkbox"]').bootstrapToggle({
                    on: 'Yes',
                    off: 'No',
                    onstyle: 'primary',
                    offstyle: 'danger'
                });
                $(".add_outproduct_btn"+pgid).hide();
                $(".add_outproduct_btn"+pgid+":last").show();

                /* if(STOCK_MANAGE_BY==1){
                } */
                $(".countoutproducts"+pgid).each(function(){
                    // var divid = $(this).attr('id').match(/\d+/);
                    var element = $(this).attr('id').split("_");
                    var divid = element[2];
                    
                    getOrderProductsForFIFO(divid,pgid);
                    getorderproductqty(divid,pgid);
                    
                });
                // $(".add_outcharges_btn").hide();
                // $(".add_outcharges_btn:last").show();
            }else{
                $("#outproductsdiv"+pgid).hide();
                if(PROCESSTYPE != "IN"){
                    $("#outextrachargesdiv"+pgid).hide();
                }
            }
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
      });
    }else{
        $("#outproductsdiv"+pgid).hide();
        $(".countoutproducts"+pgid).remove();
        if(PROCESSTYPE != "IN"){
            $("#outextrachargesdiv"+pgid).hide();
        }
    }
}
function getproduct(pgid){
    
    $("select.outproductid"+pgid).find('option')
        .remove()
        .end()
        .append("<option value='0' data-unit=''>Select Product</option>")
        .val('0')
    ;
    $("select.outproductid"+pgid).selectpicker('refresh');

    var processgroupid = pgid; //(PROCESSTYPE=="OUT" && ACTION==0)?pgid:$('#processgroupid').val();
    var processid = $('#processid'+pgid).val();
    var referenceid = $('#productprocessid'+pgid).val(); 


    PRODUCT_OPTION_DATA = '';
    if(processgroupid != '0' && processid != '0'){
      var uurl = SITE_URL+"process-group/getProductByProcessGroupIdOrProcessId";

      $.ajax({
        url: uurl,
        type: 'POST',
        data: {processgroupid:String(processgroupid),processid:String(processid),referencetype:0,referenceid:String(referenceid)},
        dataType: 'json',
        async: false,
        success: function(response){
  
            for(var i = 0; i < response.length; i++) {
                
                var variants = [];
                if (!$.isEmptyObject(response[i]['variantdata'])) {
                    variants = response[i]['variantdata'];
                }
                var productname = response[i]['name'].replace("'","&apos;");
                if(DROPDOWN_PRODUCT_LIST==0){
                    
                    $("select.outproductid"+pgid).append($('<option>', { 
                        value: response[i]['id'],
                        text : productname,
                        "data-unit" : response[i]['unit'],
                        "data-variant" : JSON.stringify(variants),
                    }));
                    
                    PRODUCT_OPTION_DATA += '<option value="'+response[i]['id']+'" data-unit="'+response[i]['unit']+'" data-variant="'+(JSON.stringify(variants).replace(/"/g, '&quot;'))+'">'+productname+'</option>';
                }else{
                    
                    $("select.outproductid"+pgid).append($('<option>', { 
                        value: response[i]['id'],
                        // text : productname,
                        "data-unit" : response[i]['unit'],
                        "data-variant" : JSON.stringify(variants),
                        "data-content" :'<img src="'+PRODUCT_PATH+response[i]['image']+'" style="width:40px">  ' + productname
                    }));

                    PRODUCT_OPTION_DATA += '<option data-content="<img src=&apos;'+PRODUCT_PATH+response[i]['image']+'&apos; style=&apos;width:40px&apos;>  '+productname+'" value="'+response[i]['id']+'" data-unit="'+response[i]['unit']+'" data-variant="'+(JSON.stringify(variants).replace(/"/g, '&quot;'))+'">'+productname+'</option>';
                }
                
            }
            PRODUCT_OPTION_DATA_ARR[pgid] = PRODUCT_OPTION_DATA;
            $("select.outproductid"+pgid).each(function(){
                // var rowid = $(this).attr('id').match(/\d+/);
                var element = $(this).attr('id').split("_");
                var rowid = element[2];
                var outproductid = $("#preoutproductid_"+pgid+"_"+rowid).val();
                if(outproductid != ""){
                    $("#outproductid_"+pgid+"_"+rowid).val(outproductid).selectpicker('refresh');
                    getproductvariant(rowid,pgid);
                    getOrderProductsForFIFO(rowid,pgid);
                    getorderproductqty(rowid,pgid);
                }
            });
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
      });
    }
    $("select.outproductid"+pgid).selectpicker('refresh');
}
function getmachine(pgid){
    
    $("#machineid"+pgid).find('option')
        .remove()
        .end()
        .append("<option value='0'>Select Machine</option>")
        .val('0')
    ;
    $("#machineid"+pgid).selectpicker('refresh');

    var processgroupid = (PROCESSTYPE=="OUT")?pgid:$('#processgroupid').val();
    var processid = $('#processid'+pgid).val();
    
    if(processgroupid != '0' && processid != '0'){
      var uurl = SITE_URL+"process-group/getMachineByProcessGroupIdOrProcessId";

      $.ajax({
        url: uurl,
        type: 'POST',
        data: {processgroupid:String(processgroupid),processid:String(processid)},
        dataType: 'json',
        async: false,
        success: function(response){
  
            for(var i = 0; i < response.length; i++) {
                $("#machineid"+pgid).append($('<option>', { 
                    value: response[i]['id'],
                    text : response[i]['name'],
                }));
            }
            if(MACHINEID!=0){
                $("#machineid"+pgid).val(MACHINEID);
            }
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
      });
    }
    $("#machineid"+pgid).selectpicker('refresh');
}
function getproductvariant(divid,pgid){

    $("#outproductvariantid_"+pgid+"_"+divid).find('option')
        .remove()
        .end()
        .append("<option value='0' data-price='0'>Select Variant</option>")
        .val('0')
    ;
    $("#outproductvariantid_"+pgid+"_"+divid).selectpicker('refresh');

    var productvariant = JSON.parse($("#outproductid_"+pgid+"_"+divid+" option:selected").attr("data-variant"));
    
    for(var i = 0; i < productvariant.length; i++) {
        var orderproductsforfifo = [];
        if (!$.isEmptyObject(productvariant[i]['orderproductsforfifo'])) {
            orderproductsforfifo = productvariant[i]['orderproductsforfifo'];
        }
        $("#outproductvariantid_"+pgid+"_"+divid).append($('<option>', { 
            value: productvariant[i]['id'],
            text : productvariant[i]['variantname'],
            "data-price" : productvariant[i]['price'],
            "data-orderproductsforfifo" : JSON.stringify(orderproductsforfifo)
        }));
    }
    if($("#preoutproductvariantid_"+pgid+"_"+divid) != ""){
        $("#outproductvariantid_"+pgid+"_"+divid).val($("#preoutproductvariantid_"+pgid+"_"+divid).val()).selectpicker('refresh');
        var price = $("#outproductvariantid_"+pgid+"_"+divid+" option:selected").attr("data-price");
        $("#outprice_"+pgid+"_"+divid).val(parseFloat(price).toFixed(2));

        var amount = 0;
        if($("#quantity_"+pgid+"_"+divid).val()!=""){
            amount = parseFloat($("#outproductvariantid_"+pgid+"_"+divid+" option:selected").attr("data-price")) * parseFloat($("#quantity_"+pgid+"_"+divid).val());
        }
        $("#productamount_"+pgid+"_"+divid).val(parseFloat(amount).toFixed(2));
    }
    if(productvariant.length == 1){
        $("#outproductvariantid_"+pgid+"_"+divid).val(productvariant[0]['id']).selectpicker('refresh').change();
    }
    $("#outproductvariantid_"+pgid+"_"+divid).selectpicker('refresh');
}
function getOrderProductsForFIFO(divid,pgid){
    
    $("#orderproducts_"+pgid+"_"+divid).val('');

    var productid = $("#outproductid_"+pgid+"_"+divid).val();
    var priceid = $("#outproductvariantid_"+pgid+"_"+divid).val();
    
    if(productid != '0' && priceid != '0'){
        var orderproductsforfifo = JSON.parse($("#outproductvariantid_"+pgid+"_"+divid+" option:selected").attr("data-orderproductsforfifo"));
        if(orderproductsforfifo.length > 0){
            $("#orderproducts_"+pgid+"_"+divid).val(JSON.stringify(orderproductsforfifo));
        }else{
            $("#orderproducts_"+pgid+"_"+divid).val("");
        }
        getorderproductqty(divid,pgid);
       
    }
    $("#outproductvariantid_"+pgid+"_"+divid).selectpicker('refresh');
    displayStockMessage(divid,pgid);
}
/* function getproductvariant(divid){
    
    $("#outproductvariantid"+divid).find('option')
        .remove()
        .end()
        .append("<option value='0' data-price='0'>Select Variant</option>")
        .val('0')
    ;
    $("#outproductvariantid"+divid).selectpicker('refresh');

    var processgroupid = $('#processgroupid').val();
    var processid = $('#processid').val();
    var productid = $('#outproductid'+divid).val();
    
    if(processgroupid != '0' && processid != '0'){
      var uurl = SITE_URL+"process-group/getProductVariantByProcessGroupIdOrProcessIdOrProductId";

      $.ajax({
        url: uurl,
        type: 'POST',
        data: {processgroupid:String(processgroupid),processid:String(processid),productid:String(productid)},
        dataType: 'json',
        async: false,
        success: function(response){
  
            for(var i = 0; i < response.length; i++) {
                $("#outproductvariantid"+divid).append($('<option>', { 
                    value: response[i]['id'],
                    text : response[i]['variantname'],
                    "data-price" : response[i]['price']
                }));
            }
            if($("#preoutproductvariantid"+divid) != ""){
                $("#outproductvariantid"+divid).val($("#preoutproductvariantid"+divid).val()).selectpicker('refresh');
                var price = $("#outproductvariantid"+divid+" option:selected").attr("data-price");
                $("#outprice"+divid).val(parseFloat(price).toFixed(2));

                var amount = 0;
                if($("#quantity"+divid).val()!=""){
                    amount = parseFloat($("#outproductvariantid"+divid+" option:selected").attr("data-price")) * parseInt($("#quantity"+divid).val());
                }
                $("#productamount"+divid).val(parseFloat(amount).toFixed(2));
            }
            if(response.length == 1){
                $("#outproductvariantid"+divid).val(response[0]['id']).selectpicker('refresh').change();
            }
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
      });
    }
    $("#outproductvariantid"+divid).selectpicker('refresh');
} */
/* function getOrderProductsForFIFO(divid){
    
    $("#orderproducts"+divid).val('');

    var productid = $('#outproductid'+divid).val();
    var priceid = $('#outproductvariantid'+divid).val();
    
    if(productid != '0' && priceid != '0'){
      var uurl = SITE_URL+"product-process/getOrderProductsForFIFO";

      $.ajax({
        url: uurl,
        type: 'POST',
        data: {productid:String(productid),priceid:String(priceid)},
        dataType: 'json',
        async: false,
        success: function(response){
            if(response.length > 0){
                $("#orderproducts"+divid).val(JSON.stringify(response));
            }else{
                $("#orderproducts"+divid).val("");
            }
            getorderproductqty(divid);
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
      });
    }
    $("#outproductvariantid"+divid).selectpicker('refresh');
} */
function getinproduct(pgid){
    
    $("select.inproductid"+pgid).find('option')
        .remove()
        .end()
        .append("<option value='0'>Select Product</option>")
        .val('0')
    ;
    $("select.inproductid"+pgid).selectpicker('refresh');

    var processgroupid = $('#postprocessgroupid').val();
    var processid = $('#processid'+pgid).val();
    INPRODUCT_OPTION_DATA = '';
    if(processgroupid != '0' && processid != '0'){
      var uurl = SITE_URL+"process-group/getProductByProcessGroupIdOrProcessId";

      $.ajax({
        url: uurl,
        type: 'POST',
        data: {processgroupid:String(processgroupid),processid:String(processid),type:"in"},
        dataType: 'json',
        async: false,
        success: function(response){
  
            for(var i = 0; i < response.length; i++) {
                var variants = [];
                if (!$.isEmptyObject(response[i]['variantdata'])) {
                    variants = response[i]['variantdata'];
                }
                var productname = response[i]['name'].replace("'","&apos;");
                if(DROPDOWN_PRODUCT_LIST==0){
                    
                    $("select.inproductid"+pgid).append($('<option>', { 
                        value: response[i]['id'],
                        text : productname,
                        "data-variant" : JSON.stringify(variants),
                    }));
    
                    INPRODUCT_OPTION_DATA += '<option value="'+response[i]['id']+'" data-variant="'+(JSON.stringify(variants).replace(/"/g, '&quot;'))+'">'+productname+'</option>';
                }else{
                    
                    $("select.inproductid"+pgid).append($('<option>', { 
                        value: response[i]['id'],
                        // text : productname,
                        "data-variant" : JSON.stringify(variants),
                        "data-content" :'<img src="'+PRODUCT_PATH+response[i]['image']+'" style="width:40px">  ' + productname
                    }));

                    INPRODUCT_OPTION_DATA += '<option data-content="<img src=&apos;'+PRODUCT_PATH+response[i]['image']+'&apos; style=&apos;width:40px&apos;>  '+productname+'" value="'+response[i]['id']+'" data-variant="'+(JSON.stringify(variants).replace(/"/g, '&quot;'))+'">'+productname+'</option>';
                }
            }
            $("select.inproductid"+pgid).each(function(){
                // var rowid = $(this).attr('id').match(/\d+/);
                var element = $(this).attr('id').split("_");
                var rowid = element[2];
                var inproductid = $("#preinproductid_"+pgid+"_"+rowid).val();
                if(inproductid != ""){
                    $("#inproductid_"+pgid+"_"+rowid).val(inproductid).selectpicker('refresh');
                    getinproductvariant(rowid,pgid);
                }
            });
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
      });
    }
    $("select.inproductid"+pgid).selectpicker('refresh');
}
function getinproductvariant(divid,pgid){
    
    $("#inproductvariantid_"+pgid+"_"+divid).find('option')
        .remove()
        .end()
        .append("<option value='0' data-price='0'>Select Variant</option>")
        .val('0')
    ;
    $("#inproductvariantid_"+pgid+"_"+divid).selectpicker('refresh');

    var productvariant = JSON.parse($("#inproductid_"+pgid+"_"+divid+" option:selected").attr("data-variant"));
     
    for(var i = 0; i < productvariant.length; i++) {
        var orderproductsforfifo = [];
        if (!$.isEmptyObject(productvariant[i]['orderproductsforfifo'])) {
            orderproductsforfifo = productvariant[i]['orderproductsforfifo'];
        }
        $("#inproductvariantid_"+pgid+"_"+divid).append($('<option>', { 
            value: productvariant[i]['id'],
            text : productvariant[i]['variantname'],
            "data-price" : productvariant[i]['price'],
            "data-orderproductsforfifo" : JSON.stringify(orderproductsforfifo)
        }));
    }
    if($("#preinproductvariantid_"+pgid+"_"+divid) != ""){
        $("#inproductvariantid_"+pgid+"_"+divid).val($("#preinproductvariantid_"+pgid+"_"+divid).val()).selectpicker('refresh');
        $("#inprice_"+pgid+"_"+divid).val($("#inproductvariantid_"+pgid+"_"+divid+" option:selected").attr("data-price"));

        var amount = 0;
        if($("#inquantity_"+pgid+"_"+divid).val()!=""){
            amount = parseFloat($("#inproductvariantid_"+pgid+"_"+divid+" option:selected").attr("data-price")) * parseFloat($("#inquantity_"+pgid+"_"+divid).val());
        }
        $("#inproductamount_"+pgid+"_"+divid).val(parseFloat(amount).toFixed(2));
    }
    if(productvariant.length == 1){
        $("#inproductvariantid_"+pgid+"_"+divid).val(productvariant[0]['id']);
    }
    $("#inproductvariantid_"+pgid+"_"+divid).selectpicker('refresh');
}
/* function getinproductvariant(divid){
    
    $("#inproductvariantid"+divid).find('option')
        .remove()
        .end()
        .append("<option value='0' data-price='0'>Select Variant</option>")
        .val('0')
    ;
    $("#inproductvariantid"+divid).selectpicker('refresh');

    var processgroupid = $('#processgroupid').val();
    var processid = $('#processid').val();
    var productid = $('#inproductid'+divid).val();
    
    if(processgroupid != '0' && processid != '0'){
      var uurl = SITE_URL+"process-group/getProductVariantByProcessGroupIdOrProcessIdOrProductId";

      $.ajax({
        url: uurl,
        type: 'POST',
        data: {processgroupid:String(processgroupid),processid:String(processid),productid:String(productid),type:"in"},
        dataType: 'json',
        async: false,
        success: function(response){
  
            for(var i = 0; i < response.length; i++) {
                $("#inproductvariantid"+divid).append($('<option>', { 
                    value: response[i]['id'],
                    text : response[i]['variantname'],
                    "data-price" : response[i]['price']
                }));
            }
            if($("#preinproductvariantid"+divid) != ""){
                $("#inproductvariantid"+divid).val($("#preinproductvariantid"+divid).val()).selectpicker('refresh');
                $("#inprice"+divid).val($("#inproductvariantid"+divid+" option:selected").attr("data-price"));

                var amount = 0;
                if($("#inquantity"+divid).val()!=""){
                    amount = parseFloat($("#inproductvariantid"+divid+" option:selected").attr("data-price")) * parseInt($("#inquantity"+divid).val());
                }
                $("#inproductamount"+divid).val(parseFloat(amount).toFixed(2));
            }
            if(response.length == 1){
                $("#inproductvariantid"+divid).val(response[0]['id']);
            }
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
      });
    }
    $("#inproductvariantid"+divid).selectpicker('refresh');
} */
function addnewcharge(pgid,type=0){
    //type-0(out), 1-(in)
    var countchargescls = "countoutcharges";
    var prtype = "out";
    if(type==1){
        countchargescls = "countincharges";
        prtype = "in";
    }
    var rowcount = parseInt($("."+countchargescls+pgid+":last").attr("id").split("_")[2])+1;
    var datahtml = '<div class="col-md-4 p-n '+countchargescls+pgid+'" id="'+countchargescls+"_"+pgid+"_"+rowcount+'">\
                        <div class="col-sm-6 pr-xs">\
                            <div class="form-group p-n" id="'+prtype+'extracharges_'+pgid+'_'+rowcount+'_div">\
                                <div class="col-sm-12">\
                                    <select id="'+prtype+'extrachargesid_'+pgid+'_'+rowcount+'" name="'+prtype+'extrachargesid['+pgid+'][]" class="selectpicker form-control '+prtype+'extrachargesid'+pgid+'" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                        <option value="0">Select Charges</option>\
                                            '+EXTRA_CHARGES_OPTIONS+'\
                                    </select>\
                                    <input type="hidden" name="'+prtype+'extrachargestax['+pgid+'][]" id="'+prtype+'extrachargestax_'+pgid+'_'+rowcount+'" class="'+prtype+'extrachargestax'+pgid+'" value="">\
                                    <input type="hidden" name="'+prtype+'extrachargesname['+pgid+'][]" id="'+prtype+'extrachargesname_'+pgid+'_'+rowcount+'" class="'+prtype+'extrachargesname'+pgid+'" value="">\
                                    <input type="hidden" name="'+prtype+'extrachargepercentage['+pgid+'][]" id="'+prtype+'extrachargepercentage_'+pgid+'_'+rowcount+'" class="'+prtype+'extrachargepercentage'+pgid+'" value="">\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-sm-3 pl-xs pr-xs">\
                            <div class="form-group p-n" id="'+prtype+'extrachargeamount_'+pgid+'_'+rowcount+'_div">\
                                <div class="col-sm-12">\
                                    <input type="text" id="'+prtype+'extrachargeamount_'+pgid+'_'+rowcount+'" name="'+prtype+'extrachargeamount['+pgid+'][]" class="form-control text-right '+prtype+'extrachargeamount'+pgid+'" placeholder="Charge" onkeypress="return decimal_number_validation(event, this.value, 8)">\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-3 text-right pt-md">\
                            <button type="button" class="btn btn-default btn-raised remove_'+prtype+'charges_btn'+pgid+' m-n" onclick="removecharge('+rowcount+','+pgid+','+type+')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
                            <button type="button" class="btn btn-default btn-raised add_'+prtype+'charges_btn'+pgid+' m-n" onclick="addnewcharge('+pgid+','+type+')" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>\
                        </div>\
                    </div>';
    
    $(".remove_"+prtype+"charges_btn"+pgid+":first").show();
    $(".add_"+prtype+"charges_btn"+pgid+":last").hide();
    $("#"+countchargescls+"_"+pgid+"_"+(rowcount-1)).after(datahtml);
    
    $("#"+prtype+"extrachargesid_"+pgid+"_"+rowcount).selectpicker("refresh");
}
function removecharge(rowid,pgid,type=0){

    var countchargescls = "countoutcharges";
    var prtype = "out";
    if(type==1){
        countchargescls = "countincharges";
        prtype = "in";
    }

    if($('select[name="'+prtype+'extrachargesid['+pgid+'][]"]').length!=1 && ACTION==1 && $('#'+prtype+'extrachargemappingid_'+pgid+'_'+rowid).val()!=null){
        var removeextrachargemappingid = $('#removeextrachargemappingid').val();
        $('#removeextrachargemappingid').val(removeextrachargemappingid+','+$('#'+prtype+'extrachargemappingid_'+pgid+'_'+rowid).val());
    }
    $("#"+countchargescls+"_"+pgid+"_"+rowid).remove();

    $(".add_"+prtype+"charges_btn"+pgid+":last").show();
    if ($(".remove_"+prtype+"charges_btn"+pgid+":visible").length == 1) {
        $(".remove_"+prtype+"charges_btn"+pgid+":first").hide();
    }
}
/* function getproductunit(){
    
    $("select.unitid").find('option')
        .remove()
        .end()
        .append("<option value='0'>Select Unit</option>")
        .val('0')
    ;
    $("select.unitid").selectpicker('refresh');

    var processgroupid = $('#processgroupid').val();
    var processid = $('#processid').val();
    PRODUCTUNIT_OPTION_DATA = '';
    if(processgroupid != '0' && processid != '0'){
      var uurl = SITE_URL+"process-group/getProductUnitByProcessGroupIdOrProcessId";

      $.ajax({
        url: uurl,
        type: 'POST',
        data: {processgroupid:String(processgroupid),processid:String(processid)},
        dataType: 'json',
        async: false,
        success: function(response){
  
            for(var i = 0; i < response.length; i++) {
                $("select.unitid").append($('<option>', { 
                    value: response[i]['id'],
                    text : response[i]['name']
                }));

                PRODUCTUNIT_OPTION_DATA += '<option value="'+response[i]['id']+'">'+response[i]['name']+'</option>';
            }
            $("select.outproductid").each(function(){
                var rowid = $(this).attr('id').match(/\d+/);
                var unitid = $("#preunitid"+rowid).val();
                if(unitid != ""){
                    $("#unitid"+rowid).val(unitid).selectpicker('refresh');
                }
            });
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
      });
    }
    $("select.unitid").selectpicker('refresh');
} */
function resetdata() {
    $("#processgroup_div").removeClass("has-error is-focused");
    
    if(ACTION==0){
        if(PROCESSTYPE == "OUT" && PROCESSGROUPID == ""){
            $('#processgroupid').val("0").change();
        }
        
        $('.selectpicker').selectpicker('refresh');
    }
    $('html, body').animate({scrollTop:0},'slow');  
}
function checkvalidationstockout(addtype=0) {
   
    var processgroupid = $('#processgroupid').val();
    
    var isvalidprocessgroupid = isvalidprocessid = isvalidtransactiondate = isvalidprocessedby = isvalidvendorid = isvalidmachineid = isvalidoutproducts = isvalidoutproductid = isvalidoutvariantid = isvalidunitid = isvalidquantity = isvaliduniqueoutproducts = isvalidbatchno = isvalidextrachargesid = isvalidextrachargeamount = isvalidduplicatecharges = 1;
    
    PNotify.removeAll();
    if(processgroupid==null) {
        $("#processgroup_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select process group !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidprocessgroupid = 0;
    } else {
        $("#processgroup_div").removeClass("has-error is-focused");
    }

    $(".pg").each(function(ind){
        var pgid = parseInt($(this).attr("id").match(/\d+/));
        var batchno = $('#batchno'+pgid).val();
        var processid = $('#processid'+pgid).val();
        var transactiondate = $('#transactiondate'+pgid).val();
        var processedby = $('input[name="processedby['+pgid+']"]:checked').val();
        var vendorid = $('#vendorid'+pgid).val();
        var machineid = $('#machineid'+pgid).val();

        if(processid==0) {
            $("#process_div"+pgid).addClass("has-error is-focused");
            new PNotify({title: 'Please select process in '+(ind+1)+' group !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidprocessid = 0;
        } else {
            $("#process_div"+pgid).removeClass("has-error is-focused");
        } 
        if(batchno=="") {
            $("#batchno_div"+pgid).addClass("has-error is-focused");
            new PNotify({title: 'Please enter batch no. in '+(ind+1)+' group !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidbatchno = 0;
        } else {
            $("#batchno_div"+pgid).removeClass("has-error is-focused");
        }
        if(transactiondate=="") {
            $("#transactiondate_div"+pgid).addClass("has-error is-focused");
            new PNotify({title: 'Please select transaction date in '+(ind+1)+' group !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidtransactiondate = 0;
        } else {
            $("#transactiondate_div"+pgid).removeClass("has-error is-focused");
        } 
        if(processedby==0 && vendorid==0) {
            $("#vendor_div"+pgid).addClass("has-error is-focused");
            new PNotify({title: 'Please select vendor in '+(ind+1)+' group !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidvendorid = 0;
        } else {
            $("#vendor_div"+pgid).removeClass("has-error is-focused");
        } 
        if(processedby==1 && machineid==0 && $("#machineid option").length > 1) {
            $("#machine_div"+pgid).addClass("has-error is-focused");
            new PNotify({title: 'Please select machine in '+(ind+1)+' group !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidmachineid = 0;
        } else {
            $("#machine_div"+pgid).removeClass("has-error is-focused");
        } 
        if(isvalidprocessgroupid == 1 && isvalidprocessid == 1){
           
            /* if($("select.outproductid").length == 0){
                new PNotify({title: 'Please add one or more product on process !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidoutproducts = 0;
            }else{ */
            if($("select.outproductid"+pgid).length > 0){
                var c=1;
                var firstoutproductid = $('.countoutproducts'+pgid+':first').attr('id').match(/\d+/);
                $('.countoutproducts'+pgid).each(function(){
                    // var rowid = $(this).attr("id").match(/\d+/);
                    var element = $(this).attr('id').split('_');
                    var rowid = element[2];

                    if($("#outproductid_"+pgid+"_"+rowid).val() > 0 || $("#outproductvariantid_"+pgid+"_"+rowid).val() > 0 || $("#unitid_"+pgid+"_"+rowid).val() != "" || $("#quantity_"+pgid+"_"+rowid).val()!=0 || rowid==firstoutproductid){
                        if($("#outproductid_"+pgid+"_"+rowid).val() == 0){
                            $("#outproduct_"+pgid+"_"+rowid+"_div").addClass("has-error is-focused");
                            new PNotify({title: 'Please select '+(c)+' OUT product in '+(ind+1)+' group !',styling: 'fontawesome',delay: '3000',type: 'error'});
                            isvalidoutproductid = 0;
                        }else {
                            $("#outproduct_"+pgid+"_"+rowid+"_div").removeClass("has-error is-focused");
                        }
                        if($("#outproductvariantid_"+pgid+"_"+rowid).val() == 0){
                            $("#outproductvariant_"+pgid+"_"+rowid+"_div").addClass("has-error is-focused");
                            new PNotify({title: 'Please select '+(c)+' OUT product variant in '+(ind+1)+' group !',styling: 'fontawesome',delay: '3000',type: 'error'});
                            isvalidoutvariantid = 0;
                        }else {
                            $("#outproductvariant_"+pgid+"_"+rowid+"_div").removeClass("has-error is-focused");
                        }
                        if($("#unitid_"+pgid+"_"+rowid).val() == ""){
                            $("#unit_"+pgid+"_"+rowid+"_div").addClass("has-error is-focused");
                            new PNotify({title: 'Please enter '+(c)+' OUT product unit in '+(ind+1)+' group !',styling: 'fontawesome',delay: '3000',type: 'error'});
                            isvalidunitid = 0;
                        }else {
                            $("#unit_"+pgid+"_"+rowid+"_div").removeClass("has-error is-focused");
                        }
                        if($("#quantity_"+pgid+"_"+rowid).val() == 0){
                            $("#quantity_"+pgid+"_"+rowid+"_div").addClass("has-error is-focused");
                            new PNotify({title: 'Please enter '+(c)+' OUT product quantity in '+(ind+1)+' group !',styling: 'fontawesome',delay: '3000',type: 'error'});
                            isvalidquantity = 0;
                        }else {
                            $("#quantity_"+pgid+"_"+rowid+"_div").removeClass("has-error is-focused");
                        }
                    } else{
                        $("#outproduct_"+pgid+"_"+rowid+"_div").removeClass("has-error is-focused");
                        $("#outproductvariant_"+pgid+"_"+rowid+"_div").removeClass("has-error is-focused");
                        $("#unit_"+pgid+"_"+rowid+"_div").removeClass("has-error is-focused");
                        $("#quantity_"+pgid+"_"+rowid+"_div").removeClass("has-error is-focused");
                    }
                    c++;
                });
    
                var outproducts = $('select[name="outproductvariantid['+pgid+'][]"]');
                var outvalues = [];
                for(j=0;j<outproducts.length;j++) {
                    
                    var uniqueoutproducts = outproducts[j];
                    // var rowid = uniqueoutproducts.id.match(/\d+/);
                    var element = uniqueoutproducts.id.split('_');
                    var rowid = element[2];

                    if(uniqueoutproducts.value!="" && $("#outproductvariantid_"+pgid+"_"+rowid+" option:selected").text()!="Select Variant"){
                        if(outvalues.indexOf(uniqueoutproducts.value)>-1) {
                            $("#outproductvariant_"+pgid+"_"+rowid+"_div").addClass("has-error is-focused");
                            new PNotify({title: 'Please select '+(j+1)+' is different OUT variant in '+(ind+1)+' group !',styling: 'fontawesome',delay: '3000',type: 'error'});
                            isvaliduniqueoutproducts = 0;
                        }
                        else{ 
                            outvalues.push(uniqueoutproducts.value);
                            if(($("#outproductvariantid_"+pgid+"_"+rowid).val()!="" && $("#outproductvariantid_"+pgid+"_"+rowid+" option:selected").text()!="Select Variant")){
                                $("#outproductvariantid_"+pgid+"_"+rowid+"_div").removeClass("has-error is-focused");
                            }
                        }
                    }
                }
            }
    
            var i=1;
            $('.countoutcharges'+pgid).each(function(){
                // var id = $(this).attr('id').match(/\d+/);
                var element = $(this).attr('id').split('_');
                var id = element[2];
                
                if($("#outextrachargesid_"+pgid+"_"+id).val() > 0 || $("#outextrachargeamount_"+pgid+"_"+id).val() > 0){
        
                    if($("#outextrachargesid_"+pgid+"_"+id).val() == 0){
                        $("#outextracharges_"+pgid+"_"+id+"_div").addClass("has-error is-focused");
                        new PNotify({title: 'Please select '+(i)+' extra charges of stock out in '+(ind+1)+' group !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        isvalidextrachargesid = 0;
                    }else {
                        $("#outextracharges_"+pgid+"_"+id+"_div").removeClass("has-error is-focused");
                    }
                    if($("#outextrachargeamount_"+pgid+"_"+id).val() == '' || $("#outextrachargeamount_"+pgid+"_"+id).val() == 0){
                        $("#outextrachargeamount_"+pgid+"_"+id+"_div").addClass("has-error is-focused");
                        new PNotify({title: 'Please enter '+(i)+' extra charge amount of stock out in '+(ind+1)+' group !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        isvalidextrachargeamount = 0;
                    }else {
                        $("#outextrachargeamount_"+pgid+"_"+id+"_div").removeClass("has-error is-focused");
                    }
                } else{
                    $("#outextracharges_"+pgid+"_"+id+"_div").removeClass("has-error is-focused");
                    $("#outextrachargeamount_"+pgid+"_"+id+"_div").removeClass("has-error is-focused");
                }
                i++;
            });
    
            var selects_charges = $('select[name="outextrachargesid['+pgid+'][]"]');
            var values = [];
            for(j=0;j<selects_charges.length;j++) {
                var selectscharges = selects_charges[j];
                // var id = selectscharges.id.match(/\d+/);
                var element = selectscharges.id.split('_');
                var id = element[2];

                if(selectscharges.value!=0){
                    if(values.indexOf(selectscharges.value)>-1) {
                        $("#outextracharges_"+pgid+"_"+id[0]+"_div").addClass("has-error is-focused");
                        new PNotify({title: 'Please select '+(j+1)+' is different extra charges of stock out in '+(ind+1)+' group !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        isvalidduplicatecharges = 0;
                    }
                    else{ 
                        values.push(selectscharges.value);
                        if($("#outextrachargesid_"+pgid+"_"+id[0]).val()!=0){
                            $("#outextracharges_"+pgid+"_"+id[0]+"_div").removeClass("has-error is-focused");
                        }
                    }
                }
            }
        }
    });
    
    if(isvalidprocessgroupid == 1 && isvalidprocessid == 1 && isvalidtransactiondate == 1 && isvalidvendorid == 1 && isvalidmachineid == 1 && isvalidoutproducts == 1 && isvalidoutproductid == 1 && isvalidoutvariantid == 1 && isvalidunitid == 1 && isvalidquantity == 1 && isvaliduniqueoutproducts == 1 && isvalidbatchno == 1 && isvalidextrachargesid == 1 && isvalidextrachargeamount == 1 && isvalidduplicatecharges == 1){
        var formData = new FormData($('#product-process-form')[0]);
        if(ACTION == 0){ // INSERT
            var baseurl = SITE_URL + 'product-process/product-process-add';
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
                    $("#name_div").removeClass("has-error is-focused");
                    if(data['error']==1){
                        new PNotify({title: 'Product start new process successfully added.',styling: 'fontawesome',delay: '3000',type: 'success'});
                        if(addtype==1){
                            resetdata();
                        }else{
                            setTimeout(function() { window.location = SITE_URL + "product-process";}, 500);
                        }
                    }else if(data['error']==2){
                        new PNotify({title: 'Product process already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        $("#name_div").addClass("has-error is-focused");
                    }else if(data['error']==-3){
                        new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else{
                        new PNotify({title: 'Product process not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
        }else{
            var baseurl = SITE_URL + 'product-process/update-product-process';
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
                    $("#name_div").removeClass("has-error is-focused");
                    if(data['error']==1){
                        new PNotify({title: 'Stock out process successfully updated.',styling: 'fontawesome',delay: '3000',type: 'success'});
                        setTimeout(function() { window.location = SITE_URL + "product-process";}, 500);
                    }else if(data['error']==2){
                        new PNotify({title: 'Product process already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        $("#name_div").addClass("has-error is-focused");
                    }else if(data['error']==-3){
                        new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else{
                        new PNotify({title: 'Product process not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
function checkvalidationstockin() {
   
    var pgid = $("#postprocessgroupid").val();
    var transactiondate = $('#transactiondate'+pgid).val();
    var isCertificate = $('#isCertificate').val();
    var isRejection = $('#isRejection').val();
    var isWastage = $('#isWastage').val();
    var isLost = $('#isLost').val();

    var isvalidtransactiondate = isvalidinproductid = isvalidinvariantid = isvalidunitid = isvalidquantity = isvaliduniqueproducts = isvaliddocno = isvaliddoctitle = isvaliddocfile = isvalidextrachargesid = isvalidextrachargeamount = isvalidduplicatecharges = isvalidprcessqty = 1;
    var isvalidprocessoption = 0;

    PNotify.removeAll();
    if(transactiondate=="") {
        $("#transactiondate_div"+pgid).addClass("has-error is-focused");
        new PNotify({title: 'Please select transaction date !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidtransactiondate = 0;
    } else {
        $("#transactiondate_div"+pgid).removeClass("has-error is-focused");
    } 
    var count = 0;
    $('input[name="outproductprocessdetailid['+pgid+'][]"]').each(function(index){
        // var divid = $(this).attr("id").match(/\d+/);
        var element = $(this).attr('id').split('_');
        var divid = element[2];

        var rejectionqty = $("#rejection_"+pgid+"_"+divid).val(); 
        var wastageqty = $("#wastage_"+pgid+"_"+divid).val(); 
        var lostqty = $("#lost_"+pgid+"_"+divid).val(); 
        var rejectionunitid = $("#rejectionunitid_"+pgid+"_"+divid).val(); 
        var wastageunitid = $("#wastageunitid_"+pgid+"_"+divid).val(); 
        var lostunitid = $("#lostunitid_"+pgid+"_"+divid).val(); 

        var isvalidrejectionunit = isvalidwastageunit = isvalidlostunit = 1;

        if(rejectionunitid == 0 && rejectionqty != "" && rejectionqty != 0) {
            $("#rejectionunit_"+pgid+"_"+divid+"_div").addClass("has-error is-focused");
            new PNotify({title: 'Please select '+(index+1)+' unit on rejection !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidrejectionunit = 0;
        } else {
            $("#rejectionunit_"+pgid+"_"+divid+"_div").removeClass("has-error is-focused");
        }
        if(wastageunitid == 0 && wastageqty != "" && wastageqty != 0) {
            $("#wastageunit_"+pgid+"_"+divid+"_div").addClass("has-error is-focused");
            new PNotify({title: 'Please select '+(index+1)+' unit on wastage !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidwastageunit = 0;
        } else {
            $("#wastageunit_"+pgid+"_"+divid+"_div").removeClass("has-error is-focused");
        }
        if(lostunitid == 0 && lostqty != "" && lostqty != 0) {
            $("#lostunit_"+pgid+"_"+divid+"_div").addClass("has-error is-focused");
            new PNotify({title: 'Please select '+(index+1)+' unit on lost !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidlostunit = 0;
        } else {
            $("#lostunit_"+pgid+"_"+divid+"_div").removeClass("has-error is-focused");
        }

        if(isvalidrejectionunit == 1 && isvalidwastageunit == 1 && isvalidlostunit == 1){
            ++count;
        }
    });
    if($('input[name="outproductprocessdetailid['+pgid+'][]"]').length == count){
        isvalidprocessoption = 1;
    }
    var c=1;
    var countqty = 0;
    var firstinproductid = parseInt($('.countinproducts'+pgid+':first').attr('id').split('_')[2]);
    
    $('.countinproducts'+pgid).each(function(){
        // var rowid = $(this).attr("id").match(/\d+/);
        var element = $(this).attr('id').split('_');
        var rowid = element[2];

        if($("#inproductid_"+pgid+"_"+rowid).val() > 0 || $("#inproductvariantid_"+pgid+"_"+rowid).val() > 0 || $("#inquantity_"+pgid+"_"+rowid).val()!=0 || rowid==firstinproductid){
            if($("#inproductid_"+pgid+"_"+rowid).val() == 0){
                $("#inproduct_"+pgid+"_"+rowid+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(c)+' IN product !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidinproductid = 0;
            }else {
                $("#inproduct_"+pgid+"_"+rowid+"_div").removeClass("has-error is-focused");
            }
            if($("#inproductvariantid_"+pgid+"_"+rowid).val() == 0){
                $("#inproductvariant_"+pgid+"_"+rowid+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(c)+' IN product variant !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidinvariantid = 0;
            }else {
                $("#inproductvariant_"+pgid+"_"+rowid+"_div").removeClass("has-error is-focused");
            }
            if($("#inquantity_"+pgid+"_"+rowid).val() != 0){
                /* $("#inquantity"+rowid+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please enter '+(c)+' IN product quantity !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidquantity = 0;
            }else { */
                totalstockqty = 0;
                $('.stockqty'+rowid).each(function(){
                    if($(this).val()!=''){
                        var qty = $(this).val();
                        var element = $(this).attr('id');

                        /* if($("#outqty_"+element[1]+"_"+element[2]).val()>qty){
                            isvalidquantity = 0; 
                            new PNotify({title: 'Please enter quantity of '+(c)+'!',styling: 'fontawesome',delay: '3000',type: 'error'});
                        } */
                    }else{
                        qty = 0;
                    }
                    totalstockqty += parseFloat(qty);
                });

                if(totalstockqty>$("#inquantity_"+pgid+"_"+rowid).val()){
                    
                    new PNotify({title: 'Please enter quantity of '+(c)+'!',styling: 'fontawesome',delay: '3000',type: 'error'});
                    isvalidquantity = 0;
                    $('.stockqty'+rowid).each(function(){
                        $(this).val('');
                    });
                }else{
                    $("#inquantity_"+pgid+"_"+rowid+"_div").removeClass("has-error is-focused");
                }
            }else{
                countqty++;
            }

            var inqty = $("#inquantity_"+pgid+"_"+rowid).val();
            if(inqty != 0 && isvalidinvariantid==1){
                var maxinqty = $("#inquantity_"+pgid+"_"+rowid).attr("data-maxqty");
                var productpricesid = $("#inproductvariantid_"+pgid+"_"+rowid).val();

                var qty = 0;
                $(".outproductpricesid"+pgid).each(function(index){
                    
                    var outproductpricesid = $("#outproductpricesid_"+pgid+"_"+(index+1)).val();
                    if(outproductpricesid == productpricesid){
                        
                        if(parseInt(isRejection)==1){
                            if($("#rejection_"+pgid+"_"+(index+1)).val() != ""){
                                qty += parseFloat($("#rejection_"+pgid+"_"+(index+1)).val());
                            }
                        }
                        if(parseInt(isWastage)==1){
                            if($("#wastage_"+pgid+"_"+(index+1)).val() != ""){
                                qty += parseFloat($("#wastage_"+pgid+"_"+(index+1)).val());
                            }
                        }
                        if(parseInt(isLost)==1){
                            if($("#lost_"+pgid+"_"+(index+1)).val() != ""){
                                qty += parseFloat($("#lost_"+pgid+"_"+(index+1)).val());
                            }
                        }
                    }
                });

                if((parseFloat(inqty) + parseFloat(qty)) > parseFloat(maxinqty) && parseFloat(maxinqty) > 0){
                    $("#inquantity_"+pgid+"_"+rowid+"_div").addClass("has-error is-focused");
                    new PNotify({title: 'Maximum '+parseFloat(maxinqty)+' qty allow on '+(c)+' IN product quantity  !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    isvalidprcessqty = 0;
                }
            }

        } else{
            $("#inproduct_"+pgid+"_"+rowid+"_div").removeClass("has-error is-focused");
            $("#inproductvariant_"+pgid+"_"+rowid+"_div").removeClass("has-error is-focused");
            $("#inquantity_"+pgid+"_"+rowid+"_div").removeClass("has-error is-focused");
        }
        c++;
    });
    // if(countqty == $('.countinproducts'+pgid).length){
    //     new PNotify({title: 'Please enter atleast 1 IN products quantity !',styling: 'fontawesome',delay: '3000',type: 'error'});
    //     isvalidquantity = 0;
    // }

    var products = $('select[name="inproductvariantid['+pgid+'][]"]');
    var values = [];
    for(j=0;j<products.length;j++) {
        
        var uniqueproducts = products[j];
        // var rowid = uniqueproducts.id.match(/\d+/);
        var rowid = uniqueproducts.id.split('_')[2];
                
        if(uniqueproducts.value!="" && $("#inproductvariantid_"+pgid+"_"+rowid+" option:selected").text()!="Select Variant"){
            if(values.indexOf(uniqueproducts.value)>-1) {
                $("#inproductvariant_"+pgid+"_"+rowid+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(j+1)+' is different IN variant !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvaliduniqueproducts = 0;
            }
            else{ 
                values.push(uniqueproducts.value);
                if(($("#inproductvariantid_"+pgid+"_"+rowid).val()!="" && $("#inproductvariantid_"+pgid+"_"+rowid+" option:selected").text()!="Select Variant")){
                    $("#inproductvariantid_"+pgid+"_"+rowid+"_div").removeClass("has-error is-focused");
                }
            }
        }
    }

    var i=1;
    $('.countincharges'+pgid).each(function(){
        var id = $(this).attr('id').match(/\d+/);
        
        if($("#inextrachargesid_"+pgid+"_"+id).val() > 0 || $("#inextrachargeamount_"+pgid+"_"+id).val() > 0){

            if($("#inextrachargesid_"+pgid+"_"+id).val() == 0){
                $("#inextracharges_"+pgid+"_"+id+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(i)+' extra charges of stock in !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidextrachargesid = 0;
            }else {
                $("#inextracharges_"+pgid+"_"+id+"_div").removeClass("has-error is-focused");
            }
            if($("#inextrachargeamount_"+pgid+"_"+id).val() == '' || $("#inextrachargeamount_"+pgid+"_"+id).val() == 0){
                $("#inextrachargeamount_"+pgid+"_"+id+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please enter '+(i)+' extra charge amount of stock in !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidextrachargeamount = 0;
            }else {
                $("#inextrachargeamount_"+pgid+"_"+id+"_div").removeClass("has-error is-focused");
            }
        } else{
            $("#inextracharges_"+pgid+"_"+id+"_div").removeClass("has-error is-focused");
            $("#inextrachargeamount_"+pgid+"_"+id+"_div").removeClass("has-error is-focused");
        }
        i++;
    });

    var selects_charges = $('select[name="inextrachargesid['+pgid+'][]"]');
    var values = [];
    for(j=0;j<selects_charges.length;j++) {
        var selectscharges = selects_charges[j];
        var id = selectscharges.id.split('_')[2];

        if(selectscharges.value!=0){
            if(values.indexOf(selectscharges.value)>-1) {
                $("#inextracharges_"+pgid+"_"+id[0]+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(j+1)+' is different extra charges of stock in !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidduplicatecharges = 0;
            }
            else{ 
                values.push(selectscharges.value);
                if($("#inextrachargesid_"+pgid+"_"+id[0]).val()!=0){
                $("#inextracharges_"+pgid+"_"+id[0]+"_div").removeClass("has-error is-focused");
                }
            }
        }
    }
    
    if($('.countcertificates').length > 0){
        
        var c=1;
        var firstcertificateid = $('.countcertificates:first').attr('id').match(/\d+/);
        $('.countcertificates').each(function(){
            var rowid = $(this).attr("id").match(/\d+/);
            if($("#docno"+rowid).val() !="" || $("#doctitle"+rowid).val() != "" || $("#docfile"+rowid).val()!="" || parseInt(rowid)==parseInt(firstcertificateid)){

                if($("#docno"+rowid).val() == ""){
                    $("#docno"+rowid+"_div").addClass("has-error is-focused");
                    new PNotify({title: 'Please enter '+(c)+' document no. !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    isvaliddocno = 0;
                }else {
                    $("#docno"+rowid+"_div").removeClass("has-error is-focused");
                }
                if($("#doctitle"+rowid).val() == ""){
                    $("#doctitle"+rowid+"_div").addClass("has-error is-focused");
                    new PNotify({title: 'Please enter '+(c)+' document title !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    isvaliddoctitle = 0;
                }else {
                    $("#doctitle"+rowid+"_div").removeClass("has-error is-focused");
                }
                if($("#docfile"+rowid).val() == "" && $("#isvaliddocfile"+rowid).val() == "0"){
                    $("#docfile"+rowid+"_div").addClass("has-error is-focused");
                    new PNotify({title: 'Please select '+(c)+' file !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    isvaliddocfile = 0;
                }else if($("#docfile"+rowid).val() != "" && $("#isvaliddocfile"+rowid).val() == "0"){
                    $("#docfile"+rowid+"_div").addClass("has-error is-focused");
                    new PNotify({title: 'Please select '+(c)+' valid file !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    isvaliddocfile = 0;
                }else{
                    $("#docfile"+rowid+"_div").removeClass("has-error is-focused");
                }
            } else{
                $("#docno"+rowid+"_div").removeClass("has-error is-focused");
                $("#doctitle"+rowid+"_div").removeClass("has-error is-focused");
                $("#docfile"+rowid+"_div").removeClass("has-error is-focused");
            }
            c++;
        });
    }
    
    if(isvalidtransactiondate == 1 && isvalidinproductid == 1 && isvalidinvariantid == 1 && isvalidquantity == 1 && isvaliduniqueproducts == 1 && isvaliddocno == 1 && isvaliddoctitle == 1 && isvaliddocfile == 1 && isvalidprocessoption == 1 && isvalidextrachargesid == 1 && isvalidextrachargeamount == 1 && isvalidduplicatecharges == 1 && isvalidprcessqty == 1){
        var formData = new FormData($('#product-process-form')[0]);
        if(ACTION == 0){ // INSERT
            var baseurl = SITE_URL + 'product-process/product-process-add';
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
                    if(data['error']==1){
                        new PNotify({title: 'Product stock in process successfully added.',styling: 'fontawesome',delay: '3000',type: 'success'});
                        setTimeout(function() { window.location = SITE_URL + "product-process";}, 500);
                    }else if(data['error']==-2){
                        new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
                        $("#docfile"+data['id']+"_div").addClass("has-error is-focused");
                    }else if(data['error']==-1){
                        new PNotify({title: 'File is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE)+') !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        $("#docfile"+data['id']+"_div").addClass("has-error is-focused");
                    }else{
                        new PNotify({title: 'Product stock in process not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
        }else{
            var baseurl = SITE_URL + 'product-process/update-product-process';
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
                    if(data['error']==1){
                        new PNotify({title: 'Product stock in process successfully updated.',styling: 'fontawesome',delay: '3000',type: 'success'});
                        setTimeout(function() { window.location = SITE_URL + "product-process";}, 500);
                    }else if(data['error']==-2){
                        new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
                        $("#docfile"+data['id']+"_div").addClass("has-error is-focused");
                    }else if(data['error']==-1){
                        new PNotify({title: 'File is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE)+') !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        $("#docfile"+data['id']+"_div").addClass("has-error is-focused");
                    }else{
                        new PNotify({title: 'Product stock in process not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
function generateproductprocess(){

    var HTML = "";

    var processgroupidarray = $('#processgroupid').map(function(index,element){ return $(element).val(); }).toArray();
    var processarray = $('#processgroupid option:selected').map(function(index,element){ return $(element).attr("data-process"); }).toArray();
    var gropnamearray = $('#processgroupid option:selected').map(function(index,element){ return $(element).text(); }).toArray();
    
    PRODUCT_OPTION_DATA_ARR = [];
    // PROCESS_GROUP_IDS_ARRAY = [];
    
    if(processgroupidarray.length > 0){
        
        for(var i=0;i<processgroupidarray.length;i++){
            var pgid = processgroupidarray[i];
            var gropname = gropnamearray[i];
            var processdata = processarray[i];
            
            
            if(PROCESS_GROUP_IDS_ARRAY==null || PROCESS_GROUP_IDS_ARRAY.indexOf(pgid) == -1){  
                PROCESS_GROUP_IDS_ARRAY.push(pgid); 

                if(Edit_outextracharges!=""){
                    var Ex_charges = Edit_outextracharges;
                }else{

                    var Ex_charges = '<div class="col-md-4 p-n countoutcharges'+pgid+'" id="countoutcharges_'+pgid+'_1">\
                        <div class="col-sm-6 pr-xs">\
                            <div class="form-group p-n" id="outextracharges_'+pgid+'_1_div">\
                                <div class="col-sm-12">\
                                    <select id="outextrachargesid_'+pgid+'_1" name="outextrachargesid['+pgid+'][]" class="selectpicker form-control outextrachargesid'+pgid+'" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                        <option value="0">Select Charges</option>\
                                        '+EXTRA_CHARGES_OPTIONS+'\
                                    </select>\
                                    <input type="hidden" name="outextrachargestax['+pgid+'][]" id="outextrachargestax_'+pgid+'_1" class="outextrachargestax'+pgid+'" value="">\
                                    <input type="hidden" name="outextrachargesname['+pgid+'][]" id="outextrachargesname_'+pgid+'_1" class="outextrachargesname'+pgid+'" value="">\
                                    <input type="hidden" name="outextrachargepercentage['+pgid+'][]" id="outextrachargepercentage_'+pgid+'_1" class="outextrachargepercentage'+pgid+'" value="">\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-sm-3 pl-xs pr-xs">\
                            <div class="form-group p-n" id="outextrachargeamount_'+pgid+'_1_div">\
                                <div class="col-sm-12">\
                                    <input type="text" id="outextrachargeamount_'+pgid+'_1" name="outextrachargeamount['+pgid+'][]" class="form-control text-right outextrachargeamount'+pgid+'" placeholder="Charge" onkeypress="return decimal_number_validation(event, this.value,8)">\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-3 text-right pt-md">\
                            <button type="button" class="btn btn-default btn-raised remove_outcharges_btn'+pgid+' m-n" onclick="removecharge(1,'+pgid+')" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>\
                            <button type="button" class="btn btn-default btn-raised add_outcharges_btn'+pgid+' m-n" onclick="addnewcharge('+pgid+')" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>\
                        </div>\
                    </div>';
                }
                var production_plan_group = [];
                
                if(production_plan!=""){
                    var productionplandata = JSON.parse(production_plan);
                    if(productionplandata.length > 0){ 
                        for(var p=0;p<productionplandata.length;p++){
                            if(productionplandata[p]['processgroupid'] == pgid){
                                production_plan_group.push(productionplandata[p]);
                            }
                        }
                    } 
                }
                HTML = '<div class="panel panel-default border-panel pg" id="pg'+pgid+'">\
                            <div class="panel-heading">\
                                <h2>Process Group : '+gropname+'</h2>\
                            </div>\
                            <div class="panel-body">\
                                <input type="hidden" name="processbymemberid['+pgid+']" value="'+Edit_processbymemberid+'">\
                                <input type="hidden" name="postvendorid['+pgid+']" value="'+Edit_postvendorid+'">\
                                <input type="hidden" name="postmachineid['+pgid+']" value="'+Edit_postmachineid+'">\
                                <input type="hidden" name="postestimatedate['+pgid+']" value="'+Edit_postestimatedate+'">\
                                <input type="hidden" name="postorderid['+pgid+']" value="'+Edit_postorderid+'"></input>\
                                <input type="hidden" id="productprocessid'+pgid+'" name="productprocessid['+pgid+']" value="'+Edit_productprocessid+'">\
                                <input type="hidden" id="mainbatchprocessid'+pgid+'" name="mainbatchprocessid['+pgid+']" value="'+Edit_mainbatchprocessid+'">\
                                <input type="hidden" id="parentproductprocessid'+pgid+'" name="parentproductprocessid['+pgid+']" value="'+Edit_parentproductprocessid+'">\
                                <textarea name="productionplanqtydetail['+pgid+']" id="productionplanqtydetail'+pgid+'" style="display:none;">'+(production_plan_group.length>0?JSON.stringify(production_plan_group):"")+'</textarea>\
                                <div class="col-sm-12">\
                                    \
                                    <div class="row">\
                                        <div class="col-md-6">\
                                            <div class="form-group" id="process_div'+pgid+'">\
                                                <label class="col-md-3 control-label" for="processid'+pgid+'">Process <span class="mandatoryfield">*</span></label>\
                                                <div class="col-md-9">\
                                                    <textarea style="display:none;" id="jsonprocessid'+pgid+'">'+processdata+'</textarea>\
                                                    <input type="hidden" name="processgroupmappingid['+pgid+']" id="processgroupmappingid'+pgid+'" value="'+Edit_processgroupmappingid+'">\
                                                    <select id="processid'+pgid+'" name="processid['+pgid+']" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8" '+Edit_disabled+'>\
                                                        <option value="0">Select Process</option>\
                                                    </select>\
                                                    <div id="noofsequence'+pgid+'"></div>\
                                                </div>\
                                            </div>\
                                            <div class="form-group">\
                                                <label for="focusedinput" class="col-md-3 control-label">Processed By</label>\
                                                <div class="col-md-9 mt-xs">\
                                                    <div class="col-md-6 col-xs-4" style="padding-left: 0px;">\
                                                        <div class="radio">\
                                                            <input type="radio" name="processedby['+pgid+']" id="inhouse'+pgid+'" value="1" '+Edit_checkinemp+' '+(PROCESSTYPE=="IN"?DISABLED:"")+'>\
                                                            <label for="inhouse'+pgid+'">In-House Emp</label>\
                                                        </div>\
                                                    </div>\
                                                    <div class="col-md-6 col-xs-4">\
                                                        <div class="radio">\
                                                            <input type="radio" name="processedby['+pgid+']" id="otherparty'+pgid+'" value="0" '+Edit_checkotherparty+' '+(PROCESSTYPE=="IN"?DISABLED:"")+'>\
                                                            <label for="otherparty'+pgid+'">Other Party</label>\
                                                        </div>\
                                                    </div>\
                                                </div>\
                                            </div>\
                                            <div class="form-group" id="vendor_div'+pgid+'" style="'+Edit_outdisabled+'">\
                                                <label class="col-md-3 control-label" for="vendorid'+pgid+'">Vendor</label>\
                                                <div class="col-md-9">\
                                                    <select id="vendorid'+pgid+'" name="vendorid['+pgid+']" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5" '+Edit_vendordisabled+'>\
                                                        <option value="0">Select Vendor</option>\
                                                        '+VENDORDATA+'\
                                                    </select>\
                                                </div>\
                                            </div>\
                                            <div class="form-group" id="machine_div'+pgid+'" style="'+Edit_machinehide+'">\
                                                <label class="col-md-3 control-label" for="machineid'+pgid+'">Machine</label>\
                                                <div class="col-md-9">\
                                                    <select id="machineid'+pgid+'" name="machineid['+pgid+']" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                                        <option value="0">Select Machine</option>\
                                                    </select>\
                                                </div>\
                                            </div>\
                                        </div>\
                                        <div class="col-md-6">\
                                            <div class="form-group" id="batchno_div'+pgid+'">\
                                                <label class="col-md-4 control-label" for="batchno'+pgid+'">Batch No. <span class="mandatoryfield">*</span></label>\
                                                <div class="col-md-8">\
                                                    <input id="batchno'+pgid+'" class="form-control" name="batchno['+pgid+']" value="'+(Edit_batchno!=""?Edit_batchno:(PROCESS_BATCH_NO+'-'+(i+1)))+'" '+(PROCESSTYPE=="IN"?"readonly":"")+'>\
                                                </div>\
                                            </div>\
                                            <div class="form-group" id="order_div'+pgid+'">\
                                                <label class="col-md-4 control-label" for="orderid'+pgid+'">Order</label>\
                                                <div class="col-md-8">\
                                                    <select id="orderid'+pgid+'" name="orderid['+pgid+']" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8" '+(PROCESSTYPE=="IN"?"readonly":"")+'>\
                                                        <option value="0">Select Order</option>\
                                                        '+ORDERDATA+'\
                                                    </select>\
                                                </div>\
                                            </div>\
                                            <div class="form-group" id="transactiondate_div'+pgid+'">\
                                                <label for="transactiondate'+pgid+'" class="col-md-4 control-label">Transaction Date <span class="mandatoryfield">*</span></label>\
                                                <div class="col-sm-8">\
                                                    <input id="transactiondate'+pgid+'" type="text" name="transactiondate['+pgid+']" value="'+Edit_transactiondate+'" class="form-control" readonly>\
                                                </div>\
                                            </div>\
                                            <div class="form-group" id="estimatedate_div'+pgid+'">\
                                                <label for="estimatedate'+pgid+'" class="col-md-4 control-label">Estimate Date</label>\
                                                <div class="col-sm-8">\
                                                    <input id="estimatedate'+pgid+'" type="text" name="estimatedate['+pgid+']" value="'+Edit_estimatedate+'" class="form-control" readonly>\
                                                </div>\
                                            </div>\
                                        </div>\
                                    </div>\
                                    \
                                    <div class="row" id="outproductsdiv'+pgid+'" style="'+(PROCESSTYPE=="OUT"?"display:none":"")+'">\
                                        <div class="col-md-12 p-n"><hr></div>\
                                        <div class="panel-heading"><h2><b>OUT Product Details</b></h2></div>\
                                        <div class="col-md-12 p-n" style="border-bottom: 1px solid #ddd;">\
                                            <div class="col-md-3">\
                                                <div class="form-group">\
                                                    <div class="col-sm-12">\
                                                        <label class="control-label"><b>Product</b></label>\
                                                    </div>\
                                                </div>\
                                            </div>\
                                            <div class="col-md-3">\
                                                <div class="form-group">\
                                                    <div class="col-sm-12">\
                                                        <label class="control-label"><b>Select Variant</b></label>\
                                                    </div>\
                                                </div>\
                                            </div>\
                                            <div class="col-md-2">\
                                                <div class="form-group">\
                                                    <div class="col-sm-12">\
                                                        <label class="control-label"><b>Unit</b></label>\
                                                    </div>\
                                                </div>\
                                            </div>\
                                            <div class="col-md-2">\
                                                <div class="form-group">\
                                                    <div class="col-sm-12 text-right">\
                                                        <label class="control-label"><b>Qty</b></label>\
                                                    </div>\
                                                </div>\
                                            </div>\
                                        </div>\
                                        <div class="col-md-12 p-n" id="productdata'+pgid+'">\
                                        </div>\
                                    </div>\
                                    \
                                    <div class="row" id="outextrachargesdiv'+pgid+'" style="'+(PROCESSTYPE=="IN"?"display:none":"display:none")+'">\
                                        <div class="col-md-12 p-n"><hr></div>\
                                        <div class="panel-heading"><h2><b>Extra Charges</b></h2></div>\
                                        <div class="col-md-12 p-n" id="extrachargesdata'+pgid+'">\
                                            '+Ex_charges+'\
                                        </div>\
                                    </div>\
                                    <div class="row">\
                                        <hr>\
                                        <div class="col-md-12">\
                                            <div class="form-group" id="remarks'+pgid+'_div">\
                                                <div class="col-md-12">\
                                                    <label class="control-label" for="remarks'+pgid+'">Remarks</label>\
                                                    <textarea id="remarks'+pgid+'" class="form-control" name="remarks['+pgid+']">'+Edit_comments+'</textarea>\
                                                </div>\
                                            </div>\
                                        </div>\
                                    </div>\
                                </div>\
                            </div>\
                        </div>';
    
    
                if(i==0){
                    $("#multiprocess").html(HTML);
                }else{
                    $("#multiprocess").append(HTML);
                }

                if(Edit_postvendorid!=""){
                    $("#vendorid"+pgid).val(Edit_postvendorid);
                }
                if(Edit_postorderid!=""){
                    $("#orderid"+pgid).val(Edit_postorderid);
                }
                $(".selectpicker").selectpicker("refresh");
                getprocess(pgid);
            }
            
        }
    }else{
        $("#multiprocess").html("");
        PROCESS_GROUP_IDS_ARRAY = [];
    }
    $(".pg").each(function(){
        var pgid = parseInt($(this).attr("id").match(/\d+/));
        $('#transactiondate'+pgid).datepicker({
            todayHighlight: true,
            format: 'dd/mm/yyyy',
            todayBtn:"linked",
        });
        $('#estimatedate'+pgid).datepicker({
            todayHighlight: true,
            format: 'dd/mm/yyyy',
            todayBtn:"linked",
            startDate: new Date()
        });
        $('#processid'+pgid).on('change', function (e) {
            getmachine(pgid);
            getoutproductsdata(pgid);
            getproduct(pgid);
            // getproductunit();
            
            var noofsequence="";
            var sequence = "1";
            if($(this).val() != 0){
                var processgroupmappingid = $('#processid'+pgid+' option:selected').attr("data-processgroupmappingid");
                $("#processgroupmappingid"+pgid).val(processgroupmappingid);
                var sequenceno = $('#processid'+pgid+' option:selected').attr("data-sequenceno");
                var maxsequenceno = $('#processid'+pgid+' option:selected').attr("data-maxsequenceno");

                noofsequence = "<label class='control-label'>Sequence "+sequenceno+" of "+maxsequenceno+"</label>";
                sequence = sequenceno;
            }
            $("#noofsequence"+pgid).html(noofsequence);
            $("#batchno"+pgid).val(PROCESS_BATCH_NO+"-"+sequence);
            
        });

        $('input[name="processedby['+pgid+']"]').change(function() {
            if($(this).val() == 0){
                $("#vendorid"+pgid).prop("disabled",false).selectpicker('refresh');
                $("#machineid"+pgid).val(0).prop("disabled",true).selectpicker('refresh');
                $("#vendor_div"+pgid).show();
                $("#machine_div"+pgid).hide();
            }else{
                $("#vendorid"+pgid).val(0).selectpicker('refresh');
                $("#vendorid"+pgid).prop("disabled",true).selectpicker('refresh');
                $("#machineid"+pgid).prop("disabled",false).selectpicker('refresh');
                $("#vendor_div"+pgid).hide();
                $("#machine_div"+pgid).show();
            }
        });

        $(document).on('change','select.outproductid'+pgid, function (e) {
            // var divid = $(this).attr('id').match(/\d+/);
            var element = $(this).attr('id').split('_');
            var divid = element[2];
            $("#productamount_"+pgid+"_"+divid).val('0');
            getproductvariant(divid,pgid);
            $("#outprice_"+pgid+"_"+divid).val($("#outproductvariantid_"+pgid+"_"+divid+" option:selected").attr("data-price"));
            $("#unitid_"+pgid+"_"+divid).val($("#outproductid_"+pgid+"_"+divid+" option:selected").attr("data-unit"));
        });
        $(document).on('change','select.outproductvariantid'+pgid, function (e) {
            // var divid = $(this).attr('id').match(/\d+/);
            var element = $(this).attr('id').split('_');
            var divid = element[2];
            $("#outprice_"+pgid+"_"+divid).val($("#outproductvariantid_"+pgid+"_"+divid+" option:selected").attr("data-price"));
            var price = 0;
            if($("#quantity_"+pgid+"_"+divid).val()!=""){
                price = parseFloat($("#outproductvariantid_"+pgid+"_"+divid+" option:selected").attr("data-price")) * parseFloat($("#quantity_"+pgid+"_"+divid).val());
            }
            $("#productamount_"+pgid+"_"+divid).val(parseFloat(price).toFixed(2));
            
            getOrderProductsForFIFO(divid,pgid);
            if(STOCK_MANAGE_BY==1){
                displayStockMessage(divid,pgid);
            }
        });
        /****EXTRA CHARGE CHANGE EVENT****/
        $(document).on('change', 'select.outextrachargesid'+pgid, function() { 
            // var rowid = $(this).attr("id").match(/\d+/);
            var element = $(this).attr('id').split('_');
            var rowid = element[2];
            calculateextracharges(rowid,0,pgid);
        });
        $(document).on('keyup', '.outextrachargeamount'+pgid, function() { 
            // var rowid = $(this).attr("id").match(/\d+/);
            var element = $(this).attr('id').split('_');
            var rowid = element[2];
            // calculateextracharges(rowid,0);
            var productamount = chargestaxamount = chargespercent = 0;
            $(".productamount"+pgid).each(function( index ) {
                var divid = $(this).attr("div-id");
                if($(this).val()!=""){
                    productamount += parseFloat($(this).val());
                }
            });
            var extrachargesid = $("#outextrachargesid_"+pgid+"_"+rowid).val();
            var tax = $("#outextrachargesid_"+pgid+"_"+rowid+" option:selected").attr("data-tax");
            var chargestype = $("#outextrachargesid_"+pgid+"_"+rowid+" option:selected").attr("data-type");
            var optiontext = $("#outextrachargesid_"+pgid+"_"+rowid+" option:selected").text();
        
            if(this.value!=''){
                if(chargestype==0){
                    if(parseFloat(this.value) > parseFloat(productamount)){
                        $(this).val(parseFloat(productamount).toFixed(2));
                    }
                }
                if(tax>0){
                    chargestaxamount = parseFloat(this.value) * parseFloat(tax) / (100+parseFloat(tax));
                }
                if(chargestype==0){
                    chargespercent = parseFloat(this.value) * 100 / parseFloat(productamount);
                    
                }
            }
            $("#outextrachargestax_"+pgid+"_"+rowid).val(parseFloat(chargestaxamount).toFixed(2));
            $("#outextrachargepercentage_"+pgid+"_"+rowid).val(parseFloat(chargespercent).toFixed(2));
            if(chargestype==0){
                optiontext = optiontext.split("(");
                $("#outextrachargesid_"+pgid+"_"+rowid+" option:selected").text(optiontext[0]+" ("+parseFloat(chargespercent).toFixed(2)+"%)");
                $("#outextrachargesid_"+pgid+"_"+rowid).selectpicker("refresh");
                $("#outextrachargesname_"+pgid+"_"+rowid).val(optiontext[0]+" ("+parseFloat(chargespercent).toFixed(2)+"%)");
            }
        });

        $(document).on('keyup', '.quantity'+pgid, function() { 
            // var rowid = $(this).attr("id").match(/\d+/);
            var element = $(this).attr('id').split('_');
            var rowid = element[2];
            var price = 0;
            if(this.value!=""){
                price = parseFloat($("#outproductvariantid_"+pgid+"_"+rowid+" option:selected").attr("data-price")) * parseFloat(this.value);
            }
            $("#productamount_"+pgid+"_"+rowid).val(parseFloat(price).toFixed(2));

            changeextrachargesamount(0,pgid);
        });
        $(document).on('keyup', '.quantity'+pgid, function() { 
            // var divid = $(this).attr("id").match(/\d+/);
            var element = $(this).attr('id').split('_');
            var divid = element[2];
            
            getorderproductqty(divid,pgid);
        });
        
        if(PROCESSTYPE!="IN"){
            $(".add_outcharges_btn"+pgid).hide();
            $(".add_outcharges_btn"+pgid+":last").show();
        }

    });
    
}

