
$(document).ready(function() {
    
    var oTable = $('#producttable').dataTable
      ({
        "processing": true,//Feature control the processing indicator.
        "language": {
          "lengthMenu": "_MENU_"
        },
        "pageLength": 10,
        "columnDefs": [{
          'orderable': false,
          'targets': [0,-1,-2]
        }],
        "order": [], //Initial no order.
        'serverSide': true,//Feature control DataTables' server-side processing mode.
        // Load data for the table's content from an Ajax source
        "ajax": {
          "url": SITE_URL+"product-section/productslisting",
          "type": "POST",
          "data" :function ( data ) {
            data.sectionid = $("#productsectionid").val();
          },
          beforeSend: function(){
            $('.mask').show();
            $('#loader').show();
          },
          complete: function(){
            $('.mask').hide();
            $('#loader').hide();
            loadpopover();
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
function displaydescription(id){
    var description = $('#description'+id).html();
    $('.modal-body').html(description.replace(/&nbsp;/g, ' '));
}