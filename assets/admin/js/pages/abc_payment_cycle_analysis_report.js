$(document).ready(function() {

    oTable = $('#abcpaymentcycleanalysisreporttable').DataTable
    ({
      "language": {
          "lengthMenu": "_MENU_"
      },
      "pageLength": 25,
      "columnDefs": [{
          'orderable': false,
          'targets': [0]
      },
      { targets: [2,3,4,5], className: "text-right" },{ targets: [6], className: "text-center" }
    ],
      "order": [], //Initial no order.
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": SITE_URL+'abc-payment-cycle-analysis-report/listing',
          "type": "POST",
          "data": function ( data ) {
              data.startdate = $("#startdate").val();
              data.enddate = $("#enddate").val();
              data.classA = $("#classA").val();
              data.classB = $("#classB").val();
              data.classC = $("#classC").val();
              data.channelid = $("#channelid").val();
              data.memberid = $("#memberid").val();
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
            loadpopover();
          },
      },
      "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          if ( aData[6] == "A" ) {
            $('td', nRow).css('background-color', '#a9d18e');
          }else if ( aData[6] == "B" ) {
            $('td', nRow).css('background-color', '#c5e0b4');
          }else if ( aData[6] == "C" ) {
            $('td', nRow).css('background-color', '#e2f0d9');
          }
      }
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
          return false;
      });
    });
    $("#classA,#classB,#classC").on('keyup', function(){
      if(this.value > 100){
        $(this).val('100');
      }
    });
});

function applyFilter(){
  var isvalid = checkclassvalidation();
  if(isvalid == 1){
    oTable.ajax.reload(null,false);
  }
}

$("#channelid").change(function(){
  var channelid = $(this).val();
  $('#memberid')
      .find('option')
      .remove()
      .end()
      .val('whatever')
      ;
  if(channelid!='' && channelid!=0){
    getmembers(channelid);
  }
  $('#memberid').selectpicker('refresh');
})

function getmembers(channelid,memberid=0){
  var uurl = SITE_URL+"abc-payment-cycle-analysis-report/getMemberOnPaymentCycleReportByChannel";
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
      if(memberid!=0){
        $("#memberid").val(memberid);
      }
      // $('#product'+prow).val(areaid);
      $('#memberid').selectpicker('refresh');
    },
    error: function(xhr) {
          //alert(xhr.responseText);
        },
  });
}

