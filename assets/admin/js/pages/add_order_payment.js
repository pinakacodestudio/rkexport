$(document).ready(function() {
    $('#transactiondate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        orientation: 'top',
        autoclose: true,
        todayBtn: "linked"
    });

    $(".add_invoice_btn").hide();
    $(".add_invoice_btn:last").show();

    $('input:radio[name=isagainstreference]').change(function () {
        if ($("input[name='isagainstreference']:checked").val() == 1) {
            $("#invoicedetailsdiv").show();
            $("#amount").prop("readonly",true);
            if(ACTION==1 && INVOICEID_ARR.length == 0){
                $('.invoiceamount,.amountdue,.remainingamount').val('');
            }
        }else {
            $("#invoicedetailsdiv").hide();
            $("#amount").prop("readonly",false);
        }
    });
    if(ACTION==1){
        getPurchaseInvoiceByVendor();
        
        $('select.invoiceid').each(function(){
            var divid = $(this).attr("id").match(/\d+/);
            
            if(INVOICEID_ARR[divid-1]!=0){
                $('#invoiceid'+divid).val(INVOICEID_ARR[divid-1]);
                $('#invoiceid'+divid).selectpicker('refresh');
            }
            var amountdue = $('#invoiceid'+divid+' option:selected').attr('data-invoiceamount');
            $("#amountdue"+divid).val(parseFloat(amountdue).toFixed(2));

            var receiptamount = $('#invoiceamount'+divid).val();
            var remainingamount = (parseFloat(amountdue) - parseFloat(receiptamount));
            if(remainingamount<=0){
                remainingamount=0;
            }
            $("#remainingamount"+divid).val(parseFloat(remainingamount).toFixed(2));
        });
    }
    /****VENDOR CHANGE EVENT****/
    $('#Partyid').on('change', function (e) {
        getPurchaseInvoiceByVendor();
    });

    /****INVOICE CHANGE EVENT****/
    $('.invoiceid').on('change', function (e) {
        var divid = $(this).attr("id").match(/\d+/);
        $("#amountdue"+divid+",#invoiceamount"+divid+",#remainingamount"+divid).val('');
        if(this.value!=0){
            var invoiceamount = $("#invoiceid"+divid+" option:selected").attr("data-invoiceamount");

            $("#amountdue"+divid).val(parseFloat(invoiceamount).toFixed(2));
        }
        calculateamount();
    });

    /****AMOUNT KEYUP EVENT****/
    $(".invoiceamount").on('keyup', function (e) {
        var divid = $(this).attr("id").match(/\d+/);
        var amountdue = $("#amountdue"+divid).val();
        
        if(amountdue!="" && this.value!=""){
            if(parseFloat(this.value) > parseFloat(amountdue)){
                $(this).val(parseFloat(amountdue).toFixed(2));
            }

            var remainingamount = parseFloat(amountdue) - parseFloat(this.value);
            $("#remainingamount"+divid).val(parseFloat(remainingamount).toFixed(2));
        }  
        calculateamount();
    });
    
    $('#cashorbankid').on('change', function (e) {
        var value = $('#cashorbankid option:selected').text()

        $("#method option").removeAttr('disabled');
        if(value.toLowerCase().trim() == "cash" && this.value!=0){
            $("#method option[value!=1][value!=0]").prop('disabled', true);
            $("#method").val('1');
        }else if(value.toLowerCase().trim() != "cash" && this.value!=0){
            $("#method option[value=1]").prop('disabled', true);
            $("#method").val('0');
        }else{
            $("#method").val('0');
        }
        $("#method").selectpicker('refresh');
    });
    
    if(ACTION==1){
        if(method > 1){
            $("#method option[value=1]").prop('disabled', true);
        }else{
            $("#method option[value!=1][value!=0]").prop('disabled', true);
        }
        $("#method").selectpicker('refresh');
    }
});
function calculateamount(){
    var amount = 0;
    $('.invoiceamount').each(function(){
        if(this.value!=""){
            amount += parseFloat(this.value);
        }
    });
    $("#amount").val(parseFloat(amount).toFixed(2));
}
function getPurchaseInvoiceByVendor(divid=""){
    
    var element = $('select.invoiceid');
    if(divid!=""){
        element = $('#invoiceid'+divid);
    }
    element.find('option')
        .remove()
        .end()
        .append('<option value="0">Select Invoice</option>')
        .val('0')
    ;
    
    element.selectpicker('refresh');
  
    var Partyid = (ACTION==0)?$("#Partyid").val():Partyid;
    var type = $("input[name='isagainstreference']:checked").val();

    if(Partyid!=0 && type == 1){
      var uurl = SITE_URL+"purchase-invoice/getPurchaseInvoiceByVendor";
     
      var param = {Partyid:String(Partyid)}; 
      if(ACTION==1 && $("#paymentreceiptid").val()!=""){
        param = {Partyid:String(Partyid),paymentreceiptid:String($("#paymentreceiptid").val())}
      }
      $.ajax({
        url: uurl,
        type: 'POST',
        data: param,
        dataType: 'json',
        async: false,
        success: function(response){
                
            for(var i = 0; i < response.length; i++) {
    
                element.append($('<option>', { 
                    value: response[i]['id'],
                    text : response[i]['invoiceno'],
                    "data-invoiceamount" : response[i]['invoiceamount']
                }));

                invoiceoptions += "<option value='"+response[i]['id']+"' data-invoiceamount='"+response[i]['invoiceamount']+"'>"+response[i]['invoiceno']+"</option>";
            }
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
      });
    }
    element.selectpicker('refresh');
}
function addnewinvoicetransaction(){

    var rowcount = parseInt($(".countinvoice:last").attr("id").match(/\d+/))+1;
    var datahtml = '<div class="countinvoice" id="countinvoice'+rowcount+'">\
                    <div class="row m-n">\
                        <div class="col-md-3">\
                            <div class="form-group" id="invoice'+rowcount+'_div">\
                                <div class="col-sm-12">\
                                    <select id="invoiceid'+rowcount+'" name="invoiceid[]" class="selectpicker form-control invoiceid" data-live-search="true" data-select-on-tab="true" data-size="6">\
                                        <option value="0">Select Invoice</option>\
                                        '+invoiceoptions+'\
                                    </select>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-2">\
                            <div class="form-group" id="amountdue'+rowcount+'_div">\
                                <div class="col-md-12">\
                                    <input type="text" id="amountdue'+rowcount+'" class="form-control text-right amountdue" value="" readonly>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-2">\
                            <div class="form-group" id="invoiceamount'+rowcount+'_div">\
                                <div class="col-md-12">\
                                    <input type="text" id="invoiceamount'+rowcount+'" class="form-control invoiceamount text-right" name="invoiceamount[]" value="" onkeypress="return decimal_number_validation(event, this.value, 10)">\
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
    $("#invoiceid"+rowcount).on('change', function (e) {
        var divid = $(this).attr("id").match(/\d+/);
        $("#amountdue"+divid+",#invoiceamount"+divid+",#remainingamount"+divid).val('');
        if(this.value!=0){
            var invoiceamount = $("#invoiceid"+divid+" option:selected").attr("data-invoiceamount");

            $("#amountdue"+divid).val(parseFloat(invoiceamount).toFixed(2));
        }
        calculateamount();
    });

    /****AMOUNT KEYUP EVENT****/
    $("#invoiceamount"+rowcount).on('keyup', function (e) {
        var divid = $(this).attr("id").match(/\d+/);
        var amountdue = $("#amountdue"+divid).val();
        
        if(amountdue!="" && this.value!=""){
            if(parseFloat(this.value) > parseFloat(amountdue)){
                $(this).val(parseFloat(amountdue).toFixed(2));
            }

            var remainingamount = parseFloat(amountdue) - parseFloat(this.value);
            $("#remainingamount"+divid).val(parseFloat(remainingamount).toFixed(2));
        }
        calculateamount();
    });

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
function resetdata(){
 
  $("#vendor_div").removeClass("has-error is-focused");
  $("#transactiondate_div").removeClass("has-error is-focused");
  $("#paymentreceiptno_div").removeClass("has-error is-focused");
  $("#cashorbankid_div").removeClass("has-error is-focused");
  $("#method_div").removeClass("has-error is-focused");
  $("#amount_div").removeClass("has-error is-focused");

  if(ACTION==0)
  {
    $('#Partyid').val(0);
    $('#cashorbankid').val(0);
    $('#method').val(0);
    $('#amount').val('');
    $('#remarks').val('');
    getPurchaseInvoiceByVendor();
    $('.invoiceamount,.amountdue,.remainingamount').val('');
    var i=1;
    $('.countinvoice').each(function(){
        var id = $(this).attr('id').match(/\d+/);
        if(id!=1){
            $('#countinvoice'+id).remove();
        }
        i++;
    });
    $('.add_invoice_btn:first').show();
    $('.remove_invoice_btn').hide();
   
  }else{
    $('#cashorbankid').val(cashorbankid);
    $('#method').val(method);
  }
  $('.selectpicker').selectpicker('refresh');
  $('html, body').animate({scrollTop:0},'slow');  
}
function checkvalidation(addtype=0){

    var Partyid = $("#Partyid").val();
    var purchaseno = $("#purchaseno").val();
    var cashorbankid = $("#cashorbankid").val();
    var method = $("#method").val();
    var amount = $("#amount").val();

 
    var isagainstreference = $("input[name=isagainstreference]:checked").val();    
  
    var isvalidPartyid = isvalidtransactiondate = isvalidpaymentreceiptno  = 1;

    PNotify.removeAll();
    
    if(Partyid==0) {
        $("#Party_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select Party !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidPartyid = 0;
    } else {
        $("#Party_div").removeClass("has-error is-focused");
    }
    if(purchaseno==""){
        $("#purchaseno_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select purchase no !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidpurchaseno = 0;
    } else {
        $("#purchaseno_div").removeClass("has-error is-focused");
    }
    if(cashorbankid=="" || cashorbankid==0){
        $("#cashorbankid_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select Cash / Bank Account !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidpurchaseno = 0;
    } else {
        $("#cashorbankid_div").removeClass("has-error is-focused");
    }
    if(method=="" || method==0){
        $("#method_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select method !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidpurchaseno = 0;
    } else {
        $("#method_div").removeClass("has-error is-focused");
    }
    if(amount=="" ){
        $("#amount_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select amount !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidpurchaseno = 0;
    } else {
        $("#amount_div").removeClass("has-error is-focused");
    }

   
   
    if(isagainstreference==1){
        var c=1;
        var firstid = $('.countinvoice:first').attr('id').match(/\d+/);
      
        $('.countinvoice').each(function(){
            var id = $(this).attr('id').match(/\d+/);
            
            if($("#invoiceid"+id).val() > 0 || $("#invoiceamount"+id).val() != "" || parseInt(firstid)==parseInt(id)){
                if($("#invoiceid"+id).val() == 0){
                    $("#invoice"+id+"_div").addClass("has-error is-focused");
                    new PNotify({title: 'Please select '+(c)+' invoice !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    isvalidinvoiceid = 0;
                }else {
                    $("#invoice"+id+"_div").removeClass("has-error is-focused");
                }
                if($("#invoiceamount"+id).val() == ""){
                    $("#invoiceamount"+id+"_div").addClass("has-error is-focused");
                    new PNotify({title: 'Please enter '+(c)+' invoice amount !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    isvalidinvoiceamount = 0;
                }else {
                    $("#invoiceamount"+id+"_div").removeClass("has-error is-focused");
                }
            } else{
                $("#invoice"+id+"_div").removeClass("has-error is-focused");
                $("#invoiceamount"+id+"_div").removeClass("has-error is-focused");
            }
            c++;
        });

        var selects = $('select[name="invoiceid[]"]');
        var values = [];
        for(j=0;j<selects.length;j++) {
            var selectsinvoice = selects[j];
            var id = selectsinvoice.id.match(/\d+/);
            
            if(selectsinvoice.value!=0){
                if(values.indexOf(selectsinvoice.value)>-1) {
                    $("#invoice"+id[0]+"_div").addClass("has-error is-focused");
                    new PNotify({title: 'Please select '+(j+1)+' is different invoice !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    isvalidduplicateinvoice = 0;
                }
                else{ 
                    values.push(selectsinvoice.value);
                    if($("#invoiceid"+id[0]).val()!=0){
                        $("#invoice"+id[0]+"_div").removeClass("has-error is-focused");
                    }
                }
            }
        }
    }

    if(isvalidPartyid==1 && isvalidtransactiondate==1 && isvalidpaymentreceiptno==1 && isvalidcashorbankid==1 && isvalidmethod==1 && isvalidamount==1 && isvalidinvoiceid==1 && isvalidinvoiceamount==1 && isvalidduplicateinvoice == 1){
            
        var formData = new FormData($('#paymentform')[0]);
        if(ACTION == 0){    
        var uurl = SITE_URL+"payment/add-payment";
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
                new PNotify({title: 'Payment successfully added.',styling: 'fontawesome',delay: '3000',type: 'success'});
                if(addtype==1){
                    resetdata();
                    $('#paymentreceiptno').val(data['paymentreceiptno']);
                }else{
                    setTimeout(function() { window.location=SITE_URL+'payment'; }, 1500);
                }
            }else if(data['error']==2) {
                new PNotify({title: 'Payment account already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else if(data['error']==3) {
                new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
            } else {
                new PNotify({title: 'Payment not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
        var uurl = SITE_URL+"payment/update-payment";
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
                    new PNotify({title: 'Payment successfully updated.',styling: 'fontawesome',delay: '3000',type: 'success'});
                    setTimeout(function() { window.location=SITE_URL+'payment'; }, 1500);
                }else if(data['error']==2) {
                    new PNotify({title: 'Payment account already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(data['error']==3) {
                    new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
                } else {
                    new PNotify({title: 'Payment not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

function addnewproduct(){

    // productoptionhtml = salesproducthtml;
    // if(PRODUCT_DISCOUNT==0){
    //     discount = "display:none;";
    // }else{ 
    //     discount = "display:block;"; 
    // }
    // var readonly = "readonly";
    // if(EDITTAXRATE_CHANNEL==1 && EDITTAXRATE_SYSTEM==1){
    //     readonly = "";
    // }
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
                        <option value="0">Select Purchase Invoice</option>\
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
            <div class="form-group" id="amount'+divcount+'_div">\
                <div class="col-sm-12">\
                    <input type="text" class="form-control amounttprice" id="amount'+divcount+'" name="amount[]" value="" readonly="" div-id="'+divcount+'">\
                    <input type="hidden" class="producttaxamount" id="producttaxamount'+divcount+'" name="producttaxamount[]" value="" div-id="'+divcount+'">\
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

    // $("#qty"+divcount).TouchSpin(touchspinoptions);

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