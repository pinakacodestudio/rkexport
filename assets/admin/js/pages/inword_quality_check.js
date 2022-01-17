
$(document).ready(function() {
  $('#grnid')
      .find('option')
      .remove()
      .end()
      .append('<option value="0">Select GRN No.</option>')
      .val('0')
  ;
  
  // $('#grnid').on('change',function(){
  //   var grnid  = $("#grnid").prop('selected');
  //   alert(grnid);
  // });
  $('#startdate').datepicker({
    todayHighlight: true,
    format: 'dd/mm/yyyy',
    todayBtn:"linked",
  });

  $('#enddate').datepicker({
    todayHighlight: true,
    format: 'dd/mm/yyyy',
    todayBtn:"linked",
  });
  // $('#grnid')
  oTable = $('#inwordtable').DataTable({

      "processing": true,//Feature control the processing indicator.
      "language": {
        "lengthMenu": "_MENU_"
      },
      drawCallback: function () {
        loadpopover();
      },
      "pageLength": 10,
      /* "scrollCollapse": true,
      "scrollY": "500px", */
      "columnDefs": [{
        'orderable': false,
        'targets': [0,3,-1,-2,-4]
      }],
      "order": [], //Initial no order.
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": SITE_URL+"inword-quality-check/listing",
        "type": "POST",
        "data": function ( data ) {
          data.vendorid = $("#vendorid").val();
          data.grnid = $("#grnid").val();
          // alert(data.grnid);
          data.statusid = $("#statusid").val();
          data.startdate = $("#startdate").val();
          data.enddate = $("#enddate").val();
          data.orderid = $("#orderid").val();
        },
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
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
  $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-lg");
  

  $(function () {
    $('.panel-heading.filter-panel').click(function() {
        $(this).find('.button-icon span').html($(this).find('.button-icon span').text() == 'keyboard_arrow_down' ? 'keyboard_arrow_up' : 'keyboard_arrow_down');
        //$(this).children().toggleClass(" ");
        $(this).next().slideToggle({duration: 200});
        $(this).toggleClass('panel-collapsed');
        return false;
    });
  });

  $('#vendorid').on('change', function (e) {
    getVendorGRN();
  });


});

function getVendorGRN(){
  $('#grnid')
      .find('option')
      .remove()
      .end()
      .append('<option value="0">Select GRN No.</option>')
      .val('0')
  ;

    if(ACTION==1){
      $('#grnid').val(GRNId);
    }

  $('#grnid').selectpicker('refresh');

  var vendorid = $("#vendorid").val();
  
  if(vendorid!=0){
    var uurl = SITE_URL+"vendor/getVendorGRN";
    
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {vendorid:String(vendorid),from:'invoice'},
      dataType: 'json',
      async: false,
      success: function(response){

        for(var i = 0; i < response.length; i++) {

          if(ACTION==1){
            if(GRNId!=null || GRNId!=''){
             
              GRNId = GRNId.toString().split(',');
             
              if(GRNId.includes(response[i]['id'])){
                $('#grnid').append($('<option>', { 
                  value: response[i]['id'],
                  selected: "selected",
                  text : ucwords(response[i]['grnnumber']),
                  "data-billingid": response[i]['billingid'],
                  "data-shippingid": response[i]['shippingid']
                }));
              }else{
                $('#grnid').append($('<option>', { 
                  value: response[i]['id'],
                  text : ucwords(response[i]['grnnumber']),
                  "data-billingid": response[i]['billingid'],
                  "data-shippingid": response[i]['shippingid']
                }));
              }
            }
          }else{
            $('#grnid').append($('<option>', { 
              value: response[i]['id'],
              text : ucwords(response[i]['grnnumber']),
              "data-billingid": response[i]['billingid'],
              "data-shippingid": response[i]['shippingid']
            }));
          }
        }
      },
      error: function(xhr) {
      //alert(xhr.responseText);
      },
    });
  }else{
    $("#inwordtable tbody").html("<tr><td colspan='9' class='text-center'>No data available in table.</td></tr>");
  }
  $('#grnid').selectpicker('refresh');
  
}

function applyFilter(){
oTable.ajax.reload();
}

function changequalitystatus(status, inwordId){
  var uurl = SITE_URL+"inword-quality-check/update-status";
      if(inwordId!=''){
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
                      data: {status:status,inwordId:inwordId},
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