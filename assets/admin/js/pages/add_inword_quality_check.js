$(document).ready(function() {  
  
  $('#inworddate').datetimepicker({
      minuteStep: 5,
      pickerPosition: 'bottom-right',
      format: 'dd/mm/yyyy HH:ii P',
      autoclose: true,
      showMeridian: false,
  });

  /****VENDOR CHANGE EVENT****/
  $('#vendorid').on('change', function (e) {
    getVendorGRN();
  });
  /****grnid CHANGE EVENT****/
  $('#grnid').on('change', function (e) {
      getTransactionProducts();
      getproductDetailsByGRN(this.value);
  });
  
  $(".countcharges0 .add_charges_btn").hide();
  $(".countcharges0 .add_charges_btn:last").show();

  if(ACTION==1){
      getVendorGRN();
      getTransactionProducts();
      getproductDetails(InwordId);
  }

});
function getproductDetailsByGRN(grnid){
  
  var uurl = SITE_URL+"inword-quality-check/getProductdatabyGRN";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {grnid:String(grnid)},
    dataType: 'json',
    async: false,
    success: function(response){
      if(response!=""){
        var productData = response['productData'];
        var inwordid = response['inwordid'];
        
        // console.log(productData);

        if(productData!=null && productData!=""){
          if(productData.length>0){
            ACTION = 1;
            for(var i=0; i<productData.length; i++){
              
              $('.countproducts').each(function(){
                var transactionproductsid = parseInt($(this).attr('id'));
                var div_id = parseInt($(this).attr('div-id'));
                /* console.log(transactionproductsid == productData[i]['transactionproductsid'])
                console.log(productData[i]['visuallychecked']) */

                
                if(transactionproductsid == productData[i]['transactionproductsid']){
                  $('#inwordid').val(inwordid);

                  $('#qualitycheckfiletext'+div_id).val(productData[i]['filename']);
                  $('#visuallycheck'+div_id).prop("checked",productData[i]['visuallychecked']==1?true:false);
                  $('#dimensioncheck'+div_id).prop("checked",productData[i]['dimensionchecked']==1?true:false);
                  $('#visuallycheckedqty'+div_id).prop("readonly",productData[i]['visuallychecked']==1?false:true);
                  $('#visuallydefectqty'+div_id).prop("readonly",productData[i]['visuallychecked']==1?false:true);
                  $('#dimensioncheckedqty'+div_id).prop("readonly",productData[i]['dimensionchecked']==1?false:true);
                  $('#dimensiondefectqty'+div_id).prop("readonly",productData[i]['dimensionchecked']==1?false:true);
                  $('#visuallycheckedqty'+div_id).val(parseInt(productData[i]['visuallycheckedqty']));
                  $('#dimensioncheckedqty'+div_id).val(parseInt(productData[i]['dimensioncheckedqty']));
                  $('#visuallydefectqty'+div_id).val(parseInt(productData[i]['visuallydefectqty']));
                  $('#dimensiondefectqty'+div_id).val(parseInt(productData[i]['dimensiondefectqty']));
                  $('#qualitycheckfiletext'+div_id).val(productData[i]['filename']);
                  $('#oldqualitycheckfiletext'+div_id).val(productData[i]['filename']);
                  $('#mappingid'+div_id).val(productData[i]['mappingid']);

                  calculatefinalstockqty(div_id);
                }
              });

            }
          }
        }
      }
    },
    error: function(xhr) {
    //alert(xhr.responseText);
    },
  });
}

function calculatefinalstockqty(id){
  var visuallydefectqty = $('#visuallydefectqty'+id).val();
  var dimensiondefectqty = $('#dimensiondefectqty'+id).val();
  var qty = $('#qty'+id).text();
  var  finalqty = qty;
  // alert(dimensiondefectqty);
  if(!isNaN(visuallydefectqty)  && parseInt(visuallydefectqty)<=parseInt(finalqty)){
    var finalqty = parseInt(qty)-parseInt(visuallydefectqty);
  }
  if(!isNaN(dimensiondefectqty) && parseInt(dimensiondefectqty)<=parseInt(finalqty))
  {
    var finalqty = finalqty - parseInt(dimensiondefectqty);
  }
  // var finalqty = parseInt(qty)-parseInt(visuallydefectqty)-parseInt(dimensiondefectqty);
  // alert(finalqty);
  if(finalqty < 0 ){
    $('#finalqty'+id).html(qty);
  }else{
    $('#finalqty'+id).html(finalqty);
  }
}

