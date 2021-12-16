$(document).ready(function(){
 
  if(size>STORAGESPACE){
      getSizemodal();
  }

  $('#datepicker-range').datepicker({
      todayHighlight: true,
      format: 'dd/mm/yyyy',
      autoclose: true,
      todayBtn: "linked"
  });
  $('#datepicker-range1').datepicker({
      todayHighlight: true,
      format: 'dd/mm/yyyy',
      autoclose: true,
      todayBtn: "linked"
  });

  $("#datefilterbtn").click(function(){
    refreshsaleshighcharts();
    refreshsaleschartbox();//chartbox
  })
  $("#datefilterbtn1").click(function(){
    refreshhighcharts();
    refreshorderschartbox();//chartbox
  })


  //charts
  if(totalsaleschart==1){
    Salescharts(saleschartdata,saleschartdrilldata);
  }
  if(totalorderchart==1){
    Orderscharts(orderchartdata,orderchartdrilldata);
  }

  //refreshhighcharts();
  //refreshsaleshighcharts();
  //getcounts(5,"customer");
  //getcounts(5,"member");
  //getcounts(5,"product");
  //getcounts(1,"ordercompleted");
  //getcounts(1,"ordercancelled");
  //getcounts(1,"totalsales");
  //getcounts(1,"quotation");
});

function getcounts(duration,counttype){
  var uurl = SITE_URL+"dashboard/getcounts";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {duration:duration,counttype:counttype},
    async: false,
    success: function(response){
      if(counttype=="customer"){
        $('#customercount').html(response);
      }else if(counttype=="dealer"){
        $('#dealercount').html(response);
      }else if(counttype=="member"){
        $('#membercount').html(response);
      }else if(counttype=="product"){
        $('#productcount').html(response);
      }else if(counttype=="ordercompleted"){
        $('#ordercompletedcount').html(response);
      }else if(counttype=="ordercancelled"){
        $('#ordercancelledcount').html(response);
      }else if(counttype=="totalsales"){
        $('#totalsalescount').html(response);
      }else if(counttype=="quotation"){
        $('#quotationcount').html(response);
      }
      $("."+counttype+"dd").removeClass("active");
      $("#"+counttype+"dd"+duration).addClass("active");
    },
    error: function(xhr) {
    //alert(xhr.responseText);
    },
  });
}


//Chart Box
function refreshsaleschartbox(){
    var uurl = SITE_URL+"dashboard/getsaleschartbox";
    var startdate = $('#fromdate').val();
    var enddate = $('#todate').val();
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {startdate:startdate,enddate:enddate},
      async: false,
      success: function(response){
        var JSONObject = JSON.parse(response);
        $('#salestotalchartbox').html(JSONObject['salescount']);
        $('#salesaveragechartbox').html(JSONObject['salesaverage']);
      },
      error: function(xhr) {
      //alert(xhr.responseText);
      },
    });
}

function refreshorderschartbox(){
    var uurl = SITE_URL+"dashboard/getorderschartbox";
    var startdate = $('#fromdate1').val();
    var enddate = $('#todate1').val();
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {startdate:startdate,enddate:enddate},
      async: false,
      success: function(response){
        var JSONObject = JSON.parse(response);
        $('#orderstotalchartbox').html(JSONObject['orderscount']);
        $('#ordersaveragechartbox').html(JSONObject['ordersaverage']);
      },
      error: function(xhr) {
      //alert(xhr.responseText);
      },
    });
}

//charts
function refreshhighcharts() {

    // if(loadpage!=0){

      fromdate = $("#fromdate1").val();
      todate = $("#todate1").val();

      var uurl = SITE_URL+"dashboard/dashboard_process";
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {fromdate:fromdate,todate:todate},
        async: false,
        success: function(response){
          var JSONObject = JSON.parse(response);
          orderchartdata = JSONObject['orderchartdata'];
          orderchartdrilldata = JSONObject['orderchartdrilldata'];
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
      });
    // }

    Orderscharts(orderchartdata,orderchartdrilldata);
     

}

function Orderscharts(orderchartdata,orderchartdrilldata){
      Highcharts.chart('container', {
        chart: {
            type: 'column'
        },
        title: {
            text: ''
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            type: 'category'
        },
        yAxis: {
            title: {
                text: 'Total Orders'
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
        colors: ['#00b09f','#795548', '#eb6357','#4d5ec1','#742841',"#ffa21a","#07264e","#045e65","#7E335C","#008d40","#4e008d","#820672"],
        tooltip: {
            headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b> of total<br/>'
        },

        "series": [
            {
                "name": "Month",
                "colorByPoint": true,
                "data": orderchartdata
            }
        ],
        "drilldown": {
          "series": orderchartdrilldata
      }
    })
}

function refreshsaleshighcharts() {

    // if(loadpage!=0){

      fromdate = $("#fromdate").val();
      todate = $("#todate").val();

      var uurl = SITE_URL+"dashboard/saleschart";
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {fromdate:fromdate,todate:todate},
        async: false,
        success: function(response){
          var JSONObject = JSON.parse(response);
          saleschartdata = JSONObject['saleschartdata'];
          saleschartdrilldata = JSONObject['saleschartdrilldata'];
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
      });
    // }

      Salescharts(saleschartdata,saleschartdrilldata);

}

function Salescharts(saleschartdata,saleschartdrilldata){
    Highcharts.chart('container1', {
      chart: {
        // renderTo: 'container',
          type: 'column'
      },
      title: {
          text: ''
      },
      subtitle: {
          text: ''
      },
      xAxis: {
          type: 'category'
      },
      yAxis: {
          title: {
              text: 'Total Sales'
          },
      },
      legend: {
          enabled: false,
      },
      plotOptions: {
          series: {
              borderWidth: 0,
              dataLabels: {
                  enabled: true,
                  format: '{point.y}'
              },
              /*label: {
                  connectorAllowed: true
              },*/
              marker: {
                radius: 10,
              }
          }
      },
      colors: ['#00b09f','#795548', '#eb6357','#4d5ec1','#742841',"#ffa21a","#07264e","#045e65","#7E335C","#008d40","#4e008d","#820672"],
      tooltip: {
          headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
          pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b> of total<br/>'
      },

      "series": [
          {
              "name": "Month",
              "colorByPoint": true,
              "data": saleschartdata
          }
      ],
      "drilldown": {
        "series": saleschartdrilldata
    }
  })

}

function getSizemodal(){
    //alert("dfjnod");
    swal({    title: "Storage Space limit reached 100%.",
    text: "Kindly contact our sales team for renewal",
    type: "warning",   
    showCancelButton: false,   
    confirmButtonColor: "#DD6B55",   
    confirmButtonText: "Ok",   
    closeOnConfirm: true });
}