$(document).ready(function() {

    oTable = $('#pendingcourierchargestable').DataTable
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
        'serverSide': true,//Feature control DataTables' server-side processing mode.
        // Load data for the table's content from an Ajax source
        "ajax": {
          	"url": SITE_URL+'pendingcouriercharges/listing',
          	"type": "POST",
          	"data": function ( data ) {
                data.courierid = $('#courierid').val();
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

});
$('#courierid').on('change', function (e) {
  oTable.ajax.reload(null,false);
});

function exportpaymentreport(){
  
  var courierid = $('#courierid').val();
  
  var totalRecords =$("#pendingcourierchargestable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"pendingcouriercharges/exportpaymentreport?courierid="+courierid;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  
}

function editshippingamount(id){
  $('#shippingorderid').val(id);
  PNotify.removeAll();
  $('#myModal').modal('show');
  $("#expenses_div").removeClass("has-error is-focused");
}

function checkvalidation(){
  var shippingorderid = $('#shippingorderid').val();
  var expenses = $('#expenses').val();

  var uurl = SITE_URL+"Pendingcouriercharges/editshippingamount";
  var isvalidexpenses = 0;

  if(expenses == 0){
    $("#expenses_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select country !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidexpenses = 0;
  }else { 
    $("#expenses_div").removeClass("has-error is-focused");
    isvalidexpenses = 1;
  }

  if(isvalidexpenses==1){
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {shippingorderid:shippingorderid,expenses:expenses},
      //async: false,
      beforeSend: function(){
        $('.mask').show();
        $('#loader').show();
      },
      success: function(response){
        if(response==1){
          new PNotify({title: "Courier expenses successfully updated.",styling: 'fontawesome',delay: '3000',type: 'success'});
          setTimeout(function() { window.location.reload(); }, 1500);
          
        }else{
          new PNotify({title: 'Courier expenses not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
}