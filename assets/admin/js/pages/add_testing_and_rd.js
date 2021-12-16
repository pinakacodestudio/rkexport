$(document).ready(function() {  
  
  $('#testdate').datepicker({
    todayHighlight: true,
    format: 'dd/mm/yyyy',
    todayBtn:"linked",
  });
  
  $('.qualitycheckfile').change(function(){
    validfile($(this),this);
  });
  
  
  $('#processid').on('change', function (e) {
    getbatchno();
  });
  $('#batchid').on('change', function (e) {
    getProducts();
    getproductDetailsByBatchId();

  });
  
  $(".countcharges0 .add_charges_btn").hide();
  $(".countcharges0 .add_charges_btn:last").show();

  if(ACTION==1){
    getbatchno();
    // alert(TestingId);
    getProducts();
    getproductDetails(TestingId);
  }

});

function calculatependingqty(id){
      var qty =$('#quantity'+id).val();
      // alert(id);
      var mechanicledefectqty = $('#mechanicledefectqty'+id).val();
      var electricallydefectqty = $('#electricallydefectqty'+id).val();
      var visuallydefectqty = $('#visuallydefectqty'+id).val();
      var pendingqty = qty;
      // alert(mechanicledefectqty);

      if(!isNaN(mechanicledefectqty)  && parseInt(mechanicledefectqty)<=parseInt(pendingqty)){
        var pendingqty = parseInt(pendingqty)-parseInt(mechanicledefectqty);
      }
     
      if(!isNaN(visuallydefectqty)  && parseInt(visuallydefectqty)<=parseInt(pendingqty)){
        var pendingqty = parseInt(pendingqty)-parseInt(visuallydefectqty);
      }
      
      if(!isNaN(electricallydefectqty)  && parseInt(electricallydefectqty)<=parseInt(pendingqty)){
        var pendingqty = parseInt(pendingqty)-parseInt(electricallydefectqty);
      }
      
      if(parseInt(pendingqty) < 0 ){
        $('#pendingqty'+id).html(qty);
      }else{
        $('#pendingqty'+id).html(pendingqty);
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
      $("#Filetext"+id).val(filename);
      $("#fileupload"+id).removeClass("has-error is-focused");
      // $("#isvalid"+element).val('1');
      break;
    default:
      $("#testingfile"+id).val("");
      $("#Filetext"+id).val("");
      // $("#isvalid"+element).val('0');
      // console.log( $("#fileupload"+id));
      $("#fileupload"+id).addClass("has-error is-focused");
      new PNotify({title: 'Please upload valid testingfile '+id+' !',styling: 'fontawesome',delay: '3000',type: 'error'});
      break;
  }
}

function getbatchno(){
  
  var processid = $("#processid").val();

    $('#batchid')
        .find('option')
        .remove()
        .end()
        .append('<option value="0">Select Batch No.</option>')
        .val('0')
    ;
  
  if(processid!='' && processid!=null){
    var uurl = SITE_URL+"testing-and-rd/getBatchNoOfINProductProcess";
    
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {processid:processid},
      dataType: 'json',
      async: false,
      success: function(response){
        if(response!=""){

          for(var i = 0; i < response.length; i++) {

              $('#batchid').append($('<option>', { 
                value: response[i]['id'],
                text : response[i]['batchno'],
            }));

            if(BatchNo!=0){
              $('#batchid').val(BatchNo);
            }
          }
        }
      },
      error: function(xhr) {
      //alert(xhr.responseText);
      },
    });
  }

  $('#batchid').selectpicker('refresh');
  
}