function validimageorpdffile(obj){
  // console.log(element);
  var val = obj.val();
  var id = obj.attr('id').match(/\d+/);
  
  var filename = obj.val().replace(/C:\\fakepath\\/i, '');
  // alert(filename);
 
  switch(val.substring(val.lastIndexOf('.') + 1).toLowerCase()){
    case 'pdf': case 'gif': case 'bmp': case 'jpg': case 'jpeg': case 'png':
      $("#qualitycheckfiletext"+id).val(filename);
      $("#fileupload"+id).removeClass("has-error is-focused");
      // $("#isvalid"+element).val('1');
      break;
    default:
      $("#qualitycheckfile"+id).val("");
      $("#qualitycheckfiletext"+id).val("");
      // $("#isvalid"+element).val('0');
      // console.log( $("#fileupload"+id));
      $("#fileupload"+id).addClass("has-error is-focused");
      new PNotify({title: 'Please upload valid inwordfile '+id+' !',styling: 'fontawesome',delay: '3000',type: 'error'});
      break;
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

    if(ACTION==1){
      // $('#grnid').val(GRNId);
    }

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
    $("#inwordtable tbody").html("<tr><td colspan='9' class='text-center'>No data available in table.</td></tr>");
  }
  $('#grnid').selectpicker('refresh');
  
}

