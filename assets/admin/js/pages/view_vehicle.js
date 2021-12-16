$(document).ready(function(){

  var url = window.location.href;
  if (url.indexOf('#') != -1) {
    var activeTab = url.substring(url.indexOf("#") + 1);
    $("#" + activeTab).addClass("active in");
    $('a[href="#'+ activeTab +'"]').tab('show')
  }

    $('#fromdate,#duedate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayBtn: "linked",
        clearBtn: true,
        orientation: "top left"
    });

    $('.daterangepicker-filter').datepicker({
      todayHighlight: true,
      format: 'dd/mm/yyyy',
      autoclose: true,
      todayBtn: "linked",
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
        "url": SITE_URL+"vehicle/vehicledocumentlisting",
        "type": "POST",
        "data" :function ( data ) {
            data.vehicleid = vehicleid;
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

    fueltable = $('#fueltable').DataTable({
        "processing": true,//Feature control the processing indicator.
        "language": {
            "lengthMenu": "_MENU_"
        },
        "columnDefs": [ {
          "targets": [-1,-2],
          "orderable": false
        },{targets:[-5,-4], className: "text-right"}],
        responsive: true,
        'serverSide': true,//Feature control DataTables' server-side processing mode.
        "ajax": {
          "url": SITE_URL+"vehicle/vehiclefuellisting",
          "type": "POST",
          "data" :function ( data ) {
              data.vehicleid = vehicleid;
              data.partyid = $("#fuelpartyid").val();
              data.fromdate = $("#fuelstartdate").val();
              data.todate = $("#fuelenddate").val();
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
      
    $('#fueltable_filter input').attr('placeholder','Search...');
    //DOM Manipulation to move datatable elements integrate to panel
    //DOM Manipulation to move datatable elements integrate to panel
    $('.panel-ctrls.fuel-tbl').append($('#fueltable_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls.fuel-tbl').append("<i class='separator'></i>");
    $('.panel-ctrls.fuel-tbl').append($('#fueltable_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('.panel-footer.fuel-tbl').append($(".dataTable+.row"));
    $('#fueltable_paginate>ul.pagination').addClass("pull-right pagination-md");

    servicetable = $('#servicetable').DataTable({
        "processing": true,//Feature control the processing indicator.
        "language": {
            "lengthMenu": "_MENU_"
        },
        "columnDefs": [ {
          "targets": [-1,-2],
          "orderable": false
        },{targets:[-4], className: "text-right"}],
        responsive: true,
        'serverSide': true,//Feature control DataTables' server-side processing mode.
        "ajax": {
          "url": SITE_URL+"vehicle/vehicleservicelisting",
          "type": "POST",
          "data" :function ( data ) {
              data.vehicleid = vehicleid;
              data.garageid = $("#servicegarageid").val();
              data.driverid = $("#servicedriverid").val();
              data.servicetypeid = $("#servicetypeid").val();
              data.fromdate = $("#servicestartdate").val();
              data.todate = $("#serviceenddate").val();
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
      
    $('#servicetable_filter input').attr('placeholder','Search...');
    //DOM Manipulation to move datatable elements integrate to panel
    //DOM Manipulation to move datatable elements integrate to panel
    $('.panel-ctrls.service-tbl').append($('#servicetable_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls.service-tbl').append("<i class='separator'></i>");
    $('.panel-ctrls.service-tbl').append($('#servicetable_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('.panel-footer.service-tbl').append($(".dataTable+.row"));
    $('#servicetable_paginate>ul.pagination').addClass("pull-right pagination-md");

    challantable = $('#challantable').DataTable({
        "processing": true,//Feature control the processing indicator.
        "language": {
            "lengthMenu": "_MENU_"
        },
        "columnDefs": [ {
          "targets": [-1,-2],
          "orderable": false
        },{targets:[-4], className: "text-right"}],
        responsive: true,
        'serverSide': true,//Feature control DataTables' server-side processing mode.
        "ajax": {
          "url": SITE_URL+"vehicle/vehiclechallanlisting",
          "type": "POST",
          "data" :function ( data ) {
              data.vehicleid = vehicleid;
              data.driverid = $("#challandriverid").val();
              data.challantypeid = $("#challantypeid").val();
              data.fromdate = $("#challanstartdate").val();
              data.todate = $("#challanenddate").val();
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
      
    $('#challantable_filter input').attr('placeholder','Search...');
    //DOM Manipulation to move datatable elements integrate to panel
    
    $('.panel-ctrls.challan-tbl').append($('#challantable_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls.challan-tbl').append("<i class='separator'></i>");
    $('.panel-ctrls.challan-tbl').append($('#challantable_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('.panel-footer.challan-tbl').append($(".dataTable+.row"));
    $('#challantable_paginate>ul.pagination').addClass("pull-right pagination-md");

    insurancetable = $('#insurancetable').DataTable({
        "processing": true,//Feature control the processing indicator.
        "language": {
            "lengthMenu": "_MENU_"
        },
        "columnDefs": [ {
          "targets": [-1,-2],
          "orderable": false
        },{targets:[-4], className: "text-right"}],
        responsive: true,
        'serverSide': true,//Feature control DataTables' server-side processing mode.
        "ajax": {
          "url": SITE_URL+"vehicle/vehicleinsurancelisting",
          "type": "POST",
          "data" :function ( data ) {
              data.vehicleid = vehicleid;
              data.insurancecompany = $("#insurancecompany").val();
              data.fromdate = $("#insurancestartdate").val();
              data.todate = $("#insuranceenddate").val();
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
      
    $('#insurancetable_filter input').attr('placeholder','Search...');
    //DOM Manipulation to move datatable elements integrate to panel
    
    $('.panel-ctrls.insurance-tbl').append($('#insurancetable_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls.insurance-tbl').append("<i class='separator'></i>");
    $('.panel-ctrls.insurance-tbl').append($('#insurancetable_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('.panel-footer.insurance-tbl').append($(".dataTable+.row"));
    $('#insurancetable_paginate>ul.pagination').addClass("pull-right pagination-md");

    insuranceclaimtable = $('#insuranceclaimtable').DataTable({
      "processing": true,//Feature control the processing indicator.
      "language": {
          "lengthMenu": "_MENU_"
      },
      "columnDefs": [ {
        "targets": [-1,-2],
        "orderable": false
      },{targets:[-3], className: "text-right"}],
      responsive: true,
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      "ajax": {
        "url": SITE_URL+"vehicle/vehicleinsuranceclaimlisting",
        "type": "POST",
        "data" :function ( data ) {
            data.vehicleid = vehicleid;
            data.insurancecompany = $("#insurancecompany").val();
            data.fromdate = $("#insuranceclaimstartdate").val();
            data.todate = $("#insuranceclaimenddate").val();
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
      
    $('#insuranceclaimtable_filter input').attr('placeholder','Search...');
    //DOM Manipulation to move datatable elements integrate to panel
    
    $('.panel-ctrls.insuranceclaim-tbl').append($('#insuranceclaimtable_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls.insuranceclaim-tbl').append("<i class='separator'></i>");
    $('.panel-ctrls.insuranceclaim-tbl').append($('#insuranceclaimtable_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('.panel-footer.insuranceclaim-tbl').append($(".dataTable+.row"));
    $('#insuranceclaimtable_paginate>ul.pagination').addClass("pull-right pagination-md");

    assignedsitetable = $('#assignedsitetable').DataTable({
      "processing": true,//Feature control the processing indicator.
      "language": {
          "lengthMenu": "_MENU_"
      },
      "columnDefs": [ {
        "targets": [],
        "orderable": false
      },{targets:[-3], className: "text-right"}],
      responsive: true,
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      "ajax": {
        "url": SITE_URL+"vehicle/vehicleassignedsitelisting",
        "type": "POST",
        "data" :function ( data ) {
            data.vehicleid = vehicleid;
            data.cityid = $("#assignedsitecityid").val();
            data.fromdate = $("#assignedsitestartdate").val();
            data.todate = $("#assignedsiteenddate").val();
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
    
    $('.panel-ctrls.assignedsite-tbl').append($('#assignedsitetable_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls.assignedsite-tbl').append("<i class='separator'></i>");
    $('.panel-ctrls.assignedsite-tbl').append($('#assignedsitetable_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('.panel-footer.assignedsite-tbl').append($(".dataTable+.row"));
    $('#assignedsitetable_paginate>ul.pagination').addClass("pull-right pagination-md");

    assignedpartytable = $('#assignedpartytable').DataTable({
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
        "url": SITE_URL+"vehicle/vehicleassignedpartylisting",
        "type": "POST",
        "data" :function ( data ) {
            data.vehicleid = vehicleid;
            data.cityid = $("#assignedpartycityid").val();
            data.fromdate = $("#assignedpartystartdate").val();
            data.todate = $("#assignedpartyenddate").val();
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
      
    $('#assignedpartytable_filter input').attr('placeholder','Search...');
    //DOM Manipulation to move datatable elements integrate to panel
    
    $('.panel-ctrls.assignedparty-tbl').append($('#assignedpartytable_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls.assignedparty-tbl').append("<i class='separator'></i>");
    $('.panel-ctrls.assignedparty-tbl').append($('#assignedpartytable_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('.panel-footer.assignedparty-tbl').append($(".dataTable+.row"));
    $('#assignedpartytable_paginate>ul.pagination').addClass("pull-right pagination-md");

      $('#emiremindertable').DataTable({
        "language": {
            "lengthMenu": "_MENU_"
        },
        "columnDefs": [ {
          "targets": [1],
          "orderable": false
        },{targets:[2,3], className: "text-right"}],
        responsive: true,
    });

    $('#emiremindertable_filter input').attr('placeholder','Search...');
    //DOM Manipulation to move datatable elements integrate to panel
    
    $('.panel-ctrls.emireminder-tbl').append($('#emiremindertable_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls.emireminder-tbl').append("<i class='separator'></i>");
    $('.panel-ctrls.emireminder-tbl').append($('#emiremindertable_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('.panel-footer.emireminder-tbl').append($(".dataTable+.row"));
    $('#emireminderable_paginate>ul.pagination').addClass("pull-right pagination-md");
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

function applyFilter(table){
    if(table=="fuel"){
      fueltable.ajax.reload(null, false);
    }else if(table=="service"){
      servicetable.ajax.reload(null, false);
    }else if(table=="challan"){
      challantable.ajax.reload(null, false);
    }else if(table=="insurance"){
      insurancetable.ajax.reload(null, false);
    }else if(table=="insuranceclaim"){
      insuranceclaimtable.ajax.reload(null, false);
    }else if(table=="assignedsite"){
      assignedsitetable.ajax.reload(null, false);
    }else if(table=="assignedparty"){
      assignedpartytable.ajax.reload(null, false);
    }
}
