$(document).ready(function() {   
    
    $("#productbarcode").scannerDetection({
        timeBeforeScanTest: 200, // wait for the next character for upto 200ms
        startChar: [120], // Prefix character for the cabled scanner (OPL6845R)
        endChar: [10], // be sure the scan is complete if key 13 (enter) is detected
        avgTimeByChar: 40, // it's not a barcode if a character takes longer than 40ms
        onComplete: function(barcode, qty){
            checkBarcode();
        }, // main callback function 
    });

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
        $("#discount"+divid).val('');
        if(partialpayment==1 && EMIreceived==0 && $("#installmentdivs").html()!=""){
            generateinstallment();
        }
        if(ACTION==1){
            addproductondelivery();
        }
    });

    loadpopover();
    
    $('.installmentdate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayBtn:"linked",
    });
    $('#quotationdate').datepicker({
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
    $('.deliverytype_date').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        autoclose: true,
        endDate: "+1m",
        startDate: "-1m",
        todayBtn:"linked",
        clearBtn: 'Clear',
    });

    $(".add_remove_btn").hide();
    $(".add_remove_btn:last").show();
    $(".add_charges_btn").hide();
    $(".add_charges_btn:last").show();
    $(".add_file_btn").hide();
    $(".add_file_btn:last").show();

    $("#qty1").TouchSpin(touchspinoptions);

    $('#emidate,#orderdate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        todayBtn:"linked",
    });
    // if(ACTION==1 || ISDUPLICATE==1){
    //     getbillingaddress();
    // }
    if(ACTION==1){
        $("#advancepayment").attr("data-calculate","true");
    }
    /****MEMBER CHANGE EVENT****/
    $('#vendorid').on('change', function (e) {
        $(".qty").parents('.form-group').removeClass("has-error is-focused");
        getbillingaddress();
        getChannelSettingByVendor();
        getproduct();
        var oldvendorid = $("#oldvendorid").val();
        $('.applyoldprice').prop("checked",false);
        $('.applyoldprice').prop("disabled",true);
        if((ISDUPLICATE==1 || ACTION==1) && (this.value == oldvendorid && this.value!=0)){
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
        if((ISDUPLICATE==1 || ACTION==1) && (this.value == oldproductid[divid-1] && this.value!=0) && vendorid==oldvendorid){
            $('#applyoldprice'+divid).prop("disabled",false);
            $('#applyoldprice'+divid).prop("checked",true);
        }
        $("#qty"+divid).val('1');
        $("#qty"+divid).trigger("touchspin.updatesettings", {min: 1, step: 1});
        $("#qty"+divid).prop("readonly",false);
        
        //$("#discount"+divid+",#discountinrs"+divid+", #amount"+divid).val('');
        changeproductamount(divid);
        changeextrachargesamount();
       
        changenetamounttotal();

        if(partialpayment==1 && EMIreceived==0 && $("#installmentdivs").html()!=""){
            generateinstallment();
        }

        showLastPurchaseOrderProductRateSuggestionBox(divid);
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
            var referencetype = parseFloat($("#priceid"+divid+" option:selected").attr("data-referencetype"));
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
        
        if((ISDUPLICATE==1 || ACTION==1) && (this.value == oldpriceid[divid-1] && this.value!="") && productid==oldproductid[divid-1] && vendorid==oldvendorid){
            $('#applyoldprice'+divid).prop("disabled",false);
            $('#applyoldprice'+divid).prop("checked",true);
        } */
        getmultiplepricebypriceid(divid);
        calculatediscount(divid);
        changeproductamount(divid);
        changeextrachargesamount();
        //$("#discount"+divid).val('');
        if(partialpayment==1 && EMIreceived==0 && $("#installmentdivs").html()!=""){
            generateinstallment();
        }
        if(ACTION==1){
            addproductondelivery();
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
            $("#qty"+divid).val(parseFloat(quantity).toFixed(2));
            $('#originalprice'+divid).val(parseFloat(actualprice).toFixed(2));
            
            var oldvendorid = $("#oldvendorid").val();
            var vendorid = (ACTION==1)?oldvendorid:$("#vendorid").val();
            var productid = $("#productid"+divid).val();
            var priceid = $("#priceid"+divid).val();
            $('#applyoldprice'+divid).prop("checked",false);
            $('#applyoldprice'+divid).prop("disabled",true);

            if((ISDUPLICATE==1 || ACTION==1) && (parseFloat(actualprice).toFixed(2) == parseFloat(oldprice[divid-1]).toFixed(2) && actualprice!="") && productid==oldproductid[divid-1] && vendorid==oldvendorid && priceid==oldpriceid[divid-1]){
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
        
        if(partialpayment==1 && EMIreceived==0 && $("#installmentdivs").html()!=""){
            generateinstallment();
        }
        if(ACTION==1){
            addproductondelivery();
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
        if(ACTION==1){
            addproductondelivery();
        }
    });
    /****PRODUCT QUANTITY CHANGE EVENT****/
    $('body').on('change', '.qty', function() {
        var divid = $(this).attr("div-id");
        
        // START - Minimum or Maximum Order Quantity Settings
        var qty = parseFloat($(this).val());
        // var minimumorderqty = $("#priceid"+divid+" option:selected").attr('data-minimumorderqty');
        // var maximumorderqty = $("#priceid"+divid+" option:selected").attr('data-maximumorderqty');
        var pricetype = $("#priceid"+divid+" option:selected").attr('data-pricetype');
        PNotify.removeAll();
        /* if(parseInt(minimumorderqty) > 0 && parseInt(qty) < parseInt(minimumorderqty)){
            new PNotify({title: 'Minimum '+parseInt(minimumorderqty)+' quantity required for this product !',styling: 'fontawesome',delay: '3000',type: 'error'});
            if(parseInt(pricetype)==0){
                $(this).val(parseInt(minimumorderqty));
            }
        }
        if(parseInt(maximumorderqty) > 0 && parseInt(qty) > parseInt(maximumorderqty)){
            new PNotify({title: 'Maximum '+parseInt(maximumorderqty)+' quantity allow for this product !',styling: 'fontawesome',delay: '3000',type: 'error'});
            if(parseInt(pricetype)==0){
                $(this).val(parseInt(maximumorderqty));
            }
        } */
        // END - Minimum or Maximum Order Quantity Settings
        calculatediscount(divid);
        changeproductamount(divid);
        changeextrachargesamount();
        var deliveryqty=0;
        $(".deliveryqty").each(function( index ) {
            if($(this).attr("div-id") == divid){
                deliveryqty += parseFloat($(this).val()); 
            }
        });
       
        if(parseFloat(deliveryqty)==parseFloat($(this).val())){
            $(".duplicate").prop("disabled", true);
        }else if(parseFloat($(this).val()) >= parseFloat(deliveryqty)){
            $(".duplicate").prop("disabled", false);
        }
        updatematchprice(divid);
    });
   
    $('body').on('keyup', '.discount', function() { 
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
                $("#discountamount").html(parseFloat(discountamount).toFixed(2)); 

                changeextrachargesamount();
                var extrachargesamount = 0;
                $(".extrachargeamount").each(function( index ) {
                    if($(this).val()!=""){
                        extrachargesamount += parseFloat($(this).val());
                    }
                });
             
                var netamount = parseFloat(grossamount) - parseFloat(discountamount) + parseFloat(extrachargesamount);
                if(netamount<0){
                    netamount=0;
                }
                var roundoff =  Math.round(parseFloat(netamount).toFixed(2))-parseFloat(netamount);
                netamount =  Math.round(parseFloat(netamount).toFixed(2));
                $("#roundoff").html(format.format(roundoff));
                $("#inputroundoff").val(parseFloat(roundoff).toFixed(2));
                $("#netamount").html(parseFloat(netamount).toFixed(2));
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
        if(partialpayment==1 && EMIreceived==0 && $("#installmentdivs").html()!=""){
            generateinstallment();
        }
    });
    $('#overalldiscountamount').on('keyup', function() { 
        var discountamount = $(this).val();
        var discountpercentage = $("#discountpercentage").html();
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
            if(parseFloat(discountamount)>parseFloat(grossamount)){
                $(this).val(parseFloat(gstongrossamount).toFixed(2));
                discountamount = parseFloat(gstongrossamount);
            }
            if(parseFloat(gstongrossamount)!=''){
                var discountpercentage = ((parseFloat(discountamount)*100) / parseFloat(gstongrossamount));
                if(parseFloat(discountpercentage)==0){
                    $("#overalldiscountpercent").val(0);   
                }else{
                    $("#overalldiscountpercent").val(parseFloat(discountpercentage).toFixed(2));   
                }

                $("#discountpercentage").html(parseFloat(discountpercentage).toFixed(2)); 
                $("#discountamount").html(parseFloat(discountamount).toFixed(2)); 
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
                if(netamount<0){
                    netamount=0;
                }
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
            $("#overalldiscountpercent").val('');
            $("#discountpercentage").html("0"); 
            $("#discountamount").html("0.00"); 
            changeextrachargesamount(); 
        }
        if(partialpayment==1 && EMIreceived==0 && $("#installmentdivs").html()!=""){
            generateinstallment();
        }
        //changenetamounttotal(); 
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
        $('#transactionid_div,#advancepayment_div').hide();
        $('#transactionid').val('');
        if(type==1){
            $('#partialpaymentoption,#transactionproof_div,#installmentmaindiv,#installmentdivs,#installmentsetting_div').hide();
            $('#transactionid_div,#transactionproof_div,#advancepayment_div').show();
        }else if(type==3 ){
            $('#partialpaymentoption,#installmentmaindiv,#installmentdivs,#installmentsetting_div').hide();
            $('#transactionid_div,#transactionproof_div,#advancepayment_div').show();
        }else if(type==4){
            $('#transactionproof_div,#advancepayment_div').hide();
            $('#partialpaymentoption,#installmentmaindiv,#installmentdivs').show();
            $("#installmentsetting_div").show();
        }else{
            $('#partialpaymentoption,#transactionproof_div,#advancepayment_div,#installmentmaindiv,#installmentdivs,#installmentsetting_div').hide();
        }
    });
    $('#advancepayment').on('keyup', function() {
        var netamount = ($("#inputnetamount").val()!=""?$("#inputnetamount").val():0);
        $(this).attr("data-calculate", true);
        if(this.value!=""){
            if(parseFloat(this.value) > parseFloat(netamount)){
                $(this).val(parseFloat(netamount).toFixed(2));
            }
        }
    });
    $('input[name="deliverytype"]').change(function() {
       
        if($(this).val() == 1){
            $('#deliverydays').show();
            $('#deliverydates').hide();
            $('#deliveryschedulefix').hide();
            if(ACTION==0){
                $('#deliveryfromdate,#deliverytodate').val('');
            }
            disabledform();
        }else if($(this).val() == 3){
            $('#deliverydays').hide();
            $('#deliverydates').hide();
            $('#deliveryschedulefix').show();
            disabledform();
            if(ACTION==0 || $("#deliveryschedulefix").html().trim()==""){
                $('#deliveryfromdate,#deliverytodate').val('');
            
                $("#deliveryschedulefix").html('<table class="table table-bordered m-n" width="100%">\
                    <thead>\
                        <tr>\
                            <th>Product Name</th>\
                            <th width="22%">Quantity</th>\
                        </tr>\
                    </thead>\
                </table>');
                fixdeliveryorder(0,0);
            }
        }else if($(this).val() == 2){         
            $('#deliverydays').hide();
            $('#deliveryschedulefix').hide();
            $('#deliverydates').show();
            if(ACTION==0){
                $('#minimumdays,#maximumdays').val('');
            }
            disabledform();
        }
    });

    $('body').on('change', '.deliveryqty', function() { 
        var divid = $(this).attr("div-id");
        var qty = $("#qty"+divid).val()!=undefined && $("#qty"+divid).val()!=''?$("#qty"+divid).val():0;
        var deliveryqty = 0;
       
        $(".deliveryqty").each(function( index ) {
            if($(this).attr("div-id") == divid){
                deliveryqty += parseFloat($(this).val()); 
            }
        });
       
        if(parseFloat(deliveryqty) > parseFloat(qty)){
            $(this).val(0);
            $(".duplicate").prop("disabled", false);
        }else{
            if(parseFloat(deliveryqty)>=parseFloat(qty)){
                $(".duplicate").prop("disabled", true);
            }else{
                $(".duplicate").prop("disabled", false);
            }
        }
    });
    $('body').on('keyup', '.deliveryqty', function() { 
        var divid = $(this).attr("div-id");
        var qty = $("#qty"+divid).val()!=undefined && $("#qty"+divid).val()!=''?$("#qty"+divid).val():0;
        var deliveryqty = 0;
       
        $(".deliveryqty").each(function( index ) {
            if($(this).attr("div-id") == divid){
                deliveryqty += parseFloat($(this).val()); 
            }
        });
        if(parseFloat(deliveryqty) > parseFloat(qty)){
            $(this).val(0);
            $(".duplicate").prop("disabled", false);
        }else{
            if(parseFloat(deliveryqty)>=parseFloat(qty)){
                $(".duplicate").prop("disabled", true);
            }else{
                $(".duplicate").prop("disabled", false);
            }
        }
    });
    $('body').on('click', '.removeorderproducts', function() { 
        $( this ).parents('.table').remove();
        $(".duplicate:first").prop("disabled", false);

        if(ACTION==1){
            var rowid = $( this ).parents('.table').attr('id').match(/(\d+)/g);
            var removedeliveryproductid = $("#removedeliveryproductid").val();
            $("#removedeliveryproductid").val(removedeliveryproductid+','+$('#fixdeliveryid'+rowid).val());
        }
    });
    $('body').on('click', '.duplicate', function() { 
        var divid = $(this).attr("div-id");
        var divcount = parseInt($("#deliveryschedulefix").find('.table:last').attr('id').match(/(\d+)/g)) + 1;
        var duplicatediv = $( this ).parents('.table').attr('id').match(/(\d+)/g);

        fixdeliveryorder(divcount,duplicatediv);
    });
    
    $(".deliveryqty").TouchSpin(touchspinoptions);
    
    $('.deliverydate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        autoclose: true,
        startDate: new Date(),
        endDate:"+1m",
        todayBtn:"linked",
        clearBtn: 'Clear',
    });
   
    disabledform();
    $(".deliverystatus").click(function (){
        if($(this).prop("checked")==true){
            $(this).parents('.table').find(".tdisdisabled").addClass("cls-disabled");
        }else{
            $(this).parents('.table').find(".tdisdisabled").removeClass("cls-disabled");
        }
        if(partialpayment==1 && EMIreceived==0){
            disabledform();
        }
    });
    disabledformwhenEMIisReceived();
   
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
        var grossamount = $("#inputgrossamount").val();
        var chargestaxamount = chargespercent = 0;
        var tax = $("#extrachargesid"+rowid+" option:selected").attr("data-tax");
        var type = $("#extrachargesid"+rowid+" option:selected").attr("data-type");
        var optiontext = $("#extrachargesid"+rowid+" option:selected").text();
       
        if(this.value!=''){
            if(parseFloat(this.value) > parseFloat(grossamount)){
                $(this).val(parseFloat(grossamount).toFixed(2));
            }
            if(tax>0){
                chargestaxamount = parseFloat(this.value) * parseFloat(tax) / (100+parseFloat(tax));
            }
            if(type==0){
                chargespercent = parseFloat(this.value) * 100 / parseFloat(grossamount);
                
            }
        }
        $("#extrachargestax"+rowid).val(parseFloat(chargestaxamount).toFixed(2));
        $("#extrachargepercentage"+rowid).val(parseFloat(chargespercent).toFixed(2));
        if(type==0){
            optiontext = optiontext.split("(");
            $("#extrachargesid"+rowid+" option:selected").text(optiontext[0]+" ("+parseFloat(chargespercent).toFixed(2)+"%)");
            $("#extrachargesid"+rowid).selectpicker("refresh");
            $("#extrachargesname"+rowid).val(optiontext[0]+" ("+parseFloat(chargespercent).toFixed(2)+"%)");
        }
        changenetamounttotal();
        if(partialpayment==1 && EMIreceived==0){
            generateinstallment();
        }
    });

    /****COUNTRY CHANGE EVENT****/
    $('#newcountryid').on('change', function (e) {
            
        $('#newprovinceid')
            .find('option')
            .remove()
            .end()
            .append('<option value="0">Select Province</option>')
            .val('0')
        ;
        $('#newcityid')
            .find('option')
            .remove()
            .end()
            .append('<option value="0">Select City</option>')
            .val('0')
        ;
        $('#newprovinceid').selectpicker('refresh');
        $('#newcityid').selectpicker('refresh');
        getprovince(this.value,'newprovinceid');
    });
    /****PROVINCE CHANGE EVENT****/
    $('#newprovinceid').on('change', function (e) {
        
        $('#newcityid')
            .find('option')
            .remove()
            .end()
            .append('<option value="0">Select City</option>')
            .val('0')
        ;
        $('#newcityid').selectpicker('refresh');
        getcity(this.value,'newcityid');
    });

    getprovince($('#newcountryid').val(),'newprovinceid');
    getcity($('#newprovinceid').val(),'newcityid');

    /****COUNTRY CHANGE EVENT****/
    $('#countryid').on('change', function (e) {
            
        $('#provinceid')
            .find('option')
            .remove()
            .end()
            .append('<option value="0">Select Province</option>')
            .val('0')
        ;
        $('#cityid')
            .find('option')
            .remove()
            .end()
            .append('<option value="0">Select City</option>')
            .val('0')
        ;
        $('#provinceid').selectpicker('refresh');
        $('#cityid').selectpicker('refresh');
        getprovince(this.value);
    });
    /****PROVINCE CHANGE EVENT****/
    $('#provinceid').on('change', function (e) {
        
        $('#cityid')
            .find('option')
            .remove()
            .end()
            .append('<option value="0">Select City</option>')
            .val('0')
        ;
        $('#cityid').selectpicker('refresh');
        getcity(this.value);
    });

    getprovince($('#countryid').val());
    getcity($('#provinceid').val());

    /****BILLING ADDRESS CHANGE EVENT****/
    $('#billingaddressid').on('change', function (e) {
        $('#billingaddress').val($('#billingaddressid option:selected').text());
    });
    /****SHIPPING ADDRESS CHANGE EVENT****/
    $('#shippingaddressid').on('change', function (e) {
        $('#shippingaddress').val($('#shippingaddressid option:selected').text());
    });
    $('#editordernumber').change(function () {
        if($(this).is(':checked')){
          $("#orderid").prop("readonly",false);
        }else{
          $("#orderid").val($("#ordernumber").val()).prop("readonly",true);
        }
    });
});
function showLastPurchaseOrderProductRateSuggestionBox(divid){

    var lastpurchaseproduct = $("#productid"+divid+" option:selected").attr("data-lastpurchaseproduct");
    var productid = $("#productid"+divid).val();
    $('.popover').remove();

    if(productid > 0 && lastpurchaseproduct!=""){
        lastpurchaseproduct = lastpurchaseproduct.toString().split("|");
        var html = '<table class="table table-striped m-n p-n" width="100%"><tr><th class="text-right">Price ('+CURRENCY_CODE+')</th><th>Vendor Name</th></tr>';
        for(var i=0; i < lastpurchaseproduct.length; i++){
            var product = lastpurchaseproduct[i].toString().split("#");
            var vendorname = product[0].trim();
        
            var rate = product[1].trim();
            
            html += '<tr><td class="text-right">'+parseFloat(rate).toFixed(2)+'</td><td>'+vendorname+'</td></tr>';

        }
        html += '</table>';
        
        $("#product"+divid+"_div .bootstrap-select").attr({'data-toggle': 'popover','data-trigger': 'hover','data-container': 'body','data-original-title': 'Last Purchase Product Rate','data-content': html.trim()}).popover({
            "html": true,
            placement: 'right'
        });
    }
}
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
            
            if(partialpayment==1 && EMIreceived==0 && $("#installmentdivs").html()!=""){
                generateinstallment();
            }
            if(ACTION==1){
                addproductondelivery();
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
                            calculatediscount(divid);
                            changeproductamount(divid);
                            changeextrachargesamount();
                            
                            if(partialpayment==1 && EMIreceived==0 && $("#installmentdivs").html()!=""){
                                generateinstallment();
                            }
                            if(ACTION==1){
                                addproductondelivery();
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
function checkBarcode(){
  
    var vendorid = (ACTION==1?$("#oldvendorid").val():$("#vendorid").val());
    var barcode = $.trim($("#productbarcode").val());
    
    var isvalidbarcode=isvalidvendorid=0;
    PNotify.removeAll();
    if(ACTION==0){
        if(vendorid==0){
            $("#vendor_div").addClass("has-error is-focused");
            new PNotify({title: 'Please select vendor !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidvendorid=0;
            $("#productbarcode").val('').focus();
        }else{
            $("#vendor_div").removeClass("has-error is-focused");
            isvalidvendorid=1;
        }
    }else{
        isvalidvendorid=1;
    }
    if(barcode==''){
        $("#productbarcode_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter barcode or QR code number !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidbarcode=0;
        $("#productbarcode").focus();
    }else{
        $("#productbarcode_div").removeClass("has-error is-focused");
        isvalidbarcode=1;
    }
   
    if(isvalidbarcode==1 && isvalidvendorid==1){
        var datastr = 'vendorid='+vendorid+'&barcode='+barcode;
        var baseurl = SITE_URL+'purchase-order/getvendorproductdetailsByBarcode';
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
                    var priceid = obj['isuniversal']==0?obj['priceid']:0;
                    var uniqueid = obj['id']+'_'+priceid;
                    
                    $('select.productid').each(function() {
                        var divid = $(this).attr("div-id");
                        
                        if((this.value==0 || this.value!=0 && $('#priceid'+divid).val()=='') && process==0){
                            if(!productsarray.includes(uniqueid)){
                                $(this).val(obj['id']).selectpicker('refresh').change();
                                $('#priceid'+divid).val(priceid).selectpicker('refresh').change();
                            }
                            process = 1;
                        }
                        if(productsarray.includes(uniqueid) && $("#uniqueproduct"+divid).val()==uniqueid){

                            $('#qty'+divid).val(parseFloat($('#qty'+divid).val()).toFixed(2)+1).change();
                        }
                        
                    });
                    
                    if(process==0 && !productsarray.includes(uniqueid)){
                        addnewproduct();
                        var divid = parseInt($(".amounttprice:last").attr("div-id"));
                        $('#productid'+divid).val(obj['id']).selectpicker('refresh').change();
                        $('#priceid'+divid).val(priceid).selectpicker('refresh').change();
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
                    new PNotify({title: 'Barcode or QR code not match with any vendor product !',styling: 'fontawesome',delay: '3000',type: 'error'});
                }
            },
            complete: function(){
                $('.mask').hide();
                $('#loader').hide();
            },
        });
    }
    
}
function fixdeliveryorder(divcount, duplicatediv){
    var htmlfixdeliverydata = '';
    var count=0;
    var starttable = '<table class="table table-bordered border-panel delivery-slot" width="100%" id="table'+divcount+'"><tbody>';
    var endtable = '</tbody></table>';
    
    var disabledIsDelivered = '';
    if(approvestatus==0){
        disabledIsDelivered = 'disabled';
    }
    
    $(".productid").each(function( index ) {
       
        var divid = $(this).attr("div-id");
        var deliveryqty = 0;
        $(".deliveryqty").each(function( index ) {
            if($(this).attr("div-id") == divid){
                deliveryqty += parseFloat($(this).val()); 
            }
        });

        var btndisabled = 'disabled';
        var onqty = parseFloat($("#qty"+divid).val() - parseFloat(deliveryqty));
        $('.duplicate').prop('disabled', true);
            
        if($(this).val()!="" && $("#productid"+divid+" option:selected").text()!='Select Product'){

            var isdelivered = '';
            if(count==0){
                btndisabled = 'disabled';
                var removebutton = '';
                var delevereddate = '';
                if(divcount!=0){
                    removebutton = '<button class="btn btn-danger btn-raised btn-sm removeorderproducts" type="button" name="removeorderproducts" id="removeorderproducts'+divcount+'"  div-id="'+duplicatediv+'"><i class="fa fa-times"></i> Remove</button>';
                }
                
                if(divcount!=0){
                    isdelivered = $("#isdelivered"+(duplicatediv)).prop("checked")==true?"checked":'';
                    delevereddate = $("#deliverydate"+(duplicatediv)).val();
                }

                htmlfixdeliverydata += starttable+'<tr id="duplicatetable'+divcount+'">\
                                                        <td colspan="3" class="text-right">\
                                                            <div class="col-md-3 p-n">\
                                                                <div class="form-group">\
                                                                    <div class="col-sm-12">\
                                                                        <div class="checkbox pt-n pl-n">\
                                                                            <input id="isdelivered'+divcount+'" type="checkbox" value="0" name="isdelivered'+divcount+'" class="checkradios deliverystatus" '+isdelivered+' '+disabledIsDelivered+'>\
                                                                            <label for="isdelivered'+divcount+'">IsDelivered</label>\
                                                                            <input type="hidden" name="fixdelivery[]" value="'+divcount+'">\
                                                                        </div>\
                                                                    </div>\
                                                                </div>\
                                                            </div>\
                                                            <div class="col-md-4">\
                                                                <div class="form-group mt-n" id="deliverydate'+divcount+'_div">\
                                                                    <div class="col-md-12">\
                                                                        <input type="text" class="form-control deliverydate" id="deliverydate'+divcount+'" name="deliverydate[]" value="'+delevereddate+'" div-id="'+divid+'" placeholder="Delivered date" readonly>\
                                                                    </div>\
                                                                </div>\
                                                            </div>\
                                                            <button class="btn btn-primary btn-raised btn-sm duplicate" type="button" name="duplicate" id="duplicate'+divcount+'" div-id="'+duplicatediv+'" '+btndisabled+'><i class="fa fa-plus"></i> Duplicate</button>\
                                                            '+removebutton+'\
                                                        </td>\
                                                    </tr>';
            }
            
            var productname = $("#productid"+divid+" option:selected").text();
            var productid = $("#productid"+divid+" option:selected").val();
            var priceid = $("#priceid"+divid+" option:selected").val();
            
            if(productid!=0 && priceid!=0){
                var uurl = SITE_URL+"order/getproductvariantinfo";
                $.ajax({
                    url: uurl,
                    type: 'POST',
                    data: {productid:String(productid),priceid:String(priceid)},
                    dataType: 'json',
                    async: false,
                    success: function(response){
                        productname = response;
                    },
                    error: function(xhr) {
                    //alert(xhr.responseText);
                    },
                });
            }
            
            var qty = parseFloat($("#qty"+divid).val() - parseFloat(deliveryqty));
            
            count++;
            htmlfixdeliverydata += '<tr>';
            htmlfixdeliverydata += '<td style="padding-top: 20px;">'+productname+'\
                                        <input type="hidden" name="fixdeliveryproductdata['+divcount+'][]" id="deliveryproductid'+divcount+divid+'" value="'+productid+'" div-id="'+divid+'">\
                                    </td>\
                                    <td width="22%" class="tdisdisabled">\
                                        <div class="form-group mt-n" id="deliveryqty'+divcount+divid+'_div">\
                                            <div class="col-md-9">\
                                                <input type="text" class="form-control deliveryqty" id="deliveryqty'+divcount+divid+'" name="deliveryqty['+divcount+'][]" value="'+qty+'" maxlength="2" onkeypress="return isNumber(event);" style="display: block;" div-id="'+divid+'">\
                                            </div>\
                                        </div>\
                                    </td>';
            htmlfixdeliverydata += '</tr>';

            if(count==$(".productid").length-1){
                htmlfixdeliverydata += endtable;
            }

        }
    });

    if(htmlfixdeliverydata==''){
        htmlfixdeliverydata += starttable+'<tr>\
                                    <td colspan="4" class="text-center">\
                                    No product selected.\
                                    </td>\
                                </tr>'+endtable;
    }
    
    $("#deliveryschedulefix").append(htmlfixdeliverydata);
    loadpopover();
   
    $(".deliverystatus").click(function (){
        if($(this).prop("checked")==true){
            $(this).parents('.table').find(".tdisdisabled").addClass("cls-disabled");
        }else{
            $(this).parents('.table').find(".tdisdisabled").removeClass("cls-disabled");
        }
        disabledform();
    });
    if(ACTION==0){
        $('.deliverystatus').prop("disabled",true);
    }  
    if($("#isdelivered"+duplicatediv).prop("checked")==true){
        $("#isdelivered"+divcount).parents('.table').find(".tdisdisabled").addClass("cls-disabled");
    }else{
        $("#isdelivered"+divcount).parents('.table').find(".tdisdisabled").removeClass("cls-disabled");
    }
    $(".deliveryqty").TouchSpin(touchspinoptions);
    
    $('.deliverydate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        autoclose: true,
        startDate: new Date(),
        endDate:"+1m",
        todayBtn:"linked",
        clearBtn: 'Clear',
    });
}
function addproductondelivery(){
    if($('input[name="deliverytype"]:checked').val()==3){
        if($('.deliverystatus:checked').length == 0){
            
            $('.delivery-slot').each(function( index ) {
                //var divid = $(this).attr("div-id");
                var divcount = $(this).attr("id").match(/(\d+)/g);
                var duplicatediv = $( this ).attr('id').match(/(\d+)/g);
                fixdeliveryorder(divcount,duplicatediv);
                $(this).remove();
            });
        
        }
    }
}
function disabledform(){
    if(ACTION==1){
        
        if($('input[name="deliverytype"]:checked').val()==3){
            if($('.deliverystatus:checked').length > 0){
                $('.add_remove_btn').prop("disabled",true);
                $('.add_remove_btn_product').prop("disabled",true);
                $('.input-group-btn-vertical').addClass("cls-disabled");
                $('.form-control').addClass("cls-disabled").prop('readonly',true);
                $('.deliverystatus').parents('.table').find('.form-control,.input-group-btn-vertical').removeClass("cls-disabled").prop('readonly',false);
                $('.amounttprice').prop('readonly',true);
                if(EDITTAXRATE_CHANNEL==1 && EDITTAXRATE_SYSTEM==1){
                    $('.tax').prop('readonly',false);
                }else{
                    $('.tax').prop('readonly',true);
                }
            }else{
                $('.add_remove_btn').prop("disabled",false);
                $('.add_remove_btn_product').prop("disabled",false);
                $('.input-group-btn-vertical').removeClass("cls-disabled");
                $('.form-control').removeClass("cls-disabled").prop('readonly',false);
                $('#vendorid,#orderid,#deliveryfromdate,#deliverytodate,.tax,.amounttprice').prop('readonly',true);
                $('.amounttprice').prop('readonly',true);
                if(EDITTAXRATE_CHANNEL==1 && EDITTAXRATE_SYSTEM==1){
                    $('.tax').prop('readonly',false);
                }else{
                    $('.tax').prop('readonly',true);
                }
                if($("#applycoupon").text() == "Remove"){
                    $('#overalldiscountpercent,#overalldiscountamount').prop('readonly',true);
                }
                $('select.productid').each(function() {
                    var divid = $(this).attr("div-id");
                    var priceid = $("#priceid"+divid).val();
        
                    if(priceid!=""){
                        pricetype = $("#priceid"+divid+" option:selected").attr("data-pricetype");
                        quantitytype = $("#priceid"+divid+" option:selected").attr("data-quantitytype");
    
                        if(parseInt(pricetype)==1 && parseInt(quantitytype)==1){
                            $("#qty"+divid).prop("readonly",true);
                        }else{
                            $("#qty"+divid).prop("readonly",false);
                        }
                    }
                });
            }
        }else{
            $('.add_remove_btn').prop("disabled",false);
            $('.add_remove_btn_product').prop("disabled",false);
            $('.input-group-btn-vertical').removeClass("cls-disabled");
            $('.form-control').removeClass("cls-disabled").prop('readonly',false);
            $('#vendorid,#orderid,#deliveryfromdate,#deliverytodate,.tax,.amounttprice').prop('readonly',true);
            $('.amounttprice').prop('readonly',true);
            if(EDITTAXRATE_CHANNEL==1 && EDITTAXRATE_SYSTEM==1){
                $('.tax').prop('readonly',false);
            }else{
                $('.tax').prop('readonly',true);
            }
            if($("#applycoupon").text() == "Remove"){
                $('#overalldiscountpercent,#overalldiscountamount').prop('readonly',true);
            }
            $('select.productid').each(function() {
                var divid = $(this).attr("div-id");
                var priceid = $("#priceid"+divid).val();
    
                if(priceid!=""){
                    pricetype = $("#priceid"+divid+" option:selected").attr("data-pricetype");
                    quantitytype = $("#priceid"+divid+" option:selected").attr("data-quantitytype");

                    if(parseInt(pricetype)==1 && parseInt(quantitytype)==1){
                        $("#qty"+divid).prop("readonly",true);
                    }else{
                        $("#qty"+divid).prop("readonly",false);
                    }
                }
            });
        }
        disabledformwhenEMIisReceived()
    }
}
function disabledformwhenEMIisReceived(){
    if(ACTION==1){
        if(partialpayment==1 && EMIreceived==1 && $("#installmentdivs").html()!=""){
           
            $('.add_remove_btn').prop("disabled",true);
            $('.add_remove_btn_product').prop("disabled",true);
            $('.input-group-btn-vertical').addClass("cls-disabled");
            $('.form-control').addClass("cls-disabled").prop('readonly',true);
            $('.deliverystatus').parents('.table').find('.form-control,.input-group-btn-vertical').removeClass("cls-disabled").prop('readonly',true);
            $('#deliveryfromdate,#deliverytodate,#minimumdays,#maximumdays,#remarks').removeClass("cls-disabled");
            $('#billingaddress_div').find('.cls-disabled').removeClass("cls-disabled").prop('readonly',true);
            $('#minimumdays,#maximumdays,#remarks').prop('readonly',false);
            $('#applycoupon').prop('disabled',true);
            //$('input').prop("disabled",true);
            $('.amounttprice').prop('readonly',true);
            if(EDITTAXRATE_CHANNEL==1 && EDITTAXRATE_SYSTEM==1){
                $('.tax').prop('readonly',false);
            }else{
                $('.tax').prop('readonly',true);
            }
        }else{
            $('.add_remove_btn').prop("disabled",false);
            $('.add_remove_btn_product').prop("disabled",false);
            $('.input-group-btn-vertical').removeClass("cls-disabled");
            $('.form-control').removeClass("cls-disabled").prop('readonly',false);
            $(' #vendorid').prop('readonly',true);
            $('.amounttprice').prop('readonly',true);
            if(EDITTAXRATE_CHANNEL==1 && EDITTAXRATE_SYSTEM==1){
                $('.tax').prop('readonly',false);
            }else{
                $('.tax').prop('readonly',true);
            }
            if($("#applycoupon").text() == "Remove"){
                $('#overalldiscountpercent,#overalldiscountamount').prop('readonly',true);
            }
            $('select.productid').each(function() {
                var divid = $(this).attr("div-id");
                var priceid = $("#priceid"+divid).val();
    
                if(priceid!=""){
                    pricetype = $("#priceid"+divid+" option:selected").attr("data-pricetype");
                    quantitytype = $("#priceid"+divid+" option:selected").attr("data-quantitytype");

                    if(parseInt(pricetype)==1 && parseInt(quantitytype)==1){
                        $("#qty"+divid).prop("readonly",true);
                    }else{
                        $("#qty"+divid).prop("readonly",false);
                    }
                }
            });
        }
       
    }
}
function getbillingaddress(loadtype=0){
    $('#billingaddressid')
        .find('option')
        .remove()
        .end()
        .append('<option value="0">Select Billing Address</option>')
        .val('0')
    ;
    $('#shippingaddressid')
        .find('option')
        .remove()
        .end()
        .append('<option value="0">Select Shipping Address</option>')
        .val('0')
    ;
    $('#billingaddressid,#shippingaddressid').selectpicker('refresh');
  
    var vendorid = $("#vendorid").val();
    var BillingAddressID = $("#vendorid option:selected").attr("data-billingid");
    var ShippingAddressID = $("#vendorid option:selected").attr("data-shippingid");

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
  
                    $('#billingaddressid').append($('<option>', { 
                        value: obj['billingaddress'][i]['id'],
                        text : ucwords(obj['billingaddress'][i]['address'])
                    }));
                    $('#shippingaddressid').append($('<option>', { 
                        value: obj['billingaddress'][i]['id'],
                        text : ucwords(obj['billingaddress'][i]['address'])
                    }));
                }
                if(addressid!=0 && (ISDUPLICATE==1 || ACTION==1)){
                    $('#billingaddressid').val(addressid);
                }else if(BillingAddressID!=0){
                    $('#billingaddressid').val(BillingAddressID);
                }
                if(shippingaddressid!=0 && (ISDUPLICATE==1 || ACTION==1)){
                    $('#shippingaddressid').val(shippingaddressid);
                }else if(ShippingAddressID!=0){
                    $('#shippingaddressid').val(ShippingAddressID);
                }
            }
            if (!jQuery.isEmptyObject(obj['channeldata'])) {
                $("#channeladvancepaymentcod").val(parseFloat(obj['channeldata']['advancepaymentcodfororder']).toFixed(2));
            }else{
                $("#channeladvancepaymentcod").val('0');
            }
            if (!jQuery.isEmptyObject(obj['globaldiscount'])) {
                GSTonDiscount = obj['globaldiscount']['gstondiscount'];
                if(ACTION==0 || (ACTION==1 && globaldicountper=="" && globaldicountamount=="")){
                    globaldicountper = (obj['globaldiscount']['discounttype']==1)?parseFloat(obj['globaldiscount']['discount']).toFixed(2):"";
                    globaldicountamount = (obj['globaldiscount']['discounttype']==0)?parseFloat(obj['globaldiscount']['discount']).toFixed(2):"";
                }
                discountminamount = parseFloat(obj['globaldiscount']['minimumbillamount']).toFixed(2);
            }
            // changeextrachargesamount();
            changenetamounttotal();
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
      });
    }
    $('#billingaddressid,#shippingaddressid').selectpicker('refresh');
    if($('#billingaddressid').val()!=0){
        $('#billingaddress').val($('#billingaddressid option:selected').text());
    }else{
        $('#billingaddress').val('');
    }
    if($('#shippingaddressid').val()!=0){
        $('#shippingaddress').val($('#shippingaddressid option:selected').text());
    }else{
        $('#shippingaddress').val('');
    }
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
            .append('<option value="0">Select Product</option>')
            .val('0')
        ;
        $('select.productid').selectpicker('refresh');
        var element = $('select.productid');
    }else{
        $('#productid'+divid)
            .find('option')
            .remove()
            .end()
            .append('<option value="0">Select Product</option>')
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
      //salesproducthtml = "";
      
      var NewProduct = [];
        if(salesproducthtml==''){
            $.ajax({
                url: uurl,
                type: 'POST',
                data: {vendorid:vendorid},
                dataType: 'json',
                async: false,
                success: function(response){
          
                    
                    for(var i = 0; i < response.length; i++) {
                        var productname = response[i]['name'].replace("'","&apos;");
                        if(DROPDOWN_PRODUCT_LIST==0){
                            
                            element.append($('<option>', { 
                                value: response[i]['id'],
                                text : productname,
                                // "data-variants" : JSON.stringify(response[i]['variantdata']),
                                "data-lastpurchaseproduct" : response[i]['lastpurchaseproduct']
                            }));
              
                            // salesproducthtml += '<option data-variants="'+(JSON.stringify(response[i]['variantdata']).replace(/"/g, '&quot;'))+'" value="'+response[i]['id']+'" data-lastpurchaseproduct="'+response[i]['lastpurchaseproduct']+'">'+productname+'</option>';
                            salesproducthtml += '<option value="'+response[i]['id']+'" data-lastpurchaseproduct="'+response[i]['lastpurchaseproduct']+'">'+productname+'</option>';
                        }else{
                            
                            element.append($('<option>', { 
                                value: response[i]['id'],
                                text : productname,
                                // "data-variants" : JSON.stringify(response[i]['variantdata']),
                                "data-lastpurchaseproduct" : response[i]['lastpurchaseproduct'],
                                "data-content" :'<img src="'+PRODUCT_PATH+response[i]['image']+'" style="width:40px">  ' + productname
                            }));
        
                            // salesproducthtml += '<option data-variants="'+(JSON.stringify(response[i]['variantdata']).replace(/"/g, '&quot;'))+'" data-content="<img src=&apos;'+PRODUCT_PATH+response[i]['image']+'&apos; style=&apos;width:40px&apos;>  '+productname+'" value='+response[i]['id']+' data-lastpurchaseproduct="'+response[i]['lastpurchaseproduct']+'">'+productname+'</option>';
                            salesproducthtml += '<option data-content="<img src=&apos;'+PRODUCT_PATH+response[i]['image']+'&apos; style=&apos;width:40px&apos;>  '+productname+'" value='+response[i]['id']+' data-lastpurchaseproduct="'+response[i]['lastpurchaseproduct']+'">'+productname+'</option>';
                        }
        
                        NewProduct.push(response[i]['id']);
                    }
                    
                },
                error: function(xhr) {
                //alert(xhr.responseText);
                },
              });
        }else{
            element.append(salesproducthtml);
        }
      

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
                    $("#actualprice"+divid).val('');
                    $("#qty"+divid).val('1');
                    $("#discount"+divid+",#discountinrs"+divid+",#amount"+divid+",#tax"+divid+",#ordertax"+divid+",#uniqueproduct"+divid).val('');
                }
                changeproductamount(divid);
                changeextrachargesamount();
                $("#discount"+divid+",#discountinrs"+divid).val('');
                if(partialpayment==1 && EMIreceived==0 && $("#installmentdivs").html()!=""){
                    generateinstallment();
                }
                if(ACTION==1){
                    addproductondelivery();
                }
                
            });
        }
        if(PRODUCT_ARR.length > 0){
            getpricebyproductid(PRODUCT_ARR); 
        }
        if(oldproductid[divid-1]!=0){
            $('#productid'+divid).val(oldproductid[divid-1]);
        }
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
   
    var ordertype = $("#ordertype").val();
    if(productids.length > 0){
        for(var i = 0; i < productids.length; i++) {

            var productid = productids[i];
            if(productid!=''){
                var uurl = SITE_URL+"purchase-order/getVariantByProductId";
                var vendorid = $("#vendorid").val();

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

                        var productvariant = JSON.parse($("#productid"+divid+" option:selected").attr("data-variants"));
                        for(var i = 0; i < productvariant.length; i++) {
                            var multiplepricedata = [];
                            if (!$.isEmptyObject(productvariant[i]['multiplepricedata'])) {
                                multiplepricedata = productvariant[i]['multiplepricedata'];
                            }
                            var offerproductsdata = [];
                            if (!$.isEmptyObject(productvariant[i]['offerproductsdata'])) {
                                offerproductsdata = productvariant[i]['offerproductsdata'];
                            }
                            $('#priceid'+divid).append($('<option>', { 
                                value: productvariant[i]['id'],
                                text : productvariant[i]['variantname'],
                                "data-id" : productvariant[i]['priceid'],
                                "data-pricetype" : productvariant[i]['pricetype'],
                                "data-quantitytype" : productvariant[i]['quantitytype'],
                                "data-multipleprices" : JSON.stringify(multiplepricedata),
                                "data-referencetype" : productvariant[i]['referencetype'],
                            }));
                            $('#producttax'+divid).val(productvariant[i]['tax']);
                        }  
                        $('#priceid'+divid).val(priceid);
                        $('#priceid'+divid).selectpicker('refresh');

                        /* for(var i = 0; i < response.length; i++) {
                            $('#priceid'+divid).append($('<option>', { 
                                value: response[i]['id'],
                                text : response[i]['variantname'],
                                "data-id" : response[i]['priceid'],
                                "data-pricetype" : response[i]['pricetype'],
                                "data-quantitytype" : response[i]['quantitytype'],
                                "data-referencetype" : response[i]['referencetype'],
                            }));
                            $('#producttax'+divid).val(response[i]['tax']);
                        }  
                        $('#priceid'+divid).val(priceid);
                        $('#priceid'+divid).selectpicker('refresh'); */
                        
                        getmultiplepricebypriceid(divid);
                        //$('#combopriceid'+divid).val(combopriceid).selectpicker('refresh');
                        $("#combopriceid"+divid+" option:nth-child(2)").attr("selected","selected");

                        var actualprice = parseFloat($("#combopriceid"+divid+" option:nth-child(2)").attr("data-price").trim());
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
                        
                        // $("#discount"+divid+",#discountinrs"+divid).val('');
                        
                        if(partialpayment==1 && EMIreceived==0 && $("#installmentdivs").html()!=""){
                            generateinstallment();
                        }
                        if(ACTION==1){
                            addproductondelivery();
                        }
                    }else{
                        // $('#applyoldprice'+divid+'_div').remove();
                    }
                });
            }
        }     
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
    var ordertype = $("#ordertype").val();
    
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
                $('#tax'+divid+',#ordertax'+divid).val(response[0]['tax']);
            }
           
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
      });

        // var productvariant = JSON.parse($("#productid"+divid+" option:selected").attr("data-variants"));
        // var len = productvariant.length;
        // for(var i = 0; i < productvariant.length; i++) {
           
        //     var multiplepricedata = [];
        //     if (!$.isEmptyObject(productvariant[i]['multiplepricedata'])) {
        //         multiplepricedata = productvariant[i]['multiplepricedata'];
        //     }
        //     var offerproductsdata = [];
        //     if (!$.isEmptyObject(productvariant[i]['offerproductsdata'])) {
        //         offerproductsdata = productvariant[i]['offerproductsdata'];
        //     }
        //     $('#priceid'+divid).append($('<option>', { 
        //         value: productvariant[i]['id'],
        //         text : productvariant[i]['variantname'],
        //         "data-id" : productvariant[i]['priceid'],
        //         "data-pricetype" : productvariant[i]['pricetype'],
        //         "data-quantitytype" : productvariant[i]['quantitytype'],
        //         "data-multipleprices" : JSON.stringify(multiplepricedata),
        //         "data-referencetype" : productvariant[i]['referencetype'],
        //     }));

        //     $('#producttax'+divid).val(productvariant[i]['tax']);
        // }
        
        // if(len==1){
        //     $('#priceid'+divid).val(productvariant[0]['id']).selectpicker('refresh');
        //     $('#priceid'+divid).change();
        // }
        // if((ISDUPLICATE==1 || ACTION==1) && oldpriceid[divid-1]!="undefined" && $('#productid'+divid).val()==oldproductid[divid-1]){
        //     $('#priceid'+divid).val(oldpriceid[divid-1]).selectpicker('refresh').change();
        // }

        // if(ISDUPLICATE==1 || ACTION==1){
        //     var actualprice = parseFloat($('#actualprice'+divid).val()).toFixed(2);
            
        //     if(parseFloat(actualprice) == parseFloat($('#oldpricewithtax'+divid).html())){
        //         $('#applyoldprice'+divid+'_div').hide();
        //         $('#applyoldprice'+divid).prop("checked",false);
        //     }else{
        //         $('#applyoldprice'+divid+'_div').show();
        //     }
        // }

        // if(ACTION==1 && oldtax[divid-1]>=0 && $('#priceid'+divid).val()==oldpriceid[divid-1] && $('#productid'+divid).val()==oldproductid[divid-1] && $('#tax'+divid).val()!=""){
        //     $('#tax'+divid+',#ordertax'+divid).val(oldtax[divid-1]);
        // }else{
        //     var tax = (productvariant.length > 0)?productvariant[0]['tax']:0;
        //     $('#tax'+divid+',#ordertax'+divid).val(tax);
        // }

        // var productid = $("select[name='productid[]']").map(function(){return $(this).val();}).get();

        // if(productid[productid.length-1]!=0){
        //     addnewproduct();
        // }
      
      
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

        // var multipleprices = JSON.parse($("#priceid"+divid+" option:selected").attr("data-multipleprices"));
        // var length = multipleprices.length;
        // for(var i = 0; i < multipleprices.length; i++) {
            
        //     var txt = "";

        //     if(parseInt(pricetype)==1){
        //         txt = CURRENCY_CODE+multipleprices[i]['price']+" "+multipleprices[i]['quantity']+(parseInt(quantitytype)==0?"+":"")+" Qty"
        //     }else{
        //         txt = multipleprices[i]['price'];
        //     }
        //     $('#combopriceid'+divid).append($('<option>', { 
        //         value: multipleprices[i]['id'],
        //         text : txt,
        //         "data-price" : multipleprices[i]['price'],
        //         "data-quantity" : multipleprices[i]['quantity'],
        //         "data-discount" : multipleprices[i]['discount']
        //     }));

        // }
        // if(length==1){
        //     $('#combopriceid'+divid).val(multipleprices[0]['id']).selectpicker('refresh');
        //     $('#combopriceid'+divid).change();
        // }
        // if(ACTION==1 && oldcombopriceid[divid-1]!="undefined" && $('#combopriceid'+divid).val()==""){
        //     $('#combopriceid'+divid).val(oldcombopriceid[divid-1]).selectpicker('refresh').change();

        //    if(productid==oldproductid[divid-1] && priceid==oldpriceid[divid-1]){
        //        var quantity = $("#combopriceid"+divid+" option:selected").attr("data-quantity");
               
        //        if(parseInt(quantitytype)==1 && parseInt(pricetype)==1){
        //            $("#qty"+divid).trigger("touchspin.updatesettings", {min: parseInt(quantity), step: parseInt(quantity)});
        //        }else{
        //            $("#qty"+divid).trigger("touchspin.updatesettings", {min: 1, step: 1});
        //        }
        //    }
        // }

        /*$.ajax({
            url: uurl,
            type: 'POST',
            data: {productid:productid,priceid:String(productpriceid),vendorid:vendorid},
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
        });*/
    }
    $('#combopriceid'+divid).selectpicker('refresh');
}
function validfile(obj){
    var val = obj.val();
    var id = obj.attr('id').match(/\d+/);
    var filename = obj.val().replace(/C:\\fakepath\\/i, '');
  
    switch(val.substring(val.lastIndexOf('.') + 1).toLowerCase()){
      case 'pdf': case 'gif': case 'bmp': case 'jpg': case 'jpeg': case 'png':
        
        $("#textfile").val(filename);
        isvalidfiletext = 1;
        $("#transactionproof_div").removeClass("has-error is-focused");
        break;
      default:
        $("#transactionproof").val("");
        $("#textfile").val("");
        isvalidfiletext = 0;
        $("#transactionproof_div").addClass("has-error is-focused");
        new PNotify({title: 'Please upload valid file !',styling: 'fontawesome',delay: '3000',type: 'error'});
        break;
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
function addnewproduct(){

    productoptionhtml = salesproducthtml;
    // if(PRODUCT_DISCOUNT==0){
    //     discount = "display:none;";
    // }else{ 
    //     discount = "display:block;"; 
    // }
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
                    <input type="text" class="form-control actualprice text-right" id="actualprice'+divcount+'" name="actualprice[]" value="" onkeypress="return decimal_number_validation(event, this.value, 8)" style="display: block;" div-id="'+divcount+'">\
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
                <div class="col-md-12">\
                <label for="discount'+divcount+'" class="control-label">Dis. (%)</label>\
                    <input type="text" class="form-control discount" id="discount'+divcount+'" name="discount[]" value="" div-id="'+divcount+'" onkeypress="return decimal_number_validation(event, this.value)">\
                    <input type="hidden" value="" id="orderdiscount'+divcount+'">\
                </div>\
            </div>\
            <div class="form-group" id="discount'+divcount+'_div">\
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
    </tr>';

    $(".add_remove_btn_product:first").show();
    $(".add_remove_btn:last").hide();
    $("#orderproducttable tbody").append(producthtml);

    disabledform();

    $("#qty"+divcount).TouchSpin(touchspinoptions);

    $(".selectpicker").selectpicker("refresh");
}
function removeproduct(divid){

    if($('select[name="categoryid[]"]').length!=1 && ACTION==1 && $('#orderproductsid'+divid).val()!=null){
        var removeorderproductid = $('#removeorderproductid').val();
        $('#removeorderproductid').val(removeorderproductid+','+$('#orderproductsid'+divid).val());
    }
    $("#orderproductdiv"+divid).remove();

    $(".add_remove_btn:last").show();
    if ($(".add_remove_btn_product:visible").length == 1) {
        $(".add_remove_btn_product:first").hide();
    }
    changeproductamount(divid);
    // changenetamounttotal();
    changeextrachargesamount();
    if(partialpayment==1 && EMIreceived==0 && $("#installmentdivs").html()!=""){
        generateinstallment();
    }
    
    disabledform();
    addproductondelivery();
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
        
        chargestaxamount = parseFloat(chargesamount) * parseFloat(tax) / (100+parseFloat(tax));
        
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
function changeproductamount(divid){
   
    if(divid!=undefined){
        var price = $("#priceid"+divid+" option:selected").text().trim();
        var combopriceid = $("#combopriceid"+divid).val();
        var actualprice = $("#actualprice"+divid).val();
        var qty = ($("#qty"+divid).val()!="")?parseFloat($("#qty"+divid).val()):0;
        var discount = $("#discount"+divid).val();
        var tax = parseFloat($("#producttax"+divid).val()).toFixed(2);
        var edittax = $("#tax"+divid).val();
        edittax = (edittax!="")?parseFloat(edittax).toFixed(2):0;
        actualprice = (actualprice!="")?parseFloat(actualprice).toFixed(2):0;
        
        var ordertax = $("#ordertax"+divid).val();
        var orderprice = $("#oldpricewithtax"+divid).html();
        
        /* if(GST_PRICE == 1){
            var productrate = parseFloat(actualprice).toFixed(2);
        }else{
            var productrate = parseFloat(parseFloat(actualprice) - ((parseFloat(actualprice) * parseFloat(tax) /(100+parseFloat(tax))))).toFixed(2);
        }  */
       
        if(combopriceid!=0 && actualprice!=0 && price!="0" && qty!="0" && price!="" && qty!="" && price!="Select Variant"){
            
            totalamount = productamount = discountamount = 0;
            // if((discount!='0' && discount!="" && productdiscount[divid-1]!=0 && PRODUCT_DISCOUNT == 0) || discount!='0' && discount!="" && PRODUCT_DISCOUNT == 1){
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
            
            /* if((ISDUPLICATE==1 || ACTION==1) && $("input[id=applyoldprice"+divid+"]").is(":checked")){
                if(GST_PRICE == 1){
                    productrate = parseFloat(orderprice);
                }else{
                    productrate = (parseFloat(orderprice) - (parseFloat(orderprice) * parseFloat(ordertax) / (100+parseFloat(ordertax))));
                }
            }
            actualprice = parseFloat(parseFloat(productrate) + (parseFloat(productrate) * parseFloat(edittax) / 100)).toFixed(2);
            
            
            productamount = parseFloat(actualprice)-parseFloat(discountamount);
            totalamount = parseFloat(productamount) * parseFloat(qty);
            producttaxamount = (parseFloat(totalamount) * parseFloat(edittax) / (100 + parseFloat(edittax))); */
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
                if(partialpayment==1 && EMIreceived==0 && $("#installmentdivs").html()!=""){
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
        var divid = $(this).attr("div-id");
        if($(this).val()!="" && $("#qty"+divid).val() >0 ){
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
                    $("#discountamount").html(format.format(globaldicountamount).toFixed(2)); 
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
    if(ACTION==0 || (ACTION==1 && $("#advancepayment").attr("data-calculate")=="true")){
        var inputnetamount = ($("#inputnetamount").val()!=""?$("#inputnetamount").val():0);
        var channeladvancepaymentcod = ($("#channeladvancepaymentcod").val()!=""?$("#channeladvancepaymentcod").val():0);
        
        if(parseFloat(inputnetamount) > 0 && parseFloat(channeladvancepaymentcod) > 0){
            var advancepayment = parseFloat(inputnetamount) * parseFloat(channeladvancepaymentcod) / 100;
            $("#advancepayment").val(parseFloat(Math.round(advancepayment)).toFixed(2));
        }else{
            $("#advancepayment").val("");
        }
    }
}
function generateinstallment(type=0){

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
     /*  $('.amounttprice').each(function (index, value) {
        if($(this).val()!=""){
          totalvalue = totalvalue+parseFloat($(this).val());
        }
      }) */
        totalvalue = $("#inputnetamount").val();
        if(parseFloat(totalvalue)>0){
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
                var dd = emidate.getDate();
                var mm = emidate.getMonth()+ 1;
                var yy = emidate.getFullYear();
                installmentdate = dd+"/"+mm+"/"+yy;
                var paymenthtml = ''; 
                paymenthtml = '<div class="col-md-2 text-center">\
                                        <div class="form-group">\
                                            <div class="col-sm-12">\
                                                <input type="text" id="paymentdate'+(i+1)+'" value="" name="paymentdate[]" class="form-control text-center" div-id="'+(i+1)+'" maxlength="5">\
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
                                    </div>';
            
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
                                <input type="text" id="installmentdate'+(i+1)+'" value="'+installmentdate+'" name="installmentdate[]" class="form-control text-center" div-id="'+(i+1)+'" maxlength="5">\
                            </div>\
                        </div>\
                    </div>\
                    '+paymenthtml+'\
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
                    /* todayBtn:"linked", */
                    clearBtn: true,
                });

            }
        }else{
            $('#installmentdivs').find(".noofinstallmentdiv").remove();
            $('#installmentmaindiv').html("");
            if(type==1){
                new PNotify({title: 'Please add one or more Product !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }
        }
}
function onlypercentage(val){
    fieldval = $("#"+val).val();
    if (parseInt(fieldval) < 0) $("#"+val).val(0);
    if (parseInt(fieldval) > 100) $("#"+val).val(100);
    changenetamounttotal();
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
    $("#orderid_div").removeClass("has-error is-focused");
    $("#billingaddress_div").removeClass("has-error is-focused");
    $("#shippingaddress_div").removeClass("has-error is-focused");
    $("#orderdate_div").removeClass("has-error is-focused");
    $("#transactionid_div").removeClass("has-error is-focused");
    $("#transactionproof_div").removeClass("has-error is-focused");
    $("#paymenttype_div").removeClass("has-error is-focused");
    $("#noofinstallment_div").removeClass("has-error is-focused");
    $("#emidate_div").removeClass("has-error is-focused");
    $("#emiduration_div").removeClass("has-error is-focused");
  
    if(ACTION==0){
        $('#vendorid').val('0');
        $('#roundoff').html('0.00');
        $('.selectpicker').selectpicker('refresh');
        $('#partialpaymentoption,#transactionproof_div,#installmentmaindiv,#installmentdivs,#installmentsetting_div').hide();
        
        $("#product1_div").removeClass("has-error is-focused");
        $("#price1_div").removeClass("has-error is-focused");
        $("#actualprice1_div").removeClass("has-error is-focused");
        $("#comboprice1_div").removeClass("has-error is-focused");
        $("#qty1_div").removeClass("has-error is-focused");
        $("#discount1_div").removeClass("has-error is-focused");
        $("#discountinrs1_div").removeClass("has-error is-focused");
        $("#amount1_div").removeClass("has-error is-focused");

        var i=1;
        $('.countproducts').each(function(){
            var id = $(this).attr('id').match(/\d+/);
            if(id!=1){
                $('#orderproductdiv'+id).remove();
            }
            i++;
        });
        $('.add_remove_btn:first').show();
        $('.add_remove_btn_product').hide();
        
        changenetamounttotal();
        $('#installmentdivs').html('');
        $("#transactionid_div,#installmentmaindivheading").hide();
    }

    $('html, body').animate({scrollTop:0},'slow');
}
function checkvalidation(){
    
    var vendorid = $("#vendorid").val();
    var billingaddressid = $("#billingaddressid").val();
    var shippingaddressid = $("#shippingaddressid").val();
    var orderid = $("#orderid").val().trim();
    var orderdate = $("#orderdate").val();
    
    var paymenttypeid = $("#paymenttypeid").val().trim();
    var transactionid = $("#transactionid").val().trim();
    var transactionproof = $('#textfile').val().trim();
    var noofinstallment = $('#noofinstallment').val().trim();
    var percentage = $("input[name='percentage[]']").map(function(){return $(this).val();}).get();
    
    var isvalidvendorid = isvalidorderid = isvalidpaymenttype = 0;
    var isvalidproductid = isvalidpriceid = isvalidcombopriceid = isvalidqty = isvalidamount = isvalidtransactionid = isvalidtransactionproof = isvalidinstallment = isvalidbillingaddressid = isvalidshippingaddressid = isvalidorderdate = isvalidduplicatecharges = isvalidextrachargesid = isvalidextrachargeamount = isvaliduniqueproducts = isvalidactualprice = isvalidinvoiceno = 1;

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
    if(orderid == ''){
        $("#orderid_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter order ID !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidorderid = 0;
    }else {
        isvalidorderid = 1;
    }
    if(orderdate == ''){
        $("#orderdate_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select order date !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidorderdate = 0;
    }else {
        $("#orderdate_div").removeClass("has-error is-focused");
    }
    var c=1;
    var firstproduct = $('.countproducts:first').attr('id').match(/\d+/);
    $('.countproducts').each(function(){
        var id = $(this).attr('id').match(/\d+/);
       
        if($("#productid"+id).val() > 0 || $("#priceid"+id).val() != "" || $("#combopriceid"+id).val() != "" || $("#actualprice"+id).val() != "" || $("#qty"+id).val() == 0 || $("#amount"+id).val() > 0 || parseInt(id)==parseInt(firstproduct)){
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
            }/* else if(parseInt($("#qty"+id).val()) > 0 && $("#priceid"+id).val() != ""){
                
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
            } */else {
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
        
        if($("#extrachargesid"+id).val() > 0 || $("#extrachargeamount"+id).val() > 0){

            if($("#extrachargesid"+id).val() == 0){
                $("#extracharges"+id+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(i)+' extra charge !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidextrachargesid = 0;
            }else {
                $("#extracharges"+id+"_div").removeClass("has-error is-focused");
            }
            if($("#extrachargeamount"+id).val() == '' || $("#extrachargeamount"+id).val() == 0){
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

    if(ACTION==0){
        var invoiceno = $("#invoiceno").val().trim();
        if(invoiceno == 0 && $("#generateinvoice").prop('checked') == true){
            $("#invoiceno_div").addClass("has-error is-focused");
            new PNotify({title: 'Please enter invoice number !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidinvoiceno = 0;
        }else{
            $("#invoiceno_div").removeClass("has-error is-focused");
        }
    }
    if(paymenttypeid == 0){
        $("#paymenttype_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select payment type !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidpaymenttype = 0;
        $("#transactionid_div").removeClass("has-error is-focused");
    }else {
        isvalidpaymenttype = 1;
        if(paymenttypeid!=3){
            $("#transactionid_div").removeClass("has-error is-focused");
        }
    }
    /* if(paymenttypeid == 3){
        if(transactionid == ""){
            $("#transactionid_div").addClass("has-error is-focused");
            new PNotify({title: 'Please enter transaction ID !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidtransactionid = 0;
        }
        if(transactionproof == ""){
            $("#transactionproof_div").addClass("has-error is-focused");
            new PNotify({title: 'Please select transaction proof !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidtransactionproof = 0;
        }
    } */
    if(paymenttypeid == 4){
        if(percentage.length == 0){
            //$("#transactionid_div").addClass("has-error is-focused");
            new PNotify({title: 'Please generate installment !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidinstallment = 0;
        }
    }
    
    var minimumorderamount = $("#vendorid option:selected").attr("data-minimumorderamount");
    var payableamount = $("#inputnetamount").val();
    
    var isvalidminimumorderamount = 0;
    if(parseFloat(minimumorderamount) > 0 && payableamount!="" && parseFloat(payableamount) < parseFloat(minimumorderamount)){
        new PNotify({title: 'Require minimum order amount is '+format.format(parseFloat(minimumorderamount))+' '+CURRENCY_CODE+' !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }else{
        isvalidminimumorderamount = 1;
    }

    if(isvalidvendorid == 1 && isvalidbillingaddressid ==1 && isvalidorderid == 1 && isvalidproductid == 1 && isvalidpriceid == 1 && isvalidcombopriceid == 1 && isvalidactualprice==1 && isvalidqty == 1 && isvalidamount == 1 && isvalidpaymenttype == 1 && isvalidtransactionid == 1 && isvalidtransactionproof == 1 && isvalidinstallment == 1 && isvalidshippingaddressid == 1 && isvalidorderdate == 1 && isvalidextrachargesid == 1 && isvalidextrachargeamount == 1 && isvalidduplicatecharges == 1 && isvaliduniqueproducts == 1 && isvalidminimumorderamount == 1 && isvalidinvoiceno == 1){
        
        var formData = new FormData($('#purchaseorderform')[0]);
        if(ACTION==0){
            var uurl = SITE_URL+"purchase-order/add-purchase-order";
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
                        new PNotify({title: "Purchase order successfully added !",styling: 'fontawesome',delay: '3000',type: 'success'});
                        setTimeout(function() { window.location=SITE_URL+"purchase-order"; }, 1500);
                    }else if(data['error']==2){
                        new PNotify({title: "Purchase order already exist !",styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else if(data['error']==-1){
                        new PNotify({title: "Quantity greater than stock quantity!",styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else if(data['error']==0){
                        new PNotify({title: 'Purchase order not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else if(data['error']==-2){
                        new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
                        $("#file"+data['id']+"_div").addClass("has-error is-focused");
                    }else if(data['error']==-3){
                        new PNotify({title: 'File is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE)+') !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
            var uurl = SITE_URL+"purchase-order/update-purchase-order";
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
                    new PNotify({title: "Purchase order successfully updated.",styling: 'fontawesome',delay: '3000',type: 'success'});
                    setTimeout(function() { window.location=SITE_URL+"purchase-order"; }, 1500);
                }else if(data['error']==2){
                    new PNotify({title: "Purchase order already exist !",styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(data['error']==-1){
                    new PNotify({title: "Quantity greater than stock quantity !",styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(data['error']==0){
                    new PNotify({title: 'Purchase order not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(data['error']==-2){
                    new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
                    $("#file"+data['id']+"_div").addClass("has-error is-focused");
                }else if(data['error']==-3){
                    new PNotify({title: 'File is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE)+') !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

function openmodal(type){
    PNotify.removeAll();

    if(type==3){
        resetNewVendorForm();
        $('.modal-title').html('Add New Vendor');
        $('#addnewvendorModal').modal('show');
        $("#newmembercode").val(randomPassword(8,8,0,0,0));
    }else{
        var vendorid = (ACTION==1)?$('#oldvendorid').val():$('#vendorid').val();
        vendoraddressresetdata();
        if(vendorid!=0){
            if(type==1){
                $('.modal-title').html('Add Vendor Billing Address');
                $('#addressbtn').attr('onclick','vendoraddresscheckvalidation(1)');
                $('#addressModal').modal('show');
            }else if(type==2){
            
                $('.modal-title').html('Add Vendor Shipping Address');
                $('#addressbtn').attr('onclick','vendoraddresscheckvalidation(2)');
                $('#addressModal').modal('show');
            }
            $('#sameasbillingaddress').prop('checked',true);
            
        }else{
            $("#vendor_div").addClass("has-error is-focused");
            new PNotify({title: 'Please select vendor !',styling: 'fontawesome',delay: '3000',type: 'error'});
        }
    }
}
function vendoraddressresetdata(){

    $("#baname_div").removeClass("has-error is-focused");
    $("#baemail_div").removeClass("has-error is-focused");
    $("#baddress_div").removeClass("has-error is-focused");
    $("#batown_div").removeClass("has-error is-focused");
    $("#bapostalcode_div").removeClass("has-error is-focused");
    $('#bamobileno_div').removeClass("has-error is-focused");
    $('#country_div').removeClass("has-error is-focused");
    $('#province_div').removeClass("has-error is-focused");
    $('#city_div').removeClass("has-error is-focused");
   
    $('#baname').val('');
    $('#baemail').val('');
    $('#baddress').val('');
    $('#batown').val('');
    $('#bapostalcode').val('');
    $('#bamobileno').val('');
    $('#countryid').val(DEFAULT_COUNTRY_ID);
    $('#provinceid').val("0");
    $('#cityid').val("0");
    
    getprovince(DEFAULT_COUNTRY_ID);
  
    $('#bayes').prop("checked", true);
    $('#baname').focus();
    
    $('#countryid,#provinceid,#cityid').selectpicker('refresh');
    $('html, body').animate({scrollTop:0},'slow');
}
function vendoraddresscheckvalidation(type){
    
    var label = 'Billing';
    if(type == 2){
        label = 'Shipping';
    }
    var name = $("#baname").val().trim();
    var email = $("#baemail").val().trim();
    var billingaddress = $("#baddress").val().trim();
    var postalcode = $("#bapostalcode").val().trim();
    var mobileno = $("#bamobileno").val().trim();
    
    var isvalidname = isvalidemail = isvalidbillingaddress = isvalidpostalcode = isvalidmobileno = 0;
    
    PNotify.removeAll();
    if(name == ''){
      $("#baname_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter name !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidname = 0;
    }else { 
      if(name.length<2){
        $("#baname_div").addClass("has-error is-focused");
        new PNotify({title: 'Name require minmum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidname = 0;
      }else{
        isvalidname = 1;
      }
    }
    if(email == ''){
      $("#baemail_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter email !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidemail = 0;
    }else { 
      if(!ValidateEmail(email)){
        $("#baemail_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter valid email !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidemail = 0;
      }else { 
        isvalidemail = 1;
      }
    }
  
    if(billingaddress == ''){
      $("#baddress_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter '+label.toLowerCase()+' address !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidbillingaddress = 0;
    }else { 
      if(billingaddress.length<3){
        $("#baddress_div").addClass("has-error is-focused");
        new PNotify({title: label+' address required minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidbillingaddress = 0;
      }else { 
        isvalidbillingaddress = 1;
      }
    }
    if(postalcode == ''){
      $("#bapostalcode_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter postal code !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidpostalcode = 0;
    }else { 
      if(isNaN(postalcode)){
        $("#bapostalcode_div").addClass("has-error is-focused");
        new PNotify({title: 'Postal code allow only numbers !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidpostalcode = 0;
      }else { 
        isvalidpostalcode = 1;
      }
    }
    if(mobileno == ''){
      $("#bamobileno_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter mobile no. !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidmobileno = 0;
    }else { 
      if(isNaN(mobileno)){
        $("#bamobileno_div").addClass("has-error is-focused");
        new PNotify({title: 'Mobile no. allow only numbers !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmobileno = 0;
      }else if(mobileno.length<10){
        $("#bamobileno_div").addClass("has-error is-focused");
        new PNotify({title: 'Mobile no. required minimum 10 digits !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmobileno = 0;
      }else { 
        isvalidmobileno = 1;
      }
    }
    
    if(isvalidname == 1 && isvalidemail == 1 && isvalidbillingaddress == 1 && isvalidmobileno == 1 && isvalidpostalcode == 1){
  
        var formData = new FormData($('#vendoraddressform')[0]);
        var vendorid = (ACTION==1)?$('#oldvendorid').val():$('#vendorid').val();
        formData.append("vendorid",vendorid);
    
        var uurl = SITE_URL+"purchase-order/add-billing-address";
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
                    new PNotify({title: label+" Address successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
                
                    $('#addressModal').modal('hide');
                    vendoraddressresetdata();

                    if($("#sameasbillingaddress").is(":checked")){
                        $('#billingaddressid').append($('<option>', { 
                            value: obj['id'],
                            text : ucwords(obj['text']),
                            "selected":"selected"
                        }));
                        $('#shippingaddressid').append($('<option>', { 
                            value: obj['id'],
                            text : ucwords(obj['text']),
                            "selected":"selected"
                        }));
                        $('#billingaddressid,#shippingaddressid').selectpicker('refresh');
                    }else{
                        if(type==1){
                            $('#billingaddressid').append($('<option>', { 
                                value: obj['id'],
                                text : ucwords(obj['text']),
                                "selected":"selected"
                            }));
                            $('#billingaddressid').selectpicker('refresh');
                        }else{
                            $('#shippingaddressid').append($('<option>', { 
                                value: obj['id'],
                                text : ucwords(obj['text']),
                                "selected":"selected"
                            }));
                            $('#shippingaddressid').selectpicker('refresh');
                        }
                    }
                }else if(obj['error']==2){
                    new PNotify({title: label+' address already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                }else{
                    new PNotify({title: label+' address not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
function resetNewVendorForm(){

    $("#newmembername_div").removeClass("has-error is-focused");
    $("#newmobile_div").removeClass("has-error is-focused");
    $("#newmembercode_div").removeClass("has-error is-focused");
    $("#newemail_div").removeClass("has-error is-focused");
    $('#newcountry_div').removeClass("has-error is-focused");
    $('#newprovince_div').removeClass("has-error is-focused");
    $('#newcity_div').removeClass("has-error is-focused");
    $("#newgstno_div").removeClass("has-error is-focused");
    $("#newpanno_div").removeClass("has-error is-focused");

    $('#newmembername').val('');
    $('#newgstno').val('');
    $('#newpanno').val('');
    $('#newcountrycodeid').val('+91');
    $('#newmobileno').val('');
    // $('#newmembercode').val(randomPassword(8,8,0,0,0));
    $('#newemail,#newgstno').val('');
    $('#newcountryid').val(DEFAULT_COUNTRY_ID);
    $('#newprovinceid').val("0");
    $('#newcityid').val("0");
    getprovince(DEFAULT_COUNTRY_ID,'newprovinceid');
    
    $('#newcountryid,#newprovinceid,#newcityid').selectpicker('refresh');
    $('html, body').animate({scrollTop:0},'slow');
}
function addNewVendor(){
    
    var name = $("#newmembername").val().trim();
    var membercode = $("#newmembercode").val();
    var countrycodeid = $("#newcountrycodeid").val();    
    var mobileno = $("#newmobileno").val().trim();
    var email = $("#newemail").val().trim();
    var gstno = $("#newgstno").val();
    var panno = $("#newpanno").val();
    var countryid = $("#newcountryid").val();
    var provinceid = $("#newprovinceid").val();
    var cityid = $("#newcityid").val();
   
    var isvalidname = isvalidmembercode = isvalidcountrycodeid = isvalidmobileno = isvalidemail = isvalidgstno = isvalidpanno = isvalidcountryid = isvalidprovinceid = isvalidcityid = 1;;

    PNotify.removeAll();
    if(name==""){
        $("#newmembername_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter name !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidname = 0;
    } else {
        if(name.length<2){
          $("#newmembername_div").addClass("has-error is-focused");
          new PNotify({title: 'Name required minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidname = 0;
        }else{
          $("#newmembername_div").removeClass("has-error is-focused");
        }
    }
    if(membercode==""){
        $("#newmembercode_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter '+member_label+' code !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmembercode = 0;
    } else {
        if(membercode.length<6){
          $("#newmembercode_div").addClass("has-error is-focused");
          new PNotify({title: Member_label+' code required minimum 6 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidmembercode = 0;
        }else{
          $("#newmembercode_div").removeClass("has-error is-focused");
        }
    }
    if(email == ''){
        $("#newemail_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter email !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidemail = 0;
    }else{
        if(!ValidateEmail(email)){
            $("#newemail_div").addClass("has-error is-focused");
            new PNotify({title: 'Please enter valid Email !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidemail = 0;
        }else{
            $("#newemail_div").removeClass("has-error is-focused");
        }
    }

    if(mobileno.trim()=="") {
        $("#newmobile_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter mobile number !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmobileno = 0;
    } else {
        if(mobileno.length!=10){
          $("#newmobile_div").addClass("has-error is-focused");
          new PNotify({title: 'Mobile number allow only 10 digits !',styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidmobileno = 0;
        }else{
          $("#newmobile_div").removeClass("has-error is-focused");
        }
    }
    if(countrycodeid=="" || countrycodeid==0) {
        $("#newmobile_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select country code !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidcountrycodeid = 0;
    } else {
        $("#newmobile_div").removeClass("has-error is-focused");
    }
    if(gstno!="" && gstno.length!=15) {
        $("#newgstno_div").addClass("has-error is-focused");
        new PNotify({title: 'GST number require 15 characters ! !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidgstno = 0;
    } else {
        $("#newgstno_div").removeClass("has-error is-focused");
    }
    if(panno!="" && panno.length!=10) {
        $("#newpanno_div").addClass("has-error is-focused");
        new PNotify({title: 'PAN number require 10 characters ! !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidpanno = 0;
    } else {
        $("#newpanno_div").removeClass("has-error is-focused");
    }
  
    if(countryid==0) {
        $("#newcountry_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select country !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidcountryid = 0;
    } else {
        $("#newcountry_div").removeClass("has-error is-focused");
    }
  
    if(provinceid==0) {
        $("#newprovince_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select province !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidprovinceid = 0;
    } else {
        $("#newprovince_div").removeClass("has-error is-focused");
    }
  
    if(cityid==0) {
        $("#newcity_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select city !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidcityid = 0;
    } else {
        $("#newcity_div").removeClass("has-error is-focused");
    }

    if(isvalidname == 1 && isvalidmembercode == 1 && isvalidcountrycodeid == 1 && isvalidmobileno == 1 && isvalidemail == 1 && isvalidgstno == 1 && isvalidpanno == 1 && isvalidcountryid == 1 && isvalidprovinceid == 1 && isvalidcityid == 1){
        
        var formData = new FormData($('#addnewvendorform')[0]);
        var uurl = SITE_URL+"purchase-order/add-new-vendor";
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
                    new PNotify({title: 'Vendor Successfully Added !',styling: 'fontawesome',delay: '3000',type: 'success'});
                    
                    $('#addnewvendorModal').modal('hide');
                    resetNewVendorForm();

                    $('#vendorid').append($('<option>', { 
                        value: obj['id'],
                        text : obj['text'],
                        "data-code" : obj['membercode'],
                        "data-billingid" : 0,
                        "data-shippingid" : 0,
                        "data-minimumorderamount" : "0.00",
                        "selected":"selected"
                    }));
                    $('#vendorid').selectpicker('refresh');
                    $('#vendorid').change();
                }else if(obj['error'] == 2) {
                    new PNotify({title: 'Mobile number already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(obj['error'] == 3) {
                    new PNotify({title: 'Email already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(obj['error'] == 6){
                    new PNotify({title: 'Vendor code already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(obj['error'] == 7){
                    new PNotify({title: 'Invalid email address !',styling: 'fontawesome',delay: '3000',type: 'error'});
                } else {
                    new PNotify({title: 'Vendor not Added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

function addnewinvoicetransaction(){

    var rowcount = parseInt($(".countinvoice:last").attr("id").match(/\d+/))+1;
    var datahtml = '<div class="countinvoice" id="countinvoice'+rowcount+'">\
                    <div class="row m-n">\
                        <div class="col-md-3">\
                            <div class="form-group" id="invoice'+rowcount+'_div">\
                                <div class="col-sm-12">\
                                    <select id="invoiceid'+rowcount+'" name="category[]" class="selectpicker form-control invoiceid" data-live-search="true" data-select-on-tab="true" data-size="6">\
                                        <option value="0">Select Category</option>\
                                    </select>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-2">\
                            <div class="form-group" id="invoiceaqmount_div">\
                                <div class="col-sm-12">\
                                    <select id="Product" name="Product[]" class="selectpicker form-control " data-live-search="true" data-select-on-tab="true" data-size="6">\
                                        <option value="0">Select Product </option>\
                                    </select>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-2">\
                            <div class="form-group" id="remainingamount'+rowcount+'_div">\
                                <div class="col-md-12">\
                                    <input type="text" id="remainingamount'+rowcount+'" class="form-control text-right remainingamount" value="" readonly>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-2 pt-md">\
                            <button type="button" class="btn btn-danger btn-raised remove_invoice_btn m-n" onclick="removetransaction('+rowcount+')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
                            <button type="button" class="btn btn-primary btn-raised add_invoice_btn m-n" onclick="addnewinvoicetransaction()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>\
                        </div>\
                    </div>\
                </div>';
    
    $(".remove_invoice_btn:first").show();
    $(".add_invoice_btn:last").hide();
    $("#countinvoice"+(rowcount-1)).after(datahtml);
    
    $("#invoiceid"+rowcount).selectpicker("refresh");

    /****INVOICE CHANGE EVENT****/
    // $("#invoiceid"+rowcount).on('change', function (e) {
    //     var divid = $(this).attr("id").match(/\d+/);
    //     $("#amountdue"+divid+",#invoiceamount"+divid+",#remainingamount"+divid).val('');
    //     if(this.value!=0){
    //         var invoiceamount = $("#invoiceid"+divid+" option:selected").attr("data-invoiceamount");

    //         $("#amountdue"+divid).val(parseFloat(invoiceamount).toFixed(2));
    //     }
    //     calculateamount();
    // });

    /****AMOUNT KEYUP EVENT****/
    // $("#invoiceamount"+rowcount).on('keyup', function (e) {
    //     var divid = $(this).attr("id").match(/\d+/);
    //     var amountdue = $("#amountdue"+divid).val();
        
    //     if(amountdue!="" && this.value!=""){
    //         if(parseFloat(this.value) > parseFloat(amountdue)){
    //             $(this).val(parseFloat(amountdue).toFixed(2));
    //         }

    //         var remainingamount = parseFloat(amountdue) - parseFloat(this.value);
    //         $("#remainingamount"+divid).val(parseFloat(remainingamount).toFixed(2));
    //     }
    //     calculateamount();
    // });

}

function removetransaction(rowid){

    if($('select[name="invoiceid[]"]').length!=1 && ACTION==1 && $('#paymentreceipttransactionsid'+rowid).val()!=null){
        var removepaymentreceipttransactionsid = $('#removepaymentreceipttransactionsid').val();
        $('#removepaymentreceipttransactionsid').val(removepaymentreceipttransactionsid+','+$('#paymentreceipttransactionsid'+rowid).val());
    }
    $("#countinvoice"+rowid).remove();

    $(".add_invoice_btn:last").show();
    if ($(".remove_invoice_btn:visible").length == 1) {
        $(".remove_invoice_btn:first").hide();
    }

    changenetamounttotal();
}