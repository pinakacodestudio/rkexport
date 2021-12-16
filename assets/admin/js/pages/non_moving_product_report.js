
$(document).ready(function() {

    $(function () {
      $('.panel-heading.filter-panel').click(function() {
          $(this).find('.button-icon span').html($(this).find('.button-icon span').text() == 'keyboard_arrow_down' ? 'keyboard_arrow_up' : 'keyboard_arrow_down');
          //$(this).children().toggleClass(" ");
          $(this).next().slideToggle({duration: 200});
          $(this).toggleClass('panel-collapsed');
          return false;
      });
    });

    $(".btn_interval_length").click(function(){
      $(".btn_interval_length").removeClass("btn-success").addClass("btn-default");
      $(this).removeClass("btn-default").addClass("btn-success");

      $("#intervallength").val(parseInt($(this).attr("data-value")));
    });
    $(".btn_interval_count").click(function(){
      $(".btn_interval_count").removeClass("btn-success").addClass("btn-default");
      $(this).removeClass("btn-default").addClass("btn-success");

      $("#intervalcount").val(parseInt($(this).attr("data-value")));
    });

    get_aging_report_data();
});

function applyFilter(){
  get_aging_report_data();
}

function get_aging_report_data(){

  var uurl = SITE_URL+"aging-report/get-aging-report-data";
  var formData = new FormData($("#agingform")[0]);
  
  $.ajax({
    url: uurl,
    type: 'POST',
    data: formData,
    // dataType: "json",
    // async: false,
    beforeSend: function(){
      $('.mask').show();
      $('#loader').show();
    },
    success: function(response){
      
      var dataObject = $.parseJSON(response);

      if ( $.fn.DataTable.isDataTable('#agingreporttable') ) {
        $('#agingreporttable').DataTable().destroy();
      }
      $('#agingreporttable').empty();

      
      // if(!$.isEmptyObject(dataObject.DATA)){
        var fixedColumns = 3;
        table =   $('#agingreporttable').DataTable({

          "data": dataObject.DATA,
          "columns": dataObject.COLUMNS,
          "language": {
            "lengthMenu": "_MENU_"
          },
          
          "destroy": true,
          "pageLength": 50,
          "columnDefs": [{
            'orderable': false,
            'targets': []
          }],
          "order": [], //Initial no order.
        });

        $('.panel-ctrls.panel-tbl').html('');
        $('.panel-footer').html('');
        
        $('.dataTables_filter input').attr('placeholder','Search...');

        //DOM Manipulation to move datatable elements integrate to panel
        $('.panel-ctrls.panel-tbl').append($('.dataTables_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center form-group");
        $('.panel-ctrls.panel-tbl').append("<i class='separator'></i>");
        $('.panel-ctrls.panel-tbl').append($('.dataTables_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center form-group");

        $('.panel-footer').append($(".dataTable+.row"));
        $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-md");
      // }
        
    },
    error: function(xhr) {
    //alert(xhr.responseText);
    },
    complete: function(){
      $('.mask').hide();
      $('#loader').hide();
    },
    cache: false,
    contentType: false,
    processData: false
  });
}

function exporttoexcelnonmovingproductreport(){
  
  var producttype = ($('#producttype').val()!=null)?$('#producttype').val().join(","):"";
  var intervallength = $('#intervallength').val();
  var intervalcount = $('#intervalcount').val();
  
  var totalRecords =$("#agingreporttable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"aging-report/exporttoexcelnonmovingproductreport?intervallength="+intervallength+"&intervalcount="+intervalcount+"&producttype="+producttype;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  
}

function exporttopdfnonmovingproductreport(){
  
  var producttype = ($('#producttype').val()!=null)?$('#producttype').val().join(","):"";
  var intervallength = $('#intervallength').val();
  var intervalcount = $('#intervalcount').val();
  
  var totalRecords =$("#agingreporttable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"aging-report/exporttopdfnonmovingproductreport?intervallength="+intervallength+"&intervalcount="+intervalcount+"&producttype="+producttype;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  
}

function printnonmovingproductreport(){

  var producttype = $('#producttype').val();
  var intervallength = $('#intervallength').val();
  var intervalcount = $('#intervalcount').val();
  
  var totalRecords =$("#agingreporttable").DataTable().page.info().recordsDisplay;
    $.skylo('end');
    if(totalRecords != 0){
      var uurl = SITE_URL + "aging-report/printnonmovingproductreport";
      $.ajax({
        url: uurl, 
        type: 'POST',
        data: {intervallength:intervallength,intervalcount:intervalcount,producttype:producttype},
        //dataType: 'json',
        async: false,
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