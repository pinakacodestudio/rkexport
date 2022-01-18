$(document).ready(function() {
    
    oTable = $('#gstr1reporttable').DataTable
    ({
    "language": {
        "lengthMenu": "_MENU_"
    },
    "pageLength": 10,
    "columnDefs": [{
        'orderable': false,
        'targets': []
    },
    { targets: [5,7,8,9,10,11,12], className: "text-right" }],
    "order": [], //Initial no order.
    "pendingshipping": [], //Initial no pendingshipping.
    'serverSide': true,//Feature control DataTables' server-side processing mode.
    // Load data for the table's content from an Ajax source
    "ajax": {
        "url": SITE_URL+'gstr1-report/listing',
        "type": "POST",
        "data": function ( data ) {
            data.vendorid = $('#vendorid').val();
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
    "footerCallback": function ( row, data, start, end, display ) {
        var api = this.api(),data;
        
        if(!$.isEmptyObject(data)){
          $.footer = '';
          $('.footercls').remove();
          if($('#gstr1reporttable tfoot').length==0){
            $.footer += '<tfoot>';
          }
          
          // if($('.footercls').length==0){
            $.footer += '<tr class="footercls">';
        
            $.footer += '<th colspan="9" class="totalrows text-right">Total</th>';
         
            this.api().columns().each(function () {
              var column = this;
              if(column[0].length > 0){
                for (var index = 9; index < column[0].length; index++) {
  
                    // Remove the formatting to get integer data for summation
                    var intVal = function (i) {
                        return typeof i === 'string' ?
                            i.replace(/[\L,]/g, '') * 1 :
                            typeof i === 'number' ?
                            i : 0;
                    };
  
                    // Total over all pages
                    var total = api
                        .column( column[0][index] )
                        .data()
                        .reduce( function (a, b) {
                            var a = intVal(a) || 0;
                            var b = intVal(b) || 0;
                            return parseFloat(a) + parseFloat(b);
                    }, 0 );
  
                    // Total over this page
                    var pageTotal = api
                        .column( column[0][index], { page: 'current'} )
                        .data()
                        .reduce( function (a, b) {
                            var a = intVal(a) || 0;
                            var b = intVal(b) || 0;
                            return parseFloat(a) + parseFloat(b);
                    }, 0 );
                  
                    $.footer += '<th class="totalrows text-right" style="padding:10px 10px;">'+CURRENCY_CODE+''+ format.format(pageTotal) +'</th>';
                }
              }
            });
            $.footer += '</tr>';
          // }
          if($('#gstr1reporttable tfoot').length==0){
            $.footer += '</tfoot>';
            $('#gstr1reporttable tbody').after($.footer);
          }else{
            $('#gstr1reporttable tfoot').prepend($.footer);
          }
        }else{
          $('#gstr1reporttable tfoot').remove();
        }
  
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
      todayBtn: 'linked'
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
    $('.footercls').remove();
    oTable.ajax.reload(null, false);
}
function exporttoexcelgstr1report(){
  
  var vendorid = $('#vendorid').val();
  var fromdate = $('#startdate').val();
  var todate = $('#enddate').val();
  
  var totalRecords =$("#gstr1reporttable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"gstr1-report/exporttoexcelgstr1report?vendorid="+vendorid+"&fromdate="+fromdate+"&todate="+todate;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  
}

function exporttopdfgstr1report(){
  
  var vendorid = $('#vendorid').val();
  var fromdate = $('#startdate').val();
  var todate = $('#enddate').val();
  
  var totalRecords =$("#gstr1reporttable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"gstr1-report/exporttopdfgstr1report?vendorid="+vendorid+"&fromdate="+fromdate+"&todate="+todate;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  
}

function printGSTR1Report(){

    var vendorid = $('#vendorid').val();
    var fromdate = $('#startdate').val();
    var todate = $('#enddate').val();

    var totalRecords =$("#gstr1reporttable").DataTable().page.info().recordsDisplay;
    $.skylo('end');
    if(totalRecords != 0){
      var uurl = SITE_URL + "gstr1-report/printGSTR1Report";
      $.ajax({
        url: uurl, 
        type: 'POST',
        data: {vendorid:vendorid,fromdate:fromdate,todate:todate},
        //dataType: 'json',
        async: false,
        beforeSend: function() {
            $('.mask').show();
            $('#loader').show();
        },
        success: function(response) {
            
          var data = JSON.parse(response);
          var html = data['content'];
        
          var frame1 = document.createElement("iframe");
          frame1.name = "frame1";
          frame1.style.position = "absolute";
          frame1.style.top = "-1000000px";
          document.body.appendChild(frame1);
          var frameDoc = frame1.contentWindow ? frame1.contentWindow : frame1.contentDocument.document ? frame1.contentDocument.document : frame1.contentDocument;
          frameDoc.document.open();
          frameDoc.document.write(html);
          frameDoc.document.close();
          setTimeout(function () {
            window.frames["frame1"].focus();
            window.frames["frame1"].print();
            document.body.removeChild(frame1);
          }, 500);
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