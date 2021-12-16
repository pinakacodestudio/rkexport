$(document).ready(function() {
    
    oTable = $('#gstr2reporttable').DataTable
    ({
        "language": {
            "lengthMenu": "_MENU_"
        },
        "pageLength": 50,
        "columnDefs": [{
            'orderable': false,
            'targets': []
        },
        { targets: [5,7,8,9,10,11,12], className: "text-right" }],
        "order": [], //Initial no order.
        "pendingshipping": [], //Initial no pendingshipping.
        'serverSide': true,//Feature control DataTables' server-side processing mode.
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": SITE_URL+'gstr2-report/listing',
            "type": "POST",
            "data": function ( data ) {
                data.channelid = $('#channelid').val();
                data.memberid = $('#memberid').val();
                data.fromdate = $('#startdate').val();
                data.todate = $('#enddate').val();
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
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(),data;
            
            if(!$.isEmptyObject(data)){
            $.footer = '';
            $('.footercls').remove();
            if($('#gstr2reporttable tfoot').length==0){
                $.footer += '<tfoot>';
            }
            
            // if($('.footercls').length==0){
                $.footer += '<tr class="footercls">';
            
                $.footer += '<th colspan="9" class="totalrows text-right">Total</th>';
            
                this.api().columns().each(function () {
                var column = this;
                if(column[0].length > 0){
                    for (var index = 9; index < column[0].length; index++) {
    
                        // Remove the formatting to get integer data for summation
                        var intVal = function (i) {
                            return typeof i === 'string' ?
                                i.replace(/[\L,]/g, '') * 1 :
                                typeof i === 'number' ?
                                i : 0;
                        };
    
                        // Total over all pages
                        var total = api
                            .column( column[0][index] )
                            .data()
                            .reduce( function (a, b) {
                                var a = intVal(a) || 0;
                                var b = intVal(b) || 0;
                                return parseFloat(a) + parseFloat(b);
                        }, 0 );
    
                        // Total over this page
                        var pageTotal = api
                            .column( column[0][index], { page: 'current'} )
                            .data()
                            .reduce( function (a, b) {
                                var a = intVal(a) || 0;
                                var b = intVal(b) || 0;
                                return parseFloat(a) + parseFloat(b);
                        }, 0 );
                    
                        $.footer += '<th class="totalrows text-right" style="padding:10px 10px;">'+CURRENCY_CODE+''+ format.format(pageTotal) +'</th>';
                    }
                }
                });
                $.footer += '</tr>';
            // }
            if($('#gstr2reporttable tfoot').length==0){
                $.footer += '</tfoot>';
                $('#gstr2reporttable tbody').after($.footer);
            }else{
                $('#gstr2reporttable tfoot').prepend($.footer);
            }
            }else{
            $('#gstr2reporttable tfoot').remove();
            }
    
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
      todayBtn: 'linked'
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

    $("#channelid").change(function(){
        getmembers();
    });
});
function applyFilter(){
    $('.footercls').remove();
    oTable.ajax.reload(null, false);
}
function getmembers(type=0){
  
    var memberelement = $("#memberid");
    var channelelement = $("#channelid");
  
    if(type==1){
      memberelement = $("#sellermemberid");
      channelelement = $("#sellerchannelid");
    }
    memberelement.find('option')
                .remove()
                .end()
                .val('0')
                .append('<option value="0">Select '+Member_label+'</option>')
              ;
    memberelement.selectpicker('refresh');
    var channelid = channelelement.val();
  
    if(channelid!='' && channelid!=0){
      var uurl = SITE_URL+"member/getmembers";
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {channelid:channelid},
        dataType: 'json',
        async: false,
        success: function(response){
  
          for(var i = 0; i < response.length; i++) {
  
            memberelement.append($('<option>', { 
              value: response[i]['id'],
              text : ucwords(response[i]['namewithcodeormobile'])
            }));
  
          }
          memberelement.selectpicker('refresh');
        },
        error: function(xhr) {
          //alert(xhr.responseText);
        },
      });
    }
}

function exporttoexcelgstr2report(){
  
  var channelid = $('#channelid').val();
  var memberid = $('#memberid').val();
  var fromdate = $('#startdate').val();
  var todate = $('#enddate').val();
  
  var totalRecords =$("#gstr2reporttable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"gstr2-report/exporttoexcelgstr2report?channelid="+channelid+"&memberid="+memberid+"&fromdate="+fromdate+"&todate="+todate;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  
}
function exporttopdfgstr2report(){
  
  var channelid = $('#channelid').val();
  var memberid = $('#memberid').val();
  var fromdate = $('#startdate').val();
  var todate = $('#enddate').val();
  
  var totalRecords =$("#gstr2reporttable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"gstr2-report/exporttopdfgstr2report?channelid="+channelid+"&memberid="+memberid+"&fromdate="+fromdate+"&todate="+todate;
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  
}
function printGSTR2Report(){

    var channelid = $('#channelid').val();
    var memberid = $('#memberid').val();
    var fromdate = $('#startdate').val();
    var todate = $('#enddate').val();

    var totalRecords =$("#gstr2reporttable").DataTable().page.info().recordsDisplay;
    $.skylo('end');
    if(totalRecords != 0){
      var uurl = SITE_URL + "gstr2-report/printGSTR2Report";
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {channelid:channelid,memberid:memberid,fromdate:fromdate,todate:todate},
        //dataType: 'json',
        async: false,
        beforeSend: function() {
            $('.mask').show();
            $('#loader').show();
        },
        success: function(response) {
            
          var data = JSON.parse(response);
          var html = data['content'];
        
          var frame1 = document.createElement("iframe");
          frame1.name = "frame1";
          frame1.style.position = "absolute";
          frame1.style.top = "-1000000px";
          document.body.appendChild(frame1);
          var frameDoc = frame1.contentWindow ? frame1.contentWindow : frame1.contentDocument.document ? frame1.contentDocument.document : frame1.contentDocument;
          frameDoc.document.open();
          frameDoc.document.write(html);
          frameDoc.document.close();
          setTimeout(function () {
            window.frames["frame1"].focus();
            window.frames["frame1"].print();
            document.body.removeChild(frame1);
          }, 500);
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