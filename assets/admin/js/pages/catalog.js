$(document).ready(function() {
    //list('catalog', 'catalog/listing', [0,-1,-2]);

    if($("#channelid").val() > 0){
      getmembers($("#channelid").val());
    }
    oTable = $('#catalog').dataTable
    ({
      "language": {
        "lengthMenu": "_MENU_"
      },
      "pageLength": 10,
      "columnDefs": [{
        'orderable': false,
        'targets': [0,-1,-2]
      }],
      "order": [], //Initial no order.
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": SITE_URL+"catalog/listing",
        "type": "POST",
        "data": function ( data ) {
          data.startdate = $('#startdate').val();
          data.enddate = $('#enddate').val();
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
});

$("#channelid").change(function(){
  var channelid = $(this).val();
  $('#memberid')
      .find('option')
      .remove()
      .end()
      .append('<option value="">All '+Member_label+'</option>')
      .val('whatever')
      ;
  if(channelid!='' && channelid!=0){
    getmembers(channelid);
  }
  $('#memberid').selectpicker('refresh');
})

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
      if(FilterMemberId!=0){
        $("#memberid").val(FilterMemberId);
      }
      // $('#product'+prow).val(areaid);
      $('#memberid').selectpicker('refresh');
    },
    error: function(xhr) {
      //alert(xhr.responseText);
    },
  });
}

function getcontent(id){

  var uurl = SITE_URL+"catalog/getcontentbyid";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {id:String(id)},
    async: false,
    success: function(response){
      var JSONObject = JSON.parse(response);

      $('#id').html(JSONObject['id']);
      $('#catalogname').html(JSONObject['catalogname']);
      $('#catalogdescription').html(JSONObject['catalogdescription']);
      $('#catalogimage').attr("src",JSONObject['catalogimage']); 
      $('#catalogpdffile').attr("href",JSONObject['catalogpdffile']); 
      $('#catalogcreateddate').html(JSONObject['catalogcreateddate']);

      if(JSONObject['catalogstatus']=="1")
      {
      	$('#catalogstatus').removeClass("btn-danger");
      	$('#catalogstatus').addClass("btn-success");
      	$('#catalogstatus').html("Active");
      }
      else
      {
      	$('#catalogstatus').removeClass("btn-success");
      	$('#catalogstatus').addClass("btn-danger");
      	$('#catalogstatus').html("Inactive");
      }

      
      
    },
    error: function(xhr) {
    //alert(xhr.responseText);
    },
  });
}
function savecollapse(panelcollapsed,cls){
  var uurl = SITE_URL+"catalog/savecollapse";
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