function getTransactionProducts(){
  
  var vendorid = $("#vendorid").val();
  var grnid = $("#grnid").val();
  var invoiceid = '';

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
              

              var htmldata = "";
              var gstcolumn = [];
             
              var headerdata = '<tr>\
                                  <th class="width5">Sr. No.</th>\
                                  <th>Product Name</th>\
                                  <th class="width8">Receive Qty.</th>\
                                  <th>Visually Check</th>\
                                  <th>Dimension Check</th>\
                                  <th class="width8">Final Stock Qty.</th>\
                                  <th>Upload Report</th>\
                                </tr>';
              if(grnproducts!=null && grnproducts!=""){
                if(grnproducts.length>0){
                  for(var i=0; i<grnproducts.length; i++){
                    // var id=i+1;
                    // $("visuallycheckdepdiv"+id).hide();
                   
                    //var qty = parseFloat(grnproducts[i]['quantity']) - parseFloat(grnproducts[i]['invoiceqty']);
                    var qty = parseFloat(grnproducts[i]['quantity']);
                      
                    var finalstockqty = qty;
                    var visuallycheckqty = dimensioncheckqty = 0;

                    gstcolumn.push(grnproducts[i]['igst']);
                    // var qty = parseInt(orderproducts[i]['quantity']);
                    var tax = parseFloat(grnproducts[i]['tax']);
                    var amount = parseFloat(grnproducts[i]['amount']);
                    var originalprice = parseFloat(grnproducts[i]['originalprice']);

                    var discount = parseFloat(grnproducts[i]['discount']);
                    

                    var discountamount = ((parseFloat(originalprice) * parseFloat(qty)) * parseFloat(discount) / 100);
                    
                    var totalprice = (parseFloat(amount) * parseFloat(qty));
                    var taxvalue = parseFloat(parseFloat(amount) * parseFloat(qty) * parseFloat(tax) / 100);
                    var total = parseFloat(totalprice) + parseFloat(taxvalue);
                    
                    var grnid = grnproducts[i]['grnid'];
                    if(parseFloat(grnproducts[i]['quantity']) == parseFloat(grnproducts[i]['invoiceqty'])){
                      var grnid = "";
                    }
                    htmldata += "<tr class='countproducts' div-id='"+(i+1)+"' id='"+grnproducts[i]['transactionproductsid']+"'>";
                      htmldata += "<td rowspan='2'>"+(i+1);
                      htmldata += '<input type="hidden" id="transactionproductsid'+(i+1)+'" name="transactionproductsid[]" value="'+grnproducts[i]['transactionproductsid']+'">';
                      htmldata += "<input type='hidden' id='oldqualitycheckfiletext"+(i+1)+"' class='form-control' name='oldFiletext[]' value=''>";
                      htmldata += "<input type='hidden' name='mappingid[]' id= 'mappingid"+(i+1)+"'class='form-control'  value=''>";

                      htmldata += '<input type="hidden" id="grnquantity'+grnproducts[i]['transactionproductsid']+'" value="'+parseFloat(grnproducts[i]['quantity'])+'" class="grnquantity'+grnid+'">';
                      
                      htmldata += "</td>";

                      htmldata += "<td rowspan='2'>"+ucwords(grnproducts[i]['productname'])+"</td>";
                      
                      htmldata += '<td rowspan="2" class="width8 text-left pl-n"><div class="col-md-12" id="qty'+(i+1)+'">'+qty+'<div class="form-group" id="quantity'+grnproducts[i]['transactionproductsid']+'_div"><input type="hidden" name="quantity[]" id="quantity'+(i+1)+'" class="form-control qty" value="'+qty+'" onkeypress="'+(MANAGE_DECIMAL_QTY==1?"return decimal_number_validation(event, this.value,8);":"return isNumber(event);")+'">\
                                   </td>';

                      htmldata += "<td><div class='checkbox'><input type='checkbox' class='text-left visuallycheck' name='visuallycheck"+(i+1)+"'  id='visuallycheck"+(i+1)+"'  value='"+(i+1)+"'><label for='visuallycheck"+(i+1)+"'></label></div>\
                                   <div class='visuallycheckdepdiv"+(i+1)+"'><div class='text-left form-group visuallycheckdiv"+(i+1)+"' ><div class='col-md-12'><label class='control-label' for='checkqty'>Checked Qty.</label><br><input type='text' class='text-left form-control checkedqty' name='visuallycheckedqty[]' id='visuallycheckedqty"+(i+1)+"' value='"+visuallycheckqty+"' onkeypress='return isNumber(event)' readonly></div></div>\
                                   <div class='text-left form-group visuallydefectdiv"+(i+1)+"'><div class='col-md-12'><label class='control-label' for='defectqty'>Defect Qty.</label><br><input type='text' class='text-left form-control defectedqty' name='visuallydefectqty[]' id='visuallydefectqty"+(i+1)+"' value='0' onkeypress='return isNumber(event)'  readonly></div></div>\
                                   </div></td>";
                      
                            //        <div class="checkbox">
                            //     <input id="deletecheckall" onchange="allchecked()" type="checkbox" value="all">
                            //     <label for="deletecheckall"></label>
                            // </div>
                      htmldata += "<td><div class='checkbox'><input type='checkbox' class='text-left dimensioncheck' name='dimensioncheck"+(i+1)+"' id='dimensioncheck"+(i+1)+"' value='"+(i+1)+"'><label for='dimensioncheck"+(i+1)+"'></label></div>\
                                   <div class='dimensioncheckdepdiv"+(i+1)+"'><div class='text-left form-group dimensioncheckdiv"+(i+1)+"'><div class='col-md-12'><label class='control-label' for='checkqty'>Checked Qty.</label><br><input type='text' class='text-left  form-control checkedqty' name='dimensioncheckedqty[]' id='dimensioncheckedqty"+(i+1)+"' value='"+dimensioncheckqty+"' onkeypress='return isNumber(event)' readonly></div></div>\
                                   <div class='text-left form-group dimensiondefectdiv"+(i+1)+"'><div class='col-md-12'><label class='control-label' for='defectqty'>Defect Qty.</label><br><input type='text' class='text-left  form-control defectedqty' name='dimensiondefectqty[]' id='dimensiondefectqty"+(i+1)+"' value='0' onkeypress='return isNumber(event)'  readonly></div></div>\
                                   </div></td>";

                      htmldata += "<td rowspan='2' class=' finalqty text-left' id='finalqty"+(i+1)+"'>"+finalstockqty+"</td>";

                      htmldata +="<td>\
                                  <div class='form-group col-md-12 pr-n' id='Filetext"+(i+1)+"_div'>\
                                  <div class='input-group ' id='fileupload"+(i+1)+"'>\
                                  <span class='input-group-btn' style='padding: 0 0px 0px 0px;'>\
                                  <span class='btn btn-primary btn-raised btn-file'>\
                                  <i class='fa fa-upload'></i>\
                                  <input type='file' name='qualitycheckfile"+(i+1)+"' class='qualitycheckfile' id='qualitycheckfile"+(i+1)+"' accept='.docx,.pdf,.bmp,.bm,.gif,.ico,.jfif,.jfif-tbnl,.jpe,.jpeg,.jpg,.pbm,.png,.svf,.tif,.tiff,.wbmp,.x-png' onchange='validimageorpdffile($(this))'>\
                                  </span>\
                                  </span>\
                                  <input type='text' readonly='' style='margin-top: 1px!important;' id='qualitycheckfiletext"+(i+1)+"' class='form-control' name='Filetext[]' value=''>\
                                  </div>\
                                  </div>\
                                  </td>";
                    htmldata += "</tr>";

                    htmldata += "<tr>";
                    
                    htmldata += "</tr>";
                  }
                }else{
                  $("#inwordtable tbody").html("<tr><td colspan='9' class='text-center'>No data available in table.</td></tr>");
                }
              }
              $("#inwordtable thead").html(headerdata);
              $("#inwordtable tbody").html(htmldata);
              // console.log($(".visuallycheckdepdiv"+(i+1)+""));

              // $('.visuallycheck').each(function(){
              //   var visdivid =$(this).attr("id").match(/\d+/g);
                // $('.visuallycheckdepdiv'+visdivid).add();
              // });

              $('.visuallycheck').click(function(){
                // alert();
                var id= $(this).attr("id").match(/\d+/g);
                // console.log($('#visuallycheckdepdiv'+id));
                
                if($('#visuallycheck'+id).prop('checked')==true){
                  $('#visuallycheckedqty'+id).prop("readonly",false);
                  $('#visuallydefectqty'+id).prop("readonly",false);
                }
                else{
                  $('#visuallycheckedqty'+id).prop("readonly",true);
                  $('#visuallydefectqty'+id).prop("readonly",true);
                }
              });

              $('.visuallycheck').each(function(){
                var id= $(this).attr("id").match(/\d+/g);
                var visuallydefectqty = $('#visuallydefectqty'+id).val();
                $('#visuallydefectqty'+id).on('change',function(){
                  // alert();
                });
              })
              
              // $('.dimensioncheck').each(function(){
              //   var dimdivid =$(this).attr("id").match(/\d+/g);
              //   $('.dimensioncheckdepdiv'+dimdivid).hide();
              // });

              $('.dimensioncheck').click(function(){
                // alert();
                var id= $(this).attr("id").match(/\d+/g);
                // console.log($('#visuallycheckdepdiv'+id));
                
                if($('#dimensioncheck'+id).prop('checked')==true){
                  $('#dimensioncheckedqty'+id).prop("readonly",false);
                  $('#dimensiondefectqty'+id).prop("readonly",false);
                }
                else{
                  $('#dimensioncheckedqty'+id).prop("readonly",true);
                  $('#dimensiondefectqty'+id).prop("readonly",true);
                }
              });
              // $(".qty").TouchSpin(touchspinoptions);
            //   $('#editordernumber').change(function () {
            //     if($(this).is(':checked')){
            //     $("#orderid").prop("readonly",false);
            //     }else{
            //     $("#orderid").val($("#ordernumber").val()).prop("readonly",true);
            //     }
            // });
              
              
             
             
        }
      },
      error: function(xhr) {
      //alert(xhr.responseText);
      },
    });
  }else{
    $("#inwordtable tbody").html("<tr><td colspan='9' class='text-center'>No data available in table.</td></tr>");
    $('#orderamountdiv').html("");
    $('#extracharges_div').html("");
    $('#billingaddress').val('');
    $('#shippingaddress').val('');
  }
  
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

