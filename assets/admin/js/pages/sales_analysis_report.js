$(document).ready(function() {

    $('#datepicker-range').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        todayBtn:"linked",
        // orientation:"bottom",
        autoclose: true,
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

    getsalesanalysisreportdata();
});

function applyFilter(){
    getsalesanalysisreportdata();
}

function getsalesanalysisreportdata(){

    var uurl = SITE_URL+"sales-analysis-report/getsalesanalysisreportdata";
    var formData = new FormData($('#salesanalysisform')[0]);
    $.ajax({
        url: uurl,
        type: 'POST',
        data: formData,
        //dataType: "json",
        //async: false,
        beforeSend: function(){
            $('.mask').show();
            $('#loader').show();
        },
        success: function(response){
            
            var dataObject = $.parseJSON(response);
            if ( $.fn.DataTable.isDataTable('#salesanalysisreporttable') ) {
            $('#salesanalysisreporttable').DataTable().destroy();
            }
            $('#salesanalysisreporttable').empty();
            
            table = $('#salesanalysisreporttable').DataTable({
    
                "data": dataObject.DATA,
                "columns": dataObject.COLUMNS,
                "language": {
                "lengthMenu": "_MENU_"
                },
                
                "destroy": true,
                "pageLength": 50,
                "columnDefs": [{
                'orderable': false,
                'targets': []
                }],
                "order": [], //Initial no order.
            });
            $('.dataTables_filter input').attr('placeholder','Search...');
    
            $('.panel-ctrls.panel-tbl').html('');
            $('.panel-footer').html('');
            $('.dataTables_filter input').attr('placeholder','Search...');
    
    
            //DOM Manipulation to move datatable elements integrate to panel
            $('.panel-ctrls.panel-tbl').append($('.dataTables_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center form-group");
            $('.panel-ctrls.panel-tbl').append("<i class='separator'></i>");
            $('.panel-ctrls.panel-tbl').append($('.dataTables_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center form-group");
    
            $('.panel-footer').append($(".dataTable+.row"));
            $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-md");
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
        complete: function(){
            $('.mask').hide();
            $('#loader').hide();
        },
        cache: false,
        contentType: false,
        processData: false
    });
}

function exporttoexcelsalesanalysisreport(){

  var employee = ($('#employee').val()!=null?$('#employee').val():'');
  var product = ($('#product').val()!=null?$('#product').val():'');
  var fromdate = $('#startdate').val();
  var todate = $('#enddate').val();

  var totalRecords =$("#salesanalysisreporttable").DataTable().page.info().recordsDisplay;
  if(totalRecords != 0){
    window.location= SITE_URL+"sales-analysis-report/exporttoexcelsalesanalysisreport?employee="+employee+"&product="+product+"&fromdate="+fromdate+"&todate="+todate;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }

}
function exporttopdfsalesanalysisreport(){

    var employee = ($('#employee').val()!=null?$('#employee').val():'');
    var product = ($('#product').val()!=null?$('#product').val():'');
    var fromdate = $('#startdate').val();
    var todate = $('#enddate').val();

    var totalRecords =$("#salesanalysisreporttable").DataTable().page.info().recordsDisplay;
    $.skylo('end');
    if(totalRecords != 0){
  
      window.location= SITE_URL+"sales-analysis-report/exporttopdfsalesanalysisreport?employee="+employee+"&product="+product+"&fromdate="+fromdate+"&todate="+todate;
    }else{
      new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }
} 
function printsalesanalysisreport(){

    var employee = $('#employee').val() || [];
    employee = employee.join(',');
    var product = $('#product').val() || [];
    product = product.join(',');
    var fromdate = $('#startdate').val();
    var todate = $('#enddate').val();

    var totalRecords =$("#salesanalysisreporttable").DataTable().page.info().recordsDisplay;
      $.skylo('end');
      if(totalRecords != 0){
        var uurl = SITE_URL + "sales-analysis-report/printsalesanalysisreport";
        $.ajax({
          url: uurl, 
          type: 'POST',
          data: {fromdate:fromdate,todate:todate,employee:employee,product:product},
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