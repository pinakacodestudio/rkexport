$(document).ready(function() {
    
    oTable = $('#vendorjobworktable').DataTable({
        "language": {
            "lengthMenu": "_MENU_"
        },
        "pageLength": 10,
        "columnDefs": [{
            'orderable': false,
            'targets': [0,-1]
        },{className: "text-right", targets: [7,8,9,10,11,12]}],
        "order": [], //Initial no order.
        'serverSide': true,//Feature control DataTables' server-side processing mode.
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": SITE_URL+"vendor-job-work/listing",
            "type": "POST",
            "data": function ( data ) {
               data.vendorid = $("#vendorid").val();
               data.productid = $("#productid").val();
               data.startdate = $("#startdate").val();
               data.enddate = $("#enddate").val();
               data.batchno = $("#batchno").val();
               data.type = $("#type").val();
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
                response.success(function(data){
                    if(data['closingbalance']!=null){
                        var balance = "Closing Qty : "+format.format(data['closingbalance']);
                        $('#vendor_closing_balance').html(balance).show();
                    }else{
                        $('#vendor_closing_balance').html('Closing Qty : 0.00').show();
                    }
                    if(data['openingbalance']!=null){
                        var balance = "Opening Qty : "+format.format(data['openingbalance']);
                        $('#vendor_opening_balance').html(balance).show();
                    }else{
                        $('#vendor_opening_balance').html("Opening Qty : 0.00").show();
                    }
                });
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

    var vendor = $("#vendorid").val();
    $('#vendor_opening_balance').html("Opening Qty : 0.00").show();
    $('#vendor_closing_balance').html("Closing Qty : 0.00").show();
    
    if(vendor==0){
        $("#vendor_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select vendor !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }else{
        $("#vendor_div").removeClass("has-error is-focused");
        oTable.ajax.reload(null, false);
    }
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
function exporttoexcelvendorjobworkreport(){
  
    var startdate = $("#startdate").val();
    var enddate = $("#enddate").val();
    var vendorid = $("#vendorid").val();
    var productid = $("#productid").val();
    var batchno = $("#batchno").val();
    var type = $("#type").val();

    var totalRecords =$("#vendorjobworktable").DataTable().page.info().recordsDisplay;
    $.skylo('end');
    if(totalRecords != 0){
        window.location= SITE_URL+"vendor-job-work/exporttoexcelvendorjobworkreport?startdate="+startdate+"&enddate="+enddate+"&vendorid="+vendorid+"&productid="+productid+"&batchno="+batchno+"&type="+type;
    }else{
        new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }
    
}
function exporttopdfvendorjobworkreport(){
  
    var startdate = $("#startdate").val();
    var enddate = $("#enddate").val();
    var vendorid = $("#vendorid").val();
    var productid = $("#productid").val();
    var batchno = $("#batchno").val();
    var type = $("#type").val();

    var totalRecords =$("#vendorjobworktable").DataTable().page.info().recordsDisplay;
    $.skylo('end');
    if(totalRecords != 0){
  
      window.location= SITE_URL+"vendor-job-work/exporttopdfvendorjobworkreport?startdate="+startdate+"&enddate="+enddate+"&vendorid="+vendorid+"&productid="+productid+"&batchno="+batchno+"&type="+type;
    }else{
      new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }
}
function printvendorjobworkreport(){

    var startdate = $("#startdate").val();
    var enddate = $("#enddate").val();
    var vendorid = $("#vendorid").val();
    var productid = $("#productid").val();
    var batchno = $("#batchno").val();
    var type = $("#type").val();

    var totalRecords =$("#vendorjobworktable").DataTable().page.info().recordsDisplay;
      $.skylo('end');
      if(totalRecords != 0){
        var uurl = SITE_URL + "vendor-job-work/printvendorjobworkreport";
        $.ajax({
          url: uurl, 
          type: 'POST',
          data: {startdate:startdate,enddate:enddate,vendorid:vendorid,productid:productid,batchno:batchno,type:type},
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