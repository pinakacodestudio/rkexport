$('input[name="importopeningstock"]').change(function(){
  var val = $(this).val();
  var filename = $("#importopeningstock").val().replace(/C:\\fakepath\\/i, '');
  switch(val.substring(val.lastIndexOf('.') + 1).toLowerCase()){
    case 'xl': case 'xlc': case 'xls' : case 'xlsx' : case 'ods':
      $("#Filetext").val(filename);
      $("#importopeningstock_div").removeClass("has-error is-focused");
      break;
    default:
      $("#Filetext").val("");
      $("#importopeningstock_div").addClass("has-error is-focused");
      new PNotify({title: 'Please upload valid excel file',styling: 'fontawesome',delay: '3000',type: 'error'});
      break;
  }
});


function checkimportopeningstockvalidation(){

  var filetext = $("#Filetext").val();
 
  var isvalidfiletext = 0;
  
  PNotify.removeAll();
  
  //CHECK FILE
  if(filetext==''){
    $("#importopeningstock_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select excel file !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }else{
      $("#importopeningstock_div").removeClass("has-error is-focused");
      isvalidfiletext = 1;
  }
  if(isvalidfiletext==1){
    
    var formData = new FormData($('#openingstockimportform')[0]);
    
    var uurl = SITE_URL+"import-openingstock/importopeningstock";
    
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
            new PNotify({title: "Opening stock successfully imported.",styling: 'fontawesome',delay: '3000',type: 'success'});
             setTimeout(function() { window.location.reload(); }, 1500);
        }else if(response=='2'){
          new PNotify({title: "Uploaded file is not an excel file !",styling: 'fontawesome',delay: '3000',type: 'error'});
        }else if(response=='3'){
          new PNotify({title: "Excel file not uploaded !",styling: 'fontawesome',delay: '3000',type: 'error'});
        }else if(response=='4'){
          new PNotify({title: "Some field name are not match !",styling: 'fontawesome',delay: '3000',type: 'error'});
        }else if(response=='5'){
          new PNotify({title: "Please enter at least one stock detail !",styling: 'fontawesome',delay: '3000',type: 'error'});
        }else if(response=='6'){
          new PNotify({title: "Please enter valid sheet name !",styling: 'fontawesome',delay: '3000',type: 'error'});
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


$(document).ready(function() {
  
  var oTable = $('#openingstocktable').dataTable
    ({
      "processing": true,//Feature control the processing indicator.
      "language": {
        "lengthMenu": "_MENU_"
      },
      "pageLength": 50,
      "scrollCollapse": true,
      "scrollY": "500px",
      "columnDefs": [{
        'orderable': false,
        'targets': [0,4,-1,-2]
      }],
      "order": [], //Initial no order.
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": SITE_URL+"import-openingstock/listing",
        "type": "POST",
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
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
  $('.panel-ctrls').append($('.dataTables_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
  $('.panel-ctrls').append("<i class='separator'></i>");
  $('.panel-ctrls').append($('.dataTables_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

  $('.panel-footer').append($(".dataTable+.row"));
  $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-md");
  

  

  


  
});