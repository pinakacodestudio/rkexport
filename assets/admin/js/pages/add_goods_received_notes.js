$(document).ready(function() {  
  
  $('#receiveddate').datepicker({
    todayHighlight: true,
    format: 'dd/mm/yyyy',
    todayBtn:"linked",
  });
  /****VENDOR CHANGE EVENT****/
  $('#vendorid').on('change', function (e) {
      getVendorSalesOrder();
      changeextrachargesamount();
      overallextracharges();
      netamounttotal();
  });
  /****ORDERID CHANGE EVENT****/
  $('#orderid').on('change', function (e) {
      getTransactionProducts();
      changeextrachargesamount();
      overallextracharges();
      netamounttotal();
  });

  $(".countcharges0 .add_charges_btn").hide();
  $(".countcharges0 .add_charges_btn:last").show();

  if((ACTION==1 || ACTION==0) && OrderId!='' && OrderId!=null){
      getVendorSalesOrder();
      getTransactionProducts();
      netamounttotal();
  }
  $('body').on('keyup', '.qty', function() { 
    var divid = $(this).attr("id").match(/(\d+)/g);
    var orderid = $("#orderidarr"+divid).val();
    if(parseFloat(this.value) > parseFloat($("#orderqty"+divid).val())){
      if(MANAGE_DECIMAL_QTY==1){
        $(this).val(parseFloat($("#orderqty"+divid).val()).toFixed(2));
      }else{
        $(this).val(parseInt($("#orderqty"+divid).val()));
      }
    }
    totalproductamount(orderid,divid);
  });
  $('body').on('change', '.qty', function() { 
    var divid = $(this).attr("id").match(/(\d+)/g);
    var orderid = $("#orderidarr"+divid).val();
    if(parseFloat(this.value) > parseFloat($("#orderqty"+divid).val())){
      if(MANAGE_DECIMAL_QTY==1){
        $(this).val(parseFloat($("#orderqty"+divid).val()).toFixed(2));
      }else{
        $(this).val(parseInt($("#orderqty"+divid).val()));
      }
    }
    totalproductamount(orderid,divid);
  });

  $('#editgrnnumber').change(function () {
    if($(this).is(':checked')){
      $("#grnno").prop("readonly",false);
    }else{
      $("#grnno").val($("#grnnumber").val()).prop("readonly",true);
    }
  });
});

function getVendorSalesOrder(){
  $('#orderid')
      .find('option')
      .remove()
      .end()
      .append()
      .val('0')
  ;
  
  $('#orderid').selectpicker('refresh');

  var vendorid = $("#vendorid").val();
  
  if(vendorid!=0){
    var uurl = SITE_URL+"vendor/getVendorSalesOrder";
    var withorderid = (ACTION==1)?OrderId:0;
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {vendorid:String(vendorid),withorderid:String(withorderid),from:'grn'},
      dataType: 'json',
      async: false,
      success: function(response){

        for(var i = 0; i < response.length; i++) {

          if(ACTION==1 || (ACTION==0 && OrderId!=null || OrderId!='')){
            if(OrderId!=null || OrderId!=''){
             
              OrderId = OrderId.toString().split(',');
             
              if(OrderId.includes(response[i]['id'])){
                $('#orderid').append($('<option>', { 
                  value: response[i]['id'],
                  selected: "selected",
                  text : ucwords(response[i]['orderid'])
                }));
              }else{
                $('#orderid').append($('<option>', { 
                  value: response[i]['id'],
                  text : ucwords(response[i]['orderid'])
                }));
              }
            }
          }else{
            $('#orderid').append($('<option>', { 
              value: response[i]['id'],
              text : ucwords(response[i]['orderid'])
            }));
          }
        }
      },
      error: function(xhr) {
      //alert(xhr.responseText);
      },
    });
  }else{
    $("#orderproducttable tbody").html("<tr><td colspan='10' class='text-center'>No data available in table.</td></tr>");
  }
  $('#orderid').selectpicker('refresh');
  
}

