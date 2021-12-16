$(document).ready(function(){
    $('.yesno input[type="checkbox"]').bootstrapToggle({
        on: 'Yes',
        off: 'No',
        onstyle: 'primary',
        offstyle: 'danger',
        size: 'mini'
    });
    $('#datepicker-range').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayBtn:"linked"
    }).on('changeDate', function(ev){
      if(offertype==1){
        offerparticipantstable.ajax.reload(null, false);
      }else{
        offerorderstable.ajax.reload(null, false);
      }
    });
    $('#status').change(function(){
      offerorderstable.ajax.reload(null, false);
    });
    offerparticipantstable = $('#offerparticipantstable').DataTable
    ({
      "language": {
        "lengthMenu": "_MENU_"
      },
      "pageLength": 10,
      "columnDefs": [{
        'orderable': false,
        'targets': [0,3,4,-1]
      }],
      "order": [], //Initial no order.
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": SITE_URL+"offer/offerparticipantslisting",
        "type": "POST",
        "data": function ( data ) {
          data.startdate = $('#startdate').val();
          data.enddate = $('#enddate').val();
          data.offerid = $('#offerid').val();
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
    offerorderstable = $('#offerorderstable').DataTable
    ({
      "language": {
        "lengthMenu": "_MENU_"
      },
      "pageLength": 10,
      "columnDefs": [{
        'orderable': false,
        'targets': [0,-2,-3]
      },{ targets: [-1], className: "text-right" }],
      "order": [], //Initial no order.
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": SITE_URL+"offer/offerorderslisting",
        "type": "POST",
        "data": function ( data ) {
          data.startdate = $('#startdate').val();
          data.enddate = $('#enddate').val();
          data.status = $('#status').val();
          data.offerid = $('#offerid').val();
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

    $('.offerparticipants .dataTables_filter input').attr('placeholder','Search...');

    //DOM Manipulation to move datatable elements integrate to panel
    $('.offerparticipants .panel-ctrls').append($('.dataTables_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.offerparticipants .panel-ctrls').append("<i class='separator'></i>");
    $('.offerparticipants .panel-ctrls').append($('.dataTables_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('.offerparticipants .panel-footer').append($(".dataTable+.row"));
    $('.offerparticipants .dataTables_paginate>ul.pagination').addClass("pull-right pagination-md");

});
function viewnotes(offerid,type){

  if(type==1){
    var description = $("#adminnotes"+offerid).html();
    $("#notesModal .modal-title").html("Admin Notes");
  }else{
    var description = $("#membernotes"+offerid).html();
    $("#notesModal .modal-title").html(Member_label+" Notes");
  }
  $('#description').html(description.replace(/&nbsp;/g, ' '));
}

function editoffernotes(id){
  $('#offerparticipantsid').val(id);
  $('#adminnotes').val($("#adminnotes"+id).html());
  $('#membernotes').val($("#membernotes"+id).html());
}
function updateoffernotes(){

  PNotify.removeAll();
  var formData = new FormData($('#editnotesform')[0]);
      
  var uurl = SITE_URL+"offer/update-offer-notes";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: formData,
    //async: false,
    beforeSend: function(){
        // $('.mask').show();
        // $('#loader').show();
    },
    success: function(response){
      
      if(response==1){
        new PNotify({title: 'Offer notes successfully updated !',styling: 'fontawesome',delay: '3000',type: 'success'});
        $("#editnotesModal").modal("hide");
        offerparticipantstable.ajax.reload(null, false);
      }else{
          new PNotify({title: 'Offer notes not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
      }
    },
    error: function(xhr) {
    //alert(xhr.responseText);
    },
    complete: function(){
        // $('.mask').hide();
        // $('#loader').hide();
    },
    cache: false,
    contentType: false,
    processData: false
  });
}