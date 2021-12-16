$(document).ready(function() {
    
    oTable = $('#cashbackoffertable').DataTable
    ({
      "language": {
        "lengthMenu": "_MENU_"
      },
      "pageLength": 10,
      "columnDefs": [{
        'orderable': false,
        'targets': [0,-3,-1,-2]
      }],
      "order": [], //Initial no order.
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": SITE_URL+"cashback-offer/listing",
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
  oTable.ajax.reload(null, false);
}
function getmembers(type=0){
  
  var memberelement = $("#memberid");
  var channelelement = $("#channelid");

  if(type==1){
    memberelement = $("#sellermemberid");
    channelelement = $("#sellerchannelid");
  }
  memberelement.find('option')
              .remove()
              .end()
              .val('0')
              .append('<option value="0">Select '+Member_label+'</option>')
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
function viewdescription(id){

  var uurl = SITE_URL+"cashback-offer/viewcashbackofferdescription";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {id:String(id)},
    async: false,
    success: function(response){
      var JSONObject = JSON.parse(response);
      
      $('#description').html(JSONObject['description'].replace(/&nbsp;/g, ' '));
    },
    error: function(xhr) {
    //alert(xhr.responseText);
    },
  });
}

function viewmemberlist(id,channelid){

  var uurl = SITE_URL+"cashback-offer/viewmemberlist";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {id:String(id),channelid:String(channelid)},
    async: false,
    success: function(response){
      var JSONObject = JSON.parse(response);
      if(JSONObject.length > 0){
        var html = "";
        for(i=0;i<JSONObject.length;i++) {
        
            html += "<tr>";
            html += "<td>"+(i+1)+"</td>";
            html += "<td><a href='"+SITE_URL+'member/member-detail/'+JSONObject[i]['id']+"' target='_blank'>"+ucwords(JSONObject[i]['name'])+"</a></td>";
            html += "<td>"+(JSONObject[i]['membercode']!=""?JSONObject[i]['membercode']:"-")+"</td>";
            html += "</tr>";

        }
        $('#memberdata').html(html);
      }
    },
    error: function(xhr) {
    //alert(xhr.responseText);
    },
  });
}