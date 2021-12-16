
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
    getproducts(-1);
    getproductwisecreditnotedata();
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
  /* if(channelid!='' && channelid==0){
    getproducts(-1);
  }else{
    $('#productid')
        .find('option')
        .remove()
        .end()
        .val('whatever')
    ;
    $('#productid').selectpicker('refresh');
  } */
  $('#memberid').selectpicker('refresh');
})

/* $("#memberid").change(function(){
    var memberid = $(this).val();
    $('#productid')
        .find('option')
        .remove()
        .end()
        .val('whatever')
        ;
    if(memberid!='' && memberid!=0){
      getproducts(memberid);
    }
    $('#productid').selectpicker('refresh');
}); */

function applyFilter(){
  $('.footercls').remove();
  getproductwisecreditnotedata();
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
function getproducts(memberid){

    $('#productid')
        .find('option')
        .remove()
        .end()
        .val('whatever')
    ;
  
    if(memberid!=0 || memberid!=""){
        var uurl = SITE_URL+"member/getProductsByMultipleMemberId";
        $.ajax({
        url: uurl,
        type: 'POST',
        data: {memberid:memberid},
        dataType: 'json',
        async: false,
        success: function(response){
    
            for(var i = 0; i < response.length; i++) {
    
              var productname = response[i]['name'].replace("'","&apos;");
              if(DROPDOWN_PRODUCT_LIST==0){
                  
                  $('#productid').append($('<option>', { 
                      value: response[i]['id'],
                      text : productname
                  }));
              }else{
                  
                $('#productid').append($('<option>', { 
                  value: response[i]['id'],
                  //text : ucwords(response[i]['name'])
                  "data-content" :'<img src="'+PRODUCT_PATH+response[i]['image']+'" style="width:40px">  ' + productname
                }));
              }
    
            }
        },
        error: function(xhr) {
            //alert(xhr.responseText);
            },
        });
    }
    $('#productid').selectpicker('refresh');
}
function getproductwisecreditnotedata(){

  var uurl = SITE_URL+"product-wise-credit-note-report/getproductwisecreditnotedata";
  var formData = new FormData($('#productwisecreditnoteform')[0]);
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

      if ( $.fn.DataTable.isDataTable('#productwisecreditnotetable') ) {
        $('#productwisecreditnotetable').DataTable().destroy();
      }
      $('#productwisecreditnotetable').empty();

      
      if(!$.isEmptyObject(dataObject.DATA)){
        var fixedColumns = 3;
        table =   $('#productwisecreditnotetable').DataTable({

          "data": dataObject.DATA,
          "columns": dataObject.COLUMNS,
          "language": {
            "lengthMenu": "_MENU_"
          },
          
          "destroy": true,
          "pageLength": 50,
          /* "scrollCollapse": true,
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
              if($('#productwisecreditnotetable tfoot').length==0){
                $.footer += '<tfoot>';
              }
              
              $.footer += '<tr class="footercls">';
              $.footer += '<th colspan="3" class="totalrows text-right">Total Sales Return</th>';
                  
              this.api().columns().each(function () {
                var column = this;
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
              });
              
              $.footer += '</tr>';
              
              if($('#productwisecreditnotetable tfoot').length==0){
                $.footer += '</tfoot>';
                $('#productwisecreditnotetable tbody').after($.footer);
              }else{
                $('#productwisecreditnotetable tfoot').prepend($.footer);
              }
            }else{
              $('#productwisecreditnotetable tfoot').remove();
            }
          },
        });

        $('.dataTables_filter input').attr('placeholder','Search...');

        $('.panel-ctrls.panel-tbl').html('');
        $('.panel-footer').html('');
        $('.dataTables_filter input').attr('placeholder','Search...');


        //DOM Manipulation to move datatable elements integrate to panel
        $('.panel-ctrls.panel-tbl').append($('.dataTables_filter').addClass("pull-right")).find("label").addClass("form-group panel-ctrls-center");
        $('.panel-ctrls.panel-tbl').append("<i class='separator'></i>");
        $('.panel-ctrls.panel-tbl').append($('.dataTables_length').addClass("pull-left")).find("label").addClass("form-group panel-ctrls-center");

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

function exportproductwisecreditnotereport(){
  
  var startdate = $('#startdate').val();
  var enddate = $('#enddate').val();
  var channelid = $('#channelid').val();
  var memberid = $('#memberid').val() || [];
  memberid = memberid.join(',');
  var productid = $('#productid').val() || [];
  productid = productid.join(',');
  var status = $('#status').val() || [];
  status = status.join(',');
  var datetype = $('#datetype').val();
  
  var totalRecords =$("#productwisecreditnotetable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"product-wise-credit-note-report/exportproductwisecreditnotereport?startdate="+startdate+"&enddate="+enddate+"&channelid="+channelid+"&memberid="+memberid+"&productid="+productid+"&status="+status+"&datetype="+datetype;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  
}

function exporttopdfproductwisecreditnotereport(){
  
  var startdate = $('#startdate').val();
  var enddate = $('#enddate').val();
  var channelid = $('#channelid').val();
  var memberid = $('#memberid').val() || [];
  memberid = memberid.join(',');
  var productid = $('#productid').val() || [];
  productid = productid.join(',');
  var status = $('#status').val() || [];
  status = status.join(',');
  var datetype = $('#datetype').val();
  
  var totalRecords =$("#productwisecreditnotetable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"product-wise-credit-note-report/exporttopdfproductwisecreditnotereport?startdate="+startdate+"&enddate="+enddate+"&channelid="+channelid+"&memberid="+memberid+"&productid="+productid+"&status="+status+"&datetype="+datetype;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  
}

function printproductwisecreditnotereport(){

  var startdate = $('#startdate').val();
  var enddate = $('#enddate').val();
  var channelid = $('#channelid').val();
  var memberid = $('#memberid').val() || [];
  memberid = memberid.join(',');
  var productid = $('#productid').val() || [];
  productid = productid.join(',');
  var status = $('#status').val() || [];
  status = status.join(',');
  var datetype = $('#datetype').val();
  
  var totalRecords =$("#productwisecreditnotetable").DataTable().page.info().recordsDisplay;
    $.skylo('end');
    if(totalRecords != 0){
      var uurl = SITE_URL + "product-wise-credit-note-report/printproductwisecreditnotereport";
      $.ajax({
        url: uurl, 
        type: 'POST',
        data: {startdate:startdate,enddate:enddate,channelid:channelid,memberid:memberid,productid:productid,status:status,datetype:datetype},
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