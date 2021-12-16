$(document).ready(function() {
    $("#channelid").change(function(){
        getmembers();
        getTransactionPrefixData();
    });
    $("#memberid").change(function(){
        getTransactionPrefixData();
    });

    $('.yesno input[type="checkbox"]').bootstrapToggle({
        on: 'Yes',
        off: 'No',
        onstyle: 'primary',
        offstyle: 'danger'
    });
    $('.yesno input[type="checkbox"]').change(function() {
        var id = $(this).attr("id");
        if($(this).prop("checked") == false){
            $("#"+id+"format,#"+id+"lastno,#"+id+"suffixlength,#"+id+"btn1,#"+id+"btn2,#"+id+"btn3").prop("disabled",true);
            $("#"+id+"suffixlength").selectpicker('refresh');
        }else{
            $("#"+id+"format,#"+id+"lastno,#"+id+"suffixlength,#"+id+"btn1,#"+id+"btn2,#"+id+"btn3").prop("disabled",false);
            $("#"+id+"suffixlength").selectpicker('refresh');
        }
        $("#"+id+"format_div").removeClass("has-error is-focused");
        diplaytransactionformatpreview(id);
    });
    $(".suffixlength").change(function(){
        var id = $(this).attr("data-id");
        diplaytransactionformatpreview(id);
    });

    getTransactionPrefixData();
});
$(document).on("keyup",".transactionformat", function(){

    var transactionprefixnm = $(this).attr("data-id");
    diplaytransactionformatpreview(transactionprefixnm);
});
function getTransactionPrefixData(){
    var channelid = $("#channelid").val();
    var memberid = $("#memberid").val();

    if(channelid==0 || (channelid!=0 && memberid!=0)){

        var uurl = SITE_URL+"transaction-prefix/getTransactionPrefixData";
        $.ajax({
          url: uurl,
          type: 'POST',
          data: {channelid:channelid,memberid:memberid},
          dataType: 'json',
          async: false,
          success: function(response){
    
           if(response.length > 0){
            for(var i = 0; i < response.length; i++) {
                var type = response[i]['transactiontype'];
                var transactionprefix = response[i]['transactionprefix'];
                var transactionprefixformat = response[i]['transactionprefixformat'];
                var lastno = response[i]['lastno'];
                var suffixlength = response[i]['suffixlength'];

                if(type == 0){
                    if(transactionprefix==1){
                        $('input[name="quotationprefix"]').bootstrapToggle('on');
                        $("#quotationprefixformat,#quotationprefixlastno,#quotationprefixsuffixlength,#quotationprefixbtn1,#quotationprefixbtn2,#quotationprefixbtn3").prop("disabled",false);
                        $('#quotationprefixformat').val(transactionprefixformat);
                        $('#quotationprefixlastno').val(lastno);
                        $('#quotationprefixsuffixlength').val(suffixlength).selectpicker('refresh');
                    }else{
                        $('input[name="quotationprefix"]').bootstrapToggle('off');
                        $("#quotationprefixformat,#quotationprefixlastno,#quotationprefixsuffixlength,#quotationprefixbtn1,#quotationprefixbtn2,#quotationprefixbtn3").prop("disabled",true);
                        $('#quotationprefixformat,#quotationprefixlastno').val("");
                        $('#quotationprefixsuffixlength').val("1").selectpicker('refresh');
                    }
                    diplaytransactionformatpreview('quotationprefix');
                }
                if(type == 6){
                    if(transactionprefix==1){
                        $('input[name="purchasequotationprefix"]').bootstrapToggle('on');
                        $("#purchasequotationprefixformat,#purchasequotationprefixlastno,#purchasequotationprefixsuffixlength,#purchasequotationprefixbtn1,#purchasequotationprefixbtn2,#purchasequotationprefixbtn3").prop("disabled",false);
                        $('#purchasequotationprefixformat').val(transactionprefixformat);
                        $('#purchasequotationprefixlastno').val(lastno);
                        $('#purchasequotationprefixsuffixlength').val(suffixlength).selectpicker('refresh');
                    }else{
                        $('input[name="purchasequotationprefix"]').bootstrapToggle('off');
                        $("#purchasequotationprefixformat,#purchasequotationprefixlastno,#purchasequotationprefixsuffixlength,#purchasequotationprefixbtn1,#purchasequotationprefixbtn2,#purchasequotationprefixbtn3").prop("disabled",true);
                        $('#purchasequotationprefixformat,#purchasequotationprefixlastno').val("");
                        $('#purchasequotationprefixsuffixlength').val("1").selectpicker('refresh');
                    }
                    diplaytransactionformatpreview('purchasequotationprefix');
                }
                if(type == 1){
                    if(transactionprefix==1){
                        $('input[name="orderprefix"]').bootstrapToggle('on');
                        $("#orderprefixformat,#orderprefixlastno,#orderprefixsuffixlength,#orderprefixbtn1,#orderprefixbtn2,#orderprefixbtn3").prop("disabled",false);
                        $('#orderprefixformat').val(transactionprefixformat);
                        $('#orderprefixlastno').val(lastno);
                        $('#orderprefixsuffixlength').val(suffixlength).selectpicker('refresh');
                    }else{
                        $('input[name="orderprefix"]').bootstrapToggle('off');
                        $("#orderprefixformat,#orderprefixlastno,#orderprefixsuffixlength,#orderprefixbtn1,#orderprefixbtn2,#orderprefixbtn3").prop("disabled",true);
                        $('#orderprefixformat,#orderprefixlastno').val("");
                        $('#orderprefixsuffixlength').val("1").selectpicker('refresh');
                    }
                    diplaytransactionformatpreview('orderprefix');
                }
                if(type == 5){
                    if(transactionprefix==1){
                        $('input[name="purchaseorderprefix"]').bootstrapToggle('on');
                        $("#purchaseorderprefixformat,#purchaseorderprefixlastno,#purchaseorderprefixsuffixlength,#purchaseorderprefixbtn1,#purchaseorderprefixbtn2,#purchaseorderprefixbtn3").prop("disabled",false);
                        $('#purchaseorderprefixformat').val(transactionprefixformat);
                        $('#purchaseorderprefixlastno').val(lastno);
                        $('#purchaseorderprefixsuffixlength').val(suffixlength).selectpicker('refresh');
                    }else{
                        $('input[name="purchaseorderprefix"]').bootstrapToggle('off');
                        $("#purchaseorderprefixformat,#purchaseorderprefixlastno,#purchaseorderprefixsuffixlength,#purchaseorderprefixbtn1,#purchaseorderprefixbtn2,#purchaseorderprefixbtn3").prop("disabled",true);
                        $('#purchaseorderprefixformat,#purchaseorderprefixlastno').val("");
                        $('#purchaseorderprefixsuffixlength').val("1").selectpicker('refresh');
                    }
                    diplaytransactionformatpreview('purchaseorderprefix');
                }
                if(type == 2){
                    if(transactionprefix==1){
                        $('input[name="invoiceprefix"]').bootstrapToggle('on');
                        $("#invoiceprefixformat,#invoiceprefixlastno,#invoiceprefixsuffixlength,#invoiceprefixbtn1,#invoiceprefixbtn2,#invoiceprefixbtn3").prop("disabled",false);
                        $('#invoiceprefixformat').val(transactionprefixformat);
                        $('#invoiceprefixlastno').val(lastno);
                        $('#invoiceprefixsuffixlength').val(suffixlength).selectpicker('refresh');
                    }else{
                        $('input[name="invoiceprefix"]').bootstrapToggle('off');
                        $("#invoiceprefixformat,#invoiceprefixlastno,#invoiceprefixsuffixlength,#invoiceprefixbtn1,#invoiceprefixbtn2,#invoiceprefixbtn3").prop("disabled",true);
                        $('#invoiceprefixformat,#invoiceprefixlastno').val("");
                        $('#invoiceprefixsuffixlength').val("1").selectpicker('refresh');
                    }
                    diplaytransactionformatpreview('invoiceprefix');
                }
                if(type == 7){
                    if(transactionprefix==1){
                        $('input[name="purchaseinvoiceprefix"]').bootstrapToggle('on');
                        $("#purchaseinvoiceprefixformat,#purchaseinvoiceprefixlastno,#purchaseinvoiceprefixsuffixlength,#purchaseinvoiceprefixbtn1,#purchaseinvoiceprefixbtn2,#purchaseinvoiceprefixbtn3").prop("disabled",false);
                        $('#purchaseinvoiceprefixformat').val(transactionprefixformat);
                        $('#purchaseinvoiceprefixlastno').val(lastno);
                        $('#purchaseinvoiceprefixsuffixlength').val(suffixlength).selectpicker('refresh');
                    }else{
                        $('input[name="purchaseinvoiceprefix"]').bootstrapToggle('off');
                        $("#purchaseinvoiceprefixformat,#purchaseinvoiceprefixlastno,#purchaseinvoiceprefixsuffixlength,#purchaseinvoiceprefixbtn1,#purchaseinvoiceprefixbtn2,#purchaseinvoiceprefixbtn3").prop("disabled",true);
                        $('#purchaseinvoiceprefixformat,#purchaseinvoiceprefixlastno').val("");
                        $('#purchaseinvoiceprefixsuffixlength').val("1").selectpicker('refresh');
                    }
                    diplaytransactionformatpreview('purchaseinvoiceprefix');
                }
                if(type == 3){
                    if(transactionprefix==1){
                        $('input[name="creditnoteprefix"]').bootstrapToggle('on');
                        $("#creditnoteprefixformat,#creditnoteprefixlastno,#creditnoteprefixsuffixlength,#creditnoteprefixbtn1,#creditnoteprefixbtn2,#creditnoteprefixbtn3").prop("disabled",false);
                        $('#creditnoteprefixformat').val(transactionprefixformat);
                        $('#creditnoteprefixlastno').val(lastno);
                        $('#creditnoteprefixsuffixlength').val(suffixlength).selectpicker('refresh');
                    }else{
                        $('input[name="creditnoteprefix"]').bootstrapToggle('off');
                        $("#creditnoteprefixformat,#creditnoteprefixlastno,#creditnoteprefixsuffixlength,#creditnoteprefixbtn1,#creditnoteprefixbtn2,#creditnoteprefixbtn3").prop("disabled",true);
                        $('#creditnoteprefixformat,#creditnoteprefixlastno').val("");
                        $('#creditnoteprefixsuffixlength').val("1").selectpicker('refresh');
                    }
                    diplaytransactionformatpreview('creditnoteprefix');
                }
                if(type == 8){
                    if(transactionprefix==1){
                        $('input[name="purchasecreditnoteprefix"]').bootstrapToggle('on');
                        $("#purchasecreditnoteprefixformat,#purchasecreditnoteprefixlastno,#purchasecreditnoteprefixsuffixlength,#purchasecreditnoteprefixbtn1,#purchasecreditnoteprefixbtn2,#purchasecreditnoteprefixbtn3").prop("disabled",false);
                        $('#purchasecreditnoteprefixformat').val(transactionprefixformat);
                        $('#purchasecreditnoteprefixlastno').val(lastno);
                        $('#purchasecreditnoteprefixsuffixlength').val(suffixlength).selectpicker('refresh');
                    }else{
                        $('input[name="purchasecreditnoteprefix"]').bootstrapToggle('off');
                        $("#purchasecreditnoteprefixformat,#purchasecreditnoteprefixlastno,#purchasecreditnoteprefixsuffixlength,#purchasecreditnoteprefixbtn1,#purchasecreditnoteprefixbtn2,#purchasecreditnoteprefixbtn3").prop("disabled",true);
                        $('#purchasecreditnoteprefixformat,#purchasecreditnoteprefixlastno').val("");
                        $('#purchasecreditnoteprefixsuffixlength').val("1").selectpicker('refresh');
                    }
                    diplaytransactionformatpreview('purchasecreditnoteprefix');
                }
                if(type == 4){
                    if(transactionprefix==1){
                        $('input[name="stockgeneralvoucherprefix"]').bootstrapToggle('on');
                        $("#stockgeneralvoucherprefixformat,#stockgeneralvoucherprefixlastno,#stockgeneralvoucherprefixsuffixlength,#stockgeneralvoucherprefixbtn1,#stockgeneralvoucherprefixbtn2,#stockgeneralvoucherprefixbtn3").prop("disabled",false);
                        $('#stockgeneralvoucherprefixformat').val(transactionprefixformat);
                        $('#stockgeneralvoucherprefixlastno').val(lastno);
                        $('#stockgeneralvoucherprefixsuffixlength').val(suffixlength).selectpicker('refresh');
                    }else{
                        $('input[name="stockgeneralvoucherprefix"]').bootstrapToggle('off');
                        $("#stockgeneralvoucherprefixformat,#stockgeneralvoucherprefixlastno,#stockgeneralvoucherprefixsuffixlength,#stockgeneralvoucherprefixbtn1,#stockgeneralvoucherprefixbtn2,#stockgeneralvoucherprefixbtn3").prop("disabled",true);
                        $('#stockgeneralvoucherprefixformat,#stockgeneralvoucherprefixlastno').val("");
                        $('#stockgeneralvoucherprefixsuffixlength').val("1").selectpicker('refresh');
                    }
                    diplaytransactionformatpreview('stockgeneralvoucherprefix');
                }
                if(type == 9){
                    if(transactionprefix==1){
                        $('input[name="goodsreceivednotesprefix"]').bootstrapToggle('on');
                        $("#goodsreceivednotesprefixformat,#goodsreceivednotesprefixlastno,#goodsreceivednotesprefixsuffixlength,#goodsreceivednotesprefixbtn1,#goodsreceivednotesprefixbtn2,#goodsreceivednotesprefixbtn3").prop("disabled",false);
                        $('#goodsreceivednotesprefixformat').val(transactionprefixformat);
                        $('#goodsreceivednotesprefixlastno').val(lastno);
                        $('#goodsreceivednotesprefixsuffixlength').val(suffixlength).selectpicker('refresh');
                    }else{
                        $('input[name="goodsreceivednotesprefix"]').bootstrapToggle('off');
                        $("#goodsreceivednotesprefixformat,#goodsreceivednotesprefixlastno,#goodsreceivednotesprefixsuffixlength,#goodsreceivednotesprefixbtn1,#goodsreceivednotesprefixbtn2,#goodsreceivednotesprefixbtn3").prop("disabled",true);
                        $('#goodsreceivednotesprefixformat,#goodsreceivednotesprefixlastno').val("");
                        $('#goodsreceivednotesprefixsuffixlength').val("1").selectpicker('refresh');
                    }
                    diplaytransactionformatpreview('goodsreceivednotesprefix');
                }

            }
           }else{
                $('.yesno input[type="checkbox"]').bootstrapToggle('off');
                $('.transactionformat').val('');
                $('.suffixlength').val('1').selectpicker('refresh');
                preview = "<b>Preview : "+$("#defaulttransactionid").val()+"</b>";
                $(".preview").html(preview);
           }
          },
          error: function(xhr) {
            //alert(xhr.responseText);
          },
        });
    }
}

