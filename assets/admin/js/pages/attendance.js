var table;
$(document).ready(function() {

  $('#datepicker-range').datepicker({
    todayHighlight: true,
    format: 'dd/mm/yyyy',
    todayBtn:"linked",
    // orientation:"bottom",
    autoclose: true,
  });   
  
  var fromdate = $('#fromdate').val();
  var todate = $('#todate').val(); 
  var employee = $("#employee").val();        
  
  table = $('#attendancetable').DataTable({
        "processing": true,//Feature control the processing indicator.       
        "language": {
            "lengthMenu": "_MENU_"
        },
        drawCallback: function () {
            loadpopover();
        }, 
        footerCallback: function ( row, data, start, end, display ) {
          var api = this.api(), data;

          var intVal = function ( i ) {
            return i != null ? moment.duration(i).asSeconds() : 0;
          };

          pageTotal = api
              .column( 6, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                total = intVal(a) + intVal(b);
                totalFormatted = [
                  parseInt(total / 60 / 60),
                  parseInt(total / 60 % 60),
                  parseInt(total % 60)
              ].join(":").replace(/\b(\d)\b/g, "0$1");
              return totalFormatted;
              }, 0 );
            if(pageTotal == 0){
              pageTotal = "00:00:00";
            }else{
              pageTotal = pageTotal;
            }

          // Update footer
          $( api.column( 5 ).footer() ).html(
              'Total Time '
          ); 
          $( api.column( 6 ).footer() ).html(
            pageTotal
        );            
      },      
        "columnDefs": [{
          "targets": [0,-1,-4],
          "orderable": false
        }],
        "responsive": true,
        "serverSide": true,//Feature control DataTables' server-side processing mode.
        "ajax": {
          "url": SITE_URL+"attendance/listing",
          "type": "POST",
          "data" :function ( data ) {
            data.employee = $("#employee").val();               
            data.fromdate = $('#fromdate').val();
            data.todate = $('#todate').val();   
          },
          //"data" :"fromdate="+fromdate+"&todate="+todate+"&employee="+employee,
          "dataType":"json",
          beforeSend: function(){
            $('.mask').show();
            $('#loader').show();
          }, 
         /* success:function(data){
            $('#totaltime').html("Total Time : "+ data.totaltime);
          },    */      
          complete: function(){
            $('.mask').hide();
            $('#loader').hide();
          },
        }
    });
    
    /* $('.table').on('mouseover', function(e){
        if($('.popoverButton').length>1){
          $('.popoverButton').popover('hide');
          $(e.target).popover('toggle');
        }
    }); */

    $('.dataTables_filter input').attr('placeholder','Search...');
    
    //DOM Manipulation to move datatable elements integrate to panel
    $('.panel-ctrls').append($('.dataTables_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls').append("<i class='separator'></i>");
    $('.panel-ctrls').append($('.dataTables_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    $('.panel-footer').append($(".dataTable+.row"));
    $('.dataTables_paginate>ul.pagination').addClass("pull-right");

    $("#applyfilterbtn").click(function()
    {      
      table.ajax.reload(null,false);
    })

    $("#showbtn").click(function()
    {      
      table.ajax.reload(null,false);
    })

});

function employeecheckinstatus(){
  swal({title: 'Are you sure want to checkin?',
  type: "warning",   
  showCancelButton: true,   
  confirmButtonColor: "#DD6B55",   
  confirmButtonText: "Yes",
  timer: 2000,   
  }, 
  function(isConfirm){
    if (isConfirm) {   
      var baseurl = SITE_URL+'attendance/addattendance';
             $.ajax({
                 url: baseurl,                
                 type: 'POST',                   
                 success: function(response){                                     
                  if(response==1)
                  {
                    location.reload();   
                    /* $('#btncheckout').show();
                    $('#btncheckin').hide();
                    $('#btnrecheckin').hide();
                    $('#btnbreakout').show();
                    $('#btnnonattendanceout').show();         */            
                  }else{
                    location.reload();   
                   /*  $('#btncheckin').show();
                    $('#btncheckout').hide();
                    $('#btnrecheckin').hide();
                    $('#btnbreakout').show();
                    $('#btnnonattendanceout').show();    */                 
                  }
              },
            });
           
    }
  });
}

function employeecheckoutstatus(){
  swal({title: 'Are you sure want to checkout?',
  type: "warning",   
  showCancelButton: true,   
  confirmButtonColor: "#DD6B55",   
  confirmButtonText: "Yes",
  timer: 2000,   
  }, 
  function(isConfirm){
    if (isConfirm) {   
      var baseurl = SITE_URL+'attendance/addattendance';
             $.ajax({
                 url: baseurl,                
                 type: 'POST',                   
                 success: function(response){
                  if(response==1)
                  {
                    location.reload();                    
                    /* $('#btncheckin').hide();
                    $('#btncheckout').hide();
                    $('#btnrecheckin').show(); 
                    $('#btnbreakout').hide();
                    $('#btnnonattendanceout').hide();    */                     
                  }else{
                    location.reload();
                   /*  $('#btncheckout').show();
                    $('#btncheckin').hide();
                    $('#btnrecheckin').hide(); 
                    $('#btnbreakout').hide();
                    $('#btnnonattendanceout').hide();     */                 
                  }
              },
            });
           
    }
  });
}

function employeerecheckinstatus(){
    swal({
        title: 'Are you sure want to re-checkin?',
        type: "warning",   
        showCancelButton: true,   
        confirmButtonColor: "#DD6B55",   
        confirmButtonText: "Yes",
        timer: 2000,   
    }, 
    function(isConfirm){
        if (isConfirm) {     
            var baseurl = SITE_URL+'attendance/readdattendance';
            $.ajax({
                url: baseurl,                
                type: 'POST',                   
                success: function(response){
                    if(response==1)
                    {
                        location.reload();
                    /*  $('#btncheckout').show();
                        $('#btncheckin').hide();
                        $('#btnrecheckin').hide();   */                 
                    }else{
                        location.reload();
                    /*  $('#btncheckin').show();
                        $('#btncheckout').hide();
                        $('#btnrecheckin').hide(); */
                    }
                },
            });
         }
    });
}

function employeebreakinstatus(){
  swal({title: 'Are you sure want to breakin?',
  type: "warning",   
  showCancelButton: true,   
  confirmButtonColor: "#DD6B55",   
  confirmButtonText: "Yes",
  timer: 2000,   
  }, 
  function(isConfirm){
    if (isConfirm) {   
      var baseurl = SITE_URL+'attendance/addbreaktime';
             $.ajax({
                 url: baseurl,                
                 type: 'POST',                   
                 success: function(response){  
                  //alert(response);                                   
                  if(response==1)
                  {
                    location.reload();   
                    /* $('#btncheckout').show();
                    $('#btncheckin').hide();
                    $('#btnrecheckin').hide();
                    $('#btnbreakout').show();
                    $('#btnnonattendanceout').show();         */            
                  }else{
                    location.reload();   
                   /*  $('#btncheckin').show();
                    $('#btncheckout').hide();
                    $('#btnrecheckin').hide();
                    $('#btnbreakout').show();
                    $('#btnnonattendanceout').show();    */                 
                  }
              },
            });
           
    }
  });  
}

function employeebreakoutstatus(attendanceid){
  swal({title: 'Are you sure want to breakout?',
  type: "warning",   
  showCancelButton: true,   
  confirmButtonColor: "#DD6B55",   
  confirmButtonText: "Yes",
  timer: 2000,   
  }, 
  function(isConfirm){
    if (isConfirm) {   
      var baseurl = SITE_URL+'attendance/addbreaktime';
             $.ajax({
                 url: baseurl,                
                 type: 'POST', 
                 data : {attendanceid:attendanceid},                  
                 success: function(response){  
                  //alert(response);                                   
                  if(response==1)
                  {
                    location.reload();   
                    /* $('#btncheckout').show();
                    $('#btncheckin').hide();
                    $('#btnrecheckin').hide();
                    $('#btnbreakout').show();
                    $('#btnnonattendanceout').show();         */            
                  }else{
                    location.reload();   
                   /*  $('#btncheckin').show();
                    $('#btncheckout').hide();
                    $('#btnrecheckin').hide();
                    $('#btnbreakout').show();
                    $('#btnnonattendanceout').show();    */                 
                  }
              },
            });
           
    }
  });
}

function employeenonattendanceoutstatus(attendanceid){
  swal({title: 'Are you sure want to non-attendance out?',
  type: "warning",   
  showCancelButton: true,   
  confirmButtonColor: "#DD6B55",   
  confirmButtonText: "Yes",
  timer: 2000,   
  }, 
  function(isConfirm){
    if (isConfirm) {         
      var baseurl = SITE_URL+'attendance/addnonattendancetime';
             $.ajax({
                 url: baseurl,                
                 type: 'POST', 
                 data : {attendanceid:attendanceid},                 
                 success: function(response){  
                  //alert(response);                                   
                  if(response==1)
                  {
                    location.reload();   
                    /* $('#btncheckout').show();
                    $('#btncheckin').hide();
                    $('#btnrecheckin').hide();
                    $('#btnbreakout').show();
                    $('#btnnonattendanceout').show();         */            
                  }else{
                    location.reload();   
                   /*  $('#btncheckin').show();
                    $('#btncheckout').hide();
                    $('#btnrecheckin').hide();
                    $('#btnbreakout').show();
                    $('#btnnonattendanceout').show();    */                 
                  }
              },
            });
           
    }
  });
}

function employeenonattendanceinstatus(){
  swal({title: 'Are you sure want to non-attendance in?',
  type: "warning",   
  showCancelButton: true,   
  confirmButtonColor: "#DD6B55",   
  confirmButtonText: "Yes",
  timer: 2000,   
  }, 
  function(isConfirm){
    if (isConfirm) {   
      var baseurl = SITE_URL+'attendance/updatenonattendancetime';
             $.ajax({
                 url: baseurl,                
                 type: 'POST',                   
                 success: function(response){  
                  //alert(response);                                   
                  if(response==1)
                  {
                    location.reload();   
                    /* $('#btncheckout').show();
                    $('#btncheckin').hide();
                    $('#btnrecheckin').hide();
                    $('#btnbreakout').show();
                    $('#btnnonattendanceout').show();         */            
                  }else{
                    location.reload();   
                   /*  $('#btncheckin').show();
                    $('#btncheckout').hide();
                    $('#btnrecheckin').hide();
                    $('#btnbreakout').show();
                    $('#btnnonattendanceout').show();    */                 
                  }
              },
            });
           
    }
  });
}

