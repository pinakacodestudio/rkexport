$(document).ready(function() {
    
    oTable = $('#rawmaterialrequesttable').DataTable({
        "language": {
            "lengthMenu": "_MENU_"
        },
        "pageLength": 10,
        "columnDefs": [{
            'orderable': false,
            'targets': [0,-1,-2]
        },
        {"targets":[-3],className: "text-center"}],
        "order": [], //Initial no order.
        'serverSide': true,//Feature control DataTables' server-side processing mode.
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": SITE_URL+"raw-material-request/listing",
            "type": "POST",
            "data": function ( data ) {
               
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

    $('.dataTables_filter input').attr('placeholder','Search...');


    //DOM Manipulation to move datatable elements integrate to panel
    $('.panel-ctrls.panel-tbl').append($('.dataTables_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls.panel-tbl').append("<i class='separator'></i>");
    $('.panel-ctrls.panel-tbl').append($('.dataTables_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('.panel-footer').append($(".dataTable+.row"));
    $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-md");
});

function applyFilter(){
    oTable.ajax.reload();
}

function chagerequeststatus(status, requestId){
    var uurl = SITE_URL+"raw-material-request/update-raw-material-request-status";
    if(requestId!=''){
      swal({    
        title: "Are you sure to change status?",
        type: "warning",   
        showCancelButton: true,   
        confirmButtonColor: "#DD6B55",   
        confirmButtonText: "Yes, change it!",   
        closeOnConfirm: false 
      }, 
      function(isConfirm){   
        if (isConfirm) {   
          $.ajax({
            url: uurl,
            type: 'POST',
            data: {status:status,requestId:requestId},
            beforeSend: function(){
                $('.mask').show();
                $('#loader').show();
            },
            success: function(response){
                if(response==1){
                    location.reload();
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

function viewrequestdetail(requestid){

    if(requestid!=""){
        var uurl = SITE_URL+"raw-material-request/get-raw-material-request-detail";
        $.ajax({
            url: uurl,
            type: 'POST',
            data: {requestid:String(requestid)},
            dataType: 'json',
            async: false,
            beforeSend: function(){
                $('.mask').show();
                $('#loader').show();
            },
            success: function(response){

                if(response.length > 0){
                    if(response[0]['remarks']!=""){

                        var html = '<tr>\
                                        <th width="19%">Remarks</th>\
                                        <td>'+response[0]['remarks']+'</td>\
                                    </tr>';
        
                        $("#requestdata").html(html);
                        $("#remarkstable").show();
                    }else{
                        $("#requestdata").html("");
                        $("#remarkstable").hide();
                    }
                    var products = '';
                    for(var i=0; i<response.length; i++){
                        
                        products += '<tr>\
                                        <td>'+(i+1)+'</td>\
                                        <td>'+response[i]['productname']+'</td>\
                                        <td>'+response[i]['unitname']+'</td>\
                                        <td class="text-right">'+response[i]['quantity']+'</td>\
                                    </tr>';
                    }
                    $("#requestproductdata").html(products);
                    
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
    // getStrartProcessProducts(productionplanid);
    $("#myModal").modal("show");
}