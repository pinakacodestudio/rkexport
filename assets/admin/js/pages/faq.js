var oTable;
$(document).ready(function() {

    oTable = $('#faqtable').DataTable
      ({
        "processing": true,//Feature control the processing indicator.
        "language": {
          "lengthMenu": "_MENU_"
        },
        "pageLength": 50,
        "columnDefs": [{
          'orderable': false,
          'targets': [0,-1,-2,-3],
        }],
        "order": [], //Initial no order.
        'serverSide': true,//Feature control DataTables' server-side processing mode.
        // Load data for the table's content from an Ajax source
        "ajax": {
          "url": SITE_URL+"faq/listing",
          "type": "POST",
          "data": function ( data ) {
                data.faqtype = $('#faqtype').val();
                data.productid = $('#productid').val();
            }
        },
        "columns": [
            { "data": "row" },
            { "data": "productname" },
            { "data": "question" },
            { "data": "faq" },
            { "data": "action" },
            { "data": "checkbox" },
        ]
      });

    $('.dataTables_filter input').attr('placeholder','Search...');


    //DOM Manipulation to move datatable elements integrate to panel
    $('.panel-ctrls').append($('.dataTables_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls').append("<i class='separator'></i>");
    $('.panel-ctrls').append($('.dataTables_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('.panel-footer').append($(".dataTable+.row"));
    $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-md");
});
function displayfaq(id){
    var question = $('#question'+id).html();
    var answer = $('#answer'+id).html();
    $.html = '<div class="col-md-12"> \
                <b>'+question+'</b> \
                '+answer+' \
            </div>';
    $('.modal-body').html($.html.replace(/&nbsp;/g, ' '));
}
$('#productid').on('change', function (e) {
  oTable.ajax.reload(null,false);
});
$('#faqtype').on('change', function (e) {
    $('#productid').val(0);
    $('#productid').selectpicker('refresh');
    if($(this).val()==2){
        $('#product_div').show();
    }else{
        $('#product_div').hide();
    }
    oTable.ajax.reload(null,false);
});