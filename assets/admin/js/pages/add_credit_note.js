$(document).ready(function() {  
  
  if(OfferId>0){
    $("#offer_div").parent().show();
    $("#Maincreditnotedetails_div").show();
    $("#offerAmountTotal").show();
    // $("#invoiceamountdiv").hide();
    $("#Maincreditnote_div").hide();
    $("#AmountTotal").hide();
    getApprovedInvoiceByMember();
    getbillingaddress();
    calculateofferreward();
  }else{
    $("#offer_div").parent().hide();
    $("#Maincreditnotedetails_div").hide();
    $("#offerAmountTotal").hide();
    // $("#invoiceamountdiv").hide();
    $("#Maincreditnote_div").show();
    $("#AmountTotal").show();
  }
  
  $('#creditnotedate').datepicker({
    todayHighlight: true,
    format: 'dd/mm/yyyy',
    todayBtn:"linked",
  });
  /****MEMBER CHANGE EVENT****/
  $('#memberid').on('change', function (e) {
    getApprovedInvoiceByMember();
    getbillingaddress();
    if($("input[name='creditnotetype']:checked").val() == 0){
      changeextrachargesamount();
      overallextracharges();
      netamounttotal();
    }
  });
  /****MEMBER CHANGE EVENT****/
  $('#invoiceid').on('change', function (e) {
    if($("input[name='creditnotetype']:checked").val() == 0){
      getTransactionProducts();
    }
    getbillingaddress();
    if($("input[name='creditnotetype']:checked").val() == 0){
      changeextrachargesamount();
      overallextracharges();
      netamounttotal();
    }
  });
  if(ACTION==1 && InvoiceId!='' && InvoiceId!=null){
    getApprovedInvoiceByMember();
    getbillingaddress();
    if($("input[name='creditnotetype']:checked").val() == 0){
      getTransactionProducts();
      netamounttotal();
    }
  }
  /****BILLING ADDRESS CHANGE EVENT****/
  $('#billingaddressid').on('change', function (e) {
    $('#billingaddress').val($('#billingaddressid option:selected').text());
  });
  /****SHIPPING ADDRESS CHANGE EVENT****/
  $('#shippingaddressid').on('change', function (e) {
    $('#shippingaddress').val($('#shippingaddressid option:selected').text());
  });

  $('input[name="creditnotetype"]').click(function(){
    var creditnotetype = this.value;
    if(creditnotetype == 0){
      $("#offer_div").parent().hide();
      $("#Maincreditnotedetails_div").hide();
      $("#offerAmountTotal").hide();
      $("#Maincreditnote_div").show();
      $("#AmountTotal").show();
      $("#invoiceamountdiv").show();
    } else {
      $("#offer_div").parent().show();
      $("#Maincreditnotedetails_div").show();
      $("#offerAmountTotal").show();
      $("#Maincreditnote_div").hide();
      $("#AmountTotal").hide();
      $("#invoiceamountdiv").hide();
    }
    $('#invoiceid').change();
  });

  /****OFFER CHANGE EVENT****/
  $('#offerid').on('change', function (e) {
    calculateofferreward();
  });
    
});
$(document).on('keyup', '.creditnoteamount', function(){
  var rowid = $(this).attr("id").match(/\d+/);
  calculateOfferNetAmount(rowid);
});
$(document).on('keyup', '.creditnotetax', function(){
  var rowid = $(this).attr("id").match(/\d+/);
  if(parseFloat(this.value)>=100){
    $("#creditnotetax"+rowid).val("100");
  }
  calculateOfferNetAmount(rowid);
});
$(document).on('keyup', '#redeempoints', function() {
  var memberredeempoint = $("#memberredeempoint").html();

  if(parseInt(this.value) > parseInt(memberredeempoint)){
    $(this).val(parseInt(memberredeempoint));
  }
  redeempointcalc();
});
function calculateofferreward(){
  var targetvalue = $('#offerid option:selected').attr("data-targetvalue");
  var rewardvalue = $('#offerid option:selected').attr("data-rewardvalue");
  var rewardtype = $('#offerid option:selected').attr("data-rewardtype");

  if(rewardvalue != "" && parseFloat(rewardvalue) > 0){

    var targetamount = parseFloat(targetvalue);
    if(rewardtype==1){
      targetamount = parseFloat(targetvalue) * parseFloat(rewardvalue) / 100;
    }

    targetamount = parseFloat(targetamount)/parseInt($(".creditnotedetail").length);
    
    $(".creditnoteamount").val(parseFloat(targetamount).toFixed(2));
    $(".creditnotedetail").each(function(){
      var rowid = $(this).attr("id").match(/\d+/);
      calculateOfferNetAmount(rowid);
    });
  }
}
function redeempointcalc(){
  var point = "";
  var amount = "0";
  var memberredeempoint = $("#redeempoints").val();
  var redeempointsrate = $("#redeempointsrate").val();
  
  if(memberredeempoint!=0){
    point = parseInt(memberredeempoint)/parseInt($(".creditnotedetail").length);
    point = (point.toString().indexOf('.') != -1)?parseFloat(point).toFixed(2):parseInt(point);
    
    amount = parseFloat(parseFloat(point) * parseInt(redeempointsrate)).toFixed(2);
  }
  
  $(".creditnotedetail").each(function(){
    var rowid = $(this).attr("id").match(/\d+/);

    $("#redeempoint"+rowid).val(point);
    if($("#creditnoteamount"+rowid).val()==0){
      $("#creditnoteamount"+rowid).val(parseFloat(amount).toFixed(2));
    }
    calculateOfferNetAmount(rowid);
  });
}
function addnewcreditnote(){
  
  var rowcount = parseInt($(".creditnote:last").attr("id").match(/\d+/))+1;
  
  var datahtml =' <div class="creditnote" id="countcreditnote'+rowcount+'">\
                    <div class="col-sm-12 p-n">\
                        <div class="col-md-4 pr-sm pl-sm">\
                          <div class="form-group ml-n mr-n" id="creditnotedetail'+rowcount+'_div">\
                            <input type="text" class="form-control creditnotedetail" name="creditnotedetail[]" id="creditnotedetail'+rowcount+'" value="">\
                          </div>\
                        </div>\
                        <div class="col-md-2 pr-sm pl-sm">\
                          <div class="form-group ml-n mr-n" id="creditnoteamount'+rowcount+'_div">\
                            <input type="text" class="form-control creditnoteamount text-right" name="creditnoteamount[]" id="creditnoteamount'+rowcount+'" value="" onkeypress="return decimal_number_validation(event,this.value,8)">\
                          </div>\
                        </div>\
                        <div class="col-md-2 pr-sm pl-sm">\
                            <div class="form-group ml-n mr-n" id="redeempoint'+rowcount+'_div">\
                                <input type="text" class="form-control redeempoint text-right" name="redeempoint[]" id="redeempoint'+rowcount+'" value="" onkeypress="return isNumber(event)" maxlength="4" readonly>\
                            </div>\
                        </div>\
                        <div class="col-md-1 pr-sm pl-sm">\
                          <div class="form-group ml-n mr-n" id="creditnotetax'+rowcount+'_div">\
                              <input type="text" class="form-control creditnotetax text-right" name="creditnotetax[]" id="creditnotetax'+rowcount+'" value="" onkeypress="return decimal_number_validation(event,this.value,3)">\
                              <input type="hidden" class="creditnotetaxamount" name="creditnotetaxamount[]" id="creditnotetaxamount'+rowcount+'">\
                          </div>\
                        </div>\
                        <div class="col-md-2 pr-sm pl-sm">\
                            <div class="form-group ml-n mr-n" id="creditnotenetamount'+rowcount+'_div">\
                                <input type="text" class="form-control creditnotenetamount text-right" name="creditnotenetamount[]" id="creditnotenetamount'+rowcount+'" value="" onkeypress="return decimal_number_validation(event,this.value,8)" maxlength="15" readonly>\
                            </div>\
                        </div>\
                        <div class="col-md-1 pl-sm" style="margin-top: 20px !important;">\
                            <button type="button" class="btn btn-default btn-raised remove_creditnote_btn m-n" id="c+'+rowcount+'" onclick="removecreditnote('+rowcount+')" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>\
                            <button type="button" class="btn btn-default btn-raised  add_creditnote_btn m-n" id="'+rowcount+'" onclick="addnewcreditnote('+rowcount+')" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>\
                        </div>\
                    </div>\
                  </div>';
  
  $(".add_creditnote_btn:last").hide();
  $(".remove_creditnote_btn:first").show();
  $("#creditnote_div").append(datahtml);
  $(".remove_creditnote_btn:last").show();
  //alert(combinationid);
  redeempointcalc();
}

function removecreditnote(rowid){
  $('#countcreditnote'+rowid).remove();
  $('.add_creditnote_btn:last').show();
  
  if($('.remove_creditnote_btn:visible').length ==1 ){
    $('.remove_creditnote_btn:first').hide();
  }

  offernetamounttotal();
  redeempointcalc();
}
function checkSerialNo(){
  
  var memberid = (ACTION==1?$("#oldmemberid").val():$("#memberid").val());
  var serialno = $.trim($("#serialno").val());
  
  var isvalidserialno = isvalidmemberid = 0;
  $("#serialno_div").removeClass("has-error is-focused");
  PNotify.removeAll();
  if(ACTION==0){
      if(memberid==0){
          $("#member_div").addClass("has-error is-focused");
          new PNotify({title: 'Please select '+member_label+' !',styling: 'fontawesome',delay: '3000',type: 'error'});
      }else{
          $("#member_div").removeClass("has-error is-focused");
          isvalidmemberid=1;
      }
  }else{
      isvalidmemberid=1;
  }
  if(serialno==''){
      $("#serialno_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter serial number !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }else{
      $("#serialno_div").removeClass("has-error is-focused");
      isvalidserialno=1;
  }
 
  if(isvalidserialno == 1 && isvalidmemberid == 1){
      var baseurl = SITE_URL+'credit-note/getinvoicedatabyorderproductserialno';
      $.ajax({
          url: baseurl,
          type: 'POST',
          data: {serialno:serialno,memberid:memberid},
          datatype:'json',
          beforeSend: function(){
              $('.mask').show();
              $('#loader').show();
          },
          success: function(response){
              var obj = JSON.parse(response);
              console.log(obj);
              if(!$.isEmptyObject(obj)){
                $("#invoiceid").val(obj['id'].split(','));
                $("#invoiceid").selectpicker('refresh');
                getTransactionProducts();
                $("#serialno").val('');
              }else{
                  $("#serialno_div").addClass("has-error is-focused");
                  new PNotify({title: 'Serial number not match with any '+member_label+' inovice !',styling: 'fontawesome',delay: '3000',type: 'error'});
              }
          },
          complete: function(){
              $('.mask').hide();
              $('#loader').hide();
          },
      });
  }
  
}
function getApprovedInvoiceByMember(){
  $('#invoiceid')
      .find('option')
      .remove()
      .end()
      .append()
      .val('0')
  ;
  
  $('#invoiceid').selectpicker('refresh');

  var memberid = $("#memberid").val();
  
  if(memberid!='' && memberid!=0){
    var uurl = SITE_URL+"invoice/getApprovedInvoiceByMember";
    
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {memberid:String(memberid)},
      dataType: 'json',
      async: false,
      success: function(response){

        for(var i = 0; i < response.length; i++) {

          if(ACTION==1){
            if(InvoiceId!=null || InvoiceId!=''){
             
              InvoiceId = InvoiceId.toString().split(',');
             
              if(InvoiceId.includes(response[i]['id'])){
                $('#invoiceid').append($('<option>', { 
                  value: response[i]['id'],
                  selected: "selected",
                  text : response[i]['invoiceno'],
                  "data-billingid": response[i]['billingid'],
                  "data-shippingid": response[i]['shippingid']
                }));
              }else{
                $('#invoiceid').append($('<option>', { 
                  value: response[i]['id'],
                  text : response[i]['invoiceno'],
                  "data-billingid": response[i]['billingid'],
                  "data-shippingid": response[i]['shippingid']
                }));
              }
            }
          }else{
            $('#invoiceid').append($('<option>', { 
              value: response[i]['id'],
              text : response[i]['invoiceno'],
              "data-billingid": response[i]['billingid'],
              "data-shippingid": response[i]['shippingid']
            }));
          }
        }
      },
      error: function(xhr) {
      //alert(xhr.responseText);
      },
    });
  }else{
    $("#creditnoteproducttable tbody").html("<tr><td colspan='16' class='text-center'>No data available in table.</td></tr>");
  }
  $('#invoiceid').selectpicker('refresh');
}