function getTransactionProducts(){
  
  var vendorid = $("#vendorid").val();
  var orderid = $("#orderid").val();
  var grnid = $("#grnid").val(); 

  $('.disccol,.cgstcol,.sgstcol,.igstcol').show();
  
  if(orderid!='' && orderid!=null){
    var uurl = SITE_URL+"goods-received-notes/getTransactionProducts";
    
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {vendorid:String(vendorid),orderid:String(orderid),grnid:String(grnid)},
      dataType: 'json',
      async: false,
      success: function(response){
          if(response!=""){
              var orderproducts = response['orderproducts'];
              var orderamountdata = response['orderamountdata'];
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

              if(orderproducts!=null && orderproducts!=""){
                if(orderproducts.length>0){
                  for(var i=0; i<orderproducts.length; i++){

                    if(grnid!=''){
                      var qty = parseFloat(orderproducts[i]['editquantity']);
                      var orderqty = parseFloat(orderproducts[i]['quantity']) - parseFloat(orderproducts[i]['grnqty']);
                    }else{
                      var qty = parseFloat(orderproducts[i]['quantity']) - parseFloat(orderproducts[i]['grnqty']);
                      var orderqty = qty;
                    }

                    gstcolumn.push(orderproducts[i]['igst']);
                    // var qty = parseInt(orderproducts[i]['quantity']);
                    var tax = parseFloat(orderproducts[i]['tax']);
                    var amount = parseFloat(orderproducts[i]['amount']);
                    var originalprice = parseFloat(orderproducts[i]['originalprice']);

                    var discount = parseFloat(orderproducts[i]['discount']);
                    discolumn += parseFloat(discount);

                    var discountamount = ((parseFloat(originalprice) * parseFloat(qty)) * parseFloat(discount) / 100);
                    
                    var totalprice = (parseFloat(amount) * parseFloat(qty));
                    var taxvalue = parseFloat(parseFloat(amount) * parseFloat(qty) * parseFloat(tax) / 100);
                    var total = parseFloat(totalprice) + parseFloat(taxvalue);
                    
                    var orderid = orderproducts[i]['orderid'];
                    if(parseFloat(orderproducts[i]['quantity']) == parseFloat(orderproducts[i]['grnqty'])){
                      var orderid = "";
                    }
                    htmldata += "<tr class='countproducts' id='"+orderproducts[i]['orderproductsid']+"'>";
                      htmldata += "<td rowspan='2'>"+(i+1);
                      htmldata += '<input type="hidden" name="orderproductsid[]" value="'+orderproducts[i]['orderproductsid']+'">';
                      htmldata += '<input type="hidden" id="price'+orderproducts[i]['orderproductsid']+'" value="'+parseFloat(amount)+'">';
                      htmldata += '<input type="hidden" id="actualprice'+orderproducts[i]['orderproductsid']+'" value="'+parseFloat(originalprice)+'">';
                      htmldata += '<input type="hidden" id="tax'+orderproducts[i]['orderproductsid']+'" value="'+parseFloat(tax)+'">';
                      htmldata += '<input type="hidden" id="taxtype'+orderproducts[i]['orderproductsid']+'" value="'+orderproducts[i]['igst']+'">';
                      htmldata += '<input type="hidden" id="taxvalue'+orderproducts[i]['orderproductsid']+'" class="taxvalue" value="'+parseFloat(taxvalue).toFixed(2)+'">';
                      htmldata += '<input type="hidden" id="producttotal'+orderproducts[i]['orderproductsid']+'" class="producttotal" value="'+parseFloat(parseFloat(amount) * parseFloat(qty)).toFixed(2)+'">';
                      htmldata += '<input type="hidden" id="discount'+orderproducts[i]['orderproductsid']+'" class="discount" value="'+parseFloat(discount).toFixed(2)+'">';
                      htmldata += '<input type="hidden" name="orderidarr[]" id="orderidarr'+orderproducts[i]['orderproductsid']+'" value="'+orderid+'">';
                      htmldata += '<input type="hidden" id="orderquantity'+orderproducts[i]['orderproductsid']+'" value="'+parseFloat(orderproducts[i]['quantity'])+'" class="orderquantity'+orderid+'">';
                      
                      htmldata += "</td>";

                      htmldata += "<td rowspan='2'>"+ucwords(orderproducts[i]['productname'])+"<br><br><b>OrderID: </b>"+orderproducts[i]['ordernumber']+"</td>";
                      
                      htmldata += '<td rowspan="2" class="width8"><div class="col-md-12 pl pr"><div class="form-group" id="quantity'+orderproducts[i]['orderproductsid']+'_div"><input type="text" name="quantity[]" id="quantity'+orderproducts[i]['orderproductsid']+'" class="form-control qty" value="'+parseFloat(qty).toFixed(2)+'" onkeypress="'+(MANAGE_DECIMAL_QTY==1?"return decimal_number_validation(event, this.value,8);":"return isNumber(event);")+'">\
                      <input type="hidden" name="orderqty" id="orderqty'+orderproducts[i]['orderproductsid']+'" value="'+parseFloat(orderqty).toFixed(2)+'"></td>';
                      
                      htmldata += "<td rowspan='2' class='text-right'>"+parseFloat(amount).toFixed(2)+"<br><br><p><b>Total GRN Qty.: </b>"+parseFloat(orderproducts[i]['grnqty']).toFixed(2)+"</p></td>";

                      if(gstprice == 1){
                        if(parseFloat(discount) > 0){
                          htmldata += "<td class='text-right disccol'>"+parseFloat(discount).toFixed(2)+"</td>";
                        }else{
                          htmldata += "<td class='text-right disccol'>-</td>";
                        }
                      
                        if(orderproducts[i]['igst']==1){
                          htmldata += "<td class='text-right sgstcol'>"+parseFloat((parseFloat(tax)/2)).toFixed(2)+"</td>";
                          htmldata += "<td class='text-right cgstcol'>"+parseFloat((parseFloat(tax)/2)).toFixed(2)+"</td>";
                        }else{
                          htmldata += "<td class='text-right igstcol'>"+parseFloat(tax).toFixed(2)+"</td>";
                        }
                      }else{
                        if(orderproducts[i]['igst']==1){
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
                      
                      htmldata += "<td rowspan='2' class='text-right netamount' id='productnetprice"+orderproducts[i]['orderproductsid']+"'>"+parseFloat(total).toFixed(2)+"</td>";
                    htmldata += "</tr>";

                    htmldata += "<tr>";
                    if(gstprice == 1){
                      if(parseFloat(discount) > 0){
                        htmldata += "<td class='text-right disccol' id='discountamount"+orderproducts[i]['orderproductsid']+"'>"+parseFloat(discountamount).toFixed(2)+"</td>";
                      }else{
                        htmldata += "<td class='text-right disccol'>-</td>";
                      }
                      if(orderproducts[i]['igst']==1){
                        htmldata += "<td class='text-right sgstcol' id='sgst"+orderproducts[i]['orderproductsid']+"'>"+parseFloat((taxvalue/2)).toFixed(2)+"</td>";
                        htmldata += "<td class='text-right cgstcol' id='cgst"+orderproducts[i]['orderproductsid']+"'>"+parseFloat((taxvalue/2)).toFixed(2)+"</td>";
                      }else{
                        htmldata += "<td class='text-right igstcol' id='igst"+orderproducts[i]['orderproductsid']+"'>"+parseFloat(taxvalue).toFixed(2)+"</td>";
                      }
                    }else{
                      if(orderproducts[i]['igst']==1){
                        htmldata += "<td class='text-right sgstcol' id='sgst"+orderproducts[i]['orderproductsid']+"'>"+parseFloat((taxvalue/2)).toFixed(2)+"</td>";
                        htmldata += "<td class='text-right cgstcol' id='cgst"+orderproducts[i]['orderproductsid']+"'>"+parseFloat((taxvalue/2)).toFixed(2)+"</td>";
                      }else{
                        htmldata += "<td class='text-right igstcol' id='igst"+orderproducts[i]['orderproductsid']+"'>"+parseFloat(taxvalue).toFixed(2)+"</td>";
                      }      
                      if(parseFloat(discount) > 0){
                        htmldata += "<td class='text-right disccol' id='discountamount"+orderproducts[i]['orderproductsid']+"'>"+parseFloat(discountamount).toFixed(2)+"</td>";
                      }else{
                        htmldata += "<td class='text-right disccol'>-</td>";
                      }              
                    }
                    htmldata += "</tr>";
                  }
                }else{
                  $("#orderproducttable tbody").html("<tr><td colspan='9' class='text-center'>No data available in table.</td></tr>");
                }
              }
              $("#orderproducttable thead").html(headerdata);
              $("#orderproducttable tbody").html(htmldata);
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
              if(orderamountdata!=null && orderamountdata!=""){
                if(orderamountdata.length>0){
                  var orderextracharge = [];
                  var orderidArr = [];
                  for(var i=0; i<orderamountdata.length; i++){
                    var extrachargesrows = extrachargeshtml = '';
                    var orderid = orderamountdata[i]['id'];
                    
                    var extracharges = orderamountdata[i]['extracharges'];
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
                        
                        extrachargeshtml += '<div class="col-md-6 p-n countcharges'+orderid+'" id="countcharges_'+orderid+'_'+(j+1)+'" '+HIDE_PURCHASE_EXTRA_CHARGES+'>\
                                              <div class="col-sm-7 pr-xs">\
                                                  <div class="form-group" id="extracharges_'+orderid+'_'+(j+1)+'_div">\
                                                      <div class="col-sm-12">\
                                                          <select id="orderextrachargesid_'+orderid+'_'+(j+1)+'" name="orderextrachargesid['+orderid+'][]" class="selectpicker form-control orderextrachargesid" data-live-search="true" data-select-on-tab="true" data-size="5" data-live-search="true">\
                                                            <option value="0">Select Extra Charges</option>\
                                                            '+extrachargeoptionhtml+'\
                                                          </select>\
                                                          <input type="hidden" name="orderextrachargestax['+orderid+'][]" id="orderextrachargestax_'+orderid+'_'+(j+1)+'" class="orderextrachargestax" value="'+extracharges[j]['taxamount']+'">\
                                                          <input type="hidden" name="orderextrachargesname['+orderid+'][]" id="orderextrachargesname_'+orderid+'_'+(j+1)+'" class="orderextrachargesname" value="'+extracharges[j]['extrachargesname']+'">\
                                                          <input type="hidden" name="orderextrachargepercentage['+orderid+'][]" id="orderextrachargepercentage_'+orderid+'_'+(j+1)+'" class="orderextrachargepercentage" value="'+extracharges[j]['extrachargepercentage']+'">\
                                                      </div>\
                                                  </div>\
                                              </div>\
                                              <div class="col-sm-3 pl-xs pr-xs">\
                                                  <div class="form-group p-n" id="orderextrachargeamount_'+orderid+'_'+(j+1)+'_div">\
                                                      <div class="col-sm-12">\
                                                          <input type="text" id="orderextrachargeamount_'+orderid+'_'+(j+1)+'" name="orderextrachargeamount['+orderid+'][]" class="form-control text-right orderextrachargeamount" placeholder="Charge" onkeypress="return decimal_number_validation(event, this.value)" value="'+parseFloat(extracharges[j]['amount']).toFixed(2)+'">\
                                                      </div>\
                                                  </div>\
                                              </div>\
                                              <div class="col-md-2 text-right pt-md">\
                                                <button type="button" class="btn btn-default btn-raised remove_charges_btn" onclick="removecharge('+orderid+','+(j+1)+')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
                                              </div>\
                                          </div>';
                      }
                    }
                    orderextracharge[String(orderid)] = extracharge;
                  
                    if(extrachargeshtml != ""){

                      var ordergrossamount = parseFloat(orderamountdata[i]['orderamount']) + parseFloat(orderamountdata[i]['taxamount']);
                      
                      extrachargespanel += '<div class="panel countorders" id="'+orderid+'">\
                                              <div class="panel-heading">\
                                                <h2 style="width: 35%;"><b>OrderID :</b> '+orderamountdata[i]['ordernumber']+'</h2>\
                                                <h2 style="width: 33%;"><b>Product Total : </b><span id="displayproducttotal'+orderid+'">0.00</span></h2>\
                                              </div>\
                                              <div class="panel-body no-padding">\
                                                <div class="row m-n">\
                                                '+extrachargeshtml+'\
                                                </div>\
                                              <input type="hidden" name="ordergrossamount[]" id="ordergrossamount_'+orderid+'" class="ordergrossamount" value="'+parseFloat(ordergrossamount).toFixed(2)+'">\
                                              <input type="hidden" name="invoiceorderamount[]" id="invoiceorderamount_'+orderid+'" class="invoiceorderamount" value="'+parseFloat(ordergrossamount).toFixed(2)+'">\
                                              </div>\
                                            </div>\
                                          </div>';
                    }
                   
                    var netamount = (parseFloat(orderamountdata[i]['netamount']) + parseFloat(totalextracharges));
                    if(parseFloat(netamount) < 0){
                      netamount = 0;
                    }
                    html += '<div class="col-sm-4 pl-sm pr-sm" style="margin-bottom:10px;min-height: 200px;">\
                              <table class="table m-n orderamounttable" style="border: 5px solid #e8e8e8;">\
                                <tr>\
                                  <th>Order No.</th>\
                                  <th> : </th>\
                                  <td><a href="'+SITE_URL+'purchase-order/view-purchase-order/'+orderamountdata[i]['id']+'" target="_blank">'+orderamountdata[i]['ordernumber']+'</a></td>\
                                </tr>\
                                <tr style="border-bottom: 2px solid #E8E8E8;">\
                                  <th>Order Date</th>\
                                  <th> : </th>\
                                  <td>'+orderamountdata[i]['orderdate']+'</td>\
                                </tr>\
                                <tr>\
                                  <td>Order Amount</td>\
                                  <th> : </th>\
                                  <td class="text-right">'+parseFloat(orderamountdata[i]['orderamount']).toFixed(2)+'</td>\
                                </tr>\
                                <tr>\
                                  <td>Tax Amount</td>\
                                  <th> : </th>\
                                  <td class="text-right">'+parseFloat(orderamountdata[i]['taxamount']).toFixed(2)+'</td>\
                                </tr>\
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
                  
                  if(orderamountdata.length > 0){
                    for(var k=0; k<orderamountdata.length; k++){
                      var OrderID = orderamountdata[k]['id'];
                      
                      for(var l=0; l<orderextracharge[OrderID].length; l++){
                        
                        var extrachargesid = orderextracharge[OrderID][l];
                        $("#orderextrachargesid_"+OrderID+"_"+(l+1)).val(extrachargesid);
                        $("#orderextrachargesid_"+OrderID+"_"+(l+1)).selectpicker('refresh');
                        $("#orderextrachargesid_"+OrderID+"_"+(l+1)+" option:not(:selected)").remove();
                        $("#orderextrachargesid_"+OrderID+"_"+(l+1)).selectpicker('refresh');
                      }
                      calculateorderamount(OrderID);
                    }
                    changeextrachargesamount();
                  }
                  /****EXTRA CHARGE CHANGE EVENT****/
                  $('body').on('change', 'select.orderextrachargesid', function() { 
                    var rowid = $(this).attr("id").split('_');
                    var orderid = rowid[1];
                    var divid = rowid[2];
                    calculateextracharges(orderid,divid);
                    changechargespercentage(orderid,divid);
                    overallextracharges();
                    netamounttotal();
                  });
                  $('body').on('keyup', '.orderextrachargeamount', function() { 
                    var rowid = $(this).attr("id").split('_');
                    var orderid = rowid[1];
                    var divid = rowid[2];
                    
                    var grossamount = $("#invoiceorderamount_"+orderid).val();
                    var inputgrossamount = $("#inputgrossamount").val();
                    
                    if(orderid==0){
                      grossamount = parseFloat(inputgrossamount);
                    }

                    var chargestaxamount = chargespercent = 0;
                    var tax = $("#orderextrachargesid_"+orderid+"_"+divid+" option:selected").attr("data-tax");
                    var type = $("#orderextrachargesid_"+orderid+"_"+divid+" option:selected").attr("data-type");
                   
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
                    $("#orderextrachargestax_"+orderid+"_"+divid).val(parseFloat(chargestaxamount).toFixed(2));
                    $("#orderextrachargepercentage_"+orderid+"_"+divid).val(parseFloat(chargespercent).toFixed(2));
                    changechargespercentage(orderid,divid);
                    overallextracharges();
                    netamounttotal();
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
    $("#orderproducttable tbody").html("<tr><td colspan='9' class='text-center'>No data available in table.</td></tr>");
    $('#orderamountdiv').html("");
    $('#extracharges_div').html("");
  }
  
}

function printGoodsReceivedNotes(id){

  var uurl = SITE_URL + "goods-received-notes/printGoodsReceivedNotes";
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
    
      printdocument(html);
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

function removecharge(orderid,rowid){

  $("#countcharges_"+orderid+"_"+rowid).remove();
  overallextracharges();
  if(orderid==0){
    $(".countcharges"+orderid+" .add_charges_btn:last").show();
    if ($(".countcharges"+orderid+" .remove_charges_btn:visible").length == 1) {
        $(".countcharges"+orderid+" .remove_charges_btn:first").hide();
    }
  }
  netamounttotal();
}

function resetdata(){  
  
  $("#vendor_div").removeClass("has-error is-focused");
  $("#orderid_div").removeClass("has-error is-focused");
  $("#grnno_div").removeClass("has-error is-focused");
  $("#receiveddate_div").removeClass("has-error is-focused");
  
  if(ACTION==0){
      if(VendorId==0){
        $('#vendorid,#orderid').val('0');
        $('#orderid')
          .find('option')
          .remove()
          .end()
          .append()
          .val('0')
      ;
      
      $('#orderid').selectpicker('refresh');
      }else{
        $('#vendorid').val(VendorId);
        $('#orderid').val(OrderId);
      }
      $('#remarks').val("");
      $('#receiveddate').val(new Date().toLocaleDateString());
      $('.selectpicker').selectpicker('refresh');
      if(VendorId!=0){
        getTransactionProducts();
      }else{
        $("#orderproducttable tbody").html("<tr><td colspan='9' class='text-center'>No data available in table.</td></tr>");
        $('#orderamountdiv').html("");
        $('#extracharges_div').html("");
      }
      overallextracharges();
      netamounttotal();
  }

  $('html, body').animate({scrollTop:0},'slow');
}

function checkvalidation(btntype=''){
  
  var grnid = $('#grnid').val();
  var vendorid = $('#vendorid').val();
  var orderid = $('#orderid').val();
  var grnno = $("#grnno").val();
  var receiveddate = $('#receiveddate').val();
  
  var isvalidvendorid = isvalidorderid = isvalidproductcount = isvalidgrnno = isvalidreceiveddate = isvalidextrachargesid = isvalidextrachargeamount = isvalidduplicatecharges = 1;
  PNotify.removeAll();

  if(vendorid == 0){
    $("#vendor_div").addClass("has-error is-focused");
    new PNotify({title: "Please select vendor !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidvendorid = 0;
  }else{
    $("#vendor_div").removeClass("has-error is-focused");
  }
  if(orderid == 0 || orderid == null){
    $("#orderid_div").addClass("has-error is-focused");
    new PNotify({title: "Please select orders !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidorderid = 0;
  }else{
    $("#orderid_div").removeClass("has-error is-focused");
  }
  if(grnno == ""){
    $("#grnno_div").addClass("has-error is-focused");
    new PNotify({title: "Please enter GRN number !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidgrnno = 0;
  }else{
    $("#grnno_div").removeClass("has-error is-focused");
  }
  if(receiveddate == ""){
    $("#receiveddate_div").addClass("has-error is-focused");
    new PNotify({title: "Please select received date !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidreceiveddate = 0;
  }else{
    $("#receiveddate_div").removeClass("has-error is-focused");
  }
  if($('.countproducts').length == 0){
    isvalidproductcount==0;
    new PNotify({title: "Please add at least one product !",styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  var i=1;
  $('.countorders').each(function(){
    var orderid = $(this).attr('id');
    $('.countcharges'+orderid).each(function(){
      var elementid = $(this).attr('id').split('_');
      var divid = elementid[2];
      
      if($("#orderextrachargesid_"+orderid+"_"+divid).val() > 0 || $("#orderextrachargeamount_"+orderid+"_"+divid).val() > 0){

          if($("#orderextrachargesid_"+orderid+"_"+divid).val() == 0){
              $("#extracharges_"+orderid+"_"+divid+"_div").addClass("has-error is-focused");
              new PNotify({title: 'Please select '+divid+' extra charge !',styling: 'fontawesome',delay: '3000',type: 'error'});
              isvalidextrachargesid = 0;
          }else {
              $("#extracharges_"+orderid+"_"+divid+"_div").removeClass("has-error is-focused");
          }
          if($("#orderextrachargeamount_"+orderid+"_"+divid).val() == '' || $("#orderextrachargeamount_"+orderid+"_"+divid).val() == 0){
              $("#orderextrachargeamount_"+orderid+"_"+divid+"_div").addClass("has-error is-focused");
              var msg = (orderid==0)?"other charges":(i)+" order";
              new PNotify({title: 'Please enter '+divid+' extra charge amount on '+msg+' !',styling: 'fontawesome',delay: '3000',type: 'error'});
              isvalidextrachargeamount = 0;
          }else {
              $("#orderextrachargeamount_"+orderid+"_"+divid+"_div").removeClass("has-error is-focused");
          }
      } else{
          $("#extracharges_"+orderid+"_"+divid+"_div").removeClass("has-error is-focused");
          $("#orderextrachargeamount_"+orderid+"_"+divid+"_div").removeClass("has-error is-focused");
      }

    });
    i++;
  });
  var k=1;
  $('.countorders').each(function(){
    var orderid = $(this).attr('id');
    
    var selects_charges = $('select[name="orderextrachargesid['+orderid+'][]"]');
    var values = [];
    for(j=0;j<selects_charges.length;j++) {
        var selectscharges = selects_charges[j];
        var id = selectscharges.id.split("_");
        var divid = id[2];

        if(selectscharges.value!=0){
            if(values.indexOf(selectscharges.value)>-1) {
                $("#extracharges_"+orderid+"_"+divid+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(j+1)+' is different extra charge !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidduplicatecharges = 0;
            }
            else{ 
                values.push(selectscharges.value);
                if($("#orderextrachargesid_"+orderid+"_"+divid).val()!=0){
                $("#extracharges_"+orderid+"_"+divid+"_div").removeClass("has-error is-focused");
                }
            }
        }
    }
    k++;
  });
  if(isvalidvendorid == 1 && isvalidorderid == 1 && isvalidproductcount == 1 && isvalidgrnno == 1 && isvalidreceiveddate == 1 && isvalidextrachargesid == 1 && isvalidextrachargeamount == 1 && isvalidduplicatecharges == 1){
    
    var formData = new FormData($('#grnform')[0]);
    if(ACTION==0){

      var uurl = SITE_URL+"goods-received-notes/goods-received-notes-add";
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
            new PNotify({title: "Goods received notes successfully generated !",styling: 'fontawesome',delay: '3000',type: 'success'});
            if(btntype=='print'){
              printGoodsReceivedNotes(obj['grnid']);
              setTimeout(function() { window.location=SITE_URL+"goods-received-notes"; }, 1500);
            }else{
              if(OrderId!='' && OrderId!=null){
                setTimeout(function() { window.location=SITE_URL+"goods-received-notes"; }, 1500);
              }else{
                resetdata();
                if(VendorId!=0){
                  getTransactionProducts();
                }
                $("#grnno,#grnnumber").val(obj['grnno']);
              }
            }
          }else if(obj['error']==2){
            new PNotify({title: "GRN number already exist !",styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#grnno_div").addClass("has-error is-focused");
          }else{
            new PNotify({title: "Goods received notes not generate !",styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL + "goods-received-notes/update-goods-received-notes";
      $.ajax({
          url: uurl,
          type: 'POST',
          data: formData,
          //async: false,
          beforeSend: function () {
              $('.mask').show();
              $('#loader').show();
          },
          success: function (response) {
              var obj = JSON.parse(response);
              if (obj['error'] == 1) {
                  new PNotify({
                      title: "Goods received notes successfully updated !", styling: 'fontawesome', delay: '3000', type: 'success'
                  });
                  if (btntype == 'print') {
                    printGoodsReceivedNotes(obj['grnid']);
                  }
                  setTimeout(function () {
                      window.location = SITE_URL + "goods-received-notes";
                  }, 1500);
              }else if (obj['error'] == 2) {
                  new PNotify({
                      title: "GRN number alerady exits !", styling: 'fontawesome', delay: '3000', type: 'error'
                  });
                  $("#grnno_div").addClass("has-error is-focused");
              } else {
                  new PNotify({
                      title: "Goods received notes not updated !", styling: 'fontawesome', delay: '3000', type: 'error'
                  });
              }
          },
          error: function (xhr) {
              //alert(xhr.responseText);
          },
          complete: function () {
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