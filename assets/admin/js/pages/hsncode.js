$(document).ready(function() {
    $('#hsncodetable').DataTable({
        "language": {
            "lengthMenu": "_MENU_"
        },
        "pageLength": 50,
        "scrollCollapse": true,
        "scrollY": "500px",
        "order": [], //Initial no order.
        'serverSide': true, //Feature control DataTables' server-side processing mode.
        // Load data for the table's content from an Ajax source
        "columnDefs": [{
            "targets": [-1, -2],
            "orderable": false
        }],
        responsive: true,
    });
    $('.dataTables_filter input').attr('placeholder', 'Search...');


    //DOM Manipulation to move datatable elements integrate to panel
    $('.panel-ctrls').append($('.dataTables_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls').append("<i class='separator'></i>");
    $('.panel-ctrls').append($('.dataTables_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('.panel-footer').append($(".dataTable+.row"));
    $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-md");
});