function resetdata(){  
  
  $("#vendor_div").removeClass("has-error is-focused");
  $("#grnid_div").removeClass("has-error is-focused");
  
  
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
        $("#inwordtable tbody").html("<tr><td colspan='9' class='text-center'>No data available in table.</td></tr>");
        $('#orderamountdiv').html("");
        $('#extracharges_div').html("");
      }
     
  }

  $('html, body').animate({scrollTop:0},'slow');
}

$('body').on('keyup', '.checkedqty',function (){
  
  
    var id = $(this).attr('id').match(/\d+/g);
    var vichecked = $('#visuallycheckedqty'+id).val();
    var dichecked = $('#dimensioncheckedqty'+id).val();
    var quantity = $('#quantity'+id).val();
    
    if( parseInt(vichecked) > parseInt(quantity)){
      $('#visuallycheckedqty'+id).val('');
      new PNotify({title: "Visually checked quantity "+id+" can not be greater than received quantity "+id+" !",styling: 'fontawesome',delay: '3000',type: 'error'});
    }
    if(parseInt(dichecked) > parseInt(quantity)){
      $('#dimensioncheckedqty'+id).val('');
      // $('#finalqty'+id).html(0);
      new PNotify({title: "Dimension checked quantity "+id+" can not be greater than received quantity "+id+" !",styling: 'fontawesome',delay: '3000',type: 'error'});
    }
    calculatefinalstockqty(id);
});

