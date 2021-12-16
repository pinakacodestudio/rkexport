
$(document).ready(function() {
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
  getpurchasedata();
});

$("#channelid").change(function(){
  var channelid = $(this).val();
  $('#memberid')
      .find('option')
      .remove()
      .end()
      .val('whatever')
      ;
  if(channelid!='' && channelid!=0){
    getmembers(channelid);
  }
  $('#memberid').selectpicker('refresh');
})

function applyFilter(){
  $('.footercls').remove();
  getpurchasedata();
}

function getmembers(channelid,memberid=0){
  var uurl = SITE_URL+"member/getmembers";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {channelid:channelid,vendor:1},
    dataType: 'json',
    async: false,
    success: function(response){

      for(var i = 0; i < response.length; i++) {

        $('#memberid').append($('<option>', { 
          value: response[i]['id'],
          text : ucwords(response[i]['name'])
        }));

      }
      if(memberid!=0){
        $("#memberid").val(memberid);
      }
      // $('#product'+prow).val(areaid);
      $('#memberid').selectpicker('refresh');
    },
    error: function(xhr) {
          //alert(xhr.responseText);
        },
      });
}

function getpurchasedata(){

  var uurl = SITE_URL+"purchase-report/getpurchasedata";
  var formData = new FormData($('#purchaseform')[0]);
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

      if ( $.fn.DataTable.isDataTable('#purchasereporttable') ) {
        $('#purchasereporttable').DataTable().destroy();
      }
      $('#purchasereporttable').empty();

      
      // if(!$.isEmptyObject(dataObject.DATA)){
        var fixedColumns = 3;
        table =   $('#purchasereporttable').DataTable({

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
          fnFooterCallback: function(row, data, start, end, display) {
            var api = this.api();
            
            if(!$.isEmptyObject(data)){
              $.footer = '';
              $('.footercls').remove();
              if($('#purchasereporttable tfoot').length==0){
                $.footer += '<tfoot>';
              }
              
              $.footer += '<tr class="footercls">';
              $.footer += '<th colspan="2" class="totalrows text-right">Total Purchase ('+CURRENCY_CODE+')</th>';
              
              this.api().columns().each(function () {
                var column = this;

                for (var index = 2; index < column[0].length; index++) {
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
                  
                  
                  $.footer += '<th class="totalrows text-right" style="padding:5px;">'+CURRENCY_CODE+''+ format.format(pageTotal)+'<br>('+CURRENCY_CODE+''+ format.format(total) +' Total)</th>';
                }
              });
              $.footer += '</tr>';

              if($('#purchasereporttable tfoot').length==0){
                $.footer += '</tfoot>';
                $('#purchasereporttable tbody').after($.footer);
              }else{
                $('#purchasereporttable tfoot').prepend($.footer);
              }
            }else{
              $('#purchasereporttable tfoot').remove();
            }
          }
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
      // }
        
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

function exportpurchasereport(){
  
  var startdate = $('#startdate').val();
  var enddate = $('#enddate').val();
  var channelid = $('#channelid').val();
  var memberid = $('#memberid').val() || [];
  memberid = memberid.join(',');
  var status = $('#status').val() || [];
  status = status.join(',');
  var datetype = $('#datetype').val();
  var purhcasetype = $('input[name=purhcasetype]:checked').val();
  var rowtype = $('input[name=rowtype]:checked').val();
  
  var totalRecords =$("#purchasereporttable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"purchase_report/exportpurchasereport?startdate="+startdate+"&enddate="+enddate+"&channelid="+channelid+"&memberid="+memberid+"&status="+status+"&datetype="+datetype+"&purhcasetype="+purhcasetype+"&rowtype="+rowtype;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  
}

function exporttopdfpurchasereport(){
  
  var startdate = $('#startdate').val();
  var enddate = $('#enddate').val();
  var channelid = $('#channelid').val();
  var memberid = $('#memberid').val() || [];
  memberid = memberid.join(',');
  var status = $('#status').val() || [];
  status = status.join(',');
  var datetype = $('#datetype').val();
  var purhcasetype = $('input[name=purhcasetype]:checked').val();
  var rowtype = $('input[name=rowtype]:checked').val();
  
  var totalRecords =$("#purchasereporttable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"purchase-report/exporttopdfpurchasereport?startdate="+startdate+"&enddate="+enddate+"&channelid="+channelid+"&memberid="+memberid+"&status="+status+"&datetype="+datetype+"&purhcasetype="+purhcasetype+"&rowtype="+rowtype;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  
}

function printpurchasereport(){

  var startdate = $('#startdate').val();
  var enddate = $('#enddate').val();
  var channelid = $('#channelid').val();
  var memberid = $('#memberid').val() || [];
  memberid = memberid.join(',');
  var status = $('#status').val() || [];
  status = status.join(',');
  var datetype = $('#datetype').val();
  var purhcasetype = $('input[name=purhcasetype]:checked').val();
  var rowtype = $('input[name=rowtype]:checked').val();

  var totalRecords =$("#purchasereporttable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    var uurl = SITE_URL + "purchase-report/printpurchasereport";
    $.ajax({
      url: uurl, 
      type: 'POST',
      data: {startdate:startdate,enddate:enddate,channelid:channelid,memberid:memberid,status:status,datetype:datetype,purhcasetype:purhcasetype,rowtype:rowtype},
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