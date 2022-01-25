$(document).ready(function() {

  $('#year').datepicker({
      todayHighlight: true,
      format: 'yyyy',
      autoclose: true,
      viewMode: "years", 
      minViewMode: "years"
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

  getcancelledordersreportdata();

  $("#countryid").change(function(){
      getProvinceByCountry();
  });
  $("#provinceid").change(function(){
      getCityByProvince();
  });
});

function applyFilter(){
  getcancelledordersreportdata();
}

function getcancelledordersreportdata(){

  var uurl = SITE_URL+"cancelled-orders-report/getcancelledordersreportdata";
  var formData = new FormData($('#cancelledordersreportform')[0]);
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
          var fixedColumns = 3;
          var dataObject = $.parseJSON(response);
          if ( $.fn.DataTable.isDataTable('#cancelledordersreporttable') ) {
          $('#cancelledordersreporttable').DataTable().destroy();
          }
          $('#cancelledordersreporttable').empty();
          
          table = $('#cancelledordersreporttable').DataTable({
  
              "data": dataObject.DATA,
              "columns": dataObject.COLUMNS,
              "language": {
              "lengthMenu": "_MENU_"
              },
              
              "destroy": true,
              "pageLength": 10,
              "columnDefs": [{
                  'orderable': false,
                  'targets': []
              }, { width: 50, targets: 0 }, { width: 200, targets: 1 }, { className: 'text-right', width: 150, targets: 2 }],
              "order": [], //Initial no order.
              /* "scrollCollapse": true,
              "scrollY": "500px",
              "scrollX": true,
              "fixedColumns":   {
                  leftColumns: fixedColumns,
                  rightColumns: 0
              } */
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
  $("#channelid").change(function(){
      getmembers();
  });
}
function getmembers(){

  $("#memberid").find('option')
              .remove()
              .end()
              .val('whatever')
            ;
  $("#memberid").selectpicker('refresh');
  var channelid = $("#channelid").val();

  if(channelid!='' && channelid!=0){
    var uurl = SITE_URL+"cancelled-orders-report/getbuyermembers";
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {channelid:channelid},
      dataType: 'json',
      async: false,
      success: function(response){

        for(var i = 0; i < response.length; i++) {

          $("#memberid").append($('<option>', { 
            value: response[i]['id'],
            text : response[i]['name']
          }));

        }
        $("#memberid").selectpicker('refresh');
      },
      error: function(xhr) {
        //alert(xhr.responseText);
      },
    });
  }
}
function exporttoexcelcancelledordersreport(){

  var countryid = ($('#countryid').val()!=null?$('#countryid').val():'');
  var provinceid = ($('#provinceid').val()!=null?$('#provinceid').val():'');
  var cityid = ($('#cityid').val()!=null?$('#cityid').val():'');
  var year = $('#year').val();
  var month = ($('#month').val()!=null?$('#month').val():'');
  var channelid = $('#channelid').val();
  var memberid = ($('#memberid').val()!=null?$('#memberid').val():'');

  var totalRecords =$("#cancelledordersreporttable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"cancelled-orders-report/exporttoexcelcancelledordersreport?channelid="+channelid+"&memberid="+memberid+"&countryid="+countryid+"&provinceid="+provinceid+"&cityid="+cityid+"&year="+year+"&month="+month;
  }else{
      new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }

}
function exporttopdfcancelledordersreport(){

  var countryid = ($('#countryid').val()!=null?$('#countryid').val():'');
  var provinceid = ($('#provinceid').val()!=null?$('#provinceid').val():'');
  var cityid = ($('#cityid').val()!=null?$('#cityid').val():'');
  var year = $('#year').val();
  var month = ($('#month').val()!=null?$('#month').val():'');
  var channelid = $('#channelid').val();
  var memberid = ($('#memberid').val()!=null?$('#memberid').val():'');
  
  var totalRecords =$("#cancelledordersreporttable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
      window.location= SITE_URL+"cancelled-orders-report/exporttopdfcancelledordersreport?channelid="+channelid+"&memberid="+memberid+"&countryid="+countryid+"&provinceid="+provinceid+"&cityid="+cityid+"&year="+year+"&month="+month;
  }else{
      new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
}
function getProvinceByCountry(){

  var countryid = ($('#countryid').val()!=null?$('#countryid').val():'');
  $('#provinceid')
      .find('option')
      .remove()
      .end()
      .val('whatever')
  ;
  $('#cityid')
      .find('option')
      .remove()
      .end()
      .val('whatever')
  ;
  if(countryid!=""){
    var uurl = SITE_URL+"cancelled-orders-report/getProvinceList";
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {countryid:countryid},
      dataType: 'json',
      async: false,
      beforeSend: function(){
        $('.mask').show();
        $('#loader').show();
      },
      success: function(response){
        
        for(var i = 0; i < response.length; i++) {
          $('#provinceid').append($('<option>', { 
            value: response[i]['pid'],
            text : response[i]['provincename']
          }));
        }
        
      },
      complete: function(){
        $('.mask').hide();
        $('#loader').hide();
      },
      error: function(xhr) {
        //alert(xhr.responseText);
      },
    });
  }
  $('#provinceid').selectpicker('refresh');
  $('#cityid').selectpicker('refresh');
}
function getCityByProvince(){

  $('#cityid')
    .find('option')
    .remove()
    .end()
    .val('whatever')
  ;
  var provinceid = ($('#provinceid').val()!=null?$('#provinceid').val():'');
  if(provinceid!=""){
    var uurl = SITE_URL+"cancelled-orders-report/getCityList";
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {provinceid:provinceid},
      dataType: 'json',
      async: false,
      beforeSend: function(){
        $('.mask').show();
        $('#loader').show();
      },
      success: function(response){
        
        for(var i = 0; i < response.length; i++) {

          $('#cityid').append($('<option>', { 
            value: response[i]['cid'],
            text : response[i]['cityname']
          }));

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
  $('#cityid').selectpicker('refresh');
}
function printcancelledordersreport(){

var countryid = $('#countryid').val() || [];
countryid = countryid.join(',');
var provinceid = $('#provinceid').val() || [];
provinceid = provinceid.join(',');
var cityid = $('#cityid').val() || [];
cityid = cityid.join(',');
var year = $('#year').val();
var month = $('#month').val() || [];
month = month.join(',');
var channelid = $('#channelid').val();
var memberid = $('#memberid').val() || [];
memberid = memberid.join(',');

var totalRecords =$("#cancelledordersreporttable").DataTable().page.info().recordsDisplay;
$.skylo('end');
if(totalRecords != 0){
    var uurl = SITE_URL + "cancelled-orders-report/printcancelledordersreport";
    $.ajax({
        url: uurl, 
        type: 'POST',
        data: {countryid:countryid,provinceid:provinceid,cityid:cityid,year:year,month:month,channelid:channelid,memberid:memberid},
        async: false,
        //dataType: 'json',
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