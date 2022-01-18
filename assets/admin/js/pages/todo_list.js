
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

  oTable = $('#todolist').DataTable({

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
        "targets": [0,-1,-2],
          "orderable": false
      }],
      "order": [], //Initial no order.
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": SITE_URL+"todo-list/listing",
        "type": "POST",
        "data": function ( data ) {
            data.fromdate = $('#startdate').val();
            data.todate = $('#enddate').val();
            data.filteremployee = $("#userid").val();
            data.filterstatus = $("#statusid").val();
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

function changeliststatus(sts,listid)
{
  //alert(sts);
  swal({title: 'Are you sure to change status?',
      type: "warning",   
      showCancelButton: true,   
      confirmButtonColor: "#DD6B55",   
      confirmButtonText: "Yes, change it!",
      timer: 2000,   
      }, 
      function(isConfirm){
          if (isConfirm) { 
              var uurl = SITE_URL+"todo-list/changestatus";
              $.ajax({
                  url: uurl,
                  type: 'POST',
                  data: {status:sts,id:listid},
                  dataType: 'json',
                  async: false,
                  success: function(response){
                      if(response==1)
                      {
                          if(sts==0){
                              $("#leavestatusdropdown"+listid).text("Pending");
                              $("#leavestatusdropdown"+listid).addClass("btn-dark");                                
                              $("#leavestatusdropdown"+listid).removeClass("btn-success");
                              location.reload();
                          }
                          if(sts==1){
                              $("#leavestatusdropdown"+listid).text("Done");
                              $("#leavestatusdropdown"+listid).addClass("btn-success");
                              $("#leavestatusdropdown"+listid).removeClass("btn-dark");                               
                              location.reload();
                          }                           
                      }
                  },
                  error: function(xhr) {
                      //alert(xhr.responseText);
                  },
              });             
          }
      
  });
}