$(document).ready(function () {
    $('#datepicker-range').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayBtn: "linked",
        clearBtn: true
    });

    oTable = $('#expireinsuranncetable').DataTable({
        "language": {
            "lengthMenu": "_MENU_"
        },
        "pageLength": 50,
        "columnDefs": [{
            "targets": [],
            "orderable": false
          },{
            targets: [-2],
            className: "text-right"}],
        "order": [], //Initial no order.
        'serverSide': true,//Feature control DataTables' server-side processing mode.
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": SITE_URL+'expire-insurance-report/listing',
            "type": "POST",
            "data": function ( data ) {
              data.insurancecompany = $("#insurancecompany").val();
              data.days = $('#days').val();
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
    });
    $('.dataTables_filter input').attr('placeholder', 'Search...');

    //DOM Manipulation to move datatable elements integrate to panel
    $('.panel-ctrls.panel-tbl').append($('.dataTables_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls.panel-tbl').append("<i class='separator'></i>");
    $('.panel-ctrls.panel-tbl').append($('.dataTables_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('.panel-footer').append($(".dataTable+.row"));
    $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-md");

    $(function () {
        $('.panel-heading.filter-panel').click(function () {
            $(this).find('.button-icon span').html($(this).find('.button-icon span').text() == 'keyboard_arrow_down' ? 'keyboard_arrow_up' : 'keyboard_arrow_down');
            //$(this).children().toggleClass(" ");
            $(this).next().slideToggle({
                duration: 200
            });
            $(this).toggleClass('panel-collapsed');
            return false;
        });
    });
});

function applyFilter() {
    oTable.ajax.reload(null, false);
}


function exportToExcelExpireinsuranceReport(){

    var insurancecompany = $("#insurancecompany").val();
    var days = $('#days').val();
    
    var totalRecords =$("#expireinsuranncetable").DataTable().page.info().recordsDisplay;
    $.skylo('end');
    if(totalRecords != 0){
      window.location= SITE_URL+"expire-insurance-report/exportToExcelExpireinsuranceReport?insurancecompany="+insurancecompany+"&days="+days;
    }else{
      new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }
    
  }

  function exportToPDFInsuranceReport(){

    var insurancecompany = $("#insurancecompany").val();
    var days = $('#days').val();
    
    var totalRecords =$("#expireinsuranncetable").DataTable().page.info().recordsDisplay;
    $.skylo('end');
    if(totalRecords != 0){ 
  
      window.location= SITE_URL+"expire-insurance-report/exportToPDFInsuranceReport?insurancecompany="+insurancecompany+"&days="+days;
    }else{
      new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }
  }
  function printExpireInsuranceReport(){
    var insurancecompany = $("#insurancecompany").val();
    var days = $('#days').val();

  var totalRecords =$("#expireinsuranncetable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
      var uurl = SITE_URL + "expire-insurance-report/printExpireInsuranceReport";
      $.ajax({
          url: uurl,
          type: 'POST',
          data: {
            insurancecompany:insurancecompany,
            days:days
          },
          //dataType: 'json',
          async: false,
          beforeSend: function() {
              $('.mask').show();
              $('#loader').show();
          },
          success: function(response) {
              
          var data = JSON.parse(response);
          var html = data['content'];
          
          var frame1 = document.createElement("iframe");
          frame1.name = "frame1";
          frame1.style.position = "absolute";
          frame1.style.top = "-1000000px";
          document.body.appendChild(frame1);
          var frameDoc = frame1.contentWindow ? frame1.contentWindow : frame1.contentDocument.document ? frame1.contentDocument.document : frame1.contentDocument;
          frameDoc.document.open();
          frameDoc.document.write(html);
          frameDoc.document.close();
          setTimeout(function () {
              window.frames["frame1"].focus();
              window.frames["frame1"].print();
              document.body.removeChild(frame1);
          }, 500);
          },
          error: function(xhr) {
              // alert(xhr.responseText);
          },
          complete: function() {
              $('.mask').hide();
              $('#loader').hide();
          },
      });
  }
  else{
      new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
}