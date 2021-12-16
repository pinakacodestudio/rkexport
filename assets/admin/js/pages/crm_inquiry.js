
$(document).ready(function() {

    $("#inquiryassignto").change(function() {
      if($("#oldassignto").val()==$("#inquiryassignto").val()) {
        $("#reason1_div").hide();
      }else{
        $("#reason1_div").show();
      }
    });
    loadpopover();
    
    oTable = $('#inquirytabel').DataTable({
      "language": {
          "lengthMenu": "_MENU_"
      },
      "columnDefs": [ 
        {
        "targets": [0,-1,-2],
        "orderable": false
      }],
      drawCallback: function () {
        loadpopover();
      },
      "order": [[ 0, 'asc' ]],
      responsive: true,
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      "ajax": {
        "url": SITE_URL+"crm-inquiry/listing",
        "type": "POST",
        "data" :function ( data ) {
          data.filterstatus = $("#filterstatus").val();
          data.filtermember = $("#filtermember").val();
          data.filteremployee = $("#filteremployee").val();
          data.fromdate = $('#startdate').val();
          data.todate = $('#enddate').val();
          data.direct = ($('#direct').prop("checked") == true)?1:0;
          data.indirect = ($('#indirect').prop("checked") == true)?1:0;
          data.filterinquiryleadsource = $('#filterinquiryleadsource').val();
          data.filtermemberindustry = $('#filtermemberindustry').val();
          data.filtermemberstatus = $('#filtermemberstatus').val();
          data.filterproduct = $('#filterproduct').val();
        },
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
        },
        complete: function(){
          $('.mask').hide();
          $('#loader').hide();
        },
      }
    });

    $('.dataTables_filter input').attr('placeholder','Search...');

    //DOM Manipulation to move datatable elements integrate to panel
    $('.panel-ctrls.inquirytabel').append($('.dataTables_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls.inquirytabel').append("<i class='separator'></i>");
    $('.panel-ctrls.inquirytabel').append($('.dataTables_length').addClass("pull-left ")).find("label").addClass("panel-ctrls-center");

    $('.panel-footer.inquirytabel').append($(".dataTable+.row"));
    $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-md");
  
    $('#datepicker-range').datepicker({
      todayHighlight: true,
      format: 'dd/mm/yyyy',
      autoclose: true,
      todayBtn:"linked"
    });
    $('#datepicker-range1').datepicker({
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
          if($(this).hasClass("inqstatuspnl")){
            savecollapse($(this).attr("display-type"),'panel-heading.filter-panel.inqstatuspnl','status');
          }else{
            savecollapse($(this).attr("display-type"),'panel-heading.filter-panel.inqpnl','inquiry');
          }
          return false;
      });
    });
    var panel1displaytype = $('.panel-heading.filter-panel.inqstatuspnl').attr("display-type");
    if(panel1displaytype==0){
      $('.panel-heading.filter-panel.inqstatuspnl').find('.button-icon span').html($(this).find('.button-icon span').text() == 'keyboard_arrow_down' ? 'keyboard_arrow_up' : 'keyboard_arrow_down');
      //$(this).children().toggleClass(" ");
      $('.panel-heading.filter-panel.inqstatuspnl').next().slideToggle({duration: 200});
      $('.panel-heading.filter-panel.inqstatuspnl').toggleClass('panel-collapsed');
    }
    var panel2displaytype = $('.panel-heading.filter-panel.inqpnl').attr("display-type");
    if(panel2displaytype==0){
      $('.panel-heading.filter-panel.inqpnl').find('.button-icon span').html($(this).find('.button-icon span').text() == 'keyboard_arrow_down' ? 'keyboard_arrow_up' : 'keyboard_arrow_down');
      //$(this).children().toggleClass(" ");
      $('.panel-heading.filter-panel.inqpnl').next().slideToggle({duration: 200});
      $('.panel-heading.filter-panel.inqpnl').toggleClass('panel-collapsed');
    }

    getallstatuscounts();
    $('.js-data-example-ajax').select2({
      placeholder: "Select "+Member_label,
      // minimumInputLength: 3,
      ajax: {
        url: SITE_URL+'crm-inquiry/getmembers',
        dataType: 'json',
        quietMillis: 100,
        type:"post",
        data: function (term, page) { // page is the one-based page number tracked by Select2
          return {
              term: term, //search term
              page_limit: 25, // page size
              page: page, // page number
              gettype:1,
          };
        },
        results: function (data, page) {
          var more = (page * 25) < data.total; // whether or not there are more results available
          result = data.results;
          // notice we return the value of more so Select2 knows if more results can be loaded
          return {results: result, more: more};
        }
      },
      initSelection: function(element, callback) {
        if($("#filtermember").val()!=""){
          callback({id: $("#filtermember").val(), text: $("#filtermember").attr("data-text") });
        }
      },
      formatResult: styleFormatResult, // omitted for brevity, see the source of this page
      dropdownCssClass: "bigdrop", // apply css that makes the dropdown taller
      escapeMarkup: function (m) { return m; } // we do not want to escape markup since we are displaying html in results
  });
});

