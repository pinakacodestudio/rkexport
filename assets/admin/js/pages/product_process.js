$(document).ready(function() {
    
    oTable = $('#productprocesstable').DataTable({
        "language": {
            "lengthMenu": "_MENU_"
        },
        "pageLength": 10,
        "columnDefs": [{
            'orderable': false,
            'targets': [0,-1,-2]
        },{targets:[5,6,7], className: "text-center"}],
        drawCallback: function () {
            loadpopover();
        },
        "order": [], //Initial no order.
        responsive: true,
        'serverSide': true,//Feature control DataTables' server-side processing mode.
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": SITE_URL+"product-process/listing",
            "type": "POST",
            "data": function ( data ) {
                data.startdate = $("#startdate").val();
                data.enddate = $("#enddate").val();
                data.processgroupid = $("#processgroupid").val();
                data.processid = $("#processid").val();
                data.finalproductid = $("#finalproductid").val();
                data.processstatus = $("#processstatus").val();
                data.processtype = $("#processtype").val();
                data.processedby = $("#processedby").val();
                data.vendorid = $("#vendorid").val();
                data.batchid = $("#batchno").val();
                data.designationid = $("#designationid").val();
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

    $(function () {
        $('.panel-heading.filter-panel').click(function() {
            $(this).find('.button-icon span').html($(this).find('.button-icon span').text() == 'keyboard_arrow_down' ? 'keyboard_arrow_up' : 'keyboard_arrow_down');
            //$(this).children().toggleClass(" ");
            $(this).next().slideToggle({duration: 200});
            $(this).toggleClass('panel-collapsed');
            return false;
        });
    });
    $('#datepicker-range').datepicker({
        // todayHighlight: true,
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayBtn:"linked",
        /* startDate: new Date(), */
    });
    
    /* $('#processedby').on('change', function (e) {

        if(this.value!=1){
            $('#vendorid').val(0).prop("disabled",true).selectpicker('refresh');
        }else{
            $('#vendorid').prop("disabled",false).selectpicker('refresh');
        }
    }); */
    
});

function applyFilter(){
    oTable.ajax.reload(null, false);
}
function chageprocessstatus(status, id){
    var uurl = SITE_URL+"product-process/update-process-status";
    if(id!=''){
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
                data: {status:status,id:id},
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

function printchallan(productprocessid){
    
    var uurl = SITE_URL+"product-process/print-process-challan";
    if(productprocessid!=''){
        $.ajax({
            url: uurl,
            type: 'POST',
            data: {productprocessid:productprocessid},
            beforeSend: function(){
                $('.mask').show();
                $('#loader').show();
            },
            success: function(response){
                var obj = JSON.parse(response);
                if(obj['error']==1){
                    var w = window.open(obj['file'],'_blank');
                    w.print();
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
}

function checkmultipleprint(url){
    var inputs = $("input[type='checkbox']");
    if(currentdids == ""){
        swal("Cancelled", 'Please select Out Product Process !', "error");
    }else{
        if(url!=''){
          var datastr = 'ids='+currentdids;
          var baseurl = url;
          $.ajax({
            url: baseurl,
            type: 'POST',
            data: datastr,
            beforeSend: function(){
                $('.mask').show();
                $('#loader').show();
            },
            success: function(response){
                var obj = JSON.parse(response);
                if(obj['error']==1){
                    swal.close();
                    if($('#deletecheckall').prop('checked') == true){
                        $('#deletecheckall').prop('checked', false);
                    }
                    for(var i=1;i<inputs.length;i++){
                        if($('#'+inputs[i].id).prop('checked') == true){
                            $('#'+inputs[i].id).prop('checked', false);
                        }
                    }
                    currentdids = [];
                    position = 0;

                    var w = window.open(obj['file'],'_blank');
                    w.print();
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
  
    }
}