$(document).ready(function() {

    //list("membertable","member/listing",[0,-1]);

    oTable = $('#vendortable').dataTable({
      "language": {
        "lengthMenu": "_MENU_"
      },
      "pageLength": 10,
      "columnDefs": [{
        'orderable': false,
        'targets': [0,2,-1]
      }],
      "order": [], //Initial no order.
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": SITE_URL+"vendor/listing",
        "type": "POST",
        "data": function ( data ) {
            data.startdate = $('#startdate').val();
            data.enddate = $('#enddate').val();
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
      // todayHighlight: true,
      format: 'dd/mm/yyyy',
      autoclose: true,
      todayBtn:"linked",
      /* startDate: new Date(), */
    });

    $('#balancedate').datepicker({
      todayHighlight: true,
      format: 'dd/mm/yyyy',
      /*orientation:"bottom",*/
      container:'#openingbalanceModal',
      autoclose: true,
      todayBtn:"linked",
      
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
  
});
function applyFilter(){
  oTable.fnDraw();
}

function setopeningbalance(memberid,balanceid,balancedate,balance){
  PNotify.removeAll();
  $('#openingbalanceModal').modal('show');
  $('#openingbalanceid').val(balanceid);
  $('#memberid').val(memberid);
  $('#balancedate').val(balancedate);
  $('#balance').val(balance);
  $("#balancedate_div").removeClass("has-error is-focused");
  $("#balance_div").removeClass("has-error is-focused");
}

function checkopeningbalancevalidation(){

  var balancedate = $("#balancedate").val();
  var balance = $("#balance").val();

  var isvalidbalancedate = isvalidbalance = 0;
  
  PNotify.removeAll();
  
  if(balancedate==''){
    $("#balancedate_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select balance date !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }else{
    $("#balancedate_div").removeClass("has-error is-focused");
    isvalidbalancedate = 1;
  }
  if(balance==''){
    $("#balance_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter balance !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }else{
    $("#balance_div").removeClass("has-error is-focused");
    isvalidbalance = 1;
  }
  if(isvalidbalancedate==1 && isvalidbalance==1){
    
    var formData = new FormData($('#openingbalanceform')[0]);

    var uurl = SITE_URL+"opening-balance/setopeningbalance";
    
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
            new PNotify({title: "Opening balance successfully updated.",styling: 'fontawesome',delay: '3000',type: 'success'});
            $('#openingbalanceModal').modal('hide');
            oTable.fnDraw();
        }else{
          new PNotify({title: "Opening balance not updated !",styling: 'fontawesome',delay: '3000',type: 'error'});
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

function generateQRCode(vendorid){
  var uurl = SITE_URL+"vendor/generateQRCode";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {vendorid:vendorid},
    async: false,
    success: function(response){
      
      var obj = JSON.parse(response);
      var vendorname = obj['vendordata']['name'];
      var qrcode = obj['qrcodedata'];
      $('#myModal .modal-title').html(ucwords(vendorname)+' - QR Code');
      if(qrcode!=""){
        $("#qrcodeimage").html("<center><img src='"+qrcode+"' class='img-thumbnail'></center>");
      }
      $('#myModal').modal('show');
    },
    error: function(xhr) {
      //alert(xhr.responseText);
    },
  }); 
}