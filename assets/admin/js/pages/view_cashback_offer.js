$(document).ready(function(){
    producttable = $('#producttable').DataTable({
      "language": {
        "lengthMenu": "_MENU_"
      },
      "pageLength": 10,
      "columnDefs": [{
        'orderable': false,
        'targets': []
      }],
      "order": [], //Initial no order.
      responsive: true,
    });

    $('#producttable_filter input').attr('placeholder','Search...');

    //DOM Manipulation to move datatable elements integrate to panel
    $('.panel-ctrls.panel-tbl').append($('#producttable_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls.panel-tbl').append("<i class='separator'></i>");
    $('.panel-ctrls.panel-tbl').append($('#producttable_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('#producttable .panel-footer').append($(".dataTable+.row"));
    $('#producttable_paginate>ul.pagination').addClass("pull-right pagination-md");
});

function printQRCode(){

    var cashbackofferid = $("#cashbackofferid").val();
    var countproduct = $('input[name="priceid[]"]').length;
    
    if(cashbackofferid!="" && countproduct > 0){
        var isprint = 0;
        $('input[name="printnoofcopies[]').each(function(){
            if(this.value!=0){
                isprint = 1;
            }
        });
        if(isprint==1){
            var formData = new FormData($('#printqrcodeform')[0]);
            var uurl = SITE_URL + "cashback-offer/printQRCode";
            $.ajax({
                url: uurl,
                type: 'POST',
                data: formData,
                //dataType: 'json',
                /* async: false, */
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
                        $('.mask').hide();
                        $('#loader').hide();
                        window.frames["frame1"].focus();
                        window.frames["frame1"].print();
                        document.body.removeChild(frame1);
                    }, 5000);
                },
                error: function(xhr) {
                    //alert(xhr.responseText);
                },
                complete: function() {
                    
                },
                cache: false,
                contentType: false,
                processData: false
            });
        }else{
            new PNotify({title: 'Enter no. of copies to print in atleast one product !',styling: 'fontawesome',delay: '3000',type: 'error'});      
        }
    }else{
      new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }
  }