function settransactionformat(format='YYYY-YY',transactionprefixnm){
    
    format = "{"+format+"}";
    if(!$("#"+transactionprefixnm+"format").val().includes(format)){
        $("#"+transactionprefixnm+"format").val($("#"+transactionprefixnm+"format").val()+format);
    }

    diplaytransactionformatpreview(transactionprefixnm);
}
function zeroPad(num, places) {
    var zero = places - num.toString().length + 1;
    return Array(+(zero > 0 && zero)).join("0") + num;
}
function diplaytransactionformatpreview(transactionprefixnm){
    
    var preview = "";
    if($("#"+transactionprefixnm).prop("checked") == true){
        if($("#"+transactionprefixnm+"format").val()!=""){
            var format = $("#"+transactionprefixnm+"format").val();
            if(format.indexOf("{YYYY-YY}") !== -1){
                var date = new Date();
                var year = date.getFullYear()+"-"+(date.getFullYear()+1).toString().substr(2,2);
                format = format.replace("{YYYY-YY}",year);    
            }
            if(format.indexOf("{YY-YY}") !== -1){
                var date = new Date();
                var year = date.getFullYear().toString().substr(2,2)+"-"+(date.getFullYear()+1).toString().substr(2,2);
                format = format.replace("{YY-YY}",year);    
            }
            if(format.indexOf("{autonumber}") !== -1){
                var lastno = $("#"+transactionprefixnm+"lastno").val();
                lastno = (lastno!="" && lastno!=0)?lastno:1;
                var suffixlength = $("#"+transactionprefixnm+"suffixlength").val();

                format = format.replace("{autonumber}",zeroPad(lastno, suffixlength));    
            }
            preview = "<b>Preview : "+format+"</b>";
        }
    }else{
        preview = "<b>Preview : "+$("#defaulttransactionid").val()+"</b>";
    }
    
    $("#"+transactionprefixnm+"preview").html(preview);
}
function getmembers(){
  
    $('#memberid')
        .find('option')
        .remove()
        .end()
        .append('<option value="0">Select '+Member_label+'</option>')
        .val('whatever')
    ;
    $('#memberid').selectpicker('refresh');
    var channelid = $("#channelid").val();
  
    if(channelid!='' && channelid!=0){
      var uurl = SITE_URL+"member/getmembers";
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {channelid:channelid},
        dataType: 'json',
        async: false,
        success: function(response){
  
          for(var i = 0; i < response.length; i++) {
  
            $('#memberid').append($('<option>', { 
              value: response[i]['id'],
              text : ucwords(response[i]['name'])
            }));
  
          }
          $('#memberid').selectpicker('refresh');
        },
        error: function(xhr) {
          //alert(xhr.responseText);
        },
      });
    }
}

