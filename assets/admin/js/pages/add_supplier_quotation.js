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
        // changenetamounttotal();
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

    $('#emidate,#quotationdate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        todayBtn:"linked",
    });
    $("#qty1").TouchSpin(touchspinoptions);
    if(ACTION==1){
        getbillingaddress();
    }
    if(ISDUPLICATE==1){
        getbillingaddress();
        getChannelSettingByMember();
    }
    /****MEMBER CHANGE EVENT****/
    $('#memberid').on('change', function (e) {
        getbillingaddress();
        getChannelSettingByMember();
        /* $(".producttaxamount").val('');
        $(".tax").val(''); */

        
        var oldmemberid = $("#oldmemberid").val();
        $('.applyoldprice').prop("checked",false);
        $('.applyoldprice').prop("disabled",true);
        if(ACTION==1 && (this.value == oldmemberid && this.value!=0)){
            $('#applyoldprice'+divid).prop("disabled",false);
            $('#applyoldprice'+divid).prop("checked",true);
        }
        changenetamounttotal();
        getproduct();
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

        var oldmemberid = $("#oldmemberid").val();
        var memberid = (ACTION==1)?oldmemberid:$("#memberid").val();
        
        $('#applyoldprice'+divid).prop("checked",false);
        $('#applyoldprice'+divid).prop("disabled",true);
        if(ACTION==1 && (this.value == oldproductid[divid-1] && this.value!=0) && memberid==oldmemberid){
            $('#applyoldprice'+divid).prop("disabled",false);
            $('#applyoldprice'+divid).prop("checked",true);
        }
        
        $("#qty"+divid).val('1');
        $("#qty"+divid).trigger("touchspin.updatesettings", {min: 1, step: 1});
        $("#qty"+divid).prop("readonly",false);

        $("#discount"+divid+",#discountinrs"+divid+", #amount"+divid).val('');
        
        changeproductamount(divid);
        changeextrachargesamount();
        
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

        var oldmemberid = $("#oldmemberid").val();
        var memberid = (ACTION==1)?oldmemberid:$("#memberid").val();
        var productid = $("#productid"+divid).val();
        $('#applyoldprice'+divid).prop("checked",false);
        $('#applyoldprice'+divid).prop("disabled",true);
        if(ACTION==1 && (this.value == oldpriceid[divid-1] && this.value!="") && productid==oldproductid[divid-1] && memberid==oldmemberid){
            $('#applyoldprice'+divid).prop("disabled",false);
            $('#applyoldprice'+divid).prop("checked",true);
        }
        var discount = $("#priceid"+divid+" option:selected").attr("data-discount");
        if(discount > 0){
            $("#discount"+divid).val(discount);
        } */
        // $("#discount"+divid).val('');
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
            
            var oldmemberid = $("#oldmemberid").val();
            var memberid = (ACTION==1)?oldmemberid:$("#memberid").val();
            var productid = $("#productid"+divid).val();
            var priceid = $("#priceid"+divid).val();
            $('#applyoldprice'+divid).prop("checked",false);
            $('#applyoldprice'+divid).prop("disabled",true);
       
            if(ACTION==1 && (parseFloat(actualprice).toFixed(2) == parseFloat(oldprice[divid-1]).toFixed(2) && actualprice!="") && productid==oldproductid[divid-1] && memberid==oldmemberid && priceid==oldpriceid[divid-1]){
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
        if(partialpayment==1 && EMIreceived==0 && $("#installmentdivs").html()!=""){
            generateinstallment();
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
                            // $("#qty"+divid).trigger("touchspin.updatesettings", {min: parseFloat(price_qty), step: parseFloat(price_qty)});
                            
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
function getChannelSettingByMember(){

    var memberid = (ACTION==1)?$('#oldmemberid').val():$('#memberid').val();

    if(memberid!='' && memberid!=0){
        var uurl = SITE_URL+"member/getChannelSettingsByMember";
        $.ajax({
          url: uurl,
          type: 'POST',
          data: {memberid:String(memberid), ordertype:0},
          dataType: 'json',
          async: false,
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
   
    if(memberid!='' && memberid!=0){
      var uurl = SITE_URL+"quotation/getBillingAddresstByMemberId";
        
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {memberid:String(memberid)},
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
                }else if(BillingAddressID!=0 && ACTION==0){
                    $('#billingaddressid').val(BillingAddressID);
                }
                if(shippingaddressid!=0 && (ACTION==1 || ISDUPLICATE==1)){
                    $('#shippingaddressid').val(shippingaddressid);
                }else if(ShippingAddressID!=0 && ACTION==0){
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
        $('#priceid'+divid).selectpicker('refresh');

        var element = $('#productid'+divid);
        //$("#qty"+divid).val('1');
        //$("#discount"+divid+", #amount"+divid).val('');
        
    }
    var memberid = $("#memberid").val();

    if(memberid!='' && memberid!=0){
      var uurl = SITE_URL+"product/getProductByCategoryId";
      salesproducthtml = "";
      
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {memberid:memberid},
        dataType: 'json',
        async: false,
        success: function(response){
  
            var NewProduct = [];
            for(var i = 0; i < response.length; i++) {
  
                var productname = response[i]['name'].replace("'","&apos;");
                if(DROPDOWN_PRODUCT_LIST==0){
                    
                    element.append($('<option>', { 
                        value: response[i]['id'],
                        text : productname,
                        "data-pointsforbuyer" : response[i]['pointsforbuyer'],
                        "data-pointsforseller" : response[i]['pointsforseller']
                    }));
      
                    salesproducthtml += "<option data-pointsforbuyer='"+response[i]['pointsforbuyer']+"' data-pointsforseller='"+response[i]['pointsforseller']+"' value="+response[i]['id']+">"+productname+"</option>";
                }else{
                    
                    element.append($('<option>', { 
                        value: response[i]['id'],
                        text : productname,
                        "data-pointsforbuyer" : response[i]['pointsforbuyer'],
                        "data-pointsforseller" : response[i]['pointsforseller'],
                        "data-content" :'<img src="'+PRODUCT_PATH+response[i]['image']+'" style="width:40px">  ' + productname
                    }));

                    salesproducthtml += '<option data-content="<img src=&apos;'+PRODUCT_PATH+response[i]['image']+'&apos; style=&apos;width:40px&apos;>  '+productname+'" data-pointsforbuyer="'+response[i]['pointsforbuyer']+'" data-pointsforseller="'+response[i]['pointsforseller']+'" value='+response[i]['id']+'>'+productname+'</option>';
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

                        $("#qty"+divid).val('1');
                        $("#actualprice"+divid).val('');
                        $("#discount"+divid+",#discountinrs"+divid+",#amount"+divid+",#tax"+divid+",#ordertax"+divid+",#uniqueproduct"+divid).val('');
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
   
    if(productids.length > 0){
        for(var i = 0; i < productids.length; i++) {

            var productid = productids[i];
            if(productid!=''){
                var uurl = SITE_URL+"order/getVariantByProductId";
                var memberid = $("#memberid").val();
                
                $.ajax({
                    url: uurl,
                    type: 'POST',
                    data: {productid:String(productid),ordertype:0,memberid:memberid},
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
                                        "data-id" : response[i]['priceid'],
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
      var uurl = SITE_URL+"order/getVariantByProductId";
      var memberid = $("#memberid").val();
      
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {productid:String(productid),ordertype:0,memberid:memberid},
        dataType: 'json',
        async: false,
        success: function(response){
            var len = response.length;
            for(var i = 0; i < response.length; i++) {
                if(ACTION==1){
                    if(typeof(response[i]['universal'])!='undefined'){
                        $('#priceid'+divid).append($('<option>', { 
                            value: response[i]['id'],
                            text : response[i]['variantname'],
                            "data-id" : response[i]['priceid'],
                            "data-pricetype" : response[i]['pricetype'],
                            "data-quantitytype" : response[i]['quantitytype'],
                            "data-referencetype" : response[i]['referencetype']
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
                            "data-referencetype" : response[i]['referencetype']
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
                }  
                $('#producttax'+divid).val(response[i]['tax']);

            }
            
            if(oldpriceid[divid-1]!="undefined"){
                $('#priceid'+divid).val(oldpriceid[divid-1]);
            }
            if(len==1){
                $('#priceid'+divid).val(response[0]['id']).selectpicker('refresh');
                $('#priceid'+divid).change();
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
        $.ajax({
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

function addnewproduct() {
    var cloopdoc = $("#cloopdoc").val();
    cloopdoc++;
    $("#cloopdoc").val(cloopdoc);
    var datahtml = '<div class="col-md-12">\
    <div class="col-sm-12 countdocuments pl-sm pr-sm" id="countdocuments' + cloopdoc + '">\
                      <input type="hidden" name="doc_id_' + cloopdoc + '" value="0" id="doc_id_' + cloopdoc + '">\
                      <div class="col-md-5 col-sm-5">\
                          <div class="form-group" id="documentnumber_' + cloopdoc + '">\
                              <div class="col-sm-12 pr-xs pl-xs">\
                                  <input id="documentname_' + cloopdoc + '" name="documentname_' + cloopdoc + '" placeholder="Enter Document Name" class="form-control documentnumber">\
                              </div>\
                          </div>\
                      </div>\
                      <div class="col-md-5 col-sm-5">\
                          <div class="form-group" id="docfile' + cloopdoc + '">\
                              <div class="col-sm-12 pr-xs pl-xs">\
                                  <input type="hidden" id="isvaliddocfile' + cloopdoc + '" value="0">\
                                  <input type="hidden" name="olddocfile[' + cloopdoc + ']" id="olddocfile' + cloopdoc + '" value="">\
                                  <div class="input-group" id="fileupload' + cloopdoc + '">\
                                      <span class="input-group-btn" style="padding: 0 0px 0px 0px;">\
                                          <span class="btn btn-primary btn-raised btn-file">\
                                          <i class="fa fa-upload"></i>\
                                              <input type="file" name="olddocfile_' + cloopdoc + '" class="docfile" id="olddocfile_' + cloopdoc + '" accept=".png,.jpeg,.jpg,.bmp,.gif,.pdf" onchange="validdocumentfile($(this),&apos;docfile' + cloopdoc + '&apos;)">\
                                          </span>\
                                      </span>\
                                      <input type="text" readonly="" placeholder="Enter File" id="Filetextdocfile' + cloopdoc + '" class="form-control docfile" name="Filetextdocfile_' + cloopdoc + '" value="">\
                                  </div>\
                              </div>\
                          </div>\
                      </div>\
                      <div class="col-md-1 addrowbutton pt-md pr-xs">\
                          <button type="button" class="btn btn-danger btn-raised remove_btn m-n" onclick="removeDocument(' + cloopdoc + ')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
                      </div>\
                      </div>\
                  </div>';

    $("#adddocrow").append(datahtml);
}

function removeDocument(divid) {

    $("#countdocuments" + divid).remove();

    $(".add_btn:last").show();
    if ($(".remove_btn:visible").length == 1) {
        $(".remove_btn:first").show();
    }
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
    if(partialpayment==1 && EMIreceived==0 && $("#installmentdivs").html()!=""){
        generateinstallment();
    }
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
        }
        if(calcdiscount==1){
            $("#overalldiscountpercent").val('');
            $("#overalldiscountamount").val('');
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
        
        if($("#overalldiscountpercent").val()!='' || $("#overalldiscountamount").val()!='' || parseFloat($("#overalldiscountpercent").val()) > 0 || parseFloat($("#overalldiscountamount").val()) > 0){
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
                        getproduct();
                        
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
    if(partialpayment==1 && EMIreceived==0){
        generateinstallment();
    }
}
function openmodal(type){
    
    PNotify.removeAll();

    var memberid = (ACTION==1)?$('#oldmemberid').val():$('#memberid').val();
    memberaddressresetdata();

    if(memberid!=0){
        if(type==1){
            $('.modal-title').html('Add '+Member_label+' Billing Address');
            $('#addressbtn').attr('onclick','memberaddresscheckvalidation(1)');
            $('#addressModal').modal('show');
        } else if(type==2) {
            $('.modal-title').html('Add '+Member_label+' Shipping Address');
            $('#addressbtn').attr('onclick','memberaddresscheckvalidation(2)');
            $('#addressModal').modal('show');
        }
        $('#sameasbillingaddress').prop('checked',true);
    } else {
        $("#member_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select '+member_label+' !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
    
        var uurl = SITE_URL+"quotation/add-billing-address";
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
function resetdata(){  
  
    $("#member_div").removeClass("has-error is-focused");
    $("#orderid_div").removeClass("has-error is-focused");
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
        $('#memberid').val('0');
        $('#paymenttypeid').val('0');
        $('#partialpaymentoption,#installmentmaindiv,#installmentdivs,#installmentsetting_div').hide();
        getproduct(1);
        
        $('.selectpicker').selectpicker('refresh');

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
    
    var memberid = $("#memberid").val();
    var billingaddressid = $("#billingaddressid").val();
    var shippingaddressid = $("#shippingaddressid").val();
    var quotationid = $("#quotationid").val().trim();
    var quotationdate = $("#quotationdate").val();
    var paymenttypeid = $("#paymenttypeid").val().trim();
    var noofinstallment = $('#noofinstallment').val().trim();
    var percentage = $("input[name='percentage[]']").map(function(){return $(this).val();}).get();

    var isvalidmemberid = isvalidquotationid = isvalidpaymenttype = 0;
    var isvalidproductid = isvalidpriceid = isvalidcombopriceid = isvalidqty = isvalidamount = isvalidinstallment = isvalidbillingaddressid = isvalidshippingaddressid = isvalidquotationdate = isvalidduplicatecharges = isvalidextrachargesid = isvalidextrachargeamount = isvaliduniqueproducts = isvalidactualprice = 1;

    PNotify.removeAll();
    if(memberid == 0){
        $("#member_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select '+member_label+' !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmemberid = 0;
    }else {
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
       
        if($("#productid"+id).val() > 0 || $("#priceid"+id).val() > 0 || $("#combopriceid"+id).val() > 0 || $("#actualprice"+id).val() != "" || $("#qty"+id).val() == 0 || $("#amount"+id).val() > 0 || id==1){
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
                // $("#price"+id+"_div").removeClass("has-error is-focused");
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
            // $("#price"+id+"_div").removeClass("has-error is-focused");
            $("#comboprice"+id+"_div").removeClass("has-error is-focused");
            $("#qty"+id+"_div").removeClass("has-error is-focused");
            $("#actualprice"+id+"_div").removeClass("has-error is-focused");
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
   
    if(isvalidmemberid == 1 && isvalidbillingaddressid ==1 && isvalidquotationid == 1 && isvalidproductid == 1 && isvalidpriceid == 1 && isvalidcombopriceid == 1 && isvalidactualprice==1 && isvalidqty == 1 && isvalidamount == 1 && isvalidpaymenttype == 1 && isvalidinstallment == 1 && isvalidshippingaddressid == 1 && isvalidquotationdate == 1 && isvalidextrachargesid == 1 && isvalidextrachargeamount == 1 && isvalidduplicatecharges == 1 && isvaliduniqueproducts == 1){
  
        var formData = new FormData($('#quotationform')[0]);
        if(ACTION==0){
            var uurl = SITE_URL+"quotation/add-quotation";
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
                        new PNotify({title: "Quotation successfully added !",styling: 'fontawesome',delay: '3000',type: 'success'});
                        setTimeout(function() { window.location=SITE_URL+"quotation"; }, 1500);
                    }else if(response==2){
                        new PNotify({title: "Quotation already exist !",styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else if(response==3){
                        new PNotify({title: "Quantity greater than stock quantity !",styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else{
                        new PNotify({title: 'Quotation not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
            var uurl = SITE_URL+"quotation/update-quotation";
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
                    new PNotify({title: "Quotation successfully updated.",styling: 'fontawesome',delay: '3000',type: 'success'});
                    setTimeout(function() { window.location=SITE_URL+"quotation"; }, 1500);
                }else if(response==2){
                    new PNotify({title: "Quotation already exist !",styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(response==3){
                    new PNotify({title: "Quantity greater than stock quantity !",styling: 'fontawesome',delay: '3000',type: 'error'});
                }else{
                    new PNotify({title: 'Quotation not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
