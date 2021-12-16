
$(document).ready(function() {
    oTable = $('#salescommissionreporttable').DataTable({
        "language": {
          "lengthMenu": "_MENU_"
        },
        drawCallback: function () {
          loadpopover('left');
        },
        "pageLength": 10,
        "columnDefs": [{
          'orderable': false,
          'targets': [0]
        },{"targets": [5,6,7,8,9,10], className: "text-right"}],
        "order": [], //Initial no order.
        'serverSide': true,//Feature control DataTables' server-side processing mode.
        // Load data for the table's content from an Ajax source
        "ajax": {
          "url": SITE_URL+"sales-commission-report/listing",
          "type": "POST",
          "data": function ( data ) {
            data.employeeid = $('#employeeid').val();
            data.fromdate = $('#startdate').val();
            data.todate = $('#enddate').val();
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

function exportsalescommissionreport(type="excel"){
  
  var employeeid = $('#employeeid').val();
  var fromdate = $('#startdate').val();
  var todate = $('#enddate').val();
  
  var totalRecords =$("#salescommissionreporttable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){

    if(type=="pdf"){
      window.location= SITE_URL+"sales-commission-report/exporttopdfsalescommissionreport?employeeid="+employeeid+"&fromdate="+fromdate+"&todate="+todate;
    }else{
      window.location= SITE_URL+"sales-commission-report/exporttoexcelsalescommissionreport?employeeid="+employeeid+"&fromdate="+fromdate+"&todate="+todate;
    }
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  
}

function printsalescommissionreport(){

  var employeeid = $('#employeeid').val();
  var fromdate = $('#startdate').val();
  var todate = $('#enddate').val();
  
  var totalRecords =$("#salescommissionreporttable").DataTable().page.info().recordsDisplay;
    $.skylo('end');
    if(totalRecords != 0){
      var uurl = SITE_URL + "sales-commission-report/printsalescommissionreport";
      $.ajax({
        url: uurl, 
        type: 'POST',
        data: {fromdate:fromdate,todate:todate,employeeid:employeeid},
        //dataType: 'json',
        async: false,
        beforeSend: function() {
            $('.mask').show();
            $('#loader').show();
        },
        success: function(response) {
            
          var html = JSON.parse(response);

          printdocument(html);
        },
        error: function(xhr) {
            //alert(xhr.responseText);
        },
        complete: function() {
            $('.mask').hide();
            $('#loader').hide();
        },
      });
    }else{
      new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }
}