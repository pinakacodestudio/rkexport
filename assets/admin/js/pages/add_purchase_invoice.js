$(document).ready(function() {  
  $('body').on('focus', ".date", function () {
    $(this).datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        orientation: "top",
        clearBtn: true,
    });
  });

  $('#invoicedate').datepicker({
    todayHighlight: true,
    format: 'dd/mm/yyyy',
    todayBtn:"linked",
  });

  /****VENDOR CHANGE EVENT****/
  $('#vendorid').on('change', function (e) {
      // getVendorSalesOrder();
      getVendorGRN();
      getbillingaddress();
      changeextrachargesamount();
      overallextracharges();
      netamounttotal();
  });
  /****grnid CHANGE EVENT****/
  $('#grnid').on('change', function (e) {
      getTransactionProducts();
      getbillingaddress();
      changeextrachargesamount();
      overallextracharges();
      netamounttotal();
  });
  /****BILLING ADDRESS CHANGE EVENT****/
  $('#billingaddressid').on('change', function (e) {
    $('#billingaddress').val($('#billingaddressid option:selected').text());
  });
  /****SHIPPING ADDRESS CHANGE EVENT****/
  $('#shippingaddressid').on('change', function (e) {
    $('#shippingaddress').val($('#shippingaddressid option:selected').text());
  });

  $(".countcharges0 .add_charges_btn").hide();
  $(".countcharges0 .add_charges_btn:last").show();

  if(ACTION==1 && GRNId!='' && GRNId!=null){
      // getVendorSalesOrder();
      getVendorGRN();
      getbillingaddress();
      getTransactionProducts();
      netamounttotal();
  }
  $('body').on('keyup', '.qty', function() { 
    var divid = $(this).attr("id").match(/(\d+)/g);
    var grnid = $("#grnidarr"+divid).val();
    if(parseFloat(this.value) > parseFloat($("#orderqty"+divid).val())){
      $(this).val(parseFloat($("#orderqty"+divid).val()).toFixed(2));
    }
    totalproductamount(grnid,divid);
  });
  $('body').on('change', '.qty', function() { 
    var divid = $(this).attr("id").match(/(\d+)/g);
    var grnid = $("#grnidarr"+divid).val();
    if(parseFloat(this.value) > parseFloat($("#orderqty"+divid).val())){
      $(this).val(parseFloat($("#orderqty"+divid).val()).toFixed(2));
    }
    totalproductamount(grnid,divid);
  });

  $('#editinvoicenumber').change(function () {
    if($(this).is(':checked')){
      $("#invoiceno").prop("readonly",false);
    }else{
      $("#invoiceno").val($("#invoicenumber").val()).prop("readonly",true);
    }
  });
});


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

