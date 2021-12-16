$(document).ready(function(){

    loadpopover();
    $('table').on('mouseover', function(e){
        if($('.popoverButton').length>1)
        $('.popoverButton').popover('hide');
        $(e.target).popover('toggle');
    });
  
    dtable = $('#followuptbl').DataTable({
      "processing": true,//Feature control the processing indicator.
      "language": {
          "lengthMenu": "_MENU_"
      },
      drawCallback: function () {
        loadpopover();
      },
      "columnDefs": [ {
        "targets": [0,1,2,-1],
        "orderable": false
      }, { width: 40, targets: [4,5] } ],
      responsive: true,
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      "ajax": {
        "url": SITE_URL+"crm-inquiry/inquiryfollowuplisting",
        "type": "POST",
        "data" :function ( data ) {
            data.inquiryid = $("#inquiryid").val();
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
    $('#followuptbl_filter input').attr('placeholder','Search...');
     //DOM Manipulation to move datatable elements integrate to panel
     $('#followuptbl_length').append($('#followuptbl_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
     $('#followuptbl_length').append("<i class='separator'></i>");
     $('#followuptbl_length').append($('#followuptbl_filter').addClass("pull-left ")).find("label").addClass("panel-ctrls-center");
   
     qftable = $('#quotationfiletbl').DataTable({
      "processing": true,//Feature control the processing indicator.
      "language": {
          "lengthMenu": "_MENU_"
      },
      drawCallback: function () {
        loadpopover();
      },
      "columnDefs": [ {
        "targets": [0,2,-1],
        "orderable": false
      }, { width: 40, targets: [4,5] } ],
      responsive: true,
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      "ajax": {
        "url": SITE_URL+"crm-inquiry/inquiryquotationlisting",
        "type": "POST",
        "data" :function ( data ) {
          data.inquiryid = $("#inquiryid").val();
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
    $('#quotationfiletbl_filter input').attr('placeholder','Search...');
    //DOM Manipulation to move datatable elements integrate to panel
    $('#quotationfiletbl_length').append($('#quotationfiletbl_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('#quotationfiletbl_length').append("<i class='separator'></i>");
    $('#quotationfiletbl_length').append($('#quotationfiletbl_filter').addClass("pull-left ")).find("label").addClass("panel-ctrls-center");
  
    table=$('#transferhistorytbl').dataTable({
      "language": {
          "lengthMenu": "_MENU_"
      },
      drawCallback: function () {
        loadpopover();
      },
      "columnDefs": [ {
        "targets": [0,-1],
        "orderable": false
      }],
      responsive: true
    });
  
    $('#transferhistorytbl_filter input').attr('placeholder','Search...');
    //DOM Manipulation to move datatable elements integrate to panel
    $('#transferhistorytbl_length').append($('#transferhistorytbl_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('#transferhistorytbl_length').append("<i class='separator'></i>");
    $('#transferhistorytbl_length').append($('#transferhistorytbl_filter').addClass("pull-left ")).find("label").addClass("panel-ctrls-center");
    

    vtable=$('#viewinquirytbl').dataTable({
      "language": {
          "lengthMenu": "_MENU_"
      },
      drawCallback: function () {
        loadpopover();
      },
      footerCallback: function ( row, data, start, end, display ) {
        var api = this.api(), data;
  
        // Remove the formatting to get integer data for summation
        var intVal = function ( i ) {
            return typeof i === 'string' ?
                i.replace(/[\$,]/g, '')*1 :
                typeof i === 'number' ?
                    i : 0;
        };
  
        // Total over all pages
        /* grossamount = api
            .column( 4 )
            .data()
            .reduce( function (a, b) {
                return intVal(a) + intVal(b);
            }, 0 );
   */
        // Total over this page
        grossamount = api
            .column( 6, { page: 'current'} )
            .data()
            .reduce( function (a, b) {
                return intVal(a) + intVal(b);
            }, 0 );
  
        // Total over this page
        totaltax = api
        .column( 7, { page: 'current'} )
        .data()
        .reduce( function (a, b) {
            return intVal(a) + intVal(b);
        }, 0 );
  
        // Update footer
        $('#total').html(
            '<p>'+format.format(parseFloat(grossamount).toFixed(2))+"</p>"+
            '<p>'+format.format(parseFloat(totaltax).toFixed(2))+"</p>"+
            '<p>'+format.format(parseFloat(grossamount+totaltax).toFixed(2))+"</p>"
        );
    },
      "columnDefs": [ {
        "targets": [0,-1],
        "orderable": false
      }],
      responsive: true,
    });
  
    $('#viewinquirytbl_filter input').attr('placeholder','Search...');
    //DOM Manipulation to move datatable elements integrate to panel
    $('#viewinquirytbl_length').append($('#viewinquirytbl_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('#viewinquirytbl_length').append("<i class='separator'></i>");
    $('#viewinquirytbl_length').append($('#viewinquirytbl_filter').addClass("pull-left ")).find("label").addClass("panel-ctrls-center");
    
    ctable=$('#contactinquirytbl').dataTable({
      "language": {
          "lengthMenu": "_MENU_"
      },
      drawCallback: function () {
        loadpopover();
      },    
      "columnDefs": [ {
        "targets": [0,-1],
        "orderable": false
      }],
      responsive: true
    });
  
    $('#contactinquirytbl_filter input').attr('placeholder','Search...');
     //DOM Manipulation to move datatable elements integrate to panel
     $('#contactinquirytbl_length').append($('#contactinquirytbl_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
     $('#contactinquirytbl_length').append("<i class='separator'></i>");
     $('#contactinquirytbl_length').append($('#contactinquirytbl_filter').addClass("pull-left ")).find("label").addClass("panel-ctrls-center");
  
  });
  function exportreportpdf(){
    var inquiryid = $('#inquiryid').val();
    window.location= SITE_URL+"crm-inquiry/exportreportpdf?inquiryid="+inquiryid;
  }