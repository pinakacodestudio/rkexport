
$(document).ready(function() {

    $('#paymentmethodtable').DataTable({
        "language": {
            "lengthMenu": "_MENU_"
        },
        "columnDefs": [ {
          "targets": [-1,-2],
          "orderable": false
        } ],
        responsive: true,
    });

    $('.dataTables_filter input').attr('placeholder','Search...');


    //DOM Manipulation to move datatable elements integrate to panel
    $('.panel-ctrls').append($('.dataTables_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls').append("<i class='separator'></i>");
    $('.panel-ctrls').append($('.dataTables_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('.panel-footer').append($(".dataTable+.row"));
    $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-md");
   
});

function updateactiveplan(){
    var paymentmethod = $("#displayinapp").val();

    if(paymentmethod!=0){
        var uurl = SITE_URL+"payment-method/changePaymentMethodInApp";
        $.ajax({
            url: uurl,
            type: 'POST',
            data: {paymentmethod:String(paymentmethod)},
            dataType: 'json',
            async: false,
            beforeSend: function(){
                $('.mask').show();
                $('#loader').show();
            },
            success: function(response){
                if(response==1){
                    new PNotify({title: "Payment method for app successfully updated.",styling: 'fontawesome',delay: '3000',type: 'success'});
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
    }else{
        $("#displayinapp_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select payment method !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }
}