function getVendorGRN(){
  $('#grnid')
      .find('option')
      .remove()
      .end()
      .append()
      .val('0')
  ;
  
  $('#grnid').selectpicker('refresh');

  var vendorid = $("#vendorid").val();
  
  if(vendorid!=0){
    var uurl = SITE_URL+"vendor/getVendorGRN";
    
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {vendorid:String(vendorid),from:'invoice'},
      dataType: 'json',
      async: false,
      success: function(response){

        for(var i = 0; i < response.length; i++) {

          if(ACTION==1){
            if(GRNId!=null || GRNId!=''){
             
              GRNId = GRNId.toString().split(',');
             
              if(GRNId.includes(response[i]['id'])){
                $('#grnid').append($('<option>', { 
                  value: response[i]['id'],
                  selected: "selected",
                  text : ucwords(response[i]['grnnumber']),
                  "data-billingid": response[i]['billingid'],
                  "data-shippingid": response[i]['shippingid']
                }));
              }else{
                $('#grnid').append($('<option>', { 
                  value: response[i]['id'],
                  text : ucwords(response[i]['grnnumber']),
                  "data-billingid": response[i]['billingid'],
                  "data-shippingid": response[i]['shippingid']
                }));
              }
            }
          }else{
            $('#grnid').append($('<option>', { 
              value: response[i]['id'],
              text : ucwords(response[i]['grnnumber']),
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
    $("#invoiceproducttable tbody").html("<tr><td colspan='9' class='text-center'>No data available in table.</td></tr>");
  }
  $('#grnid').selectpicker('refresh');
  
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

  var vendorid = $("#vendorid").val();
  var BillingAddressID = $("#grnid option:selected:last").attr("data-billingid");
  var ShippingAddressID = $("#grnid option:selected:last").attr("data-shippingid");
  
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
                
                if(BillingAddressID!=0 && BillingAddressID==obj['billingaddress'][i]['id']){
                  if($('#billingaddressid option').length==0){
                    $('#billingaddressid').append('<optgroup label="Order Billing Address"></optgroup>');
                  }else{
                    $('#billingaddressid').prepend('<optgroup label="Order Billing Address"></optgroup>');
                  }
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
                  if($('#shippingaddressid option').length==0){
                    $('#shippingaddressid').append('<optgroup label="Order Shipping Address"></optgroup>');
                  }else{
                    $('#shippingaddressid').prepend('<optgroup label="Order Shipping Address"></optgroup>');
                  }
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
          
      },
      error: function(xhr) {
     
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

function getTransactionProducts(){
  
  var vendorid = $("#vendorid").val();
  var grnid = $("#grnid").val();
  var invoiceid = $("#invoiceid").val();

  $('.disccol,.cgstcol,.sgstcol,.igstcol').show();
  
  if(grnid!='' && grnid!=null){
    var uurl = SITE_URL+"purchase-invoice/getTransactionProducts";
    
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {vendorid:String(vendorid),grnid:String(grnid),invoiceid:String(invoiceid)},
      dataType: 'json',
      async: false,
      success: function(response){
          if(response!=""){
              var grnproducts = response['grnproducts'];
              var grnamountdata = response['grnamountdata'];
              var gstprice = response['gstprice'];

              var htmldata = discolumn = "";
              var gstcolumn = [];
             
              var headerdata = '<tr>\
                                  <th rowspan="2" class="width5">Sr. No.</th>\
                                  <th rowspan="2">Product Name</th>\
                                  <th rowspan="2" class="width12">Qty.</th>\
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

              if(grnproducts!=null && grnproducts!=""){
                if(grnproducts.length>0){
                  for(var i=0; i<grnproducts.length; i++){

                    if(invoiceid!=''){
                      var qty = parseFloat(grnproducts[i]['editquantity']);
                      var grnqty = parseFloat(grnproducts[i]['quantity']) - parseFloat(grnproducts[i]['invoiceqty']);
                    }else{
                      var qty = parseFloat(grnproducts[i]['quantity']) - parseFloat(grnproducts[i]['invoiceqty']);
                      var grnqty = qty;
                    }

                    gstcolumn.push(grnproducts[i]['igst']);
                    // var qty = parseInt(orderproducts[i]['quantity']);
                    var tax = parseFloat(grnproducts[i]['tax']);
                    var amount = parseFloat(grnproducts[i]['amount']);
                    var originalprice = parseFloat(grnproducts[i]['originalprice']);

                    var discount = parseFloat(grnproducts[i]['discount']);
                    discolumn += parseFloat(discount);

                    var discountamount = ((parseFloat(originalprice) * parseFloat(qty)) * parseFloat(discount) / 100);
                    
                    var totalprice = (parseFloat(amount) * parseFloat(qty));
                    var taxvalue = parseFloat(parseFloat(amount) * parseFloat(qty) * parseFloat(tax) / 100);
                    var total = parseFloat(totalprice) + parseFloat(taxvalue);
                    
                    var grnid = grnproducts[i]['grnid'];
                    if(parseFloat(grnproducts[i]['quantity']) == parseFloat(grnproducts[i]['invoiceqty'])){
                      var grnid = "";
                    }
                    htmldata += "<tr class='countproducts' id='"+grnproducts[i]['transactionproductsid']+"'>";
                      htmldata += "<td rowspan='2'>"+(i+1);
                      htmldata += '<input type="hidden" name="transactionproductsid[]" value="'+grnproducts[i]['transactionproductsid']+'">';
                      htmldata += '<input type="hidden" id="price'+grnproducts[i]['transactionproductsid']+'" value="'+parseFloat(amount)+'">';
                      htmldata += '<input type="hidden" id="actualprice'+grnproducts[i]['transactionproductsid']+'" value="'+parseFloat(originalprice)+'">';
                      htmldata += '<input type="hidden" id="tax'+grnproducts[i]['transactionproductsid']+'" value="'+parseFloat(tax)+'">';
                      htmldata += '<input type="hidden" id="taxtype'+grnproducts[i]['transactionproductsid']+'" value="'+grnproducts[i]['igst']+'">';
                      htmldata += '<input type="hidden" id="taxvalue'+grnproducts[i]['transactionproductsid']+'" class="taxvalue" value="'+parseFloat(taxvalue).toFixed(2)+'">';
                      htmldata += '<input type="hidden" id="producttotal'+grnproducts[i]['transactionproductsid']+'" class="producttotal" value="'+parseFloat(parseFloat(amount) * parseFloat(qty)).toFixed(2)+'">';
                      htmldata += '<input type="hidden" id="discount'+grnproducts[i]['transactionproductsid']+'" class="discount" value="'+parseFloat(discount).toFixed(2)+'">';
                      htmldata += '<input type="hidden" name="grnidarr[]" id="grnidarr'+grnproducts[i]['transactionproductsid']+'" value="'+grnid+'">';
                      htmldata += '<input type="hidden" id="grnquantity'+grnproducts[i]['transactionproductsid']+'" value="'+parseFloat(grnproducts[i]['quantity'])+'" class="grnquantity'+grnid+'">';
                      
                      htmldata += "</td>";

                      htmldata += "<td rowspan='2'>"+ucwords(grnproducts[i]['productname'])+"<br><br><b>GRN No.: </b>"+grnproducts[i]['grnnumber']+"</td>";
                      
                      htmldata += '<td rowspan="2" class="width8"><div class="col-md-12 pl pr"><div class="form-group" id="quantity'+grnproducts[i]['transactionproductsid']+'_div"><input type="text" name="quantity[]" id="quantity'+grnproducts[i]['transactionproductsid']+'" class="form-control qty" value="'+parseFloat(qty).toFixed(2)+'" onkeypress="'+(MANAGE_DECIMAL_QTY==1?"return decimal_number_validation(event, this.value,8);":"return isNumber(event);")+'">\
                      <input type="hidden" name="grnqty" id="grnqty'+grnproducts[i]['transactionproductsid']+'" value="'+parseFloat(grnqty).toFixed(2)+'"></td>';
                      
                      htmldata += "<td rowspan='2' class='text-right'>"+parseFloat(amount).toFixed(2)+"<br><br><p><b>Total Invoice Qty.: </b>"+parseFloat(grnproducts[i]['invoiceqty']).toFixed(2)+"</p></td>";

                      if(gstprice == 1){
                        if(parseFloat(discount) > 0){
                          htmldata += "<td class='text-right disccol'>"+parseFloat(discount).toFixed(2)+"</td>";
                        }else{
                          htmldata += "<td class='text-right disccol'>-</td>";
                        }
                      
                        if(grnproducts[i]['igst']==1){
                          htmldata += "<td class='text-right sgstcol'>"+parseFloat((parseFloat(tax)/2)).toFixed(2)+"</td>";
                          htmldata += "<td class='text-right cgstcol'>"+parseFloat((parseFloat(tax)/2)).toFixed(2)+"</td>";
                        }else{
                          htmldata += "<td class='text-right igstcol'>"+parseFloat(tax).toFixed(2)+"</td>";
                        }
                      }else{
                        if(grnproducts[i]['igst']==1){
                          htmldata += "<td class='text-right sgstcol'>"+parseFloat((parseFloat(tax)/2)).toFixed(2)+"</td>";
                          htmldata += "<td class='text-right cgstcol'>"+parseFloat((parseFloat(tax)/2)).toFixed(2)+"</td>";
                        }else{
                          htmldata += "<td class='text-right igstcol'>"+parseFloat(tax).toFixed(2)+"</td>";
                        }
                        if(parseFloat(discount) > 0){
                          htmldata += "<td class='text-right disccol'>"+parseFloat(discount).toFixed(2)+"</td>";
                        }else{
                          htmldata += "<td class='text-right disccol'>-</td>";
                        }
                      }
                      
                      htmldata += "<td rowspan='2' class='text-right netamount' id='productnetprice"+grnproducts[i]['transactionproductsid']+"'>"+parseFloat(total).toFixed(2)+"</td>";
                    htmldata += "</tr>";

                    htmldata += "<tr>";
                    if(gstprice == 1){
                      if(parseFloat(discount) > 0){
                        htmldata += "<td class='text-right disccol' id='discountamount"+grnproducts[i]['transactionproductsid']+"'>"+parseFloat(discountamount).toFixed(2)+"</td>";
                      }else{
                        htmldata += "<td class='text-right disccol'>-</td>";
                      }
                      if(grnproducts[i]['igst']==1){
                        htmldata += "<td class='text-right sgstcol' id='sgst"+grnproducts[i]['transactionproductsid']+"'>"+parseFloat((taxvalue/2)).toFixed(2)+"</td>";
                        htmldata += "<td class='text-right cgstcol' id='cgst"+grnproducts[i]['transactionproductsid']+"'>"+parseFloat((taxvalue/2)).toFixed(2)+"</td>";
                      }else{
                        htmldata += "<td class='text-right igstcol' id='igst"+grnproducts[i]['transactionproductsid']+"'>"+parseFloat(taxvalue).toFixed(2)+"</td>";
                      }
                    }else{
                      if(grnproducts[i]['igst']==1){
                        htmldata += "<td class='text-right sgstcol' id='sgst"+grnproducts[i]['transactionproductsid']+"'>"+parseFloat((taxvalue/2)).toFixed(2)+"</td>";
                        htmldata += "<td class='text-right cgstcol' id='cgst"+grnproducts[i]['transactionproductsid']+"'>"+parseFloat((taxvalue/2)).toFixed(2)+"</td>";
                      }else{
                        htmldata += "<td class='text-right igstcol' id='igst"+grnproducts[i]['transactionproductsid']+"'>"+parseFloat(taxvalue).toFixed(2)+"</td>";
                      }      
                      if(parseFloat(discount) > 0){
                        htmldata += "<td class='text-right disccol' id='discountamount"+grnproducts[i]['transactionproductsid']+"'>"+parseFloat(discountamount).toFixed(2)+"</td>";
                      }else{
                        htmldata += "<td class='text-right disccol'>-</td>";
                      }              
                    }
                    htmldata += "</tr>";
                  }
                }else{
                  $("#invoiceproducttable tbody").html("<tr><td colspan='9' class='text-center'>No data available in table.</td></tr>");
                }
              }
              $("#invoiceproducttable thead").html(headerdata);
              $("#invoiceproducttable tbody").html(htmldata);
              if(discolumn > 0){
                $('.disccol').show();
              }else{
                $('.disccol').hide();
              }
              if(gstcolumn.includes("1")){
                $('.igstcol').hide();
                $('.cgstcol,.sgstcol').show();
              }else{
                $('.igstcol').show();
                $('.cgstcol,.sgstcol').hide();
              }
              $(".qty").TouchSpin(touchspinoptions);

              var html = extrachargespanel = '';
              if(grnamountdata!=null && grnamountdata!=""){
                if(grnamountdata.length>0){
                  var orderextracharge = [];
                  var grnidArr = [];
                  for(var i=0; i<grnamountdata.length; i++){
                    var extrachargesrows = extrachargeshtml = '';
                    var grnid = grnamountdata[i]['id'];
                    
                    var extracharges = grnamountdata[i]['extracharges'];
                    var totalextracharges = 0;
                    var extracharge = [];
                    if(extracharges.length>0){
                      for(var j=0; j<extracharges.length; j++){
                        extrachargesrows +=  '<tr '+HIDE_PURCHASE_EXTRA_CHARGES+'>\
                                              <td>'+extracharges[j]['extrachargesname']+'</td>\
                                              <th> : </th>\
                                              <td class="text-right">'+parseFloat(extracharges[j]['amount']).toFixed(2)+'</td>\
                                            </tr>';

                        totalextracharges += parseFloat(extracharges[j]['amount']);
                        
                        extracharge.push(extracharges[j]['extrachargesid']);
                        
                        extrachargeshtml += '<div class="col-md-6 p-n countcharges'+grnid+'" id="countcharges_'+grnid+'_'+(j+1)+'" '+HIDE_PURCHASE_EXTRA_CHARGES+'>\
                                              <div class="col-sm-7 pr-xs">\
                                                  <div class="form-group" id="extracharges_'+grnid+'_'+(j+1)+'_div">\
                                                      <div class="col-sm-12">\
                                                          <select id="orderextrachargesid_'+grnid+'_'+(j+1)+'" name="orderextrachargesid['+grnid+'][]" class="selectpicker form-control orderextrachargesid" data-live-search="true" data-select-on-tab="true" data-size="5" data-live-search="true">\
                                                            <option value="0">Select Extra Charges</option>\
                                                            '+extrachargeoptionhtml+'\
                                                          </select>\
                                                          <input type="hidden" name="orderextrachargestax['+grnid+'][]" id="orderextrachargestax_'+grnid+'_'+(j+1)+'" class="orderextrachargestax" value="'+extracharges[j]['taxamount']+'">\
                                                          <input type="hidden" name="orderextrachargesname['+grnid+'][]" id="orderextrachargesname_'+grnid+'_'+(j+1)+'" class="orderextrachargesname" value="'+extracharges[j]['extrachargesname']+'">\
                                                          <input type="hidden" name="orderextrachargepercentage['+grnid+'][]" id="orderextrachargepercentage_'+grnid+'_'+(j+1)+'" class="orderextrachargepercentage" value="'+extracharges[j]['extrachargepercentage']+'">\
                                                      </div>\
                                                  </div>\
                                              </div>\
                                              <div class="col-sm-3 pl-xs pr-xs">\
                                                  <div class="form-group p-n" id="orderextrachargeamount_'+grnid+'_'+(j+1)+'_div">\
                                                      <div class="col-sm-12">\
                                                          <input type="text" id="orderextrachargeamount_'+grnid+'_'+(j+1)+'" name="orderextrachargeamount['+grnid+'][]" class="form-control text-right orderextrachargeamount" placeholder="Charge" onkeypress="return decimal_number_validation(event, this.value)" value="'+parseFloat(extracharges[j]['amount']).toFixed(2)+'">\
                                                      </div>\
                                                  </div>\
                                              </div>\
                                              <div class="col-md-2 text-right pt-md">\
                                                <button type="button" class="btn btn-default btn-raised remove_charges_btn" onclick="removecharge('+grnid+','+(j+1)+')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
                                              </div>\
                                          </div>';
                      }
                    }
                    orderextracharge[String(grnid)] = extracharge;
                    
                    var orderdiscountpercent = (parseFloat(grnamountdata[i]['discountamount']) * 100 / (parseFloat(grnamountdata[i]['orderamount']) + parseFloat(grnamountdata[i]['taxamount'])));
                    var discount_text = '';
                    if(parseFloat(orderdiscountpercent) > 0){
                      discount_text += '<div class="col-md-3 pr-sm">\
                                          <div class="form-group p-n text-right" id="orderdiscountpercent'+grnid+'_div">\
                                            <div class="col-sm-12">\
                                              <label class="control-label" for="orderdiscountpercent'+grnid+'">Discount (%)</label>\
                                              <input type="text" id="orderdiscountpercent'+grnid+'" name="orderdiscountpercent['+grnid+']" class="form-control text-right orderdiscountpercent" placeholder="" onkeypress="return decimal_number_validation(event, this.value)" value="'+parseFloat(orderdiscountpercent).toFixed(2)+'">\
                                            </div>\
                                          </div>\
                                        </div>\
                                        <div class="col-md-4 pl-sm pr-sm">\
                                          <div class="form-group p-n text-right" id="orderdiscountamount'+grnid+'_div">\
                                            <div class="col-sm-12">\
                                              <label class="control-label" for="orderdiscountamount'+grnid+'">Discount Amount</label>\
                                              <input type="text" id="orderdiscountamount'+grnid+'" name="orderdiscountamount['+grnid+']" class="form-control text-right orderdiscountamount" placeholder="" onkeypress="return decimal_number_validation(event, this.value)" value="'+parseFloat(grnamountdata[i]['discountamount']).toFixed(2)+'">\
                                              <label class="control-label p-n m-n mb-xs">Max : '+CURRENCY_CODE+' <span id="applymaxdisc'+grnid+'"></span></label>\
                                              <input type="hidden" id="invoicediscamnt'+grnid+'" value="'+grnamountdata[i]['discountamount']+'">\
                                              <input type="hidden" id="orderdiscamnt'+grnid+'" value="'+grnamountdata[i]['discountamount']+'">\
                                            </div>\
                                          </div>\
                                        </div>';
                    }
                    if(extrachargeshtml != "" || discount_text != ""){

                      var ordergrossamount = parseFloat(grnamountdata[i]['orderamount']) + parseFloat(grnamountdata[i]['taxamount']);
                      
                      extrachargespanel += '<div class="panel countorders" id="'+grnid+'">\
                                              <div class="panel-heading">\
                                                <h2 style="width: 35%;"><b>GRN No. :</b> '+grnamountdata[i]['grnnumber']+'</h2>\
                                                <h2 style="width: 33%;"><b>Product Total : </b><span id="displayproducttotal'+grnid+'">0.00</span></h2>\
                                              </div>\
                                              <div class="panel-body no-padding">\
                                                <div class="row m-n">\
                                                '+extrachargeshtml+'\
                                                </div>\
                                              <input type="hidden" name="ordergrossamount[]" id="ordergrossamount_'+grnid+'" class="ordergrossamount" value="'+parseFloat(ordergrossamount).toFixed(2)+'">\
                                              <input type="hidden" name="invoiceorderamount[]" id="invoiceorderamount_'+grnid+'" class="invoiceorderamount" value="'+parseFloat(ordergrossamount).toFixed(2)+'">\
                                                <div class="row m-n">\
                                                  '+discount_text+'\
                                                </div>\
                                              </div>\
                                            </div>\
                                          </div>';
                    }

                    var discountrows = '';
                    /* if(parseFloat(grnamountdata[i]['discountamount']) > 0){
                        discountrows = '<tr>\
                                            <td>Discount Amount</td>\
                                            <th> : </th>\
                                            <td class="text-right">'+parseFloat(grnamountdata[i]['discountamount']).toFixed(2)+'</td>\
                                        </tr>';
                    } */
                   
                    var netamount = (parseFloat(grnamountdata[i]['netamount']) + parseFloat(totalextracharges));
                    if(parseFloat(netamount) < 0){
                      netamount = 0;
                    }
                    html += '<div class="col-sm-4 pl-sm pr-sm" style="margin-bottom:10px;min-height: 200px;">\
                              <table class="table m-n orderamounttable" style="border: 5px solid #e8e8e8;">\
                                <tr>\
                                  <th>GRN No.</th>\
                                  <th> : </th>\
                                  <td><a href="'+SITE_URL+'goods-received-notes/view-goods-received-notes/'+grnamountdata[i]['id']+'" target="_blank">'+grnamountdata[i]['grnnumber']+'</a></td>\
                                </tr>\
                                <tr style="border-bottom: 2px solid #E8E8E8;">\
                                  <th>Received Date</th>\
                                  <th> : </th>\
                                  <td>'+grnamountdata[i]['receiveddate']+'</td>\
                                </tr>\
                                <tr>\
                                  <td>GRN Amount</td>\
                                  <th> : </th>\
                                  <td class="text-right">'+parseFloat(grnamountdata[i]['orderamount']).toFixed(2)+'</td>\
                                </tr>\
                                <tr>\
                                  <td>Tax Amount</td>\
                                  <th> : </th>\
                                  <td class="text-right">'+parseFloat(grnamountdata[i]['taxamount']).toFixed(2)+'</td>\
                                </tr>\
                                '+discountrows+'\
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
                  $('.orderextrachargesid').selectpicker("refresh");
                  $('#orderamountdiv').html(html);
                  
                  if(grnamountdata.length > 0){
                    for(var k=0; k<grnamountdata.length; k++){
                      var GrnID = grnamountdata[k]['id'];
                      
                      for(var l=0; l<orderextracharge[GrnID].length; l++){
                        
                        var extrachargesid = orderextracharge[GrnID][l];
                        $("#orderextrachargesid_"+GrnID+"_"+(l+1)).val(extrachargesid);
                        $("#orderextrachargesid_"+GrnID+"_"+(l+1)).selectpicker('refresh');
                        $("#orderextrachargesid_"+GrnID+"_"+(l+1)+" option:not(:selected)").remove();
                        $("#orderextrachargesid_"+GrnID+"_"+(l+1)).selectpicker('refresh');
                      }
                      calculateorderamount(GrnID);
                    }
                  }
                  /****EXTRA CHARGE CHANGE EVENT****/
                  $('body').on('change', 'select.orderextrachargesid', function() { 
                    var rowid = $(this).attr("id").split('_');
                    var grnid = rowid[1];
                    var divid = rowid[2];
                    calculateextracharges(grnid,divid);
                    changechargespercentage(grnid,divid);
                    overallextracharges();
                    netamounttotal();
                  });
                  $('body').on('keyup', '.orderextrachargeamount', function() { 
                    var rowid = $(this).attr("id").split('_');
                    var grnid = rowid[1];
                    var divid = rowid[2];
                    
                    var grossamount = $("#invoiceorderamount_"+grnid).val();
                    var inputgrossamount = $("#inputgrossamount").val();
                    
                    if(grnid==0){
                      grossamount = parseFloat(inputgrossamount);
                    }

                    var chargestaxamount = chargespercent = 0;
                    var tax = $("#orderextrachargesid_"+grnid+"_"+divid+" option:selected").attr("data-tax");
                    var type = $("#orderextrachargesid_"+grnid+"_"+divid+" option:selected").attr("data-type");
                   
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
                    $("#orderextrachargestax_"+grnid+"_"+divid).val(parseFloat(chargestaxamount).toFixed(2));
                    $("#orderextrachargepercentage_"+grnid+"_"+divid).val(parseFloat(chargespercent).toFixed(2));
                    changechargespercentage(grnid,divid);
                    overallextracharges();
                    netamounttotal();
                  });
                  $('.orderdiscountpercent').on('keyup', function() { 
                    var grnid = $(this).attr("id").match(/\d+/);
                    var discountpercentage = $(this).val();
                    var invoicediscamnt = $("#invoicediscamnt"+grnid).val();
                    var inputgrossamount = $("#inputgrossamount").val();
                    
                    var grossamount = parseFloat(invoicediscamnt);
                    if(grnid==0){
                      grossamount = parseFloat(inputgrossamount);
                    }
                   
                    if(discountpercentage!=undefined && discountpercentage!=''){
                        if(parseFloat(discountpercentage)>100){
                            $(this).val("100");
                            discountpercentage = 100;
                        }
                        
                        if(grossamount!=''){
                            var discountamount = (parseFloat(grossamount)*parseFloat(discountpercentage)/100);
                            
                            $("#orderdiscountamount"+grnid).val(parseFloat(discountamount).toFixed(2));
                            
                            $("#ovdiscper").html(parseFloat(discountpercentage).toFixed(2)); 
                            $("#ovdiscamnt").html(parseFloat(discountamount).toFixed(2)); 
                            $("#inputovdiscamnt").val(parseFloat(discountamount).toFixed(2));

                            var discount = 0 ;
                            $('.orderdiscountamount').each(function( index ) { 
                              var id = $(this).attr("id").match(/\d+/);
                              if(id!=""){
                                discount += parseFloat($(this).val());
                              }
                            });
                            if(grnid==0){
                              if(parseFloat(discount)>parseFloat(grossamount)){
                                new PNotify({title: "Discount amount apply less than product total amount !",styling: 'fontawesome',delay: '3000',type: 'error'});
                                $(this).val('');
                                $("#orderdiscountamount0").val('');
                              }
                            }
                            overallextracharges();
                            netamounttotal();
                        }
                    }else{
                        $(this).val('');
                        $("#orderdiscountamount"+grnid).val('');
                        $("#ovdiscper").html("0"); 
                        $("#ovdiscamnt").html("0.00"); 
                        $("#inputovdiscamnt").val('0.00');
                        overallextracharges();
                        netamounttotal();
                    }
                  });
                  $('.orderdiscountamount').on('keyup', function() { 
                    var grnid = $(this).attr("id").match(/\d+/);
                    var discountamount = $(this).val();
                    var discountpercentage = $("#ovdiscper").html();
                    var invoicediscamnt = $("#invoicediscamnt"+grnid).val();
                    var inputgrossamount = $("#inputgrossamount").val();
                    
                    var grossamount = parseFloat(invoicediscamnt);
                    if(grnid==0){
                      grossamount = parseFloat(inputgrossamount);
                    }
                    
                    if(discountamount!=undefined && discountamount!=''){
                        if(grnid!=0){
                          if(parseFloat(discountamount)>parseFloat(grossamount)){
                            grossamount = (parseFloat(grossamount)>0)?parseFloat(grossamount):0;
                            $(this).val(parseFloat(grossamount));
                            discountamount = parseFloat(grossamount);
                          }
                        }
                        if(parseFloat(grossamount)!=''){
                            var discountpercentage = ((parseFloat(discountamount)*100) / parseFloat(grossamount));
                            if(parseFloat(discountpercentage)==0){
                                $("#orderdiscountpercent"+grnid).val(0);   
                            }else{
                                $("#orderdiscountpercent"+grnid).val(parseFloat(discountpercentage).toFixed(2));   
                            }
            
                            $("#ovdiscper").html(parseFloat(discountpercentage).toFixed(2)); 
                            $("#ovdiscamnt").html(parseFloat(discountamount).toFixed(2)); 
                            $("#inputovdiscamnt").val(parseFloat(discountamount).toFixed(2));
                            if(parseFloat(discountpercentage)>100){
                                $("#orderdiscountpercent"+grnid).val("100");
                            }
                            var discount = 0 ;
                            $('.orderdiscountamount').each(function( index ) { 
                              var id = $(this).attr("id").match(/\d+/);
                              if(id!=""){
                                discount += parseFloat($(this).val());
                              }
                            });
                            if(grnid==0){
                              if(parseFloat(discount)>parseFloat(grossamount)){
                                new PNotify({title: "Discount amount apply less than product total amount !",styling: 'fontawesome',delay: '3000',type: 'error'});
                                $(this).val('');
                                $("#orderdiscountpercent0").val('');
                              }
                            }
                            overallextracharges();
                            netamounttotal();
                        }
                    }else{
                        $(this).val('');
                        $("#orderdiscountpercent"+grnid).val('');
                        $("#ovdiscper").html("0"); 
                        $("#ovdiscamnt").html("0.00"); 
                        $("#inputovdiscamnt").val('0.00');
                        overallextracharges();
                        netamounttotal();
                    }
                  });
                }
              }else{
                $('#orderamountdiv').html("");
                $('#extracharges_div').html("");
              }
             
              overallextracharges();
              netamounttotal();
        }
      },
      error: function(xhr) {
      //alert(xhr.responseText);
      },
    });
  }else{
    $("#invoiceproducttable tbody").html("<tr><td colspan='9' class='text-center'>No data available in table.</td></tr>");
    $('#orderamountdiv').html("");
    $('#extracharges_div').html("");
    $('#billingaddress').val('');
    $('#shippingaddress').val('');
  }
  
}
function calculateorderamount(GrnID){
  var grnqty = invoiceqty = 0;
  $(".grnquantity"+GrnID).each(function( index ) {
    var transactionproductsid = $(this).attr("id").match(/(\d+)/g);
    if($(this).val()!=""){
      grnqty += parseFloat($(this).val());
    }
    if($("#quantity"+transactionproductsid).val()!=""){
      invoiceqty += parseFloat($("#quantity"+transactionproductsid).val());
    }
  });
  var ordergrossamount = (parseFloat(invoiceqty) * parseFloat($("#ordergrossamount_"+GrnID).val()) / parseFloat(grnqty));
  $("#invoiceorderamount_"+GrnID).val(parseFloat(ordergrossamount).toFixed(2));
  $("#displayproducttotal"+GrnID).html(parseFloat(ordergrossamount).toFixed(2));
  changeextrachargesamount();

  var invoicediscamnt = (parseFloat(invoiceqty) * parseFloat($("#orderdiscamnt"+GrnID).val()) / parseFloat(grnqty));
  var invoicediscper = (parseFloat(invoicediscamnt) * 100 / parseFloat(invoicediscamnt));
  $("#orderdiscountamount"+GrnID).val(parseFloat(invoicediscamnt).toFixed(2));
  $("#invoicediscamnt"+GrnID).val(parseFloat(invoicediscamnt).toFixed(2));
  $("#applymaxdisc"+GrnID).html(parseFloat(invoicediscamnt).toFixed(2));
  $("#orderdiscountpercent"+GrnID).val(parseFloat(invoicediscper).toFixed(2));
  
  var producttotal = productgstamount = 0;
  $(".producttotal").each(function( index ) {
    var divid = $(this).attr("id").match(/(\d+)/g);
    if($(this).val()!="" && $("#quantity"+divid).val() >0 ){
      producttotal += parseFloat($(this).val());
    }
  });
  $(".taxvalue").each(function( index ) {
    var divid = $(this).attr("id").match(/(\d+)/g);
    if($(this).val()!="" && $("#quantity"+divid).val() >0 ){
      productgstamount += parseFloat($(this).val());
    }
  });
  var grossamount = parseFloat(producttotal) + parseFloat(productgstamount);
  $("#applymaxdisc0").html(parseFloat(grossamount).toFixed(2));
  var discamnt = $("#orderdiscountamount0").val();
  if(discamnt != ''){
    if(parseFloat(discamnt) > parseFloat(grossamount)){
      $("#orderdiscountamount0").val(parseFloat(grossamount).toFixed(2));
      $("#orderdiscountpercent0").val(parseFloat(100).toFixed(2));
    }else{
      var invoicediscper = (parseFloat(discamnt) * 100 / parseFloat(grossamount));
     
      $("#orderdiscountpercent0").val(parseFloat(invoicediscper).toFixed(2));
    }
  }
}
function changechargespercentage(grnid,divid){
  var type = $("#orderextrachargesid_"+grnid+"_"+divid+" option:selected").attr("data-type");
  var optiontext = $("#orderextrachargesid_"+grnid+"_"+divid+" option:selected").text();
  var grossamount = $("#invoiceorderamount_"+grnid).val();
  var amount = $("#orderextrachargeamount_"+grnid+"_"+divid).val();
  var chargespercent = 0;
  var inputgrossamount = $("#inputgrossamount").val();

  if(grnid==0){
    grossamount = parseFloat(inputgrossamount);
  }
  if(type==0){
    if(parseFloat(amount)> 0){
      chargespercent = parseFloat(amount) * 100 / parseFloat(grossamount);
    }
    optiontext = optiontext.split("(");
    $("#orderextrachargesid_"+grnid+"_"+divid+" option:selected").text(optiontext[0].trim()+" ("+parseFloat(chargespercent).toFixed(2)+"%)");
    $("#orderextrachargesid_"+grnid+"_"+divid).selectpicker("refresh");
    $("#orderextrachargesname_"+grnid+"_"+divid).val(optiontext[0].trim()+" ("+parseFloat(chargespercent).toFixed(2)+"%)");
  }
}
function totalproductamount(grnid,divid) {
  var quantity = $("#quantity"+divid).val();
  var taxtype = $("#taxtype"+divid).val();
  var tax = $("#tax"+divid).val();
  var price = $("#price"+divid).val();
  var actualprice = $("#actualprice"+divid).val();
  var discount = $("#discount"+divid).val();
  
  var discountamount = ((parseFloat(actualprice) * parseFloat(quantity)) * parseFloat(discount) / 100);
  var totalprice = (parseFloat(price) * parseFloat(quantity));
  var taxvalue = parseFloat(parseFloat(price) * parseFloat(quantity) * parseFloat(tax) / 100);
  var total = parseFloat(totalprice) + parseFloat(taxvalue);
  
  if(taxtype==1){
    $("#sgst"+divid).html(parseFloat(taxvalue/2).toFixed(2));
    $("#cgst"+divid).html(parseFloat(taxvalue/2).toFixed(2));
  }else{
    $("#igst"+divid).html(parseFloat(taxvalue).toFixed(2));
  }
  $("#discountamount"+divid).html(parseFloat(discountamount).toFixed(2));
  $("#productnetprice"+divid).html(parseFloat(total).toFixed(2));
  $("#taxvalue"+divid).val(parseFloat(taxvalue).toFixed(2));
  $("#producttotal"+divid).val(parseFloat(parseFloat(totalprice)).toFixed(2));
  calculateorderamount(grnid);
  changeextrachargesamount();
  overallextracharges();
  netamounttotal();
}

function changeextrachargesamount(){
 
  $(".orderextrachargeamount").each(function( index ) {
    var element = $(this).attr("id").split('_');
    var grnid = element[1];
    var divid = element[2];
    calculateextracharges(grnid,divid);
  });
}

function calculateextracharges(grnid,rowid){
  var extracharges = $("#orderextrachargesid_"+grnid+"_"+rowid).val();
  var type = $("#orderextrachargesid_"+grnid+"_"+rowid+" option:selected").attr("data-type");
  var amount = $("#orderextrachargesid_"+grnid+"_"+rowid+" option:selected").attr("data-amount");
  var tax = $("#orderextrachargesid_"+grnid+"_"+rowid+" option:selected").attr("data-tax");

  var totalgrossamount = $("#invoiceorderamount_"+grnid).val();
  var inputgrossamount = $("#inputgrossamount").val();
                    
  if(grnid==0){
    totalgrossamount = parseFloat(inputgrossamount);
  }
  /* var discount = $("#discountamount").html();
  var couponamount = $("#coupondiscountamount").html(); */
  
  var chargesamount = chargestaxamount = 0;
  if(parseFloat(totalgrossamount)>0 && parseFloat(extracharges) > 0){
      if(type==0){
          chargesamount = parseFloat(totalgrossamount) * parseFloat(amount) / 100;
      }else{
          chargesamount = parseFloat(amount);
      }
      
      chargestaxamount = parseFloat(chargesamount) * parseFloat(tax) / (100+parseFloat(tax));
      
      $("#orderextrachargestax_"+grnid+"_"+rowid).val(parseFloat(chargestaxamount).toFixed(2));
      $("#orderextrachargeamount_"+grnid+"_"+rowid).val(parseFloat(chargesamount).toFixed(2));
  }else{
      $("#orderextrachargestax_"+grnid+"_"+rowid).val(parseFloat(0).toFixed(2));
      $("#orderextrachargeamount_"+grnid+"_"+rowid).val(parseFloat(0).toFixed(2));
  }
  var chargesname = $("#orderextrachargesid_"+grnid+"_"+rowid+" option:selected").text();
  $("#orderextrachargesname_"+grnid+"_"+rowid).val(chargesname.trim());
  var chargespercent = 0;
  if(type==0){
      chargespercent = parseFloat(amount);
  }
  $("#orderextrachargepercentage_"+grnid+"_"+rowid).val(parseFloat(chargespercent).toFixed(2));
  netamounttotal();
}

function overallextracharges(){
  
  /********* CALCULATE EXTRA CHARGES START *********/
  var extrachargesrow = '';
  var CHARGES_ARR = [];
  var extrachargesamnt = [];
  $(".tr_extracharges").remove();
  $("select.orderextrachargesid").each(function( index ) {
    var element = $(this).attr("id").split('_');
    var grnid = element[1];
    var divid = element[2];
    var extrachargesname = $("#orderextrachargesname_"+grnid+"_"+divid).val();
    var extrachargeamount = $("#orderextrachargeamount_"+grnid+"_"+divid).val();
    var extrachargestax = $("#orderextrachargestax_"+grnid+"_"+divid).val();
    var extrachargepercentage = $("#orderextrachargepercentage_"+grnid+"_"+divid).val();
    var extrachargesdatatype = $("#orderextrachargesid_"+grnid+"_"+divid+" option:selected").attr("data-type");
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
        $("select.orderextrachargesid").each(function( index ) {
          var elementid = $(this).attr("id").split('_');
          var grnid = elementid[1];
          var Id = elementid[2];
          var thisid = $(this).val();
          var sumchargeamount = $("#orderextrachargeamount_"+grnid+"_"+Id).val();
          var sumchargetax = $("#orderextrachargestax_"+grnid+"_"+Id).val();
          var thisid = $(this).val();
          var thistype = $("#orderextrachargesid_"+grnid+"_"+Id+" option:selected").attr("data-type");
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
  var discountamount = orderdiscountpercent = 0;
  $(".orderdiscountamount").each(function( index ) {
    //var divid = $(this).attr("id").match(/(\d+)/g);
    //var percent = $("#orderdiscountpercent"+divid).val();
    if(this.value > 0){
      discountamount += parseFloat(this.value);
      //orderdiscountpercent += parseFloat(percent);
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
  
  $(".producttotal").each(function( index ) {
    var divid = $(this).attr("id").match(/(\d+)/g);
    if($(this).val()!="" && $("#quantity"+divid).val() >0 ){
      producttotal += parseFloat($(this).val());
    }
  });
  $(".taxvalue").each(function( index ) {
    var divid = $(this).attr("id").match(/(\d+)/g);
    if($(this).val()!="" && $("#quantity"+divid).val() >0 ){
      productgstamount += parseFloat($(this).val());
    }
  });
  $("#producttotal").html(parseFloat(producttotal).toFixed(2));
  $("#inputproducttotal").val(parseFloat(producttotal).toFixed(2));
  $("#gsttotal").html(parseFloat(productgstamount).toFixed(2));
  $("#inputgsttotal").val(parseFloat(productgstamount).toFixed(2));

  if($("select.orderextrachargesid").length > 0){
    $(".tr_extracharges").each(function( index ) {
      if($(this).attr("id")!="default"){
        var grnid = $(this).attr("id").match(/(\d+)/g);
        var exchrgamnt = $("#extrachargeamount"+grnid).html();
        var exchrgtax = $("#extrachargestax"+grnid).val();
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
  $("#grossamount").html(parseFloat(grossamount).toFixed(2));
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

function printInvoice(id){

  var uurl = SITE_URL + "purchase-invoice/printPurchaseInvoice";
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

function addnewcharge(){

  var lastid = $(".countcharges0:last").attr("id").split("_");
  var rowcount = parseInt(lastid[2])+1;
  var datahtml = ' <div class="col-md-6 p-n countcharges0" id="countcharges_0_'+rowcount+'">\
                      <div class="col-sm-6 pr-xs">\
                          <div class="form-group p-n" id="extracharges_0_'+rowcount+'_div">\
                              <div class="col-sm-12">\
                                  <select id="orderextrachargesid_0_'+rowcount+'" name="orderextrachargesid[0][]" class="selectpicker form-control orderextrachargesid" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                      <option value="0">Select Extra Charges</option>\
                                          '+extrachargeoptionhtml+'\
                                  </select>\
                                  <input type="hidden" name="orderextrachargestax[0][]" id="orderextrachargestax_0_'+rowcount+'" class="orderextrachargestax" value="">\
                                  <input type="hidden" name="orderextrachargesname[0][]" id="orderextrachargesname_0_'+rowcount+'" class="orderextrachargesname" value="">\
                                  <input type="hidden" name="orderextrachargepercentage[0][]" id="orderextrachargepercentage_0_'+rowcount+'" class="orderextrachargepercentage" value="">\
                              </div>\
                          </div>\
                      </div>\
                      <div class="col-sm-3 pl-xs pr-xs">\
                        <div class="form-group p-n" id="orderextrachargeamount_0_'+rowcount+'_div">\
                            <div class="col-sm-12">\
                                <input type="text" id="orderextrachargeamount_0_'+rowcount+'" name="orderextrachargeamount[0][]" class="form-control text-right orderextrachargeamount" placeholder="Charge" onkeypress="return decimal_number_validation(event, this.value)">\
                            </div>\
                        </div>\
                      </div>\
                      <div class="col-md-3 text-right pt-md">\
                          <button type="button" class="btn btn-default btn-raised remove_charges_btn m-n" onclick="removecharge(0,'+rowcount+')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
                          <button type="button" class="btn btn-default btn-raised add_charges_btn m-n" onclick="addnewcharge()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>\
                      </div>\
                    </div>';
  
  $(".countcharges0 .remove_charges_btn:first").show();
  $(".countcharges0 .add_charges_btn:last").hide();
  $("#countcharges_0_"+(rowcount-1)).after(datahtml);
  
  $("#orderextrachargesid_0_"+rowcount).selectpicker("refresh");
}

function removecharge(grnid,rowid){

  $("#countcharges_"+grnid+"_"+rowid).remove();
  overallextracharges();
  if(grnid==0){
    $(".countcharges"+grnid+" .add_charges_btn:last").show();
    if ($(".countcharges"+grnid+" .remove_charges_btn:visible").length == 1) {
        $(".countcharges"+grnid+" .remove_charges_btn:first").hide();
    }
  }
  netamounttotal();
}

function resetdata(){  
  
  $("#vendor_div").removeClass("has-error is-focused");
  $("#grnid_div").removeClass("has-error is-focused");
  $("#invoiceno_div").removeClass("has-error is-focused");
  $("#billingaddress_div").removeClass("has-error is-focused");
  $("#shippingaddress_div").removeClass("has-error is-focused");
  $("#invoicedate_div").removeClass("has-error is-focused");
  
  if(ACTION==0){
      if(VendorId==0){
        $('#vendorid,#grnid').val('0');
        $('#billingaddressid,#shippingaddressid').val('');
        $('#billingaddress').val('');
        $('#shippingaddress').val('');
        $('#grnid')
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
      $('#grnid,#billingaddressid,#shippingaddressid').selectpicker('refresh');
      }else{
        $('#vendorid').val(VendorId);
        $('#grnid').val(GRNId);
      }
      $('#remarks').val("");
      $('#invoicedate').val(new Date().toLocaleDateString());
      $('.selectpicker').selectpicker('refresh');
      if(VendorId!=0){
        getTransactionProducts();
      }else{
        $("#invoiceproducttable tbody").html("<tr><td colspan='9' class='text-center'>No data available in table.</td></tr>");
        $('#orderamountdiv').html("");
        $('#extracharges_div').html("");
      }
      overallextracharges();
      netamounttotal();
  }

  $('html, body').animate({scrollTop:0},'slow');
}

function checkvalidation(btntype=''){
  
  var partyid = $('#partyid').val();
  var invoiceid = $('#invoiceid').val();
  var grnid = $('#grnid').val();
  var billingaddressid = $('#billingaddressid').val();
  var shippingaddressid = $('#shippingaddressid').val();
  var poid = $('#poid').val();
  var invoiceno = $("#invoiceno").val();
  var invoicedate = $('#invoicedate').val();
  var recievedate = $('#recievedate').val();
  var receiveby = $('#receiveby').val();
  var payment_due = $('#payment_due').val();
  
  var isvalidvendorid = 
  isvalidgrnid = 
  isvalidproductcount = 
  isvalidbillingaddressid = 
  isvalidshippingaddressid = 
  isvalidinvoiceno = 
  isvalidinvoicedate = 
  isvalidextrachargesid = 
  isvalidextrachargeamount = 
  isvalidduplicatecharges = 
  isvalidpartyid =
  isvalidreceiveby  =
  isvalidrecievedate =
  isvalidpayment_due_div 
  = 1;
  PNotify.removeAll();

  if(partyid == 0){
    $("#party_div").addClass("has-error is-focused");
    new PNotify({title: "Please select Party  !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidpartyid = 0;
  }else{
    $("#party_div").removeClass("has-error is-focused");
  }
  if(grnid == 0 || grnid == null){
    $("#grnid_div").addClass("has-error is-focused");
    new PNotify({title: "Please select branch !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidgrnid = 0;
  }else{
    $("#grnid_div").removeClass("has-error is-focused");
  }

  if(receiveby == 0 || receiveby == null){
    $("#receiveby_div").addClass("has-error is-focused");
    new PNotify({title: "Please select Receive By !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidreceiveby = 0;
  }else{
    $("#receiveby_div").removeClass("has-error is-focused");
  }

  if(receiveby == 0 || receiveby == null){
    $("#receiveby_div").addClass("has-error is-focused");
    new PNotify({title: "Please select Receive By !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidreceiveby = 0;
  }else{
    $("#receiveby_div").removeClass("has-error is-focused");
  }
  if(poid == ''){
    $("#poid_div").addClass("has-error is-focused");
    new PNotify({title: "Please select  !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidreceiveby = 0;
  }else{
    $("#poid_div").removeClass("has-error is-focused");
  }

  if(invoiceno == ""){
    $("#invoiceno_div").addClass("has-error is-focused");
    new PNotify({title: "Please enter invoice number !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidinvoiceno = 0;
  }else{
    $("#invoiceno_div").removeClass("has-error is-focused");
  }
  if(invoicedate == ""){
    $("#invoicedate_div").addClass("has-error is-focused");
    new PNotify({title: "Please select invoice date !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidinvoicedate = 0;
  }else{
    $("#invoicedate_div").removeClass("has-error is-focused");
  }
  if(recievedate == ""){
    $("#recievedate_div").addClass("has-error is-focused");
    new PNotify({title: "Please select invoice date !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidrecievedate = 0;
  }else{
    $("#recievedate_div").removeClass("has-error is-focused");
  }
  if(payment_due == ""){
    $("#payment_due_div").addClass("has-error is-focused");
    new PNotify({title: "Please select invoice date !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidpayment_due_div = 0;
  }else{
    $("#payment_due_div").removeClass("has-error is-focused");
  }

  // if(billingaddressid == "" || billingaddressid == null){
  //   $("#billingaddress_div").addClass("has-error is-focused");
  //   new PNotify({title: "Please select billing address !",styling: 'fontawesome',delay: '3000',type: 'error'});
  //   isvalidbillingaddressid = 0;
  // }else{
  //   $("#billingaddress_div").removeClass("has-error is-focused");
  // }

  // if(shippingaddressid == "" || shippingaddressid == null){
  //   $("#shippingaddress_div").addClass("has-error is-focused");
  //   new PNotify({title: "Please select shipping address !",styling: 'fontawesome',delay: '3000',type: 'error'});
  //   isvalidshippingaddressid = 0;
  // }else{
  //   $("#shippingaddress_div").removeClass("has-error is-focused");
  // }
  if($('.countproducts').length == 0){
    isvalidproductcount==0;
    new PNotify({title: "Please add at least one product !",styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  var i=1;
  $('.countorders').each(function(){
    var grnid = $(this).attr('id');
    $('.countcharges'+grnid).each(function(){
      var elementid = $(this).attr('id').split('_');
      var divid = elementid[2];
      
      if($("#orderextrachargesid_"+grnid+"_"+divid).val() > 0 || $("#orderextrachargeamount_"+grnid+"_"+divid).val() > 0){

          if($("#orderextrachargesid_"+grnid+"_"+divid).val() == 0){
              $("#extracharges_"+grnid+"_"+divid+"_div").addClass("has-error is-focused");
              new PNotify({title: 'Please select '+divid+' extra charge !',styling: 'fontawesome',delay: '3000',type: 'error'});
              isvalidextrachargesid = 0;
          }else {
              $("#extracharges_"+grnid+"_"+divid+"_div").removeClass("has-error is-focused");
          }
          if($("#orderextrachargeamount_"+grnid+"_"+divid).val() == '' || $("#orderextrachargeamount_"+grnid+"_"+divid).val() == 0){
              $("#orderextrachargeamount_"+grnid+"_"+divid+"_div").addClass("has-error is-focused");
              var msg = (grnid==0)?"other charges":(i)+" order";
              new PNotify({title: 'Please enter '+divid+' extra charge amount on '+msg+' !',styling: 'fontawesome',delay: '3000',type: 'error'});
              isvalidextrachargeamount = 0;
          }else {
              $("#orderextrachargeamount_"+grnid+"_"+divid+"_div").removeClass("has-error is-focused");
          }
      } else{
          $("#extracharges_"+grnid+"_"+divid+"_div").removeClass("has-error is-focused");
          $("#orderextrachargeamount_"+grnid+"_"+divid+"_div").removeClass("has-error is-focused");
      }

    });
    i++;
  });
  var c=1;
    $('.countproducts').each(function(){
        var id = $(this).attr('id').match(/\d+/);
        if($("#productid"+id).val() > 0  || $("#invoiceid"+id).val() != "" ){
            if($("#productid"+id).val() == 0){
                $("#product"+id+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(c)+' product !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidcategory = 0;
            }else {
                $("#product"+id+"_div").removeClass("has-error is-focused");
            }

            if($("#qty"+id).val() == ""){
                $("#qty"+id+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(c)+' qty !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidcategory = 0;
            }else {
                $("#qty"+id+"_div").removeClass("has-error is-focused");
            }

        } else {
            $("#invoice"+id+"_div").removeClass("has-error is-focused");
            $("#price"+id+"_div").removeClass("has-error is-focused");
            $("#invoiceprice"+id+"_div").removeClass("has-error is-focused");
        }
        c++;
    });
 
  if(isvalidpartyid==1 
    && isvalidduplicatecharges == 1
    && isvalidreceiveby == 1
    && isvalidrecievedate == 1
    && isvalidpayment_due_div == 1
    ){
    
    var formData = new FormData($('#purchaseinvoiceform')[0]);
    if(invoiceid==''){
      var uurl = SITE_URL+"purchase-invoice/add-purchase-invoice";
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
            new PNotify({title: "Purchase invoice successfully generated !",styling: 'fontawesome',delay: '3000',type: 'success'});
            if(btntype=='print'){
              printInvoice(obj['invoiceid']);
              setTimeout(function() { window.location=SITE_URL+"purchase-invoice"; }, 1500);
            }else{
              resetdata();
              if(VendorId!=0){
                getTransactionProducts();
              }
            }
          }else if(obj['error']==2){
            new PNotify({title: "Invoice number already exist !",styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#invoiceno_div").addClass("has-error is-focused");
          }else{
            new PNotify({title: "Purchase invoice not generate !",styling: 'fontawesome',delay: '3000',type: 'error'});
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
                      <option value="0">Select Product</option>\
                  </select>\
              </div>\
          </div>\
      </td>\
      <td>\
          <div class="form-group" id="qty'+divcount+'_div">\
              <div class="col-sm-12">\
                  <input type="text" class="form-control actualprice text-right" id="qty'+divcount+'" name="actualprice[]" value="" onkeypress="return decimal_number_validation(event, this.value)" style="display: block;" div-id="'+divcount+'">\
              </div>\
          </div>\
      </td>\
      <td>\
          <div class="form-group" id="actualprice'+divcount+'_div">\
              <div class="col-sm-12">\
                  <input type="text" class="form-control actualprice text-right" id="actualprice'+divcount+'" name="actualprice[]" value="" onkeypress="return decimal_number_validation(event, this.value)" style="display: block;" div-id="'+divcount+'">\
              </div>\
          </div>\
      </td>\
      <td>\
        <div class="form-group" id="actualprice'+divcount+'_div">\
            <div class="col-sm-12">\
                <input type="text" class="form-control actualprice text-right" id="actualprice'+divcount+'" name="actualprice[]" value="" onkeypress="return decimal_number_validation(event, this.value)" style="display: block;" div-id="'+divcount+'">\
            </div>\
        </div>\
      </td>\
      <td>\
          <div class="form-group" id="tax'+divcount+'_div">\
              <div class="col-sm-12">\
                  <input type="text" class="form-control text-right tax" id="tax'+divcount+'" name="tax[]" value="" div-id="'+divcount+'" >	\
                  <input type="hidden" value="" id="ordertax'+divcount+'">\
              </div>\
          </div>\
      </td>\
      <td>\
          <div class="form-group" id="tax'+divcount+'_div">\
              <div class="col-sm-12">\
                  <input type="text" class="form-control text-right tax" id="tax'+divcount+'" name="tax[]" value="" div-id="'+divcount+'" >	\
                  <input type="hidden" value="" id="ordertax'+divcount+'">\
              </div>\
          </div>\
      </td>\
      <td>\
          <div class="form-group" id="tax'+divcount+'_div">\
              <div class="col-sm-12">\
                  <input type="text" class="form-control text-right tax" id="tax'+divcount+'" name="tax[]" value="" div-id="'+divcount+'" >	\
                  <input type="hidden" value="" id="ordertax'+divcount+'">\
              </div>\
          </div>\
      </td>\
      <td>\
          <div class="form-group" id="tax'+divcount+'_div">\
              <div class="col-sm-12">\
                  <input type="text" class="form-control text-right tax" id="tax'+divcount+'" name="tax[]" value="" div-id="'+divcount+'" >	\
                  <input type="hidden" value="" id="ordertax'+divcount+'">\
              </div>\
          </div>\
      </td>\
      <td>\
          <div class="form-group" id="tax'+divcount+'_div">\
              <div class="col-sm-12">\
                  <input type="text" class="form-control text-right tax" id="tax'+divcount+'" name="tax[]" value="" div-id="'+divcount+'" >	\
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
 
  changeextrachargesamount();
}

