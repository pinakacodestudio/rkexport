$(document).ready(function() {
    
    //list("pendingshippingtable","Pendingshipping/listing",[0,-1]);
    oTable = $('#cashbackreporttable').DataTable
    ({
    "language": {
        "lengthMenu": "_MENU_"
    },
    "pageLength": 50,
    "columnDefs": [{
        'orderable': false,
        'targets': [0]
    },
    { targets: [-2,-3], className: "text-right" },
    { targets: [-1], className: "text-center" }],
    "order": [], //Initial no order.
    "pendingshipping": [], //Initial no pendingshipping.
    'serverSide': true,//Feature control DataTables' server-side processing mode.
    // Load data for the table's content from an Ajax source
    "ajax": {
        "url": SITE_URL+'cashback-report/listing',
        "type": "POST",
        "data": function ( data ) {
            data.buyerchannelid = $('#buyerchannelid').val();
            data.buyermemberid = $('#buyermemberid').val();
            data.sellerchannelid = $('#sellerchannelid').val();
            data.sellermemberid = $('#sellermemberid').val();
            data.status = $('#status').val();
        },
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
        },
        error: function(xhr) {
          //alert(xhr.responseText);
        },
        complete: function(){
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

    $('#datepicker-range').datepicker({
      todayHighlight: true,
      format: 'dd/mm/yyyy'
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

    $("#buyerchannelid").change(function(){
        getmembers();
    });

    $("#sellerchannelid").change(function(){
      getmembers(1);
    });
});

function applyFilter(){
  oTable.ajax.reload(null, false);
}

function getmembers(type=0){
  
    var memberelement = $("#buyermemberid");
    var channelelement = $("#buyerchannelid");
  
    if(type==1){
      memberelement = $("#sellermemberid");
      channelelement = $("#sellerchannelid");
    }
    memberelement.find('option')
                .remove()
                .end()
                .val('whatever')
              ;
    memberelement.selectpicker('refresh');
    var channelid = channelelement.val();
  
    if(channelid!='' && channelid!=0){
      var uurl = SITE_URL+"member/getmembers";
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {channelid:channelid},
        dataType: 'json',
        async: false,
        success: function(response){
  
          for(var i = 0; i < response.length; i++) {
  
            memberelement.append($('<option>', { 
              value: response[i]['id'],
              text : ucwords(response[i]['namewithcodeormobile'])
            }));
  
          }
          memberelement.selectpicker('refresh');
        },
        error: function(xhr) {
          //alert(xhr.responseText);
        },
      });
    }
}

function changestatus(status, id){
  var uurl = SITE_URL+"cashback-report/update-status";
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

function exporttoexcelcashbackreport(){
  
  var buyerchannelid = $('#buyerchannelid').val();
  var buyermemberid = ($('#buyermemberid').val()!=null?$('#buyermemberid').val():"");
  var sellerchannelid = $('#sellerchannelid').val();
  var sellermemberid = ($('#sellermemberid').val()!=null?$('#sellermemberid').val():"");
  var status = $('#status').val();

  var totalRecords =$("#cashbackreporttable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"cashback-report/exporttoexcelcashbackreport?buyerchannelid="+buyerchannelid+"&buyermemberid="+buyermemberid+"&sellerchannelid="+sellerchannelid+"&sellermemberid="+sellermemberid+"&status="+status;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  
}
function exportToPDFCashbackReport(){

  var buyerchannelid = $('#buyerchannelid').val();
  var buyermemberid = ($('#buyermemberid').val()!=null?$('#buyermemberid').val():"");
  var sellerchannelid = $('#sellerchannelid').val();
  var sellermemberid = ($('#sellermemberid').val()!=null?$('#sellermemberid').val():"");
  var status = $('#status').val();

  var totalRecords =$("#cashbackreporttable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){

    window.location= SITE_URL+"cashback-report/exportToPDFCashbackReport?buyerchannelid="+buyerchannelid+"&buyermemberid="+buyermemberid+"&sellerchannelid="+sellerchannelid+"&sellermemberid="+sellermemberid+"&status="+status;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
}

function printcashbackreport(){

  var buyerchannelid = $('#buyerchannelid').val();
  var buyermemberid = $('#buyermemberid').val() || [];
  buyermemberid = buyermemberid.join(',');
  var sellerchannelid = $('#sellerchannelid').val();
  var sellermemberid = $('#sellermemberid').val() || [];
  sellermemberid = sellermemberid.join(',');
  var status = $('#status').val();

  var totalRecords =$("#cashbackreporttable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
      var uurl = SITE_URL + "cashback-report/printcashbackreport";
      $.ajax({
          url: uurl, 
          type: 'POST',
          data: {buyerchannelid:buyerchannelid,buyermemberid:buyermemberid,sellerchannelid:sellerchannelid,sellermemberid:sellermemberid,status:status},
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