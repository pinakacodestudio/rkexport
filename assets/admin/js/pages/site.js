$(document).ready(function() {
    oTable = $('#sitetable').DataTable({
      "language": {
        "lengthMenu": "_MENU_"
      },
      
      "pageLength": 10,
      "columnDefs": [{
        'orderable': false,
        'targets': [-1,-2,-4]
      }],
      "order": [], //Initial no order.
    });

    $('.dataTables_filter input').attr('placeholder','Search...');

    //DOM Manipulation to move datatable elements integrate to panel
    $('.panel-ctrls.panel-tbl').append($('.dataTables_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls.panel-tbl').append("<i class='separator'></i>");
    $('.panel-ctrls.panel-tbl').append($('.dataTables_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('.panel-footer').append($(".dataTable+.row"));
    $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-md");
});


function exportToExcelSite(){
  
  var totalRecords =$("#sitetable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"site/exportToExcelSite";
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
}

function exportToPDFSite(){

  var totalRecords =$("#sitetable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){ 
      window.location= SITE_URL+"site/exportToPDFSite";
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
}

function printSiteDetails(){
  var totalRecords =$("#sitetable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    var uurl = SITE_URL + "site/printSiteDetails";
      $.ajax({
        url: uurl,
        type: 'POST',
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
            //alert(xhr.responseText);
        },
        complete: function() {
            $('.mask').hide();
            $('#loader').hide();
        },
      });
  }
}