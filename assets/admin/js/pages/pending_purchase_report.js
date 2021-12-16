
$(document).ready(function() {
    
    //list("ordertable","Order/listing",[0,-1]);
    oTable = $('#pendingpurchasereporttable').DataTable
      ({
        "language": {
          "lengthMenu": "_MENU_"
        },
        "pageLength": 50,
        /* "scrollCollapse": true,
        "scrollY": "500px",
        "scrollX": true, */
        "columnDefs": [{
          'orderable': false,
          'targets': []
        },{'className': 'text-right','targets': [-1,-2,-3]}],
        "order": [], //Initial no order.
        'serverSide': true,//Feature control DataTables' server-side processing mode.
        // Load data for the table's content from an Ajax source
        "ajax": {
          "url": SITE_URL+"pending-purchase-report/listing",
          "type": "POST",
          "data": function ( data ) {
            data.startdate = $('#startdate').val();
            data.enddate = $('#enddate').val();
            data.productid = $('#productid').val();
            data.vendorid = $('#vendorid').val();
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
            loadpopover();
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
      todayBtn:"linked",
      format: 'dd/mm/yyyy',
      autoclose: true
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
    oTable.ajax.reload(null, false);
}

function exporttoexcelpendingpurchasereport(){
  
  var startdate = $('#startdate').val();
  var enddate = $('#enddate').val();
  var productid = ($('#productid').val()!=null)?$('#productid').val().join(","):"";
  var vendorid = ($('#vendorid').val()!=null)?$('#vendorid').val().join(","):"";
  
  var totalRecords =$("#pendingpurchasereporttable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"pending-purchase-report/exporttoexcelpendingpurchasereport?startdate="+startdate+"&enddate="+enddate+"&productid="+productid+"&vendorid="+vendorid;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  
}

function exporttopdfpendingpurchasereport(){
  
  var startdate = $('#startdate').val();
  var enddate = $('#enddate').val();
  var productid = ($('#productid').val()!=null)?$('#productid').val().join(","):"";
  var vendorid = ($('#vendorid').val()!=null)?$('#vendorid').val().join(","):"";
  
  var totalRecords =$("#pendingpurchasereporttable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"pending-purchase-report/exporttopdfpendingpurchasereport?startdate="+startdate+"&enddate="+enddate+"&productid="+productid+"&vendorid="+vendorid;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  
}

function printpendingpurchasereport(){

  var startdate = $('#startdate').val();
  var enddate = $('#enddate').val();
  var productid = $('#productid').val();
  var memberid = $('#memberid').val();
  
  var totalRecords =$("#pendingpurchasereporttable").DataTable().page.info().recordsDisplay;
    $.skylo('end');
    if(totalRecords != 0){
      var uurl = SITE_URL + "pending-purchase-report/printpendingpurchasereport";
      $.ajax({
        url: uurl, 
        type: 'POST',
        data: {startdate:startdate,enddate:enddate,productid:productid,memberid:memberid},
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