function getbillingaddress(loadtype=0){
  $('#billingaddressid')
      .find('option,optgroup')
      .remove()
      .end()
      .val('')
  ;
  $('#shippingaddressid')
      .find('option,optgroup')
      .remove()
      .end()
      .val('')
  ;
  $('#billingaddressid,#shippingaddressid').selectpicker('refresh');

  var memberid = $("#memberid").val();
  var BillingAddressID = $("#invoiceid option:selected:last").attr("data-billingid");
  var ShippingAddressID = $("#invoiceid option:selected:last").attr("data-shippingid");
  
  var ordertype = 0;
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
      success: function(response){
          var obj = JSON.parse(response);
          if (!jQuery.isEmptyObject(obj['billingaddress'])) {
              for(var i = 0; i < obj['billingaddress'].length; i++) {

                if(BillingAddressID!=0 && BillingAddressID==obj['billingaddress'][i]['id']){
                  $('#billingaddressid option:first').after('<optgroup label="Invoice Billing Address"></optgroup>');
                  $('#billingaddressid optgroup').html($('<option>', { 
                      value: obj['billingaddress'][i]['id'],
                      text : ucwords(obj['billingaddress'][i]['address'])
                  }));
                }else{
                  $('#billingaddressid').append($('<option>', { 
                    value: obj['billingaddress'][i]['id'],
                    text : ucwords(obj['billingaddress'][i]['address'])
                  }));
                }
                if(ShippingAddressID!=0 && ShippingAddressID==obj['billingaddress'][i]['id']){
                  $('#shippingaddressid option:first').after('<optgroup label="Invoice Shipping Address"></optgroup>');
                  $('#shippingaddressid optgroup').html($('<option>', { 
                    value: obj['billingaddress'][i]['id'],
                    text : ucwords(obj['billingaddress'][i]['address'])
                }));
                }else{
                  $('#shippingaddressid').append($('<option>', { 
                      value: obj['billingaddress'][i]['id'],
                      text : ucwords(obj['billingaddress'][i]['address'])
                  }));
                }
              }
              if(BillingAddressID!=0){
                  $('#billingaddressid').val(BillingAddressID);
              }
              if(ShippingAddressID!=0){
                  $('#shippingaddressid').val(ShippingAddressID);
              }
          }
          //if($("input[name='creditnotetype']:checked").val() == 1){
            if (!jQuery.isEmptyObject(obj['countrewards'])) {
              $('#memberredeempoint').html(obj['countrewards']['rewardpoint']);
            }
            if (!jQuery.isEmptyObject(obj['channeldata'])) {
              $('#redeempointsrate').val(obj['channeldata']['conversationrate']);
            }
          //}
          /* if (!jQuery.isEmptyObject(obj['countrewards'])) {
              $('#redeempointsforbuyer').val(obj['countrewards']['rewardpoint']);
          } */
      },
      error: function(xhr) {
      //alert(xhr.responseText);
      },
    });
  }else{
    $('#memberredeempoint').html("0");
    $('#redeempoints,#redeempointsrate').val("");
    redeempointcalc();
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

function getTransactionProducts(){
    
    var memberid = $("#memberid").val();
    var invoiceid = $("#invoiceid").val();
    var creditnoteid = $("#creditnoteid").val();

    $('.disccol,.cgstcol,.sgstcol,.igstcol').show();
    if(invoiceid!='' && invoiceid!=null){
      var uurl = SITE_URL+"credit-note/getTransactionProducts";
      
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {memberid:String(memberid),invoiceid:String(invoiceid),creditnoteid:String(creditnoteid)},
        dataType: 'json',
        async: false,
        success: function(response){
            if(response!=""){
              var invoiceproducts = response['invoiceproducts'];
              var invoiceamountdata = response['invoiceamountdata'];
              var gstprice = response['gstprice'];

              var htmldata = discolumn = "";
              var gstcolumn = [];

              var headerdata = '<tr>\
                                  <th rowspan="2" class="width5">Sr. No.</th>\
                                  <th rowspan="2" class="width15">Product Name</th>\
                                  <th rowspan="2" class="text-right">Qty.</th>\
                                  <th rowspan="2" class="text-right">Rate (Excl. Tax)</th>';
                  if(gstprice == 1){
                    headerdata +='<th class="text-right width8 disccol">Dis.(%)</th>\
                                  <th class="text-right width8 sgstcol">SGST (%)</th>\
                                  <th class="text-right width8 cgstcol">CGST (%)</th>\
                                  <th class="text-right width8 igstcol">IGST (%)</th>';
                  }else{
                    headerdata +='<th class="text-right width8 sgstcol">SGST (%)</th>\
                                  <th class="text-right width8 cgstcol">CGST (%)</th>\
                                  <th class="text-right width8 igstcol">IGST (%)</th>\
                                  <th class="text-right width8 disccol">Dis.(%)</th>';
                  }
                    headerdata +='<th rowspan="2" class="text-right">Amount ('+CURRENCY_CODE+')</th>\
                                  <th rowspan="2" class="text-right">Paid Credit</th>\
                                  <th rowspan="2" class="text-right width8">Credit Qty.</th>\
                                  <th rowspan="2" class="text-right width8">Credit (%)</th>\
                                  <th rowspan="2" class="text-right width12">Credit Amount</th>\
                                  <th rowspan="2" class="text-right width12">Stock Qty.</th>\
                                  <th rowspan="2" class="text-right" width="30%">Reject Qty.</th>\
                                </tr>\
                              <tr>';
                  if(gstprice == 1){
                    headerdata +='<th class="text-right width8 disccol">Amt. ('+CURRENCY_CODE+')</th>\
                                  <th class="text-right width8 sgstcol">Amt. ('+CURRENCY_CODE+')</th>\
                                  <th class="text-right width8 cgstcol">Amt. ('+CURRENCY_CODE+')</th>\
                                  <th class="text-right width8 igstcol">Amt. ('+CURRENCY_CODE+')</th>';
                  }else{
                    headerdata +='<th class="text-right width8 sgstcol">Amt. ('+CURRENCY_CODE+')</th>\
                                  <th class="text-right width8 cgstcol">Amt. ('+CURRENCY_CODE+')</th>\
                                  <th class="text-right width8 igstcol">Amt. ('+CURRENCY_CODE+')</th>\
                                  <th class="text-right width8 disccol">Amt. ('+CURRENCY_CODE+')</th>';
                  }
              headerdata +='</tr>';

              if(invoiceproducts!=null && invoiceproducts!=""){
                if(invoiceproducts.length>0){
                  for(var i=0; i<invoiceproducts.length; i++){

                    gstcolumn.push(invoiceproducts[i]['igst']);
                    discolumn += parseFloat(invoiceproducts[i]['discount']);
                    var creditqty = invoiceqty = creditpercent = creditamount = stockqty = rejectqty = inputdisabled = "";
                 
                    var rate = parseFloat(invoiceproducts[i]['amount']);
                    var originalprice = parseFloat(invoiceproducts[i]['originalprice']);
                   /*  if(GST_PRICE == 1){
                      rate = parseFloat(invoiceproducts[i]['originalprice']);
                    } */
                    var producttax = parseFloat(invoiceproducts[i]['tax']);

                    var discount = parseFloat(invoiceproducts[i]['discount']);
                    var discountamount = (parseFloat(originalprice) * parseFloat(discount) / 100);
                    var amountwithouttax = parseFloat(rate);
                    var taxamount = (parseFloat(rate) * parseFloat(producttax) /100);
                    var amountwithtax = parseFloat(parseFloat(amountwithouttax) + parseFloat(taxamount)).toFixed(2);
                    var totalamount = (parseFloat(rate) + parseFloat(taxamount)) * parseFloat(invoiceproducts[i]['quantity']);

                    if(creditnoteid!=''){
                      creditqty = parseFloat(invoiceproducts[i]['creditqty']);
                      creditpercent = parseFloat(invoiceproducts[i]['creditpercent']);
                      creditamount = parseFloat(invoiceproducts[i]['creditamount']);
                      stockqty = parseFloat(invoiceproducts[i]['productstockqty'])!=0?parseFloat(invoiceproducts[i]['productstockqty']):'';
                      rejectqty = parseFloat(invoiceproducts[i]['productrejectqty'])!=0?parseFloat(invoiceproducts[i]['productrejectqty']):'';
                      
                      invoiceqty = parseFloat(invoiceproducts[i]['quantity']) - parseFloat(invoiceproducts[i]['paidqty']);
                    }else{
                      creditqty = parseFloat(invoiceproducts[i]['quantity']) - parseFloat(invoiceproducts[i]['paidqty']);
                      invoiceqty = creditqty;
                      creditpercent = parseFloat(100).toFixed(2);
                      creditamount = parseFloat(amountwithtax) * parseFloat(creditqty);
                    }
                    var totaltaxvalue = parseFloat(taxamount) * parseFloat(creditqty);
                    
                    var invoiceid = invoiceproducts[i]['invoiceid'];
                    var transactionproductsid = invoiceproducts[i]['transactionproductsid'];
                    if(parseFloat(invoiceproducts[i]['quantity']) == parseFloat(invoiceproducts[i]['paidqty'])){
                      invoiceid = "";
                      transactionproductsid = "";
                      inputdisabled = "disabled";
                      creditqty = creditpercent = creditamount = stockqty = rejectqty = "";
                    }

                    var stockids = invoiceproducts[i]['stockids'].toString().split(',');
                    var stockqtys = invoiceproducts[i]['stockqtys'].toString().split(',');
                    var stockprice = invoiceproducts[i]['stockprice'].toString().split(',');

                    var stockhtml = rejectstockhtml = "";
                    if(stockids.length > 0){
                      for(var s=0; s<stockids.length; s++){

                        if(stockqtys[s]!=""){
                          var stockquantity = parseFloat(stockqtys[s]);
                        }else{
                          var stockquantity = (creditqty!="")?parseFloat(creditqty):0;
                        }
                        
                        if(creditqty!="" && parseFloat(stockquantity) > parseFloat(creditqty)){
                          stockquantity = parseFloat(creditqty);
                        }
                        var hr = '';
                        if(s!=0){
                          hr += '<div class="col-md-12 p-n"><hr></div>';
                        }
                        stockhtml += '<div class="col-md-12 p-n">'+hr+'\
                                        <p>'+stockprice[s]+' ('+stockqtys[s]+')</p>\
                                      </div>\
                                      \
                                      <div class="col-md-12 p-n">\
                                        \
                                        <div class="form-group mt-n pb-n pr-sm pl-sm" id="stockqtys_'+invoiceproducts[i]['transactionproductsid']+'_'+(s+1)+'_div">\
                                          <input type="hidden" value="'+stockids[s]+'" name="stockids['+invoiceproducts[i]['transactionproductsid']+'][]">\
                                          <input type="text" div-max="'+stockqtys[s]+'" name="stockqtys['+invoiceproducts[i]['transactionproductsid']+'][]" id="stockqtys_'+invoiceproducts[i]['transactionproductsid']+'_'+(s+1)+'" class="form-control stockqtys'+invoiceproducts[i]['transactionproductsid']+' text-right qtyclass" value="" onkeypress="'+(MANAGE_DECIMAL_QTY==1?"return decimal_number_validation(event, this.value,8);":"return isNumber(event);")+'" data-max="'+(MANAGE_DECIMAL_QTY==1?parseFloat(stockquantity).toFixed(2):parseInt(stockquantity))+'" '+inputdisabled+'>\
                                        </div>\
                                      </div>';


                        rejectstockhtml += '<div class="col-md-12 p-n">'+hr+'\
                                              <p>'+stockprice[s]+' ('+stockqtys[s]+')</p>\
                                            </div>\
                                            \
                                            <div class="col-md-12 p-n">\
                                              <div class="form-group mt-n pb-n pr-sm pl-sm" id="scrapqtys_'+invoiceproducts[i]['transactionproductsid']+'_'+(s+1)+'_div">\
                                                <input type="text" name="scrapqtys['+invoiceproducts[i]['transactionproductsid']+'][]" id="scrapqtys_'+invoiceproducts[i]['transactionproductsid']+'_'+(s+1)+'" class="form-control scrapqtys'+invoiceproducts[i]['transactionproductsid']+' text-right scrapqtyclass" value="" onkeypress="'+(MANAGE_DECIMAL_QTY==1?"return decimal_number_validation(event, this.value,8);":"return isNumber(event);")+'" '+inputdisabled+'>\
                                              </div>\
                                            </div>';
                      }
                    }
                    htmldata += "<tr class='countproducts' id='"+invoiceproducts[i]['transactionproductsid']+"'>";
                      htmldata += "<td rowspan='2'>"+(i+1);
                      htmldata += '<input type="hidden" name="transactionproductsid[]" value="'+transactionproductsid+'">';
                      htmldata += '<input type="hidden" name="invoiceidarr[]" id="invoiceidarr'+invoiceproducts[i]['transactionproductsid']+'" value="'+invoiceid+'">';
                      htmldata += '<input type="hidden" id="tax'+invoiceproducts[i]['transactionproductsid']+'" value="'+parseFloat(producttax)+'">';
                      htmldata += '<input type="hidden" id="taxtype'+invoiceproducts[i]['transactionproductsid']+'" value="'+invoiceproducts[i]['igst']+'">';
                      htmldata += '<input type="hidden" id="taxvalue'+invoiceproducts[i]['transactionproductsid']+'" class="taxvalue" value="'+parseFloat(totaltaxvalue).toFixed(2)+'">';
                      htmldata += '<input type="hidden" id="price'+invoiceproducts[i]['transactionproductsid']+'" value="'+parseFloat(amountwithtax)+'">';
                      htmldata += '<input type="hidden" id="actualprice'+invoiceproducts[i]['transactionproductsid']+'" value="'+parseFloat(originalprice)+'">';
                      htmldata += '<input type="hidden" id="rate'+invoiceproducts[i]['transactionproductsid']+'" value="'+parseFloat(rate)+'">';
                      htmldata += '<input type="hidden" id="producttotalwithouttax'+invoiceproducts[i]['transactionproductsid']+'" class="producttotalwithouttax" value="'+parseFloat(parseFloat(rate) * parseFloat(creditqty)).toFixed(2)+'">';
                      htmldata += '<input type="hidden" id="producttotal'+invoiceproducts[i]['transactionproductsid']+'" class="producttotal" value="'+parseFloat(parseFloat(amountwithtax) * parseFloat(creditqty)).toFixed(2)+'">';
                      htmldata += '<input type="hidden" id="discount'+invoiceproducts[i]['transactionproductsid']+'" class="discount" value="'+parseFloat(discount).toFixed(2)+'">';
                      htmldata += '<input type="hidden" id="invoicequantity'+invoiceproducts[i]['transactionproductsid']+'" value="'+parseFloat(invoiceproducts[i]['quantity'])+'" class="invoicequantity'+invoiceid+'">';
                      htmldata += '<input type="hidden" id="invoiceamount'+invoiceproducts[i]['transactionproductsid']+'" value="'+parseFloat(creditamount).toFixed(2)+'" class="invoiceamount'+invoiceid+'">';
                      
                      htmldata += "</td>";

                      htmldata += "<td rowspan='2'>"+ucwords(invoiceproducts[i]['productname'])+"<br><br><b>Invoice No.: </b>"+invoiceproducts[i]['invoiceno']+"</td>";
                      
                      htmldata += '<td rowspan="2" class="width8 text-right">'+parseFloat(invoiceproducts[i]['quantity']).toFixed(2)+'\
                      <input type="hidden" name="invoiceqty" id="invoiceqty'+invoiceproducts[i]['transactionproductsid']+'" value="'+parseFloat(invoiceproducts[i]['quantity']).toFixed(2)+'"></td>';
                      
                      
                      htmldata += "<td rowspan='2' class='text-right'>"+parseFloat(rate).toFixed(2)+"</td>";
                      if(gstprice == 1){
                        if(parseFloat(discount) > 0){
                          htmldata += "<td class='text-right disccol'>"+parseFloat(discount).toFixed(2)+"</td>";
                        }else{
                          htmldata += "<td class='text-right disccol'>-</td>";
                        }
                      
                        if(invoiceproducts[i]['igst']==1){
                          htmldata += "<td class='text-right sgstcol'>"+parseFloat((parseFloat(producttax)/2)).toFixed(2)+"</td>";
                          htmldata += "<td class='text-right cgstcol'>"+parseFloat((parseFloat(producttax)/2)).toFixed(2)+"</td>";
                          htmldata += "<td class='text-right igstcol'>-</td>";
                        }else{
                          htmldata += "<td class='text-right sgstcol'>-</td>";
                          htmldata += "<td class='text-right cgstcol'>-</td>";
                          htmldata += "<td class='text-right igstcol'>"+parseFloat(producttax).toFixed(2)+"</td>";
                        }
                      }else{
                        if(invoiceproducts[i]['igst']==1){
                          htmldata += "<td class='text-right sgstcol'>"+parseFloat((parseFloat(producttax)/2)).toFixed(2)+"</td>";
                          htmldata += "<td class='text-right cgstcol'>"+parseFloat((parseFloat(producttax)/2)).toFixed(2)+"</td>";
                          htmldata += "<td class='text-right igstcol'>-</td>";
                        }else{
                          htmldata += "<td class='text-right sgstcol'>-</td>";
                          htmldata += "<td class='text-right cgstcol'>-</td>";
                          htmldata += "<td class='text-right igstcol'>"+parseFloat(producttax).toFixed(2)+"</td>";
                        }
                        if(parseFloat(discount) > 0){
                          htmldata += "<td class='text-right disccol'>"+parseFloat(discount).toFixed(2)+"</td>";
                        }else{
                          htmldata += "<td class='text-right disccol'>-</td>";
                        }
                      }
                      
                      htmldata += "<td rowspan='2' class='text-right netamount' id='productnetprice"+invoiceproducts[i]['transactionproductsid']+"'>"+parseFloat(totalamount).toFixed(2)+"</td>";

                      htmldata += "<td rowspan='2' class='text-right' width='33%'>"+parseFloat(invoiceproducts[i]['paidcredit']).toFixed(2)+"<br><br><p style='margin: 0 0 5px;'><b>Total Paid Qty.: </b>"+parseFloat(invoiceproducts[i]['paidqty']).toFixed(2)+"</p><p style='margin: 0 0 5px;'><b>Stock Qty.: </b>"+parseFloat(invoiceproducts[i]['stockqty']).toFixed(2)+"</p><p style='margin: 0 0 5px;'><b>Reject Qty.: </b>"+parseFloat(invoiceproducts[i]['rejectqty']).toFixed(2)+"</p></td>";

                      htmldata += '<td rowspan="2"><div class="col-md-12 pl pr"><div class="form-group" id="creditqty'+invoiceproducts[i]['transactionproductsid']+'_div"><input type="text" name="creditqty['+invoiceproducts[i]['transactionproductsid']+']" id="creditqty'+invoiceproducts[i]['transactionproductsid']+'" class="form-control creditqty text-right" value="'+creditqty+'" onkeypress="'+(MANAGE_DECIMAL_QTY==1?"return decimal_number_validation(event, this.value,8);":"return isNumber(event);")+'" '+inputdisabled+'>';

                      htmldata += '<input type="hidden" name="qty'+invoiceproducts[i]['transactionproductsid']+'" id="qty'+invoiceproducts[i]['transactionproductsid']+'" class="form-control" value="'+parseFloat(invoiceproducts[i]['quantity']).toFixed(2)+'">';
                
                      htmldata += '<input type="hidden" name="paidqty'+invoiceproducts[i]['transactionproductsid']+'" id="paidqty'+invoiceproducts[i]['transactionproductsid']+'" class="form-control" value="'+parseFloat(invoiceproducts[i]['paidqty']).toFixed(2)+'">';

                      htmldata += '<input type="hidden" name="paidcredit'+invoiceproducts[i]['transactionproductsid']+'" id="paidcredit'+invoiceproducts[i]['transactionproductsid']+'" value="'+parseFloat(invoiceproducts[i]['paidcredit']).toFixed(2)+'">';

                      htmldata += '<input type="hidden" name="actualprice'+invoiceproducts[i]['transactionproductsid']+'" id="actualprice'+invoiceproducts[i]['transactionproductsid']+'" value="'+parseFloat(amountwithtax).toFixed(2)+'"><span class="material-input"></span></div></div></td>';

                      htmldata += '<td rowspan="2"><div class="col-md-12 pl pr"><div class="form-group is-empty" id="creditpercent'+invoiceproducts[i]['transactionproductsid']+'_div"><input type="text" name="creditpercent['+invoiceproducts[i]['transactionproductsid']+']" id="creditpercent'+invoiceproducts[i]['transactionproductsid']+'" class="form-control creditpercent text-right" value="'+((creditpercent!="")?parseFloat(creditpercent).toFixed(2):"")+'" onkeypress="return decimal_number_validation(event,this.value);" '+inputdisabled+'><span class="material-input"></span></div></div></td>';

                      htmldata += '<td rowspan="2"><div class="col-md-12 pl pr"><div class="form-group is-empty" id="creditamount'+invoiceproducts[i]['transactionproductsid']+'_div"><input type="text" name="creditamount['+invoiceproducts[i]['transactionproductsid']+']" id="creditamount'+invoiceproducts[i]['transactionproductsid']+'" class="form-control creditamount text-right" value="'+((creditamount>0)?parseFloat(creditamount).toFixed(2):"")+'" '+inputdisabled+'><span class="material-input"></span></div></div></td>';

                      /* htmldata += '<td rowspan="2"><div class="col-md-12 pl pr"><div class="form-group is-empty" id="productstockqty'+invoiceproducts[i]['transactionproductsid']+'_div"><input type="text" name="productstockqty['+invoiceproducts[i]['transactionproductsid']+']" id="productstockqty'+invoiceproducts[i]['transactionproductsid']+'" class="form-control stockqty text-right" value="'+stockqty+'" onkeypress="'+(MANAGE_DECIMAL_QTY==1?"return decimal_number_validation(event, this.value,8);":"return isNumber(event);")+'" '+inputdisabled+'><span class="material-input"></span></div></div></td>'; */

                      /* htmldata += '<td rowspan="2"><div class="col-md-12 pl pr"><div class="form-group is-empty" id="productrejectqty'+invoiceproducts[i]['transactionproductsid']+'_div"><input type="text" name="productrejectqty['+invoiceproducts[i]['transactionproductsid']+']" id="productrejectqty'+invoiceproducts[i]['transactionproductsid']+'" class="form-control rejectqty text-right" value="'+rejectqty+'" onkeypress="'+(MANAGE_DECIMAL_QTY==1?"return decimal_number_validation(event, this.value,8);":"return isNumber(event);")+'" '+inputdisabled+'><span class="material-input"></span></div></div></td>'; */

                      htmldata += '<td rowspan="2">'+stockhtml+'</td>';
                      htmldata += '<td rowspan="2">'+rejectstockhtml+'</td>';

                    htmldata += "</tr>";

                    htmldata += "<tr>";
                    if(gstprice == 1){
                      if(parseFloat(discount) > 0){
                        htmldata += "<td class='text-right disccol' id='discountamount"+invoiceproducts[i]['transactionproductsid']+"'>"+parseFloat(discountamount).toFixed(2)+"</td>";
                      }else{
                        htmldata += "<td class='text-right disccol'>-</td>";
                      }
                      if(invoiceproducts[i]['igst']==1){
                        htmldata += "<td class='text-right sgstcol' id='sgst"+invoiceproducts[i]['transactionproductsid']+"'>"+parseFloat((taxamount/2)).toFixed(2)+"</td>";
                        htmldata += "<td class='text-right cgstcol' id='cgst"+invoiceproducts[i]['transactionproductsid']+"' style='border: 1px solid #e8e8e8;'>"+parseFloat((taxamount/2)).toFixed(2)+"</td>";
                        htmldata += "<td class='text-right igstcol' id='igst"+invoiceproducts[i]['transactionproductsid']+"' style='border: 1px solid #e8e8e8;'>-</td>";
                      }else{
                        htmldata += "<td class='text-right sgstcol' id='sgst"+invoiceproducts[i]['transactionproductsid']+"'>-</td>";
                        htmldata += "<td class='text-right cgstcol' id='cgst"+invoiceproducts[i]['transactionproductsid']+"' style='border: 1px solid #e8e8e8;'>-</td>";
                        htmldata += "<td class='text-right igstcol' id='igst"+invoiceproducts[i]['transactionproductsid']+"' style='border: 1px solid #e8e8e8;'>"+parseFloat(taxamount).toFixed(2)+"</td>";
                      }
                    }else{
                      if(invoiceproducts[i]['igst']==1){
                        htmldata += "<td class='text-right sgstcol' id='sgst"+invoiceproducts[i]['transactionproductsid']+"'>"+parseFloat((taxamount/2)).toFixed(2)+"</td>";
                        htmldata += "<td class='text-right cgstcol' id='cgst"+invoiceproducts[i]['transactionproductsid']+"'>"+parseFloat((taxamount/2)).toFixed(2)+"</td>";
                        htmldata += "<td class='text-right igstcol' id='igst"+invoiceproducts[i]['transactionproductsid']+"' style='border: 1px solid #e8e8e8;'>-</td>";
                      }else{
                        htmldata += "<td class='text-right sgstcol' id='sgst"+invoiceproducts[i]['transactionproductsid']+"'>-</td>";
                        htmldata += "<td class='text-right cgstcol' id='cgst"+invoiceproducts[i]['transactionproductsid']+"' style='border: 1px solid #e8e8e8;'>-</td>";
                        htmldata += "<td class='text-right igstcol' id='igst"+invoiceproducts[i]['transactionproductsid']+"'>"+parseFloat(taxamount).toFixed(2)+"</td>";
                      }
                      if(parseFloat(discount) > 0){
                        htmldata += "<td class='text-right disccol' id='discountamount"+invoiceproducts[i]['transactionproductsid']+"' style='border: 1px solid #e8e8e8;'>"+parseFloat(discountamount).toFixed(2)+"</td>";
                      }else{
                        htmldata += "<td class='text-right disccol' style='border: 1px solid #e8e8e8;'>-</td>";
                      }
                    }
                    htmldata += "</tr>"; 
                  }
                }else{
                  $("#creditnoteproducttable tbody").html("<tr><td colspan='16' class='text-center'>No data available in table.</td></tr>");
                }
              }
              $("#creditnoteproducttable thead").html(headerdata);
              $("#creditnoteproducttable tbody").html(htmldata);
              if(discolumn > 0){
                $('.disccol').show();
              }else{
                $('.disccol').hide();
              }
              if(gstcolumn.includes("1")){
                $('.igstcol').hide();
                $('.cgstcol,.sgstcol').show();
              }/* else{
                $('.igstcol').show();
                $('.cgstcol,.sgstcol').hide();
              } */

              if(gstcolumn.includes("0")){
                $('.igstcol').show();
              }
              /* $(".creditqty").TouchSpin({
                initval: 0,
                min: 1,
                max: 9999,
                // decimals: 3,
                verticalbuttons: true,
                verticalupclass: 'glyphicon glyphicon-plus',
                verticaldownclass: 'glyphicon glyphicon-minus'
              }); */
              var html = extrachargespanel = '';
              if(invoiceamountdata!=null && invoiceamountdata!=""){
                if(invoiceamountdata.length>0){
                  var invoiceextracharge = [];
                  for(var i=0; i<invoiceamountdata.length; i++){
                    var redeemamountrows = extrachargesrows = extrachargeshtml = '';
                    var invoiceId = invoiceamountdata[i]['id'];
                    var paddstyle = "padding-left";
                    if(i%2==0){
                      paddstyle = "padding-right";
                    }
                    
                    var extracharges = invoiceamountdata[i]['extracharges'];
                    var totalextracharges = 0;
                    var extracharge = [];
                    if(extracharges.length>0){
                      for(var j=0; j<extracharges.length; j++){
                        extrachargesrows +=  '<tr>\
                                              <td>'+extracharges[j]['extrachargesname']+'</td>\
                                              <th> : </th>\
                                              <td class="text-right">'+parseFloat(extracharges[j]['amount']).toFixed(2)+'</td>\
                                            </tr>';

                        totalextracharges += parseFloat(extracharges[j]['amount']);

                        extracharge.push(extracharges[j]['extrachargesid']);
                        
                        extrachargeshtml += '<div class="col-md-6 p-n countcharges'+invoiceId+'" id="countcharges_'+invoiceId+'_'+(j+1)+'">\
                                              <div class="col-sm-7 pr-xs">\
                                                  <div class="form-group" id="extracharges_'+invoiceId+'_'+(j+1)+'_div">\
                                                      <div class="col-sm-12">\
                                                          <select id="invoiceextrachargesid_'+invoiceId+'_'+(j+1)+'" name="invoiceextrachargesid['+invoiceId+'][]" class="selectpicker form-control invoiceextrachargesid" data-live-search="true" data-select-on-tab="true" data-size="5" data-live-search="true">\
                                                            <option value="0">Select Extra Charges</option>\
                                                            '+extrachargeoptionhtml+'\
                                                          </select>\
                                                          <input type="hidden" name="invoiceextrachargestax['+invoiceId+'][]" id="invoiceextrachargestax_'+invoiceId+'_'+(j+1)+'" class="invoiceextrachargestax" value="'+extracharges[j]['taxamount']+'">\
                                                          <input type="hidden" name="invoiceextrachargesname['+invoiceId+'][]" id="invoiceextrachargesname_'+invoiceId+'_'+(j+1)+'" class="invoiceextrachargesname" value="'+extracharges[j]['extrachargesname']+'">\
                                                          <input type="hidden" name="invoiceextrachargepercentage['+invoiceId+'][]" id="invoiceextrachargepercentage_'+invoiceId+'_'+(j+1)+'" class="invoiceextrachargepercentage" value="'+extracharges[j]['extrachargepercentage']+'">\
                                                      </div>\
                                                  </div>\
                                              </div>\
                                              <div class="col-sm-3 pl-xs pr-xs">\
                                                  <div class="form-group p-n" id="invoiceextrachargeamount_'+invoiceId+'_'+(j+1)+'_div">\
                                                      <div class="col-sm-12">\
                                                          <input type="text" id="invoiceextrachargeamount_'+invoiceId+'_'+(j+1)+'" name="invoiceextrachargeamount['+invoiceId+'][]" class="form-control text-right invoiceextrachargeamount" placeholder="Charge" onkeypress="return decimal_number_validation(event, this.value)" value="'+parseFloat(extracharges[j]['amount']).toFixed(2)+'">\
                                                      </div>\
                                                  </div>\
                                              </div>\
                                              <div class="col-md-2 text-right pt-md">\
                                                <button type="button" class="btn btn-default btn-raised remove_charges_btn" onclick="removecharge('+invoiceId+','+(j+1)+')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
                                              </div>\
                                          </div>';
                      }
                    }
                    invoiceextracharge[String(invoiceId)] = extracharge;

                    var invoicediscountpercent = (parseFloat(invoiceamountdata[i]['discountamount']) * 100 / (parseFloat(invoiceamountdata[i]['orderamount']) + parseFloat(invoiceamountdata[i]['taxamount'])));
                    var discount_text = '';
                   
                    if(parseFloat(invoicediscountpercent) > 0){
                      discount_text += '<div class="col-md-2 pr-sm">\
                                          <div class="form-group p-n text-right" id="invoicediscountpercent'+invoiceId+'_div">\
                                            <div class="col-sm-12">\
                                              <label class="control-label" for="invoicediscountpercent'+invoiceId+'">Discount (%)</label>\
                                              <input type="text" id="invoicediscountpercent'+invoiceId+'" name="invoicediscountpercent['+invoiceId+']" class="form-control text-right invoicediscountpercent" placeholder="" onkeypress="return decimal_number_validation(event, this.value)" value="'+parseFloat(invoicediscountpercent).toFixed(2)+'">\
                                            </div>\
                                          </div>\
                                        </div>\
                                        <div class="col-md-3 pl-sm pr-sm">\
                                          <div class="form-group p-n text-right" id="invoicediscountamount'+invoiceId+'_div">\
                                            <div class="col-sm-12">\
                                              <label class="control-label" for="invoicediscountamount'+invoiceId+'">Discount Amount</label>\
                                              <input type="text" id="invoicediscountamount'+invoiceId+'" name="invoicediscountamount['+invoiceId+']" class="form-control text-right invoicediscountamount" placeholder="" onkeypress="return decimal_number_validation(event, this.value)" value="'+parseFloat(invoiceamountdata[i]['discountamount']).toFixed(2)+'">\
                                              <label class="control-label p-n m-n mb-xs">Max : '+CURRENCY_CODE+' <span id="applymaxdisc'+invoiceId+'"></span></label>\
                                              <input type="hidden" id="crndiscamnt'+invoiceId+'" value="'+invoiceamountdata[i]['discountamount']+'">\
                                              <input type="hidden" id="invoicediscamnt'+invoiceId+'" value="'+invoiceamountdata[i]['discountamount']+'">\
                                            </div>\
                                          </div>\
                                        </div>';
                    }
                    if(extrachargeshtml != "" || discount_text != ""){

                      var invoicegrossamount = parseFloat(invoiceamountdata[i]['orderamount']) + parseFloat(invoiceamountdata[i]['taxamount']);
                      
                      //ordergrossamount = (parseInt($("#quantity"+orderid).val()) * parseFloat(ordergrossamount) / parseInt($("#orderquantity"+orderid).val()));

                      extrachargespanel += '<div class="panel countinvoice" id="'+invoiceId+'">\
                                              <div class="panel-heading">\
                                                <h2 style="width: 35%;"><b>Invoice No. :</b> '+invoiceamountdata[i]['invoiceno']+'</h2>\
                                                <h2 style="width: 33%;"><b>Product Total : </b><span id="displayproducttotal'+invoiceId+'">0.00</span></h2>\
                                              </div>\
                                              <div class="panel-body no-padding">\
                                                <div class="row m-n">\
                                                '+extrachargeshtml+'\
                                                </div>\
                                              <input type="hidden" name="invoicegrossamount[]" id="invoicegrossamount_'+invoiceId+'" class="invoicegrossamount" value="'+parseFloat(invoicegrossamount).toFixed(2)+'">\
                                              <input type="hidden" name="crnorderamount[]" id="crnorderamount_'+invoiceId+'" class="crnorderamount" value="'+parseFloat(invoicegrossamount).toFixed(2)+'">\
                                                <div class="row m-n">\
                                                  '+discount_text+'\
                                                </div>\
                                              </div>\
                                            </div>\
                                          </div>';
                    }


                    var discountrows = couponrows = '';
                    if(parseFloat(invoiceamountdata[i]['discountamount']) > 0){
                      discountrows = '<tr>\
                                        <td>Discount Amount</td>\
                                        <th> : </th>\
                                        <td class="text-right">'+format.format(invoiceamountdata[i]['discountamount'])+'</td>\
                                      </tr>';
                    }
                    if(parseFloat(invoiceamountdata[i]['couponamount']) > 0){
                      couponrows = '<tr>\
                                      <td>Coupon Amount</td>\
                                      <th> : </th>\
                                      <td class="text-right">'+format.format(invoiceamountdata[i]['couponamount'])+'</td>\
                                    </tr>';
                    }
                    if(parseFloat(invoiceamountdata[i]['redeemamount'])>0){
                      redeemamountrows =  '<tr>\
                                            <td>Redeem Amount</td>\
                                            <th> : </th>\
                                            <td class="text-right">'+parseFloat(invoiceamountdata[i]['redeemamount']).toFixed(2)+'</td>\
                                          </tr>';
                    }
                    var netamount = (parseFloat(invoiceamountdata[i]['netamount']) + parseFloat(totalextracharges));
                    if(parseFloat(netamount) < 0){
                      netamount = 0;
                    }
                    html += '<div class="col-sm-4 pl-sm pr-sm" style="margin-bottom:10px;min-height: 200px;">\
                              <table class="table m-n orderamounttable" style="border: 5px solid #e8e8e8;">\
                                <tr>\
                                  <th>Invoice No.</th>\
                                  <th> : </th>\
                                  <td><a href="'+SITE_URL+'invoice/view-invoice/'+invoiceamountdata[i]['id']+'" target="_blank">'+invoiceamountdata[i]['invoiceno']+'</a></td>\
                                </tr>\
                                <tr style="border-bottom: 2px solid #E8E8E8;">\
                                  <th>Invoice Date</th>\
                                  <th> : </th>\
                                  <td>'+invoiceamountdata[i]['invoicedate']+'</td>\
                                </tr>\
                                <tr>\
                                  <td>Invoice Amount</td>\
                                  <th> : </th>\
                                  <td class="text-right">'+format.format(invoiceamountdata[i]['invoiceamount'])+'</td>\
                                </tr>\
                                <tr>\
                                  <td>Tax Amount</td>\
                                  <th> : </th>\
                                  <td class="text-right">'+format.format(invoiceamountdata[i]['taxamount'])+'</td>\
                                </tr>\
                                '+discountrows+'\
                                '+couponrows+'\
                                '+redeemamountrows+'\
                                '+extrachargesrows+'\
                                <tr>\
                                  <th>Net Amount</th>\
                                  <th> : </th>\
                                  <th class="text-right">'+format.format(netamount)+'</th>\
                                </tr>\
                              </table>\
                            </div>';
                  }
                  $('#extracharges_div').html(extrachargespanel);
                  $('.invoiceextrachargesid').selectpicker("refresh");
                  $('#invoiceamountdiv').html(html);

                  for(var k=0; k<invoiceamountdata.length; k++){
                    var InvoiceID = invoiceamountdata[k]['id'];
                    
                    for(var l=0; l<invoiceextracharge[InvoiceID].length; l++){
                      
                      var extrachargesid = invoiceextracharge[InvoiceID][l];
                      $("#invoiceextrachargesid_"+InvoiceID+"_"+(l+1)).val(extrachargesid);
                      $("#invoiceextrachargesid_"+InvoiceID+"_"+(l+1)).selectpicker('refresh');
                      $("#invoiceextrachargesid_"+InvoiceID+"_"+(l+1)+" option:not(:selected)").remove();
                      $("#invoiceextrachargesid_"+InvoiceID+"_"+(l+1)).selectpicker('refresh');
                    }
                    calculateinvoiceamount(InvoiceID);
                  }

                  /****EXTRA CHARGE CHANGE EVENT****/
                  $('body').on('change', 'select.invoiceextrachargesid', function() { 
                    var rowid = $(this).attr("id").split('_');
                    var invoiceid = rowid[1];
                    var divid = rowid[2];
                    calculateextracharges(invoiceid,divid);
                    changechargespercentage(invoiceid,divid);
                    overallextracharges();
                    netamounttotal();
                  });
                  $('body').on('keyup', '.invoiceextrachargeamount', function() { 
                    var rowid = $(this).attr("id").split('_');
                    var invoiceid = rowid[1];
                    var divid = rowid[2];
                    var grossamount = $("#crnorderamount_"+invoiceid).val();
                  
                    var chargestaxamount = chargespercent = 0;
                    var tax = $("#invoiceextrachargesid_"+invoiceid+"_"+divid+" option:selected").attr("data-tax");
                    var type = $("#invoiceextrachargesid_"+invoiceid+"_"+divid+" option:selected").attr("data-type");
                   
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
                    $("#invoiceextrachargestax_"+invoiceid+"_"+divid).val(parseFloat(chargestaxamount).toFixed(2));
                    $("#invoiceextrachargepercentage_"+invoiceid+"_"+divid).val(parseFloat(chargespercent).toFixed(2));
                    changechargespercentage(invoiceid,divid);
                    overallextracharges();
                    netamounttotal();
                  });
                  $('.invoicediscountpercent').on('keyup', function() { 
                    var invoiceid = $(this).attr("id").match(/\d+/);
                    var discountpercentage = $(this).val();
                    var crndiscamnt = $("#crndiscamnt"+invoiceid).val();
                    var grossamount = parseFloat(crndiscamnt);
                   
                    if(discountpercentage!=undefined && discountpercentage!=''){
                      if(parseFloat(discountpercentage)>100){
                        $(this).val("100");
                        discountpercentage = 100;
                      }
                      
                      if(grossamount!=''){
                        var discountamount = (parseFloat(grossamount)*parseFloat(discountpercentage)/100);
                        
                        $("#invoicediscountamount"+invoiceid).val(parseFloat(discountamount).toFixed(2));
                        
                        $("#ovdiscper").html(parseFloat(discountpercentage).toFixed(2)); 
                        $("#ovdiscamnt").html(parseFloat(discountamount).toFixed(2)); 
                        $("#inputovdiscamnt").val(parseFloat(discountamount).toFixed(2));

                        overallextracharges();
                        netamounttotal();
                      }
                    }else{
                      $(this).val('');
                      $("#invoicediscountamount"+invoiceid).val('');
                      $("#ovdiscper").html("0"); 
                      $("#ovdiscamnt").html("0.00"); 
                      $("#inputovdiscamnt").val('0.00');
                      overallextracharges();
                      netamounttotal();
                    }
                  });
                  $('.invoicediscountamount').on('keyup', function() { 
                    var invoiceid = $(this).attr("id").match(/\d+/);
                    var discountamount = $(this).val();
                    var discountpercentage = $("#ovdiscper").html();
                    var crndiscamnt = $("#crndiscamnt"+invoiceid).val();
                    var grossamount = parseFloat(crndiscamnt);
                    
                    
                    if(discountamount!=undefined && discountamount!=''){
                       
                        if(parseFloat(discountamount)>parseFloat(grossamount)){
                          grossamount = (parseFloat(grossamount)>0)?parseFloat(grossamount):0;
                          $(this).val(parseFloat(grossamount));
                          discountamount = parseFloat(grossamount);
                        }
                        if(parseFloat(grossamount)!=''){
                            var discountpercentage = ((parseFloat(discountamount)*100) / parseFloat(grossamount));
                            if(parseFloat(discountpercentage)==0){
                                $("#invoicediscountpercent"+invoiceid).val(0);   
                            }else{
                                $("#invoicediscountpercent"+invoiceid).val(parseFloat(discountpercentage).toFixed(2));   
                            }
            
                            $("#ovdiscper").html(parseFloat(discountpercentage).toFixed(2)); 
                            $("#ovdiscamnt").html(parseFloat(discountamount).toFixed(2)); 
                            $("#inputovdiscamnt").val(parseFloat(discountamount).toFixed(2));
                            if(parseFloat(discountpercentage)>100){
                                $("#invoicediscountpercent"+invoiceid).val("100");
                            }
                           
                            overallextracharges();
                            netamounttotal();
                        }
                    }else{
                        $(this).val('');
                        $("#invoicediscountpercent"+invoiceid).val('');
                        $("#ovdiscper").html("0"); 
                        $("#ovdiscamnt").html("0.00"); 
                        $("#inputovdiscamnt").val('0.00');
                        overallextracharges();
                        netamounttotal();
                    }
                  });
                }
              }else{
                $('#invoiceamountdiv').html("");
              }
              
              $('body').on('keyup', '.creditqty', function(e) {
                var elementid = e.target.id;
                elementid = elementid.replace ( /[^\d.]/g, '' );
                var invoiceid = $("#invoiceidarr"+elementid).val();
                loaddatacredittotalandtax(elementid);
                totalproductamount(invoiceid,elementid);
              });
              $('body').on('change', '.creditqty', function(e) {
                var elementid = e.target.id;
                elementid = elementid.replace ( /[^\d.]/g, '' );
                var invoiceid = $("#invoiceidarr"+elementid).val();
                loaddatacredittotalandtax(elementid);
                totalproductamount(invoiceid,elementid);
              });
              $('body').on('keyup', '.creditpercent', function(e) {
                var elementid = e.target.id;
                elementid = elementid.replace ( /[^\d.]/g, '' );
                loaddatacredittotal(elementid);
                    
              });
              $('body').on('keyup', '.creditamount', function(e) {
                var elementid = e.target.id;
                elementid = elementid.replace ( /[^\d.]/g, '' );
                loaddatacredittax(elementid);
              });
              /* $('body').on('keyup', '.stockqty', function(e) {
                var elementid = e.target.id;
                elementid = elementid.replace ( /[^\d.]/g, '' );
                loaddatastockqty(elementid);
              });
              $('body').on('keyup', '.rejectqty', function(e) {
                var elementid = e.target.id;
                elementid = elementid.replace ( /[^\d.]/g, '' );
                loaddatarejectqty(elementid);
              }); */
              $('body').on('keyup', '.qtyclass', function(e) {
                var elementid = e.target.id.split("_");
                var ele1 = elementid[1];
                var ele2 = elementid[2];
                var max = $("#stockqtys_"+ele1+"_"+ele2).attr("data-max");
                if(this.value!="" && parseFloat(this.value) > parseFloat(max)){
                  $(this).val(max);
                }
                
                if(this.value!="" && (parseFloat(this.value) + parseFloat($("#scrapqtys_"+ele1+"_"+ele2).val())>max)){
                    new PNotify({title: "Stock and reject qty not more then "+max+" quantity !",styling: 'fontawesome',delay: '3000',type: 'error'});
                    $(this).val('');
                }else{
                  loaddatastockqty(ele1);
                }
              });

              $('body').on('keyup', '.scrapqtyclass', function(e) {
                var elementid = e.target.id.split("_");
                var ele1 = elementid[1];
                var ele2 = elementid[2];
                var max = $("#stockqtys_"+ele1+"_"+ele2).attr("data-max");
                if(this.value!="" && parseFloat(this.value) > parseFloat(max)){
                  $(this).val(max);
                }

                if(this.value!="" && (parseFloat(this.value) + parseFloat($("#stockqtys_"+ele1+"_"+ele2).val())>max)){
                  new PNotify({title: "Stock and reject qty not more then "+max+" quantity !",styling: 'fontawesome',delay: '3000',type: 'error'});
                  $(this).val('');
                }else{
                  loaddatarejectqty(ele1);
                }
              });
            }
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
      });
    }else{
      $("#creditnoteproducttable tbody").html("<tr><td colspan='16' class='text-center'>No data available in table.</td></tr>");
      $('#invoiceamountdiv').html("");
      // $('#extracharges_div').html("");
      $('#billingaddress').val('');
      $('#shippingaddress').val('');
    }
}

