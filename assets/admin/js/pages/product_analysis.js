$(document).ready(function() {

    $('#year').datepicker({
        todayHighlight: true,
        format: 'yyyy',
        autoclose: true,
        viewMode: "years", 
        minViewMode: "years"
    });

    $(function () {
        $('.panel-heading.filter-panel').click(function() {
            $(this).find('.button-icon span').html($(this).find('.button-icon span').text() == 'keyboard_arrow_down' ? 'keyboard_arrow_up' : 'keyboard_arrow_down');
            //$(this).children().toggleClass(" ");
            $(this).next().slideToggle({duration: 200});
            $(this).toggleClass('panel-collapsed');
            return false;
        });
    });

    getproductanalysisdata();
});

function applyFilter(){
    getproductanalysisdata();
}

function getproductanalysisdata(){

    var uurl = SITE_URL+"product-analysis/getproductanalysisdata";
    var formData = new FormData($('#productanalysisform')[0]);
    $.ajax({
        url: uurl,
        type: 'POST',
        data: formData,
        //dataType: "json",
        //async: false,
        beforeSend: function(){
            $('.mask').show();
            $('#loader').show();
        },
        success: function(response){
            var fixedColumns = 3;
            var dataObject = $.parseJSON(response);
            if ( $.fn.DataTable.isDataTable('#productanalysisreporttable') ) {
            $('#productanalysisreporttable').DataTable().destroy();
            }
            $('#productanalysisreporttable').empty();
            
            table = $('#productanalysisreporttable').DataTable({
    
                "data": dataObject.DATA,
                "columns": dataObject.COLUMNS,
                "language": {
                "lengthMenu": "_MENU_"
                },
                
                "destroy": true,
                "pageLength": 10,
                "columnDefs": [{
                    'orderable': false,
                    'targets': []
                }],
                "order": [], //Initial no order.
                /* "scrollCollapse": true,
                "scrollY": "500px",
                "scrollX": true,
                "fixedColumns":   {
                    leftColumns: fixedColumns,
                    rightColumns: 0
                } */
            });
            $('.dataTables_filter input').attr('placeholder','Search...');
    
            $('.panel-ctrls.panel-tbl').html('');
            $('.panel-footer').html('');
            $('.dataTables_filter input').attr('placeholder','Search...');
    
    
            //DOM Manipulation to move datatable elements integrate to panel
            $('.panel-ctrls.panel-tbl').append($('.dataTables_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center form-group");
            $('.panel-ctrls.panel-tbl').append("<i class='separator'></i>");
            $('.panel-ctrls.panel-tbl').append($('.dataTables_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center form-group");
    
            $('.panel-footer').append($(".dataTable+.row"));
            $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-md");
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

function exporttoexcelproductanalysisreport(){

    var countryid = ($('#countryid').val()!=null?$('#countryid').val():'');
    var provinceid = ($('#provinceid').val()!=null?$('#provinceid').val():'');
    var cityid = ($('#cityid').val()!=null?$('#cityid').val():'');
    var year = $('#year').val();
    var month = ($('#month').val()!=null?$('#month').val():'');
    var employee = ($('#employee').val()!=null?$('#employee').val():'');
    var product = ($('#product').val()!=null?$('#product').val():'');
    var seller = ($('#seller').val()!=null?$('#seller').val():'');
    var buyer = ($('#buyer').val()!=null?$('#buyer').val():'');

    var totalRecords =$("#productanalysisreporttable").DataTable().page.info().recordsDisplay;
    $.skylo('end');
    if(totalRecords != 0){
      window.location= SITE_URL+"product-analysis/exporttoexcelproductanalysisreport?employee="+employee+"&seller="+seller+"&buyer="+buyer+"&product="+product+"&countryid="+countryid+"&provinceid="+provinceid+"&cityid="+cityid+"&year="+year+"&month="+month;
    }else{
        new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }

}
function exporttopdfproductanalysisreport(){

    var countryid = ($('#countryid').val()!=null?$('#countryid').val():'');
    var provinceid = ($('#provinceid').val()!=null?$('#provinceid').val():'');
    var cityid = ($('#cityid').val()!=null?$('#cityid').val():'');
    var year = $('#year').val();
    var month = ($('#month').val()!=null?$('#month').val():'');
    var employee = ($('#employee').val()!=null?$('#employee').val():'');
    var product = ($('#product').val()!=null?$('#product').val():'');
    var seller = ($('#seller').val()!=null?$('#seller').val():'');
    var buyer = ($('#buyer').val()!=null?$('#buyer').val():'');

    var totalRecords =$("#productanalysisreporttable").DataTable().page.info().recordsDisplay;
    $.skylo('end');
    if(totalRecords != 0){
        window.location= SITE_URL+"product-analysis/exporttopdfproductanalysisreport?employee="+employee+"&seller="+seller+"&buyer="+buyer+"&product="+product+"&countryid="+countryid+"&provinceid="+provinceid+"&cityid="+cityid+"&year="+year+"&month="+month;
    }else{
        new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }
}

function printproductanalysisreport(){

    var countryid = $('#countryid').val() || [];
    countryid = countryid.join(',');
    var provinceid = $('#provinceid').val() || [];
    provinceid = provinceid.join(',');
    var cityid = $('#cityid').val() || [];
    cityid = cityid.join(',');
    var year = $('#year').val();
    var month = $('#month').val() || [];
    month = month.join(',');
    var employee = $('#employee').val() || [];
    employee = employee.join(',');
    var product = $('#product').val() || [];
    product = product.join(',');
    var seller = $('#seller').val() || [];
    seller = seller.join(',');
    var buyer = $('#buyer').val() || [];
    buyer = buyer.join(',');

    var totalRecords =$("#productanalysisreporttable").DataTable().page.info().recordsDisplay;
    $.skylo('end');
    if(totalRecords != 0){
        var uurl = SITE_URL + "product-analysis/printproductanalysisreport";
        $.ajax({
            url: uurl, 
            type: 'POST',
            data: {countryid:countryid,provinceid:provinceid,cityid:cityid,year:year,month:month,employee:employee,product:product,seller:seller,buyer:buyer},
            async: false,
            //dataType: 'json',
            beforeSend: function() {
                $('.mask').show();
                $('#loader').show();
            },
            success: function(response) {
                
                var html = JSON.parse(response);

                printdocument(html);
            },
            error: function(xhr) {
                //alert(xhr.responseText);
            },
            complete: function() {
                $('.mask').hide();
                $('#loader').hide();
            },
        });
    }else{
        new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }
}