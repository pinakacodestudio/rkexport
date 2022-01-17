
$(document).ready(function() {
    
    if(isdelete==1){
      var sort = [0,-1,-2,-4];
      var batchnotarrget = -4;
    }else{
      var sort = [0,-1,-3];
      var batchnotarrget = -3;
    }   
    if(HIDE_SELLER_IN_ORDER==1){
      var membercoltarget = [1];
    }else{
      var membercoltarget = [1,2];
    }
    //list("ordertable","Order/listing",[0,-1]);
    oTable = $('#ordertable').dataTable
      ({
        "language": {
          "lengthMenu": "_MENU_"
        },
        "pageLength": 10,
        "scrollCollapse": true,
        "scrollY": "500px",
        "scrollX": true,
        "columnDefs": [{
          'orderable': false,
          'targets': sort
        },{ "width": "15%", "targets": membercoltarget },{ "width": "15%", "targets": -2 },{ "width": "12%", "targets": batchnotarrget }],
        "order": [], //Initial no order.
        'serverSide': true,//Feature control DataTables' server-side processing mode.
        // Load data for the table's content from an Ajax source
        "ajax": {
          "url": SITE_URL+"order/listing",
          "type": "POST",
          "data": function ( data ) {
            data.startdate = $('#startdate').val();
            data.enddate = $('#enddate').val();
            data.status = $('#status').val();
            data.salespersonid = $('#salespersonid').val();
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
          savecollapse($(this).attr("display-type"),'panel-heading.filter-panel');

          return false;
      });
  });
  var displaytype = $('.panel-heading.filter-panel').attr("display-type");
  if(displaytype==0){

    $('.panel-heading.filter-panel').find('.button-icon span').html($(this).find('.button-icon span').text() == 'keyboard_arrow_down' ? 'keyboard_arrow_up' : 'keyboard_arrow_down');
    //$(this).children().toggleClass(" ");
    $('.panel-heading.filter-panel').next().slideToggle({duration: 200});
    $('.panel-heading.filter-panel').toggleClass('panel-collapsed');
  }
});
function applyFilter(){
  oTable.fnDraw();
}
function exportorderreport(){
  
  var productid = $('#productid').val();
  var customerid = $('#customerid').val();
  var fromdate = $('#fromdate').val();
  var todate = $('#todate').val();
  var orderstatus = $('#orderstatus').val();
  var portalid = $('#portalid').val();
  
  var totalRecords =$("#ordertable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"order/exportorderreport?productid="+productid+"&customerid="+customerid+"&fromdate="+fromdate+"&todate="+todate+"&orderstatus="+orderstatus+"&portalid="+portalid;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  
}
function chageorderstatus(status, orderId, ordernumber, membername=''){
    var uurl = SITE_URL+"order/update-status";
        if(orderId!=''){
              swal({    title: "Are you sure to change status?",
                type: "warning",   
                showCancelButton: true,   
                confirmButtonColor: "#DD6B55",   
                confirmButtonText: "Yes, change it!",   
                closeOnConfirm: false }, 
                function(isConfirm){   
                  if (isConfirm) {   
                    $.ajax({
                        url: uurl,
                        type: 'POST',
                        data: {status:status,orderId:orderId, ordernumber:ordernumber, membername:membername},
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
                  });

            }           
}
function approveorder(status, orderId){
  var uurl = SITE_URL+"order/approveorder";
  if(orderId!=''){
    swal({title: "Are you sure to approve order?",
    type: "warning",   
    showCancelButton: true,   
    confirmButtonColor: "#DD6B55",   
    confirmButtonText: "Yes, approve it!",   
    closeOnConfirm: false }, 
    function(isConfirm){   
      if (isConfirm) {   
        if(status==2){
              
          $('#rejectorderModal').modal('show');
          $('#rejectionorderid').val(orderId);
          $('#rejectionstatus').val(status);
        }else{ 
          $.ajax({
            url: uurl,
            type: 'POST',
            data: {status:status,orderId:orderId},
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
function checkvalidationforrejectionorder(){

  var resonforrejection = $('#resonforrejection').val();
  var orderId = $('#rejectionorderid').val();
  var status = $('#rejectionstatus').val();
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
    var uurl = SITE_URL+"order/approveorder";
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {status:status,orderId:orderId,resonforrejection:resonforrejection},
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
function printorderinvoice(id){

  //var orderid = $('#orderid').val();
    
    var uurl = SITE_URL + "order/printOrderInvoice";
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
function regenerateorderpdf(orderid){
  swal({title: 'Are you sure want to re-generate order PDF ?',
  type: "warning",   
  showCancelButton: true,   
  confirmButtonColor: "#DD6B55",   
  confirmButtonText: "Yes, Re-generate it!",
  timer: 2000,   
  closeOnConfirm: false }, 
  function(isConfirm){
    if (isConfirm) {   
      
      $.ajax({
        url: SITE_URL+"order/regenerateorderpdf",
        type: 'POST',
        data: {orderid:orderid},
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
        },
        success: function (data) {
          swal.close();
          var obj = JSON.parse(data);
          if(obj['error']==1){
            setTimeout(function() { var w = window.open(obj['invoice'],'_blank'); w.print(); }, 500);
          }else{
            new PNotify({title: 'PDF not re-generate !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
function savecollapse(panelcollapsed,cls){
  var uurl = SITE_URL+"order/savecollapse";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {panelcollapsed:panelcollapsed},
    dataType: 'json',
    success: function(response){
      if(response.panelcollapsed=='1'){
        $("."+cls).attr("display-type","0");
      }else{
        $("."+cls).attr("display-type","1");
      }
    },
    error: function(xhr) {
      //alert(xhr.responseText);
    },
  }); 
}
function exportorders(){
  
  var startdate = $('#startdate').val();
  var enddate = $('#enddate').val();
  var status = $('#status').val();
  var salespersonid = $('#salespersonid').val();
  
  var totalRecords =$("#ordertable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"order/exportorders?startdate="+startdate+"&enddate="+enddate+"&status="+status+"&salespersonid="+salespersonid;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  
}
function checkwhatsappnumber(id){

  whasappnumber = $("#checkwhatsappnumber"+id).val();
    if(whasappnumber==''){
      new PNotify({title: 'Whatsapp number not available !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }

}