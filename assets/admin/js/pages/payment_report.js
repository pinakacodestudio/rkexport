
$(document).ready(function() {
  
  oTable = $('#paymentreporttable').DataTable({
    "language": {
      "lengthMenu": "_MENU_"
    },
    "pageLength": 50,
    "columnDefs": [{
      'orderable': true,
      'targets': [7],
      'className': 'text-right'
    }],
    "order": [], //Initial no order.
    'serverSide': true,//Feature control DataTables' server-side processing mode.
    // Load data for the table's content from an Ajax source
    "ajax": {
      "url": SITE_URL+"payment-report/listing",
      "type": "POST",
      "async":false,
      "data": function ( data ) {
        data.startdate = $('#startdate').val();
        data.enddate = $('#enddate').val();
        data.paidfromchannelid = $('#paidfromchannelid').val();
        data.paidtochannelid = $('#paidtochannelid').val();
        data.frommemberid = ($('#frommemberid').val() || []).join(',');
        data.tomemberid = ($('#tomemberid').val() || []).join(',');
        data.paymenttype = ($('#paymenttype').val() || []).join(',');
        data.datetype = $('#datetype').val();
        data.reporttype = $('#reporttype').val();        
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
        if($('#paymentreporttable tfoot').length==0){
          $.footer += '<tfoot>';
        }
        
        // if($('.footercls').length==0){
          $.footer += '<tr class="footercls">';
      
          $.footer += '<th class="totalrows text-right"></th><th class="totalrows text-right"></th><th class="totalrows text-right"></th><th class="totalrows text-right"></th><th class="totalrows text-right"></th><th class="totalrows text-right"></th><th class="totalrows text-right">Total</th>';
       
          this.api().columns().each(function () {
            var column = this;
            if(column[0].length > 0){
              for (var index = 7; index < column[0].length; index++) {

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
                
                $.footer += '<th class="totalrows text-right" style="padding:5px;">'+CURRENCY_CODE+''+ format.format(pageTotal) +'<br>('+CURRENCY_CODE+''+ format.format(total) +' Total)</th>';
              }
            }
          });
          $.footer += '</tr>';
        // }
        if($('#paymentreporttable tfoot').length==0){
          $.footer += '</tfoot>';
          $('#paymentreporttable tbody').after($.footer);
        }else{
          $('#paymentreporttable tfoot').prepend($.footer);
        }
      }else{
        $('#paymentreporttable tfoot').remove();
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
  //getpaymentdata();
});

$("#paidfromchannelid").change(function(){
  var paidfromchannelid = $(this).val();
  $('#frommemberid')
      .find('option')
      .remove()
      .end()
      .val('whatever')
      ;
  if(paidfromchannelid!='' && paidfromchannelid!=0){
    getmembers(paidfromchannelid);
  }
  $('#frommemberid').selectpicker('refresh');
});

$("#paidtochannelid").change(function(){
  var paidtochannelid = $(this).val();
  $('#tomemberid')
      .find('option')
      .remove()
      .end()
      .val('whatever')
      ;
  if(paidtochannelid!='' && paidtochannelid!=0){
    getmembers(paidtochannelid,0,'tomemberid');
  }
  $('#tomemberid').selectpicker('refresh');
});

function applyFilter(){
  $('.footercls').remove();
  oTable.ajax.reload();
  //getpaymentdata();
}

function getmembers(paidfromchannelid,memberid,id){
  var memberid = memberid || 0;
  var id = id || 'frommemberid';
  var uurl = SITE_URL+"member/getmembers";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {channelid:paidfromchannelid},
    dataType: 'json',
    async: false,
    success: function(response){

      for(var i = 0; i < response.length; i++) {

        $('#'+id).append($('<option>', { 
          value: response[i]['id'],
          text : ucwords(response[i]['name'])
        }));

      }
      if(memberid!=0){
        $("#"+id).val(memberid);
      }
      // $('#product'+prow).val(areaid);
      $('#'+id).selectpicker('refresh');
    },
    error: function(xhr) {
          //alert(xhr.responseText);
        },
    });
}

function getpaymentdata(){

  var uurl = SITE_URL+"payment-report/getpaymentdata";
  var formData = new FormData($('#paymentform')[0]);
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

      if ( $.fn.DataTable.isDataTable('#paymentreporttable') ) {
        $('#paymentreporttable').DataTable().destroy();
      }
      $('#paymentreporttable').empty();

      
      if(!$.isEmptyObject(dataObject.DATA)){
        var fixedColumns = 3;
        table =   $('#paymentreporttable').DataTable({

          "data": dataObject.DATA,
          "columns": dataObject.COLUMNS,
          "language": {
            "lengthMenu": "_MENU_"
          },
          
          "destroy": true,
          "pageLength": 10,
          "scrollCollapse": true,
          "scrollY":        "500px",
          "scrollX":        true,
          /*"fixedColumns":   {
            leftColumns: fixedColumns
          },*/
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
        $('.panel-ctrls.panel-tbl').append($('.dataTables_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
        $('.panel-ctrls.panel-tbl').append("<i class='separator'></i>");
        $('.panel-ctrls.panel-tbl').append($('.dataTables_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

        $('.panel-footer').append($(".dataTable+.row"));
        $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-md");
      }
        
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

function exportpaymentreport(){

  var startdate = $('#startdate').val();
  var enddate = $('#enddate').val();
  var paidfromchannelid = $('#paidfromchannelid').val();
  var paidtochannelid = $('#paidtochannelid').val();
  var frommemberid = ($('#frommemberid').val() || []).join(',');
  var tomemberid = ($('#tomemberid').val() || []).join(',');
  var paymenttype = ($('#paymenttype').val() || []).join(',');
  var datetype = $('#datetype').val();
  var reporttype = $('#reporttype').val();
  
  var totalRecords =$("#paymentreporttable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"payment-report/exportpaymentreport?startdate="+startdate+"&enddate="+enddate+"&paidfromchannelid="+paidfromchannelid+"&frommemberid="+frommemberid+"&paidtochannelid="+paidtochannelid+"&tomemberid="+tomemberid+"&paymenttype="+paymenttype+"&datetype="+datetype+"&reporttype="+reporttype;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  
}

function exporttopdfpaymentreport(){
  
  var startdate = $('#startdate').val();
  var enddate = $('#enddate').val();
  var paidfromchannelid = $('#paidfromchannelid').val();
  var paidtochannelid = $('#paidtochannelid').val();
  var frommemberid = ($('#frommemberid').val() || []).join(',');
  var tomemberid = ($('#tomemberid').val() || []).join(',');
  var paymenttype = ($('#paymenttype').val() || []).join(',');
  var datetype = $('#datetype').val();
  var reporttype = $('#reporttype').val();
  
  var totalRecords =$("#paymentreporttable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"payment-report/exporttopdfpaymentreport?startdate="+startdate+"&enddate="+enddate+"&paidfromchannelid="+paidfromchannelid+"&frommemberid="+frommemberid+"&paidtochannelid="+paidtochannelid+"&tomemberid="+tomemberid+"&paymenttype="+paymenttype+"&datetype="+datetype+"&reporttype="+reporttype;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  
}

function printpaymentreport(){

  var startdate = $('#startdate').val();
  var enddate = $('#enddate').val();
  var paidfromchannelid = $('#paidfromchannelid').val();
  var paidtochannelid = $('#paidtochannelid').val();
  var frommemberid = ($('#frommemberid').val() || []).join(',');
  var tomemberid = ($('#tomemberid').val() || []).join(',');
  var paymenttype = ($('#paymenttype').val() || []).join(',');
  var datetype = $('#datetype').val();
  var reporttype = $('#reporttype').val();
  
  var totalRecords =$("#paymentreporttable").DataTable().page.info().recordsDisplay;
    $.skylo('end');
    if(totalRecords != 0){
      var uurl = SITE_URL + "payment-report/printpaymentreport";
      $.ajax({
        url: uurl, 
        type: 'POST',
        data: {startdate:startdate,enddate:enddate,paidfromchannelid:paidfromchannelid,paidtochannelid:paidtochannelid,frommemberid:frommemberid,tomemberid:tomemberid,paymenttype:paymenttype,datetype:datetype,reporttype:reporttype},
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