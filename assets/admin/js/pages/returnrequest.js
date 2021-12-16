
$(document).ready(function() {

    //list("paymenttransactiontable","paymenttransaction/listing",[0]);
    oTable = $('#returnrequesttable').DataTable
      ({
        "processing": true,//Feature control the processing indicator.
        "language": {
          "lengthMenu": "_MENU_"
        },
        "pageLength": 50,
        "columnDefs": [{
          'orderable': false,
          'targets': [3,0,-1]
        }],
        "order": [], //Initial no order.
        'serverSide': true,//Feature control DataTables' server-side processing mode.
        // Load data for the table's content from an Ajax source
        "ajax": {
          	"url": SITE_URL+'returnrequest/listing',
          	"type": "POST",
          	"data": function ( data ) {
                data.customerid = $('#customerid').val();
                data.fromdate = $('#fromdate').val();
                data.todate = $('#todate').val();
            }
        },
      });
    $('.dataTables_filter input').attr('placeholder','Search...');


    //DOM Manipulation to move datatable elements integrate to panel
    $('.panel-ctrls').append($('.dataTables_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls').append("<i class='separator'></i>");
    $('.panel-ctrls').append($('.dataTables_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('.panel-footer').append($(".dataTable+.row"));
    $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-md");

    $('#datepicker-range').datepicker({
      todayHighlight: true,
      format: 'yyyy-mm-dd'
  	}).on("change", function() {
    	oTable.ajax.reload(null,false);
  	});

});
$('#customerid').on('change', function (e) {
  oTable.ajax.reload(null,false);
});
function cancelrequest(id){
  swal({    
      title: "Are you sure want to cancel order return request?",
      type: "warning",   
      showCancelButton: true,   
      confirmButtonColor: "#DD6B55",   
      confirmButtonText: "Yes, cancel it!",   
      closeOnConfirm: false }, 
      function(isConfirm){  
        if (isConfirm) {
          $.ajax({
            url: SITE_URL+'returnrequest/cancelrequest',
            type: 'POST',
            data: {id:id},
            beforeSend: function(){
              $('.mask').show();
              $('#loader').show();
            },
            success: function(data){
              location.reload();
            },
            complete: function(){
              $('.mask').hide();
              $('#loader').hide();
            },
          });
        }
      });
}
function viewmessage(returnrequestid){
  
  PNotify.removeAll();
  var uurl = SITE_URL+"returnrequest/getreturnrequestmessage";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {id:returnrequestid},
    dataType:'json',
    //async: false,
    beforeSend: function(){
      $('.mask').show();
      $('#loader').show();
    },
    success: function(response){
      
      if(response.length>0){
        $('#returnrequestid').val(returnrequestid);
        $.html = '';
        $('#myModal').modal('show');
        for (var i = 0; i < response.length; i++) {
          
          if(response[i]['customerid']!='' && response[i]['customerid']!=0){
            $.html += '<article class="row"> \
                        <div class="col-md-10 col-sm-10"> \
                          <div class="panel panel-default arrow left"> \
                            <div class="panel-body"> \
                              <header class="text-left"> \
                                <div class="mb-sm"> \
                                  <i class="fa fa-user"></i> '+response[i]["customername"]+' \
                                </div> \
                              </header> \
                              <div class="mb-sm"> \
                                <p>'+response[i]["message"]+'</p> \
                              </div> \
                              <div class="text-right">  '+response[i]["createddate"]+'\
                              </div> \
                            </div> \
                          </div> \
                        </div> \
                      </article>';
          }else if(response[i]['addedby']!='' && response[i]['addedby']!=0){
            $.html += '<article class="row"> \
                        <div class="col-md-offset-2 col-md-10 col-sm-10"> \
                          <div class="panel panel-default arrow right"> \
                            <div class="panel-body"> \
                              <header class="text-right"> \
                                <div class="mb-sm"> \
                                  <i class="fa fa-user"></i> '+response[i]["admin"]+' \
                                </div> \
                              </header> \
                              <div class="mb-sm text-right"> \
                                <p>'+response[i]["message"]+'</p> \
                              </div> \
                              <div class="text-right">  '+response[i]["createddate"]+'\
                            </div> \
                          </div> \
                        </div> \
                     </article>';
          }
          
        }
        $('.modal-body').html($.html);
      }else{
        new PNotify({title: 'No message found !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
}
function sendmessage(){
  var returnrequestid = $('#returnrequestid').val();
  var message = $('#message').val();

  var isvalidmessage = 0;

  PNotify.removeAll();
  if(message == ''){
    $("#message_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter message !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidmessage = 0;
  }else {
    if(message.length<3){
      $("#message_div").addClass("has-error is-focused");
      new PNotify({title: 'Message require minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidmessage = 0;
    }else{
      isvalidmessage = 1;
    }
  }

  if(isvalidmessage==1){
    var uurl = SITE_URL+"returnrequest/sendmessage";
      
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {id:returnrequestid,message:message},
      //async: false,
      beforeSend: function(){
        $('.mask').show();
        $('#loader').show();
      },
      success: function(response){
        if(response==1){
          new PNotify({title: "Message successfully send.",styling: 'fontawesome',delay: '3000',type: 'success'});
          setTimeout(function() { window.location.reload(); }, 1500);
        }else{
          new PNotify({title: 'Message not send !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
  }
}