$(document).ready(function() {
    table = $('#zone').dataTable({
          "processing": true,//Feature control the processing indicator.
          "language": {
              "lengthMenu": "_MENU_"
          },
          "columnDefs": [ {
            "targets": [0,-1,-2],
            "orderable": false
          } ],
          responsive: true,
          'serverSide': true,//Feature control DataTables' server-side processing mode.
          "ajax": {
            "url": SITE_URL+"zone/listing",
            "type": "POST",
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
      $('.panel-ctrls').append($('.dataTables_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
      $('.panel-ctrls').append("<i class='separator'></i>");
      $('.panel-ctrls').append($('.dataTables_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");
  
      $('.panel-footer').append($(".dataTable+.row"));
      $('.dataTables_paginate>ul.pagination').addClass("pull-right");
  });
  
  
  
  
  
  