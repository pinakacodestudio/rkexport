
$(document).ready(function() {

      oTable = $('#daily_followup').DataTable
     ({
            "language": {
                "lengthMenu": "_MENU_"
            },
            "columnDefs": [ {
              "targets": [-1,-2,-3,-4],
              "orderable": false
            } ],
            responsive: true,
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
      $('#datepicker-range1').datepicker({
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

  function openservicepopup(){

    $("#serviceModal .modal-title").text("Add Followup");
 
    
    $('#status').selectpicker('refresh');
    $("#serviceModal").modal("show");
}
function openeditservicepopup(){

  $("#editserviceModal .modal-title").text("Edit Followup");
  $("#editserviceModal").modal("show");
}
$(document).ready(function() {

  $('#date').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        orientation: 'top',
        autoclose: true,
        todayBtn: "linked"
    });
    $('#datepicker').datepicker({
      todayHighlight: true,
      format: 'dd/mm/yyyy',
      orientation: 'top',
      autoclose: true,
      todayBtn: "linked"
  });
  
  });