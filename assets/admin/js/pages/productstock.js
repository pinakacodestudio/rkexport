var oTable;
$(document).ready(function() {

    oTable = $('#productstocktable').DataTable
      ({
        "processing": true,//Feature control the processing indicator.
        "language": {
          "lengthMenu": "_MENU_"
        },
        "pageLength": 10,
        "columnDefs": [{
          'orderable': false,
          'targets': [0,-1,-2,-3],
        }],
        "order": [], //Initial no order.
        'serverSide': true,//Feature control DataTables' server-side processing mode.
        // Load data for the table's content from an Ajax source
        "ajax": {
          "url": SITE_URL+"productstock/listing",
          "type": "POST",
          "data": function ( data ) {
                data.productid = $('#productid').val();
            }
        },
        "columns": [
            { "data": "row" },
            { "data": "productname" },
            { "data": "stock" },
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

$('#productid').on('change', function (e) {
  oTable.ajax.reload(null,false);
});

function displaystock(productstockid){

  var uurl = SITE_URL+"productstock/getProductStockByCarmodel";
      
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {productstockid:productstockid},
    dataType: 'json',
    //async: false,
    beforeSend: function(){
      $('.mask').show();
      $('#loader').show();
    },
    success: function(response){
      $.html = '';
      if(response.length>0){
        for (var i = 0; i < response.length; i++) {
          $.html += '<tr> \
                      <td>'+(i+1)+'</td> \
                      <td>'+response[i]['carmodel']+'</td> \
                      <td>'+response[i]['minimumstock']+'</td> \
                      <td>'+response[i]['currentstock']+'</td> \
                    </tr>'  
        }
      }
      $('#stocktable tbody').html($.html);
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