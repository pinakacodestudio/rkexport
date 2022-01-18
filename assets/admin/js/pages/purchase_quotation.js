
$(document).ready(function() {
    
    //list("quotationtable","Order/listing",[0,-1]);
    oTable = $('#purchasequotationtable').DataTable
      ({
        "language": {
          "lengthMenu": "_MENU_"
        },
        "pageLength": 10,
        "scrollCollapse": true,
        "scrollY": "500px",
        "columnDefs": [{
          'orderable': false,
          'targets': [0,-1]
        },
        {targets: [4], className: "text-center"},
        {targets: [5], className: "text-right"}],
        "order": [], //Initial no order.
        'serverSide': true,//Feature control DataTables' server-side processing mode.
        // Load data for the table's content from an Ajax source
        "ajax": {
          "url": SITE_URL+"purchase-quotation/listing",
          "type": "POST",
          "data": function ( data ) {
            data.startdate = $('#startdate').val();
            data.enddate = $('#enddate').val();
            data.status = $('#status').val();
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
    $('.dataTables_filter input').attr('placeholder','Search...');

    $('.panel-footer').append($('.dataTables_info').parent().parent());

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
});

function applyFilter(){
  oTable.ajax.reload(null,false);
}
function exportquotationreport(){
  
  var productid = $('#productid').val();
  var vendorid = $('#vendorid').val();
  var fromdate = $('#fromdate').val();
  var todate = $('#todate').val();
  var quotationstatus = $('#quotationstatus').val();
  var portalid = $('#portalid').val();
  
  var totalRecords =$("#quotationtable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"Order/exportquotationreport?productid="+productid+"&vendorid="+vendorid+"&fromdate="+fromdate+"&todate="+todate+"&quotationstatus="+quotationstatus+"&portalid="+portalid;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  
}

      
function chagequotationstatus(status, quotationId, quotationnumber){
    var uurl = SITE_URL+"purchase-quotation/update-status";
        if(quotationId!=''){
              swal({    title: "Are you sure to change status?",
                type: "warning",   
                showCancelButton: true,   
                confirmButtonColor: "#DD6B55",   
                confirmButtonText: "Yes, change it!",   
                closeOnConfirm: false }, 
                function(isConfirm){   
                  if (isConfirm) {  
                    if(status==2){
              
                      $('#rejectquotationModal').modal('show');
                      $('#rejectionquotationid').val(quotationId);
                      $('#rejectionstatus').val(status);
                      $('#rejectionquotationno').val(quotationnumber);
                    }else{  
                      $.ajax({
                          url: uurl,
                          type: 'POST',
                          data: {status:status,quotationId:quotationId},
                          beforeSend: function(){
                            $('.mask').show();
                            $('#loader').show();
                          },
                          success: function(response){
                            if(response==1){
                                location.reload();
                            }
                          },
                          complete: function(){
                            $('.mask').hide();
                            $('#loader').hide();
                          },
                          error: function(xhr) {
                          //alert(xhr.responseText);
                          }
                        });  
                      }
                    }
                  });

            }           
}

function checkvalidationforrejectionquotation(){

  var resonforrejection = $('#resonforrejection').val();
  var quotationId = $('#rejectionquotationid').val();
  var status = $('#rejectionstatus').val();
  var quotationnumber = $('#rejectionquotationno').val();

  var isvalidresonforrejection = 1;
  
  PNotify.removeAll();
  $("#resonalert").html('');

  if(resonforrejection == ''){
    $("#resonforrejection_div").addClass("has-error is-focused");
    $("#resonalert").html('<i class="fa fa-exclamation-triangle"></i> Please enter reson for rejection !');
    isvalidresonforrejection = 0;
  }else {
    if(resonforrejection.length < 3){
      $("#resonforrejection_div").addClass("has-error is-focused");
      $("#resonalert").html('<i class="fa fa-exclamation-triangle"></i> Reson require minimum 3 characters !');
      isvalidresonforrejection = 0;
    }
  }
  if(isvalidresonforrejection == 1){
    var uurl = SITE_URL+"purchase-quotation/update-status";
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {status:status,quotationId:quotationId, quotationnumber:quotationnumber, resonforrejection:resonforrejection},
      
      success: function(response){
        if(response==1){
            location.reload();
        }
      },
      error: function(xhr) {
      //alert(xhr.responseText);
      }
    });  
  }

}

function regeneratequotation(quotationid){
  swal({title: 'Are you sure want to re-generate quotation ?',
  type: "warning",   
  showCancelButton: true,   
  confirmButtonColor: "#DD6B55",   
  confirmButtonText: "Yes, Re-generate it!",
  timer: 2000,   
  closeOnConfirm: false }, 
  function(isConfirm){
    if (isConfirm) {   
      
      $.ajax({
        url: SITE_URL+"quotation/regeneratequotation",
        type: 'POST',
        data: {quotationid:quotationid},
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
        },
        success: function (data) {
          swal.close();
          var obj = JSON.parse(data);
          if(obj['error']==1){
            setTimeout(function() { var w = window.open(obj['quotation'],'_blank'); w.print(); }, 500);
          }else{
            new PNotify({title: 'Quotation not re-generate !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }
        },
        complete: function(){
          $('.mask').hide();
          $('#loader').hide();
        },
      });
    }
  });
}

function printquotationinvoice(id){

  //var orderid = $('#orderid').val();
    
    var uurl = SITE_URL + "purchase-quotation/printPurchaseQuotationInvoice";
    $.ajax({
        url: uurl,
        type: 'POST',
        data: {
            id:id
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
            //alert(xhr.responseText);
        },
        complete: function() {
            $('.mask').hide();
            $('#loader').hide();
        },
    });

}