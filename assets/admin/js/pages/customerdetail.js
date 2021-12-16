
$(document).ready(function() {
    $('#billingaddresstable').DataTable({
        "language": {
            "lengthMenu": "_MENU_"
        },
        "columnDefs": [ {
          "targets": [0],
          "orderable": false
        } ],
        responsive: true,
    });
    $('#billingaddresstable_filter input').attr('placeholder','Search...');


    //DOM Manipulation to move datatable elements integrate to panel
    $('.panel-ctrls1').append($('#billingaddresstable_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls1').append("<i class='separator'></i>");
    $('.panel-ctrls1').append($('#billingaddresstable_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('.panel-footer').append($(".dataTable+.row"));
    $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-md");

    $('#ordertable').DataTable({
        "language": {
            "lengthMenu": "_MENU_"
        },
        "columnDefs": [ {
          "targets": [0],
          "orderable": false
        } ],
        responsive: true,
    });
    $('#ordertable_filter input').attr('placeholder','Search...');


    //DOM Manipulation to move datatable elements integrate to panel
    $('.panel-ctrls2').append($('#ordertable_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls2').append("<i class='separator'></i>");
    $('.panel-ctrls2').append($('#ordertable_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('.panel-footer').append($(".dataTable+.row"));
    $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-md");

    $('#paymentdetailtable').DataTable({
        "language": {
            "lengthMenu": "_MENU_"
        },
        "columnDefs": [ {
          "targets": [0],
          "orderable": false
        } ],
        responsive: true,
    });
    $('#paymentdetailtable_filter input').attr('placeholder','Search...');


    //DOM Manipulation to move datatable elements integrate to panel
    $('.panel-ctrls3').append($('#paymentdetailtable_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls3').append("<i class='separator'></i>");
    $('.panel-ctrls3').append($('#paymentdetailtable_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('.panel-footer').append($(".dataTable+.row"));
    $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-md");

    $('#carttable').DataTable({
        "processing": true,
        "language": {
            "lengthMenu": "_MENU_"
        },
        "columnDefs": [{
          "targets": [0,-2],
          "orderable": false
        }],
        "order": [], //Initial no order.
        'serverSide': true,//Feature control DataTables' server-side processing mode.
        // Load data for the table's content from an Ajax source
        "ajax": {
          "url": SITE_URL+"customer/cartlisting",
          "type": "POST",
          "data" :function ( data ) {
                data.customerid = $("#customerid").val();
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
        responsive: true,
    });
    $('#carttable_filter input').attr('placeholder','Search...');


    //DOM Manipulation to move datatable elements integrate to panel
    $('.panel-ctrls4').append($('#carttable_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls4').append("<i class='separator'></i>");
    $('.panel-ctrls4').append($('#carttable_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('.panel-footer').append($(".dataTable+.row"));
    $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-md");

    
    
});