$('body').on('keyup', '.defectedqty',function (){
  
  
  var id = $(this).attr('id').match(/\d+/g);

  var vichecked = $('#visuallycheckedqty'+id).val();
  var dichecked = $('#dimensioncheckedqty'+id).val();
  var videfected = $('#visuallydefectqty'+id).val();
  var didefected = $('#dimensiondefectqty'+id).val();
  var quantity = $('#quantity'+id).val(); 
  if(vichecked === ''){
    vichecked = 0;
  }
  if(dichecked === ''){
    dichecked = 0;
  }
  // console.log(vichecked);
  if( parseInt(videfected) > parseInt(vichecked)){
    $('#visuallydefectqty'+id).val('');
    new PNotify({title: "Visually defected quantity "+id+" can not be greater than visually checked quantity "+id+" !",styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  if(parseInt(didefected) > parseInt(dichecked)){
    $('#dimensiondefectqty'+id).val('');
    // $('#finalqty'+id).html(0);
    new PNotify({title: "Dimension defected quantity "+id+" can not be greater than visually checked quantity "+id+" !",styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  if(parseInt(videfected) > parseInt(quantity)){
    $('#visuallydefectqty'+id).val('');
    new PNotify({title: "Visually defected quantity "+id+" can not be greater than received quantity "+id+" !",styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  if(parseInt(didefected) > parseInt(quantity)){
    $('#dimensiondefectqty'+id).val('');
    new PNotify({title: "Dimension defected quantity "+id+" can not be greater than received quantity "+id+" !",styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  if(parseInt(parseInt(videfected)+parseInt(didefected))>parseInt(quantity)){
    $(this).val('');
    new PNotify({title: "Total of Visually and Dimension Defected Qty."+id+" can't greater than Received Qty."+id+" !",styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  calculatefinalstockqty(id);
});

function getproductDetails(InwordId){
  // alert("getingpd");
  var uurl = SITE_URL+"inword-quality-check/getProductdatabyinwordID";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {inwordid:String(InwordId)},
    dataType: 'json',
    async: false,
    success: function(response){
      if(response!=""){
        var productData = response;
        // var inwordid = response['inwordid'];
        // console.log(productData);

        if(productData!=null && productData!=""){
          if(productData.length>0){
            for(var i=0; i<productData.length; i++){
              
              // console.log(productData.filter(p => p.transactionproductsid == productData[i]['transactionproductsid']))
              $('.countproducts').each(function(){
                var transactionproductsid = parseInt($(this).attr('id'));
                var div_id = parseInt($(this).attr('div-id'));

                // console.log(transactionproductsid == productData[i]['transactionproductsid'])

                if(transactionproductsid == productData[i]['transactionproductsid']){
      
                  $('#qualitycheckfiletext'+div_id).val(productData[i]['filename']);
                  $('#visuallycheck'+div_id).prop("checked",productData[i]['visuallychecked']==1?true:false);
                  $('#dimensioncheck'+div_id).prop("checked",productData[i]['dimensionchecked']==1?true:false);
                  $('#visuallycheckedqty'+div_id).prop("readonly",productData[i]['visuallychecked']==1?false:true);
                  $('#visuallydefectqty'+div_id).prop("readonly",productData[i]['visuallychecked']==1?false:true);
                  $('#dimensioncheckedqty'+div_id).prop("readonly",productData[i]['dimensionchecked']==1?false:true);
                  $('#dimensiondefectqty'+div_id).prop("readonly",productData[i]['dimensionchecked']==1?false:true);
                  $('#visuallycheckedqty'+div_id).val(parseInt(productData[i]['visuallycheckedqty']));
                  $('#dimensioncheckedqty'+div_id).val(parseInt(productData[i]['dimensioncheckedqty']));
                  $('#visuallydefectqty'+div_id).val(parseInt(productData[i]['visuallydefectqty']));
                  $('#dimensiondefectqty'+div_id).val(parseInt(productData[i]['dimensiondefectqty']));
                  $('#qualitycheckfiletext'+div_id).val(productData[i]['filename']);
                  $('#oldqualitycheckfiletext'+div_id).val(productData[i]['filename']);
                  $('#mappingid'+div_id).val(productData[i]['mappingid']);
                  calculatefinalstockqty(div_id);
                }
              });

            }
          }else{
            $("#inwordtable tbody").html("<tr><td colspan='9' class='text-center'>No data available in table.</td></tr>");
          }
        }
       
        $('.visuallycheck').click(function(){
          // alert();
          var id= $(this).attr("id").match(/\d+/g);
          // console.log($('#visuallycheckdepdiv'+id));
          
          if($('#visuallycheck'+id).prop('checked')==true){
            $('#visuallycheckedqty'+id).prop("readonly",false);
            $('#visuallydefectqty'+id).prop("readonly",false);
          }
          else{
            $('#visuallycheckedqty'+id).prop("readonly",true);
            $('#visuallydefectqty'+id).prop("readonly",true);
          }
        });

        $('.visuallycheck').each(function(){
          var id= $(this).attr("id").match(/\d+/g);
          var visuallydefectqty = $('#visuallydefectqty'+id).val();
          $('#visuallydefectqty'+id).on('change',function(){
            // alert();
          });
        })
        
        $('.dimensioncheck').click(function(){
          // alert();
          var id= $(this).attr("id").match(/\d+/g);
          // console.log($('#visuallycheckdepdiv'+id));
          
          if($('#dimensioncheck'+id).prop('checked')==true){
            $('#dimensioncheckedqty'+id).prop("readonly",false);
            $('#dimensiondefectqty'+id).prop("readonly",false);
          }
          else{
            $('#dimensioncheckedqty'+id).prop("readonly",true);
            $('#dimensiondefectqty'+id).prop("readonly",true);
          }
        });
      }
    },
    error: function(xhr) {
    //alert(xhr.responseText);
    },
  });
}
function checkvalidation(btntype=''){
  
  
  var vendorid = $('#vendorid').val();
  var grnid = $('#grnid').val();
  var isvalidfile = isvalidvendorid = isvaliddimensiondefect =isvalidvisuallydefect =isvalidvisuallycheck = isvaliddimensioncheck = isvalidgrnid = isvalidproducts = 1;
  PNotify.removeAll();

  if(vendorid == 0){
    $("#vendor_div").addClass("has-error is-focused");
    new PNotify({title: "Please select vendor !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidvendorid = 0;
  }else{
    $("#vendor_div").removeClass("has-error is-focused");
  }
  if(grnid == 0 || grnid == null){
    $("#grnid_div").addClass("has-error is-focused");
    new PNotify({title: "Please select orders !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidgrnid = 0;
  }else{
    $("#grnid_div").removeClass("has-error is-focused");
  }
 
  /* $('.visuallycheck').each(function(){
   
    var id = $(this).attr('id').match(/\d+/g);
    var checked = $('#visuallycheckedqty'+id).val();
    var defected = $('#visuallydefectqty'+id).val();

    
    if($(this).prop('checked')==true){
      if(checked == ''){
        $('.visuallycheckdiv'+id).addClass('has-error is-focused');
        new PNotify({title: "Please enter visually check quantity "+id+" !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidvisuallycheck == 0;
      }else{
        $('.visuallycheckdiv'+id).removeClass('has-error is-focused');
        // isvalidvisuallycheck == 1;
      }
      // if(defected == ''){
      //   $('.visuallydefectdiv'+id).addClass('has-error is-focused');
      //   new PNotify({title: "Please enter visually defect quantity "+id+" !",styling: 'fontawesome',delay: '3000',type: 'error'});
      //   isvalidvisuallydefect == 0;
      // }else{
      //   $('.visuallydefectdiv'+id).removeClass('has-error is-focused');
      //   isvalidvisuallydefect==1;
      // }
    }
     
  }); */
  

  /* $('.dimensioncheck').each(function(){
   
    var id = $(this).attr('id').match(/\d+/g);
    var checked = $('#dimensioncheckedqty'+id).val();
    var defected = $('#dimensiondefectqty'+id).val();
    var file = $('#qualitycheckfiletext'+id).val();

    if($(this).prop('checked')==true || $('#visuallycheck'+id).prop('checked')==true){
      if(file == ''){
        $('#Filetext'+id+'_div').addClass('has-error is-focused');
        new PNotify({title: "Please upload report "+id+" !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidfile = 0;
      }else{
        $('#Filetext'+id+'_div').removeClass('has-error is-focused');
      }
    }
    
    if($(this).prop('checked')==true){
      if(checked == ''){
        $('.dimensioncheckdiv'+id).addClass('has-error is-focused');
        new PNotify({title: "Please enter dimension check quantity "+id+" !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvaliddimensioncheck == 0;
      }else{
        $('.dimensioncheckdiv'+id).removeClass('has-error is-focused');
        // isvaliddimensioncheck == 1;
      }
      // if(defected == ''){
      //   $('.dimensiondefectdiv'+id).addClass('has-error is-focused');
      //   new PNotify({title: "Please enter dimension defect quantity "+id+" !",styling: 'fontawesome',delay: '3000',type: 'error'});
      //   isvaliddimensiondefect == 0;
      // }else{
      //   $('.dimensiondefectdiv'+id).removeClass('has-error is-focused');
      //   isvaliddimensiondefect==1;
      // }
    }
  }); */

  isvalidproducts = 0;
  $('.countproducts').each(function(){
    var id = $(this).attr('div-id');
    
    if($('#visuallycheck'+id).prop('checked')==true || $('#dimensioncheck'+id).prop('checked')==true){
      isvalidproducts = 1;
    }
  });
  if(isvalidproducts == 0){
    new PNotify({title: "Please check any one product !",styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  

if(isvalidvendorid == 1 && isvalidfile == 1 && isvaliddimensiondefect ==1 && isvalidvisuallydefect == 1 && isvalidvisuallycheck == 1 && isvaliddimensioncheck == 1 && isvalidgrnid == 1 && isvalidproducts == 1){
    
    var formData = new FormData($('#inwordqcform')[0]);
    if(grnid !=''){
      // alert();
      if(ACTION==1){
        var uurl = SITE_URL+"inword-quality-check/inword-quality-check-edit";
      }else{
        var uurl = SITE_URL+"inword-quality-check/inword-quality-check-add";
      }
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
          // console.log(obj);
          if(obj==1){
            new PNotify({title: "Inword Q.C. successfully added !",styling: 'fontawesome',delay: '3000',type: 'success'});
            setTimeout(function() { window.location = SITE_URL+"inword-quality-check"; }, 1500);
          }else if(obj==2){
            new PNotify({title: "Invoice Q.C. successfully updated !",styling: 'fontawesome',delay: '3000',type: 'success'});
            setTimeout(function() { window.location = SITE_URL+"inword-quality-check"; }, 1500);
          }else if(obj==3){
            new PNotify({title: "File not uploaded !",styling: 'fontawesome',delay: '3000',type: 'error'});
            // setTimeout(function() { window.location = SITE_URL+"inword-quality-check"; }, 1500);
          }else if(obj==4){
            new PNotify({title: "Invalid File!",styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(obj==5){
            new PNotify({title: "Inword Q.C. not updated !",styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(obj==0){
            new PNotify({title: "Inword Q.C. not added !",styling: 'fontawesome',delay: '3000',type: 'error'});
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