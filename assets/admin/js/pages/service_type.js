$(document).ready(function() {
  oTable = $('#servicetypetable').dataTable({
    "language": {
      "lengthMenu": "_MENU_"
    },
    
    "pageLength": 10,
    "columnDefs": [{
      'orderable': false,
      'targets': [-1,-2]
    }],
    "order": [], //Initial no order.
    
  });
  $('.dataTables_filter input').attr('placeholder','Search...');

  //DOM Manipulation to move datatable elements integrate to panel
  $('.panel-ctrls.panel-tbl').append($('.dataTables_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
  $('.panel-ctrls.panel-tbl').append("<i class='separator'></i>");
  $('.panel-ctrls.panel-tbl').append($('.dataTables_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

  $('.panel-footer').append($(".dataTable+.row"));
  $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-md");
});