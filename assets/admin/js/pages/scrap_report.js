
$(document).ready(function() {

    oTable = $('#scrapreporttable').DataTable
    ({
      "language": {
        "lengthMenu": "_MENU_"
      },
      "pageLength": 10,
      /* "scrollCollapse": true,
      "scrollY": "500px", */
      "columnDefs": [{
        'orderable': false,
        'targets': [0]
      },{className: "text-right", 'targets': [-1,-2,-3,-4]}],
      "order": [], //Initial no order.
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": SITE_URL+"scrap-report/listing",
        "type": "POST",
        "data": function ( data ) {
          data.startdate = $('#startdate').val();
          data.enddate = $('#enddate').val();
          data.categoryid = $('#categoryid').val();
          data.productid = $('#productid').val();
          data.producttype = $('#producttype').val();
          data.stocktype = $('#stocktype').val();
          data.scraptype = $('#scraptype').val();
        },
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
        },
        error: function(xhr) {
          //alert(xhr.responseText);
        },
        complete: function(response){
          $('.mask').hide();
          $('#loader').hide();

          $("#rowstotal5").html(format.format(response.responseJSON.totalrejectionqty));
          $("#rowstotal6").html(format.format(response.responseJSON.totalwastageqty));
          $("#rowstotal7").html(format.format(response.responseJSON.totalamount));
        },
      },
      "footerCallback": function ( row, data, start, end, display ) {
        var api = this.api(),data;
        
        if(!$.isEmptyObject(data)){
          $.footer = '';
          $('.footercls').remove();
          if($('#scrapreporttable tfoot').length==0){
            $.footer += '<tfoot>';
          }
          
          // if($('.footercls').length==0){
            $.footer += '<tr class="footercls">';
        
            $.footer += '<th colspan="5" class="totalrows text-right">Total</th>';
        
            this.api().columns().each(function () {
              var column = this;
              if(column[0].length > 0){
                for (var index = 5; index < column[0].length; index++) {

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
                  
                  $.footer += '<th class="totalrows text-right" style="padding:10px 15px;">'+ (index==7?CURRENCY_CODE:"")+format.format(pageTotal) +' <br>('+(index==7?CURRENCY_CODE:"")+'<span id="rowstotal'+index+'" ></span>)</th>';
                }
              }
            });
            $.footer += '</tr>';
          // }
          if($('#scrapreporttable tfoot').length==0){
            $.footer += '</tfoot>';
            $('#scrapreporttable tbody').after($.footer);
          }else{
            $('#scrapreporttable tfoot').prepend($.footer);
          }
        }else{
          $('#scrapreporttable tfoot').remove();
        }

      }
    });

    $('.dataTables_filter input').attr('placeholder','Search...');

    //DOM Manipulation to move datatable elements integrate to panel
    $('.panel-ctrls.panel-tbl').append($('.dataTables_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls.panel-tbl').append("<i class='separator'></i>");
    $('.panel-ctrls.panel-tbl').append($('.dataTables_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('.panel-footer').append($(".dataTable+.row"));
    $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-md");
    
    $(function () {
      $('.panel-heading.filter-panel').click(function() {
          $(this).find('.button-icon span').html($(this).find('.button-icon span').text() == 'keyboard_arrow_down' ? 'keyboard_arrow_up' : 'keyboard_arrow_down');
          //$(this).children().toggleClass(" ");
          $(this).next().slideToggle({duration: 200});
          $(this).toggleClass('panel-collapsed');
          return false;
      });
    });

    $('#datepicker-range').datepicker({
      todayHighlight: true,
      format: 'dd/mm/yyyy',
      autoclose: true,
      todayBtn:"linked"
    });
    
    $("#categoryid").change(function(){
      $('#productid')
        .find('option')
        .remove()
        .end()
        .val('whatever')
      ;
      $('#productid').selectpicker('refresh');

      getproducts(this.value);
    });
 
});
function applyFilter(){
  $('.footercls').remove();
  oTable.ajax.reload();
}
function getproducts(categoryid){

  $('#productid')
      .find('option')
      .remove()
      .end()
      .val('whatever')
  ;

  if(categoryid!=0){
      var uurl = SITE_URL+"product/getAllProductByCategoryID";
      $.ajax({
      url: uurl,
      type: 'POST',
      data: {categoryid:categoryid},
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

function exporttoexcelscrapreport(){
  
  var startdate = $('#startdate').val();
  var enddate = $('#enddate').val();
  var categoryid = $('#categoryid').val();
  var productid = ($('#productid').val()!=null)?$('#productid').val().join(","):"";
  var producttype = ($('#producttype').val()!=null)?$('#producttype').val().join(","):"";
  var stocktype = ($('#stocktype').val()!=null)?$('#stocktype').val().join(","):"";
  var scraptype = ($('#scraptype').val()!=null)?$('#scraptype').val().join(","):"";
  
  var totalRecords =$("#scrapreporttable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"scrap-report/exporttoexcelscrapreport?startdate="+startdate+"&enddate="+enddate+"&categoryid="+categoryid+"&productid="+productid+"&producttype="+producttype+"&stocktype="+stocktype+"&scraptype="+scraptype;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  
}

function exporttopdfscrapreport(){

  var startdate = $('#startdate').val();
  var enddate = $('#enddate').val();
  var categoryid = $('#categoryid').val();
  var productid = ($('#productid').val()!=null)?$('#productid').val().join(","):"";
  var producttype = ($('#producttype').val()!=null)?$('#producttype').val().join(","):"";
  var stocktype = ($('#stocktype').val()!=null)?$('#stocktype').val().join(","):"";
  var scraptype = ($('#scraptype').val()!=null)?$('#scraptype').val().join(","):"";

  var totalRecords =$("#scrapreporttable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"scrap-report/exporttopdfscrapreport?startdate="+startdate+"&enddate="+enddate+"&categoryid="+categoryid+"&productid="+productid+"&producttype="+producttype+"&stocktype="+stocktype+"&scraptype="+scraptype;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
}

function printscrapreport(){

  var startdate = $('#startdate').val();
  var enddate = $('#enddate').val();
  var categoryid = $('#categoryid').val();
  var productid = $('#productid').val();
  var producttype = $('#producttype').val();
  var stocktype = $('#stocktype').val();
  var scraptype = $('#scraptype').val();

  var totalRecords =$("#scrapreporttable").DataTable().page.info().recordsDisplay;
    $.skylo('end');
    if(totalRecords != 0){
      var uurl = SITE_URL + "scrap-report/printscrapreport";
      $.ajax({
        url: uurl, 
        type: 'POST',
        data: {startdate:startdate,enddate:enddate,categoryid:categoryid,productid:productid,producttype:producttype,stocktype:stocktype,scraptype:scraptype},
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