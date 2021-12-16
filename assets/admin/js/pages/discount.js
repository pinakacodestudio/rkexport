
$(document).ready(function() {

    //list("discounttable","discount/listing",[0]);
    oTable = $('#discounttable').DataTable
      ({
        "processing": true,//Feature control the processing indicator.
        "language": {
          "lengthMenu": "_MENU_"
        },
        "pageLength": 50,
        "columnDefs": [{
          'orderable': false,
          'targets': [0,-3]
        }],
        "order": [], //Initial no order.
        'serverSide': true,//Feature control DataTables' server-side processing mode.
        // Load data for the table's content from an Ajax source
        "ajax": {
          	"url": SITE_URL+'discount/listing',
          	"type": "POST",
          	"data": function ( data ) {
                data.customerid = $('#customerid').val();
            },
            beforeSend: function(){
              $('.mask').show();
              $('#loader').show();
            },
            complete: function(){
              $('.mask').hide();
              $('#loader').hide();
            },
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
$('#customerid').on('change', function (e) {
  oTable.ajax.reload(null,false);
});

function exportpaymentreport(){
  
  var customerid = $('#customerid').val();
  
  var totalRecords =$("#discounttable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"discount/exportpaymentreport?customerid="+customerid;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  
}

