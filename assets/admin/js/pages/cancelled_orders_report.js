
// var oTable;
$(document).ready(function(){
  oTable = $('#cancelledordersreporttable').DataTable({
      "language": {
          "lengthMenu": "_MENU_"
      },
      "pageLength": 10,
      "columnDefs": [{
          'orderable': false,
          'targets': []
      },
      { targets: [2,3,4,5], className: "text-right" }],
      "order": [], //Initial no order.
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      // Load data for the table's content from an Ajax source
      "ajax":{
              "url": SITE_URL+"Quotation-to-order-conversion/listing",
              "type": "POST",
              "data": function ( data ){
                  data.fromdate = $('#fromdate').val();
                  data.todate = $('#todate').val();
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
  oTable.ajax.reload(null, false);
}

$("#channelid").change(function(){
  var channelid = $(this).val();
  $('#memberid')
      .find('option')
      .remove()
      .end()
      .val('whatever')
      ;
  if(channelid > 0){
    getmembers(channelid);
  }
  $('#memberid').selectpicker('refresh');
})

function getmembers(channelid,memberid=0){
  var uurl = SITE_URL+"member/getmembers";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {channelid:channelid},
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

function exporttoexcelquotationtoorderconversionreport(){

var fromdate = $('#fromdate').val();
var todate = $('#todate').val();
var channelid = $('#channelid').val();
var memberid = $('#memberid').val()!=null?$('#memberid').val():"";


var totalRecords =$("#quotationtoorderconversiontable").DataTable().page.info().recordsDisplay;
$.skylo('end');
if(totalRecords != 0){
  window.location= SITE_URL+"Quotation-to-order-conversion/exporttoexcelquotationtoorderconversionreport?fromdate="+fromdate+"&todate="+todate+"&channelid="+channelid+"&memberid="+memberid;
}else{
  new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
}

}

function exporttopdfquotationtoorderconversionreport(){

var fromdate = $('#fromdate').val();
var todate = $('#todate').val();
var channelid = $('#channelid').val();
var memberid = $('#memberid').val()!=null?$('#memberid').val():"";

var totalRecords =$("#quotationtoorderconversiontable").DataTable().page.info().recordsDisplay;
$.skylo('end');
if(totalRecords != 0){
  window.location= SITE_URL+"Quotation-to-order-conversion/exporttopdfquotationtoorderconversionreport?fromdate="+fromdate+"&todate="+todate+"&channelid="+channelid+"&memberid="+memberid;
}else{
  new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
}

}

function printquotationtoorderconversionreport(){

var fromdate = $('#fromdate').val();
var todate = $('#todate').val();
var channelid = $('#channelid').val();
var memberid = $('#memberid').val() || [];
memberid = memberid.join(',');

var totalRecords =$("#quotationtoorderconversiontable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    var uurl = SITE_URL + "quotation-to-order-conversion/printquotationtoorderconversionreport";
    $.ajax({
      url: uurl, 
      type: 'POST',
      data: {fromdate:fromdate,todate:todate,channelid:channelid,memberid:memberid},
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

  
