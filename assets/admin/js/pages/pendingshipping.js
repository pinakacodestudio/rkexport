
$(document).ready(function() {
   
    //list("pendingshippingtable","Pendingshipping/listing",[0,-1]);
    oTable = $('#pendingshippingtable').DataTable
      ({
        "processing": true,//Feature control the processing indicator.
        "language": {
          "lengthMenu": "_MENU_"
        },
        "pageLength": 50,
        "columnDefs": [{
          'orderable': false,
          'targets': [0,2,-1]
        }],
        "order": [], //Initial no order.
        "pendingshipping": [], //Initial no pendingshipping.
        'serverSide': true,//Feature control DataTables' server-side processing mode.
        // Load data for the table's content from an Ajax source
        "ajax": {
          	"url": SITE_URL+'Pendingshipping/listing',
          	"type": "POST",
          	"data": function ( data ) {
                data.customerid = $('#customerid').val();
                data.fromdate = $('#fromdate').val();
                data.todate = $('#todate').val();
            }
        },
      });
    $('.dataTables_filter input').attr('placeholder','Search...');


    //DOM Manipulation to move datatable elements integrate to panel
    $('.panel-ctrls').append($('.dataTables_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls').append("<i class='separator'></i>");
    $('.panel-ctrls').append($('.dataTables_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('.panel-footer').append($(".dataTable+.row"));
    $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-md");

    $('#datepicker-range').datepicker({
      todayHighlight: true,
      format: 'yyyy-mm-dd'
  	}).on("change", function() {
    	oTable.ajax.reload(null,false);
  	});

    /*$('#shipdate').datepicker({
      language:  'fr',
      weekStart: 1,
      autoclose: 1,
      startDate: new Date(),
      startView: 3,
      forceParse: 0,
      format: "dd/mm/yyyy",
      todayHighlight: 1
    });*/
});
$('#customerid').on('change', function (e) {
  oTable.ajax.reload(null,false);
});

function exportpendingshippingreport(){
  
  var customerid = $('#customerid').val();
  var fromdate = $('#fromdate').val();
  var todate = $('#todate').val();
  
  var totalRecords =$("#pendingshippingtable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"Pendingshipping/exportpendingshippingreport?customerid="+customerid+"&fromdate="+fromdate+"&todate="+todate;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  
}
$('#courierid').on('change', function (e) {
  if(this.value==1){
    var orderid = $('#orderid').val();
    setfedexdata($('#weight'+orderid).val(),$('#invoiceamount'+orderid).val(),$('#paymentmethod'+orderid).text());
  }else{
    setindianpostdata();
  }
});
function setindianpostdata(){
  $('#indianpost_div').show();
  $('#fedex_div').html('');
  $('#fedex_div').hide();

  $.html = '<div class="col-md-12"> \
              <div class="form-group" id="indianposttracking_div"> \
                <label class="col-sm-4 control-label">Tracking Code <span class="mandatoryfield">*</span></label> \
                <div class="col-sm-8"> \
                  <input id="indianposttrackingcode" type="text" name="indianposttrackingcode" value="" class="form-control"> \
                </div> \
              </div> \
            </div> \
          <div class="col-md-12"> \
            <div class="col-md-5 p-n"> \
              <div class="form-group" id="indianpostweight1_div"> \
                <label class="col-sm-5 control-label">Weight (KG)</label> \
                <div class="col-sm-7"> \
                  <input id="indianpostweight1" type="text" name="indianpostweight[]" value="" class="form-control" onkeypress="return isNumber(event)" maxlength="2"> \
                </div> \
              </div> \
            </div> \
            <div class="col-md-5 p-n"> \
              <div class="form-group" id="indianpostamount1_div"> \
                <label class="col-sm-5 control-label">Amount <span class="mandatoryfield">*</span></label> \
                <div class="col-sm-7"> \
                  <input id="indianpostamount1" type="text" name="indianpostamount[]" value="0" class="form-control" onkeypress="return decimal_number_validation(event,this.value,7)"> \
                </div> \
              </div> \
            </div> \
            <div class="col-md-2"> \
              <button type="button" class="btn btn-default btn-raised" onclick="addnewindianpostpackage()" style="margin-top: 0px;"><i class="material-icons">plus_one</i><div class="ripple-container"></div></button> \
            </div> \
          </div>   \
          <div id="indianpostpackagedetail_div"></div>';
  $('#indianpost_div').html($.html); 
}
function setfedexdata(weight,invoiceamount,fedexcodamount,paymentmethod){
  $('#indianpost_div').hide();
  $('#indianpost_div').html('');
  $('#fedex_div').show();
  

  $.html = '<div class="col-md-12"> \
              <div class="row"> \
                <div class="col-md-12"> \
                  <div class="col-md-10"> \
                    <div class="form-group" id="fedexweight1_div"> \
                      <label class="col-sm-5 control-label">Weight (KG) <span class="mandatoryfield">*</span></label> \
                      <div class="col-sm-7"> \
                        <input id="fedexweight1" type="text" name="fedexweight[]" value="'+weight+'" class="form-control" onkeypress="return decimal_number_validation(event,this.value)"> \
                      </div> \
                    </div> \
                  </div> \
                  <div class="col-md-2"> \
                    <button type="button" class="btn btn-default btn-raised" onclick="addnewfedexpackage()" style="margin-top: 0px;"><i class="material-icons">plus_one</i><div class="ripple-container"></div></button> \
                  </div> \
                </div> \
                <div id="multiplepackage"></div> \
              </div> \
              <div class="form-group" id="fedexinvoiceamount_div"> \
                <label class="col-sm-4 control-label">Invoice Amount</label> \
                <div class="col-sm-8"> \
                  <input id="fedexinvoiceamount" type="text" name="fedexinvoiceamount" value="'+invoiceamount+'" class="form-control" onkeypress="return isNumber(event)" readonly> \
                </div> \
              </div>';
              if(paymentmethod=='COD'){
                $.html += '<div class="form-group" id="fedexcodamount_div"> \
                            <label class="col-sm-4 control-label">COD Amount</label> \
                            <div class="col-sm-8"> \
                              <input id="fedexcodamount" type="text" name="fedexcodamount" value="'+fedexcodamount+'" class="form-control" onkeypress="return isNumber(event)" readonly> \
                            </div> \
                          </div>';
              }
              $.html += '<div class="form-group" id="fedexservice_div"> \
                            <label class="col-sm-4 control-label">Service <span class="mandatoryfield">*</span></label> \
                            <div class="col-sm-8"> \
                              <select id="fedexservice" name="fedexservice" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true"> \
                                <option value="0">Select Service</option> \
                                <option value="FEDEX_EXPRESS_SAVER" selected>FEDEX_EXPRESS_SAVER</option> \
                                <option value="STANDARD_OVERNIGHT">STANDARD_OVERNIGHT</option> \
                                <option value="FEDEX_FIRST_FREIGHT">FEDEX_FIRST_FREIGHT</option> \
                                <option value="FEDEX_1_DAY_FREIGHT">FEDEX_1_DAY_FREIGHT</option> \
                                <option value="FEDEX_2_DAY">FEDEX_2_DAY</option> \
                                <option value="FEDEX_2_DAY_AM">FEDEX_2_DAY_AM</option> \
                                <option value="FEDEX_2_DAY_FREIGHT">FEDEX_2_DAY_FREIGHT</option> \
                                <option value="FEDEX_3_DAY_FREIGHT">FEDEX_3_DAY_FREIGHT</option> \
                                <option value="FEDEX_FREIGHT_ECONOMY">FEDEX_FREIGHT_ECONOMY</option> \
                                <option value="FEDEX_FREIGHT_PRIORITY">FEDEX_FREIGHT_PRIORITY</option> \
                                <option value="FEDEX_GROUND">FEDEX_GROUND</option> \
                                <option value="FIRST_OVERNIGHT">FIRST_OVERNIGHT</option> \
                                <option value="INTERNATIONAL_ECONOMY">INTERNATIONAL_ECONOMY</option> \
                                <option value="INTERNATIONAL_ECONOMY_FREIGHT">INTERNATIONAL_ECONOMY_FREIGHT</option> \
                                <option value="INTERNATIONAL_FIRST">INTERNATIONAL_FIRST</option> \
                                <option value="INTERNATIONAL_PRIORITY">INTERNATIONAL_PRIORITY</option> \
                                <option value="INTERNATIONAL_PRIORITY_FREIGHT">INTERNATIONAL_PRIORITY_FREIGHT</option> \
                              </select> \
                            </div> \
                          </div>';
            $.html += '<div class="form-group"> \
                            <label class="col-sm-4 control-label"></label> \
                            <div class="col-sm-8"> \
                              <input type="button" value="Calculate Rate" onclick="calculateshippingcharges()" class="btn btn-raised"> \
                            </div> \
                        </div> \
                        <div id="shippingprice_div"></div> \
                        </div>';


  $('#fedex_div').html($.html); 
 
  $('#fedexservice').selectpicker('refresh');
}
function calculateshippingcharges(){
  var fedexservice = $("#fedexservice").val();
  var fedexweight = $("input[name='fedexweight[]']").map(function(){return $(this).val();}).get();

  var isvalidfedexservice = isvalidshippingprice = 0;
  var isvalidfedexweight = 1;

  for (var i = 0; i < fedexweight.length; i++) {
    if(fedexweight[i]==0){
      $("#fedexweight"+(i+1)+"_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter '+(i+1)+' package weight !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidfedexweight = 0;
    }else{
      $("#fedexweight"+(i+1)+"_div").removeClass("has-error is-focused");
    }
  }
  
  if(fedexservice==0){
    $("#fedexservice_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select shipping service !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidfedexservice = 0;
  }else{
    isvalidfedexservice = 1;
    $("#fedexservice_div").removeClass("has-error is-focused");
  }

  if(isvalidfedexweight==1 && isvalidfedexservice==1){
    var uurl = SITE_URL+"Pendingshipping/getfedexrate";
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {fedexweight:fedexweight,orderid:$('#orderid').val(),fedexservice:fedexservice,fedexcodamount:$('#fedexcodamount').val(),invoiceamount:$('#invoiceamount').val()},
      dataType: 'json',
      //async: false,
      beforeSend: function(){
        $('.mask').show();
        $('#loader').show();
      },
      success: function(response){

        if(response['result']=='success'){
          var data = response['error'];
          
          $.html = '';
          $.html += '<div class="form-group"> \
                        <label class="col-sm-4">Delivery Date</label> \
                        <div class="col-sm-8"> \
                          <label name="deliverydate">'+data['DeliveryDate']+'</label> \
                        </div> \
                      </div> \
                      <div class="form-group"> \
                        <label class="col-sm-4">Amount</label> \
                        <div class="col-sm-8"> \
                          <label>'+data['Currency']+'&nbsp;'+data['Amount']+'</label> \
                          <input type="hidden" name="shippingamount" value="'+data['Amount']+'"> \
                        </div> \
                      </div>';
          $('#shippingprice_div').html($.html);               
        }else{
          $('#shippingprice_div').html(''); 
          new PNotify({title: response['error'],styling: 'fontawesome',delay: '3000',type: 'error'});
        }
        
        /*$.html = "<option value='0'>Select Service</option>";
        for (var i = 0; i < response.length; i++) {
          //console.log(response[i]['Amount']);
          $.html += "<option value='"+JSON.stringify(response[i])+"'>"+response[i]['Currency']+" "+response[i]['Amount']+" - "+response[i]['ServiceType']+" ("+response[i]['RateType']+")</option>";
        }
        $('#fedexservice').html($.html);
        $('#fedexservice').selectpicker('refresh');*/
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
function openshippingorder(orderid,$extendshippingaddress){

  PNotify.removeAll();

  alert("fdf");
 /*  alert("12");
  $('#indianpost_div').hide();
  $('#indianpost_div').html('');
  $('#fedex_div').hide();
  $('#fedex_div').html('');
  console.log($('#indianpost_div').html()); */
  $('#orderid').val(orderid);
  $('#invoiceamount').val($('#invoiceamount'+orderid).val());
  $('#courierid'+orderid).removeClass('has-error is-focused');
  var courierid = $('#courierid'+orderid).val();

  if($('#orderstatus'+orderid).val()==2){
    $('#submit').hide();
  }else{
    $('#submit').show();
  }
  
  if(courierid==1){
    //$('#courierid').html("<option value='1'>Fedex</option><option value='2'>India Post</option>");
    $('#courierid').val(1);
    setfedexdata($('#weight'+orderid).val(),$('#invoiceamount'+orderid).val(),$('#codamount'+orderid).val(),$('#paymentmethod'+orderid).text());
  }else if(courierid==2){
    //$('#courierid').html("<option value='2'>India Post</option>");
    $('#courierid').val(2);
    setindianpostdata();
  }else{
    setindianpostdata();
  }
  $('#courierid').selectpicker('refresh');
}
function viewshippingorder(orderid){
  PNotify.removeAll();
  var uurl = SITE_URL+"Pendingshipping/viewshippingorderdetails";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {orderid:orderid},
    datatype:'json',
    //async: false,
    beforeSend: function(){
      $('.mask').show();
      $('#loader').show();
    },
    success: function(response){
      $.html = '<table class="table table-hover table-inbox table-vam">';
      var obj = JSON.parse(response);

      if(obj['shippingdata']){
        $.html += '<tr> \
                    <td colspan="2" class="text-center"><b>Shipping Details</b></td> \
                  </tr> \
                  <tr> \
                    <td>Courier Company</td> \
                    <td>'+obj["shippingdata"]["couriercompany"]+'</td> \
                  </tr> \
                  <tr> \
                    <td>Tracking Code</td> \
                    <td>'+obj["shippingdata"]["trackingcode"]+'</td> \
                  </tr> \
                  <tr> \
                    <td>Service Name</td> \
                    <td>'+obj["shippingdata"]["servicename"]+'</td> \
                  </tr> \
                  <tr> \
                    <td>Shipping Amount</td> \
                    <td>'+obj["shippingdata"]["shippingamount"]+'</td> \
                  </tr> \
                  <tr> \
                    <td>Invoice Amount</td> \
                    <td>'+obj["shippingdata"]["invoiceamount"]+'</td> \
                  </tr> \
                  <tr> \
                    <td>Ship Date</td> \
                    <td>'+obj["shippingdata"]["shipdate"]+'</td> \
                  </tr>';

          $.html += '<tr> \
                    <td colspan="2" class="text-center"><b>Shipping Package Details</b></td> \
                  </tr> \
                  <tr> \
                    <td>Weight (KG)</td> \
                    <td>Amount</td> \
                  </tr>';

          for (var i = 0; i < obj['shippingpackagedata'].length; i++) {
              $.html += '<tr> \
                          <td>'+obj['shippingpackagedata'][i]['weight']+'</td> \
                          <td>'+obj['shippingpackagedata'][i]['amount']+'</td> \
                        </tr>';
          }           
      }
      $.html += '</table>';
      $('#shippingdetail').html($.html);
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
function checkvalidation(){
  var courierid = $('#courierid').val();
  
  if(courierid==1){

    var fedexservice = $("#fedexservice").val();
    var fedexweight = $("input[name='fedexweight[]']").map(function(){return $(this).val();}).get();

    var isvalidfedexservice = isvalidshippingprice = 0;
    var isvalidfedexweight = 1;

    for (var i = 0; i < fedexweight.length; i++) {
      if(fedexweight[i]==0){
        $("#fedexweight"+(i+1)+"_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter '+(i+1)+' package weight !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidfedexweight = 0;
      }else{
        $("#fedexweight"+(i+1)+"_div").removeClass("has-error is-focused");
      }
    }
    
    if(fedexservice==0){
      $("#fedexservice_div").addClass("has-error is-focused");
      new PNotify({title: 'Please select shipping service !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidfedexservice = 0;
    }else{
      isvalidfedexservice = 1;
      $("#fedexservice_div").removeClass("has-error is-focused");
    }
    if($('#shippingprice_div').text().trim()==''){
      isvalidshippingprice = 0;
      new PNotify({title: 'Please calculate shipping rate !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }else{
      isvalidshippingprice = 1;
    }

    if(isvalidfedexservice==1 && isvalidfedexweight==1 && isvalidshippingprice==1){
      placeshippingorder();
    }
    
  }else{
    var indianposttrackingcode = $("#indianposttrackingcode").val().trim();
    var indianpostamount = $("input[name='indianpostamount[]']").map(function(){return $(this).val();}).get();
    var indianpostamountarray = $("input[name='indianpostamount[]']").map(function(){return $(this).attr('id');}).get();
    
    var isvalidindianposttrackingcode = 0;
    var isvalidindianpostamount = 1;

    if(indianposttrackingcode == ''){
      $("#indianposttracking_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter tracking code !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidindianposttrackingcode = 0;
    }else { 
      if(indianposttrackingcode.length<5){
        $("#indianposttracking_div").addClass("has-error is-focused");
        new PNotify({title: "Tracking code require minimum 5 characters !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidindianposttrackingcode = 0;
      }else{
        isvalidindianposttrackingcode = 1;  
      }
    }
    for (var i = 0; i < indianpostamount.length; i++) {
      if(indianpostamount[i]==''){
        $("#"+(indianpostamountarray[i])+"_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter '+(i+1)+' package amount !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidindianpostamount = 0;
      }else{
        $("#"+(indianpostamountarray[i])+"_div").removeClass("has-error is-focused");
      }
    }

    if(isvalidindianposttrackingcode==1 && isvalidindianpostamount==1){
      placeshippingorder();
    }
    
  }
}
function placeshippingorder(){

  var formData = new FormData($('#shippingorderform')[0]);
  var uurl = SITE_URL+"Pendingshipping/placeshippingorder";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: formData,
    datatype:'json',
    //async: false,
    beforeSend: function(){
      $('.mask').show();
      $('#loader').show();
    },
    success: function(response){
      var obj = JSON.parse(response);console.log(obj);
      if(obj['error']==1){
        if($('#courierid').val()==1){
          for (var i = 0; i < obj['label'].length; i++) {
            var w = window.open(FEDEXLABEL+obj['label'][i],'_blank');
            w.print();
          }
        }
        new PNotify({title: "Shipping order successfully placed.",styling: 'fontawesome',delay: '3000',type: 'success'});
        setTimeout(function() { location.reload(); }, 1500);
      }else{
        new PNotify({title: obj['label'],styling: 'fontawesome',delay: '3000',type: 'error'});
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
function regenerateorderpdf(invoicenumber,orderid){
  
  var uurl = SITE_URL+"Pendingshipping/regenerateorderpdf";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {invoicenumber:invoicenumber,orderid:orderid,invoiceamount:$('#invoiceamount'+orderid).val()},
    //async: false,
    beforeSend: function(){
      $('.mask').show();
      $('#loader').show();
    },
    success: function(response){
      if(response==1){
        new PNotify({title: "Invoice successfully send to customer.",styling: 'fontawesome',delay: '3000',type: 'success'});
        setTimeout(function() { location.reload(); }, 1500);
      }else{
        new PNotify({title: "Invoice not send to customer !",styling: 'fontawesome',delay: '3000',type: 'error'});
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