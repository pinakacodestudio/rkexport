var isvalidfiletext = 0;
$(document).ready(function() {
    $('#couriercompanytable').DataTable({
        "language": {
            "lengthMenu": "_MENU_"
        },
        "columnDefs": [ {
          "targets": [-1,-2,-3],
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
    $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-md");
});
$('input[name="attachment"]').change(function(){
  var val = $(this).val();
  var filename = $("#attachment").val().replace(/C:\\fakepath\\/i, '');
  
  switch(val.substring(val.lastIndexOf('.') + 1).toLowerCase()){
    case 'xl': case 'xlc': case 'xls' : case 'xlsx' : case 'ods':
      $("#Filetext").val(filename);
      isvalidfiletext = 1;
      $("#attachment_div").removeClass("has-error is-focused");
      break;
    default:
      $("#Filetext").val("");
      isvalidfiletext = 0;
      $("#attachment_div").addClass("has-error is-focused");
      new PNotify({title: 'Please upload valid excel file !',styling: 'fontawesome',delay: '3000',type: 'error'});
      break;
  }
});
function importdeliverylocationmodel(){
    $('#myDetailModal').modal('show');
    $('#courierid').val('0');
    $("#Filetext").val("");
    $('#courierid').selectpicker('refresh');
    $("#attachment_div").removeClass("has-error is-focused");
    $("#courier_div").removeClass("has-error is-focused");
    PNotify.removeAll();
}
function checkvalidation(type){

  var filetext = $("#Filetext").val();
  var courierid = $('#courierid').val();

  var isvalidcourierid  = 0;

  PNotify.removeAll();
  if(courierid == 0){
    $("#courier_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select courier !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidcourierid = 0;
  }else {
    $("#courier_div").removeClass("has-error is-focused");
    isvalidcourierid = 1;
  }
  //CHECK FILE
  if(isvalidfiletext==0){
    $("#attachment_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select excel file !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidfiletext = 0;
  }
  
    if(isvalidcourierid==1 && isvalidfiletext==1){
    
      var formData = new FormData($('#deliverylocationimportform')[0]);
  
      var uurl = SITE_URL+"Couriercompany/importdeliverylocation";
      
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
              new PNotify({title: "Delivery location successfully imported.",styling: 'fontawesome',delay: '3000',type: 'success'});
               setTimeout(function() { window.location.reload(); }, 1500);
          }else if(response=='2'){
            new PNotify({title: "Uploaded file is not an excel file !",styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(response=='3'){
            new PNotify({title: "Excel file not uploaded !",styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(response=='4'){
            new PNotify({title: "Some field name are not match !",styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
            new PNotify({title: response,styling: 'fontawesome',delay: '3000',type: 'error'});
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
}