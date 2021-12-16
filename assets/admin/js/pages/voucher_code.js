
$(document).ready(function() {
    //list("vouchercodetable","vouchercode/listing",[0,-1,-2]);
    
    oTable = $('#vouchercodetable').dataTable({
        "language": {
            "lengthMenu": "_MENU_"
        },
        "pageLength": 50,
        "scrollCollapse": true,
        "scrollY": "500px",
        "columnDefs": [{
            'orderable': false,
            'targets': [0,-1,-2]
        }],
        "order": [], //Initial no order.
        'serverSide': true,//Feature control DataTables' server-side processing mode.
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": SITE_URL+"voucher-code/listing",
            "type": "POST",
            "data": function ( data ) {
                data.channelid = $('#channelid').val();
                data.memberid = $('#memberid').val();
                data.usertype = "1";
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

    $(function () {
        $('.panel-heading.filter-panel').click(function() {
            $(this).find('.button-icon span').html($(this).find('.button-icon span').text() == 'keyboard_arrow_down' ? 'keyboard_arrow_up' : 'keyboard_arrow_down');
            //$(this).children().toggleClass(" ");
            $(this).next().slideToggle({duration: 200});
            $(this).toggleClass('panel-collapsed');

            savecollapse($(this).attr("display-type"),'panel-heading.filter-panel');

            return false;
        });
    });
    var displaytype = $('.panel-heading.filter-panel').attr("display-type");
    if(displaytype==0){

      $('.panel-heading.filter-panel').find('.button-icon span').html($(this).find('.button-icon span').text() == 'keyboard_arrow_down' ? 'keyboard_arrow_up' : 'keyboard_arrow_down');
      //$(this).children().toggleClass(" ");
      $('.panel-heading.filter-panel').next().slideToggle({duration: 200});
      $('.panel-heading.filter-panel').toggleClass('panel-collapsed');
    }

    
    if($("#channelid").val()!="" || $("#channelid").val()!=0){

      getmembers($("#channelid").val());
    }
   
    $("#channelid").change(function(){
        var channelid = $(this).val();
        getmembers(channelid);
    });
});
function applyFilter(){
    oTable.fnDraw();
}

function getmembers(channelid=0){
  $('#memberid')
      .find('option')
      .remove()
      .end()
      .append('')
      .val('whatever')
  ;
  $('#memberid').selectpicker('refresh');

  if(channelid!=0){
    var uurl = SITE_URL+"member/get-multiple-channel-members";
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {channelid:channelid},
      dataType: 'json',
      async: false,
      success: function(response){

        for(var i = 0; i < response.length; i++) {
          
          if(FilterMemberId!=null || FilterMemberId!=''){
            
            FilterMemberId = FilterMemberId.toString().split(',');
            
            if(FilterMemberId.includes(response[i]['id'])){
              $('#memberid').append($('<option>', { 
                value: response[i]['id'],
                selected: "selected",
                text : ucwords(response[i]['name'])
              }));
            }else{
              $('#memberid').append($('<option>', { 
                value: response[i]['id'],
                text : ucwords(response[i]['name'])
              }));
            }
          }
          /* if(memberidarr!=null || memberidarr!=''){
            
            memberidarr = memberidarr.toString().split(',');
            
            if(memberidarr.includes(response[i]['id'])){
              $('#memberid').append($('<option>', { 
                value: response[i]['id'],
                selected: "selected",
                text : ucwords(response[i]['name'])
              }));
            }else{
              $('#memberid').append($('<option>', { 
                value: response[i]['id'],
                text : ucwords(response[i]['name'])
              }));
            }
          } */
          
        }
      },
      error: function(xhr) {
        //alert(xhr.responseText);
      },
    });
    $('#memberid').selectpicker('refresh');
  }
}
function savecollapse(panelcollapsed,cls){
  var uurl = SITE_URL+"voucher-code/savecollapse";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {panelcollapsed:panelcollapsed},
    dataType: 'json',
    success: function(response){
      if(response.panelcollapsed=='1'){
        $("."+cls).attr("display-type","0");
      }else{
        $("."+cls).attr("display-type","1");
      }
    },
    error: function(xhr) {
      //alert(xhr.responseText);
    },
  }); 
}
