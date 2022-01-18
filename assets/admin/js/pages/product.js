$(document).ready(function() {

    oTable = $('#producttable').DataTable({

        "processing": true, //Feature control the processing indicator.
        "language": {
            "lengthMenu": "_MENU_"
        },
        drawCallback: function() {
            loadpopover();
        },
        "pageLength": 10,
        /* "scrollCollapse": true,
        "scrollY": "500px", */
        "columnDefs": [{
            'orderable': false,
            'targets': [0,4,-1,-2,-3]
        }, { targets: 4, className: "text-right" }],
        "order": [], //Initial no order.
        'serverSide': true, //Feature control DataTables' server-side processing mode.
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": SITE_URL + "product/listing",
            "type": "POST",
            "data": function(data) {
                data.categoryid = $("#categoryid").val();
                data.brandid = $("#brandid").val();
                data.producttype = $("#producttype").val();
            },
            beforeSend: function() {
                $('.mask').show();
                $('#loader').show();
            },
            complete: function() {
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


    $(function() {
        $('.panel-heading.filter-panel').click(function() {
            $(this).find('.button-icon span').html($(this).find('.button-icon span').text() == 'keyboard_arrow_down' ? 'keyboard_arrow_up' : 'keyboard_arrow_down');
            //$(this).children().toggleClass(" ");
            $(this).next().slideToggle({ duration: 200 });
            $(this).toggleClass('panel-collapsed');
            return false;
        });
    });

    $('#attachment').change(function() {
        var val = $(this).val();
        var filename = $("#attachment").val().replace(/C:\\fakepath\\/i, '');

        switch (val.substring(val.lastIndexOf('.') + 1).toLowerCase()) {
            case 'xl':
            case 'xlc':
            case 'xls':
            case 'xlsx':
            case 'ods':
                $("#Filetext").val(filename);
                isvalidfiletext = 1;
                $("#attachment_div").removeClass("has-error is-focused");
                break;
            default:
                $("#Filetext").val("");
                isvalidfiletext = 0;
                $("#attachment_div").addClass("has-error is-focused");
                new PNotify({ title: 'Please upload valid excel file !', styling: 'fontawesome', delay: '3000', type: 'error' });
                break;
        }
    });

    $('#assignproductattachment').change(function() {
        var val = $(this).val();
        var filename = $("#assignproductattachment").val().replace(/C:\\fakepath\\/i, '');

        switch (val.substring(val.lastIndexOf('.') + 1).toLowerCase()) {
            case 'xl':
            case 'xlc':
            case 'xls':
            case 'xlsx':
            case 'ods':
                $("#assignproductFiletext").val(filename);
                isvalidfiletext = 1;
                $("#assignproductattachment_div").removeClass("has-error is-focused");
                break;
            default:
                $("#assignproductFiletext").val("");
                isvalidfiletext = 0;
                $("#assignproductattachment_div").addClass("has-error is-focused");
                new PNotify({ title: 'Please upload valid excel file !', styling: 'fontawesome', delay: '3000', type: 'error' });
                break;
        }
    });

    $('#zipfile').change(function() {
        var val = $(this).val();
        var filename = $("#zipfile").val().replace(/C:\\fakepath\\/i, '');

        switch (val.substring(val.lastIndexOf('.') + 1).toLowerCase()) {
            case 'zip':

                if (parseInt(this.files[0].size) > UPLOAD_MAX_ZIP_FILE_SIZE) {
                    $("#Zipfiletext").val('');
                    $("#validzipfile").val('1');
                    $("#validzipfilesize").val('0');
                    isvalidfiletext = 0;
                    $("#zipfile_div").addClass("has-error is-focused");
                    new PNotify({ title: 'Zip file is too large (max size ' + formatBytes(UPLOAD_MAX_ZIP_FILE_SIZE) + ')!', styling: 'fontawesome', delay: '3000', type: 'error' });
                } else {
                    $("#Zipfiletext").val(filename);
                    $("#validzipfile").val('1');
                    $("#validzipfilesize").val('1');
                    isvalidfiletext = 1;
                    $("#zipfile_div").removeClass("has-error is-focused");
                }
                break;
            default:
                $("#Zipfiletext").val("");
                $("#validzipfile").val('0');
                $("#validzipfilesize").val('0');
                isvalidfiletext = 0;
                $("#zipfile_div").addClass("has-error is-focused");
                new PNotify({ title: 'Please upload valid zip file !', styling: 'fontawesome', delay: '3000', type: 'error' });
                break;
        }
    });
});

function displaydescription(id) {
    var description = $('#description' + id).html();
    $('.modal-body').html(description.replace(/&nbsp;/g, ' '));
}

function applyFilter() {
    oTable.ajax.reload();
}

function importproduct() {
    PNotify.removeAll();

    $("#attachment_div").removeClass("has-error is-focused");
    $("#Filetext").val("");
    $('.selectpicker').selectpicker('refresh');
    $('#myProductImportModal').modal('show');
}

function checkimportproductvalidation() {

    var filetext = $("#Filetext").val();
    var isvalidfiletext = 0;

    PNotify.removeAll();

    //CHECK FILE
    if (filetext == '') {
        $("#attachment_div").addClass("has-error is-focused");
        new PNotify({ title: 'Please select excel file !', styling: 'fontawesome', delay: '3000', type: 'error' });
    } else {
        $("#attachment_div").removeClass("has-error is-focused");
        isvalidfiletext = 1;
    }
    if (isvalidfiletext == 1) {

        var formData = new FormData($('#productimportform')[0]);

        var uurl = SITE_URL + "product/importproduct";

        $.ajax({
            url: uurl,
            type: 'POST',
            data: formData,
            //async: false,
            beforeSend: function() {
                $('.mask').show();
                $('#loader').show();
            },
            success: function(response) {
                if (response == 1) {
                    new PNotify({ title: "Product successfully imported.", styling: 'fontawesome', delay: '3000', type: 'success' });
                    setTimeout(function() { window.location.reload(); }, 1500);
                } else if (response == 2) {
                    new PNotify({ title: "Uploaded file is not an excel file !", styling: 'fontawesome', delay: '3000', type: 'error' });
                } else if (response == 3) {
                    new PNotify({ title: "Excel file not uploaded !", styling: 'fontawesome', delay: '3000', type: 'error' });
                } else if (response == 4) {
                    new PNotify({ title: "Some field name are not match !", styling: 'fontawesome', delay: '3000', type: 'error' });
                } else if (response == 5) {
                    new PNotify({ title: "Please enter at least one product detail !", styling: 'fontawesome', delay: '3000', type: 'error' });
                } else if (response == 6) {
                    new PNotify({ title: "Please enter valid sheet name !", styling: 'fontawesome', delay: '3000', type: 'error' });
                } else {
                    new PNotify({ title: response, styling: 'fontawesome', delay: '3000', type: 'error' });
                }
            },
            error: function(xhr) {
                //alert(xhr.responseText);
            },
            complete: function() {
                $('.mask').hide();
                $('#loader').hide();
            },
            cache: false,
            contentType: false,
            processData: false
        });

    }
}

function assignproduct() {
    PNotify.removeAll();

    $("#assignproductattachment_div").removeClass("has-error is-focused");
    $("#assignproductFiletext").val("");
    $('.selectpicker').selectpicker('refresh');
    $('#myDetailModal').modal('show');
}

function checkassignproductvalidation() {

    var filetext = $("#assignproductFiletext").val();

    var isvalidfiletext = 0;

    PNotify.removeAll();

    //CHECK FILE
    if (filetext == '') {
        $("#assignproductattachment_div").addClass("has-error is-focused");
        new PNotify({ title: 'Please select excel file !', styling: 'fontawesome', delay: '3000', type: 'error' });
    } else {
        $("#assignproductattachment_div").removeClass("has-error is-focused");
        isvalidfiletext = 1;
    }
    if (isvalidfiletext == 1) {

        var formData = new FormData($('#assignproductform')[0]);

        var uurl = SITE_URL + "product/importproductprice";

        $.ajax({
            url: uurl,
            type: 'POST',
            data: formData,
            //async: false,
            beforeSend: function() {
                $('.mask').show();
                $('#loader').show();
            },
            success: function(response) {
                if (response == 1) {
                    new PNotify({ title: "Product successfully imported.", styling: 'fontawesome', delay: '3000', type: 'success' });
                    setTimeout(function() { window.location.reload(); }, 1500);
                } else if (response == '2') {
                    new PNotify({ title: "Uploaded file is not an excel file !", styling: 'fontawesome', delay: '3000', type: 'error' });
                } else if (response == '3') {
                    new PNotify({ title: "Excel file not uploaded !", styling: 'fontawesome', delay: '3000', type: 'error' });
                } else if (response == '4') {
                    new PNotify({ title: "Some field name are not match !", styling: 'fontawesome', delay: '3000', type: 'error' });
                } else if (response == '5') {
                    new PNotify({ title: "Please enter at least one product detail !", styling: 'fontawesome', delay: '3000', type: 'error' });
                } else {
                    new PNotify({ title: response, styling: 'fontawesome', delay: '3000', type: 'error' });
                }
            },
            error: function(xhr) {
                //alert(xhr.responseText);
            },
            complete: function() {
                $('.mask').hide();
                $('#loader').hide();
            },
            cache: false,
            contentType: false,
            processData: false
        });

    }
}

function viewvariantdetails(productid, productname) {
    PNotify.removeAll();
    $('#myModal .modal-title').html('Variant Details (' + productname + ')');
    $('.modal-body .table-responsive').html($('#variant' + productid).html());
    $('#myModal').modal('show');
}

function exportadminproduct() {

    var categoryid = $('#categoryid').val() || [];
    categoryid = categoryid.join(',');
    var brandid = $("#brandid").val() || [];
    brandid = brandid.join(',');
    var producttype = $("#producttype").val();

    var totalRecords = $("#producttable").DataTable().page.info().recordsDisplay;
    $.skylo('end');
    if (totalRecords != 0) {
        window.location = SITE_URL + "product/exportadminproduct?categoryid=" + categoryid + "&brandid=" + brandid + "&producttype=" + producttype;
    } else {
        new PNotify({ title: 'No data available !', styling: 'fontawesome', delay: '3000', type: 'error' });
    }
}

function uploadproductfile() {
    PNotify.removeAll();

    $("#zipfile_div").removeClass("has-error is-focused");
    $("#Zipfiletext").val("");
    $('.selectpicker').selectpicker('refresh');
    $('#myProductFileModal').modal('show');
}

function checkvalidationforproductimage() {

    var filetext = $("#Zipfiletext").val();
    var validzipfile = $("#validzipfile").val();
    var validzipfilesize = $("#validzipfilesize").val();
    var isvalidfiletext = 0;

    PNotify.removeAll();

    //CHECK FILE
    if (filetext == '') {
        $("#zipfile_div").addClass("has-error is-focused");
        new PNotify({ title: 'Please select zip file !', styling: 'fontawesome', delay: '3000', type: 'error' });
    } else if (validzipfile == 0) {
        $("#zipfile_div").addClass("has-error is-focused");
        new PNotify({ title: 'Please upload valid zip file !', styling: 'fontawesome', delay: '3000', type: 'error' });
    } else if (validzipfilesize == 0) {
        $("#zipfile_div").addClass("has-error is-focused");
        new PNotify({ title: 'Zip file is too large (max size ' + formatBytes(UPLOAD_MAX_ZIP_FILE_SIZE) + ')!', styling: 'fontawesome', delay: '3000', type: 'error' });
    } else {
        $("#zipfile_div").removeClass("has-error is-focused");
        isvalidfiletext = 1;
    }
    if (isvalidfiletext == 1) {

        var formData = new FormData($('#productfileuploadform')[0]);

        var uurl = SITE_URL + "product/uploadproductfile";

        $.ajax({
            url: uurl,
            type: 'POST',
            data: formData,
            //async: false,
            beforeSend: function() {
                $('.mask').show();
                $('#loader').show();
            },
            success: function(response) {
                if (response == 1) {
                    new PNotify({ title: "Product file successfully uploaded.", styling: 'fontawesome', delay: '3000', type: 'success' });
                    setTimeout(function() { window.location.reload(); }, 1500);
                } else if (response == '2') {
                    new PNotify({ title: "Uploaded file is not an zip file !", styling: 'fontawesome', delay: '3000', type: 'error' });
                } else if (response == '3') {
                    new PNotify({ title: "Zip file not uploaded !", styling: 'fontawesome', delay: '3000', type: 'error' });
                } else if (response == '4') {
                    new PNotify({ title: 'Zip file is too large (max size ' + formatBytes(UPLOAD_MAX_ZIP_FILE_SIZE) + ')!', styling: 'fontawesome', delay: '3000', type: 'error' });
                } else {
                    new PNotify({ title: response, styling: 'fontawesome', delay: '3000', type: 'error' });
                }
            },
            error: function(xhr) {
                //alert(xhr.responseText);
            },
            complete: function() {
                $('.mask').hide();
                $('#loader').hide();
            },
            cache: false,
            contentType: false,
            processData: false
        });

    }
}