function applyFilter(){
  oTable.ajax.reload(null,false);
}
function applyFilter1(){
  getallstatuscounts();
}
 
$(document).ready(function() {

  $('#date').datepicker({
      todayHighlight: true,
      format: 'dd/mm/yyyy',
      orientation: 'top',
      autoclose: true,
      todayBtn: "linked"
  });
  
  var date = new Date();
  date.setDate(date.getDate());
  displaydate = date.getDate()+"/"+(date.getMonth()+1)+"/"+date.getFullYear();
  
  $('#datetimepicker').daterangepicker({
    singleDatePicker: true,
    showDropdowns: true,
    autoUpdateInput: true,
    timePicker: true,
    /*timePicker24Hour: true,*/
    //minYear: 1901,
    minDate: displaydate,
    locale: {
      format: 'DD/MM/YYYY HH:mm'
    },
  });
  $('#datepicker').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        orientation: 'top',
        autoclose: true,
        todayBtn: "linked"
    });
});
  
function viewinquiryfollowup(inquiryid){
  $("#followupModal").modal("toggle");
  if ( $.fn.DataTable.isDataTable('#followuptbl') ) {
    $('#followuptbl').DataTable().destroy();
  }

  dtable = $('#followuptbl').DataTable({
    "processing": true,//Feature control the processing indicator.
    "language": {
        "lengthMenu": "_MENU_"
    },
    drawCallback: function () {
      loadpopover();
    },
    "columnDefs": [ {
      "targets": [0,1,2,-1],
      "orderable": false
    }, { width: 40, targets: [4,5] } ],
    responsive: true,
    'serverSide': true,//Feature control DataTables' server-side processing mode.
    "ajax": {
      "url": SITE_URL+"crm-inquiry/inquiryfollowuplisting",
      "type": "POST",
      "data" :function ( data ) {
            data.inquiryid = inquiryid;
      },
      beforeSend: function(){
        $('.mask').show();
        $('#loader').show();
      },
      complete: function(data){
        $('.mask').hide();
        $('#loader').hide();
      },
    },
    initComplete : function(data, json) {
      var memberdata = json['memberdata']; 
      
      if(memberdata != null){
        $("#companyname").html(memberdata['companyname']);
        $("#membername").html(memberdata['name']);
        $("#mobile").html(memberdata['countrycode']+memberdata['mobileno']);
        var email = "<a class='a-without-link' href='mailto:"+memberdata['email']+"'>"+memberdata['email']+"</a>";
        $("#email").html(email);
      }
    }, 
  });
  $('#followuptbl_filter input').attr('placeholder','Search...');
  $("#followuptbl_wrapper").on("keyup", "#followuptbl_filter input", function(e){
      if(e.keyCode == 13) {
        dtable.search(this.value).draw();
      }
  });
}
function transferinquiry(){
    var inputs = $("input[type='checkbox']");
    if(currentdids == ""){
      swal("Cancelled", 'Please select inquiry !', "error");
    }else{
      $("#inquiryassignto").val("0");
      $("#inquiryid1").val(currentdids);
      $('#myModal2').modal('toggle');
      $('#reason1_div').show();
      $(".selectpicker").selectpicker("refresh");
    }
}

function loadfollowup_modal(inquiryid,memberid,latitude,longitude) {
  $("#inquiryid").val(inquiryid);
  $("#memberid").val(memberid);
  $("#latitude").val(latitude);
  $("#longitude").val(longitude);

  $('.followupdate').daterangepicker({
    singleDatePicker: true,
    showDropdowns: true,
    autoUpdateInput: true,
    timePicker: true,
    /*timePicker24Hour: true,*/
    //minYear: 1901,
    minDate: displaydate,
    locale: {
      format: 'DD/MM/YYYY HH:mm'
    },
  });
}

function styleFormatResult(style) {
  var markup = style.text;
  return markup;
}