function totalproductamount(invoiceid,divid) {
  var quantity = $("#creditqty"+divid).val();
  var taxtype = $("#taxtype"+divid).val();
  var tax = $("#tax"+divid).val();
  var price = $("#price"+divid).val();
  var actualprice = $("#actualprice"+divid).val();
  var rate = $("#rate"+divid).val();
  var discount = $("#discount"+divid).val();
  
  var discountamount = ((parseFloat(actualprice) * parseFloat(quantity)) * parseFloat(discount) / 100);
  var totalprice = (parseFloat(rate) * parseFloat(quantity));
  var taxvalue = parseFloat(parseFloat(totalprice) * parseFloat(tax) / 100);
  var totalwithouttax = parseFloat(totalprice);
  
  $("#taxvalue"+divid).val(parseFloat(taxvalue).toFixed(2));
  $("#producttotal"+divid).val(parseFloat(totalprice).toFixed(2));
  $("#producttotalwithouttax"+divid).val(parseFloat(totalwithouttax).toFixed(2));
  calculateinvoiceamount(invoiceid);
  changeextrachargesamount();
  overallextracharges();
  netamounttotal();
}
function calculateinvoiceamount(InvoiceID){
  var creditqty = invoiceqty = creditamount = 0;
  $(".invoicequantity"+InvoiceID).each(function( index ) {
    var divid = $(this).attr("id").match(/(\d+)/g);
    if($(this).val()!=""){
      invoiceqty += parseFloat($(this).val());
    }
    if($("#creditqty"+divid).val()!=""){
      creditqty += parseFloat($("#creditqty"+divid).val());
      creditamount += parseFloat($("#creditamount"+divid).val());
    }
  });
  // var invoicegrossamount = (parseInt(creditqty) * parseFloat($("#invoicegrossamount_"+InvoiceID).val()) / parseInt(invoiceqty));
  $("#crnorderamount_"+InvoiceID).val(parseFloat(creditamount).toFixed(2));
  $("#displayproducttotal"+InvoiceID).html(format.format(creditamount));
  changeextrachargesamount();

  var crndiscamnt = (parseFloat(creditqty) * parseFloat($("#invoicediscamnt"+InvoiceID).val()) / parseFloat(invoiceqty));
  var crndiscper = (parseFloat(crndiscamnt) * 100 / parseFloat(crndiscamnt));
  $("#invoicediscountamount"+InvoiceID).val(parseFloat(crndiscamnt).toFixed(2));
  $("#crndiscamnt"+InvoiceID).val(parseFloat(crndiscamnt).toFixed(2));
  $("#applymaxdisc"+InvoiceID).html(parseFloat(crndiscamnt).toFixed(2));
  $("#invoicediscountpercent"+InvoiceID).val(parseFloat(crndiscper).toFixed(2));
}
function loaddatacredittotalandtax(productcount){
    
    var quantity=price=1;
    var creditpercent=amount=creditqty=0;
    creditqty = $('#creditqty'+productcount).val();
    qty = $('#qty'+productcount).val();   
    amount = $('#price'+productcount).val();
    oldqty = parseFloat($('#paidqty'+productcount).val());
    
    var stockqty = rejectqty = 0;
    $(".stockqtys"+productcount).each(function(){
      if($(this).val()!=""){
        stockqty += parseFloat($(this).val());
      }
    });
    $(".scrapqtys"+productcount).each(function(){
      if($(this).val()!=""){
        rejectqty += parseFloat($(this).val());
      }
    });
    // var stockqty = ($('#productstockqty'+productcount).val()=="")?0:$('#productstockqty'+productcount).val();          
    // var rejectqty = ($('#productrejectqty'+productcount).val()=="")?0:$('#productrejectqty'+productcount).val();
    var instockqty = parseFloat(stockqty) + parseFloat(rejectqty);
    
    if(parseFloat(instockqty)>parseFloat(creditqty)){
      // $('#productstockqty'+productcount).val('');
      // $('#productrejectqty'+productcount).val('');
      $('.stockqtys'+productcount).val("");
      $('.scrapqtys'+productcount).val("");
    }
    PNotify.removeAll();
    if(creditqty!='' && parseFloat(creditqty)!=0){
      
        if(parseFloat(creditqty)<=(parseFloat(qty)-parseFloat(oldqty))){
            
            creditamount = parseFloat(creditqty) * parseFloat(amount);
            $('#creditamount'+productcount).val(parseFloat(creditamount).toFixed(2));
            //creditpercent  =parseFloat((creditamount)*100)/ (parseFloat(amount));
            $('#creditpercent'+productcount).val("100.00");
        }else{
            new PNotify({title: "Credit quantity is not more then remain credit quantity !",styling: 'fontawesome',delay: '3000',type: 'error'});
            $('#creditqty'+productcount).val("");
            $('#creditpercent'+productcount).val("");
            $('#creditamount'+productcount).val("");
        }
    }else{
        $('#creditqty'+productcount).val("");
        $('#creditpercent'+productcount).val("");
        $('#creditamount'+productcount).val("");
    }
    overallextracharges();
    netamounttotal();
}
function loaddatacredittotal(productcount){

    var quantity=price=1;
    var creditpercent=amount=0;
    var creditqty = $('#creditqty'+productcount).val();
    creditpercent = $('#creditpercent'+productcount).val();            
    amount = $('#price'+productcount).val();

    creditamount  = parseFloat(amount) * parseFloat(creditqty) * parseFloat(creditpercent) / 100;
    if(creditpercent!=''){
      if(creditamount){

          if(creditpercent>100){

              $('#creditpercent'+productcount).val("100.00");
              $('#creditamount'+productcount).val(parseFloat(amount).toFixed(2));
          }else{
              $('#creditamount'+productcount).val(parseFloat(creditamount).toFixed(2));
          }
          
      }else{
          $('#creditamount'+productcount).val("");
      }
    }else{
      $('#creditamount'+productcount).val("");
    }
    overallextracharges();
    netamounttotal();;
}
function loaddatacredittax(productcount){

    var creditpercent=0;
    var creditamount=amount=0;
    var creditqty = $('#creditqty'+productcount).val();
    creditamount = $('#creditamount'+productcount).val();          
    amount = $('#price'+productcount).val();
    var actualprice = $('#actualprice'+productcount).val();
    oldamount = $('#paidcredit'+productcount).val();
    PNotify.removeAll();

    amount  = parseFloat(actualprice) * parseFloat(creditqty);

    if(creditamount!=''){

        if(parseFloat(creditamount)<=parseFloat(amount)){
          
            creditamount  = parseFloat(creditamount) * 100;
            creditpercent  =parseFloat(creditamount)/ (parseFloat(amount));
              
              if(creditpercent){

                if(creditpercent>100){
                    new PNotify({title: "Credit value is not more then total value !",styling: 'fontawesome',delay: '3000',type: 'error'});
                    $('#creditpercent'+productcount).val("");
                }else{
                    $('#creditpercent'+productcount).val(parseFloat(creditpercent).toFixed(2));
                
                }
              }else{
                $('#creditpercent'+productcount).val("");
              }
        }else{
            new PNotify({title: "Credit value is not more then remain credit value !",styling: 'fontawesome',delay: '3000',type: 'error'});
            $('#creditpercent'+productcount).val("");
            $('#creditamount'+productcount).val("");
        }
    }
    overallextracharges();
    netamounttotal();
}
function loaddatastockqty(productcount){

    var stockqty=rejectqty=creditqty=0;
    
    $(".stockqtys"+productcount).each(function(){
      if($(this).val()!=""){
        div_id = $(this).attr('id').split('_')[2];
        // console.log(div_id)
        stockqty += parseFloat($(this).val());

      }
    });
    $(".scrapqtys"+productcount).each(function(){
      if($(this).val()!=""){
        div_id = $(this).attr('id').split('_')[2];
        rejectqty += parseFloat($(this).val());
      }
    });
    // stockqty = ($('.stockqtys'+productcount).val()=="")?0:$('#productstockqty'+productcount).val();         
    // rejectqty = ($('#productrejectqty'+productcount).val()=="")?0:$('#productrejectqty'+productcount).val();
    creditqty = $('#creditqty'+productcount).val();
    
    PNotify.removeAll();
  
    if(parseFloat(stockqty)>0){

        instockqty = parseFloat(stockqty) + parseFloat(rejectqty);
        if(parseFloat(instockqty)<=parseFloat(creditqty)){
          /* if(MANAGE_DECIMAL_QTY==1){
            $('#productstockqty'+productcount).val(parseFloat(stockqty).toFixed(2));
          }else{
            $('#productstockqty'+productcount).val(parseInt(stockqty));
          } */
        }else{
          new PNotify({title: "Stock quantity is not more then remain credit quantity !",styling: 'fontawesome',delay: '3000',type: 'error'});
          $('.stockqtys'+productcount).val("");
          $('.stockqtys'+productcount).parent().addClass('has-error is-focused');
        }
    }
}
function loaddatarejectqty(productcount){

    var stockqty=rejectqty=creditqty=0;
    $(".stockqtys"+productcount).each(function(){
      if($(this).val()!=""){
        stockqty += parseFloat($(this).val());
      }
    });
    $(".scrapqtys"+productcount).each(function(){
      if($(this).val()!=""){
        rejectqty += parseFloat($(this).val());
      }
    });
    // stockqty = ($('#productstockqty'+productcount).val()=="")?0:$('#productstockqty'+productcount).val();          
    // rejectqty = ($('#productrejectqty'+productcount).val()=="")?0:$('#productrejectqty'+productcount).val();
    creditqty = $('#creditqty'+productcount).val();

    PNotify.removeAll();
  
    if(parseFloat(rejectqty)>0){

      rejectedqty = parseFloat(stockqty) + parseFloat(rejectqty);
      if(parseFloat(rejectedqty)<=parseFloat(creditqty)){
        /* if(MANAGE_DECIMAL_QTY==1){
          $('#productrejectqty'+productcount).val(parseFloat(rejectqty).toFixed(2));
        }else{
          $('#productrejectqty'+productcount).val(parseInt(rejectqty));
        } */
      }else{
        new PNotify({title: "Reject quantity is not more then remain credit quantity !",styling: 'fontawesome',delay: '3000',type: 'error'});
        // $('#productrejectqty'+productcount).val("");

        $('.scrapqtys'+productcount).val("");
        $('.scrapqtys'+productcount).parent().addClass('has-error is-focused');
      }
    }
}
function changeextrachargesamount(){
 
  $(".invoiceextrachargeamount").each(function( index ) {
    var element = $(this).attr("id").split('_');
    var invoiceid = element[1];
    var divid = element[2];
    calculateextracharges(invoiceid,divid);
  });
}
function calculateextracharges(invoiceid,rowid){
  var extracharges = $("#invoiceextrachargesid_"+invoiceid+"_"+rowid).val();
  var type = $("#invoiceextrachargesid_"+invoiceid+"_"+rowid+" option:selected").attr("data-type");
  var amount = $("#invoiceextrachargesid_"+invoiceid+"_"+rowid+" option:selected").attr("data-amount");
  var tax = $("#invoiceextrachargesid_"+invoiceid+"_"+rowid+" option:selected").attr("data-tax");

  var totalgrossamount = $("#crnorderamount_"+invoiceid).val();
                    
  var chargesamount = chargestaxamount = 0;
  if(parseFloat(totalgrossamount)>0 && parseFloat(extracharges) > 0){
      if(type==0){
          chargesamount = parseFloat(totalgrossamount) * parseFloat(amount) / 100;
      }else{
          chargesamount = parseFloat(amount);
      }
      
      chargestaxamount = parseFloat(chargesamount) * parseFloat(tax) / (100+parseFloat(tax));
      $("#invoiceextrachargestax_"+invoiceid+"_"+rowid).val(parseFloat(chargestaxamount).toFixed(2));
      $("#invoiceextrachargeamount_"+invoiceid+"_"+rowid).val(parseFloat(chargesamount).toFixed(2));
  }else{
      $("#invoiceextrachargestax_"+invoiceid+"_"+rowid).val(parseFloat(0).toFixed(2));
      $("#invoiceextrachargeamount_"+invoiceid+"_"+rowid).val(parseFloat(0).toFixed(2));
  }
  var chargesname = $("#invoiceextrachargesid_"+invoiceid+"_"+rowid+" option:selected").text();
  $("#invoiceextrachargesname_"+invoiceid+"_"+rowid).val(chargesname.trim());
  var chargespercent = 0;
  if(type==0){
      chargespercent = parseFloat(amount);
  }
  $("#invoiceextrachargepercentage_"+invoiceid+"_"+rowid).val(parseFloat(chargespercent).toFixed(2));
  netamounttotal();
}
function changechargespercentage(invoiceid,divid){
  var type = $("#invoiceextrachargesid_"+invoiceid+"_"+divid+" option:selected").attr("data-type");
  var optiontext = $("#invoiceextrachargesid_"+invoiceid+"_"+divid+" option:selected").text();
  var grossamount = $("#crnorderamount_"+invoiceid).val();
  var amount = $("#invoiceextrachargeamount_"+invoiceid+"_"+divid).val();
  var chargespercent = 0;
 
  if(type==0){
    if(parseFloat(amount)> 0){
      chargespercent = parseFloat(amount) * 100 / parseFloat(grossamount);
    }
    optiontext = optiontext.split("(");
    $("#invoiceextrachargesid_"+invoiceid+"_"+divid+" option:selected").text(optiontext[0].trim()+" ("+parseFloat(chargespercent).toFixed(2)+"%)");
    $("#invoiceextrachargesid_"+invoiceid+"_"+divid).selectpicker("refresh");
    $("#invoiceextrachargesname_"+invoiceid+"_"+divid).val(optiontext[0].trim()+" ("+parseFloat(chargespercent).toFixed(2)+"%)");
  }
}
function overallextracharges(){
  
  /********* CALCULATE EXTRA CHARGES START *********/
  var extrachargesrow = '';
  var CHARGES_ARR = [];
  var extrachargesamnt = [];
  $(".tr_extracharges").remove();
  $("select.invoiceextrachargesid").each(function( index ) {
    var element = $(this).attr("id").split('_');
    var invoiceid = element[1];
    var divid = element[2];
    var extrachargesname = $("#invoiceextrachargesname_"+invoiceid+"_"+divid).val();
    var extrachargeamount = $("#invoiceextrachargeamount_"+invoiceid+"_"+divid).val();
    var extrachargestax = $("#invoiceextrachargestax_"+invoiceid+"_"+divid).val();
    var extrachargepercentage = $("#invoiceextrachargepercentage_"+invoiceid+"_"+divid).val();
    var extrachargesdatatype = $("#invoiceextrachargesid_"+invoiceid+"_"+divid+" option:selected").attr("data-type");
    var extrachargesid = $(this).val();

    extrachargeamount = (parseFloat(extrachargeamount)>0)?parseFloat(extrachargeamount):0;

    if(extrachargesid!=0){

      if(!CHARGES_ARR.includes(extrachargesid)){

        extrachargesrow += "<tr class='tr_extracharges' id='tr_extracharges_"+extrachargesid+"'>";
        extrachargesrow += "<td>"+extrachargesname+"</td>";
        extrachargesrow += "<td class='text-right'><span id='extrachargeamount"+extrachargesid+"'>"+parseFloat(extrachargeamount).toFixed(2)+"</span>";
        
        extrachargesrow += '<input type="hidden" name="extrachargesid[]" id="extrachargesid'+extrachargesid+'" value="'+extrachargesid+'">';

        extrachargesrow += '<input type="hidden" name="extrachargeamount[]" id="inputextrachargeamount'+extrachargesid+'" value="'+parseFloat(extrachargeamount).toFixed(2)+'">';

        extrachargesrow += '<input type="hidden" id="extrachargestax'+extrachargesid+'" name="extrachargestax[]" value="'+parseFloat(extrachargestax).toFixed(2)+'">';
        
        extrachargesrow += '<input type="hidden" name="extrachargesname[]" id="extrachargesname'+extrachargesid+'" value="'+extrachargesname+'">';

        extrachargesrow += '<input type="hidden" name="extrachargepercentage[]" id="extrachargepercentage'+extrachargesid+'" value="'+parseFloat(extrachargepercentage).toFixed(2)+'">';

        extrachargesrow += '<input type="hidden" name="extrachargesdatatype[]" id="extrachargesdatatype'+extrachargesid+'" value="'+parseInt(extrachargesdatatype)+'">';

        extrachargesrow += "</td>";
        extrachargesrow += "</tr>";

        CHARGES_ARR.push(extrachargesid);
        
      }else{

        var sumamount = sumtax = type = 0;
        $("select.invoiceextrachargesid").each(function( index ) {
          var elementid = $(this).attr("id").split('_');
          var InvoiceId = elementid[1];
          var Id = elementid[2];
          var thisid = $(this).val();
          var sumchargeamount = $("#invoiceextrachargeamount_"+InvoiceId+"_"+Id).val();
          var sumchargetax = $("#invoiceextrachargestax_"+InvoiceId+"_"+Id).val();
          var thisid = $(this).val();
          var thistype = $("#invoiceextrachargesid_"+InvoiceId+"_"+Id+" option:selected").attr("data-type");
          sumchargeamount = (parseFloat(sumchargeamount)>0)?parseFloat(sumchargeamount):0;
          sumchargetax = (parseFloat(sumchargetax)>0)?parseFloat(sumchargetax):0;

          if(thisid == extrachargesid){
            sumamount += parseFloat(sumchargeamount);
            sumtax += parseFloat(sumchargetax);
            type = thistype;
          }
        });
        extrachargesamnt.push(extrachargesid+'_'+parseFloat(sumamount).toFixed(2)+'_'+parseFloat(sumtax).toFixed(2)+'_'+type);
      }
    }
  });
  
  $("#totaldiscounts").after(extrachargesrow);
  var inputgrossamount = $("#inputgrossamount").val();
  if(extrachargesamnt.length > 0){
    for(var i=0; i<extrachargesamnt.length; i++){

      var id = extrachargesamnt[i].split('_');
      var chargesid = id[0];
      var amount = id[1];
      var tax = id[2];
      var type = id[3];
      var chargespercent = 0;
      if(type==0){
        if(parseFloat(amount)> 0){
          chargespercent = parseFloat(amount) * 100 / parseFloat(inputgrossamount);
        }
        var optiontext = $("#extrachargesname"+chargesid).val();
        
        optiontext = optiontext.split("(");
        optiontext = optiontext[0].trim()+" ("+parseFloat(chargespercent).toFixed(2)+"%)";
        $("#tr_extracharges_"+chargesid+" td:first").text(optiontext);
        $("#extrachargesname"+chargesid).val(optiontext);
      }
      
      $("#extrachargeamount"+chargesid).html(parseFloat(amount).toFixed(2));
      $("#inputextrachargeamount"+chargesid).val(parseFloat(amount).toFixed(2));
      $("#extrachargestax"+chargesid).val(parseFloat(tax).toFixed(2));
      $("#extrachargesdatatype"+chargesid).val(parseInt(type));
      $("#extrachargepercentage"+chargesid).val(parseFloat(chargespercent).toFixed(2));
    }
  }
  /********* CALCULATE EXTRA CHARGES END *********/

  /********* CHANGE DISCOUNT START *********/
  var discountamount = 0;
  $(".invoicediscountamount").each(function( index ) {
    if(this.value > 0){
      discountamount += parseFloat(this.value);
    }
  });
  
  netamounttotal();
  if(parseFloat(discountamount) > 0){
    var grossamount = $("#inputgrossamount").val();
    $("#ovdiscper").html(parseFloat((parseFloat(discountamount) * 100 / parseFloat(grossamount))).toFixed(2));
    $("#ovdiscamnt").html(parseFloat(discountamount).toFixed(2));
    $("#inputovdiscamnt").val(parseFloat(discountamount).toFixed(2));
    $("#totaldiscounts").show();
  }else{
    $("#ovdiscamnt").html('0.00');
    $("#inputovdiscamnt").val('0.00');
    $("#totaldiscounts").hide();
  }
  /********* CHANGE DISCOUNT END *********/
}

