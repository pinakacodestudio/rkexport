$(document).ready(function(){

    $('#machineid').on('change', function (e) {
        $("#machineid").val(this.value);
        getmachinedetails(this.value,"forceupdate");
    });

    $('#servicedate').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        todayBtn:"linked",
        clearBtn: true,
        endDate: new Date()
    }).on('changeDate', function(ev){
        if(ev.date != undefined){
            $("#servicedue").prop("disabled", false);
        }else{
            $("#servicedue").prop("disabled", true).val("");
        }
    });
    $('#servicedue').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        todayBtn:"linked",
        clearBtn: true,
        endDate: new Date()
    });

    if(MACHINEID!=0){
        getmachinedetails(MACHINEID,"load");

    }
    oTable = $('#servicestable').DataTable({
        "language": {
            "lengthMenu": "_MENU_"
        },
        "pageLength": 10,
        "columnDefs": [{
            'orderable': false,
            'targets': [0,-1]
        }],
        "order": [], //Initial no order.
        'serverSide': true,//Feature control DataTables' server-side processing mode.
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": SITE_URL+"machine/machine-services-listing",
            "type": "POST",
            "data": function ( data ) {
                data.machineid = $("#machineid").val();
            },
            beforeSend: function(){
                $('.mask').show();
                $('#loader').show();
            },
            error: function(xhr) {
            //alert(xhr.responseText);
            },
            complete: function(e){
                $('.mask').hide();
                $('#loader').hide();
            },
        },
    });

    $('#servicestable_filter input').attr('placeholder','Search...');

    //DOM Manipulation to move datatable elements integrate to panel
    $('#servicedetailstab .panel-ctrls.panel-tbl').append($('#servicestable_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('#servicedetailstab .panel-ctrls.panel-tbl').append("<i class='separator'></i>");
    $('#servicedetailstab .panel-ctrls.panel-tbl').append($('#servicestable_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('#servicedetailstab .panel-footer').append($(".dataTable+.row"));
    $('#servicestable_paginate>ul.pagination').addClass("pull-right pagination-md");
});

function changestatus(status, serviceId){
     
    var machineid = $("#machineid").val();
    var uurl = SITE_URL+"machine/changeservicestatus";
    if(serviceId!=''){
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
                data: {status:status,serviceId:serviceId,machineid:machineid},
                beforeSend: function(){
                  $('.mask').show();
                  $('#loader').show();
                  },
                success: function(response){
                  if(response==1){
                    //  location.reload();
                    swal.close();
                    var pending = '<li id="dropdown-menu"><a onclick="changestatus(0,'+serviceId+')">Pending</a></li>';
                    var onhold = '<li id="dropdown-menu"><a onclick="changestatus(1,'+serviceId+')">On hold</a></li>';
                    var done = '<li id="dropdown-menu"><a onclick="changestatus(2,'+serviceId+')">Done</a></li>';
                    var cancel = '<li id="dropdown-menu"><a onclick="changestatus(3,'+serviceId+')">Cancel</a></li>';
                    if(status==0){
                        $("#btndropdown"+serviceId).removeClass("btn-success");
                        $("#btndropdown"+serviceId).removeClass("btn-danger");
                        $("#btndropdown"+serviceId).addClass("btn-warning");
                        $("#btndropdown"+serviceId).html("Pending <span class='caret'></span>");

                        $("#statusdropdown ul").html(onhold+done+cancel);
                      }else if(status==1){
                        $("#btndropdown"+serviceId).removeClass("btn-success");
                        $("#btndropdown"+serviceId).removeClass("btn-warning");
                        $("#btndropdown"+serviceId).addClass("btn-danger");
                        $("#btndropdown"+serviceId).html("On hold <span class='caret'></span>");
                        $("#statusdropdown ul").html(pending+done+cancel);
                      }else if(status==2){
                        $("#btndropdown"+serviceId).removeClass("btn-danger");
                        $("#btndropdown"+serviceId).removeClass("btn-warning");
                        $("#btndropdown"+serviceId).addClass("btn-success");
                        $("#btndropdown"+serviceId).html("Done <span class='caret'></span>");
                        $("#statusdropdown ul").html(pending+onhold+cancel);
                      }
                      else if(status==3){
                        $("#btndropdown"+serviceId).removeClass("btn-success");
                        $("#btndropdown"+serviceId).removeClass("btn-warning");
                        $("#btndropdown"+serviceId).addClass("btn-danger");
                        $("#btndropdown"+serviceId).html("Cancel <span class='caret'></span>");
                        $("#statusdropdown ul").html(pending+onhold+done);
                      }
                     
                  
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
function getmachinedetails(machineid,type){

    $("#machinedetailpanel").hide();
    if(machineid!=0){
        var uurl = SITE_URL+"machine/getMachineDetailsByID";
        $.ajax({
            url: uurl,
            type: 'POST',
            data: {machineid:String(machineid)},
            dataType: 'json',
            async: false,
            beforeSend: function(){
                $('.mask').show();
                $('#loader').show();
            },
            success: function(response){
                var TAB1_HTML = "";
                if(response){
                    var status = '<span class="label label-danger">In Active</span>';
                    if(response['status']==1){
                        status = '<span class="label label-success">Active</span>';
                    }
                    var TAB1_HTML = '<tr>\
                                        <th width="15%">Company Name</th>\
                                        <td width="35%">'+response['companyname']+'</td>\
                                        <th width="20%">Power Consumption in Units</th>\
                                        <td width="35%">'+response['unitconsumption']+'</td>\
                                    </tr>\
                                    <tr>\
                                        <th>Machine Name</th>\
                                        <td>'+response['machinename']+'</td>\
                                        <th>No. of Hours Used</th>\
                                        <td>'+response['noofhoursused']+'</td>\
                                    </tr>\
                                    <tr>\
                                        <th>Model No.</th>\
                                        <td>'+response['modelno']+'</td>\
                                        <th>Min. Production Capacity</td>\
                                        <td>'+response['minimumcapacity']+'</td>\
                                    </tr>\
                                    <tr>\
                                        <th>Purchase Date</th>\
                                        <td>'+response['purchasedate']+'</td>\
                                        <th>Max. Production Capacity</td>\
                                        <td>'+response['maximumcapacity']+'</td>\
                                    </tr>\
                                    <tr>\
                                        <th>Status</th>\
                                        <td>'+status+'</td>\
                                        <td colspan="2"></td>\
                                    </tr>\
                                    <tr>\
                                        <th>Entry Date</th>\
                                        <td>'+response['entrydate']+'</td>\
                                        <td colspan="2"></td>\
                                    </tr>';

                    $("#machinedetail").html(TAB1_HTML);

                    $("#machinedetailpanel").show();
                    $("#machineid").val(response['id']);

                    if(type=="forceupdate"){
                        oTable.ajax.reload(null, false);
                    }
                    $("#firsttab").click();
                }
            },
            error: function(xhr) {
            //alert(xhr.responseText);
            },
            complete: function(){
                $('.mask').hide();
                $('#loader').hide();
            },
        });
        
    } 
}
function openservicepopup(){

    $("#serviceModal .modal-title").text("Add Service");
    $("#machine-service-form input[name=submit]").val('ADD').attr('onclick','checkvalidation()');
    $("machineservicedetailid").val("");
    $("#serviceby_div").removeClass("has-error is-focused");
    $("#contactname_div").removeClass("has-error is-focused");
    $("#contactmobileno_div").removeClass("has-error is-focused");
    $("#servicedate_div").removeClass("has-error is-focused");
    $("#servicedue_div").removeClass("has-error is-focused");
    $("#servicestatus_div").removeClass("has-error is-focused");

    $("#machineservicedetailid,#serviceby,#contactname,#contactmobileno,#servicedate,#servicedue,#status").val("");
    $('#status').selectpicker('refresh');
    $("#serviceModal").modal("show");
}
function editservice(serviceid){

    $("#serviceby_div").removeClass("has-error is-focused");
    $("#contactname_div").removeClass("has-error is-focused");
    $("#contactmobileno_div").removeClass("has-error is-focused");
    $("#servicedate_div").removeClass("has-error is-focused");
    $("#servicedue_div").removeClass("has-error is-focused");
    $("#servicestatus_div").removeClass("has-error is-focused");

    $("#machineservicedetailid,#serviceby,#contactname,#contactmobileno,#servicedate,#servicedue,#status").val("");
    $('#status').selectpicker('refresh');
    if(serviceid!=0){
        var uurl = SITE_URL+"machine/getMachineServiceDataByID";
        $.ajax({
            url: uurl,
            type: 'POST',
            data: {serviceid:String(serviceid)},
            dataType: 'json',
            async: false,
            beforeSend: function(){
                $('.mask').show();
                $('#loader').show();
            },
            success: function(response){
                var TAB1_HTML = "";
                if(response){
                    
                    $("#machineservicedetailid").val(serviceid);
                    $("#serviceModal .modal-title").text("Edit Service");
                    $("#machine-service-form input[name=submit]").val('UPDATE').attr('onclick','checkvalidation(1)');
                    $("#serviceby").val(response['serviceby']);
                    $("#contactname").val(response['contactname']);
                    $("#contactmobileno").val(response['contactmobileno']);
                    $("#servicedate").val(response['servicedate']);
                    $("#status").val(response['status']).selectpicker('refresh');
                    
                    if(response['servicedate']!=""){
                        $("#servicedue").val(response['servicedue']).prop("disabled", false);
                    }else{
                        $("#servicedue").val("").prop("disabled", true);
                    }
                    /* if(type=="forceupdate"){
                        oTable.ajax.reload(null, false);
                    }
                    $("#firsttab").click(); */
                }
            },
            error: function(xhr) {
            //alert(xhr.responseText);
            },
            complete: function(){
                $('.mask').hide();
                $('#loader').hide();
            },
        });
        
    } 

    $("#serviceModal").modal("show");
}
function checkvalidation(ACTION_MODAL=0) {
   
    var serviceby = $('#serviceby').val().trim();
    var contactname = $('#contactname').val().trim();
    var contactmobileno = $('#contactmobileno').val().trim();
    var servicedate = $('#servicedate').val().trim();
    var servicedue = $('#servicedue').val().trim();
    var status = $('#status').val();
    
    var isvalidserviceby = isvalidcontactname = isvalidcontactmobileno = isvalidservicedate = isvalidservicedue =isvalidservicestatus = 1;
    
    PNotify.removeAll();
    if(serviceby=="") {
        $("#serviceby_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter service by !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidserviceby = 0;
    } else if(serviceby.length < 2){
        $("#serviceby_div").addClass("has-error is-focused");
        new PNotify({title: 'Service by required minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidserviceby = 0;
    } else {
        $("#serviceby_div").removeClass("has-error is-focused");
    }
    if(contactname=="") {
        $("#contactname_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter contact name !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidcontactname = 0;
    } else if(contactname.length < 2){
        $("#contactname_div").addClass("has-error is-focused");
        new PNotify({title: 'Contact name required minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidcontactname = 0;
    } else {
        $("#contactname_div").removeClass("has-error is-focused");
    }    
    if(contactmobileno=="") {
        $("#contactmobileno_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter contact mobile no. !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidcontactmobileno = 0;
    } else if(contactmobileno.length != 10){
        $("#contactmobileno_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter 10 digits contact mobile no. !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidcontactmobileno = 0;
    } else {
        $("#contactmobileno_div").removeClass("has-error is-focused");
    }  
    if(servicedate=="") {
        $("#servicedate_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select service date !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidservicedate = 0;
    } else {
        $("#servicedate_div").removeClass("has-error is-focused");
    }  
    if(servicedue=="") {
        $("#servicedue_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select service due date !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidservicedue = 0;
    } else {
        $("#servicedue_div").removeClass("has-error is-focused");
    } 
   

    if(isvalidserviceby == 1 && isvalidcontactname == 1 && isvalidcontactmobileno == 1 && isvalidservicedate == 1 && isvalidservicestatus && isvalidservicedue == 1){
        var formData = new FormData($('#machine-service-form')[0]);
        formData.append('machineid', $('#machineid').val());
        if(ACTION_MODAL == 0){ 
            // INSERT
            var baseurl = SITE_URL + 'machine/machine-service-add';
            $.ajax({
                
                url: baseurl,
                type: 'POST',
                data: formData,
                //async: false,
                beforeSend: function(){
                    $('.mask').show();
                    $('#loader').show();
                },
                success: function(response){
                    var data = JSON.parse(response);
                    if(data['error']==1){
                        new PNotify({title: 'Service successfully added.',styling: 'fontawesome',delay: '3000',type: 'success'});
                        setTimeout(function() { oTable.ajax.reload(null, false); $("#serviceModal").modal("hide"); }, 500);
                    }else if(data['error']==3){
                        new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else{
                        new PNotify({title: 'Service not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }
                },
                error: function(xhr) {
                },
                complete: function(){
                    $('.mask').hide();
                    $('#loader').hide();
                },
                cache: false,
                contentType: false,
                processData: false
            });
        } else {
            // MODIFY
            var baseurl = SITE_URL + 'machine/update-machine-service';
            $.ajax({
                url: baseurl,
                type: 'POST',
                data: formData,
                beforeSend: function(){
                    $('.mask').show();
                    $('#loader').show();
                },
                success: function(response){
                    var data = JSON.parse(response);
                    if(data['error']==1){
                        new PNotify({title: 'Service successfully updated.',styling: 'fontawesome',delay: '3000',type: 'success'});
                        setTimeout(function() { oTable.ajax.reload(null, false); $("#serviceModal").modal("hide");}, 500);
                    }else if(data['error']==3){
                        new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else{
                        new PNotify({title: 'Service not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }
                },
                error: function(xhr) {
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