function loadinquiry_modal(inquiryid) {
  $('.popoverButton').popover('hide');
  $("#inquiryid1").val(inquiryid);
  var uurl = SITE_URL+"crm-inquiry/getinquirydetail";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {inquiryid:inquiryid},
    dataType: 'json',
    async: false,
    success: function(response){
      $("#inquiryassignto").val(response.inquiryassignto);
      $("#oldassignto").val(response.inquiryassignto);
      $("#inquiryassignto").selectpicker("refresh");
      if(response.employeeactive==0){
        new PNotify({title: "Assigned employee is inactive for this "+inquiry_label+" !",styling: 'fontawesome',delay: '3000',type: 'warning'});
        $('#myModal2').modal('toggle');
      }else if($("#inquiryassignto").val()==null){
        new PNotify({title: "You can not assign this "+inquiry_label+" to any other employee !",styling: 'fontawesome',delay: '3000',type: 'error'});
      }else{
        $('#myModal2').modal('toggle');
      }
    },
    error: function(xhr) {
      //alert(xhr.responseText);
    },
  });
}

function checkvalidation(){
  
  var date = $("#date").val().trim();
  var followuptype = $("#followuptype").val().trim();
  var status = $("#status").val().trim();
  var note = $("#followupnote").val().trim();
  
  isvalidfollowuptype = isvalidstatus = isvaliddate = isvalidnote = 0 ;

  PNotify.removeAll();
  if(date == ""){
    $("#date_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select date !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvaliddate = 0;
  }else {
      isvaliddate = 1;
  }

  if(followuptype == 0 || followuptype == null || followuptype == ""){
    $("#followuptype_div").addClass("has-error is-focused");
    new PNotify({title: "Please select "+follow_up_label+" type !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidfollowuptype = 0;
  }else {
      isvalidfollowuptype = 1;
  }
 
  if(status == null || status == ""){
    $("#status_div").addClass("has-error is-focused");
    new PNotify({title: "Please Select Status !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidstatus = 0;
  }else {
    isvalidstatus = 1;
  } 

  if(note == ''){
    $("#note_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter note !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidnote = 0;
  }else {
      isvalidnote = 1;
  }

  if(isvaliddate == 1 && isvalidfollowuptype == 1 && isvalidstatus == 1 && isvalidnote == 1) {

    var formData = new FormData($('#followupform')[0]);
      var uurl = SITE_URL+"crm-inquiry/addfollowup";
      
      $.ajax({
        url: uurl,
        type: 'POST',
        data: formData,
        //async: false,
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
        },
        success: function(response){
          if(response==1){
            new PNotify({title: Followup_label+" successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
            $("#myModal").modal("toggle");
            $("#employee").val(loginuser);

            Date.prototype.addDays = function(days) {
                var date = new Date(this.valueOf());
                date.setDate(date.getDate() + days);
                return date;
            }
            
            var date = new Date();
            date = date.addDays(parseInt(DEFAULT_FOLLOWUP_DATE));
            day = (date.getDate() < 10 ? '0' : '') + date.getDate();
            month = ((date.getMonth()+1) < 10 ? '0' : '') + (date.getMonth()+1);
            year = date.getFullYear();
            hours = (date.getHours() < 10 ? '0' : '') + date.getHours();
            minutes = (date.getMinutes() < 10 ? '0' : '') + date.getMinutes();
            displaydate = day+"/"+month+"/"+year+" "+hours+":"+minutes;
    
            $("#date").val(displaydate);
            $( "#date" ).data('daterangepicker').setStartDate(displaydate);
            $( "#date" ).data('daterangepicker').setEndDate(displaydate);

            if(DEFAULT_FOLLOWUP_TYPE!=""){
              $("#followuptype").val(DEFAULT_FOLLOWUP_TYPE);
            }
            if(FOLLOWUP_DEFAULT_STATUS!=""){
              $("#status").val(FOLLOWUP_DEFAULT_STATUS);
            }
            $("#followupnote").val("");
            $("#futurenote").val("");
            $(".selectpicker").selectpicker("refresh");
            $("#date_div,#time_div,#employee_div,#followuptype_div,#status_div,#note_div,#futurenote_div").removeClass("has-error is-focused");
          }else{
            new PNotify({title: Followup_label+' not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }
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
}

function checkvalidation1(){
  
  var assignto = $("#inquiryassignto").val();
  var reason = $("#reason1").val();

  isvalidassignto = isvalidreason = 0 ;
  PNotify.removeAll();

  if(assignto == 0 || assignto == null || assignto == ""){
    $("#inquiryassignto_div").addClass("has-error is-focused");
    new PNotify({title: "Please select employee !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidassignto = 0;
  }else {
      isvalidassignto = 1;
  }

  if(reason == '' && $("#reason1_div").is(":visible")){
    $("#reason1_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter reason !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidreason = 0;
  }else {
      isvalidreason = 1;
  }

  if(isvalidassignto==1 && isvalidreason==1)
  {

    var formData = new FormData($('#inquiryform')[0]);
      var uurl = SITE_URL+"crm-inquiry/editinquiryassignto";
      
      $.ajax({
        url: uurl,
        type: 'POST',
        data: formData,
        //async: false,
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
        },
        success: function(response){
          if(response==1){
            new PNotify({title: Inquiry_label+" successfully updated.",styling: 'fontawesome',delay: '3000',type: 'success'});
            $("#myModal2").modal("toggle");
            oTable.ajax.reload(null,false);

            $("#inquiryassignto").val("0");
            $("#reason1").val("");
            $("#reason1_div").hide();
            $(".selectpicker").selectpicker("refresh");
            $("#reason1_div,#inquiryassignto_div").removeClass("has-error is-focused");
            // setTimeout(function() { location.reload(); }, 1500);
          }else{
            new PNotify({title: Inquiry_label+' not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }
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
}

function changeinquirystatus(sts,inquiryid,statustext,color=""){

  swal({title: 'Are you sure to change status?',
    type: "warning",   
    showCancelButton: true,   
    confirmButtonColor: "#DD6B55",   
    confirmButtonText: "Yes, change it!",
    timer: 2000,   
    }, 
    function(isConfirm){
      if (isConfirm) {   
                var uurl = SITE_URL+"crm-inquiry/change-status";
        $.ajax({
          url: uurl,
          type: 'POST',
          data: {status:sts,id:inquiryid},
          dataType: 'json',
          //async: false,
          success: function(response){
              if(response==1)
              {
                $("#inquirystatusdropdown"+inquiryid).css("background",color);
                $("#inquirystatusdropdown"+inquiryid).text(statustext);

                oTable.ajax.reload(null,false);
              }
          },
          error: function(xhr) {
            //alert(xhr.responseText);
          },
        }); 
      }
  });
}

function getallstatuscounts(){
  
  fromdate = $('#startdate1').val();
  todate = $('#enddate1').val();
  filteremployee = $('#filterstatusemployee').val();
  var uurl = SITE_URL+"crm-inquiry/savestatusfilter";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {fromdate:fromdate,todate:todate,filteremployee:filteremployee},
    dataType: 'json',
    //async: false,
    success: function(response){
      $.each(response, function( key, value ) {
        $("#status_count"+value.id).html(value.statuscount);
      });
    },
    error: function(xhr) {
      //alert(xhr.responseText);
    },
  }); 
}

function savecollapse(displaytype,cls,panel){
  var uurl = SITE_URL+"crm-inquiry/savecollapse";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {displaytype:displaytype,panel:panel},
    dataType: 'json',
    success: function(response){
      if(response.displaytype=='1'){
        $("."+cls).attr("display-type","0");
      }else{
        $("."+cls).attr("display-type","1");
      }
    },
    error: function(xhr) {
      //alert(xhr.responseText);
    },
  }); 
}

function exportinquiry(){

  var filterstatus = $('#filterstatus').val();
  var filtermember = $('#filtermember').val();
  var filteremployee = $('#filteremployee').val();
  var fromdate = $('#startdate').val();
  var todate = $('#enddate').val();
  var direct = ($('#direct').prop("checked") == true)?1:0;
  var indirect = ($('#indirect').prop("checked") == true)?1:0;
  var filterinquiryleadsource = ($('#filterinquiryleadsource').val()!=null?$('#filterinquiryleadsource').val():"");
  var filtermemberindustry = ($('#filtermemberindustry').val()?$('#filtermemberindustry').val():"");
  var filtermemberstatus = ($('#filtermemberstatus').val()!=null?$('#filtermemberstatus').val():"");
  var filterproduct = ($('#filterproduct').val()!=null?$('#filterproduct').val():"");
  

  var totalRecords =$("#inquirytabel").DataTable().page.info().recordsDisplay;
  if(totalRecords != 0){
    window.location= SITE_URL+"crm-inquiry/exportcrminquiry?filterstatus="+filterstatus+"&filtermember="+filtermember+"&filteremployee="+filteremployee+"&fromdate="+fromdate+"&todate="+todate+"&direct="+direct+"&indirect="+indirect+"&filterinquiryleadsource="+filterinquiryleadsource+"&filtermemberindustry="+filtermemberindustry+"&filtermemberstatus="+filtermemberstatus+"&filterproduct="+filterproduct;
  }else{
    new PNotify({title: 'No inquiry found as per set filter !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  
}