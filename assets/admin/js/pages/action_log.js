
$(document).ready(function() {

     
    oTable = $('#actionlogtable').DataTable
    ({
      "language": {
        "lengthMenu": "_MENU_"
      },
      "pageLength": 10,
      "columnDefs": [{
        'orderable': false,
        'targets': [0,3,-1]
      }],
      "order": [], //Initial no order.
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": SITE_URL+"action-log/listing",
        "type": "POST",
        "data": function ( data ) {
          data.actiontype = $('#actiontype').val();
          data.module = $('#module').val();
          data.startdate = $('#startdate').val();
          data.enddate = $('#enddate').val();
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
    oTable.ajax.reload(null,false);
  }
  function clearLog(clear=''){
    swal({    
      title: "Are you sure want to clear logs?",
      type: "warning",   
      showCancelButton: true,   
      confirmButtonColor: "#DD6B55",   
      confirmButtonText: "Yes, clear it!",   
      closeOnConfirm: false }, 
      function(isConfirm){   
        if (isConfirm) {   
          var param = {type:'all'};
          
          if(clear=="with_filter"){
            var actiontype = $('#actiontype').val();
            var module = ($('#module').val()!=null?$('#module').val():"");
            // module = (module!="null"?module:"");
            var startdate = $('#startdate').val();
            var enddate = $('#enddate').val();
          
            param = {type:'with_filter',actiontype:actiontype,module:module,startdate:startdate,enddate:enddate};
          }
          $.ajax({
            url: SITE_URL+"action-log/clear-logs",
            type: 'POST',
            data: param,
            dataType: 'json',
            async: false,
            success: function(response){
              swal.close();
              oTable.ajax.reload(null,false);
            },
            complete: function(){
            },
            error: function(xhr) {
              //alert(xhr.responseText);
            },
          });
        }
    });
  }
  function exportToExcelActionLogs(){
  
    var actiontype = $('#actiontype').val();
    var module = ($('#module').val()!=null)?$('#module').val():"";
    var startdate = $('#startdate').val();
    var enddate = $('#enddate').val();
    
    var totalRecords =$("#actionlogtable").DataTable().page.info().recordsDisplay;
    $.skylo('end');
    if(totalRecords != 0){
      window.location= SITE_URL+"action-log/exportToExcelActionLogs?actiontype="+actiontype+"&module="+module+"&startdate="+startdate+"&enddate="+enddate;
    }else{
      new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }
    
  }