function getProducts(){
  
  var batchno = $("#batchid").val();
  
  if(batchno!='' && batchno!=null){
    var uurl = SITE_URL+"testing-and-rd/getProductByBatchno";
    
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {batchno:batchno},
      dataType: 'json',
      async: false,
      success: function(response){
        if(response!=""){
              var productdata = response['productdata'];
              var htmldata = discolumn = "";
              var headerdata = '<tr>\
                                  <th class="width5">Sr. No.</th>\
                                  <th>Output Product Name</th>\
                                  <th>Qty.</th>\
                                  <th>	Mechanicle Checked</th>\
                                  <th>Electrically Checked</th>\
                                  <th>Visually Checked</th>\
                                  <th>Approve Qty.</th>\
                                  <th>Upload Report</th>\
                                  </tr>';

              if(productdata!=null && productdata!=""){
                // alert();
                if(productdata.length>0){
                  for(var i=0; i<productdata.length; i++){
                    var pendingqty = productdata[i]['quantity'];
                    htmldata += "<tr class='countproducts' div-id='"+(i+1)+"' id='"+productdata[i]['id']+"'>";
                      htmldata += "<td rowspan='2'>"+(i+1);
                      htmldata += '<input type="hidden" name="transactionproductsid[]" value="'+productdata[i]['id']+'">';
                      htmldata += "<input type='hidden' id='oldtestingfiletext"+(i+1)+"' class='form-control' name='oldFiletext[]' value=''>";
                      htmldata += "<input type='hidden' name='mappingid[]' id= 'mappingid"+(i+1)+"'class='form-control'  value=''>";
                      
                      
                      htmldata += "</td>";

                      htmldata += "<td rowspan='2'>"+ucwords(productdata[i]['productname'])+"</td>";
                      
                      htmldata += '<td rowspan="2" class="width8 text-left pl-n"><div class="col-md-12">'+parseInt(productdata[i]['quantity'])+'<input type="hidden" name="quantity[]" id="quantity'+(i+1)+'" class="form-control qty" value="'+parseInt(productdata[i]['quantity'])+'" onkeypress="'+(MANAGE_DECIMAL_QTY==1?"return decimal_number_validation(event, this.value,8);":"return isNumber(event);")+'"></td>';
                      
                      htmldata += "<td><div class='checkbox'><input type='checkbox' class='text-left mechaniclecheck' name='mechaniclecheck"+(i+1)+"'  id='mechaniclecheck"+(i+1)+"' value=''><label for='mechaniclecheck"+(i+1)+"'></label></div>\
                                   <div class='text-left form-group mechanicledefectdiv"+(i+1)+"'><div class='col-md-12'><label class='control-label' for='defectqty'>Defect Qty.</label><br><input type='text' class='text-left form-control defectedqty' name='mechanicledefectqty[]' id='mechanicledefectqty"+(i+1)+"' value='0' onkeypress='return isNumber(event)' readonly></div></div>\
                                   </td>";
                       
                      htmldata += "<td><div class='checkbox'><input type='checkbox' class='text-left electricallycheck' name='electricallycheck"+(i+1)+"'  id='electricallycheck"+(i+1)+"' value=''><label for='electricallycheck"+(i+1)+"'></label></div>\
                                   <div class='text-left form-group electricallydefectdiv"+(i+1)+"'><div class='col-md-12'><label class='control-label' for='defectqty'>Defect Qty.</label><br><input type='text' class='text-left form-control defectedqty' name='electricallydefectqty[]' id='electricallydefectqty"+(i+1)+"' value='0' onkeypress='return isNumber(event)' readonly></div></div>\
                                   </td>";

                      htmldata += "<td><div class='checkbox'><input type='checkbox' class='text-left visuallycheck' name='visuallycheck"+(i+1)+"'  id='visuallycheck"+(i+1)+"' value=''><label for='visuallycheck"+(i+1)+"'></label></div>\
                                   <div class='text-left form-group visuallydefectdiv"+(i+1)+"'><div class='col-md-12'><label class='control-label' for='defectqty'>Defect Qty.</label><br><input type='text' class='text-left form-control defectedqty' name='visuallydefectqty[]' id='visuallydefectqty"+(i+1)+"' value='0' onkeypress='return isNumber(event)' readonly></div></div>\
                                   </td>";
                      htmldata +="<td id='pendingqty"+(i+1)+"'>"+parseInt(pendingqty)+"</td>";
                      htmldata +="<td class='pr-n'>\
                                  <div class='form-group col-md-12 pr-n' id='Filetext"+(i+1)+"_div'>\
                                  <div class='input-group ' id='fileupload1'>\
                                  <span class='input-group-btn' style='padding: 0 0px 0px 0px;'>\
                                  <span class='btn btn-primary btn-raised btn-file'>\
                                  <i class='fa fa-upload'></i>\
                                  <input type='file' name='testingfile"+(i+1)+"' class='testingfile' id='testingfile"+(i+1)+"' accept='.docx,.pdf,.bmp,.bm,.gif,.ico,.jfif,.jfif-tbnl,.jpe,.jpeg,.jpg,.pbm,.png,.svf,.tif,.tiff,.wbmp,.x-png' onchange='validimageorpdffile($(this))'>\
                                  </span>\
                                  </span>\
                                  <input type='text' style='margin-top: 1px!important;' readonly='' id='Filetext"+(i+1)+"' class='form-control ' name='Filetext[]' value=''>\
                                  </div>\
                                  </div>\
                                  </td>";
                    htmldata += "</tr>";

                    htmldata += "<tr>";
                    
                    htmldata += "</tr>";

                    $("#testingproducttable thead").html(headerdata);
                    $("#testingproducttable tbody").html(htmldata);
                  }
                }else{
                  $("#testingproducttable tbody").html("<tr><td colspan='9' class='text-center'>No data available in table.</td></tr>");
                }
              }else{
                $("#testingproducttable tbody").html("<tr><td colspan='9' class='text-center'>No data available in table.</td></tr>");
              }
              // console.log(productdata);
              
              // console.log($(".visuallycheckdepdiv"+(i+1)+""));

              // $('.visuallycheck').each(function(){
              //   var visdivid =$(this).attr("id").match(/\d+/g);
                // $('.visuallycheckdepdiv'+visdivid).add();
              // });

              $('.mechaniclecheck').click(function(){
                // alert();
                var id= $(this).attr("id").match(/\d+/g);
                // console.log($('#visuallycheckdepdiv'+id));
                
                if($('#mechaniclecheck'+id).prop('checked')==true){
                  $('#mechanicledefectqty'+id).prop("readonly",false);
                }
                else{
                  $('#mechanicledefectqty'+id).prop("readonly",true);
                }
              });

              // $('.dimensioncheck').each(function(){
              //   var dimdivid =$(this).attr("id").match(/\d+/g);
              //   $('.dimensioncheckdepdiv'+dimdivid).hide();
              // });

              $('.electricallycheck').click(function(){
                // alert();
                var id= $(this).attr("id").match(/\d+/g);
                // console.log($('#visuallycheckdepdiv'+id));
                
                if($('#electricallycheck'+id).prop('checked')==true){
                  $('#electricallydefectqty'+id).prop("readonly",false);
                }
                else{
                  $('#electricallydefectqty'+id).prop("readonly",true);
                }
              });

              $('.visuallycheck').click(function(){
                // alert();
                var id= $(this).attr("id").match(/\d+/g);
                // console.log($('#visuallycheckdepdiv'+id));
                
                if($('#visuallycheck'+id).prop('checked')==true){
                  $('#visuallydefectqty'+id).prop("readonly",false);
                }
                else{
                  $('#visuallydefectqty'+id).prop("readonly",true);
                }
              });
        }
      },
      error: function(xhr) {
      //alert(xhr.responseText);
      },
    });
  }else{
    $("#testingproducttable tbody").html("<tr><td colspan='9' class='text-center'>No data available in table.</td></tr>");
    $('#orderamountdiv').html("");
    $('#extracharges_div').html("");
    $('#billingaddress').val('');
    $('#shippingaddress').val('');
  }
  
}

