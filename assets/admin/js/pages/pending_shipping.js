
$(document).ready(function() {
 
    //list("pendingshippingtable","Pendingshipping/listing",[0,-1]);
    oTable = $('#pendingshippingtable').DataTable
    ({
    "language": {
        "lengthMenu": "_MENU_"
    },
    "pageLength": 10,
    "columnDefs": [{
        'orderable': false,
        'targets': [0,-1]
    },
    { targets: [-2], className: "text-right" },
    { targets: [-3], className: "text-center" }],
    "order": [], //Initial no order.
    "pendingshipping": [], //Initial no pendingshipping.
    'serverSide': true,//Feature control DataTables' server-side processing mode.
    // Load data for the table's content from an Ajax source
    "ajax": {
        "url": SITE_URL+'pending-shipping/listing',
        "type": "POST",
        "data": function ( data ) {
          data.buyerchannelid = $('#buyerchannelid').val();
          data.buyermemberid = $('#buyermemberid').val();
          data.sellerchannelid = $('#sellerchannelid').val();
          data.sellermemberid = $('#sellermemberid').val();
          data.fromdate = $('#startdate').val();
          data.todate = $('#enddate').val();
          data.status = $('#status').val();
        },
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
        },
        error: function(xhr) {
          //alert(xhr.responseText);
        },
        complete: function(){
          $('.mask').hide();
          $('#loader').hide();
        },
    },
    });
    $('.dataTables_filter input').attr('placeholder','Search...');


    //DOM Manipulation to move datatable elements integrate to panel
    $('.panel-ctrls.panel-tbl').append($('.dataTables_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls.panel-tbl').append("<i class='separator'></i>");
    $('.panel-ctrls.panel-tbl').append($('.dataTables_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('.panel-footer').append($(".dataTable+.row"));
    $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-md");

    $('#datepicker-range').datepicker({
      todayHighlight: true,
      format: 'dd/mm/yyyy'
  	});

    $(function () {
        $('.panel-heading.filter-panel').click(function() {
            $(this).find('.button-icon span').html($(this).find('.button-icon span').text() == 'keyboard_arrow_down' ? 'keyboard_arrow_up' : 'keyboard_arrow_down');
            //$(this).children().toggleClass(" ");
            $(this).next().slideToggle({duration: 200});
            $(this).toggleClass('panel-collapsed');
            return false;
        });
    });

    $("#buyerchannelid").change(function(){
      getmembers();
    });
    $("#sellerchannelid").change(function(){
      getmembers(1);
    });


    
    $('input[name="shippingby"]').on('change', function (e) {
      if(this.value==0){
        $('#courier_div').show();
        $('#transporter_div').hide();
        $('#courierid').val("0").selectpicker("refresh");
      }else{
        $('#courier_div').hide();
        $('#transporter_div').show();
        $('#transporterid').val("0").selectpicker("refresh");
      }

      $('#indianpost_div').html(''); 
      $('#indianpost_div').hide();
      $('#fedex_div').html('');
      $('#fedex_div').hide();
    });
    $('#transporterid').on('change', function (e) {
      setindianpostdata();
    });
    $('#courierid').on('change', function (e) {
      if(this.value==fedexcourierid){
        var invoiceid = $('#invoiceid').val();
        
        setfedexdata($('#weight'+invoiceid).val(),$('#invoiceamount'+invoiceid).val(),$('#codamount'+invoiceid).val(),$('#paymentmethod'+invoiceid).text());
      }else{
        setindianpostdata();
      }
    });

});
function applyFilter(){
  oTable.ajax.reload(null, false);
}

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
              <div class="form-group"> \
                <label class="col-sm-4 control-label">Remarks</label> \
                <div class="col-sm-8"> \
                  <textarea id="indianpostremarks" name="indianpostremarks" class="form-control" rows="3" maxlength="100"></textarea> \
                </div> \
              </div> \
            </div> \
          <div class="col-md-12 countcourierpackage" id="indianpostpackagecount1"> \
            <div class="col-md-5 p-n"> \
              <div class="form-group" id="indianpostweight1_div"> \
                <label class="col-sm-5 control-label">Weight (KG)</label> \
                <div class="col-sm-7"> \
                  <input id="indianpostweight1" type="text" name="indianpostweight[]" value="" class="form-control" onkeypress="return decimal_number_validation(event,this.value,6,3)"> \
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
            <div class="col-md-2 text-right pt-md">\
                   \
                    <button type="button" class="btn btn-default btn-raised remove_package_btn m-n" onclick="removeindianpostpackage(1)" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>\
                    \
                    <button type="button" class="btn btn-default btn-raised add_package_btn m-n" onclick="addnewindianpostpackage()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>\
                </div>\
          </div>   \
          <div id="indianpostpackagedetail_div"></div>';
  $('#indianpost_div').html($.html); 
}
/*<div class="col-md-2"> \
              <button type="button" class="btn btn-default btn-raised" onclick="addnewindianpostpackage()" style="margin-top: 0px;"><i class="material-icons">plus_one</i><div class="ripple-container"></div></button> \
            </div> \*/


function getmembers(type=0){
  
  var memberelement = $("#buyermemberid");
  var channelelement = $("#buyerchannelid");

  if(type==1){
    memberelement = $("#sellermemberid");
    channelelement = $("#sellerchannelid");
  }
  memberelement.find('option')
              .remove()
              .end()
              .val('whatever')
            ;
  memberelement.selectpicker('refresh');
  var channelid = channelelement.val();

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

          memberelement.append($('<option>', { 
            value: response[i]['id'],
            text : ucwords(response[i]['namewithcodeormobile'])
          }));

        }
        memberelement.selectpicker('refresh');
      },
      error: function(xhr) {
        //alert(xhr.responseText);
      },
    });
  }
}
function openshippingorder(invoiceid,extendshippingaddress){

  PNotify.removeAll();
  if(extendshippingaddress){
    swal({title: 'Shipping address limit exceeded more than 70 characters.',
        type: "warning",   
        showCancelButton: true,   
        confirmButtonColor: "#DD6B55",   
        confirmButtonText: "Ok, Got it!",
        timer: 2000,   
        closeOnConfirm: false }, 
        function(isConfirm){
          if (isConfirm) {
            swal.close();
            openmodal(invoiceid);
          }
        });
  }else{
    openmodal(invoiceid);
  }
  
}
function openmodal(invoiceid){

  $('#myModal').modal('show');
  $('#invoiceid').val(invoiceid);
  $('#invoiceamount').val($('#invoiceamount'+invoiceid).val());
  $('#courierid'+invoiceid).removeClass('has-error is-focused');
  var courierid = $('#courierid'+invoiceid).val();

  if($('#invoicestatus'+invoiceid).val()==4){
    $('#submit').hide();
  }else{
    $('#submit').show();
  }
  
  $('#courierid').val(courierid);
  $('#courierid').selectpicker('refresh');

  $('#indianpost_div').hide();
  $('#indianpost_div').html(''); 
  $('#fedex_div').hide();
  $('#fedex_div').html(''); 

  $(".add_package_btn").hide();
  $(".add_package_btn:last").show();
}
function addnewindianpostpackage(){

  if($('input[name="indianpostamount[]"]').length<4){
      indianpostpackagecount = ++indianpostpackagecount;
        $.html = '<div class="col-md-12 countcourierpackage" id="indianpostpackagecount'+indianpostpackagecount+'"><div class="" id="productfile'+indianpostpackagecount+'_div"> \
                    <div class="col-md-5 p-n"> \
                      <div class="form-group" id="indianpostweight'+indianpostpackagecount+'_div"> \
                        <label class="col-sm-5 control-label">Weight (KG)</label> \
                        <div class="col-sm-7"> \
                          <input id="indianpostweight'+indianpostpackagecount+'" type="text" name="indianpostweight[]" value="" class="form-control" onkeypress="return decimal_number_validation(event,this.value,6,3)"> \
                        </div> \
                      </div> \
                    </div> \
                    <div class="col-md-5 p-n"> \
                      <div class="form-group" id="indianpostamount'+indianpostpackagecount+'_div"> \
                        <label class="col-sm-5 control-label">Amount <span class="mandatoryfield">*</span></label> \
                        <div class="col-sm-7"> \
                          <input id="indianpostamount'+indianpostpackagecount+'" type="text" name="indianpostamount[]" value="0" class="form-control" onkeypress="return decimal_number_validation(event,this.value,7)"> \
                        </div> \
                      </div> \
                    </div> \
                    <div class="col-md-2 text-right pt-md"> \
                        <button type="button" class="btn btn-default btn-raised remove_package_btn m-n" onclick="removeindianpostpackage('+indianpostpackagecount+')" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>\
                        <button type="button" class="btn btn-default btn-raised add_package_btn m-n" onclick="addnewindianpostpackage()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>\
                    </div> \
                    </div></div>';
                    
        $(".remove_package_btn:first").show();
        $(".add_package_btn").hide();
        $('.countcourierpackage:last').after($.html);
        
    }else{
      PNotify.removeAll();
      new PNotify({title: 'Maximum 4 package allowed !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }
}
function removeindianpostpackage(rowid){
    $('#indianpostpackagecount'+rowid).remove();

    $(".add_package_btn:last").show();
    if ($(".remove_package_btn:visible").length == 1) {
        $(".remove_package_btn:first").hide();
    }
}
function viewshippingorder(invoiceid){
  PNotify.removeAll();
  var uurl = SITE_URL+"pending-shipping/viewshippingorderdetails";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {invoiceid:invoiceid},
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
                    <td>Shipping By</td> \
                    <td>'+(obj["shippingdata"]["shippingby"]==0?"Courier":"Transporter")+'</td> \
                  </tr>\
                  <tr> \
                    <td>Shipping Company</td> \
                    <td>'+ucwords(obj["shippingdata"]["shippingcompany"])+'</td> \
                  </tr>';
        
        $.html += '<tr> \
                    <td>Tracking Code</td> \
                    <td>'+obj["shippingdata"]["trackingcode"]+'</td> \
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
                  </tr> \
                  <tr> \
                    <td>Remarks</td> \
                    <td>'+obj["shippingdata"]["remarks"]+'</td> \
                  </tr>';

          $.html += '<tr> \
                    <td colspan="3" class="text-center"><b>Shipping Package Details</b></td> \
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
  
  var shippingby = ($("#shippingbycourier").is(":checked"))?0:1;
  var courierid = $('#courierid').val();
  var transporterid = $('#transporterid').val();
  //alert(courierid);
  if(shippingby==0 && courierid==fedexcourierid){
    var fedexservice = $("#fedexservice").val();
    var fedexweight = $("input[name='fedexweight[]']").map(function(){return $(this).val();}).get();
    var length = $("input[name='length[]']").map(function(){return $(this).val();}).get();
    var width = $("input[name='width[]']").map(function(){return $(this).val();}).get();
    var height = $("input[name='height[]']").map(function(){return $(this).val();}).get();

    var isvalidfedexservice = isvalidshippingprice = 0;
    var isvalidfedexweight = isvalidfedexdimensions = 1;

    for (var i = 0; i < fedexweight.length; i++) {
      if(fedexweight[i]==0){
        $("#fedexweight"+(i+1)+"_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter '+(i+1)+' package weight !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidfedexweight = 0;
      }else{
        $("#fedexweight"+(i+1)+"_div").removeClass("has-error is-focused");
      }

      if(length[i]!='' || width[i]!='' || height[i]!=''){
        if(length[i]=='' || width[i]=='' || height[i]==''){
          $("#fedexweight"+(i+1)+"_div").addClass("has-error is-focused");
          new PNotify({title: 'Please enter '+(i+1)+' package Length, Width, and Height !',styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidfedexdimensions = 0;
        }
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

    if(isvalidfedexservice==1 && isvalidfedexweight==1 && isvalidshippingprice==1 && isvalidfedexdimensions==1){
      placeshippingorder();
    }
    
  }else{
  
    var isvalidindianposttrackingcode = 0;
    var isvalidindianpostamount = isvalidcourierid = isvalidtransporterid = 1;
    
    PNotify.removeAll();
    if(shippingby==0){
      if(courierid == 0){
          $("#courier_div").addClass("has-error is-focused");
          new PNotify({title: 'Please select courier company !',styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidcourierid = 0;
      }else{
          $("#courier_div").removeClass("has-error is-focused");
      }
    }else{
      if(transporterid == 0){
        $("#transporter_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select transporter !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidtransporterid = 0;
      }else{
          $("#transporter_div").removeClass("has-error is-focused");
      }
    }
    if(isvalidcourierid == 1 && isvalidtransporterid == 1){

      var indianposttrackingcode = $("#indianposttrackingcode").val().trim();
      //console.log(indianposttrackingcode);
      var indianpostamount = $("input[name='indianpostamount[]']").map(function(){return $(this).val();}).get();
      var indianpostamountarray = $("input[name='indianpostamount[]']").map(function(){return $(this).attr('id');}).get();
  
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
              $("#indianposttracking_div").removeClass("has-error is-focused");
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
    }

    if(isvalidcourierid == 1 && isvalidtransporterid == 1 && isvalidindianposttrackingcode==1 && isvalidindianpostamount==1){
        placeshippingorder();
    }
  }
}
function placeshippingorder(){

  var formData = new FormData($('#shippingorderform')[0]);
  
  var uurl = SITE_URL+"pending-shipping/place-shipping-order";
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
      var obj = JSON.parse(response);
      if(obj['error']==1){
        if($("#shippingbycourier").is(":checked")==0 && $('#courierid').val()==fedexcourierid){
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
function generateinvoice(invoicenumber,invoiceid){
  
  var uurl = SITE_URL+"pending-shipping/generateinvoice";
  invoiceamount = parseFloat($('#invoiceamount'+invoiceid).val());
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {invoicenumber:invoicenumber,invoiceid:invoiceid,invoiceamount:invoiceamount},
    //async: false,
    beforeSend: function(){
      $('.mask').show();
      $('#loader').show();
    },
    success: function(response){
      if(response==1){
        //new PNotify({title: "Invoice successfully send to member.",styling: 'fontawesome',delay: '3000',type: 'success'});
        new PNotify({title: "Invoice successfully updated.",styling: 'fontawesome',delay: '3000',type: 'success'});
        setTimeout(function() { location.reload(); }, 1500);
      }else{
        //new PNotify({title: "Invoice not send to member !",styling: 'fontawesome',delay: '3000',type: 'error'});
        new PNotify({title: "Invoice not updated.",styling: 'fontawesome',delay: '3000',type: 'success'});
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
function calculateshippingcharges(){
  var fedexservice = $("#fedexservice").val();
  var fedexweight = $("input[name='fedexweight[]']").map(function(){return $(this).val();}).get();
  var length = $("input[name='length[]']").map(function(){return $(this).val();}).get();
  var width = $("input[name='width[]']").map(function(){return $(this).val();}).get();
  var height = $("input[name='height[]']").map(function(){return $(this).val();}).get();
  var units = $("select[name='units[]']").map(function(){return $(this).val();}).get();

  var isvalidfedexservice = isvalidshippingprice = 0;
  var isvalidfedexweight = isvalidfedexdimensions = 1;

  for (var i = 0; i < fedexweight.length; i++) {
    if(fedexweight[i]==0){
      $("#fedexweight"+(i+1)+"_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter '+(i+1)+' package weight !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidfedexweight = 0;
    }else{
      $("#fedexweight"+(i+1)+"_div").removeClass("has-error is-focused");
    }

    if(length[i]!='' || width[i]!='' || height[i]!=''){
      if(length[i]=='' || width[i]=='' || height[i]==''){
        $("#fedexweight"+(i+1)+"_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter '+(i+1)+' package Length, Width, and Height !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidfedexdimensions = 0;
      }
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

  if(isvalidfedexweight==1 && isvalidfedexservice==1 && isvalidfedexdimensions==1){
    var uurl = SITE_URL+"Pending-shipping/getfedexrate";
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {fedexweight:fedexweight,fedexdetailid:$('#fedexdetailid').val(),invoiceid:$('#invoiceid').val(),fedexservice:fedexservice,fedexcodamount:$('#fedexcodamount').val(),invoiceamount:$('#invoiceamount').val(),
              length:length,width:width,height:height,units:units},
      dataType: 'json',
      //async: false,
      beforeSend: function(){
        $('.mask').show();
        $('#loader').show();
      },
      success: function(response){
        //console.log(response);
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
            //console.log($.html);
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