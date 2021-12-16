
$(document).ready(function() {
  
  oTable = $('#balancereporttable').DataTable
  ({
    "language": {
      "lengthMenu": "_MENU_"
    },
    "pageLength": 50,
    "columnDefs": [{
      'orderable': false,
      'targets': [0,2]
    },{ targets: [4], className: "text-right" }],
    "order": [], //Initial no order.
    'serverSide': true,//Feature control DataTables' server-side processing mode.
    // Load data for the table's content from an Ajax source
    "ajax": {
      "url": SITE_URL+"balance-report/listing",
      "type": "POST",
      "data": function ( data ) {
        data.startdate = $('#startdate').val();
        data.enddate = $('#enddate').val();
        data.channelid = $('#channelid').val();
        data.memberid = $('#memberid').val();
        data.type = $('#type').val();        
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
        if($('#balancereporttable tfoot').length==0){
          $.footer += '<tfoot>';
        }
        
        // if($('.footercls').length==0){
          $.footer += '<tr class="footercls">';
      
          $.footer += '<th class="totalrows text-right"></th><th class="totalrows text-right"></th><th class="totalrows text-right">Total</th>';
       
          this.api().columns().each(function () {
            var column = this;
            if(column[0].length > 0){
              for (var index = 3; index < column[0].length; index++) {

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
        if($('#balancereporttable tfoot').length==0){
          $.footer += '</tfoot>';
          $('#balancereporttable tbody').after($.footer);
        }else{
          $('#balancereporttable tfoot').prepend($.footer);
        }
      }else{
        $('#balancereporttable tfoot').remove();
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
  });
  //getpaymentdata();
});
function applyFilter(){
  $('.footercls').remove();
  oTable.ajax.reload();
  //getpaymentdata();
}

function getmembers(channelid){
  
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
    },
    error: function(xhr) {
          //alert(xhr.responseText);
        },
    });
}


function exportbalancereport(){

  var startdate = $('#startdate').val();
  var enddate = $('#enddate').val();
  var channelid = $('#channelid').val();
  var memberid = ($('#memberid').val()!=null?$('#memberid').val():"");
  var type = $('#type').val();
  
  var totalRecords =$("#balancereporttable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"balance-report/exportbalancereport?startdate="+startdate+"&enddate="+enddate+"&channelid="+channelid+"&memberid="+memberid+"&type="+type;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  
}

function exporttopdfbalancereport(){
  
  var startdate = $('#startdate').val();
  var enddate = $('#enddate').val();
  var channelid = $('#channelid').val();
  var memberid = ($('#memberid').val() || []).join(',');
  var type = $('#type').val();
  
  var totalRecords =$("#balancereporttable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"balance-report/exporttopdfbalancereport?startdate="+startdate+"&enddate="+enddate+"&channelid="+channelid+"&memberid="+memberid+"&type="+type;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  
}

function printbalancereport(){

  var startdate = $('#startdate').val();
  var enddate = $('#enddate').val();
  var channelid = $('#channelid').val();
  var memberid = ($('#memberid').val() || []).join(',');
  var type = $('#type').val();
  
  var totalRecords =$("#balancereporttable").DataTable().page.info().recordsDisplay;
    $.skylo('end');
    if(totalRecords != 0){
      var uurl = SITE_URL + "balance-report/printbalancereport";
      $.ajax({
        url: uurl, 
        type: 'POST',
        data: {startdate:startdate,enddate:enddate,channelid:channelid,memberid:memberid,type:type},
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