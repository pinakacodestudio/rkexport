$(document).ready(function(){
  $('[data-toggle="popover"]').popover({"html": true,trigger: "hover"});
  $('#datepicker-range').datepicker({
      todayHighlight: true,
      format: 'dd/mm/yyyy',
      autoclose: true,
      todayBtn:"linked",
      orientation:"top"
  });

  $("#datefilterbtn").click(function(){
    refreshhighcharts();
  })

  refreshhighcharts();  
  //alert("hello");
  // getcustomercount(1,"customer");
  // getcustomercount(6,"inquiry");
  // getcustomercount(6,"followup");
  // getcustomercount(1,"product");
  // getcustomercount(1,"download");
  // getcustomercount(1,"cinquiry");
  // getcustomercount(1,"cservice");
  // getcustomercount(1,"cserviceopen");
})

function refreshhighcharts() {

    // if(loadpage!=0){

      fromdate = $("#fromdate").val();
      todate = $("#todate").val();

      var uurl = SITE_URL+"crm-dashboard/dashboard-process";
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {fromdate:fromdate,todate:todate},
        async: false,
        success: function(response){
          var JSONObject = JSON.parse(response);
          mychartdata = JSONObject['memberchart'];
          myfollowupchartdata = JSONObject['followupchart'];
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
      });
    // }
    
    Highcharts.chart('container', {
      chart: {
          type: 'column'
      },
       credits: {
            text: 'STATUS',
            position: {
                align: 'center',
                // y: 5
            },
            style: {
                fontSize: '10pt',
            }
        },
      title: {
          text: 'Members Division Graph'
      },
      subtitle: {
          text: ''
      },
      colors: ['#a2d200','#ff4a43', '#22beef','#ffc100','#cd97eb','#a96868','#e46737'],
      xAxis: {
          type: 'category'
      },
      yAxis: {
          title: {
              text: 'Number of Members'
          }
      },
      legend: {
          enabled: false
      },
      plotOptions: {
          series: {
              borderWidth: 0,
              dataLabels: {
                  enabled: true,
                  format: '{point.y}'
              }
          }
      },

      tooltip: {
          headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
          pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b> of total<br/>'
      },

      "series": [
          {
              "name": "Member Status",
              "colorByPoint": true,
              "data": mychartdata
          }
      ]
  });


    Highcharts.chart('container1', {
      chart: {
          type: 'column'
      },
      credits: {
          text: 'STATUS',
          position: {
              align: 'center',
              // y: 5
          },
          style: {
              fontSize: '10pt',
          }
      },
      title: {
          text: Follow_up_label+' Division Graph'
      },
      subtitle: {
          text: ''
      },
      xAxis: {
          type: 'category'
      },
      yAxis: {
          title: {
              text: 'Number of '+Follow_up_label
          }

      },
      legend: {
          enabled: false
      },
      colors: ['#a2d200','#22beef', '#ff4a43','#ffc100','#cd97eb'],
      plotOptions: {
          series: {
              borderWidth: 0,
              dataLabels: {
                  enabled: true,
                  format: '{point.y}'
              }
          }
      },

      tooltip: {
          headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
          pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b> of total<br/>'
      },

      "series": [
          {
              "name": Follow_up_label,
              "colorByPoint": true,
              "data": myfollowupchartdata
          }
      ]
  });
}

function getdashboarddetail(){
 
}

function getcounts(duration,counttype){
  // alert();
  var uurl = SITE_URL+"crm-dashboard/getcounts";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {duration:duration,counttype:counttype},
    async: false,
    success: function(response){
        //alert(response);
        
      if(counttype=="member"){
        // alert(counttype);
        $('#membercount').html(response);
      }else if(counttype=="inquiry"){
        $('#inquirycount').html(response);
      }else if(counttype=="followup"){
        $('#followupcount').html(response);
      }else if(counttype=="product"){
        $('#productcount').html(response);
      }
      $("."+counttype+"dd").removeClass("active");
      $("#"+counttype+"dd"+duration).addClass("active");
    },
    error: function(xhr) {
    //alert(xhr.responseText);
    },
  });
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

$('#insert_form').on("submit", function(event){             
  event.preventDefault();  
  if($('#todolist').val() == ""){  
    $("#todolist_div").addClass("has-error is-focused");
    new PNotify({title: "Please enter your to do list",styling: 'fontawesome',delay: '3000',type: 'error'});      
  }else{
    $.ajax({  
      url:SITE_URL+"todo-list/addtodolistbypopup",  
      method:"POST",  
      data:$('#insert_form').serialize(),  
      beforeSend:function(){  
        $('#insert').val("Adding...");  
      },  
      success:function(data){  
        $('#add_data_Modal').modal('hide');  
        if(data==1){
          new PNotify({title: "To Do List successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
          setTimeout(function() { window.location=SITE_URL+"crm-dashboard"; }, 1500);
        }else{
          new PNotify({title: "To Do List not added !",styling: 'fontawesome',delay: '3000',type: 'error'});
        }
      }  
    });  
  }  
});

$(document).on('click', '.edit_data', function(){  
  var tdlid = $(this).attr("id");    
  $.ajax({  
    url:SITE_URL+"todo-list/get_todo_list_by_id",  
    method:"POST",  
    data:{tdlid:tdlid},  
    dataType:"json",  
    success:function(data){     
      $('#todolist1').val(data.list);          
      $('#tdlid').val(data.id);        
      $('#edit_data_Modal').modal('show');  
    }   
  });  
});

$('#update_form').on("submit", function(event){             
  event.preventDefault();  
  if($('#todolist1').val() == ""){  
    $("#todolist1_div").addClass("has-error is-focused");
    new PNotify({title: "Please enter your to do list",styling: 'fontawesome',delay: '3000',type: 'error'});      
  }else{
    $.ajax({  
      url:SITE_URL+"todo-list/updatetodolistbypopup",  
      method:"POST",  
      data:$('#update_form').serialize(),  
      beforeSend:function(){  
        $('#update').val("Updating...");  
      },  
      success:function(data){  
        $('#edit_data_Modal').modal('hide');  
        if(data==1){
          new PNotify({title: "To Do List successfully updated.",styling: 'fontawesome',delay: '3000',type: 'success'});
          setTimeout(function() { window.location=SITE_URL+"crm-dashboard"; }, 1500);
        }else{
          new PNotify({title: "To Do List not updated !",styling: 'fontawesome',delay: '3000',type: 'error'});
        }
      }  
    });  
  }  
});