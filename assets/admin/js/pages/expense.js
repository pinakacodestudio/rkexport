var oTable;
$(document).ready(function() {

  oTable = $('#expense').DataTable({
    "language": {
      "lengthMenu": "_MENU_"
    },
    "pageLength": 10,
    // "scrollCollapse": true,
    // "scrollY": "500px",
    "columnDefs": [{
      'orderable': false,
      'targets': [0,-1,-2]
    }],
    //Initial no order.
    'serverSide': true,//Feature control DataTables' server-side processing mode.
    // Load data for the table's content from an Ajax source
    "ajax": {
      "url": SITE_URL+"expense/listing",
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
  
      $('#datepicker-range').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayBtn:"linked"
      });
  
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
    oTable.draw();
  }

  function changeexpensestatus(status, expenseid){
    var uurl = SITE_URL+"expense/update-status";
        if(expenseid!=''){
              swal({    title: "Are you sure to change status?",
                type: "warning",   
                showCancelButton: true,   
                confirmButtonColor: "#DD6B55",   
                confirmButtonText: "Yes, change it!",   
                closeOnConfirm: false }, 
                function(isConfirm){   
                  if (isConfirm) {   
                    $.ajax({
                        url: uurl,
                        type: 'POST',
                        data: {status:status,expenseid:expenseid},
                        beforeSend: function(){
                          $('.mask').show();
                          $('#loader').show();
                          },
                        success: function(response){
                          if(response==1){
                              location.reload();
                           }
                         },
                         complete: function(){
                          $('.mask').hide();
                          $('#loader').hide();
                        },
                        error: function(xhr) {
                        //alert(xhr.responseText);
                        }
                      });  
                    }
                  });

            }           
}

 











  


  