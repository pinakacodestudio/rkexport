
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
  getcouponcodedata();
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
  getcouponcodedata();
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

function getcouponcodedata(){

  var uurl = SITE_URL+"purchase-report/getcouponcodedata";
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

      
      if(!$.isEmptyObject(dataObject.DATA)){
        var fixedColumns = 3;
        table =   $('#purchasereporttable').DataTable({

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


          /*"footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over all pages
            total = api
                .column( 4 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            pageTotal = api
                .column( 4, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            console.log(pageTotal);
            // Update footer
            $( api.column( 4 ).footer() ).html(
                '$'+pageTotal +' ( $'+ total +' total)'
            );
          }*/
          fnFooterCallback: function(row, data, start, end, display) {
            var api = this.api();
            
            $(this).find("th.totalrows").remove();
            var footer = $(this).append('<tfoot><tr>');
            
            this.api().columns().each(function () {
              var column = this;

              //console.log(api.columns(column[0]).data());
              $.footer = '<th class="totalrows text-right"></th><th class="totalrows text-right"></th>';
              for (var index = 2; index < column[0].length; index++) {
                //const element = array[index];

                // Total over all pages
                var total = api
                    .column( column[0][index] )
                    .data()
                    .reduce( function (a, b) {
                        return parseFloat(a) + parseFloat(b);
                    }, 0 );

                // Total over this page
                var pageTotal = api
                      .column( column[0][index], { page: 'current'} )
                      .data()
                      .reduce( function (a, b) {
                          return parseFloat(a) + parseFloat(b);
                      }, 0 );
                
                
                $.footer += '<th class="totalrows text-right" style="padding:5px;">'+CURRENCY_CODE+''+ parseFloat(pageTotal).toFixed(2) +'<br>('+CURRENCY_CODE+''+ parseFloat(total).toFixed(2) +' Total)</th>';
                //console.log(pageTotal);
              }
            });

            $(footer).append($.footer);
            $(footer).append('</tr></tfoot>');
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

function exportpurchasereport(){
  
  var startdate = $('#startdate').val();
  var enddate = $('#enddate').val();
  var channelid = $('#channelid').val();
  var memberid = $('#memberid').val() || [];
  memberid = memberid.join(',');
  var status = $('#status').val() || [];
  status = status.join(',');
  var datetype = $('#datetype').val();
  
  var totalRecords =$("#purchasereporttable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"purchase_report/exportpurchasereport?startdate="+startdate+"&enddate="+enddate+"&channelid="+channelid+"&memberid="+memberid+"&status="+status+"&datetype="+datetype;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  
}