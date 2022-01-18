
$(document).ready(function() {
  
  $('#startdate').datepicker({
    todayHighlight: true,
    format: 'dd/mm/yyyy',
    todayBtn:"linked",
  });

  $('#enddate').datepicker({
    todayHighlight: true,
    format: 'dd/mm/yyyy',
    todayBtn:"linked",
  });

  oTable = $('#leavetable').DataTable({

      "processing": true,//Feature control the processing indicator.
      "language": {
        "lengthMenu": "_MENU_"
      },
      drawCallback: function () {
        loadpopover();
      },
      "pageLength": 10,
      /* "scrollCollapse": true,
      "scrollY": "500px", */
      "columnDefs": [{
        'orderable': false,
        'targets': [0,3,6,-1,-2,-3]
      }],
      "order": [], //Initial no order.
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": SITE_URL+"Leave/listing",
        "type": "POST",
        "data": function ( data ) {
          data.statusid = $("#statusid").val();
          data.userid = $("#userid").val();
          data.startdate = $("#startdate").val();
          data.enddate = $("#enddate").val();
        },
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
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

  $('#update').on("click", function(event){     
    var reason = $('#remarks').val();
    // alert();
    if(reason == "")
    {
      $("#remark_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter remark !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }else{
      // event.preventDefault();  
      $.ajax({  
        url: SITE_URL+'leave/changestatus',  
        method:"POST",  
        data:$('#insert_form').serialize(),  
        beforeSend:function(){  
              $('#update').val("Updating...");  
        },   
        success:function(data){  
              $('#remark_data_Modal').modal('hide');  
              var baseurl = SITE_URL+'leave/insertapproved';
                    $.ajax({
                        url: baseurl,                
                        type: 'POST', 
                        data : $('#insert_form').serialize(),                       
                        success: function(response){                                                
                          if(response==1)
                          {
                            new PNotify({title: "Leave successfully decline.",styling: 'fontawesome',delay: '3000',type: 'success'});
                          }
                          setTimeout(function() { window.location=SITE_URL+"leave"; }, 1500);
                      },
                    })
        }  
      }); 
    } 
  });
  
});

function applyFilter(){
oTable.ajax.reload();
}

function changestatus(granted,leaveid,employeeid){
  if(granted == true){
    swal({title: 'Are you sure want to Approved?',
    type: "warning",   
    showCancelButton: true,   
    confirmButtonColor: "#DD6B55",   
    confirmButtonText: "Yes",
    timer: 2000,   
    }, 
    function(isConfirm){
      if (isConfirm) {   
        var baseurl = SITE_URL+'leave/insertapproved';
              $.ajax({
                  url: baseurl,                
                  type: 'POST', 
                  data : {granted:granted,id:leaveid,employeeid:employeeid},                       
                  success: function(response){                                                
                    if(response==1)
                    {
                      new PNotify({title: "Leave successfully approved.",styling: 'fontawesome',delay: '3000',type: 'success'});
                      location.reload();
                      
                    }else{
                      new PNotify({title: 'Leave not approved !',styling: 'fontawesome',delay: '3000',type: 'error'});
                      table.api().ajax.reload(null,false);
                      location.reload();               
                    }
                },
              });
            
      }
    });  
  }else{
    swal({title: 'Are you sure want to Decline?',
    type: "warning",   
    showCancelButton: true,   
    confirmButtonColor: "#DD6B55",   
    confirmButtonText: "Yes",
    timer: 2000,   
    }, 
    function(isConfirm){
      if (isConfirm) {
        $('#id').val(leaveid);
        $('#granted').val(granted);
        $('#employeeid').val(employeeid);
        $('#remark_data_Modal').modal('show');
      }
    });  
  }
}

$("#changeleave").on("change",function(){
  if($("#changeleave").val() == 1){
    $("#reason1_div").show();
  }else{
    $("#reason1_div").hide();
  }
})

function loadpaidunpaidmodal(id,paidunpaid){
  $('.popoverButton').popover('hide');
  $("#myModal2").modal('show');
  $("#previousleaveis").val(paidunpaid);
  $("#changeleave").val(paidunpaid);
  $("#leaveid").val(id);
  $("#changeleave").selectpicker("refresh");
  if($("#changeleave").val() == 1){
    $("#reason1_div").show();
  }else{
    $("#reason1_div").hide();
  }
}

function submitchangeleave(){
  var leaveid = $("#leaveid").val();
  var previousleave = $("#previousleaveis").val();
  var selectedleave = $("#changeleave").val();
  var isvalidreason = 0;
  var reason = $.trim(CKEDITOR.instances['reason'].getData());
  reason = encodeURIComponent(reason);
  CKEDITOR.instances['reason'].updateElement();
  if(previousleave == selectedleave){
    $("#errormsg").show();
  }
  else{
    $("#errormsg").hide();
    if(selectedleave == 1){
      if (reason == '') {
          $("#reason1_div").addClass("has-error is-focused");
          new PNotify({
              title: 'Please enter reason !',
              styling: 'fontawesome',
              delay: '3000',
              type: 'error'
          });
          isvalidreason = 0;
      } else {
          if (reason.length < 2) {
              $("#reason1_div").addClass("has-error is-focused");
              new PNotify({
                  title: 'Reason require minimum 2 characters !',
                  styling: 'fontawesome',
                  delay: '3000',
                  type: 'error'
              });
              isvalidreason = 0;
          } else {
              isvalidreason = 1;
              $("#reason1_div").removeClass("has-error is-focused");
          }
      }
    }
    if(isvalidreason == 1 || selectedleave == 0){
      var formData = new FormData($('#paidunpaid_form')[0]);
      $.ajax({
        url: SITE_URL+"leave/addpaidunpaidleave",
        type: "POST",
        data: formData,
        beforeSend: function() {
          $('.mask').show();
          $('#loader').show();
        },
        success: function(response) {
          if (response == 1) {
              new PNotify({ title: "Leave Type change successfully updated.", styling: 'fontawesome', delay: '3000', type: 'success' });
              $("#myModal2").modal("hide");
              setTimeout(function() { window.location=SITE_URL+"leave"; }, 1500);
              CKEDITOR.instances['reason'].setData('');
          } else {
              new PNotify({ title: 'Leave Type  not updated !', styling: 'fontawesome', delay: '3000', type: 'error' });
          }
        },
        error: function(xhr) {
            //alert(xhr.responseText);
        },
        complete: function() {
            $('.mask').hide();
            $('#loader').hide();
        },
        cache: false,
        contentType: false,
        processData: false
      })
    }
  }
}
