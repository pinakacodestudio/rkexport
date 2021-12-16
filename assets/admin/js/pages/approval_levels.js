$(document).ready(function() {
    
    oTable = $('#approvallevelstable').DataTable({
        "language": {
            "lengthMenu": "_MENU_"
        },
        "pageLength": 10,
        "columnDefs": [{
            'orderable': false,
            'targets': [0,2,-1,-2]
        }],
        "order": [], //Initial no order.
        'serverSide': true,//Feature control DataTables' server-side processing mode.
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": SITE_URL+"approval-levels/listing",
            "type": "POST",
            "data": function ( data ) {
               
            },
            beforeSend: function(){
                $('.mask').show();
                $('#loader').show();
            },
            error: function(xhr) {
            //alert(xhr.responseText);
            },
            complete: function(e){
                $('.mask').hide();
                $('#loader').hide();
            },
        },
    });

    $('.dataTables_filter input').attr('placeholder','Search...');


    //DOM Manipulation to move datatable elements integrate to panel
    $('.panel-ctrls.panel-tbl').append($('.dataTables_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls.panel-tbl').append("<i class='separator'></i>");
    $('.panel-ctrls.panel-tbl').append($('.dataTables_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('.panel-footer').append($(".dataTable+.row"));
    $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-md");
});


function applyFilter(){
    oTable.ajax.reload();
}

function getapprovallevelsmapping(id){

    var uurl = SITE_URL+"approval-levels/getapprovallevelsmapping";
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {id:String(id)},
      async: false,
      success: function(response){
        var JSONObject = JSON.parse(response);
        if(JSONObject.length > 0){
            var html = "";
            for(var i = 0; i < JSONObject.length; i++) {
                html += '<tr>\
                            <td class="text-center">'+JSONObject[i]['level']+'</td>\
                            <td>'+JSONObject[i]['designation']+'</td>\
                            <td class="text-center">'+JSONObject[i]['isenable']+'</td>\
                            <td class="text-center">'+JSONObject[i]['sendemail']+'</td>\
                        </tr>';
            }
        }
        $('.modal-title').html("Approval Level Details of "+JSONObject[0]['modulename']);
        $('#details').html(html);
      },
      error: function(xhr) {
      //alert(xhr.responseText);
      },
    });
  }