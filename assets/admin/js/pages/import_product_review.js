$(document).ready(function(){

    dtable = $('#documenttbl').DataTable({
        "processing": true,//Feature control the processing indicator.
        "language": {
            "lengthMenu": "_MENU_"
        },
        drawCallback: function () {
            loadpopover();
        },
        "columnDefs": [ {
          "targets": [0,-1,-2],
          "orderable": false
        } ],
        responsive: true,
    });
  
    $('#documenttbl_filter input').attr('placeholder','Search...');
    $('#documenttbl_filter input').attr('placeholder','Search...');
    //DOM Manipulation to move datatable elements integrate to panel
    $('#documenttbl_length').append($('#documenttbl_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center pr-sm");
    $('#documenttbl_length').append("<i class='separator'></i>");
    $('#documenttbl_length').append($('#documenttbl_filter').addClass("pull-left ")).find("label").addClass("panel-ctrls-center");



    table=$('#inquirytbl').DataTable({
        "language": {
            "lengthMenu": "_MENU_"
        },
        drawCallback: function () {
            loadpopover();
        },
        "columnDefs": [ {
          "targets": [0,-1,-2],
          "orderable": false
        }],
        responsive: true,
    });
  
    $('#inquirytbl_filter input').attr('placeholder','Search...');
    //DOM Manipulation to move datatable elements integrate to panel
    $('#inquirytbl_filter input').attr('placeholder','Search...');
    //DOM Manipulation to move datatable elements integrate to panel
    $('#inquirytbl_length').append($('#inquirytbl_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center pr-sm");
    $('#inquirytbl_length').append("<i class='separator'></i>");
    $('#inquirytbl_length').append($('#inquirytbl_filter').addClass("pull-left ")).find("label").addClass("panel-ctrls-center");


    $('input[name="importproductreviewfile"]').change(function() {
        var val = $(this).val();
        var filename = $("#importproductreviewfile").val().replace(/C:\\fakepath\\/i, '');
        switch (val.substring(val.lastIndexOf('.') + 1).toLowerCase()) {
            case 'xl':
            case 'xlc':
            case 'xls':
            case 'xlsx':
            case 'ods':
                $("#Filetext").val(filename);
                $("#importproductreviewfile_div").removeClass("has-error is-focused");
                break;
            default:
                $("#Filetext").val("");
                $("#importproductreviewfile_div").addClass("has-error is-focused");
                new PNotify({
                    title: 'Please upload valid excel file',
                    styling: 'fontawesome',
                    delay: '3000',
                    type: 'error'
                });
            break;
        }
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
});

function checkvalidation() {

    //CHECK FILE
    var filetext = $("#Filetext").val();
    
    var isvalidfiletext = 0;
    PNotify.removeAll();
    
    if (filetext == '') {
        $("#importproductreviewfile_div").addClass("has-error is-focused");
        new PNotify({
            title: 'Please select excel file !',
            styling: 'fontawesome',
            delay: '3000',
            type: 'error'
        });
        isvalidfiletext = 0;

    } else {
        var ext = filetext.substring(filetext.lastIndexOf('.') + 1);
        if (['xls', 'xlsx', 'ods'].indexOf(ext) < 0) {
            new PNotify({
                title: 'Please select excel file !',
                styling: 'fontawesome',
                delay: '3000',
                type: 'error'
            });
            isvalidfiletext = 0;
        } else {
            isvalidfiletext = 1;
        }

    }
     
    if (isvalidfiletext == 1) {
        
        var formData = new FormData($('#importproductreviewform')[0]);        
        var uurl = SITE_URL + "import-product-review/import-product-review";

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
                if (response == '2') {
                    new PNotify({
                        title: "Uploaded file is not an excel file",
                        styling: 'fontawesome',
                        delay: '3000',
                        type: 'error'
                    });
                } else if (response == '3') {
                    new PNotify({
                        title: "Excel file not uploaded",
                        styling: 'fontawesome',
                        delay: '3000',
                        type: 'error'
                    });
                } else if (response == '4') {
                    new PNotify({
                        title: "Some field name are not match",
                        styling: 'fontawesome',
                        delay: '3000',
                        type: 'error'
                    });
                } else if (response == '5') {
                    new PNotify({
                        title: "Please enter at least one product review",
                        styling: 'fontawesome',
                        delay: '3000',
                        type: 'error'
                    });
                } else {
                    if (response != "") {
                        new PNotify({
                            title: response,
                            styling: 'fontawesome',
                            delay: '4000',
                            type: 'error'
                        });
                    }
                    if (response == "") {
                        new PNotify({
                            title: "All product review imported successfully",
                            styling: 'fontawesome',
                            delay: '4000',
                            type: 'success'
                        });
                    }
                      setTimeout(function() {
                        window.location = SITE_URL + "import-product-review";
                    }, 5000);  
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
