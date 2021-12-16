$(document).ready(function() {
  
    oTable = $('#salespersonmembertable').DataTable({
      "language": {
        "lengthMenu": "_MENU_"
      },
      "pageLength": 10,
      drawCallback: function () {
        loadpopover();
      },
      "columnDefs": [{
        'orderable': false,
        'targets': [0,-1,-2]
      }],
      "order": [], //Initial no order.
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": SITE_URL+"sales-person-members/listing",
        "type": "POST",
        "data": function ( data ) {
          data.employeeid = $('#employeeid').val();
          data.channelid = $('#channelid').val();
          data.memberid = $('#memberid').val();
        },
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
        },
        error: function(xhr) {
          //alert(xhr.responseText);
        },
        complete: function(){
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
    
    $(function () {
        $('.panel-heading.filter-panel').click(function() {
            $(this).find('.button-icon span').html($(this).find('.button-icon span').text() == 'keyboard_arrow_down' ? 'keyboard_arrow_up' : 'keyboard_arrow_down');
            //$(this).children().toggleClass(" ");
            $(this).next().slideToggle({duration: 200});
            $(this).toggleClass('panel-collapsed');
            
            return false;
        });
    });
    
});
/****EMPLOYEE CHANGE EVENT****/
$(document).on('change', '#channelid', function() { 
    getmember(this.value);
});
function applyFilter(){
    oTable.ajax.reload(null,false);
}
function getmember(channelid){
    
    $('#memberid')
        .find('option')
        .remove()
        .end()
        .append('')
        .val('')
    ;
    $('#memberid').selectpicker('refresh');
  
    if(channelid!=0){
        var uurl = SITE_URL+"member/getmembers";
        
        $.ajax({
            url: uurl,
            type: 'POST',
            data: {channelid:String(channelid)},
            dataType: 'json',
            async: false,
            success: function(response){
    
                for(var i = 0; i < response.length; i++) {
                    $('#memberid').append($('<option>', { 
                        value: response[i]['id'],
                        text : response[i]['name']
                    }));
                }
            },
            error: function(xhr) {
            //alert(xhr.responseText);
            },
        });
    }
    $('#memberid').selectpicker('refresh');
}