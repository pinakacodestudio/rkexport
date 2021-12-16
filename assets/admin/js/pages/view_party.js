$(document).ready(function(){

    $('#fromdate,#duedate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayBtn: "linked",
        clearBtn: true,
        orientation: "top left"
    });

    $('#assignedsitedetails #datepicker-range').datepicker({
      todayHighlight: true,
      format: 'dd/mm/yyyy',
      autoclose: true,
      todayBtn: "linked",
    }).on('changeDate', function(e) {
      assignedsitetable.ajax.reload(null, false);
    });
    $("#sitecityid").on("change", function(){
      assignedsitetable.ajax.reload(null, false);
    });

    $('#assignedvehicledetails #datepicker-range').datepicker({
      todayHighlight: true,
      format: 'dd/mm/yyyy',
      autoclose: true,
      todayBtn: "linked",
    }).on('changeDate', function(e) {
      assignedvehicletable.ajax.reload(null, false);
    });

    documenttable = $('#documenttable').DataTable({
      "processing": true,//Feature control the processing indicator.
      "language": {
          "lengthMenu": "_MENU_"
      },
      "columnDefs": [ {
        "targets": [0,-1,-2],
        "orderable": false
      }],
      responsive: true,
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      "ajax": {
        "url": SITE_URL+"party/documentlisting",
        "type": "POST",
        "data" :function ( data ) {
            data.partyid = partyid;
        },
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
        },
        complete: function(){
          $('.mask').hide();
          $('#loader').hide();
        },
      }
    });
    
    $('#documenttable_filter input').attr('placeholder','Search...');
     //DOM Manipulation to move datatable elements integrate to panel
     //DOM Manipulation to move datatable elements integrate to panel
    $('.panel-ctrls.document-tbl').append($('#documenttable_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls.document-tbl').append("<i class='separator'></i>");
    $('.panel-ctrls.document-tbl').append($('#documenttable_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('.panel-footer.document-tbl').append($(".dataTable+.row"));
    $('#documenttable_paginate>ul.pagination').addClass("pull-right pagination-md");


    assignedsitetable = $('#assignedsitetable').DataTable({
      "processing": true,//Feature control the processing indicator.
      "language": {
          "lengthMenu": "_MENU_"
      },
      "columnDefs": [ {
        "targets": [1],
        "orderable": false
      }],
      responsive: true,
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      "ajax": {
        "url": SITE_URL+"party/assignedsitelisting",
        "type": "POST",
        "data" :function ( data ) {
          data.partyid = partyid;
          data.cityid = $("#sitecityid").val();
          data.fromdate = $("#sitestartdate").val();
          data.todate = $("#siteenddate").val();
        },
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
        },
        complete: function(){
          $('.mask').hide();
          $('#loader').hide();
        },
      }
    });
    
    $('#assignedsitetable_filter input').attr('placeholder','Search...');
     //DOM Manipulation to move datatable elements integrate to panel
     //DOM Manipulation to move datatable elements integrate to panel
    $('.panel-ctrls.assignedsite-tbl').append($('#assignedsitetable_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls.assignedsite-tbl').append("<i class='separator'></i>");
    $('.panel-ctrls.assignedsite-tbl').append($('#assignedsitetable_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('.panel-footer.assignedsite-tbl').append($(".dataTable+.row"));
    $('#assignedsitetable_paginate>ul.pagination').addClass("pull-right pagination-md");
   

    assignedvehicletable = $('#assignedvehicletable').DataTable({
      "processing": true,//Feature control the processing indicator.
      "language": {
          "lengthMenu": "_MENU_"
      },
      "columnDefs": [ {
        "targets": [],
        "orderable": false
      }],
      responsive: true,
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      "ajax": {
        "url": SITE_URL+"party/assignedvehiclelisting",
        "type": "POST",
        "data" :function ( data ) {
          data.partyid = partyid;
          data.fromdate = $("#vehiclestartdate").val();
          data.todate = $("#vehicleenddate").val();
        },
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
        },
        complete: function(){
          $('.mask').hide();
          $('#loader').hide();
        },
      }
    });
    
    $('#assignedvehicletable_filter input').attr('placeholder','Search...');
     //DOM Manipulation to move datatable elements integrate to panel
     //DOM Manipulation to move datatable elements integrate to panel
    $('.panel-ctrls.assignedvehicle-tbl').append($('#assignedvehicletable_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls.assignedvehicle-tbl').append("<i class='separator'></i>");
    $('.panel-ctrls.assignedvehicle-tbl').append($('#assignedvehicletable_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('.panel-footer.assignedvehicle-tbl').append($(".dataTable+.row"));
    $('#assignedvehicletable_paginate>ul.pagination').addClass("pull-right pagination-md");
   
});
function checkdocumentvalidation(addtype="0"){

    var documenttype = $("#documenttype").val();
    var documentnumber = $("#documentnumber").val();
    var documentid = $("#documentid").val();
    var regdate = $("#fromdate").val();
    var duedate = $("#duedate").val();
    
    var isvaliddocumenttype = isvaliddocumentnumber = isvalidduedate = 0;

    PNotify.removeAll();
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
    
    if(isvaliddocumenttype == 1 && isvaliddocumentnumber == 1 && isvalidduedate == 1){
        
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
                    documenttable.ajax.reload(null, false);
                  }
                }else if(response==2){
                  new PNotify({title: "Document already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(response==3){
                  new PNotify({title: "Document file type is not valid !",styling: 'fontawesome',delay: '3000',type: 'error'});
                }else if(response==4){
                  new PNotify({title: "Document file not upload !",styling: 'fontawesome',delay: '3000',type: 'error'});
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
                    documenttable.ajax.reload(null, false);
                  }else if(response==2){
                    new PNotify({title: "Document already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
                  }else if(response==3){
                    new PNotify({title: "Document file type is not valid !",styling: 'fontawesome',delay: '3000',type: 'error'});
                  }else if(response==4){
                    new PNotify({title: "Document file not upload !",styling: 'fontawesome',delay: '3000',type: 'error'});
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