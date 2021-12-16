
$(document).ready(function() {
    loadpopover();
    oTable = $('#grntable').DataTable
    ({
      "language": {
        "lengthMenu": "_MENU_"
      },
      "pageLength": 10,
      "columnDefs": [{
        'orderable': false,
        'targets': [-1,-2]
      },{ targets: [6], className: "text-right" }],
      drawCallback: function () {
        loadpopover();
      },
      "order": [], //Initial no order.
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": SITE_URL+"goods-received-notes/listing",
        "type": "POST",
        "data": function ( data ) {
          data.vendorid = $('#vendorid').val();
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
      todayHighlight: true,
      format: 'dd/mm/yyyy',
      autoclose: true,
      todayBtn:"linked"
    });
});
function applyFilter(){
  oTable.ajax.reload();
}

function printGoodsReceivedNotes(id){

  var uurl = SITE_URL + "goods-received-notes/printGoodsReceivedNotes";
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
}

function changeGRNstatus(status, GRNId){
  var uurl = SITE_URL+"goods-received-notes/update-status";
  if(GRNId!=''){
    swal({    title: "Are you sure to change status?",
      type: "warning",   
      showCancelButton: true,   
      confirmButtonColor: "#DD6B55",   
      confirmButtonText: "Yes, change it!",   
      closeOnConfirm: false }, 
    function(isConfirm){   
      if (isConfirm) {  
        if(status==2){
            
          $('#rejectGRNModal').modal('show');
          $('#rejectionGRNId').val(GRNId);
          $('#rejectionstatus').val(status);
          $('#resonforrejection').val('');
        }else{ 
          $.ajax({
            url: uurl,
            type: 'POST',
            data: {status:status,GRNId:GRNId},
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

function checkvalidationforrejectiongoodsreceivednotes(){

  var resonforcancellation = $('#resonforrejection').val();
  var GRNId = $('#rejectionGRNId').val();
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
    var uurl = SITE_URL+"goods-received-notes/update-status";
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {status:status,GRNId:GRNId,resonforcancellation:resonforcancellation},
      success: function(response){
          if(response==1){
            location.reload();
          }else{
            new PNotify({title: response,styling: 'fontawesome',delay: '3000',type: 'error'});
          }
        },
      error: function(xhr) {
      //alert(xhr.responseText);
      }
    }); 
  }

}