function netamounttotal() {
  var producttotal = productgstamount = grossamount = extrachargesamount = extrachargestax = chargesassesbaleamount = 0;
  
  $(".producttotalwithouttax").each(function( index ) {
    var divid = $(this).attr("id").match(/(\d+)/g);
    if($(this).val()!="" && $("#creditqty"+divid).val() >0 ){
      producttotal += parseFloat($(this).val());
    }
  });
  $(".taxvalue").each(function( index ) {
    var divid = $(this).attr("id").match(/(\d+)/g);
    if($(this).val()!="" && $("#creditqty"+divid).val() >0 ){
      productgstamount += parseFloat($(this).val());
    }
  });
  $("#producttotal").html(format.format(producttotal));
  $("#inputproducttotal").val(parseFloat(producttotal).toFixed(2));
  $("#gsttotal").html(format.format(productgstamount));
  $("#inputgsttotal").val(parseFloat(productgstamount).toFixed(2));

  if($("select.invoiceextrachargesid").length > 0){
    $(".tr_extracharges").each(function( index ) {
      if($(this).attr("id")!="default"){
        var chargesid = $(this).attr("id").match(/(\d+)/g);
        var exchrgamnt = $("#extrachargeamount"+chargesid).html();
        var exchrgtax = $("#extrachargestax"+chargesid).val();
        if(parseFloat(exchrgamnt) > 0){
          extrachargesamount += parseFloat(exchrgamnt);
          extrachargestax += parseFloat(exchrgtax);
        }
      }
    });
  }
  chargesassesbaleamount = parseFloat(extrachargesamount) - parseFloat(extrachargestax);
  var producttotalassesbaleamount = parseFloat(producttotal) + parseFloat(chargesassesbaleamount);
  var producttotalgstamount = parseFloat(productgstamount) + parseFloat(extrachargestax);

  $("#chargestotalassesbaleamount").html(format.format(parseFloat(chargesassesbaleamount).toFixed(2)));
  $("#chargestotalgstamount").html(format.format(parseFloat(extrachargestax).toFixed(2)));
  $("#producttotalassesbaleamount").html(format.format(parseFloat(producttotalassesbaleamount).toFixed(2)));
  $("#producttotalgstamount").html(format.format(parseFloat(producttotalgstamount).toFixed(2)));

  grossamount = parseFloat(producttotal) + parseFloat(productgstamount);
  $("#grossamount").html(format.format(grossamount));
  $("#inputgrossamount").val(parseFloat(grossamount).toFixed(2));
  
  var discount = $("#ovdiscamnt").html();
  var finalamount = parseFloat(grossamount) - parseFloat(discount) + parseFloat(extrachargesamount);

  if(finalamount<0){
      finalamount=0;
  }
  var roundoff =  Math.round(parseFloat(finalamount).toFixed(2))-parseFloat(finalamount);
  finalamount =  Math.round(parseFloat(finalamount).toFixed(2));
  $("#roundoff").html(format.format(roundoff));
  $("#totalpayableamount").html(format.format(finalamount));
  $("#inputtotalpayableamount").val(parseFloat(finalamount).toFixed(2));
}
function calculateOfferNetAmount(rowid){

  var amount = $("#creditnoteamount"+rowid).val();
  var tax = $("#creditnotetax"+rowid).val();
  if(amount==""){
    amount = 0;
  }
  if(tax==""){
    tax = 0;
  }
  
  var taxamount = parseFloat(amount) * parseFloat(tax) / 100;
  
  var netamount = parseFloat(amount) + parseFloat(taxamount);
  
  $("#creditnotetaxamount"+rowid).val(parseFloat(taxamount).toFixed(2));
  $("#creditnotenetamount"+rowid).val(parseFloat(netamount).toFixed(2));
  offernetamounttotal();
}
function offernetamounttotal() {
  var offertotal = offergstamount = grossamount = 0;
  
  
  $(".creditnoteamount").each(function( index ) {
    if($(this).val()!=""){
      offertotal += parseFloat($(this).val());
    }
  });
  $(".creditnotetaxamount").each(function( index ) {
    if($(this).val()!=""){
      offergstamount += parseFloat($(this).val());
    }
  });
  $("#offertotal").html(format.format(offertotal));
  $("#inputoffertotal").val(parseFloat(offertotal).toFixed(2));
  $("#offergsttotal").html(format.format(offergstamount));
  $("#inputoffergsttotal").val(parseFloat(offergstamount).toFixed(2));

  $("#offertotalassesbaleamount").html(format.format(parseFloat(offertotal).toFixed(2)));
  $("#offertotalgstamount").html(format.format(parseFloat(offergstamount).toFixed(2)));

  grossamount = parseFloat(offertotal) + parseFloat(offergstamount);
  $("#offergrossamount").html(format.format(grossamount));
  $("#inputoffergrossamount").val(parseFloat(grossamount).toFixed(2));
  
  if(grossamount<0){
    grossamount=0;
  }
  var roundoff =  Math.round(parseFloat(grossamount).toFixed(2))-parseFloat(grossamount);
  grossamount =  Math.round(parseFloat(grossamount).toFixed(2));
  $("#offerroundoff").html(format.format(roundoff));
  $("#totalofferpayableamount").html(format.format(grossamount));
  $("#inputtotalofferpayableamount").val(parseFloat(grossamount).toFixed(2));
}
function removecharge(invoiceid,rowid){
  $("#countcharges_"+invoiceid+"_"+rowid).remove();
  overallextracharges();
  netamounttotal();
}
function printCreditNote(id){

  var uurl = SITE_URL + "credit-note/printCreditNote";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {id:id},
    //dataType: 'json',
    async: false,
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
}
function resetdata(){  
  
  $("#member_div").removeClass("has-error is-focused");
  $("#invoiceid_div").removeClass("has-error is-focused");
  $("#billingaddress_div").removeClass("has-error is-focused");
  $("#shippingaddress_div").removeClass("has-error is-focused");
  $("#creditnotedate_div").removeClass("has-error is-focused");
  $("#offer_div").removeClass("has-error is-focused");
  
  if(ACTION==0){
      if(MemberId==0){
        $('#memberid,#invoiceid').val('0');
        $('#billingaddressid,#shippingaddressid').val('');
        $('#billingaddress').val('');
        $('#shippingaddress').val('');
        $('#invoiceid')
          .find('option')
          .remove()
          .end()
          .append()
          .val('0')
        ;
        $('#billingaddressid,#shippingaddressid')
          .find('option')
          .remove()
          .end()
          .val('whatever')
        ;
        $('#invoiceid,#billingaddressid,#shippingaddressid').selectpicker('refresh');
      }else{
        $('#memberid').val(MemberId);
        $('#invoiceid').val(InvoiceId);
      }
      $('#remarks').val("");
      $('#creditnotedate').val(new Date().toLocaleDateString());
      
      $("#creditnotetypeproduct").prop("checked",true);
      $("#offerid").val(0);
      $("#offer_div").parent().hide();
      $("#Maincreditnotedetails_div").hide();
      $("#offerAmountTotal").hide();
      $("#Maincreditnote_div").show();
      $("#AmountTotal").show();
      $("#invoiceamountdiv").show();
      
      $('.selectpicker').selectpicker('refresh');
      if(MemberId!=0){
        getTransactionProducts();
      }else{
        $("#creditnoteproducttable tbody").html("<tr><td colspan='16' class='text-center'>No data available in table.</td></tr>");
        $('#invoiceamountdiv').html("");
        $('#extracharges_div').html("");
      }
      overallextracharges();
      netamounttotal();

      $('.creditnote').not(':first').remove();
      var divid = $('.creditnote:first').attr("id").match(/(\d+)/g);
      $('#creditnotedetail'+divid+'_div,#creditnoteamount'+divid+'_div,#creditnotetax'+divid+'_div,#creditnotenetamount'+divid+'_div').removeClass("has-error is-focused").val("");

      $('.add_creditnote_btn:first').show();
      $('.remove_creditnote_btn').hide();
  }

  $('html, body').animate({scrollTop:0},'slow');
}

