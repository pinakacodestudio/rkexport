// productcount=0;
$(document).on('click','.removeoffersbar',function(e) {

    $("body").removeClass('infobar-active');
    $('.infobar-wrapper').css('transform','');
});
$(document).on('click','.trigger-infobar>a',function () {

    var divid = $(this).parents().attr("id").match(/\d+/);
    if($("#priceid"+divid).val()==""){
        $("#price"+divid+"_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select '+(divid)+' price !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }else{

        togglerightbar();
        if($("body").hasClass("infobar-active")){
            displayofferproducts(divid);
        }
    }
});

$('body').on('click', '.layout-static', function () {
    togglerightbar();
});

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
        if($("#applycoupon").text() == "Remove"){
            validatecoupon();
        }
        if(firstlevel==0){
            $("#discount"+divid).val('');
        }
        if(partialpayment==1 && EMIreceived==0 && $("#installmentdivs").html()!=""){
            generateinstallment();
        }
        if(ACTION==1){
            addproductondelivery();
        }
        // validatedoffergrid();
    });

    loadpopover();
    
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

    $("#qty1").TouchSpin(touchspinoptions);

    $('#emidate,#orderdate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        todayBtn:"linked",
    });
    if(ACTION==1){
        getbillingaddress();
    }
    if(addordertype=='1' || addquotationorder==1){
        getbillingaddress(1);
        //getChannelSettingByMember();
    }
    if(ACTION==1){
        $("#advancepayment").attr("data-calculate","true");
    }
    /* if(addordertype=='0' && addquotationorder==1){
        getproductcategory();
    } */
    /****MEMBER CHANGE EVENT****/
    $('#memberid').on('change', function (e) {
        getbillingaddress();
        if(REWARDS_POINTS==1 && addordertype=='0'){
            //memberredeempoints($(this).val());
            //getChannelSettingByMember();
        }
        $(".qty").parents('.form-group').removeClass("has-error is-focused");
        getChannelSettingByMember();
        if(addordertype=='0'){
            /* $('.mask').show();
            $('#loader').show();
            setTimeout(() => {
                $('.mask').hide();
                $('#loader').hide();
            }, 100); */
            getproduct();
        }
        var oldmemberid = $("#oldmemberid").val();
        $('.applyoldprice').prop("checked",false);
        $('.applyoldprice').prop("disabled",true);
        if((addquotationorder==1 || ACTION==1) && (this.value == oldmemberid && this.value!=0)){
            $('#applyoldprice'+divid).prop("disabled",false);
            $('#applyoldprice'+divid).prop("checked",true);
        }
        $(".producttaxamount").val('');
        // $(".tax").val('');
        /* $(".pointsforbuyer").html("0");
        $(".inputpointsforbuyer").val("0");
        $(".inputpointsforseller").val("0");
        $(".memberpointsdiv").hide(); */
        
        if($("#applycoupon").text() == "Remove"){
            validatecoupon();
        }
        changenetamounttotal();
        changeextrachargesamount();
    });
    /****PRODUCT CHANGE EVENT****/
    // $('#productid').on('changed.bs.select', function (e) {
    $('body').on('change', 'select.productid', function() { 
        var divid = $(this).attr("div-id");
        $("#producttaxamount"+divid).val('');
        $("#tax"+divid).val('');
        $('#actualprice'+divid).val("");
        $("#displaystockmessage"+divid).html('');
        $("#ordproductstock"+divid).val('0');

        $("#qty"+divid).val('1');
        $("#qty"+divid).trigger("touchspin.updatesettings", {min: 1, step: 1});
        $("#qty"+divid).prop("readonly",false);
        $("#discount"+divid+",#discountinrs"+divid+", #amount"+divid).val('');

        var productid = $("#productid"+divid).val();
        var uniqueproduct = (productid!="" && productid!=0)?productid+"_0_0.00":"";
        $("#uniqueproduct"+divid).val(uniqueproduct);
        
        getproductprice(divid);
        var oldmemberid = $("#oldmemberid").val();
        var memberid = (ACTION==1)?oldmemberid:$("#memberid").val();
        $('#applyoldprice'+divid).prop("checked",false);
        $('#applyoldprice'+divid).prop("disabled",true);
        if((addquotationorder==1 || ACTION==1) && (this.value == oldproductid[divid-1] && this.value!=0) && memberid==oldmemberid){
            $('#applyoldprice'+divid).prop("disabled",false);
            $('#applyoldprice'+divid).prop("checked",true);
        }
        
        changeproductamount(divid);
        changeextrachargesamount();
        if($("#applycoupon").text() == "Remove"){
            validatecoupon();
        }
        if(REWARDS_POINTS==1){
            getProductRewardpoints(divid);
        }
        // getproductdiscount(divid);
        changenetamounttotal();

        if(partialpayment==1 && EMIreceived==0 && $("#installmentdivs").html()!=""){
            generateinstallment();
        }
        $("#brandoffer"+divid).val("");
        $('#purchaseproductqty'+divid).val("0");
        $("#trigger-infobar"+divid+" .btn-available-offers").css("background","#cc2b1b").html("<i class='material-icons' style='font-size: 20px;'>add_circle_outline</i> Available Offers");
        getofferproducts(divid);
        // $('.infobar-wrapper').css('transform','');
        // $("body").removeClass('infobar-active');
        validatedoffergrid();
    });
    /****PRODUCT VARIANT CHANGE EVENT****/
    $('body').on('change', 'select.priceid', function() { 
        var divid = $(this).attr("div-id");
        $("#producttaxamount"+divid).val('');
        $("#displaystockmessage"+divid).html('');
        $("#ordproductstock"+divid).val('0');
        
        $("#qty"+divid).val('1');
        $("#qty"+divid).trigger("touchspin.updatesettings", {min: 1, step: 1});
        $("#qty"+divid).prop("readonly",false);
        $("#purchaseproductqty"+divid).val('0');
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

        $('#originalprice'+divid).val(parseFloat(actualprice).toFixed(2)); */

        /* var oldmemberid = $("#oldmemberid").val();
        var memberid = (ACTION==1)?oldmemberid:$("#memberid").val();
        var productid = $("#productid"+divid).val();
        $('#applyoldprice'+divid).prop("checked",false);
        $('#applyoldprice'+divid).prop("disabled",true);
        // alert(this.value+"=="+oldpriceid[divid-1]);
        if((addquotationorder==1 || ACTION==1) && (this.value == oldpriceid[divid-1] && this.value!="") && productid==oldproductid[divid-1] && memberid==oldmemberid){
            $('#applyoldprice'+divid).prop("disabled",false);
            $('#applyoldprice'+divid).prop("checked",true);
        }
        if(firstlevel==0){
            // $("#discount"+divid).val('');
        } */
        /* var discount = $("#priceid"+divid+" option:selected").attr("data-discount");
        if(discount > 0){
            $("#discount"+divid).val(discount);
        } */
        
        getmultiplepricebypriceid(divid);
        geProductFIFOStock(divid);
        calculatediscount(divid);
        changeproductamount(divid);
        // changeextrachargesamount();
        if($("#applycoupon").text() == "Remove"){
            validatecoupon();
        }
        
        if(partialpayment==1 && EMIreceived==0 && $("#installmentdivs").html()!=""){
            generateinstallment();
        }
        if(ACTION==1){
            addproductondelivery();
        }

        updatepoints(divid);

        var uniqueproduct = $("#uniqueproduct"+divid).val();
        if(uniqueproduct!=""){
            elementarr = uniqueproduct.split("_");
            var element1 = (this.value!="")?this.value:0;
            $("#uniqueproduct"+divid).val(elementarr[0]+"_"+element1+"_"+elementarr[2]);
        }
        $("#brandoffer"+divid).val("");
        $("#trigger-infobar"+divid+" .btn-available-offers").css("background","#cc2b1b").html("<i class='material-icons' style='font-size: 20px;'>add_circle_outline</i> Available Offers");
        getofferproducts(divid);
        // $("body").removeClass('infobar-active');
        // $('.infobar-wrapper').css('transform','');
        validatedoffergrid();

        displayStockMessage(divid);
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
            
            var oldmemberid = $("#oldmemberid").val();
            var memberid = (ACTION==1)?oldmemberid:$("#memberid").val();
            var productid = $("#productid"+divid).val();
            var priceid = $("#priceid"+divid).val();
            $('#applyoldprice'+divid).prop("checked",false);
            $('#applyoldprice'+divid).prop("disabled",true);
       
            if((addquotationorder==1 || ACTION==1) && (parseFloat(actualprice).toFixed(2) == parseFloat(oldprice[divid-1]).toFixed(2) && actualprice!="") && productid==oldproductid[divid-1] && memberid==oldmemberid && priceid==oldpriceid[divid-1]){
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
        // changeextrachargesamount();
        if($("#applycoupon").text() == "Remove"){
            validatecoupon();
        }
        
        if(partialpayment==1 && EMIreceived==0 && $("#installmentdivs").html()!=""){
            generateinstallment();
        }
        var uniqueproduct = $("#uniqueproduct"+divid).val();
        if(uniqueproduct!=""){
            elementarr = uniqueproduct.split("_");
            var element2 = (this.value!="" && $("#actualprice"+divid).val()!="")?parseFloat($("#actualprice"+divid).val()).toFixed(2):0;
            $("#uniqueproduct"+divid).val(elementarr[0]+"_"+elementarr[1]+"_"+element2);
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
        if($("#applycoupon").text() == "Remove"){
            validatecoupon();
        }
        if(partialpayment==1 && EMIreceived==0 && $("#installmentdivs").html()!=""){
            generateinstallment();
        }
        if(ACTION==1){
            addproductondelivery();
        }
        var uniqueproduct = $("#uniqueproduct"+divid).val();
        if(uniqueproduct!=""){
            elementarr = uniqueproduct.split("_");
            var element2 = (this.value!="" && $("#actualprice"+divid).val()!="")?parseFloat($("#actualprice"+divid).val()).toFixed(2):0;
            $("#uniqueproduct"+divid).val(elementarr[0]+"_"+elementarr[1]+"_"+element2);
        }
    });
    /****PRODUCT QUANTITY CHANGE EVENT****/
    $('body').on('change', '.qty', function() {
        var divid = $(this).attr("div-id");

        // START - Minimum or Maximum Order Quantity Settings
        var qty = parseFloat($(this).val());
        var minimumorderqty = $("#priceid"+divid+" option:selected").attr('data-minimumorderqty');
        var maximumorderqty = $("#priceid"+divid+" option:selected").attr('data-maximumorderqty');
        var pricetype = $("#priceid"+divid+" option:selected").attr('data-pricetype');
        
        PNotify.removeAll();
        if(parseFloat(minimumorderqty) > 0 && parseFloat(qty) < parseFloat(minimumorderqty)){
            new PNotify({title: 'Minimum '+parseFloat(minimumorderqty).toFixed(2)+' quantity required for this product !',styling: 'fontawesome',delay: '3000',type: 'error'});
            if(parseInt(pricetype)==0){
                $(this).val(parseFloat(minimumorderqty).toFixed(2));
            }
        }
        if(parseFloat(maximumorderqty) > 0 && parseFloat(qty) > parseFloat(maximumorderqty)){
            new PNotify({title: 'Maximum '+parseFloat(maximumorderqty).toFixed(2)+' quantity allow for this product !',styling: 'fontawesome',delay: '3000',type: 'error'});
            if(parseInt(pricetype)==0){
                $(this).val(parseFloat(maximumorderqty).toFixed(2));
            }
        }
        // END - Minimum or Maximum Order Quantity Settings

        var multiplypointswithqty = $("#multiplypointswithqty").val();
        var sellermultiplypointswithqty = $("#sellermultiplypointswithqty").val();
        var inputproductwisepoints = $("#inputproductwisepoints").val();
        var inputsellerproductwisepoints = $("#inputsellerproductwisepoints").val();
        var inputproductwisepointsforbuyer = $("#inputproductwisepointsforbuyer").val();
        var inputsellerproductwisepoints = $("#inputsellerproductwisepoints").val();
        
        if(REWARDS_POINTS==1 && ACTION==0){
            var qty = $(this).val();
            if(multiplypointswithqty==1 && inputproductwisepoints==1 && inputproductwisepointsforbuyer==1){
                var memberpoints = parseInt($("#pointsforbuyerwithoutmultiply"+divid).val()) * parseFloat(qty);
                // $("#pointsforbuyer"+divid).html(parseInt(memberpoints));
                $("#inputpointsforbuyer"+divid).val(parseInt(memberpoints));
            }

            if(sellermultiplypointswithqty==1 && inputsellerproductwisepoints==1 && inputsellerproductwisepoints==1){
                
                var referrerpoints = parseInt($("#pointsforsellerwithoutmultiply"+divid).val()) * parseFloat(qty);
                $("#inputpointsforseller"+divid).val(parseInt(referrerpoints));
            }

        }
        
        if(parseFloat(this.value)<parseFloat($('#purchaseproductqty'+divid).val())){
            $('#purchaseproductqty'+divid).val(this.value);
            //$('#purchaseproductqty'+divid).val(0);
            //$("#trigger-infobar"+divid+" .btn-available-offers").css("background","#cc2b1b").html("<i class='material-icons' style='font-size: 20px;'>add_circle_outline</i> Available Offers");
        }
        
        calculatediscount(divid);
        changeproductamount(divid);
        changeextrachargesamount();
        if($("#applycoupon").text() == "Remove"){
            validatecoupon();
        }
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
        $("body").removeClass('infobar-active');
        $('.infobar-wrapper').css('transform','');
        validatedoffergrid();

        updatematchprice(divid);
        setfinalproductqty(divid);
    });
    $('body').on('keyup', '.discount', function() { 
        var divid = $(this).attr("div-id");
        if(divid!=undefined){
            dicountvalue = $("#discount"+divid).val();
            if(parseFloat(dicountvalue)>=100){
                $("#discount"+divid).val("100");
            }
            calculatediscount(divid);
            changeproductamount(divid);
            changeextrachargesamount();
            if($("#applycoupon").text() == "Remove"){
                validatecoupon();
            }
        }
    });
    $('body').on('keyup', '.discountinrs', function(e) { 
        
        var divid = $(this).attr("div-id");
        if(divid!=undefined){
            calculatediscountamount(divid,$(this).val());
            changeproductamount(divid);
            changeextrachargesamount();
            if($("#applycoupon").text() == "Remove"){
                validatecoupon();
            }
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
            if($("#applycoupon").text() == "Remove"){
                validatecoupon();
            }
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
            if(parseFloat(discountpercentage)>=100){
                $(this).val("100");
            }
            if(gstongrossamount!=''){
                var discountamount = (parseFloat(gstongrossamount)*parseFloat(discountpercentage)/100);
                $("#overalldiscountamount").val(parseFloat(discountamount).toFixed(2));
                
                $("#discountpercentage").html(parseFloat(discountpercentage).toFixed(2)); 
                $("#discountamount").html(format.format(discountamount)); 

                var conversationrate = $("#conversationrateamount").html();
                if(addordertype==0){
                    changeextrachargesamount();
                }
                var extrachargesamount = 0;
                $(".extrachargeamount").each(function( index ) {
                    if($(this).val()!=""){
                        extrachargesamount += parseFloat($(this).val());
                    }
                });
             
                var netamount = parseFloat(grossamount) - parseFloat(discountamount) - parseFloat(conversationrate) + parseFloat(extrachargesamount);
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
            $("#overalldiscountamount").val('');
            $("#discountpercentage").html("0"); 
            $("#discountamount").html("0.00"); 
            if(addordertype==0){
                changeextrachargesamount();
            }else{
                changenetamounttotal(1); 
            }
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
        if(discountamount!=undefined && discountamount!='' && parseFloat(discountamount) > 0 && parseFloat(grossamount) > 0 && (parseFloat(discountminamount) == 0 || parseFloat(gstongrossamount) >= parseFloat(discountminamount))){
            if(parseFloat(discountamount)>parseFloat(gstongrossamount)){
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
                $("#discountamount").html(format.format(discountamount)); 
                if(parseFloat(discountpercentage)>100){
                    $("#overalldiscountpercent").val("100");
                }
                var conversationrate = $("#conversationrateamount").html();
                if(addordertype==0){
                    changeextrachargesamount();
                }
                var extrachargesamount = 0;
                $(".extrachargeamount").each(function( index ) {
                    if($(this).val()!=""){
                        extrachargesamount += parseFloat($(this).val());
                    }
                });
                var netamount = parseFloat(grossamount) - parseFloat(discountamount) - parseFloat(conversationrate) + parseFloat(extrachargesamount);
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
            if(addordertype==0){
                changeextrachargesamount();
            }else{
                changenetamounttotal(1); 
            }
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
    /****REDEEM POINT KEYUP EVENT****/
    $('#redeem').on('keyup', function(){
        
        var minimumpointsonredeemfororder = $("#minimumpointsonredeemfororder").val();
        var minimumpointsonredeem = $("#minimumpointsonredeem").val();
        var mimimumpurchaseorderamountforredeem = $("#mimimumpurchaseorderamountforredeem").val();
        var redeempointsforbuyer = $('#redeempointsforbuyer').val();
        var grossamount = $("#inputgrossamount").val();
       
        if($(this).val()!=''){
            var isvalid = false;
            if(parseInt(redeempointsforbuyer) < parseInt(minimumpointsonredeem)){
                $('#notesredeem').html("<i class='fa fa-exclamation-triangle'></i> If "+member_label+" has minimum balance of "+minimumpointsonredeem+" points then only he can redeem points on purchase.");
                $("#redeem_div").addClass("has-error is-focused");
            }else if(parseInt($(this).val()) < parseInt(minimumpointsonredeemfororder)){
                $('#notesredeem').html("<i class='fa fa-exclamation-triangle'></i> If Minimum "+minimumpointsonredeemfororder+" Points Required for Redeem and "+Member_label+" have "+minimumpointsonredeem+" Points Balance then, "+Member_label+" can only redeem "+minimumpointsonredeemfororder+" or more points at the time of purchase process.");
                $("#redeem_div").addClass("has-error is-focused");
            }else if(parseFloat(grossamount) < parseFloat(mimimumpurchaseorderamountforredeem)){
                $('#notesredeem').html("<i class='fa fa-exclamation-triangle'></i> Required Minimum Purchase Order Amount is "+mimimumpurchaseorderamountforredeem+"<br><i class='fa fa-exclamation-triangle'></i> If Purchase Order is less than "+mimimumpurchaseorderamountforredeem+" then "+Member_label+" can not redeem any points.");
                $("#redeem_div").addClass("has-error is-focused");               
            }else{
                $('#notesredeem').html("");
                $("#redeem_div").removeClass("has-error is-focused");
                isvalid = true;
            }
            if(isvalid == false){
                $("#conversationrate").html("0");
                $("#conversationrateamount").html("0.00");
                $("#totalredeempointsforbuyer").val('');
            }
        }else{
            $('#notesredeem').html("");
        }
        changenetamounttotal();
        if(addordertype==0){
            changeextrachargesamount();
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
                deliveryqty += parseInt($(this).val()); 
            }
        });
       
        if(parseInt(deliveryqty) > parseInt(qty)){
            $(this).val(0);
            $(".duplicate").prop("disabled", false);
        }else{
            if(parseInt(deliveryqty)>=parseInt(qty)){
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
                deliveryqty += parseInt($(this).val()); 
            }
        });
        if(parseInt(deliveryqty) > parseInt(qty)){
            $(this).val(0);
            $(".duplicate").prop("disabled", false);
        }else{
            if(parseInt(deliveryqty)>=parseInt(qty)){
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
        changenetamounttotal();
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
                $("#qty"+divid).val(parseFloat(min));
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
            // changeextrachargesamount();
            if($("#applycoupon").text() == "Remove"){
                validatecoupon();
            }
            
            if(partialpayment==1 && EMIreceived==0 && $("#installmentdivs").html()!=""){
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
                            // changeextrachargesamount();
                            if($("#applycoupon").text() == "Remove"){
                                validatecoupon();
                            }
                            
                            if(partialpayment==1 && EMIreceived==0 && $("#installmentdivs").html()!=""){
                                generateinstallment();
                            }
                        }
                    }
                }
            });
        }
    }
}
function displayStockMessage(divid){
    var productstock = $("#priceid"+divid+" option:selected").data("stock");
    if(productstock==undefined){
        $('#ordproductstock'+divid).val('0');
        $('#displaystockmessage'+divid).html('');
    }else{
        if(parseFloat(productstock) > 0){
            $('#ordproductstock'+divid).val(parseFloat(productstock));
            $('#displaystockmessage'+divid).html('<span class="text-primary" style="font-weight: 600;">Stock : '+parseFloat(productstock).toFixed(2)+'</span>');
        }else{
            $('#ordproductstock'+divid).val(0);
            $('#displaystockmessage'+divid).html('<span class="text-danger" style="font-weight: 600;">Sold Out</span>');
        }
    }
}
function calculatediscount(elementid){
    var discountpercentage = $("#discount"+elementid).val(); 
    discountpercentage = (discountpercentage!='' && discountpercentage!=0)?discountpercentage:0;
    var priceid = $("#priceid"+elementid).val();
    var combopriceid = $("#combopriceid"+elementid).val();
    var price = $("#actualprice"+elementid).val();
    price = (price!='' && price!=0)?price:0;
    var qty = $("#qty"+elementid).val();
    qty = (qty!='' && qty!=0)?qty:0;
    
    if(price!=0 && qty!=0 && priceid!="" && combopriceid!="" && discountpercentage!=0){
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
  
    var memberid = (ACTION==1?$("#oldmemberid").val():$("#memberid").val());
    var barcode = $.trim($("#productbarcode").val());
    
    var isvalidbarcode=isvalidmemberid=0;
    PNotify.removeAll();
    if(ACTION==0){
        if(memberid==0){
            $("#member_div").addClass("has-error is-focused");
            new PNotify({title: 'Please select '+member_label+' !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidmemberid=0;
            $("#productbarcode").val('').focus();
        }else{
            $("#member_div").removeClass("has-error is-focused");
            isvalidmemberid=1;
        }
    }else{
        isvalidmemberid=1;
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
   
    if(isvalidbarcode==1 && isvalidmemberid==1){
        var datastr = 'memberid='+memberid+'&barcode='+barcode;
        var baseurl = SITE_URL+'order/getproductdetailsByBarcode';
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
                    var uniqueid = obj['id']+'_'+priceid+'_0.00';
                    
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
                    new PNotify({title: 'Barcode or QR code not match with any '+member_label+' product !',styling: 'fontawesome',delay: '3000',type: 'error'});
                }
            },
            complete: function(){
                $('.mask').hide();
                $('#loader').hide();
            },
        });
    }
    
}
function togglerightbar(){
    if ($('body').hasClass('infobar-overlay')) {
        $('.infobar-wrapper').css('transform','');
    }

    $('body').toggleClass('infobar-active');
    var headerHeight = 64;
    //in layout-boxed pages, toggle visibility instead of animation
    if ($('body').hasClass('layout-boxed')) {
        $('.infobar-wrapper').css('right','0').hide();

        if ($('body').hasClass('layout-boxed')) {
            var $pc = $('#wrapper');
            var ending_right = ($(window).width() - ($pc.offset().left + $pc.outerWidth()));
            if (ending_right<0) ending_right=0;
            $('.infobar-active.infobar-overlay .infobar-wrapper').css('right',ending_right);

            $('.infobar-wrapper').show();
        }
    }
    var scr = $('body').scrollTop();

    if ($('body').hasClass('infobar-overlay')) {
        if ($('body>header, body.horizontal-nav>#wrapper>header').hasClass('navbar-fixed-top')) {
            if ($('body.infobar-overlay').hasClass('infobar-active')) {
                $('.infobar-wrapper').css('transform','translate(0, 72px)');
            }
        } else {
            if ($('body.infobar-overlay').hasClass('infobar-active')) {
                if (scr < headerHeight) {
                    $('.infobar-wrapper').css('transform','translate(0, '+ (72 - scr)+ 'px)');
                } else {
                    $('.infobar-wrapper').css('transform','translate(0, 0)');
                }
            }
        }
    }
}
function updatepoints(divid){

    var price = $("#priceid"+divid).val();
    
    var productwisepoints = $("#inputproductwisepoints").val();
    var sellerproductwisepoints = $("#inputsellerproductwisepoints").val();
    var productwisepointsforbuyer = $("#inputproductwisepointsforbuyer").val();
    var productwisepointsforseller = $("#inputproductwisepointsforseller").val();
    var pointspriority = $("#pointspriority"+divid).val();

    if(pointspriority==1){
        var pointsforbuyer = $("#priceid"+divid+" option:selected").attr("data-pointsforbuyer");
        var pointsforseller = $("#priceid"+divid+" option:selected").attr("data-pointsforseller");
    }else{
        var pointsforbuyer = $("#productid"+divid+" option:selected").attr("data-pointsforbuyer");
        var pointsforseller = $("#productid"+divid+" option:selected").attr("data-pointsforseller");
    }
    if(price != ""){
        if(ACTION==0 && REWARDS_POINTS==1){
            if(productwisepoints==1){
                if(productwisepointsforbuyer==1){
                    $("#inputpointsforbuyer"+divid).val(parseInt(pointsforbuyer));
                    $("#pointsforbuyerwithoutmultiply"+divid).val(parseInt(pointsforbuyer));
                }else{
                    $("#inputpointsforbuyer"+divid).val("0");
                    $("#pointsforbuyerwithoutmultiply"+divid).val("0");
                }
            }else{
                $("#inputpointsforbuyer"+divid).val("0");
                $("#pointsforbuyerwithoutmultiply"+divid).val("0");
            }
            if(sellerproductwisepoints==1){
                if(productwisepointsforseller==1){
                    $("#inputpointsforseller"+divid).val(parseInt(pointsforseller));
                    $("#pointsforsellerwithoutmultiply"+divid).val(parseInt(pointsforseller));
                }else{
                    $("#inputpointsforseller"+divid).val("0");
                    $("#pointsforsellerwithoutmultiply"+divid).val("0");
                }
            }else{
                $("#inputpointsforseller"+divid).val("0");
                $("#pointsforsellerwithoutmultiply"+divid).val("0");
            }
        }
    }else{
        if(pointspriority==1){
            $("#inputpointsforbuyer"+divid).val("0");
            $("#inputpointsforseller"+divid).val("0");
        }
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
                    beforeSend: function(){
                        $('.mask').show();
                        $('#loader').show();
                    },
                    success: function(response){
                        productname = response;
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
    loadpopover();
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
                $('#memberid,#orderid,#deliveryfromdate,#deliverytodate,.tax,.amounttprice').prop('readonly',true);
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
            $('#memberid,#orderid,#deliveryfromdate,#deliverytodate,.tax,.amounttprice').prop('readonly',true);
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
            $(' #memberid').prop('readonly',true);
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
  
    var memberid = $("#memberid").val();
    var BillingAddressID = $("#memberid option:selected").attr("data-billingid");
    var ShippingAddressID = $("#memberid option:selected").attr("data-shippingid");
    $('#displayrewardpoints').html("0");
    var ordertype = $("#ordertype").val();
    if(memberid!=0){
      var uurl = SITE_URL+"order/getBillingAddresstByMemberId";
      if(loadtype==0){
        passdata = {memberid:String(memberid),loadtype:0,ordertype:ordertype};
      }else{
        passdata = {memberid:String(memberid),loadtype:1,ordertype:ordertype};
      }

      $.ajax({
        url: uurl,
        type: 'POST',
        data: passdata,
        //dataType: 'json',
        async: false,
        beforeSend: function(){
            $('.mask').show();
            $('#loader').show();
        },
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
                if(addressid!=0 && (ACTION==1 || addquotationorder==1)){
                    $('#billingaddressid').val(addressid);
                }else if(BillingAddressID!=0 && ACTION==0){
                    $('#billingaddressid').val(BillingAddressID);
                }
                if(shippingaddressid!=0 && (ACTION==1 || addquotationorder==1)){
                    $('#shippingaddressid').val(shippingaddressid);
                }else if(ShippingAddressID!=0 && ACTION==0){
                    $('#shippingaddressid').val(ShippingAddressID);
                }
            }
            if (!jQuery.isEmptyObject(obj['countrewards'])) {
                $('#redeempointsforbuyer').val(obj['countrewards']['rewardpoint']);

                $('#displayrewardpoints').html(obj['countrewards']['rewardpoint']);
            }
            if (!jQuery.isEmptyObject(obj['channeldata'])) {
                if(REWARDS_POINTS==1){
                
                    $("#minimumpointsonredeem").val(parseInt(obj['channeldata']['minimumpointsonredeem']));
                    $("#minimumpointsonredeemfororder").val(parseInt(obj['channeldata']['minimumpointsonredeemfororder']));
                    $("#mimimumpurchaseorderamountforredeem").val(parseInt(obj['channeldata']['mimimumpurchaseorderamountforredeem']));
                    
                    /*var redeempointsforbuyer = $('#redeempointsforbuyer').val();
                    if(parseInt(redeempointsforbuyer) < parseInt(obj['channeldata']['minimumpointsonredeem'])){
                        $('#notesredeem').html("<i class='fa fa-exclamation-triangle'></i> If member has minimum balance of "+obj['channeldata']['minimumpointsonredeem']+" points then only he can redeem points on purchase.");
                        //$('#redeem').prop("readonly",true);
                    }*/
                }
                if(memberid==0 && addordertype==0){
                    $("#minimumpointsonredeem").val("");
                    $("#minimumpointsonredeemfororder").val("");
                    $("#mimimumpurchaseorderamountforredeem").val("");
                }
                $("#channeladvancepaymentcod").val(parseFloat(obj['channeldata']['advancepaymentcodfororder']).toFixed(2));
            }else{
                $("#minimumpointsonredeem").val("");
                $("#minimumpointsonredeemfororder").val("");
                $("#mimimumpurchaseorderamountforredeem").val("");
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
            changenetamounttotal();
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
function applycouponcode(){
    
    var memberid = $("#memberid").val();
    var discountcoupon = $("#discountcoupon").val();
    var grossamount = $("#inputgrossamount").val();
   
    PNotify.removeAll();
    if(addordertype==0){
        if(memberid == 0){
            $("#member_div").addClass("has-error is-focused");
            new PNotify({title: 'Please select '+member_label+' !',styling: 'fontawesome',delay: '3000',type: 'error'});
        }
    }
    if(discountcoupon!="" && grossamount!="" && grossamount!="0.00"){
        validatecoupon(1);
    }else{
        $("#coupondiscountamount").html('0.00');
        $("#couponamount").val("");
        if(discountcoupon == ""){
            $("#discountcoupon_div").addClass("has-error is-focused");
            new PNotify({title: 'Please enter coupon code !',styling: 'fontawesome',delay: '3000',type: 'error'});
        }
        if(grossamount == "" || grossamount == "0.00"){
            new PNotify({title: 'Please add one or more Product !',styling: 'fontawesome',delay: '3000',type: 'error'});
        }
        
    }
}
function removecoupon(){
    $("#coupondiscountamount").html('0.00');
    $("#couponamount").val("");
    $("#discountcoupon").val("");
    $('#applycoupon').removeClass("btn btn-danger btn-raised");
    $('#applycoupon').addClass("btn btn-success btn-raised").html("Apply").attr('onclick','applycouponcode()');

    /* if(addordertype=='1' || addquotationorder==1){
        if(globaldicountper!=''){
            $("#overalldiscountpercent").val(parseFloat(globaldicountper)); 
        }else{
            $("#overalldiscountpercent").val(''); 
        }
        if(globaldicountamount!=''){
            $("#overalldiscountamount").val(parseFloat(globaldicountamount)); 
        }else{
            $("#overalldiscountamount").val(''); 
        }
    }else{
        if(globaldicountper!=''){
            $("#overalldiscountpercent").val(parseFloat(globaldicountper)).prop("readonly",false);
        }else{
            $("#overalldiscountpercent").val('').prop("readonly",false);
        }
        if(globaldicountamount!=''){
            $("#overalldiscountamount").val(parseFloat(globaldicountamount)).prop("readonly",false); 
        }else{
            $("#overalldiscountamount").val('').prop("readonly",false);
        }
    } */
    if(addordertype=='0' || addquotationorder==0){
        $("#overalldiscountpercent").prop("readonly",false);
        $("#overalldiscountamount").prop("readonly",false);
    }
    if(addordertype==0){
        changeextrachargesamount();
    }
    changenetamounttotal();
}
function validatecoupon(type=0){
    
    var memberid = $("#memberid").val();
    var discountcoupon = $("#discountcoupon").val();
    var grossamount = $("#inputgrossamount").val();

    if(discountcoupon!="" && grossamount!="" && grossamount!="0.00"){
        var uurl = SITE_URL+"order/validatecoupon";
        $.ajax({
            url: uurl,
            type: 'POST',
            data: {memberid:String(memberid),discountcoupon:discountcoupon,amount:grossamount,ordertype:addordertype},
            dataType: 'json',
            async: false,
            beforeSend: function(){
                $('.mask').show();
                $('#loader').show();
            },
            success: function(response){
                if(response.result=="fail"){
                    $("#coupondiscountamount").html('0.00');
                    $("#couponamount").val("0");
                    if(type==1){
                        new PNotify({title: response.data,styling: 'fontawesome',delay: '3000',type: 'error'});
                    }
                }else{
                    $("#coupondiscountamount").html(parseFloat(response.data.discountedamount).toFixed(2));
                    $("#couponamount").val(parseFloat(response.data.discountedamount).toFixed(2));
                    $('#applycoupon').removeClass("btn btn-success btn-raised");
                    $('#applycoupon').addClass("btn btn-danger btn-raised").html("Remove").attr('onclick','removecoupon()');
                    $("#overalldiscountpercent").val('').prop("readonly",true);
                    $("#overalldiscountamount").val('').prop("readonly",true);
                    $("#discountamount").html('0.00');
                    $("#discountpercentage").html('0');
                    if(type==1){
                        new PNotify({title: "Your Coupon code is applied.",styling: 'fontawesome',delay: '3000',type: 'success'});
                    }
                }
                if(addordertype==0){
                    changeextrachargesamount();
                }
                changenetamounttotal();
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
        $("#coupondiscountamount").html('0.00');
        $("#couponamount").val("");
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

            $("#pointsforbuyer"+divid).html("0");
            $("#inputpointsforbuyer"+divid).val("0");
            $("#inputpointsforseller"+divid).val("0");
            // $("#memberpointsdiv"+divid).hide();
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
            .val('')
        ;
        $('#priceid'+divid)
            .find('option')
            .remove()
            .end()
            .append('<option value="">Select Variant</option>')
            .val('0')
        ;
        
        $('#productid'+divid).selectpicker('refresh');
        $('#priceid'+divid).selectpicker('refresh');
        $('#priceid'+divid).selectpicker('refresh');

        var element = $('#productid'+divid);
    }
    var memberid = $("#memberid").val();
    
    if(memberid!='' && memberid!=0){
      var uurl = SITE_URL+"product/getProductByCategoryId";
      //salesproducthtml = "";
      
      if(addordertype=='1'){
        memberid = 0;
      }

      var NewProduct = [];

      if(salesproducthtml==''){
          if(ACTION==1){
            withvariantdata = 1;
          }else{
            withvariantdata = 0;
          }
        $.ajax({
            url: uurl,
            type: 'POST',
            data: {memberid:memberid,withvariantdata:withvariantdata},
            dataType: 'json',
            async: false,
            beforeSend: function(){
                $('.mask').show();
                $('#loader').show();
            },
            success: function(response){
      
                
                response.forEach((product) => {
                    /* product.forEach((data) => {
                    }); */
                    var variants = [];
                    if (!$.isEmptyObject(product['variantdata'])) {
                        variants = product['variantdata'];
                    }
                    var offerproductsdata = [];
                    if (!$.isEmptyObject(product['offerproductsdata'])) {
                        offerproductsdata = product['offerproductsdata'];
                    }
                    
                    var productname = product['name'].replace("'","&apos;");
                    if(DROPDOWN_PRODUCT_LIST==0){
                        
                        element.append($('<option>', { 
                            value: product['id'],
                            text : productname,
                            "data-pointsforbuyer" : product['pointsforbuyer'],
                            "data-pointsforseller" : product['pointsforseller'],
                            "data-variants" : JSON.stringify(variants),
                            "data-offerproduct" : JSON.stringify(offerproductsdata),
                        }));
          
                        salesproducthtml += '<option data-pointsforbuyer="'+product['pointsforbuyer']+'" data-pointsforseller="'+product['pointsforseller']+'" value="'+product['id']+'" data-variants="'+(JSON.stringify(variants).replace(/"/g, '&quot;'))+'" data-offerproduct="'+(JSON.stringify(offerproductsdata).replace(/"/g, '&quot;'))+'">'+productname+'</option>';
                    }else{
                        
                        element.append($('<option>', { 
                            value: product['id'],
                            text : productname,
                            "data-pointsforbuyer" : product['pointsforbuyer'],
                            "data-pointsforseller" : product['pointsforseller'],
                            "data-variants" : JSON.stringify(variants),
                            "data-offerproduct" : JSON.stringify(offerproductsdata),
                            "data-content" :'<img src="'+PRODUCT_PATH+product['image']+'" style="width:40px">  ' + productname
                        }));
    
                        salesproducthtml += '<option data-content="<img src=&apos;'+PRODUCT_PATH+product['image']+'&apos; style=&apos;width:40px&apos;>  '+productname+'" data-pointsforbuyer="'+product['pointsforbuyer']+'" data-pointsforseller="'+product['pointsforseller']+'" value="'+product['id']+'" data-variants="'+(JSON.stringify(variants).replace(/"/g, '&quot;'))+'" data-offerproduct="'+(JSON.stringify(offerproductsdata).replace(/"/g, '&quot;'))+'">'+productname+'</option>';
                    }
    
                    NewProduct.push(product['id']);
                });
                
                /* for(var i = 0; i < response.length; i++) {
      
                    var variants = [];
                    if (!$.isEmptyObject(response[i]['variantdata'])) {
                        variants = response[i]['variantdata'];
                    }
                    var offerproductsdata = [];
                    if (!$.isEmptyObject(response[i]['offerproductsdata'])) {
                        offerproductsdata = response[i]['offerproductsdata'];
                    }
                    
                    var productname = response[i]['name'].replace("'","&apos;");
                    if(DROPDOWN_PRODUCT_LIST==0){
                        
                        element.append($('<option>', { 
                            value: response[i]['id'],
                            text : productname,
                            "data-pointsforbuyer" : response[i]['pointsforbuyer'],
                            "data-pointsforseller" : response[i]['pointsforseller'],
                            "data-variants" : JSON.stringify(variants),
                            "data-offerproduct" : JSON.stringify(offerproductsdata),
                        }));
          
                        salesproducthtml += '<option data-pointsforbuyer="'+response[i]['pointsforbuyer']+'" data-pointsforseller="'+response[i]['pointsforseller']+'" value="'+response[i]['id']+'" data-variants="'+(JSON.stringify(variants).replace(/"/g, '&quot;'))+'" data-offerproduct="'+(JSON.stringify(offerproductsdata).replace(/"/g, '&quot;'))+'">'+productname+'</option>';
                    }else{
                        
                        element.append($('<option>', { 
                            value: response[i]['id'],
                            text : productname,
                            "data-pointsforbuyer" : response[i]['pointsforbuyer'],
                            "data-pointsforseller" : response[i]['pointsforseller'],
                            "data-variants" : JSON.stringify(variants),
                            "data-offerproduct" : JSON.stringify(offerproductsdata),
                            "data-content" :'<img src="'+PRODUCT_PATH+response[i]['image']+'" style="width:40px">  ' + productname
                        }));
    
                        salesproducthtml += '<option data-content="<img src=&apos;'+PRODUCT_PATH+response[i]['image']+'&apos; style=&apos;width:40px&apos;>  '+productname+'" data-pointsforbuyer="'+response[i]['pointsforbuyer']+'" data-pointsforseller="'+response[i]['pointsforseller']+'" value="'+response[i]['id']+'" data-variants="'+(JSON.stringify(variants).replace(/"/g, '&quot;'))+'" data-offerproduct="'+(JSON.stringify(offerproductsdata).replace(/"/g, '&quot;'))+'">'+productname+'</option>';
                    }
    
                    NewProduct.push(response[i]['id']);
                } */
                /* for(var i = 0; i < response.length; i++) {
      
                    var variants = [];
                    if (!$.isEmptyObject(response[i]['variantdata'])) {
                        variants = response[i]['variantdata'];
                    }
                    var offerproductsdata = [];
                    if (!$.isEmptyObject(response[i]['offerproductsdata'])) {
                        offerproductsdata = response[i]['offerproductsdata'];
                    }
                    
                    var productname = response[i]['name'].replace("'","&apos;");
                    if(DROPDOWN_PRODUCT_LIST==0){
                        
                        element.append($('<option>', { 
                            value: response[i]['id'],
                            text : productname,
                            "data-pointsforbuyer" : response[i]['pointsforbuyer'],
                            "data-pointsforseller" : response[i]['pointsforseller'],
                            "data-variants" : JSON.stringify(variants),
                            "data-offerproduct" : JSON.stringify(offerproductsdata),
                        }));
          
                        salesproducthtml += '<option data-pointsforbuyer="'+response[i]['pointsforbuyer']+'" data-pointsforseller="'+response[i]['pointsforseller']+'" value="'+response[i]['id']+'" data-variants="'+(JSON.stringify(variants).replace(/"/g, '&quot;'))+'" data-offerproduct="'+(JSON.stringify(offerproductsdata).replace(/"/g, '&quot;'))+'">'+productname+'</option>';
                    }else{
                        
                        element.append($('<option>', { 
                            value: response[i]['id'],
                            text : productname,
                            "data-pointsforbuyer" : response[i]['pointsforbuyer'],
                            "data-pointsforseller" : response[i]['pointsforseller'],
                            "data-variants" : JSON.stringify(variants),
                            "data-offerproduct" : JSON.stringify(offerproductsdata),
                            "data-content" :'<img src="'+PRODUCT_PATH+response[i]['image']+'" style="width:40px">  ' + productname
                        }));
    
                        salesproducthtml += '<option data-content="<img src=&apos;'+PRODUCT_PATH+response[i]['image']+'&apos; style=&apos;width:40px&apos;>  '+productname+'" data-pointsforbuyer="'+response[i]['pointsforbuyer']+'" data-pointsforseller="'+response[i]['pointsforseller']+'" value="'+response[i]['id']+'" data-variants="'+(JSON.stringify(variants).replace(/"/g, '&quot;'))+'" data-offerproduct="'+(JSON.stringify(offerproductsdata).replace(/"/g, '&quot;'))+'">'+productname+'</option>';
                    }
    
                    NewProduct.push(response[i]['id']);
                } */
                
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
                    // $('#applyoldprice'+divid+'_div').remove();
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

                    $("#qty"+divid).val('1');
                    $("#actualprice"+divid).val('');
                    $("#discount"+divid+",#discountinrs"+divid+",#amount"+divid+",#tax"+divid+",#ordertax"+divid+",#uniqueproduct"+divid).val('');
                    $("#displaystockmessage"+divid).html('');
                    $("#ordproductstock"+divid).val('0');
                    // $('#applyoldprice'+divid+'_div').hide();
                }
                changeproductamount(divid);
                if(addordertype==0){
                    changeextrachargesamount();
                }
                $("#discount"+divid+",#discountinrs"+divid).val('');
                if($("#applycoupon").text() == "Remove"){
                    validatecoupon();
                }
                if(firstlevel==0){
                    $("#discount"+divid+",#discountinrs"+divid).val('');
                }
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
            .val('0')
        ;
        $('select.priceid').selectpicker('refresh');
        $(".qty").val('1');
        $(".discount,.discountinrs,.amounttprice,.actualprice").val('');
        $(".displaystockmessage").html('');
        $(".ordproductstock").val('0');
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
                var uurl = SITE_URL+"order/getVariantByProductId";
                var memberid = $("#memberid").val();
                
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
                            .val('0')
                        ;
                        $('#priceid'+divid).selectpicker('refresh');
                        $('#combopriceid'+divid)
                            .find('option')
                            .remove()
                            .end()
                            .append('<option value="">Price</option>')
                            .val('0')
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
                                "data-pointsforbuyer" : productvariant[i]['pointsforbuyer'],
                                "data-pointsforseller" : productvariant[i]['pointsforseller'],
                                "data-id" : productvariant[i]['priceid'],
                                "data-minimumorderqty" : productvariant[i]['minimumorderqty'],
                                "data-maximumorderqty" : productvariant[i]['maximumorderqty'],
                                "data-stock" : productvariant[i]['stock'],
                                /* "data-discount" : response[i]['discount'],
                                "data-discountamount" : response[i]['discountamount'], */
                                "data-pricetype" : productvariant[i]['pricetype'],
                                "data-quantitytype" : productvariant[i]['quantitytype'],
                                "data-referencetype" : productvariant[i]['referencetype'],
                                "data-multipleprices" : JSON.stringify(multiplepricedata),
                                "data-offerproduct" : JSON.stringify(offerproductsdata),
                            }));
                            $('#producttax'+divid).val(productvariant[i]['tax']);
                        }  
                        $('#priceid'+divid).val(priceid);
                        $('#priceid'+divid).selectpicker('refresh');
                        displayStockMessage(divid);
                        getmultiplepricebypriceid(divid);
                        // $('#combopriceid'+divid).val(combopriceid).selectpicker('refresh');
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
                        
                        if($("#applycoupon").text() == "Remove"){
                            validatecoupon();
                        }
                        $("#discount"+divid+",#discountinrs"+divid).val('');
                        
                        if(partialpayment==1 && EMIreceived==0 && $("#installmentdivs").html()!=""){
                            generateinstallment();
                        }
                        if(ACTION==1){
                            addproductondelivery();
                        }
                        updatepoints(divid);
                    }else{
                        // $('#applyoldprice'+divid+'_div').remove();
                    }
                });
                /* $.ajax({
                    url: uurl,
                    type: 'POST',
                    data: {productid:String(productid),ordertype:ordertype,memberid:memberid},
                    dataType: 'json',
                    async: false,
                    beforeSend: function(){
                        $('.mask').show();
                        $('#loader').show();
                    },
                    success: function(response){
                
                    },
                    error: function(xhr) {
                    //alert(xhr.responseText);
                    },
                    complete: function(){
                        $('.mask').hide();
                        $('#loader').hide();
                    },
                }); */
            }
        }     
    }
}
function getProductRewardpoints(divid){

    var productid = $('#productid'+divid).val();
    var memberid = (ACTION==1)?$('#oldmemberid').val():$('#memberid').val();

    if(productid!=0){
      var uurl = SITE_URL+"product/getProductRewardpoints";
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {productid:String(productid),memberid:String(memberid),ordertype:addordertype},
        dataType: 'json',
        async: false,
        beforeSend: function(){
            $('.mask').show();
            $('#loader').show();
        },
        success: function(response){
            if(REWARDS_POINTS==1){
                if(ACTION==0){
                    if(response.productwisepoints==1){
                        if(response.productwisepointsforbuyer==1){
                            $("#pointsforbuyer"+divid).html(parseInt(response.pointsforbuyer));
                            $("#inputpointsforbuyer"+divid).val(parseInt(response.pointsforbuyer));
                            $("#pointsforbuyerwithoutmultiply"+divid).val(parseInt(response.pointsforbuyer));
                            $("#inputproductwisepointsforbuyer").val(parseInt(response.productwisepointsforbuyer));
                            /* if(parseInt(response.pointsforbuyer)>0 && response.pointspriority == 0){
                                $("#memberpointsdiv"+divid).show();
                            }else{
                                $("#memberpointsdiv"+divid).hide();
                            } */
                        }else{
                            $("#pointsforbuyer"+divid).html("0");
                            $("#inputpointsforbuyer"+divid).val("0");
                            $("#pointsforbuyerwithoutmultiply"+divid).val("0");
                            $("#inputproductwisepointsforbuyer").val("0");
                        }
                        $("#inputproductwisepoints").val(parseInt(response.productwisepoints));
                        $("#multiplypointswithqty").val(response.productwisepointsmultiplywithqty); 
                        $("#trconversationrate").show();
                        //$("#conversationrate").val(parseInt(response.conversationrate));
                    }else{
                        $("#pointsforbuyer"+divid).html("0");
                        $("#inputpointsforbuyer"+divid).val("0");
                        $("#inputpointsforseller"+divid).val("0");
                        // $("#memberpointsdiv"+divid).hide();

                        $("#inputproductwisepoints").val("0");
                        $("#trconversationrate").hide();
                    }
                    if(response.sellerproductwisepoints==1){
                        if(response.productwisepointsforseller==1){
                            $("#inputpointsforseller"+divid).val(parseInt(response.pointsforseller));
                            $("#pointsforsellerwithoutmultiply"+divid).val(parseInt(response.pointsforseller));
                            $("#inputproductwisepointsforseller").val(parseInt(response.productwisepointsforseller));
                        }else{
                            $("#inputpointsforseller"+divid).val("0");
                            $("#pointsforsellerwithoutmultiply"+divid).val("0");
                            $("#inputproductwisepointsforseller").val("0");
                        }
                        
                        $("#inputsellerproductwisepoints").val(parseInt(response.sellerproductwisepoints));
                        $("#sellermultiplypointswithqty").val(response.sellerproductwisepointsmultiplywithqty); 
                        $("#trconversationrate").show();
                    }else{
                        $("#inputpointsforseller"+divid).val("0");
                        $("#inputsellerproductwisepoints").val("0");
                        $("#trconversationrate").hide();
                        // $("#memberpointsdiv"+divid).hide();
                    }
                    if(response.overallproductpoints==1){
                        $("#overallproductpoints").val(parseInt(response.overallproductpoints));
                        $("#buyerpointsforoverallproduct").val(parseInt(response.buyerpointsforoverallproduct));
                        $("#mimimumorderqtyforoverallproduct").val(parseInt(response.mimimumorderqtyforoverallproduct));
                        $("#trconversationrate").show();
                    }else{
                        $("#overallproductpoints").val("0");
                        $("#buyerpointsforoverallproduct").val("");
                        $("#mimimumorderqtyforoverallproduct").val("");
                        $("#trconversationrate").hide();
                    }
                    if(response.selleroverallproductpoints==1){
                        $("#selleroverallproductpoints").val(parseInt(response.selleroverallproductpoints));
                        $("#sellerpointsforoverallproduct").val(parseInt(response.sellerpointsforoverallproduct));
                        $("#sellermimimumorderqtyforoverallproduct").val(parseInt(response.sellermimimumorderqtyforoverallproduct));
                        $("#trconversationrate").show();
                    }else{
                        $("#selleroverallproductpoints").val("0");
                        $("#sellerpointsforoverallproduct").val("");
                        $("#sellermimimumorderqtyforoverallproduct").val("");
                        $("#trconversationrate").hide();
                    }
                    if(response.pointsonsalesorder==1){
                        $("#pointsonsalesorder").val(parseInt(response.pointsonsalesorder));
                        $("#buyerpointsforsalesorder").val(parseInt(response.buyerpointsforsalesorder));
                        $("#mimimumorderamountforsalesorder").val(parseInt(response.mimimumorderamountforsalesorder));
                        $("#trconversationrate").show();
                    }else{
                        $("#pointsonsalesorder").val("0");
                        $("#buyerpointsforsalesorder").val("");
                        $("#mimimumorderamountforsalesorder").val("");
                        $("#trconversationrate").hide();
                    }
                    if(response.sellerpointsonsalesorder==1){
                        $("#sellerpointsonsalesorder").val(parseInt(response.sellerpointsonsalesorder));
                        $("#sellerpointsforsalesorder").val(parseInt(response.sellerpointsforsalesorder));
                        $("#sellermimimumorderamountforsalesorder").val(parseInt(response.sellermimimumorderamountforsalesorder));
                        $("#trconversationrate").show();
                    }else{
                        $("#sellerpointsonsalesorder").val("0");
                        $("#sellerpointsforsalesorder").val("");
                        $("#sellermimimumorderamountforsalesorder").val("");
                        $("#trconversationrate").hide();
                    }
                    $("#pointspriority"+divid).val(parseInt(response.pointspriority));
                }
                $('#minimumpointsonredeem').val(response.minimumpointsonredeem);
                $('#minimumpointsonredeemfororder').val(response.minimumpointsonredeemfororder);
                $('#mimimumpurchaseorderamountforredeem').val(response.mimimumpurchaseorderamountforredeem);
                
                $("#inputconversationrate").val(parseInt(response.conversationrate));
                $("#referrerconversationrate").val(parseInt(response.sellerconversationrate));
            }else if(ACTION==1){
                $("#inputconversationrate").val(parseInt(response.conversationrate));
                $("#referrerconversationrate").val(parseInt(response.sellerconversationrate));
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
        $("#pointsforbuyer"+divid).html("0");
        $("#inputpointsforbuyer"+divid).val("0");
        $("#inputpointsforseller"+divid).val("0");
        // $("#memberpointsdiv"+divid).hide();
    }

}
function getChannelSettingByMember(){

    var memberid = (ACTION==1)?$('#oldmemberid').val():$('#memberid').val();

    if(memberid!='' && memberid!=0){
        var uurl = SITE_URL+"member/getChannelSettingsByMember";
        $.ajax({
          url: uurl,
          type: 'POST',
          data: {memberid:String(memberid), ordertype:addordertype},
          dataType: 'json',
          async: false,
          beforeSend: function(){
            $('.mask').show();
            $('#loader').show();
          },
          success: function(response){
            if(memberid!=0 && response.edittaxrate==1 && EDITTAXRATE_SYSTEM==1){
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
          complete: function(){
            $('.mask').hide();
            $('#loader').hide();
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
    
    if(productid!=0){

        var productvariant = JSON.parse($("#productid"+divid+" option:selected").attr("data-variants"));
        var len = productvariant.length;

        if(len>0){
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
                    "data-pointsforbuyer" : productvariant[i]['pointsforbuyer'],
                    "data-pointsforseller" : productvariant[i]['pointsforseller'],
                    "data-id" : productvariant[i]['priceid'],
                    "data-minimumorderqty" : productvariant[i]['minimumorderqty'],
                    "data-maximumorderqty" : productvariant[i]['maximumorderqty'],
                    "data-stock" : productvariant[i]['stock'],
                    "data-pricetype" : productvariant[i]['pricetype'],
                    "data-quantitytype" : productvariant[i]['quantitytype'],
                    "data-referencetype" : productvariant[i]['referencetype'],
                    "data-multipleprices" : JSON.stringify(multiplepricedata),
                    "data-offerproduct" : JSON.stringify(offerproductsdata),
                }));
    
                $('#producttax'+divid).val(productvariant[i]['tax']);
            }
            
            if(len==1){
                $('#priceid'+divid).val(productvariant[0]['id']).selectpicker('refresh');
                $('#priceid'+divid).change();
            }
            if((addquotationorder==1 || ACTION==1) && oldpriceid[divid-1]!="undefined" && $('#productid'+divid).val()==oldproductid[divid-1]){
                $('#priceid'+divid).val(oldpriceid[divid-1]).selectpicker('refresh').change();
                displayStockMessage(divid);
            }
    
            if(addquotationorder==1 || ACTION==1){
                var actualprice = parseFloat($('#actualprice'+divid).val()).toFixed(2);
                
                if(parseFloat(actualprice) == parseFloat($('#oldpricewithtax'+divid).html())){
                    $('#applyoldprice'+divid+'_div').hide();
                    $('#applyoldprice'+divid).prop("checked",false);
                }else{
                    $('#applyoldprice'+divid+'_div').show();
                }
            }
            
            if(ACTION==1 && oldtax[divid-1]>=0 && $('#priceid'+divid).val()==oldpriceid[divid-1] && $('#productid'+divid).val()==oldproductid[divid-1] && $('#tax'+divid).val()!=""){
                $('#tax'+divid+',#ordertax'+divid).val(oldtax[divid-1]);
            }else{
                var tax = (productvariant.length > 0)?productvariant[0]['tax']:0;
                $('#tax'+divid+',#ordertax'+divid).val(tax);
            }
            var productid = $("select[name='productid[]']").map(function(){return $(this).val();}).get();
    
            if(productid[productid.length-1]!=0){
                addnewproduct();
            }
        }else{
            var uurl = SITE_URL+"order/getVariantByProductId";
            var memberid = (ACTION==1)?$('#oldmemberid').val():$('#memberid').val();
            
            if(addordertype=='1'){
                memberid = 0;
            }
            $.ajax({
                url: uurl,
                type: 'POST',
                data: {productid:String(productid),ordertype:ordertype,memberid:memberid},
                dataType: 'json',
                async: false,
                beforeSend: function(){
                    $('.mask').show();
                    $('#loader').show();
                },
                success: function(response){
                    var len = response.length;
                    var productvariant = response;
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
                            "data-pointsforbuyer" : productvariant[i]['pointsforbuyer'],
                            "data-pointsforseller" : productvariant[i]['pointsforseller'],
                            "data-id" : productvariant[i]['priceid'],
                            "data-minimumorderqty" : productvariant[i]['minimumorderqty'],
                            "data-maximumorderqty" : productvariant[i]['maximumorderqty'],
                            "data-stock" : productvariant[i]['stock'],
                            "data-pricetype" : productvariant[i]['pricetype'],
                            "data-quantitytype" : productvariant[i]['quantitytype'],
                            "data-referencetype" : productvariant[i]['referencetype'],
                            "data-multipleprices" : JSON.stringify(multiplepricedata),
                            "data-offerproduct" : JSON.stringify(offerproductsdata),
                        }));
            
                        $('#producttax'+divid).val(productvariant[i]['tax']);
                    }
                
                    if(len==1){
                        $('#priceid'+divid).val(productvariant[0]['id']).selectpicker('refresh');
                        $('#priceid'+divid).change();
                    }
                    if((addquotationorder==1 || ACTION==1) && oldpriceid[divid-1]!="undefined" && $('#productid'+divid).val()==oldproductid[divid-1]){
                        $('#priceid'+divid).val(oldpriceid[divid-1]).selectpicker('refresh').change();
                        displayStockMessage(divid);
                    }
            
                    if(addquotationorder==1 || ACTION==1){
                        var actualprice = parseFloat($('#actualprice'+divid).val()).toFixed(2);
                        
                        if(parseFloat(actualprice) == parseFloat($('#oldpricewithtax'+divid).html())){
                            $('#applyoldprice'+divid+'_div').hide();
                            $('#applyoldprice'+divid).prop("checked",false);
                        }else{
                            $('#applyoldprice'+divid+'_div').show();
                        }
                    }
                    
                    if(ACTION==1 && oldtax[divid-1]>=0 && $('#priceid'+divid).val()==oldpriceid[divid-1] && $('#productid'+divid).val()==oldproductid[divid-1] && $('#tax'+divid).val()!=""){
                        $('#tax'+divid+',#ordertax'+divid).val(oldtax[divid-1]);
                    }else{
                        var tax = (productvariant.length > 0)?productvariant[0]['tax']:0;
                        $('#tax'+divid+',#ordertax'+divid).val(tax);
                    }
                    var productid = $("select[name='productid[]']").map(function(){return $(this).val();}).get();
            
                    if(productid[productid.length-1]!=0){
                        addnewproduct();
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
        var uurl = SITE_URL+"order/getMultiplePriceByPriceIdOrMemberId";
        var memberid = (ACTION==1)?$('#oldmemberid').val():$('#memberid').val();
        var productpriceid = $("#priceid"+divid+" option:selected").attr("data-id");
        var pricetype = $("#priceid"+divid+" option:selected").attr("data-pricetype");
        var quantitytype = $("#priceid"+divid+" option:selected").attr("data-quantitytype");

        if(parseInt(pricetype)==1 && parseInt(quantitytype)==1){
            $("#qty"+divid).prop("readonly",true);
        }else{
            $("#qty"+divid).prop("readonly",false);
        }
        var multipleprices = JSON.parse($("#priceid"+divid+" option:selected").attr("data-multipleprices"));
        var length = multipleprices.length;
        for(var i = 0; i < multipleprices.length; i++) {
            
            var txt = "";

            if(parseInt(pricetype)==1){
                txt = CURRENCY_CODE+multipleprices[i]['price']+" "+multipleprices[i]['quantity']+(parseInt(quantitytype)==0?"+":"")+" Qty"
            }else{
                txt = multipleprices[i]['price'];
            }
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
        if(ACTION==1 && oldcombopriceid[divid-1]!="undefined" && $('#combopriceid'+divid).val()==""){
            $('#combopriceid'+divid).val(oldcombopriceid[divid-1]).selectpicker('refresh').change();

           if(productid==oldproductid[divid-1] && priceid==oldpriceid[divid-1]){
               var quantity = $("#combopriceid"+divid+" option:selected").attr("data-quantity");
               
               if(parseInt(quantitytype)==1 && parseInt(pricetype)==1){
                   $("#qty"+divid).trigger("touchspin.updatesettings", {min: parseInt(quantity), step: parseInt(quantity)});
               }else{
                   $("#qty"+divid).trigger("touchspin.updatesettings", {min: 1, step: 1});
               }
           }
        }
        /* $.ajax({
            url: uurl,
            type: 'POST',
            data: {productid:productid,priceid:String(productpriceid),memberid:memberid},
            dataType: 'json',
            async: false,
            beforeSend: function(){
                $('.mask').show();
                $('#loader').show();
            },
            success: function(response){

            },
            error: function(xhr) {
            //alert(xhr.responseText);
            },
            complete: function(){
                $('.mask').hide();
                $('#loader').hide();
            },
        }); */
    }
    $('#combopriceid'+divid).selectpicker('refresh');
}
function geProductFIFOStock(divid){
    var priceid = $("#priceid"+divid).val();
    var productid = $("#productid"+divid).val();
    if(priceid!=""){
        var uurl = SITE_URL+"order/geProductFIFOStock";
        var memberid = (ACTION==1)?$('#oldmemberid').val():$('#memberid').val();
        var productpriceid = $("#priceid"+divid+" option:selected").attr("data-id");

        $.ajax({
            url: uurl,
            type: 'POST',
            data: {productid:productid,priceid:String(productpriceid)},
            dataType: 'json',
            async: false,
            beforeSend: function(){
                $('.mask').show();
                $('#loader').show();
            },
            success: function(response){

                if(response.length > 0){
                    $("#fifoproducts"+divid).val(JSON.stringify(response));
                }else{
                    $("#fifoproducts"+divid).val("");
                }
                setfinalproductqty(divid);
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
}
function setfinalproductqty(divid){
    var qty = $("#qty"+divid).val();
    
    var stockqtyarray = [];
    var fifoproducts = $("#fifoproducts"+divid).val();
    
    if(fifoproducts!=""){
        var productarr = JSON.parse(fifoproducts); 
        var key = 0;
        var stockqty = 0;
        for(var i = 0; i < productarr.length; i++) {
            var mappingid = productarr[i]['transactionproductstockmappingid'];
            var orderqty = productarr[i]['qty'];
            var orderprice = productarr[i]['fifoprice'];
            var stocktype = productarr[i]['stocktype'];
            var stocktypeid = productarr[i]['stocktypeid'];

            stockqty = stockqty + parseFloat(orderqty);
            if(parseFloat(qty) > 0){
                
                if(parseFloat(orderqty) < parseFloat(qty)){
                    qty = parseFloat(qty) - parseFloat(orderqty);
                    stockqtyarray[key] = {'mappingid':mappingid,'qty':parseInt(orderqty),'price':parseFloat(orderprice),'stocktype':stocktype,'stocktypeid':stocktypeid};
                }else if(parseFloat(orderqty) >= parseFloat(qty)){
                    stockqtyarray[key] = {'mappingid':mappingid,'qty':parseInt(qty),'price':parseFloat(orderprice),'stocktype':stocktype,'stocktypeid':stocktypeid};
                    qty = 0;
                }
                key++;
            }
        }
        $("#priceid"+divid+" option:selected").data('stock',stockqty);
        if(parseFloat(qty) > 0){
            stockqtyarray[key] = {'qty':parseInt(qty),'price':parseFloat($("#originalprice"+divid).val()),'stocktype':1,'stocktypeid':0};
        }
    }
    if(stockqtyarray.length > 0){
        if(ACTION==0){
            $("#actualprice"+divid).val(parseFloat(stockqtyarray[0]['price']).toFixed(2));
        }
        $("#finalfifoproducts"+divid).val(JSON.stringify(stockqtyarray));
    }
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
function addnewproduct(){

    if(addordertype==0){
        productoptionhtml = salesproducthtml;
    } 
    if(PRODUCT_DISCOUNT==0){
        discount = "display:none;";
    }else{ 
        discount = ""; 
    }
    var readonly = "readonly";
    if(EDITTAXRATE_CHANNEL==1 && EDITTAXRATE_SYSTEM==1){
        readonly = "";
    }

    divcount = parseInt($(".amounttprice:last").attr("div-id"))+1;
    var allowdiscount = '';
    if(addordertype==1){
        allowdiscount = 'readonly';
        editrate = "display:none;";
    }else{
        editrate = "display:block;"; 
    }

    if(REWARDS_POINTS==1){
        var memberpointshtml = '<div class="col-sm-12 memberpointsdiv" id="memberpointsdiv'+divcount+'" style="display:none;">\
                                    <label class="control-label">Points for Buyer : <span id="pointsforbuyer'+divcount+'" class="pointsforbuyer">0</span></label>\
                                    <input type="hidden" name="inputpointsforbuyer[]" id="inputpointsforbuyer'+divcount+'" class="inputpointsforbuyer" div-id="'+divcount+'">\
                                    <input type="hidden" name="pointsforbuyerwithoutmultiply[]" id="pointsforbuyerwithoutmultiply'+divcount+'" class="pointsforbuyerwithoutmultiply" div-id="'+divcount+'">\
                                    <input type="hidden" name="inputpointsforseller[]" id="inputpointsforseller'+divcount+'" class="inputpointsforseller" div-id="'+divcount+'">\
                                    <input type="hidden" name="pointsforsellerwithoutmultiply[]" id="pointsforsellerwithoutmultiply'+divcount+'" class="pointsforsellerwithoutmultiply" div-id="'+divcount+'">\
                                    <input type="hidden" name="pointspriority[]" id="pointspriority'+divcount+'" class="pointspriority" div-id="'+divcount+'">\
                                </div>';
    }else{
        var memberpointshtml = '';
    }

    producthtml = '<tr class="countproducts" id="orderproductdiv'+divcount+'">\
                    <td>\
                        <input type="hidden" name="producttax[]" id="producttax'+divcount+'">\
                        <input type="hidden" name="productrate[]" id="productrate'+divcount+'">\
                        <input type="hidden" name="originalprice[]" id="originalprice'+divcount+'">\
                        <input type="hidden" name="uniqueproduct[]" id="uniqueproduct'+divcount+'">\
                        <input type="hidden" name="offerid[]" id="offerid'+divcount+'">\
                        <input type="hidden" name="offerproductid[]" id="offerproductid'+divcount+'">\
                        <input type="hidden" name="brandoffer[]" id="brandoffer'+divcount+'">\
                        <input type="hidden" name="referencetype[]" id="referencetype'+divcount+'">\
                        <textarea style="display:none;" id="fifoproducts'+divcount+'"></textarea>\
                        <textarea style="display:none;" name="finalfifoproducts[]" id="finalfifoproducts'+divcount+'"></textarea>\
                        <div class="form-group" id="serialno'+divcount+'_div">\
                            <div class="col-sm-12">\
                                <input id="serialno'+divcount+'" type="text" name="serialno[]" class="form-control">\
                            </div>\
                        </div>\
                    </td>\
                    <td>\
                        <div class="form-group" id="product'+divcount+'_div">\
                            <div class="col-sm-12">\
                                <select id="productid'+divcount+'" name="productid[]" data-width="90%" class="selectpicker form-control productid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="'+divcount+'">\
                                    <option value="0">Select Product</option>\
                                    '+productoptionhtml+'\
                                </select>\
                            </div>\
                            <div class="col-sm-12 dropdown trigger-infobar" id="trigger-infobar'+divcount+'" style="display:none;"> \
                                <a class="dropdown-toggle btn-available-offers" data-toggle="dropdown" href="javascript:void(0)"><i class="material-icons" style="font-size: 20px;">add_circle_outline</i>&nbsp;Available Offers</a> \
                            </div> \
                            '+memberpointshtml+'\
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
                                <input type="hidden" id="ordproductstock'+divcount+'" name="ordproductstock[]" class="ordproductstock" value="0">\
                            </div>\
                            <div class="col-sm-12 text-right displaystockmessage" id="displaystockmessage'+divcount+'"></div>\
                        </div>\
                    </td>\
                    <td>\
                        <div class="form-group" id="qty'+divcount+'_div">\
                            <div class="col-md-12">\
                                <input type="text" class="form-control qty" id="qty'+divcount+'" name="qty[]" value="" onkeypress="'+(MANAGE_DECIMAL_QTY==1?"return decimal_number_validation(event, this.value,8);":"return isNumber(event);")+'" style="display: block;" div-id="'+divcount+'">\
                                <input type="hidden" value="0" id="purchaseproductqty'+divcount+'"> \
                            </div>\
                        </div>\
                    </td>\
                    <td style="'+discount+'">\
                        <div class="form-group" id="discount'+divcount+'_div">\
                            <div class="col-md-12">\
                                <label for="discount'+divcount+'" class="control-label">Dis. (%)</label> \
                                <input type="text" class="form-control discount" id="discount'+divcount+'" name="discount[]" value="" div-id="'+divcount+'" onkeypress="return decimal_number_validation(event, this.value)" '+allowdiscount+'>	\
                                <input type="hidden" value="" id="orderdiscount'+divcount+'">\
                            </div>\
                        </div>\
                        <div class="form-group" id="discountinrs'+divcount+'_div">\
                            <div class="col-md-12">\
                                <label for="discountinrs'+divcount+'" class="control-label">Dis. ('+CURRENCY_CODE+')</label> \
                                <input type="text" class="form-control discountinrs" id="discountinrs'+divcount+'" name="discountinrs[]" div-id="'+divcount+'" onkeypress="return decimal_number_validation(event, this.value)" '+allowdiscount+'>\
                            </div>\
                        </div>\
                    </td>\
                    <td>\
                        <div class="form-group" id="tax'+divcount+'_div">\
                            <div class="col-md-12">\
                                <input type="text" class="form-control text-right tax" id="tax'+divcount+'" name="tax[]" div-id="'+divcount+'" '+readonly+'>	\
                                <input type="hidden" id="ordertax'+divcount+'">\
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
                            <div class="col-sm-12 pr-n">\
                                <button type="button" class="btn btn-default btn-raised add_remove_btn_product" onclick="removeproduct('+divcount+')" style="padding: 5px 10px;"> <i class="fa fa-minus"></i></button> \
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
    validatedoffergrid();
    changeproductamount(divid);
    changeextrachargesamount();
    validatecoupon();
    if(partialpayment==1 && EMIreceived==0 && $("#installmentdivs").html()!=""){
        generateinstallment();
    }
    
    disabledform();
    addproductondelivery();
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
            var productrate = parseFloat(price).toFixed(2);
        }else{
            var productrate = parseFloat(parseFloat(price) - ((parseFloat(price) * parseFloat(tax) /(100+parseFloat(tax))))).toFixed(2);
        }   */
        if(combopriceid!=0 && actualprice!=0 && qty!="0" && price!="" && qty!="" && price!="Select Variant"){
            totalamount = productamount = discountamount = 0;
            // if((discount!='0' && discount!="" && productdiscount[divid-1]!=0 && PRODUCT_DISCOUNT == 0) || (discount!='0' && discount!="" && PRODUCT_DISCOUNT == 1)){
            if(PRODUCT_DISCOUNT == 1 && discount!='0' && discount!=""){
                discountamount = (parseFloat(actualprice)*(parseFloat(discount)/100));
            }
            price = parseFloat(parseFloat(actualprice) - parseFloat(discountamount)).toFixed(2);
            var productrate = parseFloat(price);
            if((addquotationorder==1 || ACTION==1) && $("input[id=applyoldprice"+divid+"]").is(":checked") && $('#applyoldprice'+divid+'_div').is(':visible')){
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
            if(addordertype==0){
                $("#productrate"+divid).val(parseFloat(productrate).toFixed(2));
            }
            
            /* if(addordertype==0){
                if((addquotationorder==1 || ACTION==1) && $("input[id=applyoldprice"+divid+"]").is(":checked")){
                    if(GST_PRICE == 1){
                        productrate = parseFloat(orderprice);
                    }else{
                        productrate = (parseFloat(orderprice) - (parseFloat(orderprice) * parseFloat(ordertax) / (100+parseFloat(ordertax))));
                    }
                }
                price = parseFloat(parseFloat(productrate) + (parseFloat(productrate) * parseFloat(edittax) / 100)).toFixed(2);
            }else{
                if((addquotationorder==1 || ACTION==1) && $("input[id=applyoldprice"+divid+"]").is(":checked")){
                    if(GST_PRICE == 1){
                        productrate = parseFloat(orderprice);
                    }else{
                        productrate = (parseFloat(orderprice) - (parseFloat(orderprice) * parseFloat(tax) / (100+parseFloat(tax))));
                    }
                }
                price = parseFloat(parseFloat(productrate)).toFixed(2);
            }
            if(discount!='0' && discount!="" && productdiscount[divid-1]!=0 && PRODUCT_DISCOUNT == 0){
                discountamount = (parseFloat(price)*(parseFloat(discount)/100));
            }else if(discount!='0' && discount!="" && PRODUCT_DISCOUNT == 1){
                discountamount = (parseFloat(price)*(parseFloat(discount)/100));
            } 
            productamount = parseFloat(price)-parseFloat(discountamount);
            totalamount = parseFloat(productamount) * parseFloat(qty);
            if(addordertype==0){
                producttaxamount = (parseFloat(totalamount) * parseFloat(edittax) / (100 + parseFloat(edittax)));
                $("#productrate"+divid).val(parseFloat(price - (parseFloat(price) * parseFloat(edittax) / (100 + parseFloat(edittax)))).toFixed(2));
            }else{
                producttaxamount = (parseFloat(totalamount) * parseFloat(tax) / 100);
            }*/
            $("#amount"+divid).val(parseFloat(totalamount).toFixed(2));
            $('#producttaxamount'+divid).val(parseFloat(producttaxamount));
            
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
    var couponamount = $("#coupondiscountamount").html();
    var conversationrateamount = $("#conversationrateamount").html();

    var totalgrossamount = parseFloat(grossamount) - parseFloat(discount) - parseFloat(couponamount) - parseFloat(conversationrateamount);
    
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
function changenetamounttotal(calcdiscount=0){
    
    var productgstamount = chargesassesbaleamount = extrachargesamount = extrachargestax = grossamount = 0;
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
    $(".amounttprice").each(function( index ) {
        if($(this).val()!=""){
            grossamount += parseFloat($(this).val());
        }
    });
    $('input[name="postofferamount[]"]').each(function( index ) {
        if($(this).val()!=""){
            grossamount += parseFloat($(this).val());
        }
    });
    if(addordertype==0){
        grossamount = grossamount - productgstamount;
    }
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

    var couponamount = $("#coupondiscountamount").html();
    $('#trconversationrate').hide();
    
    if(parseFloat(couponamount)!=""){
        couponamount = parseFloat(couponamount).toFixed(2);
    }else{
        couponamount = 0;
    }
    var inputconversationrateamount = 0;
    if(REWARDS_POINTS==1 && grossamount!=0 && ACTION==0){
        
        var redeem = $("#redeem").val();
        var inputconversationrate = $("#inputconversationrate").val();
        var minimumpointsonredeem = $("#minimumpointsonredeem").val();
        var minimumpointsonredeemfororder = $("#minimumpointsonredeemfororder").val();
        var mimimumpurchaseorderamountforredeem = $("#mimimumpurchaseorderamountforredeem").val();
        var redeempointsforbuyer = $('#redeempointsforbuyer').val();

        if(parseInt(redeempointsforbuyer) >= parseInt(minimumpointsonredeem)){
            if(parseInt(redeem) >= parseInt(minimumpointsonredeemfororder)){
                if(parseFloat(grossamount) >= parseFloat(mimimumpurchaseorderamountforredeem)){
                    
                    inputconversationrateamount = parseInt(redeem) * parseInt(inputconversationrate);
                    $("#conversationrate").html(parseInt(redeem)+"*"+parseInt(inputconversationrate));
                    $("#conversationrateamount").html(parseFloat(inputconversationrateamount).toFixed(2));
                    $("#totalredeempointsforbuyer").val(parseInt(redeem));
                    $('#notesredeem').html("");
                    $("#redeem_div").removeClass("has-error is-focused");
                    $('#trconversationrate').show();
                }else{
                    if(redeem!=''){
                        $('#notesredeem').html("<i class='fa fa-exclamation-triangle'></i> Required Minimum Purchase Order Amount is "+mimimumpurchaseorderamountforredeem+"<br><i class='fa fa-exclamation-triangle'></i> If Purchase Order is less than "+mimimumpurchaseorderamountforredeem+" then "+Member_label+" can not redeem any points.");
                        $("#redeem_div").addClass("has-error is-focused");
                    }
                    $("#conversationrate").html("0");
                    $("#conversationrateamount").html("0.00");
                    $("#totalredeempointsforbuyer").val('');
                }
            }else{
                if(redeem!=''){
                    $('#notesredeem').html("<i class='fa fa-exclamation-triangle'></i> If Minimum "+minimumpointsonredeemfororder+" Points Required for Redeem and "+Member_label+" have "+minimumpointsonredeem+" Points Balance then, "+Member_label+" can only redeem "+minimumpointsonredeemfororder+" or more points at the time of purchase process.");
                    $("#redeem_div").addClass("has-error is-focused");
                }
                $("#conversationrate").html("0");
                $("#conversationrateamount").html("0.00");
                $("#totalredeempointsforbuyer").val('');
            }
        }else{
            if(redeem!=''){
                $('#notesredeem').html("<i class='fa fa-exclamation-triangle'></i> If "+member_label+" has minimum balance of "+minimumpointsonredeem+" points then only he can redeem points on purchase.");
                $("#redeem_div").addClass("has-error is-focused");
            }
            $("#conversationrate").html("0");
            $("#conversationrateamount").html("0.00");
            $("#totalredeempointsforbuyer").val('');
        }

        var productwisepoints = $("#inputproductwisepoints").val();
        var sellerproductwisepoints = $("#inputsellerproductwisepoints").val();
        var overallproductpoints = $("#overallproductpoints").val();
        var selleroverallproductpoints = $("#selleroverallproductpoints").val();
        var pointsonsalesorder = $("#pointsonsalesorder").val();
        var sellerpointsonsalesorder = $("#sellerpointsonsalesorder").val();
        var productwisepointsforbuyer = $("#inputproductwisepointsforbuyer").val();
        var productwisepointsforseller = $("#inputproductwisepointsforseller").val();
        
        if(productwisepoints==1){
            if(productwisepointsforbuyer==1){
                var pointsforbuyer = 0;
                $(".inputpointsforbuyer").each(function( index ) {
                    var divid = $(this).attr("div-id");
                    if($(this).val()!="" && $("#qty"+divid).val() >0 && $("#productid"+divid).val() >0 ){
                        pointsforbuyer += parseInt($(this).val());
                    }
                });
                
                $("#totalpointsforbuyer").val(parseInt(pointsforbuyer));
            }else{
                $("#totalpointsforbuyer").val("0");
            }
        }
        if(sellerproductwisepoints==1){
            if(productwisepointsforseller==1){
                var pointsforseller = 0;
                $(".inputpointsforseller").each(function( index ) {
                    var divid = $(this).attr("div-id");
                    if($(this).val()!="" && $("#qty"+divid).val() >0 && $("#productid"+divid).val() >0 ){
                        pointsforseller += parseInt($(this).val());
                    }
                });
                
                $("#totalpointsforseller").val(parseInt(pointsforseller));
            }else{
                $("#totalpointsforseller").val("0");
            }
        }
        var totalquantity = 0;
        $(".qty").each(function( index ) {
            var divid = $(this).attr("div-id");
            if($(this).val()!="" && $("#qty"+divid).val() >0 && $("#productid"+divid).val() >0){
                totalquantity += parseFloat($(this).val());
            }
        });
        if(overallproductpoints==1){
            var mimimumorderqtyforoverallproduct = $("#mimimumorderqtyforoverallproduct").val();
            if(mimimumorderqtyforoverallproduct!=''){
                var buyerpointsforoverallproduct = $("#buyerpointsforoverallproduct").val();
                
                if(totalquantity >= mimimumorderqtyforoverallproduct){
                    var pointsforbuyer = 0;
                    if(productwisepoints==1){
                        $(".inputpointsforbuyer").each(function( index ) {
                            var divid = $(this).attr("div-id");
                            if($(this).val()!="" && $("#qty"+divid).val() >0 && $("#productid"+divid).val() >0 ){
                                pointsforbuyer += parseInt($(this).val());
                            }
                        });
                    }
                    pointsforbuyer = parseInt(pointsforbuyer) + parseInt(buyerpointsforoverallproduct);
                    $("#totalpointsforbuyer").val(parseInt(pointsforbuyer));
                    $("#overallproductpointsforbuyer").val(parseInt(buyerpointsforoverallproduct));
                }else{
                    if(productwisepoints==0){
                        $("#totalpointsforbuyer").val("0");
                    }
                    $("#overallproductpointsforbuyer").val("0");
                }
            }
        }
        if(selleroverallproductpoints==1){
            var sellermimimumorderqtyforoverallproduct = $("#sellermimimumorderqtyforoverallproduct").val();
            if(sellermimimumorderqtyforoverallproduct!=''){
                var sellerpointsforoverallproduct = $("#sellerpointsforoverallproduct").val();
               
                if(totalquantity >= sellermimimumorderqtyforoverallproduct){
                    var pointsforseller = 0;
                    if(sellerproductwisepoints==1){
                        $(".inputpointsforseller").each(function( index ) {
                            var divid = $(this).attr("div-id");
                            if($(this).val()!="" && $("#qty"+divid).val() >0 && $("#productid"+divid).val() >0 ){
                                pointsforseller += parseInt($(this).val());
                            }
                        });
                    }
                    pointsforseller = parseInt(pointsforseller) + parseInt(sellerpointsforoverallproduct);
                    $("#totalpointsforseller").val(parseInt(pointsforseller));
                    $("#overallproductpointsforseller").val(parseInt(sellerpointsforoverallproduct));
                }else{
                    if(sellerproductwisepoints==0){
                        $("#totalpointsforseller").val("0");
                    }
                    $("#overallproductpointsforseller").val("0");
                }
            }
        }
        if(pointsonsalesorder==1 && addordertype=='0'){
            var mimimumorderamountforsalesorder = $("#mimimumorderamountforsalesorder").val();
            if(mimimumorderamountforsalesorder!=''){
                var buyerpointsforsalesorder = $("#buyerpointsforsalesorder").val();
                
                if(grossamount!=0 && grossamount >= mimimumorderamountforsalesorder){
                    
                    var pointsforbuyer = pointsforseller = 0;
                    if(productwisepoints==1){
                        $(".inputpointsforbuyer").each(function( index ) {
                            var divid = $(this).attr("div-id");
                            if($(this).val()!="" && $("#qty"+divid).val() >0 && $("#productid"+divid).val() >0 ){
                                pointsforbuyer += parseInt($(this).val());
                            }
                        });
                    }
                    if(overallproductpoints==1){
                        var buyerpointsforoverallproduct = $("#buyerpointsforoverallproduct").val();
                        pointsforbuyer = parseInt(pointsforbuyer) + parseInt(buyerpointsforoverallproduct);
                    }
                    pointsforbuyer = parseInt(pointsforbuyer) + parseInt(buyerpointsforsalesorder);
                    $("#totalpointsforbuyer").val(parseInt(pointsforbuyer));
                    $("#salespointsforbuyer").val(parseInt(buyerpointsforsalesorder));
                }else{
                    if(productwisepoints==0 && overallproductpoints==0){
                        $("#totalpointsforbuyer").val("0");
                    }
                    $("#salespointsforbuyer").val("0");
                }
            }
        }
        if(sellerpointsonsalesorder==1 && addordertype=='0'){
            var sellermimimumorderamountforsalesorder = $("#sellermimimumorderamountforsalesorder").val();
            if(sellermimimumorderamountforsalesorder!=''){
                var sellerpointsforsalesorder = $("#sellerpointsforsalesorder").val();
                
                if(grossamount!=0 && grossamount >= sellermimimumorderamountforsalesorder){
                    
                    var pointsforseller = 0;
                    if(sellerproductwisepoints==1){
                        $(".inputpointsforseller").each(function( index ) {
                            var divid = $(this).attr("div-id");
                            if($(this).val()!="" && $("#qty"+divid).val() >0 && $("#productid"+divid).val() >0 ){
                                pointsforseller += parseInt($(this).val());
                            }
                        });
                    }
                    if(selleroverallproductpoints==1){
                        var sellerpointsforoverallproduct = $("#sellerpointsforoverallproduct").val();
                        pointsforseller = parseInt(pointsforseller) + parseInt(sellerpointsforoverallproduct);
                    }
                    pointsforseller = parseInt(pointsforseller) + parseInt(sellerpointsforsalesorder);
                    $("#totalpointsforseller").val(parseInt(pointsforseller));
                    $("#salespointsforseller").val(parseInt(sellerpointsforsalesorder));
                }else{
                    if(sellerproductwisepoints==0 && selleroverallproductpoints==0){
                        $("#totalpointsforseller").val("0");
                    }
                    $("#salespointsforseller").val("0");
                }
            }
        }
        
    }else{
        if(ACTION==1 && $('#notesredeem').html()==''){
            
            var redeem = $("#redeem").val();
            var inputconversationrate = $("#inputconversationrate").val();

            if(redeem != ''){

                inputconversationrateamount = parseInt(redeem) * parseInt(inputconversationrate);
                $("#conversationrate").html(parseInt(redeem)+"*"+parseInt(inputconversationrate));
                $("#conversationrateamount").html(parseFloat(inputconversationrateamount).toFixed(2));
            }
            
            var pointsforbuyer = 0;
            $(".inputpointsforbuyer").each(function( index ) {
                var divid = $(this).attr("div-id");
                if($(this).val()!="" && $("#qty"+divid).val() >0 && $("#productid"+divid).val() >0 ){
                    pointsforbuyer += parseInt($(this).val());
                }
            });
            var sellerpointsforoverallproduct = $("#sellerpointsforoverallproduct").val();
            var buyerpointsforoverallproduct = $("#buyerpointsforoverallproduct").val();
            var sellerpointsforsalesorder = $("#sellerpointsforsalesorder").val();
            var buyerpointsforsalesorder = $("#buyerpointsforsalesorder").val();

            pointsforbuyer = parseInt(pointsforbuyer) + parseInt(buyerpointsforoverallproduct) + parseInt(buyerpointsforsalesorder);
           
            $("#totalpointsforbuyer").val(parseInt(pointsforbuyer));
        }else{
            var redeem = $("#redeem").val();
            var minimumpointsonredeem = $("#minimumpointsonredeem").val();
            var minimumpointsonredeemfororder = $("#minimumpointsonredeemfororder").val();
            var mimimumpurchaseorderamountforredeem = $("#mimimumpurchaseorderamountforredeem").val();
            var redeempointsforbuyer = $('#redeempointsforbuyer').val();

            if(parseInt(redeempointsforbuyer) >= parseInt(minimumpointsonredeem)){
                if(parseInt(redeem) >= parseInt(minimumpointsonredeemfororder)){
                    if(parseFloat(grossamount) <= parseFloat(mimimumpurchaseorderamountforredeem)){
                        if(redeem!=''){
                            $('#notesredeem').html("<i class='fa fa-exclamation-triangle'></i> Required Minimum Purchase Order Amount is "+mimimumpurchaseorderamountforredeem+"<br><i class='fa fa-exclamation-triangle'></i> If Purchase Order is less than "+mimimumpurchaseorderamountforredeem+" then "+Member_label+" can not redeem any points.");
                            $("#redeem_div").addClass("has-error is-focused");
                        }
                    }else{
                        if(redeem!=''){
                            $('#notesredeem').html("<i class='fa fa-exclamation-triangle'></i> If Minimum "+minimumpointsonredeemfororder+" Points Required for Redeem and "+Member_label+" have "+minimumpointsonredeem+" Points Balance then, "+Member_label+" can only redeem "+minimumpointsonredeemfororder+" or more points at the time of purchase process.");
                            $("#redeem_div").addClass("has-error is-focused");
                        }
                    }
                }else{
                    if(redeem!=''){
                        $('#notesredeem').html("<i class='fa fa-exclamation-triangle'></i> If "+member_label+" has minimum balance of "+minimumpointsonredeem+" points then only he can redeem points on purchase.");
                        $("#redeem_div").addClass("has-error is-focused");
                    }
                }
            }
                   
            $("#conversationrate").html("0");
            $("#conversationrateamount").html("0.00");
        }
    }
    $('#discountrow,#couponrow,#trconversationrate').hide();
    if(grossamount!=0){

        var totaldiscountamount = 0;
        if(parseFloat(couponamount)==0 && $("#applycoupon").text() != "Remove"){
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
            }
            if(calcdiscount==1){
                $("#overalldiscountpercent").val('');
                $("#overalldiscountamount").val('');
                totaldiscountamount = 0;
            }
            finalamount = parseFloat(grossamount) - parseFloat(totaldiscountamount) - parseFloat(inputconversationrateamount) + parseFloat(extrachargesamount);
        }else{
            couponamount = $("#couponamount").val().trim();
            finalamount = parseFloat(grossamount) - parseFloat(couponamount) - parseFloat(inputconversationrateamount) + parseFloat(extrachargesamount);
        }   
        if(finalamount<0){
            finalamount=0;
        }
        var roundoff =  Math.round(parseFloat(finalamount).toFixed(2))-parseFloat(finalamount);
        finalamount =  Math.round(parseFloat(finalamount).toFixed(2));
        
        $("#roundoff").html(format.format(roundoff));
        $("#inputroundoff").val(parseFloat(roundoff).toFixed(2));
        $("#netamount").html(format.format(finalamount));
        $("#inputnetamount").val(parseFloat(finalamount).toFixed(2));
        
        if(parseFloat(couponamount)!=0){
            $('#couponrow').show();
        }
        if($("#overalldiscountpercent").val()!='' || $("#overalldiscountamount").val()!='' || parseFloat($("#overalldiscountpercent").val()) > 0 || parseFloat($("#overalldiscountamount").val()) > 0){
            $('#discountrow').show();
        }
        if(parseFloat(inputconversationrateamount)!=0){
            $('#trconversationrate').show();
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
                if(addordertype==0){
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
                }

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
function resetbuyerform(){
    
    $("#buyercode_div").removeClass("has-error is-focused");
    $("#buyercode").val("");
}
function searchmembercode(){

    var buyercode = $("#buyercode").val();

    var isvalidbuyercode = 1;
    PNotify.removeAll();

    if(buyercode == ""){
        $("#buyercode_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter buyer '+member_label+' code !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidbuyercode = 0;
    }else{
        if(buyercode.length != 8){
            $("#buyercode_div").addClass("has-error is-focused");
            new PNotify({title: 'Buyer '+member_label+' code required between 8 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidbuyercode = 0;
        }
    }

    if(isvalidbuyercode == 1){

        var buyermemberid = $("#memberid").find("[data-code='"+buyercode+"']").val();

        if(buyermemberid==undefined){
            var formData = new FormData($('#addbuyerform')[0]);
           
            var uurl = SITE_URL+"order/search-buyer";
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
                    if(response!=0){
                        var obj = JSON.parse(response);
                        
                        $('#memberid').append($('<option>', { 
                            value: obj['id'],
                            'data-code': obj['membercode'],
                            text : obj['name']+" ("+obj['email']+")",
                            selected : 'selected'
                        }));
                        $('#memberid').selectpicker('refresh');
                        $('#addbuyerModal').modal("hide");
    
                        getbillingaddress();
                        if(addordertype=='0'){
                            getproduct();
                        }
                       
                        new PNotify({title: "Buyer added successfully.",styling: 'fontawesome',delay: '3000',type: 'success'});
                    }else {
                        new PNotify({title: 'Buyer code not found !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
            $("#memberid").val(buyermemberid);
            $('#memberid').selectpicker('refresh');
            $('#addbuyerModal').modal("hide");

            new PNotify({title: "Buyer already exist !",styling: 'fontawesome',delay: '3000',type: 'error'});
        }
        
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
function getofferproducts(divid){
    var uurl = SITE_URL+"offer/getofferproducts";
    var memberid = (ACTION==1)?$('#oldmemberid').val():$('#memberid').val();
    var productid = $('#productid'+divid).val();

    var productvariantid = '';
    var offerproduct = JSON.parse($("#productid"+divid+" option:selected").attr("data-offerproduct"));
    if($("#priceid"+divid).val() != ""){
        productvariantid = $('#priceid'+divid+' option:selected').attr('data-id');
        offerproduct = JSON.parse($("#priceid"+divid+" option:selected").attr("data-offerproduct"));
    }
    if(productid!=0){
        
        if(offerproduct.length > 0){
            $("#trigger-infobar"+divid).show();
            if($('#purchaseproductqty'+divid).val()>0 || $('#brandoffer'+divid).val()!=""){
                $("#trigger-infobar"+divid+" .btn-available-offers").css("background","seagreen").html("<i class='material-icons' style='font-size: 20px;'>check</i> Offer Collected");
            }
            
            //displayofferproducts(divid);
        }else{
            $("#trigger-infobar"+divid).hide();
        }

        /* $.ajax({
            url: uurl,
            type: 'POST',
            data: {memberid:memberid,productid:String(productid),productvariantid:String(productvariantid)},
            dataType: 'json',
            async: false,
            beforeSend: function(){
                $('.mask').show();
                $('#loader').show();
            },
            success: function(response){
                
            },
            error: function(xhr) {
            //alert(xhr.responseText);
            },
            complete: function(){
                $('.mask').hide();
                $('#loader').hide();
            },
        }); */
    }else{
        $("#trigger-infobar"+divid).hide();
    }
}
$(document).on('click','.offerproductchk',function () {
    var elementid = $(this).attr("id");
    var element = elementid.split("_");
    var productrowid = element[1];
    var offerrowid = element[2];
    var combrowid = element[3];
    //var element = $(this).attr("id").match(/\d+/);

    if($("#"+elementid).prop("checked") == true){
        checkofferapply(productrowid,offerrowid,combrowid,1);
    }else{
        checkofferapply(productrowid,offerrowid,combrowid,0);
    }
});
function displayofferproducts(divid){
    var uurl = SITE_URL+"offer/getofferproducts";
    var memberid = $("#memberid").val();
    var productid = $("#productid"+divid).val();
    var productvariantid = '';
    if($("#priceid"+divid).val() != ""){
        productvariantid = $('#priceid'+divid+' option:selected').attr('data-id');
    }
    if(productvariantid!=''){

        $.ajax({
            url: uurl,
            type: 'POST',
            data: {memberid:memberid,productid:String(productid),productvariantid:String(productvariantid)},
            dataType: 'json',
            async: false,
            beforeSend: function(){
                $('.mask').show();
                $('#loader').show();
            },
            success: function(response){
                
                if(response.length > 0){
                    var html = "";
                    for(var offer = 0; offer < response.length; offer++) {
                        
                        var image = shortdescription = minimumpurchaseamount = "";
                        var offerimages = response[offer]['offerimages'];
                        if(offerimages.length>0){
                            image = '<div id="myCarousel'+offer+'" class="carousel slide" data-ride="carousel">\
                                <ol class="carousel-indicators">';
                                    
                                for(var oi = 0; oi < offerimages.length; oi++) {
                                    var active = (oi==0)?"active":"";
                                    image += '<li data-target="#myCarousel'+offer+'" data-slide-to="'+oi+'" class="'+active+'"></li>';
                                }

                                image += '</ol>\
                                <div class="carousel-inner">';

                                for(var oi = 0; oi < offerimages.length; oi++) {
                                    var active = (oi==0)?"active":"";
                                    image += '<div class="item '+active+'">\
                                                <img src="'+OFFER+offerimages[oi]['filename']+'" alt="'+offerimages[oi]['filename']+'" style="width: 100%;margin-bottom: 5px;height: 150px;">\
                                            </div>';
                                } 

                                image += '</div>\
                                <a class="left carousel-control" href="#myCarousel'+offer+'" data-slide="prev">\
                                    <span class="glyphicon glyphicon-chevron-left"></span>\
                                    <span class="sr-only">Previous</span>\
                                </a>\
                                <a class="right carousel-control" href="#myCarousel'+offer+'" data-slide="next">\
                                    <span class="glyphicon glyphicon-chevron-right"></span>\
                                    <span class="sr-only">Next</span>\
                                </a>\
                                </div>';
                            
                        }
                        if(response[offer]['shortdescription']!=''){
                            shortdescription = ' <p>'+response[offer]['shortdescription']+'</p>';
                        }
                        if(response[offer]['offertype']==0){
                            minimumpurchaseamount = '<p><b>Minimum Purchase Amount : </b>'+CURRENCY_CODE+' '+response[offer]['minimumpurchaseamount']+'</p>';
                        }
                        var combinationarr = response[offer]['combination'];
                        var combinationhtml = "";
                        if(combinationarr.length > 0){
                            for(var c = 0; c < combinationarr.length; c++) {
                                checked = '';
                                $('input[name="offerproductcombinationid[]"]').each(function(index){
                                    var id = this.id.match(/[\d\.]+/g);
                                    var combinationid = this.value;
                                    $('input[id="appliedpriceid'+id+'"]').each(function(index){
                                        appliedpriceid = this.value;
                                        appliedpriceid = appliedpriceid.split(',');
                                        
                                        if(combinationid==combinationarr[c]['id'] && appliedpriceid.includes(productvariantid)){
                                            checked = 'checked';
                                            return false;
                                        }
                                    });
                                    
                                });
                                /* if(checked==""){
                                    $("#brandoffer"+divid).val('');
                                }else{
                                    $("#brandoffer"+divid).val(response[offer]['offertype']);
                                } */
                                combinationhtml += "<tr>";
                                combinationhtml += '<td class="pr-n text-center">\
                                                        <input type="hidden" id="combinationid_'+divid+'_'+(offer+1)+'_'+(c+1)+'" value="'+combinationarr[c]['id']+'">\
                                                        <input type="hidden" id="multiplication_'+divid+'_'+(offer+1)+'_'+(c+1)+'" value="'+combinationarr[c]['multiplication']+'">\
                                                        <div class="checkbox">\
                                                            <input type="checkbox" name="offerproduct_'+divid+'_'+(offer+1)+'_'+(c+1)+'" id="offerproduct_'+divid+'_'+(offer+1)+'_'+(c+1)+'" value="0" '+checked+' class="offerproductchk checkradios">\
                                                            <label for="offerproduct_'+divid+'_'+(offer+1)+'_'+(c+1)+'"></label>\
                                                        </div>\
                                                    </td><td>';
                                var purchaseproduct = combinationarr[c]['purchaseproduct'];
                                if(purchaseproduct.length > 0){
                                    for(var pp = 0; pp < purchaseproduct.length; pp++) {
                                        if(response[offer]['offertype'] == 0){
                                            var purchaseproductname = purchaseproduct[pp]['productname'];
                                        }else{
                                            var purchaseproductname = purchaseproduct[pp]['quantity']+" x "+purchaseproduct[pp]['productname'];
                                        }
                                        combinationhtml += "<p>"+purchaseproductname+"</p>";
                                        combinationhtml += "<div class='inputpurproducts_"+divid+"_"+(offer+1)+"_"+(c+1)+"'>\
                                                                <input type='hidden' id='ppid_"+divid+"_"+(offer+1)+"_"+(c+1)+"_"+pp+"' value='"+purchaseproduct[pp]['productid']+"'>\
                                                                <input type='hidden' id='ppriceid_"+divid+"_"+(offer+1)+"_"+(c+1)+"_"+pp+"' value='"+purchaseproduct[pp]['productvariantid']+"'>\
                                                                <input type='hidden' id='pqty_"+divid+"_"+(offer+1)+"_"+(c+1)+"_"+pp+"' value='"+purchaseproduct[pp]['quantity']+"'>\
                                                            </div>";
                                    }
                                }
                                combinationhtml += "</td><td>";
                                var offerproduct = combinationarr[c]['offerproduct'];
                                if(offerproduct.length > 0){
                                    for(var op = 0; op < offerproduct.length; op++) {
                    
                                        combinationhtml += "<p>"+offerproduct[op]['quantity']+" x "+offerproduct[op]['productname']+" "+offerproduct[op]['variantname']+" <span style='color:green;'>("+offerproduct[op]['offerdiscountlabel']+")</span></p>";
                                        
                                        combinationhtml += "<div class='inputofproducts_"+divid+"_"+(offer+1)+"_"+(c+1)+"'>\
                                                                <input type='hidden' id='offerproductid_"+divid+"_"+(offer+1)+"_"+(c+1)+"_"+op+"' value='"+offerproduct[op]['id']+"'>\
                                                                <input type='hidden' id='ofpid_"+divid+"_"+(offer+1)+"_"+(c+1)+"_"+op+"' value='"+offerproduct[op]['productid']+"'>\
                                                                <input type='hidden' id='ofprid_"+divid+"_"+(offer+1)+"_"+(c+1)+"_"+op+"' value='"+offerproduct[op]['productvariantid']+"'>\
                                                                <input type='hidden' id='ofqty_"+divid+"_"+(offer+1)+"_"+(c+1)+"_"+op+"' value='"+offerproduct[op]['quantity']+"'>\
                                                                <input type='hidden' id='ofpname_"+divid+"_"+(offer+1)+"_"+(c+1)+"_"+op+"' value='"+offerproduct[op]['productname']+" | "+offerproduct[op]['productcategoryname']+"'>\
                                                                <input type='hidden' id='ofprice_"+divid+"_"+(offer+1)+"_"+(c+1)+"_"+op+"' value='"+offerproduct[op]['price']+"'>\
                                                                <input type='hidden' id='ofvarname_"+divid+"_"+(offer+1)+"_"+(c+1)+"_"+op+"' value='"+offerproduct[op]['price']+" "+offerproduct[op]['variantname']+"'>\
                                                                <input type='hidden' id='oftax_"+divid+"_"+(offer+1)+"_"+(c+1)+"_"+op+"' value='"+offerproduct[op]['tax']+"'>\
                                                                <input type='hidden' id='ofdisctype_"+divid+"_"+(offer+1)+"_"+(c+1)+"_"+op+"' value='"+offerproduct[op]['discounttype']+"'>\
                                                                <input type='hidden' id='ofdisc_"+divid+"_"+(offer+1)+"_"+(c+1)+"_"+op+"' value='"+offerproduct[op]['discountvalue']+"'>\
                                                            </div>";
                                    }
                                }
                                combinationhtml += "</td></tr>";
                            }
                        }
                        
                        html += '<div class="offers" style="padding: 10px 0;border-bottom: 2px solid #e8e8e8;">\
                                    <h5 style="text-transform: uppercase;font-size: 14px;"><b>'+response[offer]['offername']+'</b></h5>\
                                    '+image+'\
                                    <input type="hidden" name="offerminimumbillamount_'+divid+'_'+(offer+1)+'" id="offerminimumbillamount_'+divid+'_'+(offer+1)+'" value="'+response[offer]['minbillamount']+'">\
                                    <input type="hidden" name="minimumpurchaseamount_'+divid+'_'+(offer+1)+'" id="minimumpurchaseamount_'+divid+'_'+(offer+1)+'" value="'+response[offer]['minimumpurchaseamount']+'">\
                                    <input type="hidden" name="maximumusage_'+divid+'_'+(offer+1)+'" id="maximumusage_'+divid+'_'+(offer+1)+'" value="'+response[offer]['maximumusage']+'">\
                                    <input type="hidden" name="used_'+divid+'_'+(offer+1)+'" id="used_'+divid+'_'+(offer+1)+'" value="'+response[offer]['used']+'">\
                                    <input type="hidden" name="noofmembersused_'+divid+'_'+(offer+1)+'" id="noofmembersused_'+divid+'_'+(offer+1)+'" value="'+response[offer]['noofmembersused']+'">\
                                    <input type="hidden" name="noofmemberusedoffer_'+divid+'_'+(offer+1)+'" id="noofmemberusedoffer_'+divid+'_'+(offer+1)+'" value="'+response[offer]['noofmemberusedoffer']+'">\
                                    <input type="hidden" name="offertype_'+divid+'_'+(offer+1)+'" id="offertype_'+divid+'_'+(offer+1)+'" value="'+response[offer]['offertype']+'">\
                                    <table class="table table-bordered">\
                                        <tr>\
                                            <th colspan="2">Purchase Products</th>\
                                            <th>Offer Products</th>\
                                        </tr>\
                                        '+combinationhtml+'\
                                    </table>\
                                    '+minimumpurchaseamount+'\
                                    <p>'+shortdescription+'</p>\
                                    <p>'+response[offer]['termscondition']+'</p>\
                                </div>';
                    
                    }
                    $("#offerproductsdata").html(html);
                    // $('#offerproductsdata [data-toggle="popover"]').popover({"html": true,trigger: "click"});
                    loadpopover();
                }else{
                    $("#offerproductsdata").html('');
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
}
function checkofferapply(productrowid,offerrowid,combrowid,checked){

    var combinationid = $("#combinationid_"+productrowid+"_"+offerrowid+"_"+combrowid).val();
    var orderpayableamount = $("#inputnetamount").val();
    //checked =1;
    var availableoffershtml = "<i class='material-icons' style='font-size: 20px;'>add_circle_outline</i> Available Offers";
    var offercollectedhtml = "<i class='material-icons' style='font-size: 20px;'>check</i> Offer Collected";
    if(checked==1){
        
        var count = 1;
        var offermatch = [];
        var productmatch = [];
        var offerminimumbillamount =$("#offerminimumbillamount_"+productrowid+"_"+offerrowid).val();
        var minimumpurchaseamount = $("#minimumpurchaseamount_"+productrowid+"_"+offerrowid).val();
        var maximumusage = parseInt($("#maximumusage_"+productrowid+"_"+offerrowid).val());  
        var used = parseInt($("#used_"+productrowid+"_"+offerrowid).val());  
        var noofmembersused = parseInt($("#noofmembersused_"+productrowid+"_"+offerrowid).val());  
        var noofmemberusedoffer = parseInt($("#noofmemberusedoffer_"+productrowid+"_"+offerrowid).val());  
        var offertype = $("#offertype_"+productrowid+"_"+offerrowid).val();
        var isbrandoffer = $("#brandoffer"+productrowid).val();
        var validoffer = 1;
        PNotify.removeAll();

        if(maximumusage!=0 && maximumusage<=used){
            new PNotify({title: 'Maximum offer apply limit over!',styling: 'fontawesome',delay: '3000',type: 'error'});
            $('#offerproduct_'+productrowid+"_"+offerrowid+"_"+combrowid).prop("checked", false);
            return false;
        }
        if(noofmembersused!=0 && noofmembersused<=noofmemberusedoffer){
            new PNotify({title: Member_label+' offer apply limit over!',styling: 'fontawesome',delay: '3000',type: 'error'});
            $('#offerproduct_'+productrowid+"_"+offerrowid+"_"+combrowid).prop("checked", false);
            return false;
        }
        if((offertype==1 && isbrandoffer==1) || isbrandoffer==""){
            var PRODUCT_AMOUNT = 0;
            $('.inputpurproducts_'+productrowid+"_"+offerrowid+"_"+combrowid).each(function(index){
                
                var pproductid = $("#ppid_"+productrowid+"_"+offerrowid+"_"+combrowid+"_"+index).val();
                var ppriceid = $("#ppriceid_"+productrowid+"_"+offerrowid+"_"+combrowid+"_"+index).val();
                var pqty = $("#pqty_"+productrowid+"_"+offerrowid+"_"+combrowid+"_"+index).val();
                var priceidarr = [];
                if (ppriceid.indexOf(',') > -1) { 
                    priceidarr = ppriceid.split(',') 
                    //priceidarr.push($("#priceid"+productrowid+" option:selected").attr("data-id"));
                }else{
                    priceidarr.push(ppriceid);
                }
                
                if(parseFloat(orderpayableamount)>=parseFloat(offerminimumbillamount)){
                    $('select.productid').each(function(index){
                        var divid = $(this).attr("div-id");
                        var oproductid = $("#productid"+divid).val();
                        var opriceid = $("#priceid"+divid+" option:selected").attr("data-id");
                        var oqty = $("#qty"+divid).val();
                        var productamount = $("#amount"+divid).val();
                        var purchaseproductqty = $("#purchaseproductqty"+divid).val();
                        var brandoffer = $("#brandoffer"+divid).val();
                        oqty = parseFloat(oqty) - parseFloat(purchaseproductqty);
                        
                        if(oproductid!=0 && opriceid != ""){
                            if(offertype==0){
                                if(oproductid==pproductid && priceidarr.includes(opriceid) && parseFloat(productamount) != "" ){
                                    /* if(parseFloat(productamount) >= parseFloat(minimumpurchaseamount)){
                                    } */
                                    if(parseInt(brandoffer)==0 || parseInt(brandoffer)==1){
                                        validoffer = 0;
                                    }else{
                                        offermatch.push(divid);
                                        PRODUCT_AMOUNT += parseFloat(productamount);
                                    }
                                }
                            }else{
                                if(oproductid==pproductid && priceidarr.includes(opriceid) && oqty>=pqty){
                                    if(parseInt(brandoffer)==0 || parseInt(brandoffer)==1){
                                        validoffer = 0;
                                    }else{
                                        offermatch.push(divid);
                                        if(priceidarr.length>1){
                                            return false;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
                productmatch.push(pproductid);
            });
            if(validoffer==0){
                new PNotify({title: 'Can not apply this offer. Because you are already applied other offer !',styling: 'fontawesome',delay: '3000',type: 'error'});
                $('#offerproduct_'+productrowid+"_"+offerrowid+"_"+combrowid).prop("checked", false);
                return false;
            }
            if(parseFloat(orderpayableamount)>=parseFloat(offerminimumbillamount)){
                
                if((productmatch.length == offermatch.length && offertype==1) ||  (offertype==0 && parseFloat(PRODUCT_AMOUNT)>=parseFloat(minimumpurchaseamount) && productmatch.length == offermatch.length)){
                    new PNotify({title: 'Offer applied.',styling: 'fontawesome',delay: '3000',type: 'success'});
                    $("#trigger-infobar"+productrowid+" .btn-available-offers").css("background","seagreen").html(offercollectedhtml);
                    for(var offer = 0; offer < offermatch.length; offer++) {
                        $("#brandoffer"+offermatch[offer]).val(offertype);
                    }
                    generateoffergrid(productrowid,offerrowid,combrowid,combinationid);
                }else{
                    if(offertype==0 && parseFloat(PRODUCT_AMOUNT)<parseFloat(minimumpurchaseamount)){
                        new PNotify({title: 'Require minimum '+CURRENCY_CODE+' '+minimumpurchaseamount+' purchase product amount then apply this offer !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else{
                        new PNotify({title: 'Can not apply this offer because not fullfill terms & condition !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }
                    $('#offerproduct_'+productrowid+"_"+offerrowid+"_"+combrowid).prop("checked", false);
                    // $("#brandoffer"+productrowid).val('');
                    if($("#offerproductsdata input[type='checkbox']:checked").length == 0){
                        $("#trigger-infobar"+productrowid+" .btn-available-offers").css("background","#cc2b1b").html(availableoffershtml);
                    }
                }
            }else{
                new PNotify({title: 'Require minimum '+CURRENCY_CODE+' '+offerminimumbillamount+' order amount then apply this offer !',styling: 'fontawesome',delay: '3000',type: 'error'});
                $('#offerproduct_'+productrowid+"_"+offerrowid+"_"+combrowid).prop("checked", false);
                // $("#brandoffer"+productrowid).val('');
                $("#trigger-infobar"+productrowid+" .btn-available-offers").css("background","#cc2b1b").html(availableoffershtml);
            }
        }else{
            if(offertype==0){
                if(isbrandoffer==1){
                    new PNotify({title: 'Can not apply other offers !',styling: 'fontawesome',delay: '3000',type: 'error'});
                }else{
                    new PNotify({title: 'Can not apply other offers !',styling: 'fontawesome',delay: '3000',type: 'error'});
                }
            }else{
                new PNotify({title: 'Can not apply this offer. Because you are already applied other offer !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }
            $('#offerproduct_'+productrowid+"_"+offerrowid+"_"+combrowid).prop("checked", false);
        }
        
    }else{
        // $('.countofferproducts_'+combinationid).remove();
        
        $('.inputpurproducts_'+productrowid+"_"+offerrowid+"_"+combrowid).each(function(index){
                
            var pproductid = $("#ppid_"+productrowid+"_"+offerrowid+"_"+combrowid+"_"+index).val();
            var ppriceid = $("#ppriceid_"+productrowid+"_"+offerrowid+"_"+combrowid+"_"+index).val();
            var priceidarr = [];
            if (ppriceid.indexOf(',') > -1) { 
                // priceidarr = ppriceid.split(',');
                priceidarr.push($("#priceid"+productrowid+" option:selected").attr("data-id"));
            }else{
                priceidarr.push(ppriceid);
            }
            $('select.productid').each(function(index){
                var divid = $(this).attr("div-id");
                var oproductid = $("#productid"+divid).val();
                var opriceid = $("#priceid"+divid+" option:selected").attr("data-id");
                
                if(oproductid!=0 && opriceid != "" && oproductid==pproductid && priceidarr.includes(opriceid) && $("#offerproductsdata input[type='checkbox']:checked").length == 0){
                    
                    $("#trigger-infobar"+divid+" .btn-available-offers").css("background","#cc2b1b").html(availableoffershtml);
                }
                    
                if($("#offerproductsdata input[type='checkbox']:checked").length == 0){
                    $("#brandoffer"+divid).val('');
                }
            });
            
        });
        generateoffergrid(productrowid,offerrowid,combrowid,combinationid,1);
        //console.log($('.postcountoffers').length);
        if($('.postcountoffers').length == 0){
            $("#displayofferproductsdata").hide();
        }
    }
}
function generateoffergrid(productrowid,offerrowid,combrowid,combinationid,remove=0) {
    
    if(remove==1){
        var orderpriceid = [];
        $('.inputofproducts_'+productrowid+"_"+offerrowid+"_"+combrowid).each(function(index){
            var ppid = $("#ppid_"+productrowid+"_"+offerrowid+"_"+combrowid+"_"+index).val();
            var ppriceid = $("#ppriceid_"+productrowid+"_"+offerrowid+"_"+combrowid+"_"+index).val();
            var pqty = $("#pqty_"+productrowid+"_"+offerrowid+"_"+combrowid+"_"+index).val();
            var offertype = $("#offertype_"+productrowid+"_"+offerrowid).val();   

            var priceidarr = [];

            if (ppriceid.indexOf(',') > -1) { 
                // priceidarr = ppriceid.split(',');
                priceidarr.push($("#priceid"+productrowid+" option:selected").attr("data-id")); 
            }else{
                priceidarr.push(ppriceid);
            }
            $('select.productid').each(function(index){
                var divid = $(this).attr("div-id");
                var oproductid = $("#productid"+divid).val();
                var opriceid = $("#priceid"+divid+" option:selected").attr("data-id");

                if(oproductid!=0 && opriceid != ""){
                   
                    if(oproductid==ppid && priceidarr.includes(opriceid)){
                        if(offertype==1){
                            var purchaseproductqty = $("#purchaseproductqty"+divid).val();
                            if(purchaseproductqty!=0){
                                $("#purchaseproductqty"+divid).val(parseFloat(purchaseproductqty) - parseFloat(pqty));
                            }
                        }
                        orderpriceid.push(opriceid);
                    }
                }
            });
        });
        
        $('.offerproducts_'+combinationid+' input[name="orderproducttableid[]"]').each(function(index){
            var id = this.id.match(/[\d\.]+/g);
            if(this.value!='' || this.value!=0){
                $('.offerproducts_'+combinationid+' input[name="appliedpriceid[]"]').each(function(index){
                    appliedpriceid = this.value;
                    appliedpriceid = appliedpriceid.split(',');
                    
                    if (JSON.stringify(appliedpriceid) == JSON.stringify(orderpriceid)){
                        var removeorderproductid = $('#removeorderproductid').val();
                        $('#removeorderproductid').val(removeorderproductid+','+$('input[name="orderproducttableid['+index+']').val());
                        return false;
                    }
                });
            }
           
        });
        $('.offerproducts_'+combinationid).filter('[data-priceid="'+orderpriceid.join(",")+'"]').remove();

    }else{
        var offerhtml = offerproductdetailshtml = '';
        var offerproductdetails = [];

        var multiplication = $("#multiplication_"+productrowid+"_"+offerrowid+"_"+combrowid).val();   
        var offerminimumbillamount = $("#offerminimumbillamount_"+productrowid+"_"+offerrowid).val();  
        
        var offertype = $("#offertype_"+productrowid+"_"+offerrowid).val();   
        var minimumpurchaseamount = $("#minimumpurchaseamount_"+productrowid+"_"+offerrowid).val();   
        var MultiplyArray = [];
        var PURCHASE_PRODUCTS_ARR = new Array();   
        var PRODUCT_AMOUNT = 0;
        $('.inputpurproducts_'+productrowid+"_"+offerrowid+"_"+combrowid).each(function(index){
                
            var pproductid = $("#ppid_"+productrowid+"_"+offerrowid+"_"+combrowid+"_"+index).val();
            var ppriceid = $("#ppriceid_"+productrowid+"_"+offerrowid+"_"+combrowid+"_"+index).val();
            var pqty = $("#pqty_"+productrowid+"_"+offerrowid+"_"+combrowid+"_"+index).val();
            var priceidarr = [];
            if (ppriceid.indexOf(',') > -1) { 
                priceidarr = ppriceid.split(',');
                //priceidarr.push($("#priceid"+productrowid+" option:selected").attr("data-id")); 
            }else{
                priceidarr.push(ppriceid);
            }
            $('select.productid').each(function(index){
                var divid = $(this).attr("div-id");
                var oproductid = $("#productid"+divid).val();
                var opriceid = $("#priceid"+divid+" option:selected").attr("data-id");
                var oqty = $("#qty"+divid).val();
                var productamount = $("#amount"+divid).val();

                if(oproductid!=0 && opriceid != "" && oqty!="" && oproductid==pproductid && priceidarr.includes(opriceid)){
                    if(multiplication==1){
                        if(offertype==0){
                            PRODUCT_AMOUNT += parseFloat(productamount);
                        }else{
                            MultiplyArray.push(parseFloat(oqty / pqty)); 
                        }
                    }
                    if(priceidarr.length>1){
                        return false;
                    }
                }
            });
            PURCHASE_PRODUCTS_ARR[index]= [pproductid,ppriceid,pqty];   
        });
        // console.log(PRODUCT_AMOUNT);
        if(offertype==0){
            var orqty = parseFloat(parseFloat(PRODUCT_AMOUNT) / parseFloat(minimumpurchaseamount));
        }else{
            var orqty = 1;
            if (MultiplyArray.length > 0) {
                orqty = Math.min.apply(Math,MultiplyArray);
            }
        }
        var appliedpriceid = [];
        $('.inputofproducts_'+productrowid+"_"+offerrowid+"_"+combrowid).each(function(index){
            
            var ofpname = $("#ofpname_"+productrowid+"_"+offerrowid+"_"+combrowid+"_"+index).val();
            var ofvarname = $("#ofvarname_"+productrowid+"_"+offerrowid+"_"+combrowid+"_"+index).val();
            var offqty = $("#ofqty_"+productrowid+"_"+offerrowid+"_"+combrowid+"_"+index).val();
            var oftax = $("#oftax_"+productrowid+"_"+offerrowid+"_"+combrowid+"_"+index).val();
            var ofdisctype = $("#ofdisctype_"+productrowid+"_"+offerrowid+"_"+combrowid+"_"+index).val();
            var ofdisc = $("#ofdisc_"+productrowid+"_"+offerrowid+"_"+combrowid+"_"+index).val();
            var ofprice = $("#ofprice_"+productrowid+"_"+offerrowid+"_"+combrowid+"_"+index).val();

            var offerproductid = $("#offerproductid_"+productrowid+"_"+offerrowid+"_"+combrowid+"_"+index).val();
            var ofpid = $("#ofpid_"+productrowid+"_"+offerrowid+"_"+combrowid+"_"+index).val();
            var ofpriceid = $("#ofprid_"+productrowid+"_"+offerrowid+"_"+combrowid+"_"+index).val();

            if(offertype==1 && PURCHASE_PRODUCTS_ARR.length > 0 && index==0){
                for(p=0;p<PURCHASE_PRODUCTS_ARR.length;p++){
                    var pproductid = PURCHASE_PRODUCTS_ARR[p][0];
                    var ppriceid = PURCHASE_PRODUCTS_ARR[p][1];
                    var pqty = PURCHASE_PRODUCTS_ARR[p][2];
                    var priceidarr = [];
                    
                    if (ppriceid.indexOf(',') > -1) { 
                        priceidarr = ppriceid.split(',');
                        //priceidarr.push($("#priceid"+productrowid+" option:selected").attr("data-id")); 
                    }else{
                        priceidarr.push(ppriceid);
                    }
                    
                    $('select.productid').each(function(index){
                        var divid = $(this).attr("div-id");
                        var oproductid = $("#productid"+divid).val();
                        var opriceid = $("#priceid"+divid+" option:selected").attr("data-id");
                        if(oproductid!=0 && opriceid != ""){
                            
                            if(oproductid==pproductid && priceidarr.includes(opriceid) && offertype==1){
                                appliedpriceid.push(opriceid);
                                var purchaseproductqty = $("#purchaseproductqty"+divid).val();
                                $("#purchaseproductqty"+divid).val((parseFloat(purchaseproductqty) + parseFloat(pqty))  * parseFloat(orqty));
                                
                                //$("#purchaseproductqty"+divid).val(parseInt(purchaseproductqty) + parseInt(pqty));
                                if(priceidarr.length>1){
                                    return false;
                                }
                            }
                        }
                    });
                }
            }else{
                for(p=0;p<PURCHASE_PRODUCTS_ARR.length;p++){
                    var pproductid = PURCHASE_PRODUCTS_ARR[p][0];
                    var ppriceid = PURCHASE_PRODUCTS_ARR[p][1];
                    var pqty = PURCHASE_PRODUCTS_ARR[p][2];
                    var priceidarr = [];
                    
                    if (ppriceid.indexOf(',') > -1) { 
                        priceidarr = ppriceid.split(',');
                        //priceidarr.push($("#priceid"+productrowid+" option:selected").attr("data-id")); 
                    }else{
                        priceidarr.push(ppriceid);
                    }
                    
                    $('select.productid').each(function(index){
                        var divid = $(this).attr("div-id");
                        var oproductid = $("#productid"+divid).val();
                        var opriceid = $("#priceid"+divid+" option:selected").attr("data-id");
                        if(oproductid!=0 && opriceid != ""){
                            
                            if(oproductid==pproductid && priceidarr.includes(opriceid)){
                                appliedpriceid.push(opriceid);
                                
                                if(priceidarr.length>1){
                                    return false;
                                }
                            }
                        }
                    });
                }
            }
            var ofqty = offqty;
            
            if(multiplication==1){
                ofqty = parseFloat(orqty) * offqty;    
            }
            // console.log(ofqty);
            offerproductdetails.push({
                ofpid:ofpid, 
                ofpriceid:ofpriceid,
                multiplication:multiplication,
                ofqty:offqty,
                ofdisctype:ofdisctype,
                ofdisc:ofdisc,
                oftax:oftax,
                ofprice:ofprice,
                offerminimumbillamount:offerminimumbillamount,
                offertype:offertype,
                minimumpurchaseamount:minimumpurchaseamount,
                purchaseproducts:PURCHASE_PRODUCTS_ARR,
                appliedpriceid:appliedpriceid.join(",")
            });
            //console.log(ofqty);
            var amountexctax = parseFloat(ofprice) - (parseFloat(ofprice) * parseFloat(oftax) / (100 + parseFloat(oftax)));
            var amountinctax = parseFloat(ofprice);
            
            var amount = discountamount = discountper = 0;
            if(ofdisctype==1){
                discountper = format.format(ofdisc);
                discountamount = format.format(parseFloat(((parseFloat(amountinctax) * parseFloat(ofqty)) * parseFloat(ofdisc)) / 100).toFixed(2));
                amount = parseFloat((parseFloat(amountinctax) * parseFloat(ofqty)) - parseFloat(((parseFloat(amountinctax) * parseFloat(ofqty)) * parseFloat(ofdisc)) / 100)).toFixed(2);
            }else{
                discountamount = format.format(ofdisc);
                discountper = parseFloat((parseFloat(ofdisc)*100)/(parseFloat(amountinctax) * parseFloat(ofqty))).toFixed(2);
                amount = parseFloat((parseFloat(amountinctax) * parseFloat(ofqty)) - parseFloat(ofdisc)).toFixed(2);
            }
            var producttaxamount = (parseFloat(amount) * parseFloat(oftax) / (100 + parseFloat(oftax)));
            if(parseFloat(amount) <= 0){
                var postofferproductrate = parseFloat(amountexctax).toFixed(2);
                amount = parseFloat(0).toFixed(2);
                producttaxamount = parseFloat(0).toFixed(2);
            }
            var postofferproductrate = parseFloat(amountexctax).toFixed(2);
            if(GST_PRICE==1){
                postofferproductrate = parseFloat(amountinctax).toFixed(2);
            }
            // amount = parseFloat(amount) * parseInt(ofqty);
            
            offerhtml += '<tr class="postcountoffers offerproducts_'+combinationid+'" data-priceid="'+appliedpriceid.join(",")+'"> \
                            <td rowspan="2">'+ofpname+' \
                            <input type="hidden" name="orderproducttableid[]" id="orderproducttableid'+(index+1)+'" value=""> \
                            <input type="hidden" name="postofferproducttableid[]" id="postofferproducttableid'+(index+1)+'" value="'+offerproductid+'"> \
                            <input type="hidden" name="postofferproductid[]" id="postofferproductid'+(index+1)+'" value="'+ofpid+'"> \
                            <input type="hidden" name="appliedpriceid[]" id="appliedpriceid'+(index+1)+'" value="'+appliedpriceid.join(",")+'"> \
                            <input type="hidden" name="offerproductcombinationid[]" id="offerproductcombinationid'+(index+1)+'" value="'+combinationid+'"> \
                            <input type="hidden" name="postofferpriceid[]" id="postofferpriceid'+(index+1)+'" value="'+ofpriceid+'"> \
                            <input type="hidden" name="postofferproductrate[]" id="postofferproductrate'+(index+1)+'" value="'+parseFloat(postofferproductrate)+'"> \
                            <input type="hidden" name="postofferoriginalprice[]" id="postofferoriginalprice'+(index+1)+'" value="'+amountinctax+'"> \
                            <input type="hidden" name="postofferquantity[]" id="postofferquantity'+combinationid+'_'+ofpriceid+'" value="'+ofqty+'"> \
                            <input type="hidden" name="postoffertax[]" id="postoffertax'+(index+1)+'" value="'+parseFloat(oftax)+'"> \
                            <input type="hidden" name="postofferdiscountper[]" id="postofferdiscountper'+combinationid+'_'+ofpriceid+'" value="'+discountper+'"> \
                            <input type="hidden" name="postofferproducttaxamount[]" id="postofferproducttaxamount'+combinationid+'_'+ofpriceid+'" value="'+parseFloat(producttaxamount)+'"> \
                            <input type="hidden" name="postofferamount[]" id="postofferamount'+combinationid+'_'+ofpriceid+'" value="'+parseFloat(amount)+'"> \
                            </td> \
                            <td rowspan="2">'+ofvarname+' \
                            </td> \
                            <td rowspan="2" class="text-right"><span id="postofferqtyspan'+combinationid+'_'+ofpriceid+'">'+parseFloat(ofqty)+'</span> \
                            </td> \
                            <td rowspan="2" class="text-right">'+oftax+' \
                            </td> \
                            <td class="text-right"><span id="postofferdiscountperspan'+combinationid+'_'+ofpriceid+'">'+discountper+'</span> \
                            </td> \
                            <td rowspan="2" class="text-right"><span id="postofferamountspan'+combinationid+'_'+ofpriceid+'">'+format.format(amount)+'</span> \
                            </td> \
                        </tr> \
                        <tr class="postcountoffers offerproducts_'+combinationid+'" data-priceid="'+appliedpriceid.join(",")+'"> \
                            <td class="text-right"><span id="postofferdiscountspan'+combinationid+'_'+ofpriceid+'">'+discountamount+'</span></td> \
                        </tr>';
        });
        
        /* if($("#offerproductdetails #offerproductdetails"+combinationid).length==0){
            offerproductdetailshtml = '<div class="offerproductdetails" id="offerproductdetails'+combinationid+'">'+JSON.stringify(offerproductdetails)+'</div>';
            $("#offerproductdetails").append(offerproductdetailshtml);
        }else{
            offerproductdetailshtml = JSON.stringify(offerproductdetails);
            $("#offerproductdetails #offerproductdetails"+combinationid).html(offerproductdetailshtml);
        } */
        offerproductdetailshtml = '<div class="offerproductdetails" id="offerproductdetails'+combinationid+'" data-priceid="'+appliedpriceid.join(",")+'">'+JSON.stringify(offerproductdetails)+'</div>';
        $("#offerproductdetails").append(offerproductdetailshtml);
        
        /* if($('#displayofferproductsdata').find('.offerproducts_'+combinationid).length > 0){
            $('.offerproducts_'+combinationid+' input[name="orderproducttableid[]"]').each(function(index){
                if(this.value!='' || this.value!=0){
                    var removeorderproductid = $('#removeorderproductid').val();
                    $('#removeorderproductid').val(removeorderproductid+','+this.value);
                }
            });
            $('.offerproducts_'+combinationid).remove();
        } */
        
        $("#displayofferproductsdata tbody").append(offerhtml);
        $("#displayofferproductsdata").show();
    }
    changenetamounttotal();
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

        var multipleprices = JSON.parse($("#priceid"+divid+" option:selected").attr("data-multipleprices"));
        var length = multipleprices.length;
        for(var i = 0; i < multipleprices.length; i++) {
            
            var txt = "";

            if(parseInt(pricetype)==1){
                txt = CURRENCY_CODE+multipleprices[i]['price']+" "+multipleprices[i]['quantity']+(parseInt(quantitytype)==0?"+":"")+" Qty"
            }else{
                txt = multipleprices[i]['price'];
            }
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
        if(ACTION==1 && oldcombopriceid[divid-1]!="undefined" && $('#combopriceid'+divid).val()==""){
            $('#combopriceid'+divid).val(oldcombopriceid[divid-1]).selectpicker('refresh').change();

           if(productid==oldproductid[divid-1] && priceid==oldpriceid[divid-1]){
               var quantity = $("#combopriceid"+divid+" option:selected").attr("data-quantity");
               
               if(parseInt(quantitytype)==1 && parseInt(pricetype)==1){
                   $("#qty"+divid).trigger("touchspin.updatesettings", {min: parseInt(quantity), step: parseInt(quantity)});
               }else{
                   $("#qty"+divid).trigger("touchspin.updatesettings", {min: 1, step: 1});
               }
           }
        }

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

function validatedoffergrid(){
    
    var orderpayableamount = $("#inputnetamount").val();

    $('select.productid').each(function(index){
        var divid = $(this).attr("div-id");
        $("#purchaseproductqty"+divid).val(0);
    });
    
    $('.offerproductdetails').each(function(index){
        var offermatch = [];
        var productmatch = [];
        arr = JSON.parse($(this).html());
        
        for(i=0;i<arr.length;i++){

            var offerminimumbillamount = arr[i]['offerminimumbillamount'];
            var minimumpurchaseamount = arr[i]['minimumpurchaseamount'];
            var offertype = arr[i]['offertype'];
            var purchaseproducts = arr[i]['purchaseproducts'];
            var appliedpriceid = arr[i]['appliedpriceid'];
            appliedpriceid = appliedpriceid.split(',');
            var PRODUCT_AMOUNT = 0;
            if(i==0){
                if(parseFloat(orderpayableamount)>=parseFloat(offerminimumbillamount)){
                    if(purchaseproducts.length > 0){
                        var isvalidproduct = [];
                        for(p=0;p<purchaseproducts.length;p++){
                            var pproductid = purchaseproducts[p][0];
                            var ppriceid = purchaseproducts[p][1];
                            var pqty = purchaseproducts[p][2];
                            var priceidarr = [];
                            if (ppriceid.indexOf(',') > -1) { 
                                priceidarr = ppriceid.split(',');
                                // priceidarr.push($("#priceid"+productrowid+" option:selected").attr("data-id")); 
                            }else{
                                priceidarr.push(ppriceid);
                            }
                            
                            $('select.productid').each(function(index){
                                var divid = $(this).attr("div-id");
                                var oproductid = $("#productid"+divid).val();
                                var opriceid = $("#priceid"+divid+" option:selected").attr("data-id");
                                var oqty = $("#qty"+divid).val();
                                var productamount = $("#amount"+divid).val();
                                var purchaseproductqty = $("#purchaseproductqty"+divid).val();
                                //oqty = parseInt(oqty) + parseInt(purchaseproductqty) - parseInt(pqty);
                                oqty = parseFloat(oqty) - parseFloat(purchaseproductqty);
                                //oqty = parseInt(purchaseproductqty);
                                
                                if(oproductid!=0 && opriceid != ""){
                                    if(offertype==0){
                                        if(oproductid==pproductid && priceidarr.includes(opriceid) && parseFloat(productamount) != ""){
                                            PRODUCT_AMOUNT += parseFloat(productamount);
                                            isvalidproduct.push(divid);
                                            if(priceidarr.length>1){
                                                return false;
                                            }
                                        }
                                    }else{
                                        
                                        if(oproductid==pproductid && priceidarr.includes(opriceid) && appliedpriceid.includes(opriceid) && oqty>=pqty){
                                            isvalidproduct.push(divid);
                                            if(priceidarr.length>1){
                                                return false;
                                            }
                                        }
                                    }
                                }
                            });
                        }
                        if(offertype==0 && parseFloat(PRODUCT_AMOUNT)>=parseFloat(minimumpurchaseamount) && purchaseproducts.length == isvalidproduct.length){
                            offermatch.push(1);
                        }
                        if(offertype==1 && purchaseproducts.length == isvalidproduct.length){
                            offermatch.push(1);
                        }
                    }
                }
                productmatch.push(1);
            }
        }
        
        if(productmatch.length != offermatch.length){
            var combinationid = $(this).attr('id').match(/\d+/);
            var orderpriceid = [];
            for(i=0;i<arr.length;i++){

                var ofqty = arr[i]['ofqty'];
                var offertype = arr[i]['offertype'];
                var purchaseproducts = arr[i]['purchaseproducts'];
                if(i==0 && purchaseproducts.length > 0){                                                                                            
                    for(p=0;p<purchaseproducts.length;p++){
                        var pproductid = purchaseproducts[p][0];
                        var ppriceid = purchaseproducts[p][1];
                        var pqty = purchaseproducts[p][2];
                        var priceidarr = [];
                        if (ppriceid.indexOf(',') > -1) { 
                            priceidarr = ppriceid.split(',');
                            // priceidarr.push($("#priceid"+productrowid+" option:selected").attr("data-id")); 
                        }else{
                            priceidarr.push(ppriceid);
                        }
                        $('select.productid').each(function(index){
                            var divid = $(this).attr("div-id");
                            var oproductid = $("#productid"+divid).val();
                            var opriceid = $("#priceid"+divid+" option:selected").attr("data-id");
                            var oqty = $("#qty"+divid).val();
                            var purchaseproductqty = $("#purchaseproductqty"+divid).val();
                            oqty = (parseFloat(oqty) - parseFloat(purchaseproductqty)) + parseFloat(pqty);
                            //oqty = parseInt(purchaseproductqty);
                            if(oproductid!=0 && opriceid != ""){
                            
                                var purchaseproductqty = $("#purchaseproductqty"+divid).val();
                                if(oproductid==pproductid && priceidarr.includes(opriceid) && appliedpriceid.includes(opriceid)){
                                    if(offertype==1 && purchaseproductqty!=0 && oqty<pqty){
                                        $("#purchaseproductqty"+divid).val(Math.max(0, (parseFloat(purchaseproductqty) - parseFloat(pqty))));
                                        orderpriceid.push(opriceid);
                                    }
                                    $("#brandoffer"+divid).val('');
                                }
                            }
                        });
                    }
                }
            }
            /* $('.offerproducts_'+combinationid+' input[name="orderproducttableid[]"]').each(function(index){
                var id = this.id.match(/[\d\.]+/g);
                if(this.value!='' || this.value!=0){
                    $('.offerproducts_'+combinationid+' input[name="appliedpriceid[]"]').each(function(index){
                        appliedpriceid = this.value;
                        appliedpriceid = appliedpriceid.split(',');
                        console.log('appliedpriceid'+appliedpriceid);
                        console.log('orderpriceid'+orderpriceid);
                        if (JSON.stringify(appliedpriceid) == JSON.stringify(orderpriceid)){
                            var removeorderproductid = $('#removeorderproductid').val();
                            $('#removeorderproductid').val(removeorderproductid+','+$('input[name="orderproducttableid['+index+']').val());
                            return false;
                        }
                    });
                }
            }); */
            
            $('.offerproducts_'+combinationid).filter('[data-priceid="'+$(this).attr('data-priceid')+'"]').each(function(index){
                $('input[name="orderproducttableid[]"]', this).each(function(index){
                    if(this.value!='' || this.value!=0){
                        var removeorderproductid = $('#removeorderproductid').val();
                        $('#removeorderproductid').val(removeorderproductid+','+this.value);
                    }
                });
                
            });
            $('.offerproducts_'+combinationid).filter('[data-priceid="'+$(this).attr('data-priceid')+'"]').remove();
            $('#offerproductdetails'+combinationid).filter('[data-priceid="'+$(this).attr('data-priceid')+'"]').remove();
            //$('#offerproductdetails'+combinationid).remove();
        }else{
            var combinationid = $(this).attr('id').match(/\d+/);
           
            for(i=0;i<arr.length;i++){
                
                var ofpid = arr[i]['ofpid'];
                var ofpriceid = arr[i]['ofpriceid'];
                var ofqty = arr[i]['ofqty'];
                var multiplication = arr[i]['multiplication'];
                var ofdisctype = arr[i]['ofdisctype'];
                var ofdisc = arr[i]['ofdisc'];
                var oftax = arr[i]['oftax'];
                var ofprice = arr[i]['ofprice'];
                var purchaseproducts = arr[i]['purchaseproducts'];
                var minimumpurchaseamount = arr[i]['minimumpurchaseamount'];
                var offertype = arr[i]['offertype'];
                var MultiplyArray = [];
                var PRODUCT_AMOUNT = 0; 
                var orderpriceid = [];
                if(multiplication==1 && purchaseproducts.length > 0){
                    for(p=0;p<purchaseproducts.length;p++){
                        var pproductid = purchaseproducts[p][0];
                        var ppriceid = purchaseproducts[p][1];
                        var pqty = purchaseproducts[p][2];
                        var priceidarr = [];
                        if (ppriceid.indexOf(',') > -1) { 
                            priceidarr = ppriceid.split(',');
                            //priceidarr.push($("#priceid"+productrowid+" option:selected").attr("data-id"));
                        }else{
                            priceidarr.push(ppriceid);
                        }
                        
                        $('select.productid').each(function(index){
                            var divid = $(this).attr("div-id");
                            var oproductid = $("#productid"+divid).val();
                            var opriceid = $("#priceid"+divid+" option:selected").attr("data-id");
                            var oqty = $("#qty"+divid).val();
                            var productamount = $("#amount"+divid).val();
                            
                            if(oproductid!=0 && opriceid != "" && oqty!="" && parseFloat(productamount) != "" && oproductid==pproductid && priceidarr.includes(opriceid) && appliedpriceid.includes(opriceid)){
                                if(offertype==0){
                                    PRODUCT_AMOUNT += parseFloat(productamount);
                                }else{
                                    orderpriceid.push(opriceid);
                                    MultiplyArray.push(parseFloat(oqty / pqty)); 
                                }
                            }
                        });
                    }
                    if(offertype==0){
                        var orqty = parseFloat(parseFloat(PRODUCT_AMOUNT) / parseFloat(minimumpurchaseamount));
                    }else{
                        var orqty = Math.min.apply(Math,MultiplyArray);
                    }
                    ofqty = parseFloat(orqty) * ofqty;

                    for(p=0;p<purchaseproducts.length;p++){
                        var pproductid = purchaseproducts[p][0];
                        var ppriceid = purchaseproducts[p][1];
                        var pqty = purchaseproducts[p][2];
                        var priceidarr = [];
                        if (ppriceid.indexOf(',') > -1) { 
                            priceidarr = ppriceid.split(',');
                            //priceidarr.push($("#priceid"+productrowid+" option:selected").attr("data-id"));
                        }else{
                            priceidarr.push(ppriceid);
                        }
                        $('select.productid').each(function(index){
                            var divid = $(this).attr("div-id");
                            var oproductid = $("#productid"+divid).val();
                            var opriceid = $("#priceid"+divid+" option:selected").attr("data-id");
                            var oqty = $("#qty"+divid).val();
                            var productamount = $("#amount"+divid).val();
                            
                            if(oproductid!=0 && opriceid != "" && oqty!="" && parseFloat(productamount) != "" && oproductid==pproductid && priceidarr.includes(opriceid) && appliedpriceid.includes(opriceid)){
                                if(offertype==0){
                                    PRODUCT_AMOUNT += parseFloat(productamount);
                                }else{
                                    var purchaseproductqty = $("#purchaseproductqty"+divid).val();
                                    $("#purchaseproductqty"+divid).val((parseFloat(purchaseproductqty) + parseFloat(pqty)) * parseFloat(ofqty));
                                }
                            }
                        });
                    }
                }else{
                    for(p=0;p<purchaseproducts.length;p++){
                        var pproductid = purchaseproducts[p][0];
                        var ppriceid = purchaseproducts[p][1];
                        var pqty = purchaseproducts[p][2];
                        var priceidarr = [];
                        if (ppriceid.indexOf(',') > -1) { 
                            priceidarr = ppriceid.split(',');
                            //priceidarr.push($("#priceid"+productrowid+" option:selected").attr("data-id"));
                        }else{
                            priceidarr.push(ppriceid);
                        }
                        
                        $('select.productid').each(function(index){
                            var divid = $(this).attr("div-id");
                            var oproductid = $("#productid"+divid).val();
                            var opriceid = $("#priceid"+divid+" option:selected").attr("data-id");
                            var oqty = $("#qty"+divid).val();
                            
                            if(oproductid!=0 && opriceid != "" && oqty!="" && oproductid==pproductid && priceidarr.includes(opriceid) && appliedpriceid.includes(opriceid)){
                                if(offertype==1){
                                    orderpriceid.push(opriceid);
                                    var purchaseproductqty = $("#purchaseproductqty"+divid).val();
                                    $("#purchaseproductqty"+divid).val(parseFloat(purchaseproductqty) + parseFloat(pqty));
                                }
                            }
                        });
                    }
                }
                
                var amountexctax = parseFloat(ofprice) - (parseFloat(ofprice) * parseFloat(oftax) / (100 + parseFloat(oftax)));
                var amountinctax = parseFloat(ofprice);
                var amount = discountamount = discountper = 0;
                if(ofdisctype==1){
                    discountper = format.format(ofdisc);
                    discountamount = format.format(parseFloat(((parseFloat(amountinctax) * parseFloat(ofqty)) * parseFloat(ofdisc)) / 100).toFixed(2));
                    amount = parseFloat((parseFloat(amountinctax) * parseFloat(ofqty)) - parseFloat(((parseFloat(amountinctax) * parseFloat(ofqty)) * parseFloat(ofdisc)) / 100)).toFixed(2);
                }else{
                    discountamount = format.format(ofdisc);
                    discountper = parseFloat((parseFloat(ofdisc)*100)/(parseFloat(amountinctax) * parseFloat(ofqty))).toFixed(2);
                    amount = parseFloat((parseFloat(amountinctax) * parseFloat(ofqty)) - parseFloat(ofdisc)).toFixed(2);
                }   
                var producttaxamount = (parseFloat(amount) * parseFloat(oftax) / (100 + parseFloat(oftax)));
                if(parseFloat(amount) <= 0){
                    amount = parseFloat(0).toFixed(2);
                    producttaxamount = parseFloat(0).toFixed(2);
                }    

                /* $('.offerproducts_'+combinationid+' input[name="orderproducttableid[]"]').each(function(index){
                    var id = this.id.match(/[\d\.]+/g);
                    
                        $('.offerproducts_'+combinationid+' input[name="appliedpriceid[]"]').each(function(index){
                            appliedpriceid = this.value;
                            appliedpriceid = appliedpriceid.split(',');
                            
                            if (JSON.stringify(appliedpriceid) == JSON.stringify(orderpriceid)){
                                var removeorderproductid = $('#removeorderproductid').val();
                                $('#removeorderproductid').val(removeorderproductid+','+$('input[name="orderproducttableid['+index+']').val());
                                return false;
                            }
                        });
                    
                   
                }); */
                $("#postofferqtyspan"+combinationid+"_"+ofpriceid).html(ofqty);
                $("#postofferquantity"+combinationid+"_"+ofpriceid).val(ofqty);
                $("#postofferdiscountspan"+combinationid+"_"+ofpriceid).html(discountamount);
                $("#postofferdiscountperspan"+combinationid+"_"+ofpriceid).html(discountper);
                $("#postofferdiscountper"+combinationid+"_"+ofpriceid).val(discountper);
                
                $("#postofferproducttaxamount"+combinationid+"_"+ofpriceid).val(parseFloat(producttaxamount));
                $("#postofferamountspan"+combinationid+"_"+ofpriceid).html(parseFloat(amount).toFixed(2));
                $("#postofferamount"+combinationid+"_"+ofpriceid).val(parseFloat(amount).toFixed(2));
            }
        }
    });
    if($('.postcountoffers').length == 0){
        $("#displayofferproductsdata").hide();
    }
    var calcdiscount = ($("#overalldiscountamount").val()!=""?0:1);
    changenetamounttotal(calcdiscount);
}
function resetdata(){  
  
    $("#member_div").removeClass("has-error is-focused");
    $("#orderid_div").removeClass("has-error is-focused");
    $("#billingaddress_div").removeClass("has-error is-focused");
    $("#shippingaddress_div").removeClass("has-error is-focused");
    $("#quotationdate_div").removeClass("has-error is-focused");
    $("#transactionid_div").removeClass("has-error is-focused");
    $("#transactionproof_div").removeClass("has-error is-focused");
    $("#paymenttype_div").removeClass("has-error is-focused");
    $("#noofinstallment_div").removeClass("has-error is-focused");
    $("#emidate_div").removeClass("has-error is-focused");
    $("#emiduration_div").removeClass("has-error is-focused");
  
    if(ACTION==0){
        $('#memberid').val('0');
        $('#roundoff').html('0.00');
        $('.selectpicker').selectpicker('refresh');
        $('#partialpaymentoption,#transactionproof_div,#installmentmaindiv,#installmentdivs,#installmentsetting_div').hide();
        if(addordertype==0){
            getproduct(1);
        }
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
    
    var memberid = $("#memberid").val();
    var billingaddressid = $("#billingaddressid").val();
    var shippingaddressid = $("#shippingaddressid").val();
    var orderid = $("#orderid").val().trim();
    var orderdate = $("#orderdate").val();
    var ordertype = $("#ordertype").val().trim();
    var paymenttypeid = $("#paymenttypeid").val().trim();
    var transactionid = $("#transactionid").val().trim();
    var transactionproof = $('#textfile').val().trim();
    var noofinstallment = $('#noofinstallment').val().trim();
    var percentage = $("input[name='percentage[]']").map(function(){return $(this).val();}).get();
    
    var isvalidmemberid = isvalidorderid = isvalidpaymenttype = 0;
    var isvalidcategoryid = isvalidproductid = isvalidpriceid = isvalidcombopriceid = isvalidqty = isvalidamount = isvalidtransactionid = isvalidtransactionproof = isvalidinstallment = isvalidbillingaddressid = isvalidshippingaddressid = isvalidorderdate = isvalidduplicatecharges = isvalidextrachargesid = isvalidextrachargeamount = isvaliduniqueproducts = isvalidactualprice = 1;

    PNotify.removeAll();
    if(ordertype=='0'){
        if(memberid == 0){
          $("#member_div").addClass("has-error is-focused");
          new PNotify({title: 'Please select '+member_label+' !',styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidmemberid = 0;
        }else {
          isvalidmemberid = 1;
        }
    }else{
        isvalidmemberid = 1;
    }
    /* if(billingaddressid == 0){
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
    } */
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
       
        if($("#productid"+id).val() > 0 || $("#priceid"+id).val() > 0 || $("#combopriceid"+id).val() > 0 || $("#actualprice"+id).val() != "" || $("#qty"+id).val() == 0 || $("#amount"+id).val() > 0 || parseInt(id)==parseInt(firstproduct)){
            if($("#productid"+id).val() == 0){
                $("#product"+id+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(c)+' product !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidproductid = 0;
            }else {
                $("#product"+id+"_div").removeClass("has-error is-focused");
            }
            if($("#priceid"+id).val() == ""){
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
            }else{
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
            $("#productcategory"+id+"_div").removeClass("has-error is-focused");
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

    if(ordertype=='0'){
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
    
    var minimumorderamount = $("#memberid option:selected").attr("data-minimumorderamount");
    var payableamount = $("#inputnetamount").val();
    
    var isvalidminimumorderamount = 0;
    if(parseFloat(minimumorderamount) > 0 && payableamount!="" && parseFloat(payableamount) < parseFloat(minimumorderamount)){
        new PNotify({title: 'Require minimum order amount is '+format.format(parseFloat(minimumorderamount))+' '+CURRENCY_CODE+' !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }else{
        isvalidminimumorderamount = 1;
    }

    if(isvalidmemberid == 1 && isvalidbillingaddressid ==1 && isvalidorderid == 1 && isvalidcategoryid == 1 && isvalidproductid == 1 && isvalidpriceid == 1 && isvalidcombopriceid == 1 && isvalidactualprice==1 && isvalidqty == 1 && isvalidamount == 1 && isvalidpaymenttype == 1 && isvalidtransactionid == 1 && isvalidtransactionproof == 1 && isvalidinstallment == 1 && isvalidshippingaddressid == 1 && isvalidorderdate == 1 && isvalidextrachargesid == 1 && isvalidextrachargeamount == 1 && isvalidduplicatecharges == 1 && isvaliduniqueproducts == 1 && isvalidminimumorderamount == 1){
        validatedoffergrid();
        var formData = new FormData($('#orderform')[0]);
        if(ACTION==0){
            var uurl = SITE_URL+"order/add-order";
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
                    if(response==1){
                        new PNotify({title: "Order successfully added !",styling: 'fontawesome',delay: '3000',type: 'success'});
                        if(ordertype=='1'){
                            setTimeout(function() { window.location=SITE_URL+"purchase-order"; }, 1500);
                        }else{
                            setTimeout(function() { window.location=SITE_URL+"order"; }, 1500);
                        }
                    }else if(response==2){
                        new PNotify({title: "Order already exist !",styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else if(response==3){
                        new PNotify({title: "Quantity greater than stock quantity!",styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else if(response==0){
                        new PNotify({title: 'Order not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else{
                        new PNotify({title:response,styling: 'fontawesome',delay: '3000',type: 'error'});
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
            var uurl = SITE_URL+"order/update-order";
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
                if(response==1){
                    new PNotify({title: "Order successfully updated.",styling: 'fontawesome',delay: '3000',type: 'success'});
                    if(ordertype=='1'){
                        setTimeout(function() { window.location=SITE_URL+"purchase-order"; }, 1500);
                    }else{
                        setTimeout(function() { window.location=SITE_URL+"order"; }, 1500);
                    }
                }else if(response==2){
                    new PNotify({title: "Order already exist !",styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(response==3){
                    new PNotify({title: "Quantity greater than stock quantity !",styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(response==0){
                    new PNotify({title: 'Order not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
                }else{
                    new PNotify({title: response,styling: 'fontawesome',delay: '3000',type: 'error'});
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
        resetNewMemberForm();
        $('.modal-title').html('Add New '+Member_label);
        $('#addnewmemberModal').modal('show');
        $("#newmembercode").val(randomPassword(8,8,0,0,0));
    }else{
        var memberid = (ACTION==1)?$('#oldmemberid').val():$('#memberid').val();
        memberaddressresetdata();
        if(memberid!=0){
            if(type==1){
                $('.modal-title').html('Add '+Member_label+' Billing Address');
                $('#addressbtn').attr('onclick','memberaddresscheckvalidation(1)');
                $('#addressModal').modal('show');
            }else if(type==2){
            
                $('.modal-title').html('Add '+Member_label+' Shipping Address');
                $('#addressbtn').attr('onclick','memberaddresscheckvalidation(2)');
                $('#addressModal').modal('show');
            }
            $('#sameasbillingaddress').prop('checked',true);
            
        }else{
            $("#member_div").addClass("has-error is-focused");
            new PNotify({title: 'Please select '+member_label+' !',styling: 'fontawesome',delay: '3000',type: 'error'});
        }
    }
}
function memberaddressresetdata(){

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

function memberaddresscheckvalidation(type){
    
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
  
        var formData = new FormData($('#memberaddressform')[0]);
        var memberid = (ACTION==1)?$('#oldmemberid').val():$('#memberid').val();
        formData.append("memberid",memberid);
    
        var uurl = SITE_URL+"order/add-billing-address";
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
                    memberaddressresetdata();

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
function resetNewMemberForm(){

    $("#newchannelid_div").removeClass("has-error is-focused");
    $("#newmembername_div").removeClass("has-error is-focused");
    $("#newmobile_div").removeClass("has-error is-focused");
    $("#newmembercode_div").removeClass("has-error is-focused");
    $("#newemail_div").removeClass("has-error is-focused");
    $('#newcountry_div').removeClass("has-error is-focused");
    $('#newprovince_div').removeClass("has-error is-focused");
    $('#newcity_div').removeClass("has-error is-focused");
    $("#newgstno_div").removeClass("has-error is-focused");
    $("#newpanno_div").removeClass("has-error is-focused");

    if($('#newchannelid option').length == 2){
        $('#newchannelid').val($('#newchannelid option:last').val());      
    }else{
        $('#newchannelid').val(0);
    }
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
function addNewMember(){
    
    var channelid = $("#newchannelid").val();
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
   
    var isvalidname = isvalidchannelid = isvalidmembercode = isvalidcountrycodeid = isvalidmobileno = isvalidemail = isvalidgstno = isvalidpanno = isvalidcountryid = isvalidprovinceid = isvalidcityid = 1;;

    PNotify.removeAll();
    if(channelid == 0){
        $("#newchannelid_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select '+member_label+' channel !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidchannelid = 0;
    }else {
        $("#newchannelid_div").removeClass("has-error is-focused");
    }
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

    if(mobileno=="") {
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

    if(isvalidname == 1 && isvalidchannelid == 1 && isvalidmembercode == 1 && isvalidcountrycodeid == 1 && isvalidmobileno == 1 && isvalidemail == 1 && isvalidgstno == 1 && isvalidpanno == 1 && isvalidcountryid == 1 && isvalidprovinceid == 1 && isvalidcityid == 1){
        
        var formData = new FormData($('#addnewmemberform')[0]);
        var uurl = SITE_URL+"order/add-new-member";
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
                    new PNotify({title: Member_label+' Successfully Added !',styling: 'fontawesome',delay: '3000',type: 'success'});
                    
                    $('#addnewmemberModal').modal('hide');
                    resetNewMemberForm();

                    $('#memberid').append($('<option>', { 
                        value: obj['id'],
                        text : obj['text'],
                        "data-code" : obj['membercode'],
                        "data-billingid" : 0,
                        "data-shippingid" : 0,
                        "selected":"selected"
                    }));
                    $('#memberid').selectpicker('refresh');
                    $('#memberid').change();
                }else if(obj['error'] == 2) {
                    new PNotify({title: 'Mobile number already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(obj['error'] == 3) {
                    new PNotify({title: 'Email already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(obj['error'] == 6){
                    new PNotify({title: Member_label+' code already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(obj['error'] == 7){
                    new PNotify({title: 'Invalid email address !',styling: 'fontawesome',delay: '3000',type: 'error'});
                } else {
                    new PNotify({title: Member_label+' not Added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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