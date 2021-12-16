
$(document).ready(function() {

    $('#datepicker-range').datepicker({
      todayHighlight: true,
      format: 'dd/mm/yyyy',
      orientation: 'top',
      autoclose: true,
      todayBtn:"linked"
    });
  
      oTable = $('#paymenttable').DataTable({
        "language": {
          "lengthMenu": "_MENU_"
        },
        "pageLength": 10,
       /*  "scrollCollapse": true,
        "scrollY": "500px", */
        "columnDefs": [{
          'orderable': false,
          'targets': [0,-1,-2]
        },
        { targets: [7], className: "text-right" },
        { targets: [6], className: "text-center" }],
        "order": [], //Initial no order.
        'serverSide': true,//Feature control DataTables' server-side processing mode.
        // Load data for the table's content from an Ajax source
        "ajax": {
          "url": SITE_URL+"payment/listing",
          "type": "POST",
          "data": function ( data ) {
            data.startdate = $('#startdate').val();
            data.enddate = $('#enddate').val();
            data.vendorid = $('#vendorid').val();
            data.transactiontype = $('#transactiontype').val();
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
  });
  
  function applyFilter(){
    oTable.ajax.reload(null,false);
  }
  
  
  function changestatus(status, id){
    var uurl = SITE_URL+"payment/update-status";
    if(id!=''){
      swal({    title: "Are you sure to change status?",
        type: "warning",   
        showCancelButton: true,   
        confirmButtonColor: "#DD6B55",   
        confirmButtonText: "Yes, change it!",   
        closeOnConfirm: false }, 
      function(isConfirm){   
        if (isConfirm) {  
          if(status==1){
              
            $('#paymentModal').modal('show');
            $('#paymentid').val(id);
            $('#status').val(status);
            // $('#resonforrejection').val('');
          }else if(status==2){
              
            $('#rejectModal').modal('show');
            $('#rejectionid').val(id);
            $('#rejectionstatus').val(status);
            $('#resonforrejection').val('');
          }else{ 
            $.ajax({
              url: uurl,
              type: 'POST',
              data: {status:status,id:id},
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
  
  function checkvalidationforrejectionpayment(){
  
    var resonforcancellation = $('#resonforrejection').val();
    var id = $('#rejectionid').val();
    var status = $('#rejectionstatus').val();
    var isvalidresonforrejection = 1;
    
    PNotify.removeAll();
    $("#resonalert").html('');
  
    if(resonforcancellation == ''){
      $("#resonforrejection_div").addClass("has-error is-focused");
      $("#resonalert").html('<i class="fa fa-exclamation-triangle"></i> Please enter reson for cancellation !');
      isvalidresonforrejection = 0;
    }else {
      if(resonforcancellation.length < 3){
        $("#resonforrejection_div").addClass("has-error is-focused");
        $("#resonalert").html('<i class="fa fa-exclamation-triangle"></i> Reson require minimum 3 characters !');
        isvalidresonforrejection = 0;
      }
    }
    if(isvalidresonforrejection == 1){
      var uurl = SITE_URL+"payment/update-status";
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {status:status,id:id,resonforcancellation:resonforcancellation},
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
        },
        success: function(response){
          if(response==1){
            location.reload();
          }else{
            new PNotify({title: response,styling: 'fontawesome',delay: '3000',type: 'error'});
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
  
  function checkvalidationpayment(){
  
    var cashorbankid = $('#cashorbankid').val();
    var id = $('#paymentid').val();
    var status = $('#status').val();
    var isvalidcashorbankid = 1;
    
    PNotify.removeAll();
    $("#erroralert").html('');
  
    if(cashorbankid == 0){
      $("#cashorbankid_div").addClass("has-error is-focused");
      $("#erroralert").html('<i class="fa fa-exclamation-triangle"></i> Please select cash or bank account !');
      isvalidcashorbankid = 0;
    }else {
      $("#cashorbankid_div").removeClass("has-error is-focused");
    }
    if(isvalidcashorbankid == 1){
      var uurl = SITE_URL+"payment/update-status";
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {status:status,id:id,cashorbankid:cashorbankid},
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
        },
        success: function(response){
            if(response==1){
              location.reload();
            }else{
              new PNotify({title: response,styling: 'fontawesome',delay: '3000',type: 'error'});
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
  
  function printPayment(id){
  
    var uurl = SITE_URL + "payment/printPayment";
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {id:id},
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