function checkvalidation(btntype=''){
    
  var creditnoteid = $('#creditnoteid').val();
  var memberid = $('#memberid').val();
  var invoiceid = $('#invoiceid').val();
  var billingaddressid = $('#billingaddressid').val();
  var shippingaddressid = $('#shippingaddressid').val();
  var creditnotedate = $('#creditnotedate').val();
  var creditnotetype = $('#creditnotetype').val();
  var offerid = $('#offerid').val();

  var isvalidmemberid = isvalidinvoiceid = isvalidcreditqty = isvalidcreditpercent = isvalidcreditamount = isvalidproductcount = isvalidstockqty = isvalidrejectqty = isvalidbillingaddressid = isvalidshippingaddressid = isvalidcreditnotedate = isvalidextrachargesid = isvalidextrachargeamount = isvalidduplicatecharges = isvalidofferid = isvalidcreditnotedetail = isvalidcreditnoteamount = isvalidtransactionproductqty = 1;
  PNotify.removeAll();
  
  if(memberid == 0){
    $("#member_div").addClass("has-error is-focused");
    new PNotify({title: "Please select "+member_label+" !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidmemberid = 0;
  }else{
    $("#member_div").removeClass("has-error is-focused");
  }
  if(invoiceid == 0 || invoiceid == null){
    $("#invoiceid_div").addClass("has-error is-focused");
    new PNotify({title: "Please select invoice no. !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidinvoiceid = 0;
  }else{
    $("#invoiceid_div").removeClass("has-error is-focused");
  }
  /* if(billingaddressid == "" || billingaddressid == null){
    $("#billingaddress_div").addClass("has-error is-focused");
    new PNotify({title: "Please select billing address !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidbillingaddressid = 0;
  }else{
    $("#billingaddress_div").removeClass("has-error is-focused");
  }
  if(shippingaddressid == "" || shippingaddressid == null){
    $("#shippingaddress_div").addClass("has-error is-focused");
    new PNotify({title: "Please select shipping address !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidshippingaddressid = 0;
  }else{
    $("#shippingaddress_div").removeClass("has-error is-focused");
  } */
  if(creditnotedate == ""){
    $("#creditnotedate_div").addClass("has-error is-focused");
    new PNotify({title: "Please select credit note date !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidcreditnotedate = 0;
  }else{
    $("#creditnotedate_div").removeClass("has-error is-focused");
  }

  if($("input[name='creditnotetype']:checked").val() == 0){
    var c=1;
    var countproduct = 0;
    $('.countproducts').each(function(){
        var id = $(this).attr('id');
        
        if(parseFloat($("#paidqty"+id).val()) < parseFloat($("#qty"+id).val())){
          if($("#creditqty"+id).val() == ""){
              $("#creditqty"+id+"_div").addClass("has-error is-focused");
              new PNotify({title: 'Please select '+(c)+' credit quantity !',styling: 'fontawesome',delay: '3000',type: 'error'});
              isvalidcreditqty = 0;
          }else {
              $("#creditqty"+id+"_div").removeClass("has-error is-focused");
          }
          if($("#creditpercent"+id).val() == ""){
              $("#creditpercent"+id+"_div").addClass("has-error is-focused");
              new PNotify({title: 'Please select '+(c)+' credit percent !',styling: 'fontawesome',delay: '3000',type: 'error'});
              isvalidcreditpercent = 0;
          }else {
              $("#creditpercent"+id+"_div").removeClass("has-error is-focused");
          }
          if($("#creditamount"+id).val() == ""){
              $("#creditamount"+id+"_div").addClass("has-error is-focused");
              new PNotify({title: 'Please select '+(c)+' credit amount !',styling: 'fontawesome',delay: '3000',type: 'error'});
              isvalidcreditamount = 0;
          }else {
              $("#creditamount"+id+"_div").removeClass("has-error is-focused");
          }
          if($("#creditqty"+id).val() != "" && $("#creditpercent"+id).val() != "" && $("#creditamount"+id).val() != ""){

            var productstockqty = productrejectqty = creditqty=0;
            $(".stockqtys"+id).each(function(){
              if($(this).val()!=""){
                productstockqty += parseFloat($(this).val());
              }
            });
            $(".scrapqtys"+id).each(function(){
              if($(this).val()!=""){
                productrejectqty += parseFloat($(this).val());
              }
            });

            // var productstockqty = ($("#productstockqty"+id).val()!="")?parseFloat($("#productstockqty"+id).val()):0;
            // var productrejectqty = ($("#productrejectqty"+id).val()!="")?parseFloat($("#productrejectqty"+id).val()):0;
            var creditqty = ($("#creditqty"+id).val()!="")?parseFloat($("#creditqty"+id).val()):0;

            if(parseFloat(productstockqty)+parseFloat(productrejectqty)!=parseFloat(creditqty)){
              // $("#productstockqty"+id+"_div").addClass("has-error is-focused");
              // $("#productrejectqty"+id+"_div").addClass("has-error is-focused");
              $('.stockqtys'+id).parent().addClass('has-error is-focused');
              $('.scrapqtys'+id).parent().addClass('has-error is-focused');

              new PNotify({title: 'Stock quantity and reject quantity must be same with '+(c)+' credit quantity !',styling: 'fontawesome',delay: '3000',type: 'error'});
              isvalidstockqty = 0;
              isvalidrejectqty = 0;
            }else{
              $('.stockqtys'+id).parent().removeClass('has-error is-focused');
              $('.scrapqtys'+id).parent().removeClass('has-error is-focused');
            }
          }else{
            $('.stockqtys'+id).parent().removeClass('has-error is-focused');
            $('.scrapqtys'+id).parent().removeClass('has-error is-focused');
          }
          /* var returnqty = 0;
          $(".stockqtys"+id).each(function(){

            if(this.value != 0){
              returnqty += parseFloat(this.value); 
            }
          });
          if(parseFloat(returnqty)==0 || parseFloat(returnqty) > parseFloat($("#creditqty"+id).val())){
            new PNotify({title: 'Does not match '+(c)+' transaction product quantity with credit quantity !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidtransactionproductqty = 0;
          } */
        }else{
          countproduct++;
        } 
        c++;
    });
    if($('.countproducts').length==0){
      new PNotify({title: "Please add at least one product !",styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidproductcount=0;
    }

    var i=1;
    $('.countinvoice').each(function(){
      var invoiceid = $(this).attr('id');
      $('.countcharges'+invoiceid).each(function(){
        var elementid = $(this).attr('id').split('_');
        var divid = elementid[2];
        
        if($("#invoiceextrachargesid_"+invoiceid+"_"+divid).val() > 0 || $("#invoiceextrachargeamount_"+invoiceid+"_"+divid).val() > 0){

            if($("#invoiceextrachargesid_"+invoiceid+"_"+divid).val() == 0){
                $("#extracharges_"+invoiceid+"_"+divid+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+divid+' extra charge !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidextrachargesid = 0;
            }else {
                $("#extracharges_"+invoiceid+"_"+divid+"_div").removeClass("has-error is-focused");
            }
            if($("#invoiceextrachargeamount_"+invoiceid+"_"+divid).val() == '' || $("#invoiceextrachargeamount_"+invoiceid+"_"+divid).val() == 0){
                $("#invoiceextrachargeamount_"+invoiceid+"_"+divid+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please enter '+divid+' extra charge amount on '+(i)+' invoice !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidextrachargeamount = 0;
            }else {
                $("#invoiceextrachargeamount_"+invoiceid+"_"+divid+"_div").removeClass("has-error is-focused");
            }
        } else{
            $("#extracharges_"+invoiceid+"_"+divid+"_div").removeClass("has-error is-focused");
            $("#invoiceextrachargeamount_"+invoiceid+"_"+divid+"_div").removeClass("has-error is-focused");
        }

      });
      i++;
    });
    var k=1;
    $('.countinvoice').each(function(){
      var invoiceid = $(this).attr('id');
      
      var selects_charges = $('select[name="invoiceextrachargesid['+invoiceid+'][]"]');
      var values = [];
      for(j=0;j<selects_charges.length;j++) {
          var selectscharges = selects_charges[j];
          var id = selectscharges.id.split("_");
          var divid = id[2];

          if(selectscharges.value!=0){
              if(values.indexOf(selectscharges.value)>-1) {
                  $("#extracharges_"+invoiceid+"_"+divid+"_div").addClass("has-error is-focused");
                  new PNotify({title: 'Please select '+(j+1)+' is different extra charge !',styling: 'fontawesome',delay: '3000',type: 'error'});
                  isvalidduplicatecharges = 0;
              }
              else{ 
                  values.push(selectscharges.value);
                  if($("#invoiceextrachargesid_"+invoiceid+"_"+divid).val()!=0){
                  $("#extracharges_"+invoiceid+"_"+divid+"_div").removeClass("has-error is-focused");
                  }
              }
          }
      }
      k++;
    });
  }else{
    if(offerid == 0){
      $("#offer_div").addClass("has-error is-focused");
      new PNotify({title: "Please select offer !",styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidofferid = 0;
    }else{
      $("#offer_div").removeClass("has-error is-focused");
    }
    
    var row=1;
    $('.creditnote').each(function(){
      var rowid = $(this).attr('id').match(/(\d+)/g);
      
      if($("#creditnotedetail"+rowid).val() == ""){
        $("#creditnotedetail"+rowid+"_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter '+(row)+' credit note detail !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidcreditnotedetail = 0;
      }else if($("#creditnotedetail"+rowid).val().length < 2){
        $("#creditnotedetail"+rowid+"_div").addClass("has-error is-focused");
        new PNotify({title: 'Credit note detail '+(row)+' require minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidcreditnotedetail = 0;
      }else {
        $("#creditnotedetail"+rowid+"_div").removeClass("has-error is-focused");
      }
      if($("#creditnoteamount"+rowid).val() == "" || parseFloat($("#creditnoteamount"+rowid).val()) == 0){
        $("#creditnoteamount"+rowid+"_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter '+(row)+' credit note amount !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidcreditnoteamount = 0;
      }else {
        $("#creditnoteamount"+rowid+"_div").removeClass("has-error is-focused");
      }

      row++;
    });
  }
  if(isvalidmemberid == 1 && isvalidinvoiceid == 1 && isvalidbillingaddressid == 1 && isvalidshippingaddressid == 1 && isvalidcreditnotedate == 1 && isvalidcreditqty == 1 && isvalidcreditpercent == 1 && isvalidcreditamount == 1 &&  isvalidproductcount == 1 &&
    isvalidstockqty == 1 && isvalidrejectqty == 1 && isvalidextrachargesid == 1 && isvalidextrachargeamount == 1 && isvalidduplicatecharges == 1 && isvalidofferid == 1 && isvalidcreditnotedetail == 1 && isvalidcreditnoteamount == 1 && isvalidtransactionproductqty == 1){
    
    // $('#credittotal').val($('#totalamount').text());

    var formData = new FormData($('#creditnoteform')[0]);
    if(creditnoteid==''){
      var uurl = SITE_URL+"credit-note/add-credit-note";
      $.ajax({
        url: uurl,
        type: 'POST',
        data:formData,
        //async: false,
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
        },
        success: function(response){
          var obj = JSON.parse(response);
          if(obj['error']==1){
            new PNotify({title: "Credit note successfully generated !",styling: 'fontawesome',delay: '3000',type: 'success'});
            if(btntype=='print'){
              printCreditNote(obj['creditnoteid']);
              setTimeout(function() { window.location=SITE_URL+"credit-note"; }, 1500);
            }else{
              resetdata();
              if(MemberId!=0){
                setTimeout(function() { window.location=SITE_URL+"credit-note"; }, 1500);
              }
            }
          }else{
            new PNotify({title: "Credit note not generate !",styling: 'fontawesome',delay: '3000',type: 'error'});
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