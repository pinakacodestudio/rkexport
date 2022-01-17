
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
    getcreditnotedata();
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
  getcreditnotedata();
}

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
          text : response[i]['name']
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

function getcreditnotedata(){

  var uurl = SITE_URL+"sales-return-report/getcreditnotedata";
  var formData = new FormData($('#creditnoteform')[0]);
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

      if ( $.fn.DataTable.isDataTable('#creditnotetable') ) {
        $('#creditnotetable').DataTable().destroy();
      }
      $('#creditnotetable').empty();

      
      if(!$.isEmptyObject(dataObject.DATA)){
        var fixedColumns = 3;
        table =   $('#creditnotetable').DataTable({

          "data": dataObject.DATA,
          "columns": dataObject.COLUMNS,
          "language": {
            "lengthMenu": "_MENU_"
          },
          
          "destroy": true,
          "pageLength": 10,
         /*  "scrollCollapse": true,
          "scrollY":        "500px",
          "scrollX":        true, */
          /*"fixedColumns":   {
            leftColumns: fixedColumns
          },*/
          "columnDefs": [{
            'orderable': false,
            'targets': []
          }],
          "order": [], //Initial no order.

          fnFooterCallback: function(row, data, start, end, display) {
            var api = this.api(),data;
      
            if(!$.isEmptyObject(data)){
              $.footer = '';
              $('.footercls').remove();
              if($('#creditnotetable tfoot').length==0){
                $.footer += '<tfoot>';
              }
              
              $.footer += '<tr class="footercls">';
              $.footer += '<th colspan="3" class="totalrows text-right">Total Sales Return</th>';
              this.api().columns().each(function () {
                var column = this;

                // for (var index = 2; index < column[0].length; index++) {
                
                  var intVal = function (i) {
                    return typeof i === 'string' ?
                        i.replace(/[\L,]/g, '') * 1 :
                        typeof i === 'number' ?
                        i : 0;
                  };
                  // Total over all pages
                  var total = api
                      .column( column[0][3] )
                      .data()
                      .reduce( function (a, b) {
                        var a = intVal(a) || 0;
                        var b = intVal(b) || 0;
                        return parseFloat(a) + parseFloat(b);
                      }, 0 );

                  // Total over this page
                  var pageTotal = api
                      .column( column[0][3], { page: 'current'} )
                      .data()
                      .reduce( function (a, b) {
                        var a = intVal(a) || 0;
                        var b = intVal(b) || 0;
                        return parseFloat(a) + parseFloat(b);
                      }, 0 );
                  
                  $.footer += '<th class="totalrows text-right" style="padding:5px;">'+CURRENCY_CODE+''+ format.format(pageTotal) +'<br>('+CURRENCY_CODE+''+ format.format(total) +' Total)</th>';
                // }
              });
               
              $.footer += '</tr>';
              if($('#creditnotetable tfoot').length==0){
                $.footer += '</tfoot>';
                $('#creditnotetable tbody').after($.footer);
              }else{
                $('#creditnotetable tfoot').prepend($.footer);
              }
            }else{
              $('#creditnotetable tfoot').remove();
            }
          }
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

function exportcreditnotereport(){
  
  var startdate = $('#startdate').val();
  var enddate = $('#enddate').val();
  var channelid = $('#channelid').val();
  var memberid = $('#memberid').val() || [];
  memberid = memberid.join(',');
  var status = $('#status').val() || [];
  status = status.join(',');
  var datetype = $('#datetype').val();
  
  var totalRecords =$("#creditnotetable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"sales-return-report/exportcreditnotereport?startdate="+startdate+"&enddate="+enddate+"&channelid="+channelid+"&memberid="+memberid+"&status="+status+"&datetype="+datetype;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  
}

function exporttopdfsalesreturnreport(){
  
  var startdate = $('#startdate').val();
  var enddate = $('#enddate').val();
  var channelid = $('#channelid').val();
  var memberid = $('#memberid').val() || [];
  memberid = memberid.join(',');
  var status = $('#status').val() || [];
  status = status.join(',');
  var datetype = $('#datetype').val();
  
  var totalRecords =$("#creditnotetable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"sales-return-report/exporttopdfsalesreturnreport?startdate="+startdate+"&enddate="+enddate+"&channelid="+channelid+"&memberid="+memberid+"&status="+status+"&datetype="+datetype;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  
}

function printsalesreturnreport(){

    var startdate = $('#startdate').val();
    var enddate = $('#enddate').val();
    var channelid = $('#channelid').val();
    var memberid = $('#memberid').val() || [];
    memberid = memberid.join(',');
    var status = $('#status').val() || [];
    status = status.join(',');
    var datetype = $('#datetype').val();

    var totalRecords =$("#creditnotetable").DataTable().page.info().recordsDisplay;
    $.skylo('end');
    if(totalRecords != 0){
      var uurl = SITE_URL + "sales-return-report/printsalesreturnreport";
      $.ajax({
        url: uurl, 
        type: 'POST',
        data: {startdate:startdate,enddate:enddate,channelid:channelid,memberid:memberid,status:status,datetype:datetype},
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