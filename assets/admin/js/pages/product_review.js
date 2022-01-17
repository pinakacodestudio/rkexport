
$(document).ready(function() {
    
  oTable = $('#productreviewtable').dataTable
    ({
      "language": {
        "lengthMenu": "_MENU_"
      },
      "pageLength": 10,
        "columnDefs": [{
          'orderable': false,
          'targets': [-1,-2,-7,-8]
        }],
      "order": [], //Initial no order.
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": SITE_URL+"product-review/listing",
        "type": "POST",
        "data": function ( data ) {    
          data.startdate = $('#startdate').val();
          data.enddate = $('#enddate').val();    
          data.productid = $('#productid').val();
          data.type = $('#type').val();
          data.memberid = $('#memberid').val();
          
        
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
          loadpopover();
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
        return false;
    });
});
});

function displayproductreview(id){
    var message = $('#message'+id).html();
    $('#myModal .modal-body').html(message.replace(/&nbsp;/g, ' '));

}
$('#productid').on('change', function (e) {
  oTable.ajax.reload(null,false);
});

function chagereviewstatus(type, reviewId){
  var uurl = SITE_URL+"product-review/changereviewtype";
  if(reviewId!=''){
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
              data: {type:type,reviewId:reviewId},
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
function applyFilter(){
  oTable.fnDraw();
}

function updateratingstatus(){
  var formData = new FormData($('#memberform')[0]);
      var uurl = SITE_URL+"product-review/updateratingstatus";
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
                new PNotify({title: "Product review rating status successfully updated.",styling: 'fontawesome',delay: '3000',type: 'success'});
                setTimeout(function() { window.location=SITE_URL+"product-review"; }, 1500);
            }else{
                new PNotify({title: "Product review rating status not updated !",styling: 'fontawesome',delay: '3000',type: 'error'});
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