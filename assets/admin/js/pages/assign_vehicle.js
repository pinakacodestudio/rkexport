$(document).ready(function() {
    oTable = $('#assignvehicletable').dataTable
      ({
        "language": {
          "lengthMenu": "_MENU_"
        },
        
        "pageLength": 10,
        "columnDefs": [{
          'orderable': false,
          'targets': [-1,-2]
        }],
        "order": [], //Initial no order.
      });
    $('.dataTables_filter input').attr('placeholder','Search...');

    //DOM Manipulation to move datatable elements integrate to panel
    $('.panel-ctrls.panel-tbl').append($('.dataTables_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls.panel-tbl').append("<i class='separator'></i>");
    $('.panel-ctrls.panel-tbl').append($('.dataTables_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('.panel-footer').append($(".dataTable+.row"));
    $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-md");

    $('#assignvehicledate').datepicker({
      todayHighlight: true,
      format: 'dd/mm/yyyy',
      autoclose: true,
      todayBtn: "linked",
      clearBtn: true
  });
});

function exportToExcelAssignVehicle(){

  var totalRecords =$("#assignvehicletable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"assign-vehicle/exportToExcelAssignVehicle";
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
}

function exportToPDFAssignVehicle(){

 
  var totalRecords =$("#assignvehicletable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){ 
      window.location= SITE_URL+"assign-vehicle/exportToPDFAssignVehicle";
  }else{
      new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
}

function printAssignVehicle(){
  
  var totalRecords =$("#assignvehicletable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
      var uurl = SITE_URL + "assign-vehicle/printAssignVehicle";
      $.ajax({
          url: uurl,
          type: 'POST',
          data: {},
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
              // alert(xhr.responseText);
          },
          complete: function() {
              $('.mask').hide();
              $('#loader').hide();
          },
      });
  }
  else{
      new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
}

function checkvalidation(){

  var vehicleid = $("#vehicleid").val();
  var siteid = $("#siteid").val();
  var date = $("#assignvehicledate").val();

  var isvalidsiteid = isvaliddate = 0;
  PNotify.removeAll();

  if (siteid == 0) {
      $("#site_div").addClass("has-error is-focused");
      new PNotify({title: 'Please select site !',styling: 'fontawesome',delay: '3000',type: 'error'});
  } else {
      $("#site_div").removeClass("has-error is-focused");
      isvalidsiteid = 1;
  }

  if (date == '') {
      $("#date_div").addClass("has-error is-focused");
      new PNotify({title: 'Please select date !',styling: 'fontawesome',delay: '3000',type: 'error'});
  } else {
      $("#date_div").removeClass("has-error is-focused");
      isvaliddate = 1;
  }
  if (isvalidsiteid == 1 && isvaliddate == 1) {
    
    var formData = new FormData($('#assign-vehicle-form')[0]);
          var uurl = SITE_URL + "assign-vehicle/assign-vehicle-add";
          $.ajax({
              url: uurl,
              type: 'POST',
              data: formData,
              //async: false,
              beforeSend: function () {
                  $('.mask').show();
                  $('#loader').show();
              },
              success: function (response) {
                  if (response == 1) {
                      new PNotify({title: "Assign vehicle successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
                          setTimeout(function () {window.location = SITE_URL + "assign-vehicle";}, 1500);
                  } else {
                      new PNotify({title: 'Assign vehicle not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
                  }
              },
              error: function (xhr) {
                  //alert(xhr.responseText);
              },
              complete: function () {
                  $('.mask').hide();
                  $('#loader').hide();
              },
              cache: false,
              contentType: false,
              processData: false
          });
  
  }
}