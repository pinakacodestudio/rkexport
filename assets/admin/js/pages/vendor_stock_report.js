$(document).ready(function() {
    
    oTable = $('#vendorstockreporttable').DataTable({
        "language": {
            "lengthMenu": "_MENU_"
        },
        "pageLength": 10,
        "columnDefs": [{
            'orderable': false,
            'targets': [0]
        },{className: "text-right", targets: [6,7,8]}],
        "order": [], //Initial no order.
        'serverSide': true,//Feature control DataTables' server-side processing mode.
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": SITE_URL+"vendor-stock-report/listing",
            "type": "POST",
            "data": function ( data ) {
                data.startdate = $("#startdate").val();
                data.enddate = $("#enddate").val();
                data.vendorid = $("#vendorid").val();
                data.productid = $("#productid").val();
                data.batchno = $("#batchno").val();
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
                
                $("#rowstotal7").html(format.format(response.responseJSON.totalqty));
                $("#rowstotal8").html(format.format(response.responseJSON.totalamount));
            },
        },
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(),data;
            
            if(!$.isEmptyObject(data)){
              $.footer = '';
              $('.footercls').remove();
              if($('#vendorstockreporttable tfoot').length==0){
                $.footer += '<tfoot>';
              }
              
              // if($('.footercls').length==0){
                $.footer += '<tr class="footercls">';
            
                $.footer += '<th colspan="7" class="totalrows text-right">Total</th>';
            
                this.api().columns().each(function () {
                  var column = this;
                  if(column[0].length > 0){

                    // Remove the formatting to get integer data for summation
                    var intVal = function (i) {
                        return typeof i === 'string' ?
                            i.replace(/[\L,]/g, '') * 1 :
                            typeof i === 'number' ?
                            i : 0;
                    };

                    var pageTotalQty = api
                        .column( column[0][7], { page: 'current'} )
                        .data()
                        .reduce( function (a, b) {
                          var a = intVal(a) || 0;
                          var b = intVal(b) || 0;
                          return parseFloat(a) + parseFloat(b);
                        }, 0 );
                     
                    var pageTotalAmount = api
                        .column( column[0][8], { page: 'current'} )
                        .data()
                        .reduce( function (a, b) {
                          var a = intVal(a) || 0;
                          var b = intVal(b) || 0;
                          return parseFloat(a) + parseFloat(b);
                        }, 0 );
                    $.footer += '<th class="totalrows text-right" style="padding:10px 15px;">'+ format.format(pageTotalQty) +' <br>(<span id="rowstotal7"></span>)</th>';

                    $.footer += '<th class="totalrows text-right" style="padding:10px 15px;">'+ CURRENCY_CODE+format.format(pageTotalAmount) +' <br>('+CURRENCY_CODE+'<span id="rowstotal8" ></span>)</th>\
                                <th class="totalrows text-right"></th>';

                  }
                });
                $.footer += '</tr>';
              // }
              if($('#vendorstockreporttable tfoot').length==0){
                $.footer += '</tfoot>';
                $('#vendorstockreporttable tbody').after($.footer);
              }else{
                $('#vendorstockreporttable tfoot').prepend($.footer);
              }
            }else{
              $('#vendorstockreporttable tfoot').remove();
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
        // todayHighlight: true,
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayBtn:"linked",
        /* startDate: new Date(), */
    });
    
    $('#productcategoryid').on('change', function (e) {
        getproduct();
    });
});

function applyFilter(){
    $('.footercls').remove();
    oTable.ajax.reload(null, false);
}

function getproduct(){
    
    $('#productid').find('option')
        .remove()
        .end()
        .append('<option value="">All Product</option>')
        .val('0')
    ;
    $('#productid').selectpicker('refresh');
    var categoryid = $("#productcategoryid").val();
    
    if(categoryid != '0'){
      var uurl = SITE_URL+"process-group/getProductByCategoryId";
      
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {categoryid:String(categoryid)},
        dataType: 'json',
        async: false,
        success: function(response){
  
            for(var i = 0; i < response.length; i++) {
                $('#productid').append($('<option>', { 
                    value: response[i]['id'],
                    text : response[i]['name']
                }));
            }
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
      });
    }
    $('#productid').selectpicker('refresh');
}

function exporttoexcelvendorstockreport(){
  
    var startdate = $("#startdate").val();
    var enddate = $("#enddate").val();
    var vendorid = $("#vendorid").val();
    var productid = $("#productid").val();
    var batchno = $("#batchno").val();
  
    var totalRecords =$("#vendorstockreporttable").DataTable().page.info().recordsDisplay;
    $.skylo('end');
    if(totalRecords != 0){
        window.location= SITE_URL+"vendor-stock-report/exporttoexcelvendorstockreport?startdate="+startdate+"&enddate="+enddate+"&vendorid="+vendorid+"&productid="+productid+"&batchno="+batchno;
    }else{
        new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }
    
}
function exporttopdfvendorstockreport(){
  
    var startdate = $("#startdate").val();
    var enddate = $("#enddate").val();
    var vendorid = $("#vendorid").val();
    var productid = $("#productid").val();
    var batchno = $("#batchno").val();
  
    var totalRecords =$("#vendorstockreporttable").DataTable().page.info().recordsDisplay;
    $.skylo('end');
    if(totalRecords != 0){
  
      window.location= SITE_URL+"vendor-stock-report/exporttopdfvendorstockreport?startdate="+startdate+"&enddate="+enddate+"&vendorid="+vendorid+"&productid="+productid+"&batchno="+batchno;
    }else{
      new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }
}


function printvendorstockreport(){

    var startdate = $("#startdate").val();
    var enddate = $("#enddate").val();
    var vendorid = $("#vendorid").val();
    var productid = $("#productid").val();
    var batchno = $("#batchno").val();
  
    var totalRecords =$("#vendorstockreporttable").DataTable().page.info().recordsDisplay;
      $.skylo('end');
      if(totalRecords != 0){
        var uurl = SITE_URL + "vendor-stock-report/printvendorstockreport";
        $.ajax({
          url: uurl, 
          type: 'POST',
          data: {startdate:startdate,enddate:enddate,vendorid:vendorid,productid:productid,batchno:batchno},
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