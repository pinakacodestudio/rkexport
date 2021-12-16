
$(document).ready(function() {
    $('#blogtable').DataTable({
        "language": {
            "lengthMenu": "_MENU_"
        },
        "columnDefs": [ {
          "targets": [-1,-2,-3,-4,-5],
          "orderable": false
        } ],
        responsive: true,
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