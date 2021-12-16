$(document).ready(function() {   
    
    $('.applyoldprice').change(function() {
        var divid = $(this).attr("id").match(/(\d+)/g);
        if(this.checked) {
            var ordertax = $("#ordertax"+divid).val();
            var orderdiscount = $("#orderdiscount"+divid).val();
            $("#tax"+divid).val(ordertax);
            $("#discount"+divid).val(orderdiscount);
        }else{
            var producttax = $("#producttax"+divid).val();
            $("#tax"+divid).val(producttax);
            var discount = $("#combopriceid"+divid+" option:selected").attr("data-discount");
            if(discount > 0){
                $("#discount"+divid).val(discount);
            }
        }
        changeproductamount(divid);
        changeextrachargesamount();
        changenetamounttotal();
        $("#discount"+divid).val('');
        if(partialpayment==1 && EMIreceived==0){
            generateinstallment();
        }
    });
    $('.installmentdate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayBtn:"linked",
    });
    $('.paymentdate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        autoclose: true,
        endDate:new Date(),
        todayBtn:"linked",
        clearBtn: 'Clear',
    });
    $(".add_remove_btn").hide();
    $(".add_remove_btn:last").show();
    $(".add_charges_btn").hide();
    $(".add_charges_btn:last").show();
    $(".add_file_btn").hide();
    $(".add_file_btn:last").show();

    $('#emidate,#quotationdate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        todayBtn:"linked",
    });
    $("#qty1").TouchSpin(touchspinoptions);
    if(ACTION==1 || ISDUPLICATE==1){
        getbillingaddress();
        if(ISDUPLICATE==1){
            getChannelSettingByVendor();
        }
    }
    /**** VENDOR CHANGE EVENT****/
    $('#vendorid').on('change', function (e) {
        getbillingaddress();
        getChannelSettingByVendor();
        getproduct();
        
        var oldvendorid = $("#oldvendorid").val();
        $('.applyoldprice').prop("checked",false);
        $('.applyoldprice').prop("disabled",true);
        if(ACTION==1 && (this.value == oldvendorid && this.value!=0)){
            $('#applyoldprice'+divid).prop("disabled",false);
            $('#applyoldprice'+divid).prop("checked",true);
        }
        $(".producttaxamount").val('');
        changenetamounttotal();
        changeextrachargesamount();
    });
    /****PRODUCT CHANGE EVENT****/
    $('body').on('change', 'select.productid', function() { 
        var divid = $(this).attr("div-id");
        $("#producttaxamount"+divid).val('');
        $("#tax"+divid).val('');
        $('#actualprice'+divid).val("");

        var productid = $("#productid"+divid).val();
        var uniqueproduct = (productid!="" && productid!=0)?productid+"_0":"";
        $("#uniqueproduct"+divid).val(uniqueproduct);
        
        getproductprice(divid);

        var oldvendorid = $("#oldvendorid").val();
        var vendorid = (ACTION==1)?oldvendorid:$("#vendorid").val();
       
        $('#applyoldprice'+divid).prop("checked",false);
        $('#applyoldprice'+divid).prop("disabled",true);
        if(ACTION==1 && (this.value == oldproductid[divid-1] && this.value!=0) && vendorid==oldvendorid){
            $('#applyoldprice'+divid).prop("disabled",false);
            $('#applyoldprice'+divid).prop("checked",true);
        }
        
        $("#qty"+divid).val('1');
        $("#qty"+divid).trigger("touchspin.updatesettings", {min: 1, step: 1});
        $("#qty"+divid).prop("readonly",false);

        $("#discount"+divid+",#discountinrs"+divid+", #amount"+divid).val('');
        changeproductamount(divid);
        changeextrachargesamount();
        var discount = $("#productid"+divid+" option:selected").attr("data-discount");
        if(discount > 0){
            $("#discount"+divid).val(discount);
        }
        changenetamounttotal();
        if(partialpayment==1 && EMIreceived==0){
            generateinstallment();
        }
    });
    /****PRODUCT PRICE CHANGE EVENT****/
    $('body').on('change', 'select.priceid', function() { 
        var divid = $(this).attr("div-id");
        $("#producttaxamount"+divid).val('');
        $("#qty"+divid).val('1');
        $("#qty"+divid).trigger("touchspin.updatesettings", {min: 1, step: 1});
        $("#qty"+divid).prop("readonly",false);
        $("#amount"+divid+",#actualprice"+divid+",#discount"+divid+",#discountinrs"+divid).val('');

        if(this.value!=""){
            var referencetype = parseFloat($("#priceid"+divid+" option:selected").attr("data-referencetype").trim());
            $('#referencetype'+divid).val(parseInt(referencetype));
        }
        /* var tax = parseFloat($("#producttax"+divid).val());
        
        var actualprice = parseFloat($("#priceid"+divid+" option:selected").text().trim());
        var productrate = parseFloat(actualprice - ((actualprice * tax /(100+parseFloat(tax))))).toFixed(2);
        $('#productrate'+divid).val(productrate);
        if(this.value!=""){
            $('#actualprice'+divid).val(parseFloat(actualprice).toFixed(2));
        }else{
            $('#actualprice'+divid).val("");
        }
        $('#originalprice'+divid).val(parseFloat(actualprice).toFixed(2));

        var oldvendorid = $("#oldvendorid").val();
        var vendorid = (ACTION==1)?oldvendorid:$("#vendorid").val();
        var productid = $("#productid"+divid).val();
        $('#applyoldprice'+divid).prop("checked",false);
        $('#applyoldprice'+divid).prop("disabled",true);
        if(ACTION==1 && (this.value == oldpriceid[divid-1] && this.value!="") && productid==oldproductid[divid-1] && vendorid==oldvendorid){
            $('#applyoldprice'+divid).prop("disabled",false);
            $('#applyoldprice'+divid).prop("checked",true);
        } */
        getmultiplepricebypriceid(divid);
        calculatediscount(divid);
        changeproductamount(divid);
        changeextrachargesamount();
        //changenetamounttotal();
        if(partialpayment==1 && EMIreceived==0){
            generateinstallment();
        }

        var uniqueproduct = $("#uniqueproduct"+divid).val();
        if(uniqueproduct!=""){
            elementarr = uniqueproduct.split("_");
            var element1 = (this.value!="")?this.value:0;
            $("#uniqueproduct"+divid).val(elementarr[0]+"_"+element1);
        }
    });
    /****PRODUCT PRICE CHANGE EVENT****/
    $('body').on('change', 'select.combopriceid', function() { 
        var divid = $(this).attr("div-id");
        $("#qty"+divid).val('1');
        $("#qty"+divid).trigger("touchspin.updatesettings", {min: 1, step: 1});
        $("#qty"+divid).prop("readonly",false);
        $("#purchaseproductqty"+divid).val('0');
        $("#amount"+divid+",#actualprice"+divid+",#discount"+divid+",#discountinrs"+divid).val('');

        var tax = parseFloat($("#producttax"+divid).val());
        var quantitytype = $("#priceid"+divid+" option:selected").attr('data-quantitytype');
        var pricetype = $("#priceid"+divid+" option:selected").attr('data-pricetype');
        
        if(this.value!=""){
            var actualprice = parseFloat($("#combopriceid"+divid+" option:selected").attr("data-price"));
            var quantity = parseFloat($("#combopriceid"+divid+" option:selected").attr("data-quantity"));
            var productrate = parseFloat(actualprice - ((actualprice * tax /(100+parseFloat(tax))))).toFixed(2);
            $('#productrate'+divid).val(productrate);
            $('#actualprice'+divid).val(parseFloat(actualprice).toFixed(2));
            if(parseInt(quantitytype)==1 && parseInt(pricetype)==1){
                $("#qty"+divid).trigger("touchspin.updatesettings", {min: parseFloat(quantity), step: parseFloat(quantity)});
                $("#qty"+divid).prop("readonly",true);
            }else{
                $("#qty"+divid).trigger("touchspin.updatesettings", {min: 1, step: 1});
                $("#qty"+divid).prop("readonly",false);
            }
            $("#qty"+divid).val(parseFloat(quantity));
            $('#originalprice'+divid).val(parseFloat(actualprice).toFixed(2));
            
            var oldvendorid = $("#oldvendorid").val();
            var vendorid = (ACTION==1)?oldvendorid:$("#vendorid").val();
            var productid = $("#productid"+divid).val();
            var priceid = $("#priceid"+divid).val();
            $('#applyoldprice'+divid).prop("checked",false);
            $('#applyoldprice'+divid).prop("disabled",true);

            if(ACTION==1 && (parseFloat(actualprice).toFixed(2) == parseFloat(oldprice[divid-1]).toFixed(2) && actualprice!="") && productid==oldproductid[divid-1] && vendorid==oldvendorid && priceid==oldpriceid[divid-1]){
                $('#applyoldprice'+divid).prop("disabled",false);
                $('#applyoldprice'+divid).prop("checked",true);
            }

            var discount = $("#combopriceid"+divid+" option:selected").attr("data-discount");
            if(discount > 0){
                $("#discount"+divid).val(discount);
            }
        }else{
            $('#actualprice'+divid).val("");
            $('#productrate'+divid).val("");
            $('#originalprice'+divid).val(0);
            $("#discount"+divid).val("");
        }

        calculatediscount(divid);
        changeproductamount(divid);
        changeextrachargesamount();
        
        if(partialpayment==1 && EMIreceived==0){
            generateinstallment();
        }
    });
    /****ACTUAL PRICE CHANGE EVENT****/
    $('body').on('keyup', '.actualprice', function() {
        var divid = $(this).attr("div-id");
        
        var actualprice = (this.value!="")?parseFloat(this.value):0;
        
        var tax = parseFloat($("#producttax"+divid).val());
        var productrate = parseFloat(actualprice - ((actualprice * tax /(100+parseFloat(tax))))).toFixed(2);
        $('#productrate'+divid).val(parseFloat(productrate).toFixed(2));
        $('#originalprice'+divid).val(parseFloat(actualprice).toFixed(2));
        
        calculatediscount(divid);
        changeproductamount(divid);
        changeextrachargesamount();
        
        if(partialpayment==1 && EMIreceived==0 && $("#installmentdivs").html()!=""){
            generateinstallment();
        }
    });
    /****PRODUCT QUANTITY CHANGE EVENT****/
    $('body').on('change', '.qty', function() { 
        var divid = $(this).attr("div-id");
        calculatediscount(divid);
        changeproductamount(divid);
        changeextrachargesamount();

        updatematchprice(divid);
    });
    $('body').on('keyup', '.discount', function() { 
        var divid = $(this).attr("div-id");
        if(divid!=undefined){
            dicountvalue = $("#discount"+divid).val();
            if(parseFloat(dicountvalue)>99){
                $("#discount"+divid).val("100");
            }
            calculatediscount(divid);
            changeproductamount(divid);
            changeextrachargesamount();
        }
    });
    $('body').on('keyup', '.discountinrs', function(e) { 
        
        var divid = $(this).attr("div-id");
        if(divid!=undefined){
            calculatediscountamount(divid,$(this).val());
            changeproductamount(divid);
            changeextrachargesamount();
        }
    });
    $('body').on('keyup', '.tax', function() { 
        var divid = $(this).attr("div-id");
        if(divid!=undefined){
            taxvalue = $("#tax"+divid).val();
            if(parseFloat(taxvalue)>=100){
                $("#tax"+divid).val("100");
            }
            changeproductamount(divid);
            changeextrachargesamount();
        }
    });
    $('#overalldiscountpercent').on('keyup', function() { 
        var discountpercentage = $(this).val();
        var grossamount = $("#inputgrossamount").val();
        var gstongrossamount = parseFloat(grossamount);
        var productgstamount = 0;
        $(".producttaxamount").each(function( index ) {
            var divid = $(this).attr("div-id");
            if($(this).val()!="" && $("#qty"+divid).val() >0 ){
                productgstamount += parseFloat($(this).val());
            }
        });
        $('input[name="postofferproducttaxamount[]"]').each(function( index ) {
            if($(this).val()!=""){
                productgstamount += parseFloat($(this).val());
            }
        });
        
        if(GSTonDiscount == 1){
            gstongrossamount = parseFloat(grossamount) - parseFloat(productgstamount);
        }
        $('#discountrow').hide();
        if(discountpercentage!=undefined && discountpercentage!='' && parseFloat(discountpercentage) > 0 && (parseFloat(discountminamount) == 0 || parseFloat(gstongrossamount) >= parseFloat(discountminamount))){
            if(parseFloat(discountpercentage)>100){
                $(this).val("100");
            }
            if(gstongrossamount!=''){
                var discountamount = (parseFloat(gstongrossamount)*parseFloat(discountpercentage)/100);
                $("#overalldiscountamount").val(parseFloat(discountamount).toFixed(2));
                                
                $("#discountpercentage").html(parseFloat(discountpercentage).toFixed(2)); 
                $("#discountamount").html(format.format(discountamount)); 
                changeextrachargesamount();
                var extrachargesamount = 0;
                $(".extrachargeamount").each(function( index ) {
                    if($(this).val()!=""){
                        extrachargesamount += parseFloat($(this).val());
                    }
                });
                var netamount = parseFloat(grossamount) - parseFloat(discountamount) + parseFloat(extrachargesamount);
                var roundoff =  Math.round(parseFloat(netamount).toFixed(2))-parseFloat(netamount);
                netamount =  Math.round(parseFloat(netamount).toFixed(2));
                $("#roundoff").html(format.format(roundoff));
                $("#inputroundoff").val(parseFloat(roundoff).toFixed(2));
                $("#netamount").html(format.format(netamount));
                $("#inputnetamount").val(parseFloat(netamount).toFixed(2));
                $('#discountrow').show();
            }
        }else{
            $(this).val('');
            $("#overalldiscountamount").val('');
            $("#discountpercentage").html("0"); 
            $("#discountamount").html("0.00");
            changeextrachargesamount();
        }
        // changenetamounttotal();
    });
    $('#overalldiscountamount').on('keyup', function() { 
        var discountamount = $(this).val();
        var grossamount = $("#inputgrossamount").val();
        var gstongrossamount = parseFloat(grossamount);
        var productgstamount = 0;
        $(".producttaxamount").each(function( index ) {
            var divid = $(this).attr("div-id");
            if($(this).val()!="" && $("#qty"+divid).val() >0 ){
                productgstamount += parseFloat($(this).val());
            }
        });
        $('input[name="postofferproducttaxamount[]"]').each(function( index ) {
            if($(this).val()!=""){
                productgstamount += parseFloat($(this).val());
            }
        });
        
        if(GSTonDiscount == 1){
            gstongrossamount = parseFloat(grossamount) - parseFloat(productgstamount);
        }
        $('#discountrow').hide();
        if(discountamount!=undefined && discountamount!='' && parseFloat(discountamount) > 0 && parseFloat(gstongrossamount) > 0 && (parseFloat(discountminamount) == 0 || parseFloat(gstongrossamount) >= parseFloat(discountminamount))){
            
            if(parseFloat(discountamount)>parseFloat(gstongrossamount)){
                $(this).val(parseFloat(gstongrossamount).toFixed(2));
                discountamount = gstongrossamount;
            }
            if(gstongrossamount!=''){
                var discountpercentage = ((parseFloat(discountamount)*100) / parseFloat(gstongrossamount));
                if(discountpercentage==0){
                    $("#overalldiscountpercent").val(0);
                }else{
                    $("#overalldiscountpercent").val(parseFloat(discountpercentage).toFixed(2));  
                }
                
                $("#discountpercentage").html(parseFloat(discountpercentage).toFixed(2));    
                $("#discountamount").html(format.format(discountamount)); 
                if(parseFloat(discountpercentage)>100){
                    $("#overalldiscountpercent").val("100");
                }
                changeextrachargesamount();
                var extrachargesamount = 0;
                $(".extrachargeamount").each(function( index ) {
                    if($(this).val()!=""){
                        extrachargesamount += parseFloat($(this).val());
                    }
                });
                var netamount = parseFloat(grossamount) - parseFloat(discountamount) + parseFloat(extrachargesamount);
                var roundoff =  Math.round(parseFloat(netamount).toFixed(2))-parseFloat(netamount);
                netamount =  Math.round(parseFloat(netamount).toFixed(2));
                $("#roundoff").html(format.format(roundoff));
                $("#inputroundoff").val(parseFloat(roundoff).toFixed(2));
                $("#netamount").html(format.format(netamount));
                $("#inputnetamount").val(parseFloat(netamount).toFixed(2));
                $('#discountrow').show();
            }
        }else{
            $(this).val('')
            $("#overalldiscountpercent").val('');
            $("#discountpercentage").html("0"); 
            $("#discountamount").html("0.00"); 
            changeextrachargesamount(); 
        }

    });
    $(document).on('keyup', '.percentage', function() {
        totalpercentage = 0;
        $(".percentage").each(function(value,index){
          if($(this).val()!=""){
            totalpercentage += parseFloat($(this).val());
          }
        })
        if(totalpercentage > 100){
          $(this).val(0);
          new PNotify({title: "Total installment can not be more than 100.",styling: 'fontawesome',delay: '3000',type: 'error'});
        }
       changeinstallmentamount();
       totalnetamount();
    });
    /****PAYMENT TYPE CHANGE EVENT****/
    $('#paymenttypeid').on('change', function (e) {
        var type = $(this).val();
        if(type==1){
            $('#partialpaymentoption,#installmentmaindiv,#installmentdivs,#installmentsetting_div').hide();
        }else if(type==3){
            $('#partialpaymentoption,#installmentmaindiv,#installmentdivs,#installmentsetting_div').hide();
        }else if(type==4){
            $('#partialpaymentoption,#installmentmaindiv,#installmentdivs').show();
            $("#installmentsetting_div").show();
        }else{
            $('#partialpaymentoption,#installmentmaindiv,#installmentdivs,#installmentsetting_div').hide();
        }
    });
    /****EXTRA CHARGE CHANGE EVENT****/
    $('body').on('change', 'select.extrachargesid', function() { 
        var rowid = $(this).attr("id").match(/\d+/);
        calculateextracharges(rowid);
        // changenetamounttotal();
        if(partialpayment==1 && EMIreceived==0){
            generateinstallment();
        }
    });
    $('body').on('keyup', '.extrachargeamount', function() { 
        var rowid = $(this).attr("id").match(/\d+/);
        var chargestaxamount = 0;
        var tax = $("#extrachargesid"+rowid+" option:selected").attr("data-tax");
        if(tax>0 && this.value!=''){
            chargestaxamount = parseFloat(this.value) * parseFloat(tax) / (100+parseFloat(tax));
        }
        $("#extrachargestax"+rowid).val(parseFloat(chargestaxamount).toFixed(2));

        changenetamounttotal();
        if(partialpayment==1 && EMIreceived==0){
            generateinstallment();
        }
    });
});
function updatematchprice(divid,type=0){
    var qty = $("#qty"+divid).val();
    var pricetype = $("#priceid"+divid+" option:selected").attr('data-pricetype');
    var quantitytype = $("#priceid"+divid+" option:selected").attr('data-quantitytype');

    if(parseInt(pricetype)==1 && parseFloat(qty)>0){
        if(parseInt(quantitytype)==0){ //Range Base

            var minqty = [];
            $("#combopriceid"+divid+" option").each(function(){
                if(this.value!=""){
                    var price_qty = $(this).attr("data-quantity");
                    var price = $(this).attr("data-price");
                   
                    minqty.push(parseFloat(price_qty));
                    if(parseFloat(qty) >= parseFloat(price_qty)){
                        if(this.value!=$("#combopriceid"+divid).val()){
                            $("#combopriceid"+divid).val(this.value).selectpicker("refresh");
                            $("#qty"+divid).trigger("touchspin.updatesettings", {min: 1, step: 1});
                        }
                    }
                }
            });
            var min = Math.min.apply(Math,minqty);
            if(parseFloat(qty) < parseFloat(min)){
                $("#qty"+divid).val(parseFloat(min).toFixed(2));
            }

            var tax = parseFloat($("#producttax"+divid).val());
            var actualprice = parseFloat($("#combopriceid"+divid+" option:selected").attr("data-price").trim());
            
            if(this.value!=""){
                var productrate = parseFloat(actualprice - ((actualprice * tax /(100+parseFloat(tax))))).toFixed(2);
                $('#productrate'+divid).val(productrate);
                $('#actualprice'+divid).val(parseFloat(actualprice).toFixed(2));
            }else{
                $('#actualprice'+divid).val("");
                $('#productrate'+divid).val("");
            }

            $('#originalprice'+divid).val(parseFloat(actualprice).toFixed(2));

            var discount = $("#combopriceid"+divid+" option:selected").attr("data-discount");
            if(parseFloat(discount) > 0){
                $("#discount"+divid).val(parseFloat(discount).toFixed(2));
            }else{
                $("#discount"+divid).val("");
            }
            calculatediscount(divid);
            changeproductamount(divid);
            changeextrachargesamount();
            
            if(partialpayment==1 && EMIreceived==0){
                generateinstallment();
            }
        }else{
            $("#qty"+divid).prop("readonly",true);
            $("#combopriceid"+divid+" option").each(function(){
                if(this.value!=""){
                    var price_qty = $(this).attr("data-quantity");
                    
                    if(parseFloat(qty) == parseFloat(price_qty)){
                        if(this.value!=$("#combopriceid"+divid).val()){
                            $("#combopriceid"+divid).val(this.value).selectpicker("refresh");
                            // $("#qty"+divid).trigger("touchspin.updatesettings", {min: parseInt(price_qty), step: parseInt(price_qty)});
                            
                            var tax = parseFloat($("#producttax"+divid).val());
                            var actualprice = parseFloat($("#combopriceid"+divid+" option:selected").attr("data-price").trim());
                            
                            if(this.value!=""){
                                var productrate = parseFloat(actualprice - ((actualprice * tax /(100+parseFloat(tax))))).toFixed(2);
                                $('#productrate'+divid).val(productrate);
                                $('#actualprice'+divid).val(parseFloat(actualprice).toFixed(2));
                            }else{
                                $('#actualprice'+divid).val("");
                                $('#productrate'+divid).val("");
                            }

                            $('#originalprice'+divid).val(parseFloat(actualprice).toFixed(2));

                            var discount = $("#combopriceid"+divid+" option:selected").attr("data-discount");
                            if(parseFloat(discount) > 0){
                                $("#discount"+divid).val(parseFloat(discount).toFixed(2));
                            }else{
                                $("#discount"+divid).val("");
                            }
                            calculatediscount(divid);
                            changeproductamount(divid);
                            changeextrachargesamount();
                            
                            if(partialpayment==1 && EMIreceived==0){
                                generateinstallment();
                            }
                        }
                    }
                }
            });
        }
    }
}
function calculatediscount(elementid){
    var discountpercentage = $("#discount"+elementid).val(); 
    discountpercentage = (discountpercentage!='' && discountpercentage!=0)?discountpercentage:0;
    var priceid = $("#priceid"+elementid).val();
    var price = $("#actualprice"+elementid).val();
    price = (price!='' && price!=0)?price:0;
    var qty = $("#qty"+elementid).val();
    qty = (qty!='' && qty!=0)?qty:0;
    
    if(price!=0 && qty!=0 && priceid!="" && discountpercentage!=0){
        var discountamount = (parseFloat(price)*parseFloat(discountpercentage)/100) * parseFloat(qty);
        
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
function validattachmentfile(obj,element,elethis){
    var val = obj.val();
    var id = element.match(/\d+/);
    var filename = obj.val().replace(/C:\\fakepath\\/i, '');
    
    if(elethis.files[0].size <= UPLOAD_MAX_FILE_SIZE){

        switch(val.substring(val.lastIndexOf('.') + 1).toLowerCase()){
          case 'gif': case 'bmp': case 'jpg': case 'jpeg': case 'png': case 'pdf': case 'doc': case 'docx':
           
            isvalidattachmentfile = 1;
            $("#Filetext"+id).val(filename);
            $("#"+element+"_div").removeClass("has-error is-focused");
            break;
          default:
            isvalidattachmentfile = 0;
            $("#"+element).val("");
            $("#Filetext"+id).val("");
            $("#"+element+"_div").addClass("has-error is-focused");
            new PNotify({title: 'File type does not valid !',styling: 'fontawesome',delay: '3000',type: 'error'});
            break;
        }
    }else{
        isvalidattachmentfile = 0;
        $("#"+element).val("");
        $("#Filetext"+id).val("");
        $("#"+element+"_div").addClass("has-error is-focused");
        new PNotify({title: 'File is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE)+') !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }
}
function getChannelSettingByVendor(){

    var vendorid = (ACTION==1)?$('#oldvendorid').val():$('#vendorid').val();

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
                $(".tax").prop("readonly",false);
            }else{
                EDITTAXRATE_CHANNEL = 0;
                $(".tax").val('').prop("readonly",true);
            }
          },
          error: function(xhr) {
          //alert(xhr.responseText);
          },
        });
    }else{
        $(".tax").val('').prop("readonly",true);
    }
}
function changeextrachargesamount(){
 
    $(".extrachargeamount").each(function( index ) {
        var rowid = $(this).attr("id").match(/\d+/);
        calculateextracharges(rowid);
    });
}
function calculateextracharges(rowid){
    var extracharges = $("#extrachargesid"+rowid).val();
    var type = $("#extrachargesid"+rowid+" option:selected").attr("data-type");
    var amount = $("#extrachargesid"+rowid+" option:selected").attr("data-amount");
    var tax = $("#extrachargesid"+rowid+" option:selected").attr("data-tax");

    var grossamount = $("#inputgrossamount").val();
    var discount = $("#overalldiscountamount").val()!=""?$("#overalldiscountamount").val():0;
    var totalgrossamount = parseFloat(grossamount) - parseFloat(discount);
    var chargesamount = chargestaxamount = 0;
    if(parseFloat(extracharges) > 0){
        if(type==0){
            if(parseFloat(totalgrossamount)>0){
                chargesamount = parseFloat(totalgrossamount) * parseFloat(amount) / 100;
            } 
        }else{
            chargesamount = parseFloat(amount);
        }
        if(tax>0){
            chargestaxamount = parseFloat(chargesamount) * parseFloat(tax) / (100+parseFloat(tax));
        }
       
        $("#extrachargestax"+rowid).val(parseFloat(chargestaxamount).toFixed(2));
        $("#extrachargeamount"+rowid).val(parseFloat(chargesamount).toFixed(2));
    }else{
        $("#extrachargestax"+rowid).val(parseFloat(0).toFixed(2));
        $("#extrachargeamount"+rowid).val(parseFloat(0).toFixed(2));
    }
    var chargesname = $("#extrachargesid"+rowid+" option:selected").text();
    $("#extrachargesname"+rowid).val(chargesname.trim());
    var chargespercent = 0;
    if(type==0){
        chargespercent = parseFloat(amount);
    }
    $("#extrachargepercentage"+rowid).val(parseFloat(chargespercent).toFixed(2));
    var calcdiscount = ($("#overalldiscountamount").val()!=""?0:1);
    changenetamounttotal(calcdiscount);
}
function getbillingaddress(){
    $('#billingaddressid')
        .find('option')
        .remove()
        .end()
        .append('<option value="">Select Billing Address</option>')
        .val('0')
    ;
    $('#shippingaddressid')
        .find('option')
        .remove()
        .end()
        .append('<option value="">Select Shipping Address</option>')
        .val('0')
    ;
    $('#billingaddressid,#shippingaddressid').selectpicker('refresh');
  
    var vendorid = $("#vendorid").val();
    var BillingAddressID = $("#vendorid option:selected").attr("data-billingid");
    var ShippingAddressID = $("#vendorid option:selected").attr("data-shippingid");
   
    if(vendorid!='' && vendorid!=0){
      var uurl = SITE_URL+"purchase-order/getBillingAddressByVendorId";
        
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {vendorid:String(vendorid)},
        /* dataType: 'json', */
        async: false,
        success: function(response){
  
            var obj = JSON.parse(response);
            if (!jQuery.isEmptyObject(obj['billingaddress'])) {
                for(var i = 0; i < obj['billingaddress'].length; i++) {
  
                    $('#billingaddressid').append($('<option>', { 
                        value: obj['billingaddress'][i]['id'],
                        text : ucwords(obj['billingaddress'][i]['address'])
                    }));
                    $('#shippingaddressid').append($('<option>', { 
                        value: obj['billingaddress'][i]['id'],
                        text : ucwords(obj['billingaddress'][i]['address'])
                    }));
                }
                if(addressid!=0 && (ACTION==1 || ISDUPLICATE==1)){
                    $('#billingaddressid').val(addressid);
                }else if(BillingAddressID!=0){
                    $('#billingaddressid').val(BillingAddressID);
                }
                if(shippingaddressid!=0 && (ACTION==1 || ISDUPLICATE==1)){
                    $('#shippingaddressid').val(shippingaddressid);
                }else if(ShippingAddressID!=0){
                    $('#shippingaddressid').val(ShippingAddressID);
                }
            }
            if (!jQuery.isEmptyObject(obj['globaldiscount'])) {
                GSTonDiscount = obj['globaldiscount']['gstondiscount'];
                if(ACTION==0 || (ACTION==1 && globaldicountper=="" && globaldicountamount=="")){
                    globaldicountper = (obj['globaldiscount']['discounttype']==1)?parseFloat(obj['globaldiscount']['discount']).toFixed(2):"";
                    globaldicountamount = (obj['globaldiscount']['discounttype']==0)?parseFloat(obj['globaldiscount']['discount']).toFixed(2):"";
                }
                discountminamount = parseFloat(obj['globaldiscount']['minimumbillamount']).toFixed(2);
            }
            changenetamounttotal();
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
      });
    }
    $('#billingaddressid,#shippingaddressid').selectpicker('refresh');
}
function getproduct(divid=''){

    if(divid==''){
        UIPRODUCT = [];
        UIPRICE = [];
        $('select.productid').each(function() {
            var divid = $(this).attr("div-id");
            UIPRODUCT.push($('#productid'+divid).val());
            UIPRICE.push($('#priceid'+divid).val());
        });
        
        $('select.productid')
            .find('option')
            .remove()
            .end()
            .append('<option value="">Select Product</option>')
            .val('0')
        ;
        $('select.productid').selectpicker('refresh');
        var element = $('select.productid');
    }else{
        $('#productid'+divid)
            .find('option')
            .remove()
            .end()
            .append('<option value="">Select Product</option>')
            .val('0')
        ;
        $('#priceid'+divid)
            .find('option')
            .remove()
            .end()
            .append('<option value="">Select Variant</option>')
            .val('')
        ;
        $('#productid'+divid).selectpicker('refresh');
        $('#priceid'+divid).selectpicker('refresh');
        var element = $('#productid'+divid);
    }

    var vendorid = $("#vendorid").val();
    
    if(vendorid!='' && vendorid!=0){
      var uurl = SITE_URL+"product/getVendorProducts";
      salesproducthtml = "";
      
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {vendorid:vendorid},
        dataType: 'json',
        async: false,
        success: function(response){
  
            var NewProduct = [];
            for(var i = 0; i < response.length; i++) {
                
                var productname = response[i]['name'].replace("'","&apos;");
                if(DROPDOWN_PRODUCT_LIST==0){
                    
                    element.append($('<option>', { 
                        value: response[i]['id'],
                        text : productname
                    }));
      
                    salesproducthtml += "<option value='"+response[i]['id']+"'>"+productname+"</option>";
                }else{
                    
                    element.append($('<option>', { 
                        value: response[i]['id'],
                        text : productname,
                        "data-content" :'<img src="'+PRODUCT_PATH+response[i]['image']+'" style="width:40px">  ' + productname
                    }));

                    salesproducthtml += '<option data-content="<img src=&apos;'+PRODUCT_PATH+response[i]['image']+'&apos; style=&apos;width:40px&apos;>  '+productname+'" value='+response[i]['id']+'>'+productname+'</option>';
                }

                NewProduct.push(response[i]['id']);
            }
            
            /***** Change member then product not reset for user interface *****/
            var PRODUCT_ARR = [];
            if(NewProduct.length > 0 && ACTION==0 && divid==''){

                $('select.productid').each(function(index) {
                    var divid = $(this).attr("div-id");
                   
                    if(NewProduct.includes(UIPRODUCT[index])){
                        // If product id is match then execute
                        $('#productid'+divid).val(UIPRODUCT[index]);
                        $('#productid'+divid).selectpicker('refresh');
                        
                        if(!PRODUCT_ARR.includes(UIPRODUCT[index])){
                            PRODUCT_ARR.push(UIPRODUCT[index]);
                        }
                    }else{
                        // If product id is not match then reset all product data
                        $('#priceid'+divid)
                            .find('option')
                            .remove()
                            .end()
                            .append('<option value="">Select Variant</option>')
                            .val('')
                        ;
                        $('#priceid'+divid).selectpicker('refresh');
                        $("#actualprice"+divid+",#discount"+divid+",#discountinrs"+divid+",#amount"+divid+",#tax"+divid+",#ordertax"+divid+",#uniqueproduct"+divid).val('');
                        // $('#applyoldprice'+divid+'_div').hide();
                    }
                    changeproductamount(divid);
                    changeextrachargesamount();
                    $("#discount"+divid+",#discountinrs"+divid).val('');
                    if(partialpayment==1 && EMIreceived==0){
                        generateinstallment();
                    }
                });
            }
            if(PRODUCT_ARR.length > 0){
                getpricebyproductid(PRODUCT_ARR); 
            }
            if(oldproductid[divid-1]!=0){
                $('#productid'+divid).val(oldproductid[divid-1]);
            }
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
            .val('')
        ;
        $('select.priceid').selectpicker('refresh');
        $(".qty").val('1');
        $(".discount,.discountinrs,.amounttprice,.actualprice").val('');
    }
    if(divid==''){
        $('select.productid').selectpicker('refresh');
    }else{
        $('#productid'+divid).selectpicker('refresh');
        $('#priceid'+divid).selectpicker('refresh');
    }
}
function getpricebyproductid(productids){
   
    var quotationtype = $("#quotationtype").val();
    if(productids.length > 0){
        for(var i = 0; i < productids.length; i++) {

            var productid = productids[i];
            if(productid!=''){
                var uurl = SITE_URL+"purchase-/getVariantByProductId";
                var vendorid = $("#vendorid").val();
                
                $.ajax({
                    url: uurl,
                    type: 'POST',
                    data: {productid:String(productid),vendorid:vendorid},
                    dataType: 'json',
                    async: false,
                    success: function(response){
                
                        $('select.productid').each(function(index) {
                            var divid = $(this).attr("div-id");
                            var pid = $('#productid'+divid).val();
                            var priceid = $('#priceid'+divid).val();
                            var combopriceid = $('#priceid'+divid).val();

                            if(pid == productid){

                                $('#priceid'+divid)
                                    .find('option')
                                    .remove()
                                    .end()
                                    .append('<option value="">Select Variant</option>')
                                    .val('')
                                ;
                                $('#priceid'+divid).selectpicker('refresh');
                                $('#combopriceid'+divid)
                                    .find('option')
                                    .remove()
                                    .end()
                                    .append('<option value="">Price</option>')
                                    .val('')
                                ;
                                $('#combopriceid'+divid).selectpicker('refresh');

                                for(var i = 0; i < response.length; i++) {
                                    $('#priceid'+divid).append($('<option>', { 
                                        value: response[i]['id'],
                                        text : response[i]['variantname'],
                                        "data-pricetype" : response[i]['pricetype'],
                                        "data-quantitytype" : response[i]['quantitytype'],
                                        "data-referencetype" : response[i]['referencetype'],
                                    }));
                                    $('#producttax'+divid).val(response[i]['tax']);
                                }  
                                $('#priceid'+divid).val(priceid);
                                $('#priceid'+divid).selectpicker('refresh');
                                getmultiplepricebypriceid(divid);
                                $('#combopriceid'+divid).val(combopriceid).selectpicker('refresh');
                                
                                var actualprice = parseFloat($("#combopriceid"+divid+" option:selected").attr("data-price"));
                                var discount = parseFloat($("#combopriceid"+divid+" option:selected").attr("data-discount"));
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
                                changeextrachargesamount();
                                $("#discount"+divid+",#discountinrs"+divid).val('');
                                if(partialpayment==1 && EMIreceived==0){
                                    generateinstallment();
                                }
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
function getproductprice(divid){
   
    $('#priceid'+divid)
        .find('option')
        .remove()
        .end()
        .append('<option value="">Select Variant</option>')
        .val('')
    ;
    $('#priceid'+divid).selectpicker('refresh');
    $('#combopriceid'+divid)
        .find('option')
        .remove()
        .end()
        .append('<option value="">Price</option>')
        .val('')
    ;
    $('#combopriceid'+divid).selectpicker('refresh');
    var productid = $("#productid"+divid).val();
   
    if(productid!=''){
        var uurl = SITE_URL+"purchase-order/getVariantByProductId";
        var vendorid = (ACTION==1)?$('#oldvendorid').val():$('#vendorid').val();
      
        $.ajax({
        url: uurl,
        type: 'POST',
        data: {productid:String(productid),vendorid:vendorid},
        dataType: 'json',
        async: false,
        success: function(response){
  
            var len = response.length;
            for(var i = 0; i < response.length; i++) {
                $('#priceid'+divid).append($('<option>', { 
                    value: response[i]['id'],
                    text : response[i]['variantname'],
                    "data-id" : response[i]['priceid'],
                    "data-pricetype" : response[i]['pricetype'],
                    "data-quantitytype" : response[i]['quantitytype'],
                    "data-referencetype" : response[i]['referencetype'],
                }));
                /* if(ACTION==1){
                    if(typeof(response[i]['universal'])!='undefined'){
                        $('#priceid'+divid).append($('<option>', { 
                          value: response[i]['id'],
                          text : response[i]['variantname'],
                          "data-id" : response[i]['priceid'],
                          "data-pricetype" : response[i]['pricetype'],
                          "data-quantitytype" : response[i]['quantitytype'],
                          "data-referencetype" : response[i]['referencetype'],
                        }));
                        $('#priceid'+divid).val(response[i]['id']);
                        $('#priceid'+divid).selectpicker("refresh");
                    }else{
                        $('#priceid'+divid).append($('<option>', { 
                            value: response[i]['id'],
                            text : response[i]['variantname'],
                            "data-id" : response[i]['priceid'],
                            "data-pricetype" : response[i]['pricetype'],
                            "data-quantitytype" : response[i]['quantitytype'],
                            "data-referencetype" : response[i]['referencetype'],
                        }));
                    }  
                }else{
                    $('#priceid'+divid).append($('<option>', { 
                        value: response[i]['id'],
                        text : response[i]['variantname'],
                        "data-id" : response[i]['priceid'],
                        "data-pricetype" : response[i]['pricetype'],
                        "data-quantitytype" : response[i]['quantitytype'],
                        "data-referencetype" : response[i]['referencetype'],
                    }));
                    if(response[i]['universal']!='undefined' && ISDUPLICATE==1){
                        $('#priceid'+divid).val(response[i]['id']);
                    }
                } */  
                $('#producttax'+divid).val(response[i]['tax']);

            }
            if(len==1){
                $('#priceid'+divid).val(response[0]['id']).selectpicker('refresh');
                $('#priceid'+divid).change();
            }
            if((ISDUPLICATE==1 || ACTION==1) && oldpriceid[divid-1]!="undefined" && $('#productid'+divid).val()==oldproductid[divid-1]){
                $('#priceid'+divid).val(oldpriceid[divid-1]);
            }
            if(ISDUPLICATE==1 || ACTION==1){
                var actualprice = parseFloat($('#actualprice'+divid).val()).toFixed(2);
                
                if(parseFloat(actualprice) == parseFloat($('#oldpricewithtax'+divid).html())){
                    $('#applyoldprice'+divid+'_div').hide();
                    $('#applyoldprice'+divid).prop("checked",false);
                }else{
                    $('#applyoldprice'+divid+'_div').show();
                }
            }
            if((ISDUPLICATE==1 || ACTION==1) && oldtax[divid-1]>=0 && $('#priceid'+divid).val()==oldpriceid[divid-1] && $('#productid'+divid).val()==oldproductid[divid-1] && $('#tax'+divid).val()!=""){
                $('#tax'+divid+',#ordertax'+divid).val(oldtax[divid-1]);
            }else{
                var tax = (response.length > 0)?response[0]['tax']:0;
                $('#tax'+divid+',#ordertax'+divid).val(tax);
            }
           
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
      });
    }
    $('#priceid'+divid).selectpicker('refresh');
}
function getmultiplepricebypriceid(divid){

    $('#combopriceid'+divid)
        .find('option')
        .remove()
        .end()
        .append('<option value="">Price</option>')
        .val('')
    ;
    $('#combopriceid'+divid).selectpicker('refresh');

    var priceid = $("#priceid"+divid).val();
    var productid = $("#productid"+divid).val();
    
    if(priceid!=""){
        var uurl = SITE_URL+"purchase-order/getMultiplePriceByPriceIdOrVendorId";
        var vendorid = (ACTION==1)?$('#oldvendorid').val():$('#vendorid').val();
        var productpriceid = $("#priceid"+divid+" option:selected").attr("data-id");
        var pricetype = $("#priceid"+divid+" option:selected").attr("data-pricetype");
        var quantitytype = $("#priceid"+divid+" option:selected").attr("data-quantitytype");
        
        if(parseInt(pricetype)==1 && parseInt(quantitytype)==1){
            $("#qty"+divid).prop("readonly",true);
        }else{
            $("#qty"+divid).prop("readonly",false);
        }

        $.ajax({
            url: uurl,
            type: 'POST',
            data: {productid:String(productid),priceid:String(productpriceid),vendorid:vendorid},
            dataType: 'json',
            async: false,
            beforeSend: function(){
                $('.mask').show();
                $('#loader').show();
            },
            success: function(response){

                var length = response.length;
                for(var i = 0; i < response.length; i++) {
                    
                    var txt = "";

                    if(parseInt(pricetype)==1){
                        txt = CURRENCY_CODE+response[i]['price']+" "+response[i]['quantity']+(parseInt(quantitytype)==0?"+":"")+" Qty"
                    }else{
                        txt = response[i]['price'];
                    }
                    $('#combopriceid'+divid).append($('<option>', { 
                        value: response[i]['id'],
                        text : txt,
                        "data-price" : response[i]['price'],
                        "data-quantity" : response[i]['quantity'],
                        "data-discount" : response[i]['discount']
                    }));

                }
                if(length==1){
                    $('#combopriceid'+divid).val(response[0]['id']).selectpicker('refresh');
                    $('#combopriceid'+divid).change();
                }
                if(ACTION==1 && oldcombopriceid[divid-1]!="undefined" && $('#combopriceid'+divid).val()==""){
                    $('#combopriceid'+divid).val(oldcombopriceid[divid-1]).selectpicker('refresh').change();

                    if(productid==oldproductid[divid-1] && priceid==oldpriceid[divid-1]){
                       var quantity = $("#combopriceid"+divid+" option:selected").attr("data-quantity");
                        if(parseInt(quantitytype)==1 && parseInt(pricetype)==1){
                           $("#qty"+divid).trigger("touchspin.updatesettings", {min: parseFloat(quantity), step: parseFloat(quantity)});
                        }else{
                           $("#qty"+divid).trigger("touchspin.updatesettings", {min: 1, step: 1});
                       }
                   }
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
    $('#combopriceid'+divid).selectpicker('refresh');
}
function addnewproduct(){

    productoptionhtml = salesproducthtml;
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
    producthtml = '<tr class="countproducts" id="quotationproductdiv'+divcount+'">\
        <td>\
            <input type="hidden" name="producttax[]" id="producttax'+divcount+'">\
            <input type="hidden" name="productrate[]" id="productrate'+divcount+'">\
            <input type="hidden" name="originalprice[]" id="originalprice'+divcount+'">\
            <input type="hidden" name="uniqueproduct[]" id="uniqueproduct'+divcount+'">\
            <input type="hidden" name="referencetype[]" id="referencetype'+divcount+'">\
            <div class="form-group" id="product'+divcount+'_div">\
                <div class="col-sm-12">\
                    <select id="productid'+divcount+'" name="productid[]" data-width="90%" class="selectpicker form-control productid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="'+divcount+'">\
                        <option value="0">Select Product</option>\
                        '+productoptionhtml+'\
                    </select>\
                </div>\
            </div>\
        </td>\
        <td>\
            <div class="form-group" id="price'+divcount+'_div">\
                <div class="col-md-12">\
                    <select id="priceid'+divcount+'" name="priceid[]" data-width="90%" class="selectpicker form-control priceid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="'+divcount+'">\
                        <option value="">Select Variant</option>\
                    </select>\
                </div>\
            </div>\
        </td>\
        <td>\
            <div class="form-group" id="comboprice'+divcount+'_div">\
                <div class="col-sm-12">\
                    <select id="combopriceid'+divcount+'" name="combopriceid[]" data-width="150px" class="selectpicker form-control combopriceid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="'+divcount+'">\
                        <option value="">Price</option>\
                    </select>\
                </div>\
            </div>\
            <div class="form-group" id="actualprice'+divcount+'_div">\
                <div class="col-sm-12">\
                    <label for="actualprice'+divcount+'" class="control-label">Rate ('+CURRENCY_CODE+')</label>\
                    <input type="text" class="form-control actualprice text-right" id="actualprice'+divcount+'" name="actualprice[]" value="" onkeypress="return decimal_number_validation(event, this.value)" style="display: block;" div-id="'+divcount+'">\
                </div>\
            </div>\
        </td>\
        <td>\
            <div class="form-group" id="qty'+divcount+'_div">\
                <div class="col-md-12">\
                    <input type="text" class="form-control qty" id="qty'+divcount+'" name="qty[]" value="" onkeypress="'+(MANAGE_DECIMAL_QTY==1?"return decimal_number_validation(event, this.value,8);":"return isNumber(event);")+'" style="display: block;" div-id="'+divcount+'">\
                </div>\
            </div>\
        </td>\
        <td style="'+discount+'">\
            <div class="form-group" id="discount'+divcount+'_div">\
                <div class="col-sm-12">\
                    <label for="discount'+divcount+'" class="control-label">Dis. (%)</label>\
                    <input type="text" class="form-control discount" id="discount'+divcount+'" onkeypress="return decimal_number_validation(event, this.value)" name="discount[]" value="" div-id="'+divcount+'">\
                    <input type="hidden" value="" id="orderdiscount'+divcount+'">\
                </div>\
            </div>\
            <div class="form-group" id="discountinrs'+divcount+'_div">\
                <div class="col-sm-12">\
                    <label for="discountinrs'+divcount+'" class="control-label">Dis. ('+CURRENCY_CODE+')</label>\
                    <input type="text" class="form-control discountinrs" id="discountinrs'+divcount+'" name="discountinrs[]" value="" div-id="'+divcount+'" onkeypress="return decimal_number_validation(event, this.value)">\
                </div>\
            </div>\
        </td>\
        <td>\
            <div class="form-group" id="tax'+divcount+'_div">\
                <div class="col-sm-12">\
                    <input type="text" class="form-control text-right tax" id="tax'+divcount+'" name="tax[]" value="" div-id="'+divcount+'" '+readonly+'>	\
                    <input type="hidden" value="" id="ordertax'+divcount+'">\
                </div>\
            </div>\
        </td>\
        <td>\
            <div class="form-group" id="amount'+divcount+'_div">\
                <div class="col-sm-12">\
                    <input type="text" class="form-control amounttprice" id="amount'+divcount+'" name="amount[]" value="" readonly="" div-id="'+divcount+'">\
                    <input type="hidden" class="producttaxamount" id="producttaxamount'+divcount+'" name="producttaxamount[]" value="" div-id="'+divcount+'">\
                </div>\
            </div>\
        </td>\
        <td>\
            <div class="form-group pt-sm">\
                <div class="col-sm-12 pr-n">\
                    <button type = "button" class = "btn btn-default btn-raised  add_remove_btn_product" onclick = "removeproduct('+divcount+')" style="padding: 5px 10px;"> <i class = "fa fa-minus"></i></button> \
                    <button type="button" class="btn btn-default btn-raised  add_remove_btn" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button> \
                </div>\
            </div>\
        </td>\
    </div>';

    $(".add_remove_btn_product:first").show();
    $(".add_remove_btn:last").hide();
    $("#quotationproducttable tbody").append(producthtml);

    $("#qty"+divcount).TouchSpin(touchspinoptions);

    $(".selectpicker").selectpicker("refresh");
}
function removeproduct(divid){

    if($('select[name="productid[]"]').length!=1 && ACTION==1 && $('#quotationproductsid'+divid).val()!=null){
        var removequotationproductid = $('#removequotationproductid').val();
        $('#removequotationproductid').val(removequotationproductid+','+$('#quotationproductsid'+divid).val());
    }
    $("#quotationproductdiv"+divid).remove();

    $(".add_remove_btn:last").show();
    if ($(".add_remove_btn_product:visible").length == 1) {
        $(".add_remove_btn_product:first").hide();
    }
    changeproductamount(divid);
    changeextrachargesamount();
}
function changeproductamount(divid){
    if(divid!=undefined){
        var price = $("#priceid"+divid+" option:selected").text().trim();
        var combopriceid = $("#combopriceid"+divid).val();
        var actualprice = $("#actualprice"+divid).val();
        var qty = ($("#qty"+divid).val()!="")?parseFloat($("#qty"+divid).val()):0;
        var discount = $("#discount"+divid).val();
        var tax = parseFloat($("#producttax"+divid).val()).toFixed(2);
        var ordertax = $("#ordertax"+divid).val();
        var orderprice = $("#oldpricewithtax"+divid).html();
        var edittax = $("#tax"+divid).val();
        edittax = (edittax!="")?parseFloat(edittax).toFixed(2):0;
        actualprice = (actualprice!="")?parseFloat(actualprice).toFixed(2):0;

        /* if(GST_PRICE == 1){
            var productrate = parseFloat(actualprice).toFixed(2);
        }else{
            var productrate = parseFloat(parseFloat(actualprice) - ((parseFloat(actualprice) * parseFloat(tax) /(100+parseFloat(tax))))).toFixed(2);
        } */  
        
        if(combopriceid!=0 && actualprice!=0 && qty!="0" && price!="" && qty!="" && price!="Select Variant"){
            
            totalamount = productamount = discountamount = 0;
            if(PRODUCT_DISCOUNT == 1 && discount!='0' && discount!=""){
                discountamount = (parseFloat(actualprice)*(parseFloat(discount)/100));
            }
            price = parseFloat(parseFloat(actualprice) - parseFloat(discountamount)).toFixed(2);
            
            var productrate = parseFloat(price);
            if((ISDUPLICATE==1 || ACTION==1) && $("input[id=applyoldprice"+divid+"]").is(":checked") && $('#applyoldprice'+divid+'_div').is(':visible')){
                price = parseFloat(parseFloat(orderprice) - parseFloat(discountamount)).toFixed(2);
                edittax = parseFloat(ordertax);
            }
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
            
            var grossamount = productgstamount = 0;
            $(".amounttprice").each(function( index ) {
                if($(this).val()!=""){
                    grossamount += parseFloat($(this).val());
                }
            });
            $(".producttaxamount").each(function( index ) {
                var divid = $(this).attr("div-id");
                if($(this).val()!="" && $("#qty"+divid).val() >0 ){
                    productgstamount += parseFloat($(this).val());
                }
            });
            var gstongrossamount = parseFloat(grossamount);
            if(GSTonDiscount == 1){
                gstongrossamount = parseFloat(grossamount) - parseFloat(productgstamount);
            }
            if(parseFloat(discountminamount) == 0 || parseFloat(gstongrossamount) >= parseFloat(discountminamount) && $("#overalldiscountpercent").val()!=''){
                var discountamount = (parseFloat(gstongrossamount)*parseFloat($("#overalldiscountpercent").val())/100);
                $("#overalldiscountamount").val(parseFloat(discountamount).toFixed(2));

                $("#discountpercentage").html(parseFloat($("#overalldiscountpercent").val()).toFixed(2)); 
                $("#discountamount").html(format.format(discountamount)); 
            }else{
                $("#discountpercentage,#discountamount").html(''); 
                $('#discountrow').hide();
            }
            if(totalamount!=0 && totalamount!=''){
                changenetamounttotal();
                if(partialpayment==1 && EMIreceived==0){
                    generateinstallment();
                }
            }   
        }else{
            $("#overalldiscountpercent").val('');
            $("#overalldiscountamount").val('');
            $("#amount"+divid).val(0);
            $("#discountpercentage").html('0'); 
            $("#discountamount").html('0.00'); 
            changenetamounttotal();
        }
    }
}
function changenetamounttotal(calcdiscount=0){
    var productgstamount = chargesassesbaleamount = extrachargesamount = extrachargestax = grossamount = 0;
    $(".producttaxamount").each(function( index ) {
        if($(this).val()!=""){
            productgstamount += parseFloat($(this).val());
        }
    });
    $(".amounttprice").each(function( index ) {
        if($(this).val()!=""){
            grossamount += parseFloat($(this).val());
        }
    });
    
    grossamount = grossamount - productgstamount;
    $(".extrachargestax").each(function( index ) {
        if($(this).val()!=""){
            extrachargestax += parseFloat($(this).val());
        }
    });
    $(".extrachargeamount").each(function( index ) {
        if($(this).val()!=""){
            extrachargesamount += parseFloat($(this).val());
        }
    });
    chargesassesbaleamount = parseFloat(extrachargesamount) - parseFloat(extrachargestax);
    var producttotalassesbaleamount = parseFloat(grossamount) + parseFloat(chargesassesbaleamount);
    var producttotalgstamount = parseFloat(productgstamount) + parseFloat(extrachargestax);
    $("#productgstamount").html(format.format(parseFloat(productgstamount).toFixed(2)));
    $("#productassesbaleamount").html(format.format(parseFloat(grossamount).toFixed(2)));
    $("#chargestotalassesbaleamount").html(format.format(parseFloat(chargesassesbaleamount).toFixed(2)));
    $("#chargestotalgstamount").html(format.format(parseFloat(extrachargestax).toFixed(2)));
    $("#producttotalassesbaleamount").html(format.format(parseFloat(producttotalassesbaleamount).toFixed(2)));
    $("#producttotalgstamount").html(format.format(parseFloat(producttotalgstamount).toFixed(2)));
    
    $("#totalgrossamount").val(parseFloat(producttotalassesbaleamount).toFixed(2));
    $("#inputtotaltaxamount").val(parseFloat(producttotalgstamount).toFixed(2));

    grossamount = parseFloat(grossamount) + parseFloat(productgstamount);
    $("#grossamount").html(format.format(parseFloat(grossamount).toFixed(2)));
    $("#inputgrossamount").val(parseFloat(grossamount).toFixed(2));
    $('#discountrow').hide();
    if(grossamount!=0){
      
        var totaldiscountamount = 0;
        var gstongrossamount = parseFloat(grossamount);
        if(GSTonDiscount == 1){
            gstongrossamount = parseFloat(grossamount) - parseFloat(productgstamount);
        }
        if(calcdiscount==0 && (parseFloat(discountminamount) == 0 || parseFloat(gstongrossamount) >= parseFloat(discountminamount))){
            var overalldiscountpercent = $("#overalldiscountpercent").val();
            var overalldiscountamount = $("#overalldiscountamount").val();

            if(overalldiscountpercent!=''){
                if(parseFloat(overalldiscountpercent)>100){
                    $('#overalldiscountpercent').val("100");
                }
                if(parseFloat(overalldiscountamount)>parseFloat(gstongrossamount)){
                    $('#overalldiscountamount').val(parseFloat(gstongrossamount).toFixed(2));
                    overalldiscountamount = parseFloat(gstongrossamount);
                }
                $("#discountpercentage").html(parseFloat(overalldiscountpercent).toFixed(2)); 
                $("#discountamount").html(format.format(overalldiscountamount)); 

                totaldiscountamount = parseFloat(overalldiscountamount);
            }else{
                if(globaldicountper!=""){
                    var discountamount = (parseFloat(gstongrossamount)*parseFloat(globaldicountper)/100);
                    $("#overalldiscountpercent").val(globaldicountper);
                    $("#overalldiscountamount").val(parseFloat(discountamount).toFixed(2));
                    
                    $("#discountpercentage").html(parseFloat(globaldicountper).toFixed(2)); 
                    $("#discountamount").html(format.format(discountamount)); 
                }else if(globaldicountamount!=""){
                    var discountpercentage = ((parseFloat(globaldicountamount)*100) / parseFloat(gstongrossamount));
                    $("#overalldiscountpercent").val(discountpercentage);
                    $("#overalldiscountamount").val(parseFloat(globaldicountamount).toFixed(2));
                    
                    $("#discountpercentage").html(parseFloat(discountpercentage).toFixed(2)); 
                    $("#discountamount").html(format.format(globaldicountamount)); 
                }else{
                    $("#overalldiscountpercent,#overalldiscountamount").val('');
                    $("#discountpercentage,#discountamount").html('');
                }
                totaldiscountamount = ($("#overalldiscountamount").val()!=""?parseFloat($("#overalldiscountamount").val()):0);
            }
        }else{
            $("#overalldiscountpercent").val('');
            $("#overalldiscountamount").val('');
            $("#discountpercentage").html(''); 
            $("#discountamount").html(''); 
        }
        if(calcdiscount==1){
            $("#overalldiscountpercent").val('');
            $("#overalldiscountamount").val('');
            $("#discountpercentage").html(''); 
            $("#discountamount").html(''); 
            totaldiscountamount = 0;
        }
        finalamount = parseFloat(grossamount) - parseFloat(totaldiscountamount) + parseFloat(extrachargesamount);
     
        if(finalamount<0){
            finalamount=0;
        }
        var roundoff =  Math.round(parseFloat(finalamount).toFixed(2))-parseFloat(finalamount);
        finalamount =  Math.round(parseFloat(finalamount).toFixed(2));
        
        $("#roundoff").html(format.format(roundoff));
        $("#inputroundoff").val(parseFloat(roundoff).toFixed(2));
        $("#netamount").html(format.format(finalamount));
        $("#inputnetamount").val(parseFloat(finalamount).toFixed(2));
        
        if($("#overalldiscountpercent").val()!='' || $("#overalldiscountamount").val()!=''){
            $('#discountrow').show();
        }
    }else{
        $("#roundoff").html("0.00");
        $("#inputroundoff").val(parseFloat("0").toFixed(2));
        $("#netamount").html('0.00');
        $("#inputnetamount").val('');
    }
}
function onlypercentage(val){
    fieldval = $("#"+val).val();
    if (parseInt(fieldval) < 0) $("#"+val).val(0);
    if (parseInt(fieldval) > 100) $("#"+val).val(100);
    changenetamounttotal();
}
function generateinstallment(){

    $("#installmentdivs").html("");
      noofinstallmentval = $("#noofinstallment").val();
      noofinstallmentdiv = $(".noofinstallmentdiv").length;
      emidate = $("#emidate").val();
      emiduration = $("#emiduration").val();
      
      if(noofinstallmentval=="" || noofinstallmentval == "0" || emidate=="" || emiduration=="" || emiduration == "0"){
        if($('#paymenttypeid').val() == 4){
            if(noofinstallmentval == "" || noofinstallmentval == "0"){
                $("#noofinstallment_div").addClass("has-error is-focused");
                new PNotify({title: 'Please enter no. of installment !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else{
                $("#noofinstallment_div").removeClass("has-error is-focused");
            }
            if(emidate == ""){
                $("#emidate_div").addClass("has-error is-focused");
                new PNotify({title: 'Please enter EMI start date !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else{
                $("#emidate_div").removeClass("has-error is-focused");
            }
            if(emiduration == "" || emiduration == "0"){
                $("#emiduration_div").addClass("has-error is-focused");
                new PNotify({title: 'Please enter EMI duration !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else{
                $("#emiduration_div").removeClass("has-error is-focused");
            }
        }
        return false;
      }

      totalvalue=0;
      /* $('.amounttprice').each(function (index, value) {
        if($(this).val()!=""){
          totalvalue = totalvalue+parseFloat($(this).val());
        }
      }) */
      totalvalue = parseFloat($("#inputnetamount").val());
      installmentamount = (parseFloat(totalvalue)/parseFloat(noofinstallmentval)).toFixed(2);
      installmentpercentage = (100/parseFloat(noofinstallmentval)).toFixed(2);

      $("#installmentmaindivheading").show();
    
      var datearray = emidate.split("/");
      var emidate = new Date(datearray[2]+"-"+datearray[1]+"-"+datearray[0]);
      emidurationval=0;
      percentagetotal=0;
      amounttotal=0;
      $('#installmentmaindiv').find(".noofinstallmentdiv").slice( noofinstallmentval,noofinstallmentdiv).remove();
      for (var i = 0; i <= noofinstallmentval-1; i++) {

        if(emidurationval==0){
          emidate.setDate(emidate.getDate());
        }else{
          emidate.setDate(emidate.getDate()+parseInt(emiduration));
        }
        if(i == noofinstallmentval-1){
          installmentpercentage = (100-parseFloat(percentagetotal)).toFixed(2);
          installmentamount = (parseFloat(totalvalue)-parseFloat(amounttotal)).toFixed(2);
        }
        percentagetotal = parseFloat(percentagetotal)+parseFloat(installmentpercentage);
        amounttotal = parseFloat(amounttotal)+parseFloat(installmentamount);
        emidurationval=1;
        var dd = (emidate.getDate() < 10)?"0"+emidate.getDate():emidate.getDate();
        var mm = (emidate.getMonth() < 10)?("0"+(emidate.getMonth()+ 1)):emidate.getMonth()+ 1;
        var yy = emidate.getFullYear();
        installmentdate = dd+"/"+mm+"/"+yy;

        $("#installmentdivs").append('<div class="row noofinstallmentdiv">\
            <div class="col-md-1 text-center"><div class="form-group"><div class="col-sm-12">'+(i+1)+' </div></div></div>\
            <div class="col-md-2 text-center">\
                <div class="form-group">\
                    <div class="col-sm-12">\
                        <input type="text" id="percentage'+(i+1)+'" value="'+installmentpercentage+'" name="percentage[]" class="form-control text-right percentage"  div-id="'+(i+1)+'" maxlength="5" onkeyup="return onlypercentage(this.id)" onkeypress="return decimal(event,this.id)">\
                    </div>\
                </div>\
            </div>\
            <div class="col-md-2 text-center">\
                <div class="form-group">\
                    <div class="col-sm-12">\
                        <input type="text" id="installmentamount'+(i+1)+'" value="'+installmentamount+'" name="installmentamount[]" class="form-control text-right installmentamount" div-id="'+(i+1)+'" maxlength="5" onkeypress="return decimal(event,this.id);" readonly>\
                    </div>\
                </div>\
            </div>\
            <div class="col-md-2 text-center">\
                <div class="form-group">\
                    <div class="col-sm-12">\
                        <input type="text" id="installmentdate'+(i+1)+'" value="'+installmentdate+'" name="installmentdate[]" class="form-control" div-id="'+(i+1)+'" maxlength="5">\
                    </div>\
                </div>\
            </div>\
            <div class="col-md-2 text-center">\
                <div class="form-group">\
                    <div class="col-sm-12">\
                        <input type="text" id="paymentdate'+(i+1)+'" value="" name="paymentdate[]" class="form-control" div-id="'+(i+1)+'" maxlength="5">\
                    </div>\
                </div>\
            </div>\
            <div class="col-md-2 text-center">\
                <div class="form-group">\
                    <div class="col-sm-12">\
                        <div class="checkbox">\
                            <input id="installmentstatus'+(i+1)+'" type="checkbox" value="1" name="installmentstatus'+(i+1)+'" div-id="'+(i+1)+'" class="checkradios">\
                            <label for="installmentstatus'+(i+1)+'"></label>\
                        </div>\
                    </div>\
                </div>\
            </div>\
          </div>');

        $('#installmentdate'+(i+1)).datepicker({
            todayHighlight: true,
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayBtn:"linked",
        });
        $('#paymentdate'+(i+1)).datepicker({
            todayHighlight: true,
            format: 'dd/mm/yyyy',
            autoclose: true,
            endDate:new Date(),
            todayBtn:"linked",
            clearBtn: 'Clear',
        });

      }

}
function addnewcharge(){

    var rowcount = parseInt($(".countcharges:last").attr("id").match(/\d+/))+1;
    var datahtml = ' <tr class="countcharges" id="countcharges'+rowcount+'">\
                     <th>\
                        <div class="col-md-9 p-n">\
                            <div class="form-group p-n" id="extracharges'+rowcount+'_div">\
                                <div class="col-sm-12">\
                                    <select id="extrachargesid'+rowcount+'" name="extrachargesid[]" class="selectpicker form-control extrachargesid" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                        <option value="0">Select Extra Charges</option>\
                                            '+extrachargeoptionhtml+'\
                                    </select>\
                                    <input type="hidden" name="extrachargestax[]" id="extrachargestax'+rowcount+'" class="extrachargestax" value="">\
                                    <input type="hidden" name="extrachargesname[]" id="extrachargesname'+rowcount+'" class="extrachargesname" value="">\
                                    <input type="hidden" name="extrachargepercentage[]" id="extrachargepercentage'+rowcount+'" class="extrachargepercentage" value="">\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-3 text-right p-n pt-md">\
                            <button type="button" class="btn btn-default btn-raised remove_charges_btn m-n" onclick="removecharge('+rowcount+')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
                            <button type="button" class="btn btn-default btn-raised add_charges_btn m-n" onclick="addnewcharge()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>\
                        </div>\
                    </th>\
                    <td class="text-right">\
                        <div class="form-group p-n" id="extrachargeamount'+rowcount+'_div">\
                            <div class="col-sm-12">\
                                <input type="text" id="extrachargeamount'+rowcount+'" name="extrachargeamount[]" class="form-control text-right extrachargeamount" placeholder="Charge" onkeypress="return decimal_number_validation(event, this.value)">\
                            </div>\
                        </div>\
                    </td>\
                </tr>';
    
        $(".remove_charges_btn:first").show();
        $(".add_charges_btn:last").hide();
        $("#countcharges"+(rowcount-1)).after(datahtml);
        
        $("#extrachargesid"+rowcount).selectpicker("refresh");
}
function removecharge(rowid){

    if($('select[name="extrachargesid[]"]').length!=1 && ACTION==1 && $('#extrachargemappingid'+rowid).val()!=null){
        var removeextrachargemappingid = $('#removeextrachargemappingid').val();
        $('#removeextrachargemappingid').val(removeextrachargemappingid+','+$('#extrachargemappingid'+rowid).val());
    }
    $("#countcharges"+rowid).remove();

    $(".add_charges_btn:last").show();
    if ($(".remove_charges_btn:visible").length == 1) {
        $(".remove_charges_btn:first").hide();
    }
    var calcdiscount = ($("#overalldiscountamount").val()!=""?0:1);
    changenetamounttotal(calcdiscount);
}
function addattachfile(){

    var rowcount = parseInt($(".countfiles:last").attr("id").match(/\d+/))+1;
    var element = "file"+rowcount;
    var datahtml = '<div class="col-md-6 p-n countfiles" id="countfiles'+rowcount+'">\
                        <div class="col-md-7">\
                            <div class="form-group" id="file'+rowcount+'_div">\
                                <div class="col-md-12 pl-n">\
                                    <div class="input-group" id="fileupload'+rowcount+'">\
                                        <span class="input-group-btn" style="padding: 0 0px 0px 0px;">\
                                            <span class="btn btn-primary btn-raised btn-file"><i class="fa fa-upload"></i>\
                                                <input type="file" name="file'+rowcount+'" class="file" id="file'+rowcount+'" accept=".bmp,.bm,.gif,.ico,.jfif,.jfif-tbnl,.jpe,.jpeg,.jpg,.pbm,.png,.svf,.tif,.tiff,.wbmp,.x-png,.doc,.docx,.pdf" onchange="validattachmentfile($(this),&apos;'+element+'&apos;,this)">\
                                            </span>\
                                        </span>\
                                        <input type="text" readonly="" id="Filetext'+rowcount+'" class="form-control" name="Filetext[]" value="">\
                                    </div>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-3">\
                            <div class="form-group" id="fileremarks'+rowcount+'_div">\
                                <input type="text" class="form-control" name="fileremarks'+rowcount+'" id="fileremarks'+rowcount+'" value="">\
                            </div>\
                        </div>\
                        <div class="col-md-2 pl-sm pr-sm mt-md">\
                            <button type="button" class="btn btn-default btn-raised remove_file_btn m-n" onclick="removeattachfile('+rowcount+')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
                            <button type="button" class="btn btn-default btn-raised add_file_btn m-n" onclick="addattachfile()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>\
                        </div>\
                    </div>';
    
    $(".remove_file_btn:first").show();
    $(".add_file_btn:last").hide();
    $("#countfiles"+(rowcount-1)).after(datahtml);
    if($(".countfiles").length == 1){
        $("#filesheading2").hide();
    }else{
        $("#filesheading2").show();
    }
}
function removeattachfile(rowid){

    if($('.countfiles').length!=1 && ACTION==1 && $('#transactionattachmentid'+rowid).val()!=null){
        var removetransactionattachmentid = $('#removetransactionattachmentid').val();
        $('#removetransactionattachmentid').val(removetransactionattachmentid+','+$('#transactionattachmentid'+rowid).val());
    }
    $("#countfiles"+rowid).remove();
    if($(".countfiles").length == 1){
        $("#filesheading2").hide();
    }else{
        $("#filesheading2").show();
    }
    $(".add_file_btn:last").show();
    if ($(".remove_file_btn:visible").length == 1) {
        $(".remove_file_btn:first").hide();
    }
}
function resetdata(){  
  
    $("#vendor_div").removeClass("has-error is-focused");
    $("#quotationid_div").removeClass("has-error is-focused");
    $("#billingaddress_div").removeClass("has-error is-focused");
    $("#shippingaddress_div").removeClass("has-error is-focused");
    $("#quotationdate_div").removeClass("has-error is-focused");
    $("#paymenttype_div").removeClass("has-error is-focused");
    $("#noofinstallment_div").removeClass("has-error is-focused");
    $("#emidate_div").removeClass("has-error is-focused");
    $("#emiduration_div").removeClass("has-error is-focused");

    if(ACTION==1){
        $('html, body').animate({scrollTop:0},'slow');
    }else{
        $('#vendorid').val('0');
        $('#roundoff').html('0.00');
        $('#paymenttypeid').val('0');
        $('#partialpaymentoption,#installmentmaindiv,#installmentdivs,#installmentsetting_div').hide();
        $('.selectpicker').selectpicker('refresh');

        $("#product1_div").removeClass("has-error is-focused");
        $("#price1_div").removeClass("has-error is-focused");
        $("#comboprice1_div").removeClass("has-error is-focused");
        $("#actualprice1_div").removeClass("has-error is-focused");
        $("#qty1_div").removeClass("has-error is-focused");
        $("#discount1_div").removeClass("has-error is-focused");
        $("#amount1_div").removeClass("has-error is-focused");

        var i=1;
        $('.countproducts').each(function(){
            var id = $(this).attr('id').match(/\d+/);
            if(id!=1){
                $('#quotationproductdiv'+id).remove();
            }
            i++;
        });
        $('.add_remove_btn:first').show();
        $('.add_remove_btn_product').hide();
        
        changenetamounttotal();
        $('#installmentdivs').html('');
        $("#installmentmaindivheading").hide();
        $('html, body').animate({scrollTop:0},'slow');
    }
    
}
function checkvalidation(){
    
    var vendorid = $("#vendorid").val();
    var billingaddressid = $("#billingaddressid").val();
    var shippingaddressid = $("#shippingaddressid").val();
    var quotationid = $("#quotationid").val().trim();
    var quotationdate = $("#quotationdate").val();
    var quotationtype = $("#quotationtype").val().trim();
    var paymenttypeid = $("#paymenttypeid").val().trim();
    var noofinstallment = $('#noofinstallment').val().trim();
    var percentage = $("input[name='percentage[]']").map(function(){return $(this).val();}).get();

    var isvalidvendorid = isvalidquotationid = isvalidpaymenttype = 0;
    var isvalidproductid = isvalidpriceid = isvalidcombopriceid = isvalidqty = isvalidamount = isvalidinstallment = isvalidbillingaddressid = isvalidshippingaddressid = isvalidquotationdate = isvalidduplicatecharges = isvalidextrachargesid = isvalidextrachargeamount = isvaliduniqueproducts = isvalidactualprice = 1;

    PNotify.removeAll();
    if(vendorid == 0){
        $("#vendor_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select vendor !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidvendorid = 0;
    }else {
        isvalidvendorid = 1;
    }
    if(billingaddressid == 0){
        $("#billingaddress_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select billing address !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidbillingaddressid = 0;
    }else {
        $("#billingaddress_div").removeClass("has-error is-focused");
    }
    if(shippingaddressid == 0){
        $("#shippingaddress_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select shipping address !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidshippingaddressid = 0;
    }else {
        $("#shippingaddress_div").removeClass("has-error is-focused");
    }
    if(quotationid == ''){
        $("#quotationid_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter quotation ID !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidquotationid = 0;
    }else {
        isvalidquotationid = 1;
    }
    if(quotationdate == ''){
        $("#quotationdate_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select quotation date !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidquotationdate = 0;
    }else {
        $("#quotationdate_div").removeClass("has-error is-focused");
    }

    var c=1;
    $('.countproducts').each(function(){
        var id = $(this).attr('id').match(/\d+/);
       
        if($("#productid"+id).val() > 0 ||$("#priceid"+id).val() != "" || $("#combopriceid"+id).val() != "" || $("#actualprice"+id).val() != "" || $("#qty"+id).val() == 0 || $("#amount"+id).val() > 0 || id==1){
            if($("#productid"+id).val() == 0){
                $("#product"+id+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(c)+' product !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidproductid = 0;
            }else {
                $("#product"+id+"_div").removeClass("has-error is-focused");
            }
            if($("#priceid"+id).val() == "" || $("#priceid"+id+" option:selected").text() == "Select Variant"){
                $("#price"+id+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(c)+' variant !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidpriceid = 0;
            }else {
                $("#price"+id+"_div").removeClass("has-error is-focused");
            }
            if($("#combopriceid"+id).val() == ""){
                $("#comboprice"+id+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(c)+' price !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidcombopriceid = 0;
            }else {
                $("#comboprice"+id+"_div").removeClass("has-error is-focused");
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
            $("#product"+id+"_div").removeClass("has-error is-focused");
            $("#price"+id+"_div").removeClass("has-error is-focused");
            $("#comboprice"+id+"_div").removeClass("has-error is-focused");
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
        
        if(uniqueproducts.value!="" && $("#productid"+id[0]).val()!=0 && ($("#priceid"+id[0]).val()!="" && $("#priceid"+id[0]+" option:selected").text()!="Select Variant")){
            if(values.indexOf(uniqueproducts.value)>-1) {
                $("#product"+id[0]+"_div,#price"+id[0]+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(j+1)+' is different product & price !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvaliduniqueproducts = 0;
            }
            else{ 
                values.push(uniqueproducts.value);
                if($("#productid"+id[0]).val()!=0 && ($("#priceid"+id[0]).val()!="" && $("#priceid"+id[0]+" option:selected").text()!="Select Variant")){
                    $("#product"+id[0]+"_div,#price"+id[0]+"_div").removeClass("has-error is-focused");
                }
            }
        }
    }


    var i=1;
    $('.countcharges').each(function(){
        var id = $(this).attr('id').match(/\d+/);
        
        if($("#extrachargesid"+id).val() > 0 || ($("#extrachargeamount"+id).val() != "" && $("#extrachargeamount"+id).val() > 0)){

            if($("#extrachargesid"+id).val() == 0){
                $("#extracharges"+id+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(i)+' extra charge !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidextrachargesid = 0;
            }else {
                $("#extracharges"+id+"_div").removeClass("has-error is-focused");
            }
            if($("#extrachargeamount"+id).val() == '' && $("#extrachargeamount"+id).val() == 0){
            $("#extrachargeamount"+id+"_div").addClass("has-error is-focused");
            new PNotify({title: 'Please enter '+(i)+' extra charge amount !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidextrachargeamount = 0;
            }else {
                $("#extrachargeamount"+id+"_div").removeClass("has-error is-focused");
            }
        } else{
            $("#extracharges"+id+"_div").removeClass("has-error is-focused");
            $("#extrachargeamount"+id+"_div").removeClass("has-error is-focused");
        }
        i++;
    });

    var selects_charges = $('select[name="extrachargesid[]"]');
    var values = [];
    for(j=0;j<selects_charges.length;j++) {
        var selectscharges = selects_charges[j];
        var id = selectscharges.id.match(/\d+/);
        
        if(selectscharges.value!=0){
            if(values.indexOf(selectscharges.value)>-1) {
                $("#extracharges"+id[0]+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(j+1)+' is different extra charges !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidduplicatecharges = 0;
            }
            else{ 
                values.push(selectscharges.value);
                if($("#extrachargesid"+id[0]).val()!=0){
                $("#extracharges"+id[0]+"_div").removeClass("has-error is-focused");
                }
            }
        }
    }
    if(paymenttypeid == 0){
        $("#paymenttype_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select payment type !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidpaymenttype = 0;
    }else {
        isvalidpaymenttype = 1;
    }
   
    if(paymenttypeid == 4){
        if(percentage.length == 0){
            //$("#transactionid_div").addClass("has-error is-focused");
            new PNotify({title: 'Please generate installment !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidinstallment = 0;
        }
    }
   
    if(isvalidvendorid == 1 && isvalidbillingaddressid ==1 && isvalidquotationid == 1 && isvalidproductid == 1 && isvalidpriceid == 1 && isvalidcombopriceid == 1 && isvalidactualprice==1 && isvalidqty == 1 && isvalidamount == 1 && isvalidpaymenttype == 1 && isvalidinstallment == 1 && isvalidshippingaddressid == 1 && isvalidquotationdate == 1 && isvalidextrachargesid == 1 && isvalidextrachargeamount == 1 && isvalidduplicatecharges == 1 && isvaliduniqueproducts == 1){
  
        var formData = new FormData($('#purchasequotationform')[0]);
        if(ACTION==0){
            var uurl = SITE_URL+"purchase-quotation/add-purchase-quotation";
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
                        new PNotify({title: "Purchase quotation successfully added !",styling: 'fontawesome',delay: '3000',type: 'success'});
                        setTimeout(function() { window.location=SITE_URL+"purchase-quotation"; }, 1500);
                    }else if(data['error']==2){
                        new PNotify({title: "Purchase quotation already exist !",styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else if(data['error']==-2){
                        new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
                        $("#file"+data['id']+"_div").addClass("has-error is-focused");
                    }else if(data['error']==-3){
                        new PNotify({title: 'File is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE)+') !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        $("#file"+data['id']+"_div").addClass("has-error is-focused");
                    }else{
                        new PNotify({title: 'Purchase quotation not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
            var uurl = SITE_URL+"purchase-quotation/update-purchase-quotation";
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
                    new PNotify({title: "Purchase quotation successfully updated.",styling: 'fontawesome',delay: '3000',type: 'success'});
                    setTimeout(function() { window.location=SITE_URL+"purchase-quotation"; }, 1500);
                }else if(data['error']==2){
                    new PNotify({title: "Purchase quotation already exist !",styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(data['error']==-2){
                    new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
                    $("#file"+data['id']+"_div").addClass("has-error is-focused");
                }else if(data['error']==-3){
                    new PNotify({title: 'File is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE)+') !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    $("#file"+data['id']+"_div").addClass("has-error is-focused");
                }else{
                    new PNotify({title: 'Purchase quotation not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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