function checkclassvalidation(){
  var classA = $("#classA").val();
  var classB = $("#classB").val();
  var classC = $("#classC").val();
  
  var isvalidclass = 1;
  PNotify.removeAll();

  if(classA == ""){
    $("#classA_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter value of class A !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidclass = 0;
  }else if(classB != "" && parseFloat(classA) < parseFloat(classB)){
    $("#classA_div").addClass("has-error is-focused");
    new PNotify({title: 'Value of class A is not allow less than of class B !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidclass = 0;
  }else if(classC != "" && parseFloat(classA) <= parseFloat(classC)){
    $("#classA_div").addClass("has-error is-focused");
    new PNotify({title: 'Value of class A is not allow less than or equal class C !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidclass = 0;
  }else{
    $("#classA_div").removeClass("has-error is-focused");
  }

  if(classB == ""){
    $("#classB_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter value of class B !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidclass = 0;
  }else if(classA != "" && parseFloat(classB) >= parseFloat(classA)){
    $("#classB_div").addClass("has-error is-focused");
    new PNotify({title: 'Value of class B is not allow greater than or equal class A !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidclass = 0;
  }else if(classC != "" && parseFloat(classB) <= parseFloat(classC)){
    $("#classB_div").addClass("has-error is-focused");
    new PNotify({title: 'Value of class B is not allow less than or equal class C !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidclass = 0;
  }else{
    $("#classB_div").removeClass("has-error is-focused");
  }

  if(classC == ""){
    $("#classC_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter value of class C !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidclass = 0;
  }else if(classA != "" && parseFloat(classC) >= parseFloat(classA)){
    $("#classC_div").addClass("has-error is-focused");
    new PNotify({title: 'Value of class C is not allow greater than or equal class A !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidclass = 0;
  }else if(classB != "" && parseFloat(classC) > parseFloat(classB)){
    $("#classC_div").addClass("has-error is-focused");
    new PNotify({title: 'Value of class C is not allow greater than of class B !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidclass = 0;
  }else{
    $("#classC_div").removeClass("has-error is-focused");
  }

  if(classA != "" && classB != "" && classC != ""){
    var total = parseFloat(classA) + parseFloat(classB) + parseFloat(classC);
    if(total != "100"){
      new PNotify({title: 'Allow total value of class is 100 !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidclass = 0;
    }
  }

  return isvalidclass;
}

function exportToExcelABCPaymentCycleReport(){
  
  var isvalid = checkclassvalidation();
  if(isvalid == 1){
    var startdate = $("#startdate").val();
    var enddate = $("#enddate").val();
    var classA = $("#classA").val();
    var classB = $("#classB").val();
    var classC = $("#classC").val();
    var channelid = $("#channelid").val();
    var memberid = ($("#memberid").val()!=null?$("#memberid").val():"");

    var totalRecords =$("#abcpaymentcycleanalysisreporttable").DataTable().page.info().recordsDisplay;
    $.skylo('end');
    if(totalRecords != 0){
      window.location= SITE_URL+"abc-payment-cycle-analysis-report/exportToExcelABCPaymentCycleReport?startdate="+startdate+"&enddate="+enddate+"&classA="+classA+"&classB="+classB+"&classC="+classC+"&channelid="+channelid+"&memberid="+memberid;
    }else{
      new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }
  }
}

function exportToPDFABCPaymentCycleReport(){

  var isvalid = checkclassvalidation();
  if(isvalid == 1){
    var startdate = $("#startdate").val();
    var enddate = $("#enddate").val();
    var classA = $("#classA").val();
    var classB = $("#classB").val();
    var classC = $("#classC").val();
    var channelid = $("#channelid").val();
    var memberid = ($("#memberid").val()!=null?$("#memberid").val():"");

    var totalRecords =$("#abcpaymentcycleanalysisreporttable").DataTable().page.info().recordsDisplay;
    $.skylo('end');
    if(totalRecords != 0){
  
      window.location= SITE_URL+"abc-payment-cycle-analysis-report/exportToPDFABCPaymentCycleReport?startdate="+startdate+"&enddate="+enddate+"&classA="+classA+"&classB="+classB+"&classC="+classC+"&channelid="+channelid+"&memberid="+memberid;
    }else{
      new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }
  }
}

function printabcpaymentcyclereport(){

  var isvalid = checkclassvalidation();
  if(isvalid == 1){
    var startdate = $("#startdate").val();
    var enddate = $("#enddate").val();
    var classA = $("#classA").val();
    var classB = $("#classB").val();
    var classC = $("#classC").val();
    var channelid = $("#channelid").val();
    var memberid = $("#memberid").val() || [];
    memberid = memberid.join(",");

    var totalRecords =$("#abcpaymentcycleanalysisreporttable").DataTable().page.info().recordsDisplay;
    $.skylo('end');
    if(totalRecords != 0){
      var uurl = SITE_URL + "abc-payment-cycle-analysis-report/printabcpaymentcyclereport";
      $.ajax({
        url: uurl, 
        type: 'POST',
        data: {startdate:startdate,enddate:enddate,classA:classA,classB:classB,classC:classC,channelid:channelid,memberid:memberid},
        async: false,
        //dataType: 'json',
        beforeSend: function() {
            $('.mask').show();
            $('#loader').show();
        },
        success: function(response) {
            
          var html = JSON.parse(response);

          printdocument(html);
        },
        error: function(xhr) {
            //alert(xhr.responseText);
        },
        complete: function() {
            $('.mask').hide();
            $('#loader').hide();
        },
      });
    }else{
      new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }
  }
}