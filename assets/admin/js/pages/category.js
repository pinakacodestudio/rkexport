$(document).ready(function() {
    //list("categorytable","category/listing",[0,-1,-2]);

    oTable = $('#categorytable').DataTable({
        "language": {
            "lengthMenu": "_MENU_"
        },
        "pageLength": 10,
        "columnDefs": [{
            'orderable': false,
            'targets': [0, -1, -2]
        }],
        "order": [], //Initial no order.
        'serverSide': true, //Feature control DataTables' server-side processing mode.
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": SITE_URL + "category/listing",
            "type": "POST",
            "data": function(data) {
                data.maincategoryid = $("#categoryid").val();
            },
            beforeSend: function() {
                $('.mask').show();
                $('#loader').show();
            },
            error: function(xhr) {
                //alert(xhr.responseText);
            },
            complete: function(e) {
                $('.mask').hide();
                $('#loader').hide();

                if ($('#categoryid').val() != "" && e.responseJSON.data.length > 0) {
                    $('#btntype').show();
                } else {
                    $('#btntype').hide();
                }
            },
        },
    });
    $('.dataTables_filter input').attr('placeholder', 'Search...');
    //DOM Manipulation to move datatable elements integrate to panel
    $('.panel-ctrls').append($('.dataTables_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls').append("<i class='separator'></i>");
    $('.panel-ctrls').append($('.dataTables_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('.panel-footer').append($(".dataTable+.row"));
    $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-md");
    // $(function() {
    //     $('.panel-heading.filter-panel').click(function() {
    //         $(this).find('.button-icon span').html($(this).find('.button-icon span').text() == 'keyboard_arrow_down' ? 'keyboard_arrow_up' : 'keyboard_arrow_down');
    //         //$(this).children().toggleClass(" ");
    //         $(this).next().slideToggle({ duration: 200 });
    //         $(this).toggleClass('panel-collapsed');
    //         return false;
    //     });
    // });

    /* $('#categoryid').on('change', function (e) {

        if(this.value != ""){
            $('#btntype').show();
        }else{
            $('#btntype').hide();
        }
    }); */
});


function applyFilter() {
    oTable.ajax.reload();
}