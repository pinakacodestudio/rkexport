
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
    $("#categoryid").change(function(){
      $('#productid')
        .find('option')
        .remove()
        .end()
        .val('whatever')
      ;
      $('#productid').selectpicker('refresh');

      getproducts(-1,this.value);
    });
    getproductwisepurchasedata();
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
}) */

function applyFilter(){
  $('.footercls').remove();
  getproductwisepurchasedata();
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
function getproducts(memberid,categoryid){

    $('#productid')
        .find('option')
        .remove()
        .end()
        .val('whatever')
    ;

    if(memberid!=0 || memberid!="" && categoryid!=0){
        var uurl = SITE_URL+"member/getProductsByMultipleMemberId";
        $.ajax({
        url: uurl,
        type: 'POST',
        data: {memberid:memberid,categoryid:categoryid},
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
function getproductwisepurchasedata(){

  var uurl = SITE_URL+"product-wise-purchase-report/getproductwisepurchasedata";
  var formData = new FormData($('#productwisepurchaseform')[0]);
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
      
      // var dataObject = $.parseJSON(response);

      if ( $.fn.DataTable.isDataTable('#productwisepurchasetable') ) {
        $('#productwisepurchasetable').DataTable().destroy();
      }
      $('#productwisepurchasetable').empty();
      $('#productwisepurchasetable').html(response);
      
      // if(!$.isEmptyObject(dataObject.DATA) && dataObject.DATA!=undefined){
        var fixedColumns = 3;
        table =   $('#productwisepurchasetable').DataTable({

          // "data": dataObject.DATA,
          // "columns": dataObject.COLUMNS,
          "language": {
            "lengthMenu": "_MENU_"
          },
          
          "destroy": true,
          "pageLength": 10,
          /* "scrollCollapse": true,
          "scrollY":        "500px",
          "scrollX":        true, */
          /* "fixedColumns":   {
            leftColumns: fixedColumns
          }, */
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
              if($('#productwisepurchasetable tfoot').length==0){
                $.footer += '<tfoot>';
              }
              
              $.footer += '<tr class="footercls">';
              $.footer += '<th colspan="3" class="totalrows text-right">Total Purchase</th>';
              
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
                  
                      var currency = CURRENCY_CODE;
                      if(index % 2 == 0){
                        currency = "";
                      }
                      $.footer += '<th class="totalrows text-right" style="padding:5px;">'+currency+ format.format(pageTotal) +'<br>('+currency+ format.format(total) +' Total)</th>';
                }
              });
             
              $.footer += '</tr>';
            
              if($('#productwisepurchasetable tfoot').length==0){
                $.footer += '</tfoot>';
                $('#productwisepurchasetable tbody').after($.footer);
              }else{
                $('#productwisepurchasetable tfoot').prepend($.footer);
              }
            }else{
              $('#productwisepurchasetable tfoot').remove();
            }
          }
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
      //}
        
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

function exportproductwisepurchasereport(){
  
  var startdate = $('#startdate').val();
  var enddate = $('#enddate').val();
  var channelid = $('#channelid').val();
  var memberid = $('#memberid').val() || [];
  memberid = memberid.join(',');
  var categoryid = $('#categoryid').val();
  var productid = $('#productid').val() || [];
  productid = productid.join(',');
  var status = $('#status').val() || [];
  status = status.join(',');
  var datetype = $('#datetype').val();
  
  var totalRecords =$("#productwisepurchasetable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"product-wise-purchase-report/exportproductwisepurchasereport?startdate="+startdate+"&enddate="+enddate+"&channelid="+channelid+"&memberid="+memberid+"&categoryid="+categoryid+"&productid="+productid+"&status="+status+"&datetype="+datetype;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  
}

function exporttopdfproductwisepurchasereport(){
  
  var startdate = $('#startdate').val();
  var enddate = $('#enddate').val();
  var channelid = $('#channelid').val();
  var memberid = $('#memberid').val() || [];
  memberid = memberid.join(',');
  var categoryid = $('#categoryid').val();
  var productid = $('#productid').val() || [];
  productid = productid.join(',');
  var status = $('#status').val() || [];
  status = status.join(',');
  var datetype = $('#datetype').val();
  
  var totalRecords =$("#productwisepurchasetable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"product-wise-purchase-report/exporttopdfproductwisepurchasereport?startdate="+startdate+"&enddate="+enddate+"&channelid="+channelid+"&memberid="+memberid+"&categoryid="+categoryid+"&productid="+productid+"&status="+status+"&datetype="+datetype;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  
}

function printproductwisepurchasereport(){

  var startdate = $('#startdate').val();
  var enddate = $('#enddate').val();
  var channelid = $('#channelid').val();
  var memberid = $('#memberid').val() || [];
  memberid = memberid.join(',');
  var categoryid = $('#categoryid').val();
  var productid = $('#productid').val() || [];
  productid = productid.join(',');
  var status = $('#status').val() || [];
  status = status.join(',');
  var datetype = $('#datetype').val();
  
  var totalRecords =$("#productwisepurchasetable").DataTable().page.info().recordsDisplay;
    $.skylo('end');
    if(totalRecords != 0){
      var uurl = SITE_URL + "product-wise-purchase-report/printproductwisepurchasereport";
      $.ajax({
        url: uurl, 
        type: 'POST',
        data: {startdate:startdate,enddate:enddate,channelid:channelid,memberid:memberid,categoryid:categoryid,productid:productid,status:status,datetype:datetype},
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