
$(document).ready(function() {
  
    oTable = $('#salescommissiontable').DataTable({
      "language": {
        "lengthMenu": "_MENU_"
      },
      "pageLength": 10,
      "columnDefs": [{
        'orderable': false,
        'targets': [0,3,-1,-2]
      },{"targets":[3], className: "text-center"}],
      "order": [], //Initial no order.
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": SITE_URL+"sales-commission/listing",
        "type": "POST",
        "data": function ( data ) {
          data.employeeid = $('#employeeid').val();
          data.commissiontype = $('#commissiontype').val();
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

function applyFilter(){
    oTable.ajax.reload(null,false);
}

function viewcommissiondetail(SalesCommissionId){
   
  if(SalesCommissionId > 0){
    
    var uurl = SITE_URL+"sales-commission/getSalesCommissionDataById";
   
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {salescommissionid:String(SalesCommissionId)},
      dataType: 'json',
      async: false,
      beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
      },
      success: function(response){

          if(response.length>0){
            var name = ""; 
            if(response[0]['commissiontype']==2){
              name = "Product Name";
            }else if(response[0]['commissiontype']==3){
              name = Member_label+" Name";
            }else if(response[0]['commissiontype']==4){
              name = "Range";
            }

            var HTML = '<table class="table table-bordered">\
                          <thead>\
                            <th>Sr. No.</th>\
                            <th>'+name+'</th>\
                            <th class="text-right">Commission (%)</th>\
                            <th class="text-center">GST</th>\
                          </thead><tbody>';
            for(var i = 0; i < response.length; i++) {

              var gst = (response[i]['gst']==1)?'With GST':'Without GST';
              HTML += '<tr>\
                          <td>'+(i+1)+'</td>\
                          <td>'+response[i]['reference']+'</td>\
                          <td class="text-right">'+response[i]['commission']+'</td>\
                          <td class="text-center">'+gst+'</td>\
                      </tr>';
            }
            HTML += '</tbody></table>';

            $('#CommissionTypeModal .modal-title').html('View '+response[0]['typename']+' Details of '+ucwords(response[0]['employeename']));
            $('#CommissionTypeModal .modal-body').html(HTML);
            $('#CommissionTypeModal').modal('show');
          }
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
}
