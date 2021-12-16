
$(document).ready(function() {
    
    //list("ordertable","Order/listing",[0,-1]);
    oTable = $('#productinquirytable').dataTable
      ({
        "language": {
          "lengthMenu": "_MENU_"
        },
        "pageLength": 50,
        "columnDefs": [{
          'orderable': false,
          'targets': [0,4,-1,-2,-3,-5]
        }],
        "order": [], //Initial no order.
        'serverSide': true,//Feature control DataTables' server-side processing mode.
        // Load data for the table's content from an Ajax source
        "ajax": {
          "url": SITE_URL+"product-inquiry/listing",
          "type": "POST",
          "data": function ( data ) {
            data.startdate = $('#startdate').val();
            data.enddate = $('#enddate').val();
            data.channelid = $('#channelid').val();
            data.memberid = $('#memberid').val();
            data.productid = $('#productid').val();
            data.type = $('#type').val();
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
            loadpopover();
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
      format: 'dd/mm/yyyy',
      autoclose: true,
      todayBtn:"linked"
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
$("#channelid").change(function(){
  var channelid = $(this).val();
  $('#memberid')
      .find('option')
      .remove()
      .end()
      .append('<option value="">Select '+Member_label+'</option>')
      .val('whatever')
      ;
  $('#productid')
      .find('option')
      .remove()
      .end()
      .append('<option value="0">All Product</option>')
      .val('whatever')
      ;
  if(channelid!='' && channelid!=0){
    getmembers(channelid);
  }else if(channelid==0){
    getmemberproduct(channelid);
  }
  $('#memberid').selectpicker('refresh');
  $('#productid').selectpicker('refresh');
});


$("#memberid").change(function(){

  var memberid = $(this).val();
  $('#productid')
      .find('option')
      .remove()
      .end()
      .append('<option value="0">All Product</option>')
      .val('whatever')
      ;
  if(memberid!='' && memberid!=0){
    var channelid = $('#channelid').val();
    getmemberproduct(channelid,memberid);
  }
  $('#productid').selectpicker('refresh');
});


function applyFilter(){
  oTable.fnDraw();
}

function getmembers(channelid,memberid=0){
  var uurl = SITE_URL+"member/getmembers";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {channelid:channelid},
    dataType: 'json',
    async: false,
    success: function(response){

      for(var i = 0; i < response.length; i++) {

        $('#memberid').append($('<option>', { 
          value: response[i]['id'],
          text : ucwords(response[i]['name'])
        }));

      }
      if(memberid!=0){
        $("#memberid").val(memberid);
      }
      // $('#product'+prow).val(areaid);
      $('#memberid').selectpicker('refresh');
    },
    error: function(xhr) {
          //alert(xhr.responseText);
        },
      });
}

function getmemberproduct(channelid,memberid=0){
  var uurl = SITE_URL+"member/getmemberproduct";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {channelid:channelid,memberid:memberid},
    dataType: 'json',
    async: false,
    success: function(response){

      for(var i = 0; i < response.length; i++) {

        var productname = response[i]['name'].replace("'","&apos;");
        if(DROPDOWN_PRODUCT_LIST==0){
          $('#productid').append($('<option>', { 
            value: response[i]['id'],
            text :  productname
          }));
        }else{

          $('#productid').append($('<option>', { 
            value: response[i]['id'],
            //text :  response[i]['name'],
            "data-content" :'<img src="'+PRODUCT_PATH+response[i]['image']+'" style="width:40px">  ' + productname      
           
          }));
        }

      }
      $('#productid').selectpicker('refresh');
    },
    error: function(xhr) {
          //alert(xhr.responseText);
    },
  });
}

function viewinquirymessage(inquiryid){
  $('.modal-body #message').html($('#message'+inquiryid).html());
  $('#myModal').modal('show');
}