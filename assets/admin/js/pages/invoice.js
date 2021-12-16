
$(document).ready(function() {

    oTable = $('#invoicetable').DataTable
    ({
      "language": {
        "lengthMenu": "_MENU_"
      },
      "pageLength": 50,
      "scrollCollapse": true,
      "scrollY": "500px",
      "columnDefs": [{
        'orderable': false,
        'targets': [0,3,-1]
      },{ targets: [7], className: "text-right" }],
      "order": [], //Initial no order.
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": SITE_URL+"invoice/listing",
        "type": "POST",
        "data": function ( data ) {
          data.buyerchannelid = $('#buyerchannelid').val();
          data.buyermemberid = $('#buyermemberid').val();
          data.sellerchannelid = $('#sellerchannelid').val();
          data.sellermemberid = $('#sellermemberid').val();
          data.startdate = $('#startdate').val();
          data.enddate = $('#enddate').val();
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
    
    $('.panel-footer').append($(".dataTables_info").parent().parent());

    $(function () {
      $('.panel-heading.filter-panel').click(function() {
          $(this).find('.button-icon span').html($(this).find('.button-icon span').text() == 'keyboard_arrow_down' ? 'keyboard_arrow_up' : 'keyboard_arrow_down');
          //$(this).children().toggleClass(" ");
          $(this).next().slideToggle({duration: 200});
          $(this).toggleClass('panel-collapsed');
          return false;
      });
    });

    $('#datepicker-range').datepicker({
      todayHighlight: true,
      format: 'dd/mm/yyyy',
      autoclose: true,
      todayBtn:"linked"
    });

    $("#buyerchannelid").change(function(){
      getmembers();
    });
    $("#sellerchannelid").change(function(){
      getmembers(1);
    });
});
function applyFilter(){
  oTable.ajax.reload();
}
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
function printInvoice(id){

  var uurl = SITE_URL + "invoice/printInvoice";
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

function chageinvoicestatus(status, invoiceId){
  var uurl = SITE_URL+"invoice/update-status";
  if(invoiceId!=''){
    swal({    title: "Are you sure to change status?",
      type: "warning",   
      showCancelButton: true,   
      confirmButtonColor: "#DD6B55",   
      confirmButtonText: "Yes, change it!",   
      closeOnConfirm: false }, 
    function(isConfirm){   
      if (isConfirm) {  
        if(status==2){
            
          $('#rejectinvoiceModal').modal('show');
          $('#rejectioninvoiceid').val(invoiceId);
          $('#rejectionstatus').val(status);
          $('#resonforrejection').val('');
        }else{ 
          $.ajax({
            url: uurl,
            type: 'POST',
            data: {status:status,invoiceId:invoiceId},
            beforeSend: function(){
              $('.mask').show();
              $('#loader').show();
            },
            success: function(response){
              if(response==1){
                  location.reload();
                }
            },
            complete: function(){
              $('.mask').hide();
              $('#loader').hide();
            },
            error: function(xhr) {
            //alert(xhr.responseText);
            }
          });  
        }
      }
    });
  }           
}

function checkvalidationforrejectioninvoice(){

  var resonforcancellation = $('#resonforrejection').val();
  var invoiceId = $('#rejectioninvoiceid').val();
  var status = $('#rejectionstatus').val();
  var isvalidresonforrejection = 1;
  
  PNotify.removeAll();
  $("#resonalert").html('');

  if(resonforcancellation == ''){
    $("#resonforrejection_div").addClass("has-error is-focused");
    $("#resonalert").html('<i class="fa fa-exclamation-triangle"></i> Please enter reson for cancellation !');
    isvalidresonforrejection = 0;
  }else {
    if(resonforcancellation.length < 3){
      $("#resonforrejection_div").addClass("has-error is-focused");
      $("#resonalert").html('<i class="fa fa-exclamation-triangle"></i> Reson require minimum 3 characters !');
      isvalidresonforrejection = 0;
    }
  }
  if(isvalidresonforrejection == 1){
    var uurl = SITE_URL+"invoice/update-status";
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {status:status,invoiceId:invoiceId,resonforcancellation:resonforcancellation},
      success: function(response){
          if(response==1){
            location.reload();
          }else{
            new PNotify({title: response,styling: 'fontawesome',delay: '3000',type: 'error'});
          }
        },
      error: function(xhr) {
      //alert(xhr.responseText);
      }
    }); 
  }

}

function generateAwB(invoiceid){

  var uurl = SITE_URL + "invoice/generateAwB";
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {invoiceid:invoiceid},
        //dataType: 'json',
        async: false,
        beforeSend: function() {
            $('.mask').show();
            $('#loader').show();
        },
        success: function(response) {
          
            //console.log(response);
            $('.awbbody').html(response);
        },
        error: function(xhr) {
            //alert(xhr.responseText);
        },
        complete: function() {
            $('.mask').hide();
            $('#loader').hide();
        },
      });

    $('#myAWBModal').modal('show');
   

}

function exporttoexcelinvoice(){
  
  var buyerchannelid = $('#buyerchannelid').val();
  var buyermemberid = ($('#buyermemberid').val()!=null)?$('#buyermemberid').val():"";
  var sellerchannelid = $('#sellerchannelid').val();
  var sellermemberid = ($('#sellermemberid').val()!=null)?$('#sellermemberid').val():"";
  var startdate = $('#startdate').val();
  var enddate = $('#enddate').val();
  var status = $('#status').val();
  
  var totalRecords =$("#invoicetable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"invoice/exporttoexcelinvoice?buyerchannelid="+buyerchannelid+"&buyermemberid="+buyermemberid+"&sellerchannelid="+sellerchannelid+"&sellermemberid="+sellermemberid+"&startdate="+startdate+"&enddate="+enddate+"&status="+status;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  
}
function checkwhatsappnumber(id){

  whasappnumber = $("#checkwhatsappnumber"+id).val();
    if(whasappnumber==''){
      new PNotify({title: 'Whatsapp number not available !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }

}