function getproductDetails(TestingId){
  // alert("getingpd");
  var uurl = SITE_URL+"testing-and-rd/getProductdatabytestingID";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {TestingId:String(TestingId)},
    dataType: 'json',
    async: false,
    success: function(response){
      if(response!=""){
        var productData = response;
        // console.log(productData);

        if(productData!=null && productData!=""){
          if(productData.length>0){
            for(var i=0; i<productData.length; i++){

              // updateData
              $('.countproducts').each(function(){
                var transactionproductsid = parseInt($(this).attr('id'));
                var div_id = parseInt($(this).attr('div-id'));

                if(transactionproductsid == productData[i]['transactionproductsid']){

                  $('#mechaniclecheck'+div_id).prop('checked',productData[i]['mechaniclecheck']==1?true:false);
                  $('#electricallycheck'+div_id).prop('checked',productData[i]['electricallycheck']==1?true:false);
                  $('#visuallycheck'+div_id).prop('checked',productData[i]['visuallychecked']==1?true:false);

                  $('#mechanicledefectqty'+div_id).prop("readonly",false);
                  $('#electricallydefectqty'+div_id).prop("readonly",false);
                  $('#visuallydefectqty'+div_id).prop("readonly",false);

                  $('#mechanicledefectqty'+div_id).val(parseInt(productData[i]['mechanicledefectqty']));
                  $('#electricallydefectqty'+div_id).val(parseInt(productData[i]['electricallydefectqty']));
                  $('#visuallydefectqty'+div_id).val(parseInt(productData[i]['visuallydefectqty']));
                  $('#Filetext'+div_id).val(productData[i]['filename']);
                  $('#oldtestingfiletext'+div_id).val(productData[i]['filename']);
                  $('#mappingid'+div_id).val(productData[i]['mappingid']);
                  calculatependingqty(div_id);
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
function getproductDetailsByBatchId(){
  
  var batchno = $("#batchid").val();

  var uurl = SITE_URL+"testing-and-rd/getproductDetailsByBatchId";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {batchno:String(batchno)},
    dataType: 'json',
    async: false,
    success: function(response){
      if(response!=""){
        var productData = response['productData'];
        var TestingId = response['TestingId'];
        // var productData = response['productData'];
        // console.log(productData);

        if(productData!=null && productData!=""){
          if(productData.length>0){
            ACTION = 1;
            $('#testingid').val(TestingId);
            for(var i=0; i<productData.length; i++){

              // updateData
              $('.countproducts').each(function(){
                var transactionproductsid = parseInt($(this).attr('id'));
                var div_id = parseInt($(this).attr('div-id'));

                if(transactionproductsid == productData[i]['transactionproductsid']){

                  $('#mechaniclecheck'+div_id).prop('checked',productData[i]['mechaniclecheck']==1?true:false);
                  $('#electricallycheck'+div_id).prop('checked',productData[i]['electricallycheck']==1?true:false);
                  $('#visuallycheck'+div_id).prop('checked',productData[i]['visuallychecked']==1?true:false);

                  $('#mechanicledefectqty'+div_id).prop("readonly",false);
                  $('#electricallydefectqty'+div_id).prop("readonly",false);
                  $('#visuallydefectqty'+div_id).prop("readonly",false);

                  $('#mechanicledefectqty'+div_id).val(parseInt(productData[i]['mechanicledefectqty']));
                  $('#electricallydefectqty'+div_id).val(parseInt(productData[i]['electricallydefectqty']));
                  $('#visuallydefectqty'+div_id).val(parseInt(productData[i]['visuallydefectqty']));
                  $('#Filetext'+div_id).val(productData[i]['filename']);
                  $('#oldtestingfiletext'+div_id).val(productData[i]['filename']);
                  $('#mappingid'+div_id).val(productData[i]['mappingid']);
                  calculatependingqty(div_id);
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
  
  $("#process_div").removeClass("has-error is-focused");
  $("#batchid_div").removeClass("has-error is-focused");
  
  
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
        getProducts();
      }else{
        $("#testingproducttable tbody").html("<tr><td colspan='9' class='text-center'>No data available in table.</td></tr>");
        $('#orderamountdiv').html("");
        $('#extracharges_div').html("");
      }
      // overallextracharges();
      // netamounttotal();
  }

  $('html, body').animate({scrollTop:0},'slow');
}

$('body').on('keyup', '.defectedqty',function (){
  
    var id = $(this).attr('id').match(/\d+/g);
    // alert(id);
    var mechanicledefectqty = ($('#mechanicledefectqty'+id).val()!=''?$('#mechanicledefectqty'+id).val():0);
    var electricallydefectqty = ($('#electricallydefectqty'+id).val()!=''?$('#electricallydefectqty'+id).val():0);
    var visuallydefectqty = ($('#visuallydefectqty'+id).val()!=''?$('#visuallydefectqty'+id).val():0);
    var quantity = $('#quantity'+id).val();
    var pendingqty = $('#pendingqty'+id).text();
    // alert(pendingqty)
    if((parseInt(quantity)-(parseInt(mechanicledefectqty)+parseInt(electricallydefectqty)+parseInt(visuallydefectqty))) < 0){
      // $('#mechanicledefectqty'+id).val('');
      $(this).val('');
      new PNotify({title: "Total of defect qty."+id+" can't greater than qty."+id+" !",styling: 'fontawesome',delay: '3000',type: 'error'});
    }
    calculatependingqty(id);
});

// $('body').on('keyup', '.defectedqty',function (){
  
  
//   var id = $(this).attr('id').match(/\d+/g);

//   var vichecked = $('#visuallycheckedqty'+id).val();
//   var dichecked = $('#dimensioncheckedqty'+id).val();
//   var videfected = $('#visuallydefectqty'+id).val();
//   var didefected = $('#dimensiondefectqty'+id).val();
//   if(vichecked === '')
//   {
//     vichecked = 0;
//   }
//   if(dichecked === '')
//   {
//     dichecked = 0;
//   }
//   // console.log(vichecked);
//   if( parseInt(videfected) > parseInt(vichecked)){
//     $('#visuallydefectqty'+id).val('');
//     new PNotify({title: "Visually defected quantity "+id+" can not be greater than visually checked quantity "+id+" !",styling: 'fontawesome',delay: '3000',type: 'error'});
//   }
//   if(parseInt(didefected) > parseInt(dichecked)){
//     $('#dimensiondefectqty'+id).val('');
//     new PNotify({title: "Dimension defected quantity "+id+" can not be greater than visually checked quantity "+id+" !",styling: 'fontawesome',delay: '3000',type: 'error'});
//   }

// });

function checkvalidation(){
  
  var processid = $('#processid').val();
  var batchid =$('#batchid').val();
  var isvalidfile = 1; 
  var isvalidprocessid =  isvalidbatchid = isvalidmechanicledefect = isvalidelectricaldefect = isvalidvisuallydefect = 1;
  PNotify.removeAll();

  if(processid == 0){
    $("#process_div").addClass("has-error is-focused");
    new PNotify({title: "Please select process !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidprocessid = 0;
  }else{
    $("#process_div").removeClass("has-error is-focused");
    isvalidprocessid =1;
  }

  if(batchid == 0){
    $("#batchid_div").addClass("has-error is-focused");
    new PNotify({title: "Please select batch no !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidbatchid = 0;
  }else{
    $("#batchid_div").removeClass("has-error is-focused");
    isvalidbatchid =1;
  }
  
  $('.mechaniclecheck').each(function(){
   
    var id = $(this).attr('id').match(/\d+/g);
    var medefected = $('#mechanicledefectqty'+id).val();
    var file = $('#Filetext'+id).val();

    if($(this).prop('checked')==true || $('#electricallycheck'+id).prop('checked')==true || $('#visuallycheck'+id).prop('checked')==true){
      if(file == ''){
        $('#Filetext'+id+'_div').addClass('has-error is-focused');
        new PNotify({title: "Please upload report "+id+" !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidfile = 0;
      }else{
        $('#Filetext'+id+'_div').removeClass('has-error is-focused');
        //isvalidfile = 1;
      }
    }
    /* if($(".mechaniclecheck").prop('checked')==true){
      if(medefected == ''){
        $('.mechanicledefectdiv'+id).addClass('has-error is-focused');
        new PNotify({title: "Please enter  mechanicle defect quantity "+id+" !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmechanicledefect = 0;
      }else{
        $('.mechanicledefectdiv'+id).removeClass('has-error is-focused');
        isvalidmechanicledefect =1;
      }
    } */
  });

  /* $('.electricallycheck').each(function(){
   
    var id = $(this).attr('id').match(/\d+/g);
    var eldefected = $('#electricallydefectqty'+id).val();
    
    if($(".electricallycheck").prop('checked')==true){
      if(eldefected == ''){
        $('.electricallydefectdiv'+id).addClass('has-error is-focused');
        new PNotify({title: "Please enter  electrically check quantity "+id+" !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidelectricaldefect = 0;
      }else{
        $('.electricallydefectdiv'+id).removeClass('has-error is-focused');
        isvalidelectricaldefect=1;
      }
    }
  }); */

  /* $('.visuallycheck').each(function(){
   
    var id = $(this).attr('id').match(/\d+/g);
    var videfected = $('#visuallydefectqty'+id).val();
    
    if($(".visuallycheck").prop('checked')==true){
      if(videfected == ''){
        $('.visuallydefectdiv'+id).addClass('has-error is-focused');
        new PNotify({title: "Please enter  visually defect quantity "+id+" !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidvisuallydefect = 0;
      }else{
        $('.visuallydefectdiv'+id).removeClass('has-error is-focused');
        isvalidvisuallydefect=1;
      }
    }
  }); */

  var isvalidproducts = 0;
  $('.countproducts').each(function(){
    var id = $(this).attr('div-id');
    
    if($('#mechaniclecheck'+id).prop('checked')==true || $('#electricallycheck'+id).prop('checked')==true || $('#visuallycheck'+id).prop('checked')==true){
      isvalidproducts = 1;
    }
  });
  if(isvalidproducts == 0){
    new PNotify({title: "Please check any one product !",styling: 'fontawesome',delay: '3000',type: 'error'});
  }

if(isvalidprocessid == 1 && isvalidfile == 1 && isvalidbatchid ==1 && isvalidmechanicledefect ==1 && isvalidelectricaldefect == 1 && isvalidvisuallydefect==1 && isvalidproducts==1){
    
    var formData = new FormData($('#testingform')[0]);
      if(ACTION==1){
        var uurl = SITE_URL+"testing-and-rd/testing-and-rd-edit";
      }else{
        var uurl = SITE_URL+"testing-and-rd/testing-and-rd-add";
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
          if(obj==1){
            new PNotify({title: "Testing And R&D successfully added !",styling: 'fontawesome',delay: '3000',type: 'success'});
            setTimeout(function() { window.location = SITE_URL+"testing-and-rd"; }, 1500);
          }else if(obj==2){
            new PNotify({title: "Testing And R&D successfully updated !",styling: 'fontawesome',delay: '3000',type: 'success'});
            setTimeout(function() { window.location = SITE_URL+"testing-and-rd"; }, 1500);
          }else if(obj==3){
            new PNotify({title: "File not uploaded !",styling: 'fontawesome',delay: '3000',type: 'error'});
            // setTimeout(function() { window.location = SITE_URL+"testing-and-rd"; }, 1500);
          }else if(obj==4){
            new PNotify({title: "Invalid File!",styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(obj==5){
            new PNotify({title: "Testing And R&D not updated !",styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(obj==0){
            new PNotify({title: "Testing And R&D not added !",styling: 'fontawesome',delay: '3000',type: 'error'});
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