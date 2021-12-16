
/* $(document).ready(function() {
    
    $('#invoicesettingtable').DataTable({
        "language": {
            "lengthMenu": "_MENU_"
        },
        "columnDefs": [ {
          "targets": [-1,-2,-3,-6],
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
}); */


$(document).ready(function() {

  oTable = $('#invoicesettingtable').DataTable
  ({
    "language": {
      "lengthMenu": "_MENU_"
    },
    "pageLength": 10,
    "columnDefs": [{
      'orderable': false,
      'targets': [0,-1,-2,-3,-4,-6]
    }],
    "order": [], //Initial no order.
    'serverSide': true,//Feature control DataTables' server-side processing mode.
    // Load data for the table's content from an Ajax source
    "ajax": {
      "url": SITE_URL+"invoice-setting/listing",
      "type": "POST",
      "data": function ( data ) {
        data.channelid = $('#channelid').val();
        data.memberid = $('#memberid').val();
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
        return false;
    });
  });

  $("#channelid").change(function(){
    getmembers();
  });
});
function applyFilter(){
oTable.ajax.reload();
}


function getinvoicenotes(id){

    var uurl = SITE_URL+"invoice-setting/getnotesbyid";
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {id:String(id)},
      async: false,
      success: function(response){
        var JSONObject = JSON.parse(response);
        
        $('#pagetitle').html(JSONObject['pagetitle']);
        $('#description').html(JSONObject['description'].replace(/&nbsp;/g, ' '));
      },
      error: function(xhr) {
      //alert(xhr.responseText);
      },
    });
}

function getmembers(){

  $('#memberid')
    .find('option')
    .remove()
    .end()
    .append('<option value="0">All '+Member_label+'</option>')
    .val('whatever')
  ;
  $('#memberid').selectpicker('refresh');
  var channelid = $("#channelid").val();

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

          $('#memberid').append($('<option>', { 
            value: response[i]['id'],
            text : ucwords(response[i]['name'])
          }));

        }
        $('#memberid').selectpicker('refresh');
      },
      error: function(xhr) {
        //alert(xhr.responseText);
      },
    });
  }
}