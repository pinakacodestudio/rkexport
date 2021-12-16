$(document).ready(function () {
    $('#fromdate,#duedate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayBtn: "linked",
        clearBtn: true
    });

    oTable = $('#documenttable').DataTable({
        "language": {
            "lengthMenu": "_MENU_"
        },
        "pageLength": 10,
        "columnDefs": [{
            'orderable': false,
            'targets': [0, -1, -2]
        }],
        "order": [], //Initial no order.
        'serverSide': true,//Feature control DataTables' server-side processing mode.
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": SITE_URL+'document/listing',
            "type": "POST",
            "data": function ( data ) {
              data.type = $('#type').val();
              data.documenttypeid = $('#documenttypeid').val();
              data.newpartyid = $('#newpartyid').val();
              data.newvehicleid = $('#newvehicleid').val();
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
    $('.dataTables_filter input').attr('placeholder', 'Search...');

    //DOM Manipulation to move datatable elements integrate to panel
    $('.panel-ctrls.panel-tbl').append($('.dataTables_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls.panel-tbl').append("<i class='separator'></i>");
    $('.panel-ctrls.panel-tbl').append($('.dataTables_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('.panel-footer').append($(".dataTable+.row"));
    $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-md");

    $(function () {
        $('.panel-heading.filter-panel').click(function () {
            $(this).find('.button-icon span').html($(this).find('.button-icon span').text() == 'keyboard_arrow_down' ? 'keyboard_arrow_up' : 'keyboard_arrow_down');
            //$(this).children().toggleClass(" ");
            $(this).next().slideToggle({
                duration: 200
            });
            $(this).toggleClass('panel-collapsed');
            return false;
        });
    });
});

function applyFilter() {
    oTable.ajax.reload(null, false);
}

function checkdocumentvalidation(addtype="0"){

    var documenttype = $("#documenttype").val();
    var newvehicleid = $("#newvehicleid").val();
    var newpartyid = $("#newpartyid").val();
    var documentnumber = $("#documentnumber").val();
    var documentid = $("#documentid").val();
    var regdate = $("#fromdate").val();
    var duedate = $("#duedate").val();
    
    var isvaliddocumenttype = isvaliddocumentnumber = isvalidduedate = 0;
    var isvalidreferencetype = 1;

    PNotify.removeAll();
    if($("#newvehicleid_div").is(":visible")){
      if(newvehicleid == 0){
        $("#newvehicleid_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select vehicle !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidreferencetype = 0;
      }else {
          $("#newvehicleid_div").removeClass("has-error is-focused");
      }
    }
    if($("#newpartyid_div").is(":visible")){
      if(newpartyid == 0){
        $("#newpartyid_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select party !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidreferencetype = 0;
      }else {
          $("#newpartyid_div").removeClass("has-error is-focused");
      }
    }
    if(documenttype == 0){
        $("#documenttype_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select document type !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }else {
        $("#documenttype_div").removeClass("has-error is-focused");
        isvaliddocumenttype = 1;
    }
    if(documentnumber == 0){
        $("#documentnumber_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter document number !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }else {
        $("#documentnumber_div").removeClass("has-error is-focused");
        isvaliddocumentnumber = 1;
    }
    
    if(duedate != "" && regdate != ""){
      var RegDate = regdate.split("/");
      RegDate = new Date(RegDate[2], RegDate[1]-1, RegDate[0]);
      var rdd = String(RegDate.getDate()).padStart(2, '0');
      var rmm = String(RegDate.getMonth() + 1).padStart(2, '0'); //January is 0!
      var ryyyy = RegDate.getFullYear();
      RegDate = ryyyy+"-"+rmm+"-"+rdd;
     
      var DueDate = duedate.split("/");
      DueDate = new Date(DueDate[2], DueDate[1]-1, DueDate[0]);
      var dd = String(DueDate.getDate()).padStart(2, '0');
      var mm = String(DueDate.getMonth() + 1).padStart(2, '0'); //January is 0!
      var yyyy = DueDate.getFullYear();
      DueDate = yyyy+"-"+mm+"-"+dd;
      
      if(DueDate < RegDate){
        $("#duedate_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select due date greater than of register date !',styling: 'fontawesome',delay: '3000',type: 'error'});
      }else {
        isvalidduedate = 1;
        $("#duedate_div").removeClass("has-error is-focused");
      }
    }else{
      isvalidduedate = 1;
      $("#duedate_div").removeClass("has-error is-focused");
    }
    
    if(isvaliddocumenttype == 1 && isvaliddocumentnumber == 1 && isvalidduedate == 1 && isvalidreferencetype ==1){
        
        var formData = new FormData($('#document-form')[0]);
        
        if(documentid=="" || documentid==0){
            var uurl = SITE_URL+"document/document-add";
      
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
                  new PNotify({title: "Document successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
                  if(addtype==1){
                    resetdocumentdata();
                  }else{
                    $("#documentModal").modal("hide");
                    oTable.ajax.reload(null, false);
                  }
                }else if(response==2){
                  new PNotify({title: "Document already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(response==3){
                  new PNotify({title: "Document file type is not valid !",styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(response==4){
                  new PNotify({title: "Document file not upload !",styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(response==-1){
                  new PNotify({title: 'File is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE)+') !',styling: 'fontawesome',delay: '3000',type: 'error'});
                }else{
                  new PNotify({title: 'Document not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
        }else{
            var uurl = SITE_URL+"document/update-document";
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
                    new PNotify({title: "Document successfully updated.",styling: 'fontawesome',delay: '3000',type: 'success'});
                    $("#documentModal").modal("hide");
                    oTable.ajax.reload(null, false);
                  }else if(response==2){
                    new PNotify({title: "Document already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
                  }else if(response==3){
                    new PNotify({title: "Document file type is not valid !",styling: 'fontawesome',delay: '3000',type: 'error'});
                  }else if(response==4){
                    new PNotify({title: "Document file not upload !",styling: 'fontawesome',delay: '3000',type: 'error'});
                  }else if(response==-1){
                    new PNotify({title: 'File is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE)+') !',styling: 'fontawesome',delay: '3000',type: 'error'});
                  }else{
                    new PNotify({title: 'Document not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
}

function exportToExcelDocument(){

  var type = $('#type').val();
  var documenttypeid = $('#documenttypeid').val();
  var newpartyid = $('#newpartyid').val();
  var newvehicleid = $('#newvehicleid').val();

  var totalRecords =$("#documenttable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"document/exportToExcelDocument?type="+type+"&documenttypeid="+documenttypeid+"&newpartyid="+newpartyid+"&newvehicleid="+newvehicleid;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
}

function exportToPDFDocument(){

  var type = $('#type').val();
  var documenttypeid = $('#documenttypeid').val();
  var newpartyid = $('#newpartyid').val();
  var newvehicleid = $('#newvehicleid').val();

  var totalRecords =$("#documenttable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){ 
      window.location= SITE_URL+"document/exportToPDFDocument?type="+type+"&documenttypeid="+documenttypeid+"&newpartyid="+newpartyid+"&newvehicleid="+newvehicleid;
  }else{
      new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
}

function printDocumentDetails(){
  var type = $('#type').val();
  var documenttypeid = $('#documenttypeid').val();
  var newpartyid = $('#newpartyid').val();
  var newvehicleid = $('#newvehicleid').val();

  var totalRecords =$("#documenttable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
      var uurl = SITE_URL + "document/printDocumentDetails";
      $.ajax({
          url: uurl,
          type: 'POST',
          data: {
            type:type,
            documenttypeid:documenttypeid,
            newpartyid:newpartyid,
            newvehicleid:newvehicleid
          },
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