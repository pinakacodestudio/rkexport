var oTable;
$(document).ready(function() {
	 //$.fn.raty.defaults.path = SITE_URL+'../assets/admin/plugins/raty-master/images';
	
    oTable = $('#productreviewtable').DataTable
      ({
        "processing": true,//Feature control the processing indicator.
        "language": {
          "lengthMenu": "_MENU_"
        },
        "pageLength": 50,
        "columnDefs": [{
          'orderable': false,
          'targets': [-1,-2,-4,-5],
        }],
        "order": [], //Initial no order.
        'serverSide': true,//Feature control DataTables' server-side processing mode.
        // Load data for the table's content from an Ajax source
        "ajax": {
          "url": SITE_URL+"productreview/listing",
          "type": "POST",
          "data": function ( data ) {
                data.productid = $('#productid').val();
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

function displayproductreview(id){
    var message = $('#message'+id).html();
    $('.modal-body').html(message.replace(/&nbsp;/g, ' '));

}
$('#productid').on('change', function (e) {
  oTable.ajax.reload(null,false);
});