function resetdata(){
    $("#channel_div").removeClass("has-error is-focused");
    $("#member_div").removeClass("has-error is-focused");
          
    if(ACTION==0){     
    }
    $('#channelid').val('0');
    $('#memberid').val('0');    
    getTransactionPrefixData();
    $('.selectpicker').selectpicker('refresh');    
}

function checkvalidation(){ 
       
    var channelid = $("#channelid").val();
    var memberid = $("#memberid").val();
    var quotationformat = $("#quotationprefixformat").val();
    var purchasequotationformat = $("#purchasequotationprefixformat").val();
    var orderformat = $("#orderprefixformat").val();
    var purchaseorderformat = $("#purchaseorderprefixformat").val();
    var invoiceformat = $("#invoiceprefixformat").val();
    var purchaseinvoiceformat = $("#purchaseinvoiceprefixformat").val();
    var creditnoteformat = $("#creditnoteprefixformat").val();
    var purchasecreditnoteformat = $("#purchasecreditnoteprefixformat").val();
    var stockgeneralvoucherprefixformat = $("#stockgeneralvoucherprefixformat").val();
    var goodsreceivednotesprefixformat = $("#goodsreceivednotesprefixformat").val();

    var isvalidmemberid = isvalidquotationformat = isvalidpurchasequotationformat = isvalidorderformat = isvalidpurchaseorderformat = isvalidinvoiceformat = isvalidpurchaseinvoiceformat = isvalidcreditnoteformat = isvalidpurchasecreditnoteformat = isvalidstockgeneralvoucherprefixformat = isvalidgoodsreceivednotesprefixformat = 1;
    PNotify.removeAll();
    
    if(channelid != 0 && memberid==0){
        $("#member_div").addClass("has-error is-focused");
        new PNotify({title: "Please select "+member_label+" !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmemberid = 0;
    }

    if($("#quotationprefix").prop("checked") == true && quotationformat.indexOf("{autonumber}") == -1){
        $("#quotationprefixformat_div").addClass("has-error is-focused");
        new PNotify({title: "Auto number required on quotation prefix format !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidquotationformat = 0;
    }else{
        $("#quotationprefixformat_div").removeClass("has-error is-focused");
    }

    if($("#purchasequotationprefix").prop("checked") == true && purchasequotationformat.indexOf("{autonumber}") == -1){
        $("#purchasequotationprefixformat_div").addClass("has-error is-focused");
        new PNotify({title: "Auto number required on purchase quotation prefix format !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidpurchasequotationformat = 0;
    }else{
        $("#purchasequotationprefixformat_div").removeClass("has-error is-focused");
    }

    if($("#orderprefix").prop("checked") == true && orderformat.indexOf("{autonumber}") == -1){
        $("#orderprefixformat_div").addClass("has-error is-focused");
        new PNotify({title: "Auto number required on order prefix format !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidorderformat = 0;
    }else{
        $("#orderprefixformat_div").removeClass("has-error is-focused");
    }

    if($("#purchaseorderprefix").prop("checked") == true && purchaseorderformat.indexOf("{autonumber}") == -1){
        $("#purchaseorderprefixformat_div").addClass("has-error is-focused");
        new PNotify({title: "Auto number required on purchase order prefix format !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidpurchaseorderformat = 0;
    }else{
        $("#purchaseorderprefixformat_div").removeClass("has-error is-focused");
    }

    if($("#invoiceprefix").prop("checked") == true && invoiceformat.indexOf("{autonumber}") == -1){
        $("#invoiceprefixformat_div").addClass("has-error is-focused");
        new PNotify({title: "Auto number required on invoice prefix format !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidinvoiceformat = 0;
    }else{
        $("#invoiceprefixformat_div").removeClass("has-error is-focused");
    }

    if($("#purchaseinvoiceprefix").prop("checked") == true && purchaseinvoiceformat.indexOf("{autonumber}") == -1){
        $("#purchaseinvoiceprefixformat_div").addClass("has-error is-focused");
        new PNotify({title: "Auto number required on purchase invoice prefix format !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidpurchaseinvoiceformat = 0;
    }else{
        $("#purchaseinvoiceprefixformat_div").removeClass("has-error is-focused");
    }

    if($("#creditnoteprefix").prop("checked") == true && creditnoteformat.indexOf("{autonumber}") == -1){
        $("#creditnoteprefixformat_div").addClass("has-error is-focused");
        new PNotify({title: "Auto number required on credit note prefix format !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidcreditnoteformat = 0;
    }else{
        $("#creditnoteprefixformat_div").removeClass("has-error is-focused");
    }

    if($("#purchasecreditnoteprefix").prop("checked") == true && purchasecreditnoteformat.indexOf("{autonumber}") == -1){
        $("#purchasecreditnoteprefixformat_div").addClass("has-error is-focused");
        new PNotify({title: "Auto number required on purchase credit note prefix format !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidpurchasecreditnoteformat = 0;
    }else{
        $("#purchasecreditnoteprefixformat_div").removeClass("has-error is-focused");
    }

    if($("#stockgeneralvoucherprefix").prop("checked") == true && stockgeneralvoucherprefixformat.indexOf("{autonumber}") == -1){
        $("#stockgeneralvoucherprefixformat_div").addClass("has-error is-focused");
        new PNotify({title: "Auto number required on stock general voucher prefix prefix format !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidstockgeneralvoucherprefixformat = 0;
    }else{
        $("#stockgeneralvoucherprefixformat_div").removeClass("has-error is-focused");
    }

    if($("#goodsreceivednotesprefix").prop("checked") == true && goodsreceivednotesprefixformat.indexOf("{autonumber}") == -1){
        $("#goodsreceivednotesprefixformat_div").addClass("has-error is-focused");
        new PNotify({title: "Auto number required on goods received notes prefix prefix format !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidgoodsreceivednotesprefixformat = 0;
    }else{
        $("#goodsreceivednotesprefixformat_div").removeClass("has-error is-focused");
    }

    if(isvalidmemberid==1 && isvalidquotationformat==1 && isvalidpurchasequotationformat==1 && isvalidorderformat==1 && isvalidpurchaseorderformat==1 && isvalidinvoiceformat==1 && isvalidpurchaseinvoiceformat==1 && isvalidcreditnoteformat==1 && isvalidpurchasecreditnoteformat==1 && isvalidstockgeneralvoucherprefixformat == 1 && isvalidgoodsreceivednotesprefixformat == 1){
      
        var formData = new FormData($('#companytransactionprefixform')[0]);
        var uurl = SITE_URL+"transaction-prefix/update-transaction-prefix";
        
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
                    new PNotify({title: "Transaction prefix successfully updated.",styling: 'fontawesome',delay: '3000',type: 'success'});
                    setTimeout(function() { window.location.reload(); }, 1500);
                }else{
                    new PNotify({title: "Transaction prefix not updated !",styling: 'fontawesome',delay: '3000',type: 'error'});
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
  