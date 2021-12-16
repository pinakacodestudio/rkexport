$(document).ready(function() {
    list("feedbacktable","feedback/listing",[0,-1,-2]);
    // $('#feedbacktable').DataTable({
    //     "language": {
    //         "lengthMenu": "_MENU_"
    //     },
    //     "columnDefs": [ {
    //       "targets": [0,-1,-2],
    //       "orderable": false
    //     } ],
    //     responsive: true,
    //     "bDestroy": true,
    // });
    $('.dataTables_filter input').attr('placeholder','Search...');


    //DOM Manipulation to move datatable elements integrate to panel
    $('.panel-ctrls').append($('.dataTables_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls').append("<i class='separator'></i>");
    $('.panel-ctrls').append($('.dataTables_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('.panel-footer').append($(".dataTable+.row"));
    $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-md");
});
function displaymessage(id){
    var message = $('#message'+id).html();
    $('.modal-body').html(message.replace(/&nbsp;/g, ' '));
}
