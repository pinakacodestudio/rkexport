
$(document).ready(function() {
    
    //list("ordertable","Order/listing",[0,-1]);
    oTable = $('#shiprocketordertable').dataTable
      ({
        "language": {
          "lengthMenu": "_MENU_"
        },
        "pageLength": 50,
        "scrollCollapse": true,
        "scrollY": "500px",
        /* "scrollX": true, */
        "columnDefs": [{
          'orderable': false,
          'targets': [0,-1,-2,-3,-4,-5]
        }],
        "order": [], //Initial no order.
        'serverSide': true,//Feature control DataTables' server-side processing mode.
        // Load data for the table's content from an Ajax source
        "ajax": {
          "url": SITE_URL+"shiprocket_order/listing",
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


function savecollapse(panelcollapsed,cls){
  var uurl = SITE_URL+"shiprocket_order/savecollapse";
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


function cancelorder(shiprocketorderid,id){
  var uurl = SITE_URL+"Shiprocket_order/cancel_order";
  if(id!=''){
        swal({    title: "Are you sure to cancel order?",
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
                  data: {shiprocketorderid:shiprocketorderid,id:id},
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


function generateAwB(shiprocketshipmentid,shippingorderid,invoiceid){
  var uurl = SITE_URL+"Shiprocket_order/generateAwB";
  if(shiprocketshipmentid!=''){
        swal({    title: "Are you sure to generate AWB Code?",
          type: "warning",   
          showCancelButton: true,   
          confirmButtonColor: "#DD6B55",   
          confirmButtonText: "Yes, generate it!",   
          closeOnConfirm: false }, 
          function(isConfirm){   
            if (isConfirm) {   
              $.ajax({
                  url: uurl,
                  type: 'POST',
                  data: {shiprocketshipmentid:shiprocketshipmentid,shippingorderid:shippingorderid,invoiceid:invoiceid},
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
