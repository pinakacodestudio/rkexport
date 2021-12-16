
$(document).ready(function() {
    $('#dealertable').DataTable({
        "language": {
            "lengthMenu": "_MENU_"
        },
        "columnDefs": [ {
          "targets": [-1,-2],
          "orderable": false
        } ],
        responsive: true,
    });
    $('.dataTables_filter input').attr('placeholder','Search...');


    //DOM Manipulation to move datatable elements integrate to panel
    $('.panel-ctrls').append($('.dataTables_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls').append("<i class='separator'></i>");
    $('.panel-ctrls').append($('.dataTables_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('.panel-footer').append($(".dataTable+.row"));
    $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-lg");
});


function getcontent(id){

  var uurl = SITE_URL+"catalog/getcontentbyid";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {id:String(id)},
    async: false,
    success: function(response){
      var JSONObject = JSON.parse(response);

      $('#id').html(JSONObject['id']);
      $('#catalogname').html(JSONObject['catalogname']);
      $('#catalogdescription').html(JSONObject['catalogdescription']);
      $('#catalogimage').attr("src",JSONObject['catalogimage']); 
      $('#catalogpdffile').attr("href",JSONObject['catalogpdffile']); 
      $('#catalogcreateddate').html(JSONObject['catalogcreateddate']);

      if(JSONObject['catalogstatus']=="1")
      {
      	$('#catalogstatus').removeClass("btn-danger");
      	$('#catalogstatus').addClass("btn-success");
      	$('#catalogstatus').html("Active");
      }
      else
      {
      	$('#catalogstatus').removeClass("btn-success");
      	$('#catalogstatus').addClass("btn-danger");
      	$('#catalogstatus').html("Inactive");
      }

      
      
    },
    error: function(xhr) {
    //alert(xhr.responseText);
    },
  });
}