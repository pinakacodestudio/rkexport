$(document).ready(function() {
    
    oTable = $('#stockgeneralvouchertable').DataTable({
        "language": {
            "lengthMenu": "_MENU_"
        },
        drawCallback: function () {
          loadpopover();
        },
        "pageLength": 10,
        "columnDefs": [{
            'orderable': false,
            'targets': [-1,-2]
        },
        {"targets":[7],className: "text-center"},
        {"targets":[4,5,6],className: "text-right"}],
        "order": [], //Initial no order.
        'serverSide': true,//Feature control DataTables' server-side processing mode.
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": SITE_URL+"stock-general-voucher/listing",
            "type": "POST",
            "data": function ( data ) {
                data.productid = $("#productid").val();
                data.type = $("#type").val();
                data.startdate = $("#startdate").val();
                data.enddate = $("#enddate").val();
            },
            beforeSend: function(){
                $('.mask').show();
                $('#loader').show();
            },
            error: function(xhr) {
            //alert(xhr.responseText);
            },
            complete: function(e){
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

    $('#attachment').change(function(){
        var val = $(this).val();
        var filename = $("#attachment").val().replace(/C:\\fakepath\\/i, '');
        
        switch(val.substring(val.lastIndexOf('.') + 1).toLowerCase()){
          case 'xl': case 'xlc': case 'xls' : case 'xlsx' : case 'ods':
            $("#Filetext").val(filename);
            isvalidfiletext = 1;
            $("#attachment_div").removeClass("has-error is-focused");
            break;
          default:
            $("#Filetext").val("");
            isvalidfiletext = 0;
            $("#attachment_div").addClass("has-error is-focused");
            new PNotify({title: 'Please upload valid excel file !',styling: 'fontawesome',delay: '3000',type: 'error'});
            break;
        }
      });
});

function applyFilter(){
    oTable.ajax.reload();
}

function importstockgeneralvoucher(){
    PNotify.removeAll();
  
    $("#attachment_div").removeClass("has-error is-focused");
    $("#Filetext").val("");
    $('.selectpicker').selectpicker('refresh');  
    $('#ImportModal').modal('show');
}
function checkimportvalidation(){
  
    var filetext = $("#Filetext").val();
  
    var isvalidfiletext = 0;
    
    PNotify.removeAll();
    
    //CHECK FILE
    if(filetext==''){
      $("#attachment_div").addClass("has-error is-focused");
      new PNotify({title: 'Please select excel file !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }else{
        $("#attachment_div").removeClass("has-error is-focused");
        isvalidfiletext = 1;
    }
    if(isvalidfiletext==1){
      
      var formData = new FormData($('#importform')[0]);
  
      var uurl = SITE_URL+"stock-general-voucher/importstockgeneralvoucher";
      
      $.ajax({
        url: uurl,
        type: 'POST',
        data: formData,
        //async: false,
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
        },
        success: function(response){
          if(response==1){
              new PNotify({title: "Stock general voucher successfully imported.",styling: 'fontawesome',delay: '3000',type: 'success'});
               setTimeout(function() { window.location.reload(); }, 1500);
          }else if(response=='2'){
            new PNotify({title: "Uploaded file is not an excel file !",styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(response=='3'){
            new PNotify({title: "Excel file not uploaded !",styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(response=='4'){
            new PNotify({title: "Some field name are not match !",styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(response=='5'){
            new PNotify({title: "Please enter at least one product detail !",styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(response=='6'){
            new PNotify({title: "Please enter valid sheet name !",styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
            new PNotify({title: response,styling: 'fontawesome',delay: '3000',type: 'error'});
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
  }